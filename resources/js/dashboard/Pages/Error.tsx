import { Head, router } from '@inertiajs/react';
import { Button } from '@dashboard/Components/ui/button';
    
export default function Error({ status }: { status: number }) {
    const title = {
        503: 'Layanan Tidak Tersedia',
        500: 'Kesalahan Server',
        404: 'Halaman Tidak Ditemukan',
        403: 'Dilarang Akses',
    }[status] || 'Kesalahan';

    const description = {
        503: 'Maaf, kami sedang melakukan pemeliharaan. Silakan coba lagi nanti.',
        500: 'Maaf, terjadi kesalahan pada server.',
        404: 'Maaf, halaman yang Anda cari tidak ditemukan.',
        403: 'Maaf, Anda tidak diizinkan mengakses halaman ini.',
    }[status] || 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.';

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
                        Ke Beranda
                    </Button>
                    <Button variant="outline" onClick={() => window.location.reload()}>
                        Coba Lagi
                    </Button>
                </div>
            </div>
        </div>
    );
}
