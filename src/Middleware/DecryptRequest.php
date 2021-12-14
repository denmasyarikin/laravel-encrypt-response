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

    public function __construct(Decryptor $decryptor = null)
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

            $data = $this->decrypt($request);

            $request->replace($data);
        }

        return $next($request);
    }

    /**
     * let decrypt.
     */
    protected function decrypt(Request $request)
    {
        $key = $this->config['request_key'];
        
        if (!$key) {
            throw new RuntimeException('No request_key set for decryption');
        }

        $data = $request->all();

        return $this->decryptor->decrypt($data, $key);
    }

    /**
     * determine response encryption.
     */
    protected function shouldDecrypt($request)
    {
        $inMethod = in_array($request->method(), ['POST', 'PUT']);

        return $inMethod && $this->isServiceEnabled('request') && isset($this->decryptor);
    }
}
