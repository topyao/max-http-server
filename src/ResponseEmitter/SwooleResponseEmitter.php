<?php

declare(strict_types=1);

/**
 * This file is part of the Max package.
 *
 * (c) Cheng Yao <987861463@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max\HttpServer\ResponseEmitter;

use Max\HttpMessage\Cookie;
use Max\HttpMessage\Stream\FileStream;
use Max\HttpMessage\Stream\StringStream;
use Max\HttpServer\Contracts\ResponseEmitterInterface;
use Psr\Http\Message\ResponseInterface;
use Swoole\Http\Response;

class SwooleResponseEmitter implements ResponseEmitterInterface
{
    /**
     * @param ResponseInterface $psrResponse
     * @param                   $sender
     *
     * @return void
     */
    public function emit(ResponseInterface $psrResponse, $sender = null): void
    {
        $sender->status($psrResponse->getStatusCode());
        foreach ($psrResponse->getHeader('Set-Cookie') as $str) {
            $this->sendCookie(Cookie::parse($str), $sender);
        }
        $psrResponse = $psrResponse->withoutHeader('Set-Cookie');

        foreach ($psrResponse->getHeaders() as $name => $value) {
            $sender->header($name, $value);
        }
        $body = $psrResponse->getBody();
        switch (true) {
            case $body instanceof FileStream:
                $sender->sendfile($body->getMetadata('uri'));
                break;
            case $body instanceof StringStream:
                $sender->end($body->getContents());
                break;
            default:
                $sender->end();
        }
        $body?->close();
    }

    /**
     * @param Cookie   $cookie
     * @param Response $response
     *
     * @return void
     */
    protected function sendCookie(Cookie $cookie, Response $response): void
    {
        $response->cookie(
            $cookie->getName(), $cookie->getValue(),
            $cookie->getExpires(), $cookie->getPath(),
            $cookie->getDomain(), $cookie->isSecure(),
            $cookie->isHttponly(), $cookie->getSamesite()
        );
    }
}
