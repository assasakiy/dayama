import { Head, router } from '@inertiajs/react';
import { Button } from '@dashboard/Components/ui/button';
    
export default function Error({ status }: { status: number }) {
    const title = {
        503: 'Service Unavailable',
        500: 'Server Error',
        404: 'Page Not Found',
        403: 'Forbidden',
    }[status] || 'Error';

    const description = {
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
        500: 'Whoops, something went wrong on our servers.',
        404: 'Sorry, the page you are looking for could not be found.',
        403: 'Sorry, you are forbidden from accessing this page.',
    }[status] || 'An unexpected error occurred. Please try again later.';

    return (
        <div className="flex flex-col min-h-screen antialiased bg-background text-foreground items-center justify-center p-6">
            <Head title={title} />
            <div className="w-full max-w-md text-center space-y-6">
                <div className="text-[8rem] font-black leading-none text-primary/10 select-none">
                    {status}
                </div>
                
                <div className="space-y-2 relative z-10 -mt-16">
                    <h1 className="text-2xl font-bold tracking-tight">{title}</h1>
                    <p className="text-muted-foreground">{description}</p>
                </div>

                <div className="pt-6 flex items-center justify-center gap-4">
                    <Button onClick={() => router.visit('/')}>
                        Go Home
                    </Button>
                    <Button variant="outline" onClick={() => window.location.reload()}>
                        Try Again
                    </Button>
                </div>
            </div>
        </div>
    );
}
