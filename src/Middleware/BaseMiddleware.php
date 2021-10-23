<?php

namespace Denmasyarikin\EncyptResponse\Middleware;

class BaseMiddleware
{
        /**
     * configuration
     * 
     * @var array
     */
    protected $config = [];

    public function __construct()
    {
        $this->config = config('encrypt_response');
    }

    /**
     * check is service enabled
     */
    protected function isServiceEnabled(string $type = 'response'): bool
    {
        if ($type === 'response') {
            return null !== $this->config['response_key'] && $this->config['response_enabled'] === true;
        }

        if ($type === 'request') {
            return null !== $this->config['request_key'] && $this->config['request_enabled'] === true;
        }

        return false;
    }
}
