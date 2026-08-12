import React, { useState } from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Btn } from '@dashboard/Components/ui/btn';
import { Badge } from '@dashboard/Components/ui/badge';
import { Input } from '@dashboard/Components/ui/input';
import {
    School,
    Pencil,
    Trash2,
    Plus,
    Globe,
} from 'lucide-react';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter, DialogDescription } from '@dashboard/Components/ui/dialog';

interface InstitutionType {
    id: string;
    nama: string;
}

interface Institution {
    id: string;
    name: string;
    slug: string;
    short_description: string;
    is_active: boolean;
    sort_order: number;
    status: string;
    kode: string | null;
    type: InstitutionType | null;
    updated_at: string;
}

interface Role {
    id: string;
    name: string;
    display_name: string;
}

export default function InstitutionsIndex({ institutions, roles, institutionTypes }: { institutions: Institution[]; roles: Role[]; institutionTypes: InstitutionType[] }) {
    const [createModalOpen, setCreateModalOpen] = useState(false);
    const [deleteTarget, setDeleteTarget] = useState<Institution | null>(null);

    const { data, setData, post, processing, errors, reset, clearErrors } = useForm({
        name: '',
        slug: '',
        institution_type_id: '',
        assign_role_id: '',
    });

    const statusBadge = (status: string) => {
        const styles: Record<string, string> = {
            draft: 'bg-gray-50 text-gray-600 border border-gray-200',
            menunggu_kelengkapan: 'bg-yellow-50 text-yellow-700 border border-yellow-200',
            lengkap: 'bg-blue-50 text-blue-700 border border-blue-200',
            terverifikasi: 'bg-green-50 text-green-700 border border-green-200',
        };
        const labels: Record<string, string> = {
            draft: 'Draft',
            menunggu_kelengkapan: 'Menunggu',
            lengkap: 'Lengkap',
            terverifikasi: 'Terverifikasi',
        };
        return (
            <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium ${styles[status] || styles.draft}`}>
                {labels[status] || status}
            </span>
        );
    };

    const handleCreate = (e: React.FormEvent) => {
        e.preventDefault();
        post('/institutions', {
            preserveScroll: true,
            onSuccess: () => {
                setCreateModalOpen(false);
                reset();
            },
        });
    };

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/institutions/${deleteTarget.id}`, {
            preserveScroll: true,
            onFinish: () => setDeleteTarget(null),
        });
    };

    return (
        <DashboardLayout>
            <Head title="Lembaga Pendidikan" />

            <div className="flex items-center justify-between mb-6">
                <div>
                    <h1 className="text-2xl font-bold">Lembaga Pendidikan</h1>
                    <p className="text-muted-foreground text-sm mt-1">
                        Kelola profil dan halaman lembaga
                    </p>
                </div>
                <Btn variant="primary" onClick={() => setCreateModalOpen(true)}>
                    <Plus className="w-4 h-4 mr-2" />
                    Tambah Lembaga
                </Btn>
            </div>

            <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                {institutions.map((institution) => (
                    <div
                        key={institution.id}
                        className="group relative rounded-xl border border-border-subtle bg-background p-6 transition-all hover:shadow-sm hover:border-primary/20"
                    >
                        <div className="flex items-start justify-between mb-4">
                            <div className="flex items-center gap-3">
                                <div className="flex items-center justify-center w-10 h-10 rounded-lg bg-primary/10 text-primary shrink-0">
                                    <School className="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 className="font-semibold text-sm leading-tight">{institution.name}</h3>
                                    <div className="flex items-center gap-1.5 mt-0.5">
                                        <Globe className="w-3 h-3 text-muted-foreground" />
                                        <p className="text-xs text-muted-foreground font-mono">/{institution.slug}</p>
                                    </div>
                                </div>
                            </div>
                            <div className="flex items-center gap-2">
                                {statusBadge(institution.status || (institution.is_active ? 'lengkap' : 'draft'))}
                            </div>
                        </div>

                        <div className="flex items-center gap-2 text-xs text-muted-foreground mb-4">
                            {institution.type && (
                                <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-surface-muted">
                                    {institution.type.nama}
                                </span>
                            )}
                            {institution.kode && (
                                <span className="font-mono">Kode: {institution.kode}</span>
                            )}
                        </div>

                        <p className="text-sm text-muted-foreground line-clamp-2 min-h-[40px] mb-5">
                            {institution.short_description || 'Belum ada deskripsi singkat.'}
                        </p>

                        <div className="flex items-center justify-between pt-4 border-t border-border-subtle">
                            <span className="text-xs text-muted-foreground">
                                Diperbarui {new Date(institution.updated_at).toLocaleDateString('id-ID')}
                            </span>
                            <div className="flex items-center gap-1">
                                <Link href={`/institutions/${institution.id}/edit`}>
                                    <Btn variant="outline" size="sm">
                                        <Pencil className="w-3.5 h-3.5 mr-1.5" />
                                        Edit
                                    </Btn>
                                </Link>
                                <button
                                    onClick={() => setDeleteTarget(institution)}
                                    className="p-2 rounded-lg text-muted-foreground hover:bg-destructive/10 hover:text-destructive transition-colors"
                                >
                                    <Trash2 className="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {institutions.length === 0 && (
                <div className="text-center py-16 text-muted-foreground rounded-xl border border-border-subtle bg-background">
                    <School className="w-12 h-12 mx-auto mb-4 opacity-40" />
                    <p className="text-base font-medium">Belum ada lembaga</p>
                    <p className="text-sm mt-1">Klik "Tambah Lembaga" untuk menambahkan lembaga baru.</p>
                </div>
            )}

            <Dialog open={createModalOpen} onOpenChange={setCreateModalOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Tambah Lembaga Baru</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={handleCreate} className="px-6 py-4 space-y-5 overflow-y-auto">
                        <div>
                            <label className="block text-sm font-medium mb-1.5">
                                Nama Lembaga <span className="text-destructive">*</span>
                            </label>
                            <Input
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                placeholder="Misal: Madrasah Ibtidaiyah"
                                className={errors.name ? 'border-destructive' : ''}
                            />
                            {errors.name && <p className="text-xs text-destructive mt-1">{errors.name}</p>}
                        </div>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium mb-1.5">Tipe Lembaga</label>
                                <select
                                    value={data.institution_type_id}
                                    onChange={(e) => setData('institution_type_id', e.target.value)}
                                    className="flex h-10 w-full rounded-lg border border-border-subtle bg-background px-3 py-2 text-sm"
                                >
                                    <option value="">Pilih tipe</option>
                                    {institutionTypes.map((t) => (
                                        <option key={t.id} value={t.id}>{t.nama}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-1.5">Slug URL</label>
                                <Input
                                    value={data.slug}
                                    onChange={(e) => setData('slug', e.target.value)}
                                    placeholder="Generate otomatis"
                                    className={errors.slug ? 'border-destructive' : ''}
                                />
                                {errors.slug && <p className="text-xs text-destructive mt-1">{errors.slug}</p>}
                            </div>
                        </div>
                        <div>
                            <label className="block text-sm font-medium mb-1.5">Role Default</label>
                            <select
                                value={data.assign_role_id}
                                onChange={(e) => setData('assign_role_id', e.target.value)}
                                className={`flex h-10 w-full rounded-lg border bg-background px-3 py-2 text-sm ${errors.assign_role_id ? 'border-destructive' : 'border-border-subtle'}`}
                            >
                                <option value="">Pilih role (atau kosongkan)</option>
                                {roles.map((role) => (
                                    <option key={role.id} value={role.id}>
                                        {role.display_name}
                                    </option>
                                ))}
                            </select>
                            {errors.assign_role_id && <p className="text-xs text-destructive mt-1">{errors.assign_role_id}</p>}
                            <p className="text-xs text-muted-foreground mt-1.5">
                                Role ini akan diberikan ke akun Anda saat ini agar bisa mengelola lembaga.
                                Disarankan: <strong>Operator</strong>.
                            </p>
                        </div>
                    </form>
                    <DialogFooter>
                        <Btn variant="outline" type="button" onClick={() => { setCreateModalOpen(false); clearErrors(); reset(); }}>
                            Batal
                        </Btn>
                        <Btn variant="primary" type="submit" onClick={handleCreate} disabled={processing}>
                            {processing ? 'Menyimpan...' : 'Simpan'}
                        </Btn>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <ConfirmDialog
                open={!!deleteTarget}
                onOpenChange={(isOpen: boolean) => !isOpen && setDeleteTarget(null)}
                title="Hapus Lembaga"
                message={`Apakah Anda yakin ingin menghapus lembaga "${deleteTarget?.name}"? Tindakan ini tidak dapat dibatalkan.`}
                onConfirm={handleDelete}
                confirmLabel="Hapus"
                cancelLabel="Batal"
                variant="danger"
            />
        </DashboardLayout>
    );
}
