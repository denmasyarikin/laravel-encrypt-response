<?php

namespace Denmasyarikin\EncryptResponse\Middleware;

use Closure;
use Denmasyarikin\EncryptResponse\Contracts\Encryptor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class EncryptResponse extends BaseMiddleware
{
    /**
     * encryptor
     * 
     * @var \Denmasyarikin\EncryptResponse\Contracts\Encryptor
     */
    protected $encryptor;

    public function __construct(Encryptor $encryptor)
    {
        $this->encryptor = $encryptor;
        parent::__construct();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($this->shouldEncrypt($request, $response)) {
            $data = json_decode($response->getContent(), true);
            return new JsonResponse(json_decode($this->encrypt($data), true), $response->getStatusCode());
        }

        return $response;
    }

    /**
     * let encrypt
     */
    protected function encrypt(array $data)
    {
        $key = $this->config['response_key'];

        if (!$key) {
            throw new RuntimeException('No response_key set for encryption');
        }

        return $this->encryptor->encrypt(json_encode($data), $key);
    }

    /**
     * determine response encryption
     */
    protected function shouldEncrypt($request, $response): bool
    {
        if ($this->isServiceEnabled()) {
            $shouldEncrypt = true;
            if ($this->config['response_optional']) {
                $shouldEncrypt = $request->header($this->config['response_header_key']) === 'true';
            }

            $isJsonResponse = $response instanceof JsonResponse;

            return $shouldEncrypt && ($isJsonResponse || $this->hasJsonHeader($response));
        }

        return false;
    }

    /**
     * check is response has json header
     */
    protected function hasJsonHeader(Response $response)
    {
        return $response->headers->get('content-type') === 'application/json';
    }
}
