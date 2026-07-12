<?php

use App\Toast\Contracts\ToastManagerInterface;

if (!function_exists('toast')) {
    /**
     * Get the toast manager instance or send a success toast.
     *
     * @param  string|null  $message
     * @param  string|null  $title
     * @return \App\Toast\Contracts\ToastManagerInterface
     */
    function toast(?string $message = null, ?string $title = null): ToastManagerInterface
    {
        $toastManager = app(ToastManagerInterface::class);

        if (!is_null($message)) {
            $toastManager->success($message, $title);
        }

        return $toastManager;
    }
}
