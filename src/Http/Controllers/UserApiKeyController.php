<?php

namespace Zenit\ApplicationAPIKeys\Http\Controllers;

use App\Exceptions\DisplayException;
use App\Facades\Activity;
use App\Http\Controllers\Api\Application\ApplicationApiController;
use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Zenit\ApplicationAPIKeys\Http\Requests\DeleteUserApiKeyRequest;
use Zenit\ApplicationAPIKeys\Http\Requests\GetUserApiKeysRequest;
use Zenit\ApplicationAPIKeys\Http\Requests\StoreUserApiKeyRequest;
use Zenit\ApplicationAPIKeys\Transformers\ApiKeyTransformer;

class UserApiKeyController extends ApplicationApiController
{
    /**
     * Return all API keys associated with the given user.
     *
     * @return array<array-key, mixed>
     */
    public function index(GetUserApiKeysRequest $request, User $user): array
    {
        $apiKeys = $user->apiKeys()->latest('id')->get();

        return $this->fractal->collection($apiKeys)
            ->transformWith($this->getTransformer(ApiKeyTransformer::class))
            ->toArray();
    }

    /**
     * Create a new API key for the given user.
     *
     * @return array<array-key, mixed>
     */
    public function store(StoreUserApiKeyRequest $request, User $user): array
    {
        if ($user->apiKeys()->count() >= config('panel.api.key_limit')) {
            throw new DisplayException('This user has reached the account limit for number of API keys.');
        }

        $token = $user->createToken(
            $request->input('description'),
            $request->input('allowed_ips')
        );

        Activity::event('user:api-key.create')
            ->subject($user, $token->accessToken)
            ->property('identifier', $token->accessToken->identifier)
            ->log();

        return $this->fractal->item($token->accessToken)
            ->transformWith($this->getTransformer(ApiKeyTransformer::class))
            ->addMeta(['secret_token' => $token->plainTextToken])
            ->toArray();
    }

    /**
     * Delete the specified API key for the given user.
     */
    public function delete(DeleteUserApiKeyRequest $request, User $user, string $identifier): JsonResponse
    {
        /** @var ApiKey $key */
        $key = $user->apiKeys()
            ->where('identifier', $identifier)
            ->firstOrFail();

        Activity::event('user:api-key.delete')
            ->subject($user, $key)
            ->property('identifier', $key->identifier)
            ->log();

        $key->delete();

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }
}
