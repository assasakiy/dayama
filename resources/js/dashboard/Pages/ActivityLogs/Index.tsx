import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import { Activity, User, CalendarDays, Trash2, Filter, X, Eye } from 'lucide-react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogClose, DialogDescription } from '@dashboard/Components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@dashboard/Components/ui/select';

const eventBadge = (event: string) => {
    const map: Record<string, string> = {
        created: 'bg-green-50 text-green-700 border-green-200 dark:bg-green-950/40 dark:text-green-300',
        updated: 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300',
        deleted: 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/40 dark:text-red-300',
        restored: 'bg-success/10 text-success border-success/20 dark:bg-emerald-950/40 dark:text-emerald-300',
    };
    const key = Object.keys(map).find((k) => event?.toLowerCase().includes(k)) ?? '';
    const cls = map[key] ?? 'bg-warning/10 text-warning border-warning/20 dark:bg-yellow-950/40 dark:text-yellow-300';
    return (
        <span className={`inline-flex items-center px-2 py-0.5 text-[11px] font-medium rounded-full border ${cls}`}>
            {event ?? '—'}
        </span>
    );
};

function UserAvatar({ user }: { user: any }) {
    const safeName = user.name || 'Pengguna Tidak Diketahui';
    const initials = safeName.split(' ').map((n: string) => n[0]).join('').slice(0, 2).toUpperCase();
    const colors = ['from-violet-500 to-purple-500', 'from-blue-500 to-cyan-500', 'from-green-500 to-emerald-500', 'from-orange-500 to-amber-500', 'from-pink-500 to-rose-500'];
    const color = colors[safeName.charCodeAt(0) % colors.length];
    if (user.avatar_url) return <img src={user.avatar_url} alt={user.name} className="w-7 h-7 rounded-full object-cover ring-2 ring-background" />;
    return <div className={`w-7 h-7 rounded-full bg-gradient-to-tr ${color} flex items-center justify-center text-white text-[10px] font-bold ring-2 ring-background shrink-0`}>{initials}</div>;
}

