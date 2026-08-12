import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Plus, Pencil, Trash2, GraduationCap, User } from 'lucide-react';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import { Btn } from '@dashboard/Components/ui/btn';

interface PersonData {
    id: string;
    nama_lengkap: string;
    nik: string;
}

interface StudentData {
    id: string;
    nis: string;
    nisn: string | null;
    angkatan: string | null;
    status: string | null;
    person: PersonData | null;
}

interface Props {
    students: {
        data: StudentData[];
    };
}

function Index({ students }: Props) {
    const [deleteTarget, setDeleteTarget] = useState<{ id: string; nis: string } | null>(null);

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/academic/students/${deleteTarget.id}`, {
            onSuccess: () => setDeleteTarget(null),
        });
    };

    return (
        <DashboardLayout>
            <Head title="Siswa" />

            <div className="flex items-center justify-between mb-6">
                <div>
                    <h1 className="text-2xl font-bold">Siswa</h1>
                    <p className="text-muted-foreground text-sm mt-1">
                        Kelola data siswa
                    </p>
                </div>
                <Link href="/academic/students/create">
                    <Btn variant="primary">
                        <Plus className="w-4 h-4 mr-2" />
                        Tambah Siswa
                    </Btn>
                </Link>
            </div>

            <div className="bg-background rounded-xl border border-border-subtle overflow-hidden">
                <table className="w-full">
                    <thead>
                        <tr className="border-b border-border-subtle bg-surface/50">
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">
                                NIS
                            </th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">
                                Nama
                            </th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">
                                NISN
                            </th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">
                                Angkatan
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
                        {students.data.length === 0 ? (
                            <tr>
                                <td colSpan={6} className="px-6 py-12 text-center text-muted-foreground">
                                    <GraduationCap className="w-10 h-10 mx-auto mb-3 opacity-40" />
                                    <p>Belum ada data siswa.</p>
                                    <Link href="/academic/students/create" className="text-primary text-sm mt-1 inline-block hover:underline">
                                        Tambah siswa baru
                                    </Link>
                                </td>
                            </tr>
                        ) : (
                            students.data.map((student) => (
                                <tr key={student.id} className="border-b border-border-subtle last:border-0 hover:bg-surface/30 transition-colors">
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-3">
                                            <div className="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center">
                                                <User className="w-4 h-4 text-primary" />
                                            </div>
                                            <p className="font-medium text-sm">{student.nis}</p>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <p className="font-medium text-sm">{student.person?.nama_lengkap || '-'}</p>
                                        {student.person?.nik && (
                                            <p className="text-xs text-muted-foreground">NIK: {student.person.nik}</p>
                                        )}
                                    </td>
                                    <td className="px-6 py-4 text-sm text-muted-foreground">
                                        {student.nisn || '-'}
                                    </td>
                                    <td className="px-6 py-4 text-sm text-muted-foreground">
                                        {student.angkatan || '-'}
                                    </td>
                                    <td className="px-6 py-4">
                                        {student.status ? (
                                            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary border border-primary/20">
                                                {student.status}
                                            </span>
                                        ) : (
                                            <span className="text-xs text-muted-foreground">-</span>
                                        )}
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <div className="flex items-center justify-end gap-1">
                                            <Link
                                                href={`/academic/students/${student.id}/edit`}
                                                className="p-2 rounded-lg text-muted-foreground hover:bg-surface-muted hover:text-foreground transition-colors"
                                            >
                                                <Pencil className="w-4 h-4" />
                                            </Link>
                                            <button
                                                onClick={() => setDeleteTarget({ id: student.id, nis: student.nis })}
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
                title="Hapus Siswa"
                message={`Apakah Anda yakin ingin menghapus siswa dengan NIS "${deleteTarget?.nis}"? Data terkait seperti enrollment juga akan terhapus.`}
            />
        </DashboardLayout>
    );
}

export default Index;
