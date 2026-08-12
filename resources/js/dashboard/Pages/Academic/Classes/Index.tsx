import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Plus, Pencil, Trash2, Users, GraduationCap } from 'lucide-react';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import { Btn } from '@dashboard/Components/ui/btn';

interface AcademicYear {
    id: string;
    nama: string;
}

interface EducationLevel {
    id: string;
    nama: string;
}

interface ClassItem {
    id: string;
    name: string;
    slug: string;
    capacity: number;
    is_active: boolean;
    academic_year: AcademicYear | null;
    education_level: EducationLevel | null;
    created_at: string;
}

interface Props {
    classes: ClassItem[];
}

function Index({ classes }: Props) {
    const [deleteTarget, setDeleteTarget] = useState<{ id: string; name: string } | null>(null);

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/academic/classes/${deleteTarget.id}`, {
            onSuccess: () => setDeleteTarget(null),
        });
    };

    return (
        <>
            <Head title="Kelas" />

            <div className="flex items-center justify-between mb-6">
                <div>
                    <h1 className="text-2xl font-bold">Kelas</h1>
                    <p className="text-muted-foreground text-sm mt-1">
                        Kelola kelas akademik
                    </p>
                </div>
                <Link href="/academic/classes/create">
                    <Btn variant="primary">
                        <Plus className="w-4 h-4 mr-2" />
                        Tambah Kelas
                    </Btn>
                </Link>
            </div>

            <div className="bg-background rounded-xl border border-border-subtle overflow-hidden">
                <table className="w-full">
                    <thead>
                        <tr className="border-b border-border-subtle bg-surface/50">
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">
                                Nama Kelas
                            </th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">
                                Tingkat
                            </th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">
                                Tahun Ajaran
                            </th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">
                                Wali Kelas
                            </th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">
                                Kapasitas
                            </th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">
                                Status
                            </th>
                            <th className="text-right px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {classes.length === 0 ? (
                            <tr>
                                <td colSpan={7} className="px-6 py-12 text-center text-muted-foreground">
                                    <GraduationCap className="w-10 h-10 mx-auto mb-3 opacity-40" />
                                    <p>Belum ada kelas.</p>
                                    <Link href="/academic/classes/create" className="text-primary text-sm mt-1 inline-block hover:underline">
                                        Tambah kelas baru
                                    </Link>
                                </td>
                            </tr>
                        ) : (
                            classes.map((cls) => (
                                <tr key={cls.id} className="border-b border-border-subtle last:border-0 hover:bg-surface/30 transition-colors">
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-3">
                                            <div className="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center">
                                                <Users className="w-4 h-4 text-primary" />
                                            </div>
                                            <div>
                                                <p className="font-medium text-sm">{cls.name}</p>
                                                <p className="text-xs text-muted-foreground">
                                                    {new Date(cls.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 text-sm">
                                        {cls.education_level?.nama || '-'}
                                    </td>
                                    <td className="px-6 py-4 text-sm">
                                        {cls.academic_year?.nama || '-'}
                                    </td>
                                    <td className="px-6 py-4 text-sm text-muted-foreground">
                                        -
                                    </td>
                                    <td className="px-6 py-4 text-sm">
                                        {cls.capacity}
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium ${
                                            cls.is_active
                                                ? 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-950/40 dark:text-green-300'
                                                : 'bg-gray-50 text-gray-500 border border-gray-200 dark:bg-gray-900/40 dark:text-gray-400'
                                        }`}>
                                            {cls.is_active ? 'Aktif' : 'Tidak Aktif'}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <div className="flex items-center justify-end gap-1">
                                            <Link
                                                href={`/academic/classes/${cls.id}/edit`}
                                                className="p-2 rounded-lg text-muted-foreground hover:bg-surface-muted hover:text-foreground transition-colors"
                                            >
                                                <Pencil className="w-4 h-4" />
                                            </Link>
                                            <button
                                                onClick={() => setDeleteTarget({ id: cls.id, name: cls.name })}
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

            <ConfirmDialog
                open={!!deleteTarget}
                onOpenChange={() => setDeleteTarget(null)}
                onConfirm={handleDelete}
                title="Hapus Kelas"
                message={`Apakah Anda yakin ingin menghapus kelas "${deleteTarget?.name}"?`}
            />
        </>
    );
}

Index.layout = (page: React.ReactNode) => <DashboardLayout>{page}</DashboardLayout>;

export default Index;
