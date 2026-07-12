<div 
    x-data="{ 
        open: false,
        submitConsent(level) {
            fetch('{{ route('cookie-consent.store') }}', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ level: level })
            }).then(response => {
                if (response.ok) {
                    this.open = false;
                    // Also dispatch to banner to hide it if they are both open
                    const bannerEl = document.querySelector('[x-data*=submitConsent]');
                    if (bannerEl && bannerEl.__x) {
                        bannerEl.__x.$data.show = false;
                    }
                    window.toast && window.toast.success('Cookie preferences updated.');
                }
            });
        }
    }"
    @open-cookie-preferences.window="open = true"
    @keydown.escape.window="open = false"
    x-show="open"
    class="relative z-[70]"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
    style="display: none;"
>
    <!-- Background overlay -->
    <div 
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-background/80 backdrop-blur-sm transition-opacity"
        @click="open = false"
    ></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div 
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-xl bg-surface border border-border-subtle text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
            >
                <div class="bg-surface px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold leading-6 text-foreground" id="modal-title">Cookie Preferences</h3>
                        <button @click="open = false" class="text-muted-foreground hover:text-foreground">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- Necessary -->
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h4 class="text-sm font-medium text-foreground">Strictly Necessary</h4>
                                <p class="text-xs text-muted-foreground mt-1">Required for the website to function properly. Cannot be disabled.</p>
                            </div>
                            <div class="relative inline-flex h-5 w-9 shrink-0 cursor-not-allowed items-center justify-center rounded-full">
                                <span class="absolute mx-auto h-4 w-8 rounded-full bg-primary/50 transition-colors"></span>
                                <span class="absolute right-1 inline-block h-3 w-3 transform rounded-full bg-white transition-transform"></span>
                            </div>
                        </div>

                        <!-- Analytics (Coming Soon) -->
                        <div class="flex items-start justify-between gap-4">
                            <div class="opacity-60">
                                <h4 class="text-sm font-medium text-foreground flex items-center gap-2">
                                    Analytics 
                                    <span class="text-[10px] font-bold bg-surface-muted text-muted-foreground px-1.5 py-0.5 rounded uppercase tracking-wider">Coming Soon</span>
                                </h4>
                                <p class="text-xs text-muted-foreground mt-1">Help us improve by allowing us to monitor usage.</p>
                            </div>
                            <div class="relative inline-flex h-5 w-9 shrink-0 cursor-not-allowed items-center justify-center rounded-full opacity-60">
                                <span class="absolute mx-auto h-4 w-8 rounded-full bg-surface-muted transition-colors"></span>
                                <span class="absolute left-1 inline-block h-3 w-3 transform rounded-full bg-muted-foreground transition-transform"></span>
                            </div>
                        </div>

                        <!-- Marketing (Coming Soon) -->
                        <div class="flex items-start justify-between gap-4">
                            <div class="opacity-60">
                                <h4 class="text-sm font-medium text-foreground flex items-center gap-2">
                                    Marketing 
                                    <span class="text-[10px] font-bold bg-surface-muted text-muted-foreground px-1.5 py-0.5 rounded uppercase tracking-wider">Coming Soon</span>
                                </h4>
                                <p class="text-xs text-muted-foreground mt-1">Used to deliver personalized advertisements.</p>
                            </div>
                            <div class="relative inline-flex h-5 w-9 shrink-0 cursor-not-allowed items-center justify-center rounded-full opacity-60">
                                <span class="absolute mx-auto h-4 w-8 rounded-full bg-surface-muted transition-colors"></span>
                                <span class="absolute left-1 inline-block h-3 w-3 transform rounded-full bg-muted-foreground transition-transform"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-surface-muted border-t border-border-subtle px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                    <button type="button" @click="submitConsent('all')" class="inline-flex w-full justify-center rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-primary-foreground shadow-sm hover:bg-primary-strong sm:w-auto">Accept All</button>
                    <button type="button" @click="submitConsent('necessary')" class="mt-3 inline-flex w-full justify-center rounded-lg bg-surface px-3 py-2 text-sm font-semibold text-foreground shadow-sm ring-1 ring-inset ring-border-strong hover:bg-surface-muted sm:mt-0 sm:w-auto">Save Preferences</button>
                </div>
            </div>
        </div>
    </div>
</div>
