import React from 'react';
import { Head, Link } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Search } from 'lucide-react';

interface PersonIndexItem {
    id: string;
    nik: string | null;
    nama_lengkap: string;
    tanggal_lahir: string | null;
    refs: Record<string, string> | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedData {
    data: PersonIndexItem[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
}

interface Props {
    persons: PaginatedData;
}

function Index({ persons }: Props) {
    const refsList = (refs: Record<string, string> | null): string => {
        if (!refs) return '-';
        return Object.values(refs).join(', ');
    };

    return (
        <>
            <Head title="Indeks NIK" />

            <div className="flex items-center justify-between mb-6">
                <div>
                    <h1 className="text-2xl font-bold">Indeks NIK</h1>
                    <p className="text-muted-foreground text-sm mt-1">
                        Indeks NIK Pusat — hanya baca
                    </p>
                </div>
            </div>

            <div className="bg-background rounded-xl border border-border-subtle overflow-hidden">
                <table className="w-full">
                    <thead>
                        <tr className="border-b border-border-subtle bg-surface/50">
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">NIK</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Nama Lengkap</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Tanggal Lahir</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Referensi Institusi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {persons.data.length === 0 ? (
                            <tr>
                                <td colSpan={4} className="px-6 py-12 text-center text-muted-foreground">
                                    <Search className="w-10 h-10 mx-auto mb-3 opacity-40" />
                                    <p>Belum ada data indeks NIK.</p>
                                </td>
                            </tr>
                        ) : (
                            persons.data.map((person) => (
                                <tr key={person.id} className="border-b border-border-subtle last:border-0 hover:bg-surface/30 transition-colors">
                                    <td className="px-6 py-4 font-mono text-sm">{person.nik ?? '-'}</td>
                                    <td className="px-6 py-4 font-medium text-sm">{person.nama_lengkap}</td>
                                    <td className="px-6 py-4 text-sm text-muted-foreground">
                                        {person.tanggal_lahir
                                            ? new Date(person.tanggal_lahir).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
                                            : '-'}
                                    </td>
                                    <td className="px-6 py-4 text-sm text-muted-foreground">{refsList(person.refs)}</td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>

            {persons.last_page > 1 && (
                <div className="flex items-center justify-center gap-1 mt-6">
                    {persons.links.map((link, i) => (
                        link.url ? (
                            <Link
                                key={i}
                                href={link.url}
                                className={`px-3 py-1.5 text-sm rounded-lg border transition-colors ${
                                    link.active
                                        ? 'bg-primary text-primary-foreground border-primary'
                                        : 'bg-background text-muted-foreground border-border-subtle hover:bg-surface'
                                }`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ) : (
                            <span
                                key={i}
                                className="px-3 py-1.5 text-sm rounded-lg border border-border-subtle text-muted-foreground/40"
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        )
                    ))}
                </div>
            )}
        </>
    );
}

Index.layout = (page: React.ReactNode) => <DashboardLayout>{page}</DashboardLayout>;

export default Index;