export default function ActivityLogsIndex({ logs, events, filters, can }: {
    logs: any;
    events: string[];
    filters: Record<string, string>;
    can: { see_all: boolean; delete: boolean };
}) {
    const [filterEvent, setFilterEvent] = useState(filters.event || 'all');
    const [filterDateFrom, setFilterDateFrom] = useState(filters.date_from ?? '');
    const [filterDateTo, setFilterDateTo] = useState(filters.date_to ?? '');
    const [deleteId, setDeleteId] = useState<string | null>(null);
    const [selectedLog, setSelectedLog] = useState<any>(null);
    const [selectedIds, setSelectedIds] = useState<string[]>([]);
    const [isBulkDeleteOpen, setIsBulkDeleteOpen] = useState(false);

    const updateFilters = (key: string, value: string) => {
        const newFilters = {
            event: key === 'event' ? (value === 'all' ? undefined : value) : (filterEvent === 'all' ? undefined : filterEvent),
            date_from: key === 'date_from' ? (value || undefined) : (filterDateFrom || undefined),
            date_to: key === 'date_to' ? (value || undefined) : (filterDateTo || undefined),
        };
        
        if (key === 'event') setFilterEvent(value || 'all');
        if (key === 'date_from') setFilterDateFrom(value);
        if (key === 'date_to') setFilterDateTo(value);

        router.get('/activity-logs', newFilters, { preserveState: true, preserveScroll: true });
    };

    const hasFilter = !!(filters.event || filters.date_from || filters.date_to);

    const handleDelete = () => {
        if (!deleteId) return;
        router.delete(`/activity-logs/${deleteId}`, { preserveScroll: true });
        setDeleteId(null);
        setSelectedIds(prev => prev.filter(id => id !== deleteId));
    };

    const toggleSelectAll = () => {
        if (selectedIds.length === logs.data.length && logs.data.length > 0) {
            setSelectedIds([]);
        } else {
            setSelectedIds(logs.data.map((log: any) => log.id));
        }
    };

    const toggleSelect = (id: string) => {
        if (selectedIds.includes(id)) {
            setSelectedIds(selectedIds.filter(selectedId => selectedId !== id));
        } else {
            setSelectedIds([...selectedIds, id]);
        }
    };

    const handleBulkDelete = () => {
        if (selectedIds.length === 0) return;
        router.delete('/activity-logs', {
            data: { ids: selectedIds },
            preserveScroll: true,
            onSuccess: () => {
                setSelectedIds([]);
                setIsBulkDeleteOpen(false);
            },
        });
    };

    return (
        <DashboardLayout>
            <Head title="Log Aktivitas" />
            <div className="space-y-5">

                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="hidden md:block">
                        <h1 className="text-xl font-semibold tracking-tight flex items-center gap-2">
                            Log Aktivitas
                        </h1>
                        <p className="hidden lg:block text-sm text-muted-foreground mt-0.5">
                            {can.see_all ? 'Jejak audit semua aktivitas sistem.' : 'Aktivitas akun Anda baru-baru ini.'}
                        </p>
                    </div>
                </div>

                {/* Filter bar */}
                <div className="bg-background border border-border-subtle rounded-lg p-3 flex flex-col md:flex-row md:items-end gap-3">
                    <div className="w-full md:flex-1 md:min-w-[150px]">
                        <label className="text-xs text-muted-foreground font-medium mb-1.5 block">Tipe Kejadian</label>
                        <Select value={filterEvent} onValueChange={v => updateFilters('event', v)}>
                            <SelectTrigger className="w-full bg-background">
                                <SelectValue placeholder="Semua Kejadian" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Semua Kejadian</SelectItem>
                                {events.map(e => <SelectItem key={e} value={e}>{e}</SelectItem>)}
                            </SelectContent>
                        </Select>
                    </div>
                    
                    <div className="flex gap-3 w-full md:w-auto">
                        <div className="flex-1 md:min-w-[140px]">
                            <label className="text-xs text-muted-foreground font-medium mb-1.5 block">Dari Tanggal</label>
                            <input type="date" value={filterDateFrom} onChange={e => updateFilters('date_from', e.target.value)}
                                className="w-full h-9 flex items-center justify-between rounded-md border border-border-subtle bg-background px-3 py-1.5 text-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-primary hover:border-primary" />
                        </div>
                        <div className="flex-1 md:min-w-[140px]">
                            <label className="text-xs text-muted-foreground font-medium mb-1.5 block">Ke Tanggal</label>
                            <input type="date" value={filterDateTo} onChange={e => updateFilters('date_to', e.target.value)}
                                className="w-full h-9 flex items-center justify-between rounded-md border border-border-subtle bg-background px-3 py-1.5 text-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-primary hover:border-primary" />
                        </div>
                    </div>
                </div>

                {/* Table */}
                <div className="bg-background border border-border-subtle rounded-lg overflow-hidden relative">
                    {/* Bulk Actions */}
                    {selectedIds.length > 0 && can.delete && (
                        <div className="absolute top-0 left-0 w-full h-12 bg-surface border-b border-border-subtle flex items-center justify-between px-4 z-10">
                            <span className="text-xs font-medium text-muted-foreground">{selectedIds.length} log terpilih</span>
                            <div className="flex items-center gap-2">
                                <button onClick={() => setSelectedIds([])} className="h-8 px-3 bg-surface-muted text-muted-foreground rounded-md text-xs font-medium hover:bg-surface hover:text-foreground transition-colors border border-border-subtle">
                                    Batalkan Semua
                                </button>
                                <button onClick={() => setIsBulkDeleteOpen(true)} className="h-8 px-3 bg-danger text-danger-foreground rounded-md text-xs font-semibold flex items-center gap-1.5 hover:opacity-90 transition-opacity">
                                    <Trash2 className="w-3.5 h-3.5" /> Hapus Terpilih
                                </button>
                            </div>
                        </div>
                    )}
                    
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-border-subtle bg-surface-muted/50">
                                {can.delete && (
                                    <th className="w-10 px-4 py-3">
                                        <input 
                                            type="checkbox" 
                                            className="rounded border-border-subtle bg-background text-primary focus:ring-primary h-4 w-4"
                                            checked={logs.data.length > 0 && selectedIds.length === logs.data.length}
                                            onChange={toggleSelectAll}
                                        />
                                    </th>
                                )}
                                <th className="text-left px-4 py-3 font-medium">Waktu</th>
                                {can.see_all && <th className="text-left px-4 py-3 font-medium hidden md:table-cell">Pelaku</th>}
                                <th className="text-left px-4 py-3 font-medium hidden sm:table-cell">Kejadian</th>
                                <th className="text-left px-4 py-3 font-medium hidden lg:table-cell">Subjek</th>
                                <th className="text-left px-4 py-3 font-medium">Deskripsi</th>
                                <th className="text-right px-4 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border-subtle">
                            {logs.data.length === 0 ? (
                                <tr>
                                    <td colSpan={can.delete ? 7 : 5} className="px-4 py-14 text-center">
                                        <Activity className="w-10 h-10 text-muted-foreground/30 mx-auto mb-2" />
                                        <p className="text-sm font-medium text-foreground">Tidak ada aktivitas</p>
                                        <p className="text-xs text-muted-foreground mt-1">
                                            {hasFilter ? 'Coba hapus filter.' : 'Aktivitas akan muncul di sini saat tindakan dilakukan.'}
                                        </p>
                                    </td>
                                </tr>
                            ) : logs.data.map((log: any) => (
                                <tr key={log.id} className="hover:bg-surface-muted/30 transition-colors">
                                    {can.delete && (
                                        <td className="px-4 py-3">
                                            <input 
                                                type="checkbox" 
                                                className="rounded border-border-subtle bg-background text-primary focus:ring-primary h-4 w-4"
                                                checked={selectedIds.includes(log.id)}
                                                onChange={() => toggleSelect(log.id)}
                                            />
                                        </td>
                                    )}
                                    <td className="px-4 py-3 whitespace-nowrap">
                                        <span className="inline-flex items-center gap-1.5 text-xs text-muted-foreground">
                                            <CalendarDays className="w-3.5 h-3.5 shrink-0" />
                                            {log.created_at_human}
                                        </span>
                                    </td>
                                    {can.see_all && (
                                        <td className="px-4 py-3 hidden md:table-cell">
                                            {log.causer ? (
                                                <div className="flex items-center gap-2">
                                                    <UserAvatar user={log.causer} />
                                                    <span className="text-xs font-medium">{log.causer.name}</span>
                                                </div>
                                            ) : <span className="text-muted-foreground text-xs">Sistem</span>}
                                        </td>
                                    )}
                                    <td className="px-4 py-3 hidden sm:table-cell">{eventBadge(log.event)}</td>
                                    <td className="px-4 py-3 hidden lg:table-cell text-xs text-muted-foreground font-mono">
                                        {log.subject_type ? `${log.subject_type} #${log.subject_id?.slice(0, 8)}` : '—'}
                                    </td>
                                    <td className="px-4 py-3 text-xs text-muted-foreground max-w-xs truncate">
                                        {log.description ?? '—'}
                                    </td>
                                    <td className="px-4 py-3 text-right whitespace-nowrap">
                                        <button
                                            onClick={() => setSelectedLog(log)}
                                            className="p-1.5 rounded-md text-muted-foreground hover:text-primary hover:bg-primary/10 transition-colors mr-1"
                                            title="Lihat detail"
                                        >
                                            <Eye className="w-3.5 h-3.5" />
                                        </button>
                                        {can.delete && (
                                            <button
                                                onClick={() => setDeleteId(log.id)}
                                                className="p-1.5 rounded-md text-muted-foreground hover:text-danger hover:bg-danger/10 transition-colors"
                                                title="Hapus log"
                                            >
                                                <Trash2 className="w-3.5 h-3.5" />
                                            </button>
                                        )}
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    {/* Pagination */}
                    {logs.last_page > 1 && (
                        <div className="px-4 py-3 border-t border-border-subtle flex items-center justify-between">
                            <span className="text-xs text-muted-foreground">
                                Menampilkan {logs.from}–{logs.to} dari {logs.total}
                            </span>
                            <div className="flex items-center gap-1">
                                {logs.links.map((link: any, i: number) => (
                                    link.url ? (
                                        <Link key={i} href={link.url}
                                            className={`h-8 min-w-[32px] px-2 flex items-center justify-center rounded text-xs font-medium transition-colors ${link.active ? 'bg-primary text-primary-foreground' : 'bg-surface-muted hover:bg-surface text-muted-foreground border border-border-subtle'}`}
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                        />
                                    ) : (
                                        <span key={i} className="h-8 min-w-[32px] px-2 flex items-center justify-center text-xs text-muted-foreground/40" dangerouslySetInnerHTML={{ __html: link.label }} />
                                    )
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>

            <ConfirmDialog
                open={!!deleteId}
                onOpenChange={(open) => { if (!open) setDeleteId(null); }}
                title="Hapus Entri Log"
                message="Apakah Anda yakin ingin menghapus log aktivitas ini secara permanen? Tindakan ini tidak dapat dibatalkan."
                confirmLabel="Hapus"
                variant="danger"
                onConfirm={handleDelete}
            />

            <ConfirmDialog
                open={isBulkDeleteOpen}
                onOpenChange={setIsBulkDeleteOpen}
                title="Bulk Delete Logs"
                message={`Are you sure you want to permanently delete ${selectedIds.length} selected activity logs? This action cannot be undone.`}
                confirmLabel="Delete Selected"
                variant="danger"
                onConfirm={handleBulkDelete}
            />

            {/* View Details Dialog */}
            <Dialog open={!!selectedLog} onOpenChange={(open) => { if (!open) setSelectedLog(null); }}>
                <DialogContent className="max-w-3xl max-h-[85vh] flex flex-col">
                    <DialogHeader>
                        <DialogTitle>Activity Details</DialogTitle>
                        <DialogDescription>
                            Full information about this activity log entry.
                        </DialogDescription>
                    </DialogHeader>
                    {selectedLog && (
                        <div className="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6">
                            {/* Summary Grid */}
                            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div className="space-y-1">
                                    <span className="text-xs font-medium text-muted-foreground uppercase tracking-wider">Event</span>
                                    <div>{eventBadge(selectedLog.event)}</div>
                                </div>
                                <div className="space-y-1">
                                    <span className="text-xs font-medium text-muted-foreground uppercase tracking-wider">Date & Time</span>
                                    <div className="text-sm font-medium">{new Date(selectedLog.created_at).toLocaleString()}</div>
                                </div>
                                <div className="space-y-1">
                                    <span className="text-xs font-medium text-muted-foreground uppercase tracking-wider">Actor</span>
                                    <div className="text-sm font-medium flex items-center gap-2 mt-1">
                                        {selectedLog.causer ? (
                                            <>
                                                <UserAvatar user={selectedLog.causer} />
                                                <span>{selectedLog.causer.name}</span>
                                            </>
                                        ) : (
                                            <span>System</span>
                                        )}
                                    </div>
                                </div>
                                <div className="space-y-1">
                                    <span className="text-xs font-medium text-muted-foreground uppercase tracking-wider">Subject Type</span>
                                    <div className="text-sm font-medium font-mono">{selectedLog.subject_type || '—'}</div>
                                </div>
                                <div className="space-y-1">
                                    <span className="text-xs font-medium text-muted-foreground uppercase tracking-wider">Subject ID</span>
                                    <div className="text-sm font-medium font-mono">{selectedLog.subject_id || '—'}</div>
                                </div>
                                <div className="space-y-1">
                                    <span className="text-xs font-medium text-muted-foreground uppercase tracking-wider">Log Name</span>
                                    <div className="text-sm font-medium">{selectedLog.log_name || '—'}</div>
                                </div>
                            </div>
                            
                            {/* Description */}
                            <div className="space-y-1 border-t border-border-subtle pt-5">
                                <span className="text-xs font-medium text-muted-foreground uppercase tracking-wider">Description</span>
                                <p className="text-sm">{selectedLog.description || '—'}</p>
                            </div>

                            {/* Properties JSON */}
                            {((selectedLog.properties && Object.keys(selectedLog.properties).length > 0) || (selectedLog.attribute_changes && Object.keys(selectedLog.attribute_changes).length > 0)) && (
                                <div className="space-y-1 border-t border-border-subtle pt-5">
                                    <span className="text-xs font-medium text-muted-foreground uppercase tracking-wider mb-2 block">Properties / Changes</span>
                                    
                                    {selectedLog.attribute_changes?.old || selectedLog.attribute_changes?.attributes || selectedLog.properties?.old || selectedLog.properties?.attributes ? (
                                        <div className="bg-background border border-border-subtle rounded-md overflow-hidden">
                                            <table className="w-full text-left text-sm">
                                                <thead className="bg-surface-muted/50 border-b border-border-subtle text-xs uppercase text-muted-foreground">
                                                    <tr>
                                                        <th className="px-4 py-2 font-medium">Field</th>
                                                        <th className="px-4 py-2 font-medium border-l border-border-subtle">Old Value</th>
                                                        <th className="px-4 py-2 font-medium border-l border-border-subtle">New Value</th>
                                                    </tr>
                                                </thead>
                                                <tbody className="divide-y divide-border-subtle">
                                                    {Array.from(new Set([
                                                        ...Object.keys(selectedLog.attribute_changes?.old || selectedLog.properties?.old || {}),
                                                        ...Object.keys(selectedLog.attribute_changes?.attributes || selectedLog.properties?.attributes || {})
                                                    ])).map(key => {
                                                        const oldObj = selectedLog.attribute_changes?.old || selectedLog.properties?.old || {};
                                                        const newObj = selectedLog.attribute_changes?.attributes || selectedLog.properties?.attributes || {};
                                                        return (
                                                        <tr key={key} className="hover:bg-surface-muted/30 transition-colors">
                                                            <td className="px-4 py-2.5 font-mono text-xs text-foreground bg-surface-muted/10">{key}</td>
                                                            <td className="px-4 py-2.5 border-l border-border-subtle font-mono text-xs text-red-600 dark:text-red-400 break-all">
                                                                {oldObj[key] !== undefined ? JSON.stringify(oldObj[key]) : <span className="text-muted-foreground italic">none</span>}
                                                            </td>
                                                            <td className="px-4 py-2.5 border-l border-border-subtle font-mono text-xs text-green-600 dark:text-green-400 break-all">
                                                                {newObj[key] !== undefined ? JSON.stringify(newObj[key]) : <span className="text-muted-foreground italic">none</span>}
                                                            </td>
                                                        </tr>
                                                        );
                                                    })}
                                                </tbody>
                                            </table>
                                        </div>
                                    ) : (
                                        <div className="bg-surface-muted border border-border-subtle rounded-md overflow-x-auto p-4">
                                            <pre className="text-xs font-mono text-foreground">
                                                {JSON.stringify(selectedLog.properties, null, 2)}
                                            </pre>
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>
                    )}
                </DialogContent>
            </Dialog>
        </DashboardLayout>
    );
}
