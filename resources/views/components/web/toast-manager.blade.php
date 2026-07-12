@props([])

<div 
    x-data="toastManager()"
    @toast.window="addToast($event.detail)"
    class="fixed bottom-4 right-4 z-[100] flex flex-col gap-2 pointer-events-none"
>
    <!-- Server-side flashed toast -->
    @if(session()->has('toast'))
        @php
            $toasts = session('toast');
            if (!is_array($toasts) || isset($toasts['type'])) {
                $toasts = [$toasts];
            }
        @endphp
        @foreach($toasts as $toast)
            <div x-init="addToast({ type: '{{ $toast['type'] ?? 'info' }}', message: '{{ addslashes($toast['message'] ?? '') }}' })"></div>
        @endforeach
    @elseif(session()->has('success'))
        <div x-init="addToast({ type: 'success', message: '{{ addslashes(session('success')) }}' })"></div>
    @elseif(session()->has('error'))
        <div x-init="addToast({ type: 'error', message: '{{ addslashes(session('error')) }}' })"></div>
    @elseif(session()->has('status'))
        <div x-init="addToast({ type: 'success', message: '{{ addslashes(session('status')) }}' })"></div>
    @endif

    <template x-for="toast in toasts" :key="toast.id">
        <div 
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-2"
            :class="{
                'bg-primary text-primary-foreground border-primary': toast.type === 'success',
                'bg-destructive text-destructive-foreground border-destructive': toast.type === 'error',
                'bg-warning text-warning-foreground border-warning': toast.type === 'warning',
                'bg-info text-info-foreground border-info': toast.type === 'info' || toast.type === 'loading'
            }"
            class="flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg border pointer-events-auto"
        >
            <!-- Icons -->
            <template x-if="toast.type === 'success'">
                <svg class="w-5 h-5 text-primary-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </template>
            <template x-if="toast.type === 'error'">
                <svg class="w-5 h-5 text-destructive-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </template>
            <template x-if="toast.type === 'warning'">
                <svg class="w-5 h-5 text-warning-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </template>
            <template x-if="toast.type === 'info'">
                <svg class="w-5 h-5 text-info-foreground" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </template>
            <template x-if="toast.type === 'loading'">
                <svg class="w-5 h-5 text-info-foreground animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </template>

            <p class="text-sm font-medium text-inherit/90" x-text="toast.message"></p>
        </div>
    </template>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('toastManager', () => ({
            toasts: [],
            addToast(toast) {
                const id = Date.now() + Math.random();
                const newToast = {
                    id: id,
                    type: toast.type || 'info',
                    message: toast.message,
                    visible: true,
                };
                this.toasts.push(newToast);

                if (newToast.type !== 'loading') {
                    setTimeout(() => {
                        this.removeToast(id);
                    }, 3000);
                }
            },
            removeToast(id) {
                const toastIndex = this.toasts.findIndex(t => t.id === id);
                if (toastIndex !== -1) {
                    this.toasts[toastIndex].visible = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 300); // Wait for transition
                }
            }
        }));

        // Expose globally for JS logic outside Alpine
        window.toast = {
            success: (msg) => window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: msg } })),
            error: (msg) => window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: msg } })),
            warning: (msg) => window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'warning', message: msg } })),
            info: (msg) => window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'info', message: msg } })),
            loading: (msg) => window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'loading', message: msg } }))
        };
    });
</script>
