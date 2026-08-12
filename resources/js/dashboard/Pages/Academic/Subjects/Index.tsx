import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Plus, Pencil, Trash2, BookOpenCheck } from 'lucide-react';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import { Btn } from '@dashboard/Components/ui/btn';

interface Subject {
    id: string;
    nama: string;
    kode: string | null;
    created_at: string;
}

interface Props {
    subjects: Subject[];
}

function Index({ subjects }: Props) {
    const [deleteTarget, setDeleteTarget] = useState<{ id: string; nama: string } | null>(null);

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/academic/subjects/${deleteTarget.id}`, {
            onSuccess: () => setDeleteTarget(null),
        });
    };

    return (
        <>
            <Head title="Mata Pelajaran" />

            <div className="flex items-center justify-between mb-6">
                <div>
                    <h1 className="text-2xl font-bold">Mata Pelajaran</h1>
                    <p className="text-muted-foreground text-sm mt-1">
                        Kelola mata pelajaran akademik
                    </p>
                </div>
                <Link href="/academic/subjects/create">
                    <Btn variant="primary">
                        <Plus className="w-4 h-4 mr-2" />
                        Tambah Mata Pelajaran
                    </Btn>
                </Link>
            </div>

            <div className="bg-background rounded-xl border border-border-subtle overflow-hidden">
                <table className="w-full">
                    <thead>
                        <tr className="border-b border-border-subtle bg-surface/50">
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">
                                Mata Pelajaran
                            </th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">
                                Kode
                            </th>
                            <th className="text-right px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {subjects.length === 0 ? (
                            <tr>
                                <td colSpan={3} className="px-6 py-12 text-center text-muted-foreground">
                                    <BookOpenCheck className="w-10 h-10 mx-auto mb-3 opacity-40" />
                                    <p>Belum ada mata pelajaran.</p>
                                    <Link href="/academic/subjects/create" className="text-primary text-sm mt-1 inline-block hover:underline">
                                        Tambah mata pelajaran baru
                                    </Link>
                                </td>
                            </tr>
                        ) : (
                            subjects.map((subject) => (
                                <tr key={subject.id} className="border-b border-border-subtle last:border-0 hover:bg-surface/30 transition-colors">
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-3">
                                            <div className="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center">
                                                <BookOpenCheck className="w-4 h-4 text-primary" />
                                            </div>
                                            <div>
                                                <p className="font-medium text-sm">{subject.nama}</p>
                                                <p className="text-xs text-muted-foreground">
                                                    Dibuat {new Date(subject.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className="text-sm text-muted-foreground">
                                            {subject.kode || '-'}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <div className="flex items-center justify-end gap-1">
                                            <Link
                                                href={`/academic/subjects/${subject.id}/edit`}
                                                className="p-2 rounded-lg text-muted-foreground hover:bg-surface-muted hover:text-foreground transition-colors"
                                            >
                                                <Pencil className="w-4 h-4" />
                                            </Link>
                                            <button
                                                onClick={() => setDeleteTarget({ id: subject.id, nama: subject.nama })}
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
                title="Hapus Mata Pelajaran"
                description={`Apakah Anda yakin ingin menghapus mata pelajaran "${deleteTarget?.nama}"?`}
            />
        </>
    );
}

Index.layout = (page: React.ReactNode) => <DashboardLayout>{page}</DashboardLayout>;

export default Index;
