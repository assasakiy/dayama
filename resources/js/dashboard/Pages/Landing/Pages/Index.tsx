import React from 'react';
import { Head, Link } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Btn } from '@dashboard/Components/ui/btn';
import { Badge } from '@dashboard/Components/ui/badge';
import {
    Home,
    GraduationCap,
    Pencil,
    FileText,
    Eye,
    EyeOff,
} from 'lucide-react';

interface Page {
    id: string;
    name: string;
    slug: string;
    sections: Record<string, any> | null;
    is_active: boolean;
    sort_order: number;
    created_at: string;
    updated_at: string;
}

const SLUG_ICONS: Record<string, React.ReactNode> = {
    home: <Home className="w-5 h-5" />,
    pendidikan: <GraduationCap className="w-5 h-5" />,
};

export default function PagesIndex({ pages }: { pages: Page[] }) {
    const sectionCount = (sections: Record<string, any> | null) => {
        if (!sections) return 0;
        return Object.keys(sections).length;
    };

    return (
        <DashboardLayout>
            <Head title="Halaman Depan" />

            <div className="space-y-6">
                {/* Header */}
                <div>
                    <h1 className="text-2xl font-bold text-foreground">Halaman Depan</h1>
                    <p className="text-sm text-muted-foreground mt-1">
                        Kelola konten halaman-halaman landing website Anda.
                    </p>
                </div>

                {/* Page Cards */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {pages.map((page) => (
                        <div
                            key={page.id}
                            className="group relative rounded-lg border border-border-subtle bg-background p-6 transition-all hover:shadow-sm hover:border-primary/20"
                        >
                            {/* Status Badge */}
                            <div className="absolute top-4 right-4">
                                {page.is_active ? (
                                    <Badge variant="default" className="bg-success/10 text-success border-success/20">
                                        <Eye className="w-3 h-3 mr-1" /> Aktif
                                    </Badge>
                                ) : (
                                    <Badge variant="secondary" className="bg-zinc-500/10 text-zinc-500 border-zinc-500/20">
                                        <EyeOff className="w-3 h-3 mr-1" /> Nonaktif
                                    </Badge>
                                )}
                            </div>

                            {/* Icon & Title */}
                            <div className="flex items-center gap-3 mb-4">
                                <div className="flex items-center justify-center w-10 h-10 rounded-lg bg-primary/10 text-primary">
                                    {SLUG_ICONS[page.slug] || <FileText className="w-5 h-5" />}
                                </div>
                                <div>
                                    <h3 className="font-semibold text-foreground">{page.name}</h3>
                                    <p className="text-xs text-muted-foreground font-mono">/{page.slug}</p>
                                </div>
                            </div>

                            {/* Stats */}
                            <div className="flex items-center gap-4 text-sm text-muted-foreground mb-5">
                                <span>{sectionCount(page.sections)} section</span>
                                <span>•</span>
                                <span>Diperbarui {new Date(page.updated_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}</span>
                            </div>

                            <div className="flex gap-2">
                                <Link href={`/landing/pages/${page.id}/edit`} className="flex-1">
                                    <Btn variant="outline" size="sm" className="w-full" icon={<Pencil className="w-3.5 h-3.5" />}>
                                        Edit Konten
                                    </Btn>
                                </Link>
                            </div>
                        </div>
                    ))}
                </div>

                {pages.length === 0 && (
                    <div className="text-center py-16 text-muted-foreground rounded-lg border border-border-subtle bg-background">
                        <FileText className="w-12 h-12 mx-auto mb-4 opacity-40" />
                        <p className="text-lg font-medium">Belum ada halaman</p>
                        <p className="text-sm">Jalankan seeder untuk menambahkan halaman default.</p>
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
