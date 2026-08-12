import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Button } from '@dashboard/Components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogClose, DialogFooter } from '@dashboard/Components/ui/dialog';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import { Plus, KeyRound, Pencil, Trash2, X, ChevronDown, ChevronRight, Sparkles, CheckCircle2, Save } from 'lucide-react';
import { Btn } from '@dashboard/Components/ui/btn';

interface Permission {
    id: string;
    name: string;
    module?: string | null;
    action?: string | null;
    scope?: string | null;
    description?: string | null;
    guard_name: string;
    roles_count: number;
    created_at: string;
    group_ids: string[];
}

interface PermissionGroupItem {
    id: string;
    name: string;
    slug: string;
    icon?: string;
    color?: string;
}

interface GroupedPermissions {
    [module: string]: Permission[];
}

const CMS_MODULES = [
    'dashboard', 'posts', 'pages', 'media', 'comments',
    'categories', 'tags', 'users', 'roles', 'settings', 'analytics',
];

const CMS_ACTIONS: Record<string, string[]> = {
    dashboard:  ['view'],
    posts:      ['view', 'create', 'edit', 'delete', 'publish', 'restore', 'force-delete'],
    pages:      ['view', 'create', 'edit', 'delete', 'publish'],
    media:      ['view', 'upload', 'edit', 'delete'],
    comments:   ['view', 'reply', 'delete', 'moderate'],
    categories: ['view', 'create', 'edit', 'delete'],
    tags:       ['view', 'create', 'edit', 'delete'],
    users:      ['view', 'create', 'edit', 'delete'],
    roles:      ['view', 'create', 'edit', 'delete'],
    settings:   ['view', 'update'],
    analytics:  ['view'],
};

const ACTION_COLORS: Record<string, string> = {
    view: 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800',
    create: 'bg-green-50 text-green-700 border-green-200 dark:bg-green-950/40 dark:text-green-300 dark:border-green-800',
    edit: 'bg-warning/10 text-warning border-warning/20 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
    delete: 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/40 dark:text-red-300 dark:border-red-800',
    publish: 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800',
    moderate: 'bg-warning/10 text-warning border-warning/20 dark:bg-orange-950/40 dark:text-orange-300 dark:border-orange-800',
    upload: 'bg-info/10 text-info border-info/20 dark:bg-teal-950/40 dark:text-teal-300 dark:border-teal-800',
    restore: 'bg-cyan-50 text-cyan-700 border-cyan-200 dark:bg-cyan-950/40 dark:text-cyan-300 dark:border-cyan-800',
    update: 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800',
    reply: 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-800',
};

const MODULE_LABELS: Record<string, string> = {
    dashboard: 'Dashboard', posts: 'Posts', pages: 'Pages', media: 'Media',
    comments: 'Comments', categories: 'Categories', tags: 'Tags', users: 'Users',
    roles: 'Roles', settings: 'Settings', analytics: 'Analytics', other: 'Other',
};

const SCOPE_LABELS: Record<string, string> = { own: '·Own', all: '·All', assigned: '·Assigned' };

