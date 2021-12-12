<?php

namespace Denmasyarikin\EncryptResponse\Middleware;

use Closure;
use Denmasyarikin\EncryptResponse\Contracts\Decryptor;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DecryptRequest extends BaseMiddleware
{
    /**
     * encryptor.
     *
     * @var \Denmasyarikin\EncryptResponse\Contracts\Decryptor
     */
    protected $decryptor;

    public function __construct(Decryptor $decryptor)
    {
        $this->decryptor = $decryptor;
        parent::__construct();
    }

    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldDecrypt($request) && !$this->inExceptArray($request)) {
            if (!$this->decryptor->validate($request)) {
                throw new BadRequestHttpException('Payload data is not encrypted');
            }

            $data = $this->decrypt($request->all());

            $request->replace($data);
        }

        return $next($request);
    }

    /**
     * let decrypt.
     */
    protected function decrypt(array $data)
    {
        $key = $this->config['request_key'];

        if (!$key) {
            throw new RuntimeException('No request_key set for decryption');
        }

        return $this->decryptor->decrypt(json_encode($data), $key);
    }

    /**
     * determine response encryption.
     */
    protected function shouldDecrypt($request)
    {
        $inMethod = in_array($request->method(), ['POST', 'PUT']);

        if ($inMethod && $this->isServiceEnabled('request')) {
            $shouldDecrypt = true;
            if ($this->config['request_optional']) {
                $shouldDecrypt = 'true' === $request->header($this->config['request_header_key']);
            }

            return $shouldDecrypt && count($request->all()) > 0;
        }

        return false;
    }
}
