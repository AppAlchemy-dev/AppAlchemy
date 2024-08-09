<?php

namespace AppAlchemy;

use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AppAlchemy
{
    private array $customStyles = [];

    public function __construct(
        private readonly Request $request,
        private readonly Repository $config,
        private readonly AuthFactory $auth
    ) {}

    public function getAuthToken(): ?string
    {
        return $this->request->bearerToken() ?? $this->request->header('X-AppAlchemy-Auth-Token');
    }

    public function authenticateToken(string $token): bool
    {
        $user = $this->auth->guard('api')->getProvider()->retrieveByCredentials(['api_token' => $token]);

        if ($user) {
            $this->auth->guard('web')->login($user);

            return true;
        }

        return false;
    }

    public function isAppAlchemyApp(): bool
    {
        $userAgent = $this->request->header('User-Agent');

        return str_contains($userAgent, $this->config->get('appalchemy.user_agent', 'AppAlchemy'));
    }

    public function injectScripts(): string
    {
        return "<script>
            document.documentElement.classList.add('appalchemy-app');
            // Add any other necessary JavaScript here
        </script>";
    }

    public function injectJavaScriptBridge(): string
    {
        if ($this->isAppAlchemyApp()) {
            $bridgeJs = File::get(__DIR__.'/../resources/js/appalchemy-bridge.js');

            return "<script>$bridgeJs</script>";
        }

        return '';
    }

    public function wrapContent(string $content, string $wrapperClass = 'appalchemy-content'): string
    {
        return "<div class=\"$wrapperClass\">$content</div>";
    }

    public function getConfig(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->config->get('appalchemy');
        }

        return $this->config->get("appalchemy.$key", $default);
    }

    public function addCustomStyle(string $style): void
    {
        $this->customStyles[] = $style;
    }

    public function getCustomStyles(): string
    {
        return implode("\n", $this->customStyles);
    }

    public function injectCustomStyles(): string
    {
        if ($this->isAppAlchemyApp()) {
            return '<style>'.$this->getCustomStyles().'</style>';
        }

        return '';
    }
}