export default function PermissionIndex({ permissions, grouped, permissionGroups }: { permissions: Permission[]; grouped: GroupedPermissions; permissionGroups?: PermissionGroupItem[] }) {
    const [modalOpen, setModalOpen] = useState(false);
    const [editPermission, setEditPermission] = useState<Permission | null>(null);
    const [deleteTarget, setDeleteTarget] = useState<{ id: string; name: string } | null>(null);
    const [seedConfirm, setSeedConfirm] = useState(false);
    const [collapsed, setCollapsed] = useState<Set<string>>(new Set());

    // Form fields
    const [module, setModule] = useState('posts');
    const [action, setAction] = useState('view');
    const [scope, setScope] = useState('');
    const [description, setDescription] = useState('');
    const [groupIds, setGroupIds] = useState<string[]>([]);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [submitting, setSubmitting] = useState(false);

    const isEdit = !!editPermission;

    const resetForm = () => {
        setModule('posts'); setAction('view'); setScope(''); setDescription('');
        setGroupIds([]); setErrors({}); setSubmitting(false); setEditPermission(null);
    };

    const openCreate = () => { resetForm(); setModalOpen(true); };

    const openEdit = (perm: Permission) => {
        setEditPermission(perm);
        setModule(perm.module || 'posts');
        setAction(perm.action || 'view');
        setScope(perm.scope || '');
        setDescription(perm.description || '');
        setGroupIds(perm.group_ids || []);
        setErrors({}); setSubmitting(false);
        setModalOpen(true);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitting(true);
        const data = { module, action, scope: scope || null, description: description || null, group_ids: groupIds.length > 0 ? groupIds : undefined };

        if (isEdit) {
            router.put(`/permissions/${editPermission!.id}`, { description: description || null }, {
                onError: (errs) => { setErrors(errs); setSubmitting(false); },
                onSuccess: () => { setModalOpen(false); resetForm(); },
            });
        } else {
            router.post('/permissions', data, {
                onError: (errs) => { setErrors(errs); setSubmitting(false); },
                onSuccess: () => { setModalOpen(false); resetForm(); },
            });
        }
    };

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/permissions/${deleteTarget.id}`, { preserveScroll: true });
        setDeleteTarget(null);
    };

    const handleSeed = () => {
        router.post('/permissions/seed', {}, { onSuccess: () => setSeedConfirm(false) });
    };

    const toggleCollapse = (module: string) => {
        setCollapsed((prev) => {
            const next = new Set(prev);
            next.has(module) ? next.delete(module) : next.add(module);
            return next;
        });
    };

    const getActionColor = (action: string | null | undefined): string => {
        return ACTION_COLORS[action || ''] || 'bg-surface-muted text-muted-foreground border-border-subtle';
    };

    const modules = Object.keys(grouped);

    return (
        <DashboardLayout>
            <Head title="Izin" />
            <div className="space-y-5">
                <div className="flex items-center justify-end md:justify-between gap-4 flex-wrap w-full">
                    <div className="hidden md:block">
                        <h1 className="text-xl font-semibold tracking-tight">Izin</h1>
                        <p className="hidden lg:block text-sm text-muted-foreground mt-0.5">{permissions.length} izin ditentukan</p>
                    </div>
                    <div className="flex items-center gap-2">
                        <button
                            onClick={() => setSeedConfirm(true)}
                            className="inline-flex items-center gap-2 h-9 px-4 bg-warning/100 hover:bg-warning/90 text-white rounded-md text-sm font-medium transition-all shadow-sm"
                        >
                            <Sparkles className="w-4 h-4" />
                            Seed Default CMS
                        </button>
                        <button
                            onClick={openCreate}
                            className="inline-flex items-center gap-2 h-9 px-4 bg-primary text-primary-foreground rounded-md text-sm font-medium hover:bg-primary/90 active:bg-primary/80 transition-all shadow-sm"
                        >
                            <Plus className="w-4 h-4" />
                            Izin Baru
                        </button>
                    </div>
                </div>

                {/* Permission groups */}
                <div className="space-y-3">
                    {modules.length === 0 ? (
                        <div className="flex flex-col items-center justify-center py-16 text-center bg-background border border-border-subtle rounded-xl">
                            <KeyRound className="w-12 h-12 text-muted-foreground/20 mb-4" />
                            <p className="text-sm font-medium">Belum ada izin</p>
                            <p className="text-xs text-muted-foreground mt-1">Seed default CMS atau buat izin secara manual.</p>
                            <button onClick={() => setSeedConfirm(true)} className="mt-4 inline-flex items-center gap-1.5 h-8 px-3 text-xs font-medium bg-warning/100 text-white rounded-md hover:bg-warning/90 transition-colors">
                                <Sparkles className="w-3.5 h-3.5" />
                                Seed Default
                            </button>
                        </div>
                    ) : (
                        modules.map((mod) => {
                            const perms = grouped[mod];
                            const isCollapsed = collapsed.has(mod);
                            return (
                                <div key={mod} className="bg-background border border-border-subtle rounded-xl overflow-hidden">
                                    {/* Module header */}
                                    <button
                                        type="button"
                                        onClick={() => toggleCollapse(mod)}
                                        className="flex items-center gap-3 w-full px-5 py-3.5 hover:bg-surface-muted/50 transition-colors text-left"
                                    >
                                        <div className="w-7 h-7 rounded-md bg-primary/10 flex items-center justify-center shrink-0">
                                            <KeyRound className="w-3.5 h-3.5 text-primary" />
                                        </div>
                                        <span className="flex-1 font-semibold text-sm">{MODULE_LABELS[mod] || mod}</span>
                                        <span className="text-xs text-muted-foreground mr-2">{perms.length} izin</span>
                                        {isCollapsed ? <ChevronRight className="w-4 h-4 text-muted-foreground" /> : <ChevronDown className="w-4 h-4 text-muted-foreground" />}
                                    </button>

                                    {!isCollapsed && (
                                        <div className="divide-y divide-border-subtle border-t border-border-subtle">
                                            {perms.map((perm) => (
                                                <div key={perm.id} className="flex items-center gap-3 px-5 py-3 hover:bg-surface-muted/30 transition-colors group">
                                                    <div className="flex items-center gap-2 flex-1 min-w-0">
                                                        <span className={`inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border ${getActionColor(perm.action)}`}>
                                                            {perm.action || perm.name}
                                                            {perm.scope && <span className="opacity-70">{SCOPE_LABELS[perm.scope] || '·' + perm.scope}</span>}
                                                        </span>
                                                        <span className="text-xs text-muted-foreground font-mono truncate hidden sm:block">{perm.name}</span>
                                                    </div>
                                                    <div className="flex items-center gap-2 shrink-0">
                                                        <span className="text-xs text-muted-foreground hidden md:block">
                                                            {perm.roles_count} peran
                                                        </span>
                                                        <div className="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                            <button
                                                                onClick={() => openEdit(perm)}
                                                                className="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors"
                                                                title="Edit deskripsi"
                                                            >
                                                                <Pencil className="w-3 h-3" />
                                                            </button>
                                                            <button
                                                                onClick={() => setDeleteTarget({ id: perm.id, name: perm.name })}
                                                                className="p-1.5 rounded-md text-muted-foreground hover:text-destructive hover:bg-destructive/10 transition-colors"
                                                                title="Hapus"
                                                            >
                                                                <Trash2 className="w-3 h-3" />
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            );
                        })
                    )}
                </div>
            </div>

            {/* Create/Edit Dialog */}
            <Dialog open={modalOpen} onOpenChange={(open) => { if (!open) { setModalOpen(false); resetForm(); } }}>
                <DialogContent className="max-w-md max-h-[90vh] flex flex-col p-0 gap-0">
                    <DialogHeader className="flex flex-row items-center justify-between px-6 py-4 border-b border-border-subtle shrink-0">
                        <DialogTitle className="text-base">{isEdit ? 'Edit Izin' : 'Buat Izin'}</DialogTitle>
                        <DialogClose className="p-1 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors">
                            <X className="w-4 h-4" />
                        </DialogClose>
                    </DialogHeader>
                    <form onSubmit={handleSubmit} className="flex flex-col flex-1 min-h-0">
                        <div className="space-y-4 px-6 py-4 overflow-y-auto flex-1">
                            <>
                                <div className={`grid grid-cols-2 gap-3 ${isEdit ? 'opacity-50 pointer-events-none' : ''}`}>
                                    <div className="space-y-1.5">
                                        <label className="text-sm font-medium">Modul</label>
                                        {isEdit ? (
                                            <div className="flex h-9 items-center px-3 rounded-sm border border-border-subtle bg-surface-muted/50 text-sm font-mono">
                                                {editPermission?.module || '-'}
                                            </div>
                                        ) : (
                                            <input
                                                value={module}
                                                onChange={(e) => setModule(e.target.value)}
                                                placeholder="contoh: posts, users, academic-years"
                                                className="flex w-full h-9 rounded-sm border border-border-subtle bg-background px-3 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                                            />
                                        )}
                                    </div>
                                    <div className="space-y-1.5">
                                        <label className="text-sm font-medium">Aksi</label>
                                        {isEdit ? (
                                            <div className="flex h-9 items-center px-3 rounded-sm border border-border-subtle bg-surface-muted/50 text-sm font-mono">
                                                {editPermission?.action || '-'}
                                            </div>
                                        ) : (
                                            <input
                                                value={action}
                                                onChange={(e) => setAction(e.target.value)}
                                                placeholder="contoh: view, create, edit, delete"
                                                className="flex w-full h-9 rounded-sm border border-border-subtle bg-background px-3 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                                            />
                                        )}
                                    </div>
                                </div>
                                {!isEdit && (
                                    <div className="space-y-1.5">
                                        <label className="text-sm font-medium">Cakupan <span className="text-muted-foreground font-normal">(opsional)</span></label>
                                        <select
                                            value={scope}
                                            onChange={(e) => setScope(e.target.value)}
                                            className="flex w-full h-9 rounded-sm border border-border-subtle bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                                        >
                                            <option value="">Tanpa cakupan (global)</option>
                                            <option value="own">own – sumber daya milik sendiri</option>
                                            <option value="all">all – semua sumber daya</option>
                                            <option value="assigned">assigned – sumber daya yang ditugaskan</option>
                                        </select>
                                    </div>
                                )}
                                {isEdit && (
                                    <div className="p-3 bg-surface-muted/50 rounded-lg">
                                        <p className="text-xs text-muted-foreground mb-1">Izin</p>
                                        <p className="text-sm font-mono font-medium">{editPermission?.name}</p>
                                    </div>
                                )}
                                {!isEdit && (
                                    <div className="p-3 bg-surface-muted/50 rounded-lg">
                                        <p className="text-xs text-muted-foreground mb-1">Nama izin yang dihasilkan</p>
                                        <p className="text-sm font-mono font-medium text-primary">
                                            {module || 'modul'}.{action || 'aksi'}{scope ? '.' + scope : ''}
                                        </p>
                                    </div>
                                )}
                                {permissionGroups && permissionGroups.length > 0 && (
                                    <div className="space-y-1.5">
                                        <label className="text-sm font-medium">Grup Izin <span className="text-muted-foreground font-normal">(opsional)</span></label>
                                        <div className="flex flex-wrap gap-2 pt-1">
                                            {permissionGroups.map((g) => {
                                                const active = groupIds.includes(g.id);
                                                return (
                                                    <button
                                                        key={g.id}
                                                        type="button"
                                                        onClick={() => setGroupIds((prev) => prev.includes(g.id) ? prev.filter((id) => id !== g.id) : [...prev, g.id])}
                                                        className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium border transition-all ${active ? 'bg-primary text-primary-foreground border-primary shadow-sm' : 'bg-background text-muted-foreground border-border-subtle hover:border-primary/50 hover:text-foreground'}`}
                                                    >
                                                        {g.name}
                                                    </button>
                                                );
                                            })}
                                        </div>
                                    </div>
                                )}
                                <div className="space-y-1.5">
                                    <label className="text-sm font-medium">Deskripsi <span className="text-muted-foreground font-normal">(opsional)</span></label>
                                    <textarea
                                        value={description}
                                        onChange={(e) => setDescription(e.target.value)}
                                        rows={2}
                                        placeholder="Jelaskan apa yang diizinkan oleh izin ini..."
                                        className="flex w-full rounded-sm border border-border-subtle bg-background px-3 py-1.5 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                    />
                                </div>
                            </>
                        </div>
                        <DialogFooter className="flex items-center justify-end gap-2 px-6 py-4 border-t border-border-subtle shrink-0">
                            <Button type="button" variant="outline" onClick={() => { setModalOpen(false); resetForm(); }}>Batal</Button>
                            <Btn
                                type="submit"
                                loading={submitting}
                                disabled={submitting}
                                icon={<Save className="w-4 h-4" />}
                            >
                                {isEdit ? 'Perbarui' : 'Buat Izin'}
                            </Btn>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <ConfirmDialog
                open={seedConfirm}
                onOpenChange={setSeedConfirm}
                title="Seed Default Izin CMS"
                message="Ini akan membuat semua izin CMS standar (postingan, halaman, media, pengguna, dll.) jika belum ada. Izin yang sudah ada tidak akan terpengaruh."
                confirmLabel="Seed Izin"
                variant="primary"
                onConfirm={handleSeed}
            />

            <ConfirmDialog
                open={!!deleteTarget}
                onOpenChange={(open) => { if (!open) setDeleteTarget(null); }}
                title="Hapus Izin"
                message={deleteTarget ? `Apakah Anda yakin ingin menghapus "${deleteTarget.name}"? Peran dengan izin ini mungkin kehilangan akses.` : ''}
                confirmLabel="Hapus"
                variant="danger"
                onConfirm={handleDelete}
            />
        </DashboardLayout>
    );
}
