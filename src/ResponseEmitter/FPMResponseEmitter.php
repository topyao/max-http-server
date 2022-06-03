<?php

namespace Max\HttpServer\ResponseEmitter;

use Max\HttpServer\Contracts\ResponseEmitterInterface;
use Psr\Http\Message\ResponseInterface;

class FPMResponseEmitter implements ResponseEmitterInterface
{
    public function emit(ResponseInterface $psrResponse, $sender = null)
    {
        foreach ($psrResponse->getHeaders() as $name => $value) {
            header($name, implode(', ', $value));
        }
        $body = $psrResponse->getBody();
        echo $body?->getContents();
    }
}