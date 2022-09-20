<?php

namespace Max\Http\Server;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'bindings' => [
                'Max\Http\Server\Contract\RouteDispatcherInterface' => 'Max\Http\Server\RouteDispatcher',
            ]
        ];
    }
}