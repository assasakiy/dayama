import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Plus, Pencil, Trash2, ClipboardCheck } from 'lucide-react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@dashboard/Components/ui/dialog';
import { Input } from '@dashboard/Components/ui/input';
import { Textarea } from '@dashboard/Components/ui/textarea';
import { Btn } from '@dashboard/Components/ui/btn';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';

interface Person {
    id: string;
    nama_lengkap: string;
}

interface Employee {
    id: string;
    nip: string | null;
    person: Person | null;
}

interface Attendance {
    id: string;
    employee_id?: string;
    date: string;
    check_in: string | null;
    check_out: string | null;
    status: string;
    notes: string | null;
    employee: Employee | null;
}

interface Props {
    attendances: Attendance[];
    employees: Employee[];
}

function Index({ attendances, employees }: Props) {
    const [deleteTarget, setDeleteTarget] = useState<{ id: string; name: string } | null>(null);
    const [showModal, setShowModal] = useState(false);
    const [editingAttendance, setEditingAttendance] = useState<Attendance | null>(null);
    const [employeeId, setEmployeeId] = useState('');
    const [date, setDate] = useState('');
    const [checkIn, setCheckIn] = useState('');
    const [checkOut, setCheckOut] = useState('');
    const [status, setStatus] = useState('');
    const [notes, setNotes] = useState('');
    const [saving, setSaving] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    const openCreate = () => {
        setEditingAttendance(null);
        setEmployeeId('');
        setDate('');
        setCheckIn('');
        setCheckOut('');
        setStatus('');
        setNotes('');
        setErrors({});
        setShowModal(true);
    };

    const openEdit = (att: Attendance) => {
        setEditingAttendance(att);
        setEmployeeId(att.employee_id || att.employee?.id || '');
        setDate(att.date || '');
        setCheckIn(att.check_in || '');
        setCheckOut(att.check_out || '');
        setStatus(att.status || '');
        setNotes(att.notes || '');
        setErrors({});
        setShowModal(true);
    };

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/hr/attendance/${deleteTarget.id}`, {
            onSuccess: () => setDeleteTarget(null),
        });
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSaving(true);

        const data = {
            employee_id: employeeId,
            date,
            check_in: checkIn || null,
            check_out: checkOut || null,
            status,
            notes: notes || null,
        };

        const url = editingAttendance ? `/hr/attendance/${editingAttendance.id}` : '/hr/attendance';
        const method = editingAttendance ? 'put' : 'post';

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

    const statusBadge = (s: string) => {
        const styles: Record<string, string> = {
            hadir: 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-950/40 dark:text-green-300',
            izin: 'bg-yellow-50 text-yellow-700 border border-yellow-200 dark:bg-yellow-950/40 dark:text-yellow-300',
            sakit: 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-950/40 dark:text-red-300',
            alpha: 'bg-gray-50 text-gray-500 border border-gray-200 dark:bg-gray-900/40 dark:text-gray-400',
            cuti: 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300',
        };
        const labels: Record<string, string> = {
            hadir: 'Hadir',
            izin: 'Izin',
            sakit: 'Sakit',
            alpha: 'Alpha',
            cuti: 'Cuti',
        };
        return (
            <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium ${styles[s] || ''}`}>
                {labels[s] || s}
            </span>
        );
    };

    return (
        <>
            <Head title="Presensi" />

            <div className="flex items-center justify-between mb-6">
                <div>
                    <h1 className="text-2xl font-bold">Presensi</h1>
                    <p className="text-muted-foreground text-sm mt-1">
                        Kelola kehadiran karyawan
                    </p>
                </div>
                <Btn variant="primary" onClick={openCreate}>
                    <Plus className="w-4 h-4 mr-2" />
                    Tambah Attendance
                </Btn>
            </div>

            <div className="bg-background rounded-xl border border-border-subtle overflow-hidden">
                <table className="w-full">
                    <thead>
                        <tr className="border-b border-border-subtle bg-surface/50">
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Tanggal</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Karyawan</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">NIP</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Check In</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Check Out</th>
                            <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Status</th>
                            <th className="text-right px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {attendances.length === 0 ? (
                            <tr>
                                <td colSpan={7} className="px-6 py-12 text-center text-muted-foreground">
                                    <ClipboardCheck className="w-10 h-10 mx-auto mb-3 opacity-40" />
                                    <p>Belum ada data attendance.</p>
                                    <button onClick={openCreate} className="text-primary text-sm mt-1 inline-block hover:underline">
                                        Tambah attendance baru
                                    </button>
                                </td>
                            </tr>
                        ) : (
                            attendances.map((att) => (
                                <tr key={att.id} className="border-b border-border-subtle last:border-0 hover:bg-surface/30 transition-colors">
                                    <td className="px-6 py-4">
                                        <p className="font-medium text-sm">
                                            {new Date(att.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                                        </p>
                                    </td>
                                    <td className="px-6 py-4">
                                        <p className="text-sm">{att.employee?.person?.nama_lengkap || '-'}</p>
                                    </td>
                                    <td className="px-6 py-4">
                                        <p className="text-sm text-muted-foreground">{att.employee?.nip || '-'}</p>
                                    </td>
                                    <td className="px-6 py-4">
                                        <p className="text-sm">{att.check_in || '-'}</p>
                                    </td>
                                    <td className="px-6 py-4">
                                        <p className="text-sm">{att.check_out || '-'}</p>
                                    </td>
                                    <td className="px-6 py-4">
                                        {statusBadge(att.status)}
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <div className="flex items-center justify-end gap-1">
                                            <button
                                                onClick={() => openEdit(att)}
                                                className="p-2 rounded-lg text-muted-foreground hover:bg-surface-muted hover:text-foreground transition-colors"
                                            >
                                                <Pencil className="w-4 h-4" />
                                            </button>
                                            <button
                                                onClick={() => setDeleteTarget({ id: att.id, name: att.employee?.person?.nama_lengkap || 'attendance' })}
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
                        <DialogTitle>{editingAttendance ? 'Edit Presensi' : 'Tambah Presensi'}</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={handleSubmit} className="px-6 py-4 space-y-5 overflow-y-auto">
                        <div>
                            <label className="block text-sm font-medium mb-1.5">
                                Karyawan <span className="text-destructive">*</span>
                            </label>
                            <select
                                value={employeeId}
                                onChange={(e) => setEmployeeId(e.target.value)}
                                className={`flex h-10 w-full rounded-lg border bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/20 focus-visible:border-primary disabled:cursor-not-allowed disabled:opacity-50 ${errors.employee_id ? 'border-destructive' : 'border-border-subtle'}`}
                            >
                                <option value="">Pilih karyawan</option>
                                {employees.map((emp) => (
                                    <option key={emp.id} value={emp.id}>
                                        {emp.person?.nama_lengkap} ({emp.nip})
                                    </option>
                                ))}
                            </select>
                            {errors.employee_id && <p className="text-xs text-destructive mt-1">{errors.employee_id}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium mb-1.5">
                                Tanggal <span className="text-destructive">*</span>
                            </label>
                            <Input
                                type="date"
                                value={date}
                                onChange={(e) => setDate(e.target.value)}
                                className={errors.date ? 'border-destructive' : ''}
                            />
                            {errors.date && <p className="text-xs text-destructive mt-1">{errors.date}</p>}
                        </div>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium mb-1.5">Check In</label>
                                <Input
                                    type="time"
                                    value={checkIn}
                                    onChange={(e) => setCheckIn(e.target.value)}
                                    className={errors.check_in ? 'border-destructive' : ''}
                                />
                                {errors.check_in && <p className="text-xs text-destructive mt-1">{errors.check_in}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-1.5">Check Out</label>
                                <Input
                                    type="time"
                                    value={checkOut}
                                    onChange={(e) => setCheckOut(e.target.value)}
                                    className={errors.check_out ? 'border-destructive' : ''}
                                />
                                {errors.check_out && <p className="text-xs text-destructive mt-1">{errors.check_out}</p>}
                            </div>
                        </div>
                        <div>
                            <label className="block text-sm font-medium mb-1.5">
                                Status <span className="text-destructive">*</span>
                            </label>
                            <select
                                value={status}
                                onChange={(e) => setStatus(e.target.value)}
                                className={`flex h-10 w-full rounded-lg border bg-background px-3 py-2 text-sm ${errors.status ? 'border-destructive' : 'border-border-subtle'}`}
                            >
                                <option value="">Pilih status</option>
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="alpha">Alpha</option>
                                <option value="cuti">Cuti</option>
                            </select>
                            {errors.status && <p className="text-xs text-destructive mt-1">{errors.status}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium mb-1.5">Catatan</label>
                            <Textarea
                                value={notes}
                                onChange={(e) => setNotes(e.target.value)}
                                placeholder="Catatan tambahan (opsional)"
                                className={errors.notes ? 'border-destructive' : ''}
                            />
                            {errors.notes && <p className="text-xs text-destructive mt-1">{errors.notes}</p>}
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
                title="Hapus Attendance"
                description={`Apakah Anda yakin ingin menghapus attendance ${deleteTarget?.name}?`}
            />
        </>
    );
}

Index.layout = (page: React.ReactNode) => <DashboardLayout>{page}</DashboardLayout>;

export default Index;
