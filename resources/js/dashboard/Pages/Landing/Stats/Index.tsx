import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Input } from '@dashboard/Components/ui/input';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogClose, DialogFooter } from '@dashboard/Components/ui/dialog';
import { Switch } from '@dashboard/Components/ui/switch';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import { Btn } from '@dashboard/Components/ui/btn';
import { Badge } from '@dashboard/Components/ui/badge';
import {
    Plus,
    Pencil,
    Trash2,
    BarChart3,
    Save,
    Eye,
    EyeOff,
    X,
} from 'lucide-react';

interface StatGroup {
    id: string;
    name: string;
    items: { number: string; label: string }[];
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export default function StatGroupIndex({ statGroups }: { statGroups: StatGroup[] }) {
    const [modalOpen, setModalOpen] = useState(false);
    const [editGroup, setEditGroup] = useState<StatGroup | null>(null);
    const [deleteTarget, setDeleteTarget] = useState<StatGroup | null>(null);
    const [submitting, setSubmitting] = useState(false);

    // Form state
    const [name, setName] = useState('');
    const [items, setItems] = useState<{ number: string; label: string }[]>([]);
    const [isActive, setIsActive] = useState(true);

    const isEdit = !!editGroup;

    const resetForm = () => {
        setName('');
        setItems([]);
        setIsActive(true);
        setEditGroup(null);
    };

    const openCreate = () => {
        resetForm();
        setModalOpen(true);
    };

    const openEdit = (group: StatGroup) => {
        setEditGroup(group);
        setName(group.name);
        setItems(group.items || []);
        setIsActive(group.is_active);
        setModalOpen(true);
    };

    const addItem = () => {
        setItems([...items, { number: '', label: '' }]);
    };

    const updateItem = (index: number, field: 'number' | 'label', value: string) => {
        const newItems = [...items];
        newItems[index][field] = value;
        setItems(newItems);
    };

    const removeItem = (index: number) => {
        const newItems = [...items];
        newItems.splice(index, 1);
        setItems(newItems);
    };

    const handleSubmit = () => {
        setSubmitting(true);
        const data = {
            name,
            items,
            is_active: isActive,
        };

        if (isEdit && editGroup) {
            router.put(`/landing/stats/${editGroup.id}`, data, {
                preserveScroll: true,
                onFinish: () => { setSubmitting(false); setModalOpen(false); resetForm(); },
            });
        } else {
            router.post('/landing/stats', data, {
                preserveScroll: true,
                onFinish: () => { setSubmitting(false); setModalOpen(false); resetForm(); },
            });
        }
    };

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/landing/stats/${deleteTarget.id}`, {
            preserveScroll: true,
            onFinish: () => setDeleteTarget(null),
        });
    };

    return (
        <DashboardLayout>
            <Head title="Manajemen Statistik" />

            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-foreground">Manajemen Statistik</h1>
                        <p className="text-sm text-muted-foreground mt-1">
                            Kelola grup statistik (angka-angka highlight) untuk halaman landing.
                        </p>
                    </div>
                    <Btn onClick={openCreate} icon={<Plus className="w-4 h-4" />}>
                        Tambah Grup
                    </Btn>
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    {statGroups.map((group) => (
                        <div
                            key={group.id}
                            className="group relative rounded-lg border border-border-subtle bg-background overflow-hidden transition-all hover:shadow-sm hover:border-primary/20"
                        >
                            <div className="bg-gradient-to-br from-primary/5 to-primary/10 p-6">
                                <div className="flex items-center justify-between mb-4">
                                    <h3 className="text-lg font-bold text-foreground">{group.name}</h3>
                                    {group.is_active ? (
                                        <Badge variant="default" className="bg-success/10 text-success border-success/20">
                                            <Eye className="w-3 h-3 mr-1" /> Aktif
                                        </Badge>
                                    ) : (
                                        <Badge variant="secondary" className="bg-zinc-500/10 text-zinc-500 border-zinc-500/20">
                                            <EyeOff className="w-3 h-3 mr-1" /> Nonaktif
                                        </Badge>
                                    )}
                                </div>
                                <div className="grid grid-cols-2 gap-4">
                                    {(group.items || []).map((item, idx) => (
                                        <div key={idx} className="bg-background rounded p-3 border border-border-subtle">
                                            <div className="text-xl font-bold text-primary">{item.number}</div>
                                            <div className="text-xs text-muted-foreground mt-1">{item.label}</div>
                                        </div>
                                    ))}
                                    {(group.items || []).length === 0 && (
                                        <div className="col-span-2 text-sm text-muted-foreground italic">
                                            Belum ada data statistik.
                                        </div>
                                    )}
                                </div>
                            </div>

                            <div className="flex items-center justify-end gap-1 p-3 border-t border-border">
                                <Btn variant="ghost" size="sm" onClick={() => openEdit(group)} icon={<Pencil className="w-4 h-4" />}>
                                    Edit
                                </Btn>
                                <Btn variant="ghost" size="sm" onClick={() => setDeleteTarget(group)} className="text-destructive hover:text-destructive" icon={<Trash2 className="w-4 h-4" />}>
                                    Hapus
                                </Btn>
                            </div>
                        </div>
                    ))}
                </div>

                {statGroups.length === 0 && (
                    <div className="text-center py-16 text-muted-foreground rounded-lg border border-border-subtle bg-background">
                        <BarChart3 className="w-12 h-12 mx-auto mb-4 opacity-40" />
                        <p className="text-lg font-medium">Belum ada grup statistik</p>
                        <p className="text-sm">Klik "Tambah Grup" untuk membuat grup statistik pertama.</p>
                    </div>
                )}
            </div>

            <Dialog open={modalOpen} onOpenChange={(v) => { if (!v) { setModalOpen(false); resetForm(); } }}>
                <DialogContent className="max-w-2xl max-h-[90vh] flex flex-col p-0 gap-0">
                    <DialogHeader className="flex flex-row items-center justify-between px-6 py-4 border-b border-border-subtle mb-0 shrink-0">
                        <DialogTitle className="text-base">{isEdit ? 'Edit Grup Statistik' : 'Tambah Grup Statistik'}</DialogTitle>
                        <DialogClose className="p-1 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors">
                            <X className="w-4 h-4" />
                        </DialogClose>
                    </DialogHeader>

                    <div className="flex flex-col flex-1 min-h-0">
                        <div className="space-y-6 px-6 py-4 overflow-y-auto flex-1">
                        <div className="space-y-1.5">
                            <label className="text-sm font-medium">Nama Grup</label>
                            <Input
                                value={name}
                                onChange={e => setName(e.target.value)}
                                placeholder="Contoh: Statistik Utama Home"
                            />
                        </div>

                        <div className="space-y-3">
                            <div className="flex items-center justify-between">
                                <label className="text-sm font-medium">Data Statistik</label>
                                <Btn variant="outline" size="sm" onClick={addItem} icon={<Plus className="w-3.5 h-3.5" />}>
                                    Tambah Item
                                </Btn>
                            </div>

                            <div className="space-y-3">
                                {items.map((item, i) => (
                                    <div key={i} className="flex items-center gap-3 bg-muted/30 p-3 rounded-lg border border-border-subtle relative">
                                        <div className="flex-1 space-y-1.5">
                                            <label className="text-xs text-muted-foreground">Angka/Nilai</label>
                                            <Input
                                                value={item.number}
                                                onChange={e => updateItem(i, 'number', e.target.value)}
                                                placeholder="Misal: 500+"
                                            />
                                        </div>
                                        <div className="flex-[2] space-y-1.5">
                                            <label className="text-xs text-muted-foreground">Label</label>
                                            <Input
                                                value={item.label}
                                                onChange={e => updateItem(i, 'label', e.target.value)}
                                                placeholder="Misal: Santri Aktif"
                                            />
                                        </div>
                                        <div className="mt-5">
                                            <Btn variant="ghost" size="sm" className="text-destructive hover:text-destructive p-2" onClick={() => removeItem(i)} icon={<Trash2 className="w-4 h-4" />} />
                                        </div>
                                    </div>
                                ))}
                                {items.length === 0 && (
                                    <div className="text-center p-6 border border-dashed border-border-subtle rounded-lg text-muted-foreground text-sm">
                                        Belum ada item statistik ditambahkan.
                                    </div>
                                )}
                            </div>
                        </div>

                        <div className="flex items-center justify-between pt-4 border-t border-border-subtle">
                            <div>
                                <p className="text-sm font-medium">Status Aktif</p>
                                <p className="text-xs text-muted-foreground">Grup ini dapat dipilih jika aktif.</p>
                            </div>
                            <Switch checked={isActive} onCheckedChange={setIsActive} />
                        </div>
                    </div>

                    <div className="px-6 py-4 bg-surface-muted/30 border-t border-border-subtle shrink-0 flex items-center justify-end gap-3">
                        <DialogClose asChild>
                            <Btn variant="outline" type="button" onClick={() => { setModalOpen(false); resetForm(); }}>Batal</Btn>
                        </DialogClose>
                        <Btn onClick={handleSubmit} disabled={submitting || !name.trim()} icon={<Save className="w-4 h-4" />}>
                            {submitting ? 'Menyimpan...' : isEdit ? 'Perbarui' : 'Simpan'}
                        </Btn>
                    </div>
                </div>
                </DialogContent>
            </Dialog>

            <ConfirmDialog
                open={!!deleteTarget}
                onOpenChange={() => setDeleteTarget(null)}
                onConfirm={handleDelete}
                title="Hapus Grup Statistik"
                message={`Apakah Anda yakin ingin menghapus "${deleteTarget?.name}"?`}
            />
        </DashboardLayout>
    );
}
