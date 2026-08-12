import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Button } from '@dashboard/Components/ui/button';
import { Input } from '@dashboard/Components/ui/input';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogClose, DialogFooter } from '@dashboard/Components/ui/dialog';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import PermissionMatrix from '@dashboard/Components/ui/permission-matrix';
import {
    Plus, ShieldCheck, Pencil, Trash2, Users, Lock, X,
    Copy, MoreVertical, Crown, Shield, Star, Save
} from 'lucide-react';
import { Btn } from '@dashboard/Components/ui/btn';

interface PermissionItem {
    id: string;
    name: string;
    module?: string;
    action?: string;
    scope?: string;
    description?: string;
}

interface Role {
    id: string;
    name: string;
    slug?: string;
    guard_name: string;
    display_name?: string | null;
    description?: string | null;
    color?: string | null;
    icon?: string | null;
    is_system: boolean;
    status: string;
    scope?: string | null;
    sort_order: number;
    permissions_count: number;
    permission_names?: string[];
    users_count: number;
    rank: number;
    created_at: string;
    can: {
        update: boolean;
        delete: boolean;
    };
}

interface GroupedPermissions {
    [group: string]: PermissionItem[];
}

const ROLE_COLORS = [
    '#7c3aed', '#2563eb', '#dc2626', '#059669', '#d97706',
    '#6b7280', '#ec4899', '#0891b2', '#65a30d', '#9333ea',
];

const ROLE_ICONS = [
    { name: 'shield', label: 'Shield' },
    { name: 'crown', label: 'Crown' },
    { name: 'star', label: 'Star' },
    { name: 'user', label: 'User' },
    { name: 'pen-tool', label: 'Editor' },
    { name: 'feather', label: 'Author' },
    { name: 'edit-3', label: 'Writer' },
    { name: 'eye', label: 'Viewer' },
];

function RoleIcon({ icon, color, size = 5 }: { icon?: string | null; color?: string | null; size?: number }) {
    const style = color ? { color } : {};
    const cls = `w-${size} h-${size}`;
    switch (icon) {
        case 'crown': return <Crown className={cls} style={style} />;
        case 'star': return <Star className={cls} style={style} />;
        case 'shield': return <Shield className={cls} style={style} />;
        default: return <ShieldCheck className={cls} style={style} />;
    }
}

interface PermissionGroupItem {
    id: string;
    name: string;
    slug: string;
    icon?: string;
    color?: string;
    permissions: PermissionItem[];
}

