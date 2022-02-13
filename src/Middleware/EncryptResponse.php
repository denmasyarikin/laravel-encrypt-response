<?php

namespace Denmasyarikin\EncryptResponse\Middleware;

use Closure;
use Denmasyarikin\EncryptResponse\Contracts\Encryptor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class EncryptResponse extends BaseMiddleware
{
    /**
     * encryptor.
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
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($this->shouldEncrypt($request, $response) && !$this->inExceptArray($request)) {
            try {
                $data = json_decode($response->getContent(), true);
                $headers = $response->headers->all();
                $headers[$this->config['response_header_key']] = $this->config['response_driver'];
    
                return new JsonResponse(
                    $this->encrypt($data),
                    $response->getStatusCode(),
                    $headers
                );
            } catch (\Exception $error) {
                return new JsonResponse(
                    $this->encrypt(['message' => $error->getMessage()]),
                    500
                );
            }
        }

        return $response;
    }

    /**
     * let encrypt.
     */
    protected function encrypt(array $data)
    {
        $key = $this->config['response_key'];

        if (!$key) {
            throw new RuntimeException('No response_key set for encryption');
        }

        return $this->encryptor->encrypt($data, $key);
    }

    /**
     * determine response encryption.
     */
    protected function shouldEncrypt($request, $response): bool
    {
        if ($this->isServiceEnabled()) {
            $shouldEncrypt = true;
            if ($this->config['response_optional']) {
                $shouldEncrypt = $request->headers->has($this->config['response_header_key']);
            }

            $isJsonResponse = $response instanceof JsonResponse;

            return $shouldEncrypt && ($isJsonResponse || $this->hasJsonHeader($response));
        }

        return false;
    }

    /**
     * check is response has json header.
     */
    protected function hasJsonHeader(Response $response)
    {
        return 'application/json' === $response->headers->get('content-type');
    }
}
