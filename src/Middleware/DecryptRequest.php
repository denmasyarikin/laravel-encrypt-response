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
     * encryptor
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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->shouldDecrypt($request)) {
            $payload = $request->input($this->config['request_body_key']);
            if (is_null($payload)) {
                throw new BadRequestHttpException($this->config['request_body_key'].' is required');
            }

            $data = $this->decrypt($payload);

            $request->replace($data);
        }

        return $next($request);
    }

    /**
     * let decrypt
     */
    protected function decrypt($plain)
    {
        $key = $this->config['request_key'];

        if (!$key) {
            throw new RuntimeException('No request_key set for decryption');
        }

        return json_decode($this->decryptor->decrypt($plain, $key), true);
    }

    /**
     * determine response encryption
     */
    protected function shouldDecrypt($request)
    {
        $inMethod = in_array($request->method(), ['POST', 'PUT']);

        if ($inMethod && $this->isServiceEnabled('request')) {
            $shouldDecrypt = true;
            if ($this->config['request_optional']) {
                $shouldDecrypt = $request->header($this->config['request_header_key']) === 'true';
            }

            return $shouldDecrypt;
        }

        return false;
    }
}
