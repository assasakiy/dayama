import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Plus, Pencil, Trash2, Briefcase } from 'lucide-react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@dashboard/Components/ui/dialog';
import { Input } from '@dashboard/Components/ui/input';
import { Textarea } from '@dashboard/Components/ui/textarea';
import { Btn } from '@dashboard/Components/ui/btn';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';

interface Position {
    id: string;
    nama: string;
    deskripsi: string | null;
    sort_order: number;
    created_at: string;
}

interface Props {
    positions: Position[];
}

function Index({ positions }: Props) {
    const [deleteTarget, setDeleteTarget] = useState<{ id: string; nama: string } | null>(null);
    const [showModal, setShowModal] = useState(false);
    const [editingPosition, setEditingPosition] = useState<Position | null>(null);
    const [nama, setNama] = useState('');
    const [deskripsi, setDeskripsi] = useState('');
    const [sortOrder, setSortOrder] = useState('');
    const [saving, setSaving] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    const openCreate = () => {
        setEditingPosition(null);
        setNama('');
        setDeskripsi('');
        setSortOrder('');
        setErrors({});
        setShowModal(true);
    };

    const openEdit = (position: Position) => {
        setEditingPosition(position);
        setNama(position.nama);
        setDeskripsi(position.deskripsi || '');
        setSortOrder(position.sort_order?.toString() || '');
        setErrors({});
        setShowModal(true);
    };

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/hr/positions/${deleteTarget.id}`, {
            onSuccess: () => setDeleteTarget(null),
        });
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSaving(true);

        const data = {
            nama,
            deskripsi: deskripsi || null,
            sort_order: sortOrder ? parseInt(sortOrder, 10) : 0,
        };

        const url = editingPosition ? `/hr/positions/${editingPosition.id}` : '/hr/positions';
        const method = editingPosition ? 'put' : 'post';

        router[method](url, data, {
            preserveScroll: true,
            onSuccess: () => {
                setShowModal(false);
                setSaving(false);
            },
            onError: (err) => {
                setErrors(err);
                setSaving(false);
            },
        });
    };

    return (
        <>
            <Head title="Jabatan" />

            <div className="flex items-center justify-between mb-6">
                <div>
                    <h1 className="text-2xl font-bold">Jabatan</h1>
                    <p className="text-muted-foreground text-sm mt-1">
                        Kelola jabatan / posisi karyawan
                    </p>
                </div>
                <Btn variant="primary" onClick={openCreate}>
                    <Plus className="w-4 h-4 mr-2" />
                    Tambah Jabatan
                </Btn>
            </div>

            <div className="bg-background rounded-xl border border-border-subtle overflow-hidden">
                <table className="w-full">
                    <thead>
                        <tr className="border-b border-border-subtle bg-surface/50">
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Jabatan</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Deskripsi</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Urutan</th>
                            <th className="text-right px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {positions.length === 0 ? (
                            <tr>
                                <td colSpan={4} className="px-6 py-12 text-center text-muted-foreground">
                                    <Briefcase className="w-10 h-10 mx-auto mb-3 opacity-40" />
                                    <p>Belum ada jabatan.</p>
                                    <button onClick={openCreate} className="text-primary text-sm mt-1 inline-block hover:underline">
                                        Tambah jabatan baru
                                    </button>
                                </td>
                            </tr>
                        ) : (
                            positions.map((position) => (
                                <tr key={position.id} className="border-b border-border-subtle last:border-0 hover:bg-surface/30 transition-colors">
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-3">
                                            <div className="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center">
                                                <Briefcase className="w-4 h-4 text-primary" />
                                            </div>
                                            <div>
                                                <p className="font-medium text-sm">{position.nama}</p>
                                                <p className="text-xs text-muted-foreground">
                                                    Dibuat {new Date(position.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 text-sm text-muted-foreground">
                                        {position.deskripsi || '-'}
                                    </td>
                                    <td className="px-6 py-4 text-sm text-muted-foreground">
                                        {position.sort_order}
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <div className="flex items-center justify-end gap-1">
                                            <button
                                                onClick={() => openEdit(position)}
                                                className="p-2 rounded-lg text-muted-foreground hover:bg-surface-muted hover:text-foreground transition-colors"
                                            >
                                                <Pencil className="w-4 h-4" />
                                            </button>
                                            <button
                                                onClick={() => setDeleteTarget({ id: position.id, nama: position.nama })}
                                                className="p-2 rounded-lg text-muted-foreground hover:bg-destructive/10 hover:text-destructive transition-colors"
                                            >
                                                <Trash2 className="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>

            <Dialog open={showModal} onOpenChange={(open) => { if (!open) setShowModal(false); }}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>{editingPosition ? 'Edit Jabatan' : 'Tambah Jabatan'}</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={handleSubmit} className="px-6 py-4 space-y-5 overflow-y-auto">
                        <div>
                            <label className="block text-sm font-medium mb-1.5">
                                Nama Jabatan <span className="text-destructive">*</span>
                            </label>
                            <Input
                                value={nama}
                                onChange={(e) => setNama(e.target.value)}
                                placeholder="Nama jabatan"
                                className={errors.nama ? 'border-destructive' : ''}
                            />
                            {errors.nama && <p className="text-xs text-destructive mt-1">{errors.nama}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium mb-1.5">Deskripsi</label>
                            <Textarea
                                value={deskripsi}
                                onChange={(e) => setDeskripsi(e.target.value)}
                                placeholder="Deskripsi jabatan (opsional)"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium mb-1.5">Urutan</label>
                            <Input
                                type="number"
                                value={sortOrder}
                                onChange={(e) => setSortOrder(e.target.value)}
                                placeholder="0"
                                min={0}
                            />
                        </div>
                    </form>
                    <DialogFooter>
                        <Btn variant="outline" type="button" onClick={() => setShowModal(false)}>Batal</Btn>
                        <Btn variant="primary" type="submit" onClick={handleSubmit} disabled={saving}>
                            {saving ? 'Menyimpan...' : 'Simpan'}
                        </Btn>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <ConfirmDialog
                open={!!deleteTarget}
                onOpenChange={() => setDeleteTarget(null)}
                onConfirm={handleDelete}
                title="Hapus Jabatan"
                description={`Apakah Anda yakin ingin menghapus jabatan "${deleteTarget?.nama}"?`}
            />
        </>
    );
}

Index.layout = (page: React.ReactNode) => <DashboardLayout>{page}</DashboardLayout>;

export default Index;