export default function RoleIndex({ roles, groupedPermissions, permissionGroups }: { roles: Role[]; groupedPermissions: GroupedPermissions; permissionGroups?: PermissionGroupItem[] }) {
    const [modalOpen, setModalOpen] = useState(false);
    const [editRole, setEditRole] = useState<Role | null>(null);
    const [deleteTarget, setDeleteTarget] = useState<{ id: string; name: string } | null>(null);
    const [openMenuId, setOpenMenuId] = useState<string | null>(null);

    // Form state
    const [name, setName] = useState('');
    const [displayName, setDisplayName] = useState('');
    const [description, setDescription] = useState('');
    const [color, setColor] = useState('#7c3aed');
    const [icon, setIcon] = useState('shield');
    const [status, setStatus] = useState('active');
    const [scope, setScope] = useState('');
    const [rank, setRank] = useState(10);
    const [selectedPermissions, setSelectedPermissions] = useState<string[]>([]);
    const [initialPermissions, setInitialPermissions] = useState<string[]>([]);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [submitting, setSubmitting] = useState(false);

    const isEdit = !!editRole;

    const isDirty = isEdit 
        ? (
            name !== (editRole?.name ?? '') ||
            displayName !== (editRole?.display_name ?? '') ||
            description !== (editRole?.description ?? '') ||
            color !== (editRole?.color ?? '#7c3aed') ||
            icon !== (editRole?.icon ?? 'shield') ||
            status !== (editRole?.status ?? 'active') ||
            scope !== (editRole?.scope ?? '') ||
            rank !== (editRole?.rank ?? 10) ||
            JSON.stringify([...selectedPermissions].sort()) !== JSON.stringify([...initialPermissions].sort())
        )
        : (!!name || !!displayName || !!description || !!scope || selectedPermissions.length > 0);

    const resetForm = () => {
        setName(''); setDisplayName(''); setDescription('');
        setColor('#7c3aed'); setIcon('shield'); setStatus('active'); setScope(''); setRank(10);
        setSelectedPermissions([]); setInitialPermissions([]); setErrors({}); setSubmitting(false); setEditRole(null);
    };

    const openCreate = () => { resetForm(); setModalOpen(true); };

    const openEdit = (role: Role) => {
        setEditRole(role);
        setName(role.name);
        setDisplayName(role.display_name ?? '');
        setDescription(role.description ?? '');
        setColor(role.color || '#7c3aed');
        setIcon(role.icon || 'shield');
        setStatus(role.status);
        setScope(role.scope || '');
        setRank(role.rank);
        
        setSelectedPermissions(role.permission_names || []);
        setInitialPermissions(role.permission_names || []);
        setSubmitting(false);
        setModalOpen(true);
        setOpenMenuId(null);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitting(true);
        const data = {
            name, display_name: displayName, description,
            color, icon, status, scope: scope || null, rank, permissions: selectedPermissions
        }; 
        if (isEdit) {
            router.put(`/roles/${editRole!.id}`, data, {
                onError: (errs) => { setErrors(errs); setSubmitting(false); },
                onSuccess: () => { setModalOpen(false); resetForm(); },
                onFinish: () => setSubmitting(false),
            });
        } else {
            router.post('/roles', data, {
                onError: (errs) => { setErrors(errs); setSubmitting(false); },
                onSuccess: () => { setModalOpen(false); resetForm(); },
                onFinish: () => setSubmitting(false),
            });
        }
    };

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/roles/${deleteTarget.id}`, { preserveScroll: true });
        setDeleteTarget(null);
    };

    const handleDuplicate = (id: string) => {
        router.post(`/roles/${id}/duplicate`, {}, { preserveScroll: true });
        setOpenMenuId(null);
    };

    return (
        <DashboardLayout>
            <Head title="Peran" />
            <div className="space-y-5">
                <div className="flex items-center justify-end md:justify-between w-full">
                    <div className="hidden md:block">
                        <h1 className="text-xl font-semibold tracking-tight">Peran</h1>
                        <p className="hidden lg:block text-sm text-muted-foreground mt-0.5">{roles.length} peran dikonfigurasi</p>
                    </div>
                    <button
                        onClick={openCreate}
                        className="inline-flex items-center gap-2 h-9 px-4 bg-primary text-primary-foreground rounded-md text-sm font-medium hover:bg-primary/90 active:bg-primary/80 transition-all shadow-sm"
                    >
                        <Plus className="w-4 h-4" />
                        Peran Baru
                    </button>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {roles.map((role) => (
                        <div 
                            key={role.id} 
                            className="bg-background border border-border-subtle rounded-xl p-5 hover:shadow-elevated transition-shadow group relative"
                            style={{ borderTopWidth: '4px', borderTopColor: role.color || '#7c3aed' }}
                        >
                            <div className="flex items-start justify-between mt-1">
                                <div className="flex items-center gap-3">
                                    <div
                                        className="w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
                                        style={{ backgroundColor: (role.color || '#7c3aed') + '20' }}
                                    >
                                        <RoleIcon icon={role.icon} color={role.color} />
                                    </div>
                                    <div>
                                        <div className="flex items-center gap-1.5">
                                            <p className="font-semibold text-sm">{role.display_name || role.name}</p>
                                            {role.is_system && (
                                                <span className="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-medium bg-warning/10 text-warning border border-warning/20 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800">
                                                    <Lock className="w-2.5 h-2.5" />
                                                    Sistem
                                                </span>
                                            )}
                                            {role.scope && (
                                                <span className={`inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium border ${
                                                    role.scope === 'yayasan' ? 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800' : 'bg-cyan-50 text-cyan-700 border-cyan-200 dark:bg-cyan-950/40 dark:text-cyan-300 dark:border-cyan-800'
                                                }`}>
                                                    {role.scope === 'yayasan' ? 'Yayasan' : 'Lembaga'}
                                                </span>
                                            )}
                                        </div>
                                        {role.display_name && (
                                            <p className="text-xs text-muted-foreground">{role.name}</p>
                                        )}
                                    </div>
                                </div>

                                <div className="relative">
                                    <button
                                        onClick={() => setOpenMenuId(openMenuId === role.id ? null : role.id)}
                                        className={`p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-all ${openMenuId === role.id ? 'opacity-100 bg-surface-muted' : 'opacity-100 md:opacity-0 group-hover:opacity-100'}`}
                                    >
                                        <MoreVertical className="w-4 h-4" />
                                    </button>
                                    {openMenuId === role.id && (
                                        <>
                                            <div className="fixed inset-0 z-10" onClick={() => setOpenMenuId(null)} />
                                            <div className="absolute right-0 top-8 z-20 w-44 bg-background border border-border-subtle rounded-xl shadow-lg py-1 overflow-hidden">
                                                <div className="py-1">
                                                    {role.can.update && (
                                                        <button
                                                            onClick={() => { openEdit(role); setOpenMenuId(null); }}
                                                            className="w-full text-left px-4 py-2 text-sm text-foreground hover:bg-surface-muted flex items-center gap-2"
                                                        >
                                                            <Pencil className="w-4 h-4" /> Edit Peran
                                                        </button>
                                                    )}
                                                    <button
                                                        onClick={() => { handleDuplicate(role.id); setOpenMenuId(null); }}
                                                        className="w-full text-left px-4 py-2 text-sm text-foreground hover:bg-surface-muted flex items-center gap-2"
                                                    >
                                                        <Copy className="w-4 h-4" /> Duplikat
                                                    </button>
                                                    {role.can.delete && !role.is_system && (
                                                        <button
                                                            onClick={() => { setDeleteTarget({ id: role.id, name: role.name }); setOpenMenuId(null); }}
                                                            className="w-full text-left px-4 py-2 text-sm text-destructive hover:bg-destructive/10 flex items-center gap-2"
                                                        >
                                                            <Trash2 className="w-4 h-4" /> Hapus Peran
                                                        </button>
                                                    )}
                                                </div>
                                            </div>
                                        </>
                                    )}
                                </div>
                            </div>

                            {role.description && (
                                <p className="text-xs text-muted-foreground mt-3 line-clamp-2">{role.description}</p>
                            )}

                            <div className="flex items-center gap-4 text-xs text-muted-foreground mt-4 pt-3 border-t border-border-subtle">
                                <span className="inline-flex items-center gap-1">
                                    <Lock className="w-3 h-3" />
                                    {role.permissions_count} izin
                                </span>
                                <span className="inline-flex items-center gap-1">
                                    <Users className="w-3 h-3" />
                                    {role.users_count} pengguna
                                </span>
                                <span className="ml-auto font-medium">Rank: {role.rank}</span>
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            <Dialog open={modalOpen} onOpenChange={(open) => { if (!open) { setModalOpen(false); resetForm(); } }}>
                <DialogContent className="max-w-2xl max-h-[90vh] flex flex-col p-0 gap-0">
                    <DialogHeader className="flex flex-row items-center justify-between px-6 py-4 border-b border-border-subtle mb-0 shrink-0">
                        <DialogTitle className="text-base">{isEdit ? 'Edit Peran' : 'Buat Peran'}</DialogTitle>
                        <DialogClose className="p-1 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors">
                            <X className="w-4 h-4" />
                        </DialogClose>
                    </DialogHeader>

                    <form onSubmit={handleSubmit} className="flex flex-col flex-1 min-h-0">
                        <div className="space-y-5 px-6 py-4 overflow-y-auto flex-1">
                            <div className="grid grid-cols-2 gap-4">
                                <Input label="Nama (slug)" value={name} onChange={(e) => setName(e.target.value)} error={errors.name} required placeholder="cth. editor" disabled={isEdit && editRole?.is_system} />
                                <Input label="Nama Tampilan" value={displayName} onChange={(e) => setDisplayName(e.target.value)} error={errors.display_name} placeholder="cth. Editor" />
                            </div>
                            <div className="space-y-1.5">
                                <label className="text-sm font-medium">Deskripsi</label>
                                <textarea
                                    value={description}
                                    onChange={(e) => setDescription(e.target.value)}
                                    placeholder="Deskripsi opsional peran ini..."
                                    rows={2}
                                    className="flex w-full rounded-sm border border-border-subtle bg-background px-3 py-1.5 text-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                />
                            </div>

                            <div className="space-y-1.5">
                                <label className="text-sm font-medium">Cakupan <span className="text-muted-foreground font-normal">(opsional)</span></label>
                                <select
                                    value={scope}
                                    onChange={(e) => setScope(e.target.value)}
                                    className="flex w-full h-9 rounded-sm border border-border-subtle bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                                >
                                    <option value="">Global (tanpa cakupan)</option>
                                    <option value="yayasan">Yayasan</option>
                                    <option value="lembaga">Lembaga</option>
                                </select>
                                {errors.scope && <p className="text-xs text-destructive">{errors.scope}</p>}
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="space-y-1.5">
                                    <label className="text-sm font-medium">Warna Peran</label>
                                    <div className="flex flex-wrap gap-2">
                                        {ROLE_COLORS.map((c) => (
                                            <button
                                                key={c}
                                                type="button"
                                                onClick={() => setColor(c)}
                                                className={`w-7 h-7 rounded-full transition-all ${color === c ? 'ring-2 ring-offset-2 ring-primary scale-110' : 'hover:scale-105'}`}
                                                style={{ backgroundColor: c }}
                                            />
                                        ))}
                                        <input type="color" value={color} onChange={(e) => setColor(e.target.value)} className="w-7 h-7 rounded-full cursor-pointer border border-border-subtle" title="Warna kustom" />
                                    </div>
                                </div>
                                <div className="grid grid-cols-2 gap-4">
                                    <div className="space-y-1.5">
                                        <label className="text-xs font-medium text-foreground">Status</label>
                                        <select
                                            value={status}
                                            onChange={(e) => setStatus(e.target.value)}
                                            className="w-full h-9 rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                        >
                                            <option value="active">Aktif</option>
                                            <option value="inactive">Nonaktif</option>
                                        </select>
                                    </div>
                                    <div className="space-y-1.5">
                                        <label className="text-xs font-medium text-foreground">Rank <span className="text-muted-foreground font-normal">(0-100)</span></label>
                                        <Input
                                            type="number"
                                            min="0"
                                            max="100"
                                            value={rank}
                                            onChange={(e) => setRank(parseInt(e.target.value) || 0)}
                                            error={errors.rank}
                                        />
                                    </div>
                                </div>
                            </div>

                            <div className="space-y-1.5">
                                <label className="text-sm font-medium">Izin</label>
                                {errors.permissions && <p className="text-xs text-destructive">{errors.permissions}</p>}
                                <PermissionMatrix
                                    groupedPermissions={groupedPermissions}
                                    permissionGroups={permissionGroups}
                                    selected={selectedPermissions}
                                    onChange={setSelectedPermissions}
                                />
                            </div>
                        </div>

                        <DialogFooter className="flex items-center justify-end gap-2 px-6 py-4 border-t border-border-subtle mt-0 shrink-0">
                            <Button type="button" variant="outline" onClick={() => { setModalOpen(false); resetForm(); }}>Batal</Button>
                            <Btn
                                type="submit"
                                loading={submitting}
                                disabled={!isDirty || submitting}
                                icon={<Save className="w-4 h-4" />}
                            >
                                {isEdit ? 'Perbarui Peran' : 'Buat Peran'}
                            </Btn>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <ConfirmDialog
                open={!!deleteTarget}
                onOpenChange={(open) => { if (!open) setDeleteTarget(null); }}
                title="Hapus Peran"
                message={deleteTarget ? `Apakah Anda yakin ingin menghapus "${deleteTarget.name}"? Pengguna dengan peran ini mungkin kehilangan izin.` : ''}
                confirmLabel="Hapus"
                variant="danger"
                onConfirm={handleDelete}
            />
        </DashboardLayout>
    );
}
