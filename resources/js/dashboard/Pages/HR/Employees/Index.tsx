import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Plus, Pencil, Trash2, Users } from 'lucide-react';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import { Btn } from '@dashboard/Components/ui/btn';

interface Employee {
    id: string;
    nip: string | null;
    nuptk: string | null;
    sudah_sertifikasi: boolean;
    person: { id: string; nama_lengkap: string } | null;
    employment_status: { id: string; nama: string } | null;
}

interface Props {
    employees: Employee[];
}

function Index({ employees }: Props) {
    const [deleteTarget, setDeleteTarget] = useState<{ id: string; nama: string } | null>(null);

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/hr/employees/${deleteTarget.id}`, {
            onSuccess: () => setDeleteTarget(null),
        });
    };

    return (
        <DashboardLayout>
            <Head title="Guru & Staf" />

            <div className="flex items-center justify-between mb-6">
                <div>
                    <h1 className="text-2xl font-bold">Guru & Staf</h1>
                    <p className="text-muted-foreground text-sm mt-1">
                        Kelola data guru dan staf
                    </p>
                </div>
                <Link href="/hr/employees/create">
                    <Btn variant="primary">
                        <Plus className="w-4 h-4 mr-2" />
                        Tambah
                    </Btn>
                </Link>
            </div>

            <div className="bg-background rounded-xl border border-border-subtle overflow-hidden">
                <table className="w-full">
                    <thead>
                        <tr className="border-b border-border-subtle bg-surface/50">
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">NIP</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Nama</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">NUPTK</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Status</th>
                            <th className="text-right px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {employees.length === 0 ? (
                            <tr>
                                <td colSpan={5} className="px-6 py-12 text-center text-muted-foreground">
                                    <Users className="w-10 h-10 mx-auto mb-3 opacity-40" />
                                    <p>Belum ada data employee.</p>
                                    <Link href="/hr/employees/create" className="text-primary text-sm mt-1 inline-block hover:underline">
                                        Tambah employee baru
                                    </Link>
                                </td>
                            </tr>
                        ) : (
                            employees.map((emp) => (
                                <tr key={emp.id} className="border-b border-border-subtle last:border-0 hover:bg-surface/30 transition-colors">
                                    <td className="px-6 py-4 text-sm">{emp.nip || '-'}</td>
                                    <td className="px-6 py-4 text-sm font-medium">{emp.person?.nama_lengkap ?? '-'}</td>
                                    <td className="px-6 py-4 text-sm text-muted-foreground">{emp.nuptk || '-'}</td>
                                    <td className="px-6 py-4">
                                        <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium ${emp.sudah_sertifikasi ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-50 text-gray-500 border border-gray-200'}`}>
                                            {emp.sudah_sertifikasi ? 'Tersertifikasi' : 'Belum Sertifikasi'}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <div className="flex items-center justify-end gap-1">
                                            <Link
                                                href={`/hr/employees/${emp.id}/edit`}
                                                className="p-2 rounded-lg text-muted-foreground hover:bg-surface-muted hover:text-foreground transition-colors"
                                            >
                                                <Pencil className="w-4 h-4" />
                                            </Link>
                                            <button
                                                onClick={() => setDeleteTarget({ id: emp.id, nama: emp.person?.nama_lengkap ?? emp.nip ?? '' })}
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
                title="Hapus Employee"
                message={`Apakah Anda yakin ingin menghapus employee "${deleteTarget?.nama}"?`}
            />
        </DashboardLayout>
    );
}

export default Index;
