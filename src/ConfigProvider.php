<?php
namespace YuanxinHealthy\Exceptions;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'exceptions' => [
                'jsonrpc' => [
                    Handler\JsonRpcServerExceptionHandler::class,
                ],
            ],
        ];
    }
}