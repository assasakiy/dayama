import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Input } from '@dashboard/Components/ui/input';
import { Textarea } from '@dashboard/Components/ui/textarea';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogClose, DialogFooter } from '@dashboard/Components/ui/dialog';
import { Switch } from '@dashboard/Components/ui/switch';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import { Btn } from '@dashboard/Components/ui/btn';
import { Badge } from '@dashboard/Components/ui/badge';
import {
    Plus,
    Pencil,
    Trash2,
    Megaphone,
    Save,
    ExternalLink,
    Eye,
    EyeOff,
    X,
} from 'lucide-react';

interface Cta {
    id: string;
    name: string;
    title: string;
    subtitle: string | null;
    button_text: string;
    button_url: string | null;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export default function CtaIndex({ ctas }: { ctas: Cta[] }) {
    const [modalOpen, setModalOpen] = useState(false);
    const [editCta, setEditCta] = useState<Cta | null>(null);
    const [deleteTarget, setDeleteTarget] = useState<Cta | null>(null);
    const [submitting, setSubmitting] = useState(false);

    // Form state
    const [name, setName] = useState('');
    const [title, setTitle] = useState('');
    const [subtitle, setSubtitle] = useState('');
    const [buttonText, setButtonText] = useState('Selengkapnya');
    const [buttonUrl, setButtonUrl] = useState('');
    const [isActive, setIsActive] = useState(true);

    const isEdit = !!editCta;

    const resetForm = () => {
        setName('');
        setTitle('');
        setSubtitle('');
        setButtonText('Selengkapnya');
        setButtonUrl('');
        setIsActive(true);
        setEditCta(null);
    };

    const openCreate = () => {
        resetForm();
        setModalOpen(true);
    };

    const openEdit = (cta: Cta) => {
        setEditCta(cta);
        setName(cta.name);
        setTitle(cta.title);
        setSubtitle(cta.subtitle || '');
        setButtonText(cta.button_text);
        setButtonUrl(cta.button_url || '');
        setIsActive(cta.is_active);
        setModalOpen(true);
    };

    const handleSubmit = () => {
        setSubmitting(true);
        const data = {
            name,
            title,
            subtitle: subtitle || null,
            button_text: buttonText,
            button_url: buttonUrl || null,
            is_active: isActive,
        };

        if (isEdit && editCta) {
            router.put(`/landing/ctas/${editCta.id}`, data, {
                preserveScroll: true,
                onFinish: () => { setSubmitting(false); setModalOpen(false); resetForm(); },
            });
        } else {
            router.post('/landing/ctas', data, {
                preserveScroll: true,
                onFinish: () => { setSubmitting(false); setModalOpen(false); resetForm(); },
            });
        }
    };

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/landing/ctas/${deleteTarget.id}`, {
            preserveScroll: true,
            onFinish: () => setDeleteTarget(null),
        });
    };

    return (
        <DashboardLayout>
            <Head title="Manajemen CTA" />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-foreground">Manajemen CTA</h1>
                        <p className="text-sm text-muted-foreground mt-1">
                            Kelola Blok Call-to-Action. CTA ini dapat dipanggil di berbagai halaman landing secara konsisten.
                        </p>
                    </div>
                    <Btn onClick={openCreate} icon={<Plus className="w-4 h-4" />}>
                        Tambah CTA
                    </Btn>
                </div>

                {/* CTA Cards */}
                <div className="grid gap-4 md:grid-cols-2">
                    {ctas.map((cta) => (
                        <div
                            key={cta.id}
                            className="group relative rounded-lg border border-border-subtle bg-background overflow-hidden transition-all hover:shadow-sm hover:border-primary/20"
                        >
                            {/* Preview Area */}
                            <div className="bg-gradient-to-br from-primary/5 to-primary/10 p-6">
                                <div className="flex items-center justify-between mb-3">
                                    <Badge variant="secondary" className="text-xs font-mono">{cta.name}</Badge>
                                    {cta.is_active ? (
                                        <Badge variant="default" className="bg-success/10 text-success border-success/20">
                                            <Eye className="w-3 h-3 mr-1" /> Aktif
                                        </Badge>
                                    ) : (
                                        <Badge variant="secondary" className="bg-zinc-500/10 text-zinc-500 border-zinc-500/20">
                                            <EyeOff className="w-3 h-3 mr-1" /> Nonaktif
                                        </Badge>
                                    )}
                                </div>
                                <h3 className="text-lg font-bold text-foreground">{cta.title}</h3>
                                {cta.subtitle && (
                                    <p className="text-sm text-muted-foreground mt-1 line-clamp-2">{cta.subtitle}</p>
                                )}
                                <div className="mt-4 flex items-center gap-2">
                                    <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-primary text-primary-foreground text-sm font-medium">
                                        {cta.button_text}
                                        <ExternalLink className="w-3 h-3" />
                                    </span>
                                    {cta.button_url && (
                                        <span className="text-xs text-muted-foreground font-mono truncate max-w-[180px]">{cta.button_url}</span>
                                    )}
                                    {!cta.button_url && (
                                        <span className="text-xs text-muted-foreground italic">URL default</span>
                                    )}
                                </div>
                            </div>

                            {/* Actions */}
                            <div className="flex items-center justify-end gap-1 p-3 border-t border-border">
                                <Btn variant="ghost" size="sm" onClick={() => openEdit(cta)} icon={<Pencil className="w-4 h-4" />}>
                                    Edit
                                </Btn>
                                <Btn variant="ghost" size="sm" onClick={() => setDeleteTarget(cta)} className="text-destructive hover:text-destructive" icon={<Trash2 className="w-4 h-4" />}>
                                    Hapus
                                </Btn>
                            </div>
                        </div>
                    ))}
                </div>

                {ctas.length === 0 && (
                    <div className="text-center py-16 text-muted-foreground rounded-lg border border-border-subtle bg-background">
                        <Megaphone className="w-12 h-12 mx-auto mb-4 opacity-40" />
                        <p className="text-lg font-medium">Belum ada CTA</p>
                        <p className="text-sm">Klik "Tambah CTA" untuk membuat Blok Call-to-Action pertama.</p>
                    </div>
                )}
            </div>

            {/* Create/Edit Modal */}
            <Dialog open={modalOpen} onOpenChange={(v) => { if (!v) { setModalOpen(false); resetForm(); } }}>
                <DialogContent className="max-w-2xl max-h-[90vh] flex flex-col p-0 gap-0">
                    <DialogHeader className="flex flex-row items-center justify-between px-6 py-4 border-b border-border-subtle mb-0 shrink-0">
                        <DialogTitle className="text-base">{isEdit ? 'Edit CTA' : 'Tambah CTA Baru'}</DialogTitle>
                        <DialogClose className="p-1 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors">
                            <X className="w-4 h-4" />
                        </DialogClose>
                    </DialogHeader>

                    <div className="flex flex-col flex-1 min-h-0">
                        <div className="space-y-4 px-6 py-4 overflow-y-auto flex-1">
                        <div className="space-y-1.5">
                            <label className="text-sm font-medium">Nama Internal</label>
                            <Input
                                value={name}
                                onChange={e => setName(e.target.value)}
                                placeholder="CTA Pendaftaran"
                            />
                            <p className="text-xs text-muted-foreground">Identifier internal, tidak ditampilkan ke pengunjung.</p>
                        </div>
                        <div className="space-y-1.5">
                            <label className="text-sm font-medium">Judul</label>
                            <Input
                                value={title}
                                onChange={e => setTitle(e.target.value)}
                                placeholder="Siap Bergabung?"
                            />
                        </div>
                        <div className="space-y-1.5">
                            <label className="text-sm font-medium">Deskripsi (opsional)</label>
                            <Textarea
                                value={subtitle}
                                onChange={e => setSubtitle(e.target.value)}
                                placeholder="Daftarkan putra-putri Anda sekarang..."
                                rows={3}
                            />
                        </div>
                        <div className="grid grid-cols-2 gap-3">
                            <div className="space-y-1.5">
                                <label className="text-sm font-medium">Teks Tombol</label>
                                <Input
                                    value={buttonText}
                                    onChange={e => setButtonText(e.target.value)}
                                    placeholder="Daftar Sekarang"
                                />
                            </div>
                            <div className="space-y-1.5">
                                <label className="text-sm font-medium">URL Tombol (opsional)</label>
                                <Input
                                    value={buttonUrl}
                                    onChange={e => setButtonUrl(e.target.value)}
                                    placeholder="https://... (kosongkan untuk default)"
                                />
                            </div>
                        </div>
                        <div className="flex items-center justify-between">
                            <label className="text-sm font-medium">Aktif</label>
                            <Switch checked={isActive} onCheckedChange={setIsActive} />
                        </div>
                    </div>

                    <div className="px-6 py-4 bg-surface-muted/30 border-t border-border-subtle shrink-0 flex items-center justify-end gap-3">
                        <DialogClose asChild>
                            <Btn variant="outline" type="button" onClick={() => { setModalOpen(false); resetForm(); }}>Batal</Btn>
                        </DialogClose>
                        <Btn onClick={handleSubmit} disabled={submitting || !name.trim() || !title.trim()} icon={<Save className="w-4 h-4" />}>
                            {submitting ? 'Menyimpan...' : isEdit ? 'Perbarui' : 'Simpan'}
                        </Btn>
                    </div>
                </div>
                </DialogContent>
            </Dialog>

            {/* Delete Confirmation */}
            <ConfirmDialog
                open={!!deleteTarget}
                onOpenChange={() => setDeleteTarget(null)}
                onConfirm={handleDelete}
                title="Hapus CTA"
                message={`Apakah Anda yakin ingin menghapus CTA "${deleteTarget?.name}"?`}
            />
        </DashboardLayout>
    );
}
