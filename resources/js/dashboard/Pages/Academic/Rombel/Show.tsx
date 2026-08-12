import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@dashboard/Components/ui/tabs';
import { Btn } from '@dashboard/Components/ui/btn';
import { Input } from '@dashboard/Components/ui/input';
import { ArrowLeft, Plus, Trash2, UserCheck, BookOpen, Info, Users } from 'lucide-react';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';

interface Person {
    id: string;
    nama_lengkap: string;
}

interface Subject {
    id: string;
    nama: string;
    kode: string | null;
}

interface AcademicYear {
    id: string;
    nama: string;
}

interface Student {
    id: string;
    nis: string;
    nisn: string | null;
    person: Person | null;
}

interface AvailableStudent {
    id: string;
    nis: string;
    person_name: string;
}

interface TeachingAssignment {
    id: string;
    teacher: Person | null;
    subject: Subject | null;
    jam_per_minggu: number | null;
}

interface RombelDetail {
    id: string;
    nama: string;
    tingkat: string | null;
    academic_year: AcademicYear | null;
    wali_kelas: Person | null;
}

interface Props {
    rombel: RombelDetail;
    students: Student[];
    availableStudents: AvailableStudent[];
    teachingAssignments: TeachingAssignment[];
    teachers: Person[];
    subjects: Subject[];
}

