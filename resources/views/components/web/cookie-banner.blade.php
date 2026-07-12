@inject('cookieConsent', 'App\CookieConsent\CookieConsentService')

@if(is_null($cookieConsent->level()))
<div 
    x-data="{ 
        show: true,
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
                    this.show = false;
                    window.toast && window.toast.success('Cookie preferences updated.');
                }
            }).catch(error => {
                console.error('Error saving cookie consent:', error);
            });
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-full"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-full"
    class="fixed bottom-0 inset-x-0 z-[60] bg-background border-t border-border-strong shadow-2xl p-4 sm:p-6"
    style="display: none;"
>
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-4 md:gap-8">
        <div class="flex-1 text-sm text-foreground">
            <h3 class="text-base font-semibold text-foreground mb-1">We value your privacy</h3>
            <p class="text-muted-foreground">
                We use cookies to enhance your browsing experience, serve personalized ads or content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.
            </p>
        </div>
        <div class="flex flex-wrap md:flex-nowrap items-center gap-3 w-full md:w-auto shrink-0 justify-center">
            <button 
                @click="$dispatch('open-cookie-preferences')"
                class="px-4 py-2 text-sm font-medium text-foreground bg-surface border border-border-strong rounded-lg hover:bg-surface-muted transition-colors flex-1 md:flex-none whitespace-nowrap"
            >
                Customize
            </button>
            <button 
                @click="submitConsent('necessary')"
                class="px-4 py-2 text-sm font-medium text-foreground bg-surface border border-border-strong rounded-lg hover:bg-surface-muted transition-colors flex-1 md:flex-none whitespace-nowrap"
            >
                Necessary Only
            </button>
            <button 
                @click="submitConsent('all')"
                class="px-4 py-2 text-sm font-medium text-primary-foreground bg-primary rounded-lg hover:bg-primary-strong transition-colors flex-1 md:flex-none whitespace-nowrap"
            >
                Accept All
            </button>
        </div>
    </div>
</div>
@endif
