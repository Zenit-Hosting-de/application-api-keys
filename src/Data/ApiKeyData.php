<?php

namespace Zenit\ApplicationAPIKeys\Data;

use App\Data\Api\ApiResource;
use App\Models\ApiKey;

final class ApiKeyData extends ApiResource
{
    /**
     * @param  array<array-key, mixed>|null  $allowed_ips
     * @param  array<array-key, mixed>|null  $permissions
     */
    public function __construct(
        public int $id,
        public int $user_id,
        public int $key_type,
        public string $identifier,
        public ?string $memo,
        public ?array $allowed_ips,
        public ?array $permissions,
        public ?string $last_used_at,
        public ?string $expires_at,
        public string $created_at,
        public string $updated_at,
    ) {}

    public static function getResourceName(): string
    {
        return ApiKey::RESOURCE_NAME;
    }

    public static function fromModel(ApiKey $model): static
    {
        return new self(
            id: $model->id,
            user_id: $model->user_id,
            key_type: $model->key_type,
            identifier: $model->identifier,
            memo: $model->memo,
            allowed_ips: $model->allowed_ips,
            permissions: $model->permissions,
            last_used_at: $model->last_used_at ? self::formatTimestamp($model->last_used_at) : null,
            expires_at: $model->expires_at ? self::formatTimestamp($model->expires_at) : null,
            created_at: self::formatTimestamp($model->created_at),
            updated_at: self::formatTimestamp($model->updated_at),
        );
    }
}
