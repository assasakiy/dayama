<?php

namespace App\Toast;

use App\Toast\Contracts\ToastManagerInterface;

class SessionToastManager implements ToastManagerInterface
{
    public function success(string $message, ?string $title = null, array $options = []): void
    {
        $this->send('success', $message, $title, $options);
    }

    public function error(string $message, ?string $title = null, array $options = []): void
    {
        $this->send('error', $message, $title, $options);
    }

    public function warning(string $message, ?string $title = null, array $options = []): void
    {
        $this->send('warning', $message, $title, $options);
    }

    public function info(string $message, ?string $title = null, array $options = []): void
    {
        $this->send('info', $message, $title, $options);
    }

    public function loading(string $message, ?string $title = null, array $options = []): void
    {
        $this->send('loading', $message, $title, $options);
    }

    public function send(string $type, string $message, ?string $title = null, array $options = []): void
    {
        $payload = array_merge([
            'type' => $type,
            'message' => $message,
            'title' => $title,
        ], $options);

        $this->push($payload);
    }

    public function push(array $payload): void
    {
        $toasts = session()->get('toast', []);
        
        // Upgrade legacy single toast format to array stack
        if (!is_array($toasts) || isset($toasts['type'])) {
            $toasts = []; 
        }

        $toasts[] = $payload;

        session()->flash('toast', $toasts);
    }
}
