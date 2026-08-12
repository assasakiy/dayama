import React from 'react';
import { Head, Link } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { History } from 'lucide-react';

interface TransferLogItem {
    id: string;
    nik: string | null;
    created_at: string;
    from_institution: { id: string; name: string } | null;
    to_institution: { id: string; name: string } | null;
    source_person: { id: string; nama_lengkap: string } | null;
    destination_person: { id: string; nama_lengkap: string } | null;
    trigger: { id: string; name?: string; person?: { nama_lengkap: string } } | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedData {
    data: TransferLogItem[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
}

interface Props {
    logs: PaginatedData;
}

function Index({ logs }: Props) {
    const triggerName = (trigger: TransferLogItem['trigger']): string => {
        if (!trigger) return '-';
        return trigger.name || trigger.person?.nama_lengkap || '-';
    };

    return (
        <>
            <Head title="Log Transfer" />

            <div className="flex items-center justify-between mb-6">
                <div>
                    <h1 className="text-2xl font-bold">Log Transfer</h1>
                    <p className="text-muted-foreground text-sm mt-1">
                        Audit trail transfer person
                    </p>
                </div>
            </div>

            <div className="bg-background rounded-xl border border-border-subtle overflow-hidden">
                <table className="w-full">
                    <thead>
                        <tr className="border-b border-border-subtle bg-surface/50">
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Waktu</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">NIK</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Dari</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Ke</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Sumber</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Tujuan</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        {logs.data.length === 0 ? (
                            <tr>
                                <td colSpan={7} className="px-6 py-12 text-center text-muted-foreground">
                                    <History className="w-10 h-10 mx-auto mb-3 opacity-40" />
                                    <p>Belum ada log transfer.</p>
                                </td>
                            </tr>
                        ) : (
                            logs.data.map((log) => (
                                <tr key={log.id} className="border-b border-border-subtle last:border-0 hover:bg-surface/30 transition-colors">
                                    <td className="px-6 py-4 text-sm text-muted-foreground whitespace-nowrap">
                                        {new Date(log.created_at).toLocaleDateString('id-ID', {
                                            day: 'numeric', month: 'short', year: 'numeric',
                                            hour: '2-digit', minute: '2-digit',
                                        })}
                                    </td>
                                    <td className="px-6 py-4 font-mono text-sm">{log.nik ?? '-'}</td>
                                    <td className="px-6 py-4 text-sm">{log.from_institution?.name ?? '-'}</td>
                                    <td className="px-6 py-4 text-sm">{log.to_institution?.name ?? '-'}</td>
                                    <td className="px-6 py-4 text-sm">{log.source_person?.nama_lengkap ?? '-'}</td>
                                    <td className="px-6 py-4 text-sm">{log.destination_person?.nama_lengkap ?? '-'}</td>
                                    <td className="px-6 py-4 text-sm">{triggerName(log.trigger)}</td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>

            {logs.last_page > 1 && (
                <div className="flex items-center justify-center gap-1 mt-6">
                    {logs.links.map((link, i) => (
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
