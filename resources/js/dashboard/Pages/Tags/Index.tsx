import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Input } from '@dashboard/Components/ui/input';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogClose, DialogFooter } from '@dashboard/Components/ui/dialog';
import { Switch } from '@dashboard/Components/ui/switch';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import {
    Plus,
    Pencil,
    Trash2,
    Tags as TagsIcon,
    Layers,
    X,
    Save,
} from 'lucide-react';
import { usePermissions } from '@dashboard/hooks/usePermissions';
import { Btn } from '@dashboard/Components/ui/btn';

interface Tag {
    id: string;
    name: string;
    slug: string;
    description: string | null;
    color: string | null;
    is_visible: boolean;
    posts_count: number;
    created_by: string | null;
    updated_by: string | null;
    deleted_at: string | null;
    created_at: string;
    updated_at: string;
}

export default function TagIndex({ tags }: { tags: Tag[] }) {
    const { can } = usePermissions();
    const [modalOpen, setModalOpen] = useState(false);
    const [editTag, setEditTag] = useState<Tag | null>(null);
    const [deleteTarget, setDeleteTarget] = useState<{ id: string; name: string } | null>(null);

    const formatDate = (dateString?: string | null) => {
        if (!dateString) return null;
        const d = new Date(dateString);
        return `${d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })} • ${d.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' })}`;
    };

    const [name, setName] = useState('');
    const [description, setDescription] = useState('');
    const [color, setColor] = useState('#64748b');
    const [isVisible, setIsVisible] = useState(true);
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [submitting, setSubmitting] = useState(false);

    const isEdit = !!editTag;

    const isDirty = isEdit 
        ? name !== (editTag?.name ?? '') || 
          description !== (editTag?.description ?? '') ||
          color !== (editTag?.color ?? '#64748b') ||
          isVisible !== (editTag?.is_visible ?? true)
        : !!name;

    const resetForm = () => {
        setName('');
        setDescription('');
        setColor('#64748b');
        setIsVisible(true);
        setErrors({});
        setSubmitting(false);
        setEditTag(null);
    };

    const openCreate = () => {
        resetForm();
        setModalOpen(true);
    };

    const openEdit = (tag: Tag) => {
        setEditTag(tag);
        setName(tag.name);
        setDescription(tag.description ?? '');
        setColor(tag.color ?? '#64748b');
        setIsVisible(tag.is_visible);
        setErrors({});
        setSubmitting(false);
        setModalOpen(true);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitting(true);
        const data = { 
            name, 
            description: description || null,
            color: color || null,
            is_visible: isVisible 
        };

        if (isEdit) {
            router.put(`/dashboard/tags/${editTag!.id}`, data, {
                onError: (errs) => { setErrors(errs); setSubmitting(false); },
                onSuccess: () => { setModalOpen(false); resetForm(); },
                onFinish: () => setSubmitting(false),
            });
        } else {
            router.post('/dashboard/tags', data, {
                onError: (errs) => { setErrors(errs); setSubmitting(false); },
                onSuccess: () => { setModalOpen(false); resetForm(); },
                onFinish: () => setSubmitting(false),
            });
        }
    };

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/dashboard/tags/${deleteTarget.id}`, { preserveScroll: true });
        setDeleteTarget(null);
    };

    return (
        <DashboardLayout>
            <Head title="Tags" />
            <div className="space-y-5">
                <div className="flex items-center justify-end md:justify-between w-full">
                    <div className="hidden md:block">
                        <h1 className="text-xl font-semibold tracking-tight">Tags</h1>
                        <p className="hidden lg:block text-sm text-muted-foreground mt-0.5">Label and categorize your posts</p>
                    </div>
                    {can('tags.create') && (
                        <button
                            onClick={openCreate}
                            className="inline-flex items-center gap-2 h-9 px-4 bg-primary text-primary-foreground rounded-md text-sm font-medium hover:bg-primary/90 active:bg-primary/80 transition-all shadow-sm"
                        >
                            <Plus className="w-4 h-4" />
                            New Tag
                        </button>
                    )}
                </div>

                <div className="bg-background border border-border-subtle rounded-lg overflow-hidden">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-border-subtle bg-surface-muted/50">
                                <th className="text-left px-4 py-3 font-medium">Name</th>
                                <th className="text-left px-4 py-3 font-medium">Posts</th>
                                <th className="text-left px-4 py-3 font-medium">Visibility</th>
                                <th className="text-right px-4 py-3 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border-subtle">
                            {tags.map((tag) => (
                                <tr key={tag.id} className="hover:bg-surface-muted/30 transition-colors">
                                    <td className="px-4 py-3">
                                        <div className="flex items-center gap-3">
                                            <div 
                                                className="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border border-border-subtle"
                                                style={{ backgroundColor: tag.color ? `${tag.color}20` : 'var(--surface-muted)' }}
                                            >
                                                <TagsIcon className="w-4 h-4" style={{ color: tag.color || 'var(--muted-foreground)' }} />
                                            </div>
                                            <div>
                                                <button
                                                    onClick={() => openEdit(tag)}
                                                    className="font-medium hover:text-primary transition-colors flex items-center gap-2"
                                                >
                                                    #{tag.name}
                                                </button>
                                                {tag.description && (
                                                    <p className="text-xs text-muted-foreground line-clamp-1">{tag.description}</p>
                                                )}
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-4 py-3">
                                        <span className="inline-flex items-center gap-1.5 text-muted-foreground">
                                            <Layers className="w-3.5 h-3.5" />
                                            {tag.posts_count ?? 0}
                                        </span>
                                    </td>
                                    <td className="px-4 py-3">
                                        {tag.is_visible ? (
                                            <span className="inline-flex items-center gap-1.5 text-xs font-medium text-success bg-success/10 px-2 py-1 rounded-md">
                                                <span className="w-1.5 h-1.5 rounded-full bg-success"></span>
                                                Visible
                                            </span>
                                        ) : (
                                            <span className="inline-flex items-center gap-1.5 text-xs font-medium text-muted-foreground bg-surface-muted px-2 py-1 rounded-md">
                                                <span className="w-1.5 h-1.5 rounded-full bg-muted-foreground"></span>
                                                Hidden
                                            </span>
                                        )}
                                    </td>
                                    <td className="px-4 py-3 text-right">
                                        <div className="flex items-center justify-end gap-1">
                                            {can('tags.edit') && (
                                                <button
                                                    onClick={() => openEdit(tag)}
                                                    className="p-1.5 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors"
                                                    title="Edit"
                                                >
                                                    <Pencil className="w-3.5 h-3.5" />
                                                </button>
                                            )}
                                            {can('tags.delete') && (
                                                <button
                                                    onClick={() => setDeleteTarget({ id: tag.id, name: tag.name })}
                                                    className="p-1.5 rounded-md text-muted-foreground hover:text-danger hover:bg-danger/10 transition-colors"
                                                    title="Delete"
                                                >
                                                    <Trash2 className="w-3.5 h-3.5" />
                                                </button>
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    {tags.length === 0 && (
                        <div className="flex flex-col items-center justify-center py-16 text-center">
                            <TagsIcon className="w-12 h-12 text-muted-foreground/30 mb-4" />
                            <p className="text-sm font-medium text-foreground">No tags yet</p>
                            <p className="text-xs text-muted-foreground mt-1">Create tags to organize your posts.</p>
                        </div>
                    )}
                </div>
            </div>

            <Dialog open={modalOpen} onOpenChange={(open) => { if (!open) { setModalOpen(false); resetForm(); } }}>
                <DialogContent className="max-w-md max-h-[90vh] flex flex-col p-0 gap-0">
                    <DialogHeader className="flex flex-row items-center justify-between px-6 py-4 border-b border-border-subtle mb-0 shrink-0">
                        <DialogTitle className="text-base">{isEdit ? 'Edit Tag' : 'Create Tag'}</DialogTitle>
                        <DialogClose className="p-1 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors">
                            <X className="w-4 h-4" />
                        </DialogClose>
                    </DialogHeader>
                    <form onSubmit={handleSubmit} className="flex flex-col flex-1 min-h-0">
                        <div className="space-y-4 px-6 py-4 overflow-y-auto flex-1">
                            <Input
                                        label="Name"
                                        value={name}
                                        onChange={(e) => setName(e.target.value)}
                                        error={errors.name}
                                        required
                                        placeholder="Tag name"
                                    />

                                    <div className="space-y-1.5">
                                        <label className="text-sm font-medium">Description</label>
                                        <textarea
                                            value={description}
                                            onChange={(e) => setDescription(e.target.value)}
                                            placeholder="Optional description shown on tag page..."
                                            rows={3}
                                            className="flex w-full rounded-sm border border-border-subtle bg-background px-3 py-1.5 text-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-primary hover:border-primary"
                                        />
                                        {errors.description && <p className="text-xs text-destructive">{errors.description}</p>}
                                    </div>

                                    <div className="space-y-2">
                                        <label className="text-sm font-medium">Tag Color</label>
                                        <div className="flex items-center gap-3">
                                            <div className="relative w-10 h-10 rounded-full border-2 border-border-strong overflow-hidden shrink-0 shadow-sm" style={{ backgroundColor: color }}>
                                                <input 
                                                    type="color" 
                                                    value={color} 
                                                    onChange={(e) => setColor(e.target.value)}
                                                    className="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                                                />
                                            </div>
                                            <Input 
                                                value={color} 
                                                onChange={(e) => setColor(e.target.value)}
                                                placeholder="#64748b"
                                                className="flex-1 font-mono text-sm uppercase"
                                                error={errors.color}
                                            />
                                        </div>
                                        <p className="text-xs text-muted-foreground">Used for badges and accents.</p>
                                    </div>
                                    
                                    <div className="flex items-center justify-between p-4 bg-gradient-to-r from-surface-muted/50 to-transparent rounded-xl border border-border-subtle/50 group hover:border-border-subtle transition-colors mt-4">
                                        <div>
                                            <label className="text-sm font-semibold flex items-center gap-2">
                                                Visibility
                                                {isVisible ? <span className="w-2 h-2 rounded-full bg-success shadow-[0_0_8px_rgba(34,197,94,0.5)]"></span> : <span className="w-2 h-2 rounded-full bg-muted-foreground"></span>}
                                            </label>
                                            <span className="text-xs text-muted-foreground mt-0.5 block">Show this tag on the public site</span>
                                        </div>
                                        <Switch checked={isVisible} onCheckedChange={setIsVisible} />
                                    </div>

                                    {isEdit && editTag && (
                                        <div className="mt-6 pt-6 border-t border-border-subtle space-y-3">
                                            <h4 className="text-sm font-semibold text-foreground">Tag Information</h4>
                                            <div className="grid grid-cols-2 gap-y-2 gap-x-4 text-xs">
                                                <div className="flex flex-col gap-1">
                                                    <span className="text-muted-foreground">ID</span>
                                                    <span className="font-mono text-[10px] text-foreground break-all bg-surface-muted px-1.5 py-0.5 rounded w-fit">{editTag.id}</span>
                                                </div>
                                                <div className="flex flex-col gap-1">
                                                    <span className="text-muted-foreground">Slug</span>
                                                    <span className="font-medium text-foreground">{editTag.slug}</span>
                                                </div>
                                                <div className="flex flex-col gap-1">
                                                    <span className="text-muted-foreground">Posts Count</span>
                                                    <span className="font-medium text-foreground">{editTag.posts_count}</span>
                                                </div>
                                                <div className="flex flex-col gap-1">
                                                    <span className="text-muted-foreground">Created At</span>
                                                    <span className="font-medium text-foreground">{new Date(editTag.created_at).toLocaleString()}</span>
                                                </div>
                                                <div className="flex flex-col gap-1">
                                                    <span className="text-muted-foreground">Updated At</span>
                                                    <span className="font-medium text-foreground">{new Date(editTag.updated_at).toLocaleString()}</span>
                                                </div>
                                                {editTag.deleted_at && (
                                                    <div className="flex flex-col gap-1">
                                                        <span className="text-muted-foreground">Deleted At</span>
                                                        <span className="font-medium text-danger">{new Date(editTag.deleted_at).toLocaleString()}</span>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    )}
                        </div>
                                <div className="flex items-center justify-between w-full px-6 py-4 border-t border-border-subtle mt-0 shrink-0 bg-surface-muted/30">
                                    <div className="flex gap-8">
                                        {isEdit && editTag && (
                                            <>
                                                <div className="flex flex-col gap-1">
                                                    <span className="text-muted-foreground text-[10px] uppercase tracking-wider font-semibold">Created</span>
                                                    {editTag.created_by ? (
                                                        <div className="flex flex-col">
                                                            <span className="font-medium text-sm text-foreground">{editTag.created_by}</span>
                                                            <span className="text-xs text-muted-foreground">{formatDate(editTag.created_at)}</span>
                                                        </div>
                                                    ) : (
                                                        <div className="flex flex-col">
                                                            <span className="font-medium text-sm text-foreground">System</span>
                                                            <span className="text-xs text-muted-foreground">{formatDate(editTag.created_at)}</span>
                                                        </div>
                                                    )}
                                                </div>
                                                <div className="flex flex-col gap-1">
                                                    <span className="text-muted-foreground text-[10px] uppercase tracking-wider font-semibold">Updated</span>
                                                    {editTag.updated_by && editTag.updated_at ? (
                                                        <div className="flex flex-col">
                                                            <span className="font-medium text-sm text-foreground">{editTag.updated_by}</span>
                                                            <span className="text-xs text-muted-foreground">{formatDate(editTag.updated_at)}</span>
                                                        </div>
                                                    ) : (
                                                        <span className="text-sm text-muted-foreground italic mt-0.5">Never updated</span>
                                                    )}
                                                </div>
                                            </>
                                        )}
                                    </div>
                                    <div className="flex items-center gap-2 shrink-0">
                                        <DialogClose asChild>
                                            <button
                                                type="button"
                                                className="inline-flex items-center justify-center h-9 px-4 border border-border-subtle bg-background text-foreground rounded-md text-sm font-medium hover:bg-surface-muted active:bg-surface-muted/80 transition-all shadow-sm"
                                            >
                                                Cancel
                                            </button>
                                        </DialogClose>
                                        <Btn
                                            type="submit"
                                            loading={submitting}
                                            disabled={!isDirty || submitting}
                                            className="h-9 px-4"
                                            icon={<Save className="w-4 h-4" />}
                                        >
                                            {isEdit ? 'Update Tag' : 'Create Tag'}
                                        </Btn>
                                    </div>
                                </div>
                    </form>
                </DialogContent>
            </Dialog>

            <ConfirmDialog
                open={!!deleteTarget}
                onOpenChange={(open) => { if (!open) setDeleteTarget(null); }}
                title="Delete Tag"
                message={deleteTarget ? `Are you sure you want to delete "${deleteTarget.name}"? Posts with this tag will remain.` : ''}
                confirmLabel="Delete"
                variant="danger"
                onConfirm={handleDelete}
            />
        </DashboardLayout>
    );
}
