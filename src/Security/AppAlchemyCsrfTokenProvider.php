<?php

declare(strict_types=1);

namespace AppAlchemy\Security;

use AppAlchemy\AppAlchemy;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Override;

class AppAlchemyCsrfTokenProvider extends VerifyCsrfToken
{
    protected AppAlchemy $appAlchemy;

    public function __construct(AppAlchemy $appAlchemy, Application $app, Encrypter $encrypter)
    {
        $this->appAlchemy = $appAlchemy;
        parent::__construct($app, $encrypter);
    }

    #[Override]
    protected function getTokenFromRequest($request): ?string
    {
        if ($this->appAlchemy->isAppAlchemyApp()) {
            return $request->header('X-AppAlchemy-CSRF-Token');
        }

        return parent::getTokenFromRequest($request);
    }
}
