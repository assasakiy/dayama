import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Plus, Pencil, Trash2, Users, Eye } from 'lucide-react';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import { Btn } from '@dashboard/Components/ui/btn';

interface WaliKelas {
    id: string;
    nama_lengkap: string;
}

interface AcademicYear {
    id: string;
    nama: string;
}

interface RombelItem {
    id: string;
    nama: string;
    tingkat: string | null;
    academic_year: AcademicYear | null;
    wali_kelas: WaliKelas | null;
    students_count: number;
    created_at: string;
}

interface Props {
    rombel: RombelItem[];
}

function Index({ rombel }: Props) {
    const [deleteTarget, setDeleteTarget] = useState<{ id: string; nama: string } | null>(null);

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/academic/rombel/${deleteTarget.id}`, {
            onSuccess: () => setDeleteTarget(null),
        });
    };

    return (
        <>
            <Head title="Rombel" />

            <div className="flex items-center justify-between mb-6">
                <div>
                    <h1 className="text-2xl font-bold">Rombel</h1>
                    <p className="text-muted-foreground text-sm mt-1">
                        Kelola rombongan belajar
                    </p>
                </div>
                <Link href="/academic/rombel/create">
                    <Btn variant="primary">
                        <Plus className="w-4 h-4 mr-2" />
                        Tambah Rombel
                    </Btn>
                </Link>
            </div>

            <div className="bg-background rounded-xl border border-border-subtle overflow-hidden">
                <table className="w-full">
                    <thead>
                        <tr className="border-b border-border-subtle bg-surface/50">
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Nama Rombel</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Tingkat</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Tahun Ajaran</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Wali Kelas</th>
                            <th className="text-center px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Jml Siswa</th>
                            <th className="text-right px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {rombel.length === 0 ? (
                            <tr>
                                <td colSpan={6} className="px-6 py-12 text-center text-muted-foreground">
                                    <Users className="w-10 h-10 mx-auto mb-3 opacity-40" />
                                    <p>Belum ada rombel.</p>
                                    <Link href="/academic/rombel/create" className="text-primary text-sm mt-1 inline-block hover:underline">
                                        Tambah rombel baru
                                    </Link>
                                </td>
                            </tr>
                        ) : (
                            rombel.map((r) => (
                                <tr key={r.id} className="border-b border-border-subtle last:border-0 hover:bg-surface/30 transition-colors">
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-3">
                                            <div className="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center">
                                                <Users className="w-4 h-4 text-primary" />
                                            </div>
                                            <div>
                                                <p className="font-medium text-sm">{r.nama}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 text-sm">{r.tingkat || '-'}</td>
                                    <td className="px-6 py-4 text-sm">{r.academic_year?.nama || '-'}</td>
                                    <td className="px-6 py-4 text-sm">{r.wali_kelas?.nama_lengkap || '-'}</td>
                                    <td className="px-6 py-4 text-center text-sm font-medium">{r.students_count}</td>
                                    <td className="px-6 py-4 text-right">
                                        <div className="flex items-center justify-end gap-1">
                                            <Link
                                                href={`/academic/rombel/${r.id}`}
                                                className="p-2 rounded-lg text-muted-foreground hover:bg-surface-muted hover:text-foreground transition-colors"
                                            >
                                                <Eye className="w-4 h-4" />
                                            </Link>
                                            <Link
                                                href={`/academic/rombel/${r.id}/edit`}
                                                className="p-2 rounded-lg text-muted-foreground hover:bg-surface-muted hover:text-foreground transition-colors"
                                            >
                                                <Pencil className="w-4 h-4" />
                                            </Link>
                                            <button
                                                onClick={() => setDeleteTarget({ id: r.id, nama: r.nama })}
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
                title="Hapus Rombel"
                description={`Apakah Anda yakin ingin menghapus rombel "${deleteTarget?.nama}"?`}
            />
        </>
    );
}

Index.layout = (page: React.ReactNode) => <DashboardLayout>{page}</DashboardLayout>;

export default Index;
