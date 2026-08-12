import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Plus, Pencil, Trash2, Calendar, CheckCircle2, XCircle } from 'lucide-react';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import { Btn } from '@dashboard/Components/ui/btn';

interface AcademicYear {
    id: string;
    nama: string;
    is_active: boolean;
    created_at: string;
}

interface Props {
    years: AcademicYear[];
}

function Index({ years }: Props) {
    const [deleteTarget, setDeleteTarget] = useState<{ id: string; nama: string } | null>(null);

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/academic/years/${deleteTarget.id}`, {
            onSuccess: () => setDeleteTarget(null),
        });
    };

    const toggleActive = (year: AcademicYear) => {
        router.put(`/academic/years/${year.id}`, {
            nama: year.nama,
            is_active: !year.is_active,
        });
    };

    return (
        <>
            <Head title="Tahun Ajaran" />

            <div className="flex items-center justify-between mb-6">
                <div>
                    <h1 className="text-2xl font-bold">Tahun Ajaran</h1>
                    <p className="text-muted-foreground text-sm mt-1">
                        Kelola tahun ajaran akademik
                    </p>
                </div>
                <Link href="/academic/years/create">
                    <Btn variant="primary">
                        <Plus className="w-4 h-4 mr-2" />
                        Tambah Tahun Ajaran
                    </Btn>
                </Link>
            </div>

            <div className="bg-background rounded-xl border border-border-subtle overflow-hidden">
                <table className="w-full">
                    <thead>
                        <tr className="border-b border-border-subtle bg-surface/50">
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">
                                Tahun Ajaran
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
                        {years.length === 0 ? (
                            <tr>
                                <td colSpan={3} className="px-6 py-12 text-center text-muted-foreground">
                                    <Calendar className="w-10 h-10 mx-auto mb-3 opacity-40" />
                                    <p>Belum ada tahun ajaran.</p>
                                    <Link href="/academic/years/create" className="text-primary text-sm mt-1 inline-block hover:underline">
                                        Tambah tahun ajaran baru
                                    </Link>
                                </td>
                            </tr>
                        ) : (
                            years.map((year) => (
                                <tr key={year.id} className="border-b border-border-subtle last:border-0 hover:bg-surface/30 transition-colors">
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-3">
                                            <div className="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center">
                                                <Calendar className="w-4 h-4 text-primary" />
                                            </div>
                                            <div>
                                                <p className="font-medium text-sm">{year.nama}</p>
                                                <p className="text-xs text-muted-foreground">
                                                    Dibuat {new Date(year.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <button
                                            onClick={() => toggleActive(year)}
                                            className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium transition-colors ${
                                                year.is_active
                                                    ? 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-950/40 dark:text-green-300'
                                                    : 'bg-gray-50 text-gray-500 border border-gray-200 dark:bg-gray-900/40 dark:text-gray-400'
                                            }`}
                                        >
                                            {year.is_active ? (
                                                <CheckCircle2 className="w-3 h-3" />
                                            ) : (
                                                <XCircle className="w-3 h-3" />
                                            )}
                                            {year.is_active ? 'Aktif' : 'Tidak Aktif'}
                                        </button>
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <div className="flex items-center justify-end gap-1">
                                            <Link
                                                href={`/academic/years/${year.id}/edit`}
                                                className="p-2 rounded-lg text-muted-foreground hover:bg-surface-muted hover:text-foreground transition-colors"
                                            >
                                                <Pencil className="w-4 h-4" />
                                            </Link>
                                            <button
                                                onClick={() => setDeleteTarget({ id: year.id, nama: year.nama })}
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
                title="Hapus Tahun Ajaran"
                description={`Apakah Anda yakin ingin menghapus tahun ajaran "${deleteTarget?.nama}"? Data terkait seperti kelas dan rombel mungkin akan terpengaruh.`}
            />
        </>
    );
}

Index.layout = (page: React.ReactNode) => <DashboardLayout>{page}</DashboardLayout>;

export default Index;
