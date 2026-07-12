<?php

namespace App\Toast\Contracts;

interface ToastManagerInterface
{
    public function success(string $message, ?string $title = null, array $options = []): void;
    public function error(string $message, ?string $title = null, array $options = []): void;
    public function warning(string $message, ?string $title = null, array $options = []): void;
    public function info(string $message, ?string $title = null, array $options = []): void;
    public function loading(string $message, ?string $title = null, array $options = []): void;
    
    /**
     * Send a raw toast.
     */
    public function send(string $type, string $message, ?string $title = null, array $options = []): void;

    /**
     * Push a raw toast payload.
     */
    public function push(array $payload): void;
}
