import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Button } from '@dashboard/Components/ui/button';
import { Input } from '@dashboard/Components/ui/input';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogClose, DialogFooter } from '@dashboard/Components/ui/dialog';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import PermissionMatrix from '@dashboard/Components/ui/permission-matrix';
import { Plus, Layers, Pencil, Trash2, X, KeyRound, MoreVertical, Save } from 'lucide-react';
import { Btn } from '@dashboard/Components/ui/btn';

interface PermissionItem {
    id: string;
    name: string;
    module?: string;
    action?: string;
    scope?: string;
}

interface PermissionGroup {
    id: string;
    name: string;
    slug?: string;
    description?: string | null;
    icon?: string | null;
    color?: string | null;
    sort_order: number;
    permissions_count: number;
    created_at: string;
}

interface GroupedPermissions {
    [module: string]: PermissionItem[];
}

const GROUP_COLORS = [
    '#2563eb', '#7c3aed', '#059669', '#d97706', '#6b7280',
    '#ec4899', '#0891b2', '#dc2626', '#65a30d', '#9333ea',
];

const GROUP_ICONS = [
    'file-text', 'image', 'message-square', 'users', 'settings',
    'shield', 'layers', 'globe', 'database', 'bell',
];

export default function PermissionGroupsIndex({
    groups,
    permissions,
    groupedPermissions,
}: {
    groups: PermissionGroup[];
    permissions: PermissionItem[];
    groupedPermissions: GroupedPermissions;
}) {
    const [modalOpen, setModalOpen] = useState(false);
    const [editGroup, setEditGroup] = useState<PermissionGroup | null>(null);
    const [deleteTarget, setDeleteTarget] = useState<{ id: string; name: string } | null>(null);
    const [openMenuId, setOpenMenuId] = useState<string | null>(null);

    // Form state
    const [name, setName] = useState('');
    const [description, setDescription] = useState('');
    const [color, setColor] = useState('#2563eb');
    const [sortOrder, setSortOrder] = useState(0);
    const [selectedPermissions, setSelectedPermissions] = useState<string[]>([]);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [submitting, setSubmitting] = useState(false);

    const isEdit = !!editGroup;

    const resetForm = () => {
        setName(''); setDescription(''); setColor('#2563eb'); setSortOrder(0);
        setSelectedPermissions([]); setErrors({}); setSubmitting(false); setEditGroup(null);
    };

    const openCreate = () => { resetForm(); setModalOpen(true); };

    const openEdit = (group: PermissionGroup) => {
        setEditGroup(group);
        setName(group.name);
        setDescription(group.description || '');
        setColor(group.color || '#2563eb');
        setSortOrder(group.sort_order);
        setSelectedPermissions([]);
        setErrors({}); setSubmitting(false);
        setModalOpen(true);
        setOpenMenuId(null);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitting(true);
        const permIds = permissions
            .filter((p) => selectedPermissions.includes(p.name))
            .map((p) => p.id);
        const data = { name, description: description || null, color, sort_order: sortOrder, permission_ids: permIds };

        if (isEdit) {
            router.put(`/dashboard/permission-groups/${editGroup!.id}`, data, {
                onError: (errs) => { setErrors(errs); setSubmitting(false); },
                onSuccess: () => { setModalOpen(false); resetForm(); },
            });
        } else {
            router.post('/dashboard/permission-groups', data, {
                onError: (errs) => { setErrors(errs); setSubmitting(false); },
                onSuccess: () => { setModalOpen(false); resetForm(); },
            });
        }
    };

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/dashboard/permission-groups/${deleteTarget.id}`, { preserveScroll: true });
        setDeleteTarget(null);
    };

    return (
        <DashboardLayout>
            <Head title="Permission Groups" />
            <div className="space-y-5">
                <div className="flex items-center justify-end md:justify-between w-full">
                    <div className="hidden md:block">
                        <h1 className="text-xl font-semibold tracking-tight">Permission Groups</h1>
                        <p className="hidden lg:block text-sm text-muted-foreground mt-0.5">Organize permissions into logical groups</p>
                    </div>
                    <button
                        onClick={openCreate}
                        className="inline-flex items-center gap-2 h-9 px-4 bg-primary text-primary-foreground rounded-md text-sm font-medium hover:bg-primary/90 active:bg-primary/80 transition-all shadow-sm"
                    >
                        <Plus className="w-4 h-4" />
                        New Group
                    </button>
                </div>

                {/* Group cards */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {groups.map((group) => (
                        <div 
                            key={group.id} 
                            className="bg-background border border-border-subtle rounded-xl p-5 hover:shadow-elevated transition-shadow group relative"
                            style={{ borderTopWidth: '4px', borderTopColor: group.color || '#2563eb' }}
                        >
                            <div className="flex items-start justify-between mt-1">
                                <div className="flex items-center gap-3">
                                    <div
                                        className="w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
                                        style={{ backgroundColor: (group.color || '#2563eb') + '20' }}
                                    >
                                        <Layers className="w-5 h-5" style={{ color: group.color || '#2563eb' }} />
                                    </div>
                                    <div>
                                        <p className="font-semibold text-sm">{group.name}</p>
                                        {group.slug && <p className="text-xs text-muted-foreground">{group.slug}</p>}
                                    </div>
                                </div>

                                {/* Actions */}
                                <div className="relative">
                                    <button
                                        onClick={() => setOpenMenuId(openMenuId === group.id ? null : group.id)}
                                        className={`p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-all ${openMenuId === group.id ? 'opacity-100 bg-surface-muted' : 'opacity-100 md:opacity-0 group-hover:opacity-100'}`}
                                    >
                                        <MoreVertical className="w-4 h-4" />
                                    </button>
                                    {openMenuId === group.id && (
                                        <>
                                            <div className="fixed inset-0 z-10" onClick={() => setOpenMenuId(null)} />
                                            <div className="absolute right-0 top-8 z-20 w-40 bg-background border border-border-subtle rounded-xl shadow-lg py-1 overflow-hidden">
                                                <button onClick={() => openEdit(group)} className="flex items-center gap-2 w-full px-4 py-2 text-sm text-foreground hover:bg-surface-muted transition-colors">
                                                    <Pencil className="w-3.5 h-3.5" /> Edit Group
                                                </button>
                                                <button
                                                    onClick={() => { setDeleteTarget({ id: group.id, name: group.name }); setOpenMenuId(null); }}
                                                    className="flex items-center gap-2 w-full px-4 py-2 text-sm text-destructive hover:bg-destructive/10 transition-colors"
                                                >
                                                    <Trash2 className="w-3.5 h-3.5" /> Delete
                                                </button>
                                            </div>
                                        </>
                                    )}
                                </div>
                            </div>

                            {group.description && (
                                <p className="text-xs text-muted-foreground mt-3 line-clamp-2">{group.description}</p>
                            )}

                            <div className="flex items-center gap-2 text-xs text-muted-foreground mt-4 pt-3 border-t border-border-subtle">
                                <KeyRound className="w-3 h-3" />
                                <span>{group.permissions_count} permission{group.permissions_count !== 1 ? 's' : ''}</span>
                                <span className="ml-auto text-[10px] text-muted-foreground/60">Order: {group.sort_order}</span>
                            </div>
                        </div>
                    ))}

                    {groups.length === 0 && (
                        <div className="col-span-full flex flex-col items-center justify-center py-16 text-center bg-background border border-border-subtle rounded-xl">
                            <Layers className="w-12 h-12 text-muted-foreground/20 mb-4" />
                            <p className="text-sm font-medium">No permission groups yet</p>
                            <p className="text-xs text-muted-foreground mt-1">Group permissions by feature area for easier management.</p>
                            <button
                                onClick={openCreate}
                                className="mt-4 inline-flex items-center gap-2 h-8 px-3 bg-primary text-primary-foreground rounded-md text-xs font-medium hover:bg-primary/90 transition-all shadow-sm"
                            >
                                <Plus className="w-3.5 h-3.5" />
                                Create Group
                            </button>
                        </div>
                    )}
                </div>
            </div>

            {/* Create / Edit Dialog */}
            <Dialog open={modalOpen} onOpenChange={(open) => { if (!open) { setModalOpen(false); resetForm(); } }}>
                <DialogContent className="max-w-2xl max-h-[90vh] flex flex-col p-0 gap-0">
                    <DialogHeader className="flex flex-row items-center justify-between px-6 py-4 border-b border-border-subtle mb-0 shrink-0">
                        <DialogTitle className="text-base">{isEdit ? 'Edit Group' : 'Create Permission Group'}</DialogTitle>
                        <DialogClose className="p-1 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors">
                            <X className="w-4 h-4" />
                        </DialogClose>
                    </DialogHeader>
                    <form onSubmit={handleSubmit} className="flex flex-col flex-1 min-h-0">
                        <div className="space-y-4 px-6 py-4 overflow-y-auto flex-1">
                            <div className="grid grid-cols-2 gap-4">
                                <Input label="Group Name" value={name} onChange={(e) => setName(e.target.value)} error={errors.name} required placeholder="e.g. Content Management" />
                                <div className="space-y-1.5">
                                    <label className="text-sm font-medium">Sort Order</label>
                                    <input
                                        type="number"
                                        value={sortOrder}
                                        onChange={(e) => setSortOrder(parseInt(e.target.value) || 0)}
                                        className="flex w-full h-9 rounded-sm border border-border-subtle bg-background px-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                                        min={0}
                                    />
                                </div>
                            </div>
                            <div className="space-y-1.5">
                                <label className="text-sm font-medium">Description</label>
                                <textarea
                                    value={description}
                                    onChange={(e) => setDescription(e.target.value)}
                                    rows={2}
                                    placeholder="Optional description..."
                                    className="flex w-full rounded-sm border border-border-subtle bg-background px-3 py-1.5 text-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                />
                            </div>
                            <div className="space-y-1.5">
                                <label className="text-sm font-medium">Group Color</label>
                                <div className="flex flex-wrap gap-2">
                                    {GROUP_COLORS.map((c) => (
                                        <button
                                            key={c}
                                            type="button"
                                            onClick={() => setColor(c)}
                                            className={`w-7 h-7 rounded-full transition-all ${color === c ? 'ring-2 ring-offset-2 ring-primary scale-110' : 'hover:scale-105'}`}
                                            style={{ backgroundColor: c }}
                                        />
                                    ))}
                                    <input type="color" value={color} onChange={(e) => setColor(e.target.value)} className="w-7 h-7 rounded-full cursor-pointer border border-border-subtle" title="Custom color" />
                                </div>
                            </div>

                            {/* Permission matrix */}
                            <div className="space-y-1.5">
                                <label className="text-sm font-medium">Permissions in this Group</label>
                                <PermissionMatrix
                                    groupedPermissions={groupedPermissions}
                                    selected={selectedPermissions}
                                    onChange={setSelectedPermissions}
                                />
                            </div>
                        </div>
                        <DialogFooter className="flex items-center justify-end gap-2 px-6 py-4 border-t border-border-subtle shrink-0">
                            <Button type="button" variant="outline" onClick={() => { setModalOpen(false); resetForm(); }}>Cancel</Button>
                            <Btn
                                type="submit"
                                loading={submitting}
                                disabled={submitting}
                                icon={<Save className="w-4 h-4" />}
                            >
                                {isEdit ? 'Update Group' : 'Create Group'}
                            </Btn>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <ConfirmDialog
                open={!!deleteTarget}
                onOpenChange={(open) => { if (!open) setDeleteTarget(null); }}
                title="Delete Permission Group"
                message={deleteTarget ? `Are you sure you want to delete "${deleteTarget.name}"? Permissions in this group won't be deleted.` : ''}
                confirmLabel="Delete Group"
                variant="danger"
                onConfirm={handleDelete}
            />
        </DashboardLayout>
    );
}
