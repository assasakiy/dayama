import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Plus, Pencil, Trash2, Library, CheckCircle2, XCircle } from 'lucide-react';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import { Btn } from '@dashboard/Components/ui/btn';

interface Semester {
    id: string;
    academic_year_id: string;
    name: string;
    start_date: string;
    end_date: string;
    is_active: boolean;
    academic_year: { id: string; nama: string };
}

interface Props {
    semesters: Semester[];
}

function Index({ semesters }: Props) {
    const [deleteTarget, setDeleteTarget] = useState<{ id: string; name: string } | null>(null);

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/academic/semesters/${deleteTarget.id}`, {
            onSuccess: () => setDeleteTarget(null),
        });
    };

    const formatDate = (date: string) => {
        return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    };

    return (
        <>
            <Head title="Semester" />

            <div className="flex items-center justify-between mb-6">
                <div>
                    <h1 className="text-2xl font-bold">Semester</h1>
                    <p className="text-muted-foreground text-sm mt-1">Kelola semester akademik</p>
                </div>
                <Link href="/academic/semesters/create">
                    <Btn variant="primary">
                        <Plus className="w-4 h-4 mr-2" />
                        Tambah Semester
                    </Btn>
                </Link>
            </div>

            <div className="bg-background rounded-xl border border-border-subtle overflow-hidden">
                <table className="w-full">
                    <thead>
                        <tr className="border-b border-border-subtle bg-surface/50">
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Semester</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Tahun Ajaran</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Periode</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Status</th>
                            <th className="text-right px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {semesters.length === 0 ? (
                            <tr>
                                <td colSpan={5} className="px-6 py-12 text-center text-muted-foreground">
                                    <Library className="w-10 h-10 mx-auto mb-3 opacity-40" />
                                    <p>Belum ada semester.</p>
                                    <Link href="/academic/semesters/create" className="text-primary text-sm mt-1 inline-block hover:underline">
                                        Tambah semester baru
                                    </Link>
                                </td>
                            </tr>
                        ) : (
                            semesters.map((sem) => (
                                <tr key={sem.id} className="border-b border-border-subtle last:border-0 hover:bg-surface/30 transition-colors">
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-3">
                                            <div className="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center">
                                                <Library className="w-4 h-4 text-primary" />
                                            </div>
                                            <span className="font-medium text-sm">{sem.name}</span>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 text-sm text-muted-foreground">{sem.academic_year?.nama}</td>
                                    <td className="px-6 py-4 text-sm text-muted-foreground">
                                        {formatDate(sem.start_date)} — {formatDate(sem.end_date)}
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium ${
                                            sem.is_active
                                                ? 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-950/40 dark:text-green-300'
                                                : 'bg-gray-50 text-gray-500 border border-gray-200 dark:bg-gray-900/40 dark:text-gray-400'
                                        }`}>
                                            {sem.is_active ? <CheckCircle2 className="w-3 h-3" /> : <XCircle className="w-3 h-3" />}
                                            {sem.is_active ? 'Aktif' : 'Tidak Aktif'}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <div className="flex items-center justify-end gap-1">
                                            <Link href={`/academic/semesters/${sem.id}/edit`} className="p-2 rounded-lg text-muted-foreground hover:bg-surface-muted hover:text-foreground transition-colors">
                                                <Pencil className="w-4 h-4" />
                                            </Link>
                                            <button onClick={() => setDeleteTarget({ id: sem.id, name: sem.name })} className="p-2 rounded-lg text-muted-foreground hover:bg-destructive/10 hover:text-destructive transition-colors">
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
                title="Hapus Semester"
                description={`Apakah Anda yakin ingin menghapus semester "${deleteTarget?.name}"?`}
            />
        </>
    );
}

Index.layout = (page: React.ReactNode) => <DashboardLayout>{page}</DashboardLayout>;

export default Index;