function Show({ rombel, students, availableStudents, teachingAssignments, teachers, subjects }: Props) {
    const [deleteTarget, setDeleteTarget] = useState<{ type: 'student' | 'assignment'; id: string; label: string } | null>(null);
    const [showAddStudent, setShowAddStudent] = useState(false);
    const [selectedStudentId, setSelectedStudentId] = useState('');
    const [showAddAssignment, setShowAddAssignment] = useState(false);
    const [assignmentPersonId, setAssignmentPersonId] = useState('');
    const [assignmentSubjectId, setAssignmentSubjectId] = useState('');
    const [assignmentJam, setAssignmentJam] = useState('');

    const addStudent = () => {
        if (!selectedStudentId) return;
        router.post(`/academic/rombel/${rombel.id}/students`, { student_id: selectedStudentId }, {
            onSuccess: () => {
                setSelectedStudentId('');
                setShowAddStudent(false);
            },
        });
    };

    const removeStudent = () => {
        if (!deleteTarget || deleteTarget.type !== 'student') return;
        router.delete(`/academic/rombel/${rombel.id}/students/${deleteTarget.id}`, {
            onSuccess: () => setDeleteTarget(null),
        });
    };

    const addAssignment = () => {
        if (!assignmentPersonId || !assignmentSubjectId) return;
        router.post(`/academic/rombel/${rombel.id}/teaching-assignments`, {
            person_id: assignmentPersonId,
            subject_id: assignmentSubjectId,
            jam_per_minggu: assignmentJam ? parseInt(assignmentJam) : null,
        }, {
            onSuccess: () => {
                setAssignmentPersonId('');
                setAssignmentSubjectId('');
                setAssignmentJam('');
                setShowAddAssignment(false);
            },
        });
    };

    const removeAssignment = () => {
        if (!deleteTarget || deleteTarget.type !== 'assignment') return;
        router.delete(`/academic/rombel/${rombel.id}/teaching-assignments/${deleteTarget.id}`, {
            onSuccess: () => setDeleteTarget(null),
        });
    };

    return (
        <>
            <Head title={rombel.nama} />

            <div className="max-w-5xl mx-auto">
                <Link
                    href="/academic/rombel"
                    className="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground transition-colors mb-6"
                >
                    <ArrowLeft className="w-4 h-4" />
                    Kembali ke daftar rombel
                </Link>

                <h1 className="text-2xl font-bold mb-6">{rombel.nama}</h1>

                <Tabs defaultValue="students">
                    <TabsList className="mb-6">
                        <TabsTrigger value="students" className="flex items-center gap-2">
                            <Users className="w-4 h-4" />
                            Anggota Rombel
                        </TabsTrigger>
                        <TabsTrigger value="teaching" className="flex items-center gap-2">
                            <BookOpen className="w-4 h-4" />
                            Guru & Mapel
                        </TabsTrigger>
                        <TabsTrigger value="info" className="flex items-center gap-2">
                            <Info className="w-4 h-4" />
                            Informasi
                        </TabsTrigger>
                    </TabsList>

                    {/* Tab 1: Students */}
                    <TabsContent value="students">
                        <div className="flex items-center justify-between mb-4">
                            <p className="text-sm text-muted-foreground">
                                {students.length} siswa terdaftar
                            </p>
                            <Btn variant="primary" size="sm" onClick={() => setShowAddStudent(!showAddStudent)}>
                                <Plus className="w-4 h-4 mr-2" />
                                Tambah Siswa
                            </Btn>
                        </div>

                        {showAddStudent && (
                            <div className="bg-background rounded-xl border border-border-subtle p-4 mb-4 flex items-end gap-3">
                                <div className="flex-1">
                                    <label className="block text-sm font-medium mb-1.5">Pilih Siswa</label>
                                    <select
                                        value={selectedStudentId}
                                        onChange={(e) => setSelectedStudentId(e.target.value)}
                                        className="flex h-9 w-full rounded-md border border-border-subtle bg-background px-3 py-1.5 text-sm"
                                    >
                                        <option value="">Pilih Siswa</option>
                                        {availableStudents.map((s) => (
                                            <option key={s.id} value={s.id}>
                                                {s.person_name} ({s.nis})
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <Btn variant="primary" size="sm" onClick={addStudent}>Tambah</Btn>
                            </div>
                        )}

                        <div className="bg-background rounded-xl border border-border-subtle overflow-hidden">
                            <table className="w-full">
                                <thead>
                                    <tr className="border-b border-border-subtle bg-surface/50">
                                        <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">NIS</th>
                                        <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Nama</th>
                                        <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">NISN</th>
                                        <th className="text-right px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {students.length === 0 ? (
                                        <tr>
                                            <td colSpan={4} className="px-6 py-12 text-center text-muted-foreground">
                                                <UserCheck className="w-10 h-10 mx-auto mb-3 opacity-40" />
                                                <p>Belum ada siswa di rombel ini.</p>
                                            </td>
                                        </tr>
                                    ) : (
                                        students.map((s) => (
                                            <tr key={s.id} className="border-b border-border-subtle last:border-0 hover:bg-surface/30 transition-colors">
                                                <td className="px-6 py-4 text-sm font-medium">{s.nis}</td>
                                                <td className="px-6 py-4 text-sm">{s.person?.nama_lengkap || '-'}</td>
                                                <td className="px-6 py-4 text-sm">{s.nisn || '-'}</td>
                                                <td className="px-6 py-4 text-right">
                                                    <button
                                                        onClick={() => setDeleteTarget({ type: 'student', id: s.id, label: s.person?.nama_lengkap || s.nis })}
                                                        className="p-2 rounded-lg text-muted-foreground hover:bg-destructive/10 hover:text-destructive transition-colors"
                                                    >
                                                        <Trash2 className="w-4 h-4" />
                                                    </button>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </TabsContent>

                    {/* Tab 2: Teaching Assignments */}
                    <TabsContent value="teaching">
                        <div className="bg-background rounded-xl border border-border-subtle p-4 mb-4">
                            <p className="text-sm font-medium mb-1">Wali Kelas</p>
                            <p className="text-muted-foreground">{rombel.wali_kelas?.nama_lengkap || 'Belum ditentukan'}</p>
                        </div>

                        <div className="flex items-center justify-between mb-4">
                            <p className="text-sm text-muted-foreground">
                                {teachingAssignments.length} penugasan mengajar
                            </p>
                            <Btn variant="primary" size="sm" onClick={() => setShowAddAssignment(!showAddAssignment)}>
                                <Plus className="w-4 h-4 mr-2" />
                                Tambah Penugasan
                            </Btn>
                        </div>

                        {showAddAssignment && (
                            <div className="bg-background rounded-xl border border-border-subtle p-4 mb-4 space-y-3">
                                <div className="grid grid-cols-3 gap-3">
                                    <div>
                                        <label className="block text-sm font-medium mb-1.5">Guru</label>
                                        <select
                                            value={assignmentPersonId}
                                            onChange={(e) => setAssignmentPersonId(e.target.value)}
                                            className="flex h-9 w-full rounded-md border border-border-subtle bg-background px-3 py-1.5 text-sm"
                                        >
                                            <option value="">Pilih Guru</option>
                                            {teachers.map((t) => (
                                                <option key={t.id} value={t.id}>{t.nama_lengkap}</option>
                                            ))}
                                        </select>
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium mb-1.5">Mata Pelajaran</label>
                                        <select
                                            value={assignmentSubjectId}
                                            onChange={(e) => setAssignmentSubjectId(e.target.value)}
                                            className="flex h-9 w-full rounded-md border border-border-subtle bg-background px-3 py-1.5 text-sm"
                                        >
                                            <option value="">Pilih Mapel</option>
                                            {subjects.map((s) => (
                                                <option key={s.id} value={s.id}>{s.nama}</option>
                                            ))}
                                        </select>
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium mb-1.5">Jam/Minggu</label>
                                        <Input
                                            type="number"
                                            value={assignmentJam}
                                            onChange={(e) => setAssignmentJam(e.target.value)}
                                            placeholder="0"
                                            min={0}
                                        />
                                    </div>
                                </div>
                                <div className="flex justify-end">
                                    <Btn variant="primary" size="sm" onClick={addAssignment}>Simpan</Btn>
                                </div>
                            </div>
                        )}

                        <div className="bg-background rounded-xl border border-border-subtle overflow-hidden">
                            <table className="w-full">
                                <thead>
                                    <tr className="border-b border-border-subtle bg-surface/50">
                                        <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Guru</th>
                                        <th className="text-left px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Mata Pelajaran</th>
                                        <th className="text-center px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Jam/Minggu</th>
                                        <th className="text-right px-6 py-3 text-xs font-semibold uppercase tracking-widest text-muted-foreground/60">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {teachingAssignments.length === 0 ? (
                                        <tr>
                                            <td colSpan={4} className="px-6 py-12 text-center text-muted-foreground">
                                                <BookOpen className="w-10 h-10 mx-auto mb-3 opacity-40" />
                                                <p>Belum ada penugasan mengajar.</p>
                                            </td>
                                        </tr>
                                    ) : (
                                        teachingAssignments.map((ta) => (
                                            <tr key={ta.id} className="border-b border-border-subtle last:border-0 hover:bg-surface/30 transition-colors">
                                                <td className="px-6 py-4 text-sm">{ta.teacher?.nama_lengkap || '-'}</td>
                                                <td className="px-6 py-4 text-sm">{ta.subject?.nama || '-'}</td>
                                                <td className="px-6 py-4 text-center text-sm">{ta.jam_per_minggu ?? '-'}</td>
                                                <td className="px-6 py-4 text-right">
                                                    <button
                                                        onClick={() => setDeleteTarget({ type: 'assignment', id: ta.id, label: `${ta.subject?.nama || ''} - ${ta.teacher?.nama_lengkap || ''}` })}
                                                        className="p-2 rounded-lg text-muted-foreground hover:bg-destructive/10 hover:text-destructive transition-colors"
                                                    >
                                                        <Trash2 className="w-4 h-4" />
                                                    </button>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </TabsContent>

                    {/* Tab 3: Info */}
                    <TabsContent value="info">
                        <div className="bg-background rounded-xl border border-border-subtle p-6 space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <p className="text-xs font-semibold uppercase tracking-widest text-muted-foreground/60 mb-1">Nama Rombel</p>
                                    <p className="font-medium">{rombel.nama}</p>
                                </div>
                                <div>
                                    <p className="text-xs font-semibold uppercase tracking-widest text-muted-foreground/60 mb-1">Tingkat</p>
                                    <p>{rombel.tingkat || '-'}</p>
                                </div>
                                <div>
                                    <p className="text-xs font-semibold uppercase tracking-widest text-muted-foreground/60 mb-1">Tahun Ajaran</p>
                                    <p>{rombel.academic_year?.nama || '-'}</p>
                                </div>
                                <div>
                                    <p className="text-xs font-semibold uppercase tracking-widest text-muted-foreground/60 mb-1">Wali Kelas</p>
                                    <p>{rombel.wali_kelas?.nama_lengkap || '-'}</p>
                                </div>
                                <div>
                                    <p className="text-xs font-semibold uppercase tracking-widest text-muted-foreground/60 mb-1">Jumlah Siswa</p>
                                    <p>{students.length}</p>
                                </div>
                            </div>
                            <div className="pt-4">
                                <Link href={`/academic/rombel/${rombel.id}/edit`}>
                                    <Btn variant="outline">Edit Rombel</Btn>
                                </Link>
                            </div>
                        </div>
                    </TabsContent>
                </Tabs>
            </div>

            <ConfirmDialog
                open={!!deleteTarget}
                onOpenChange={() => setDeleteTarget(null)}
                onConfirm={deleteTarget?.type === 'student' ? removeStudent : removeAssignment}
                title={deleteTarget?.type === 'student' ? 'Hapus Siswa' : 'Hapus Penugasan'}
                description={
                    deleteTarget?.type === 'student'
                        ? `Hapus ${deleteTarget?.label} dari rombel ini?`
                        : `Hapus penugasan ${deleteTarget?.label}?`
                }
            />
        </>
    );
}

Show.layout = (page: React.ReactNode) => <DashboardLayout>{page}</DashboardLayout>;

export default Show;
