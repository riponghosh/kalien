<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/api-merchant/*',
        '/api-employee/*',
        'facebook/messenger/auto_reply',
        'facebook/messenger/merchant/auto_reply',
        '/telegram_webhook/*'
    ];
}