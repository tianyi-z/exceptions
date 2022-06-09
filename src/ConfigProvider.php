<?php
namespace YuanxinHealthy\Exceptions;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'exceptions' => [
                'handler' => [
                    'jsonrpc' => [
                        Handler\JsonRpcServerExceptionHandler::class,
                    ],
                ]
            ],
        ];
    }
}