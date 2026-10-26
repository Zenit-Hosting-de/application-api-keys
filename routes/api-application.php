<?php

use Illuminate\Support\Facades\Route;
use Zenit\ApplicationAPIKeys\Http\Controllers\UserApiKeyController;

Route::prefix('/users/{user:id}/api-keys')->group(function () {
    Route::get('/', [UserApiKeyController::class, 'index'])->name('api.application.users.api-keys');
    Route::post('/', [UserApiKeyController::class, 'store']);
    Route::delete('/{identifier}', [UserApiKeyController::class, 'delete'])->name('api.application.users.api-keys.delete');
});
