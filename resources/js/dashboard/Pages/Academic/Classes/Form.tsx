import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Input } from '@dashboard/Components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@dashboard/Components/ui/select';
import { Switch } from '@dashboard/Components/ui/switch';
import { Btn } from '@dashboard/Components/ui/btn';
import { ArrowLeft, Save } from 'lucide-react';

interface AcademicYear {
    id: string;
    nama: string;
    is_active: boolean;
}

interface EducationLevel {
    id: string;
    nama: string;
}

interface Teacher {
    id: string;
    nama_lengkap: string;
}

interface ClassData {
    id: string;
    name: string;
    academic_year_id: string;
    education_level_id: string | null;
    homeroom_teacher_id: string | null;
    capacity: number;
    is_active: boolean;
}

interface Props {
    class?: ClassData;
    academicYears: AcademicYear[];
    educationLevels: EducationLevel[];
    teachers: Teacher[];
}

function Form({ class: classData, academicYears, educationLevels, teachers }: Props) {
    const isEditing = !!classData;
    const [name, setName] = useState(classData?.name || '');
    const [academicYearId, setAcademicYearId] = useState(classData?.academic_year_id || '');
    const [educationLevelId, setEducationLevelId] = useState(classData?.education_level_id || '');
    const [homeroomTeacherId, setHomeroomTeacherId] = useState(classData?.homeroom_teacher_id || '');
    const [capacity, setCapacity] = useState(classData?.capacity ?? 0);
    const [isActive, setIsActive] = useState(classData?.is_active ?? true);
    const [saving, setSaving] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSaving(true);

        const data = {
            name,
            academic_year_id: academicYearId,
            education_level_id: educationLevelId || null,
            homeroom_teacher_id: homeroomTeacherId || null,
            capacity,
            is_active: isActive,
        };

        const url = isEditing ? `/academic/classes/${classData.id}` : '/academic/classes';
        const method = isEditing ? 'put' : 'post';

        router[method](url, data, {
            onSuccess: () => setSaving(false),
            onError: (err) => {
                setErrors(err);
                setSaving(false);
            },
        });
    };

    return (
        <>
            <Head title={isEditing ? 'Edit Kelas' : 'Tambah Kelas'} />

            <div className="max-w-2xl mx-auto">
                <Link
                    href="/academic/classes"
                    className="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground transition-colors mb-6"
                >
                    <ArrowLeft className="w-4 h-4" />
                    Kembali
                </Link>

                <h1 className="text-2xl font-bold mb-1">
                    {isEditing ? 'Edit Kelas' : 'Tambah Kelas'}
                </h1>
                <p className="text-muted-foreground text-sm mb-8">
                    {isEditing
                        ? 'Ubah informasi kelas'
                        : 'Buat kelas baru untuk akademik'}
                </p>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-background rounded-xl border border-border-subtle p-6 space-y-5">
                        <div>
                            <label className="block text-sm font-medium mb-1.5">
                                Nama Kelas
                            </label>
                            <Input
                                value={name}
                                onChange={(e) => setName(e.target.value)}
                                placeholder="Contoh: 7A"
                                className={errors.name ? 'border-destructive' : ''}
                            />
                            {errors.name && (
                                <p className="text-xs text-destructive mt-1">{errors.name}</p>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium mb-1.5">
                                Tahun Ajaran
                            </label>
                            <Select value={academicYearId} onValueChange={setAcademicYearId}>
                                <SelectTrigger className={errors.academic_year_id ? 'border-destructive' : ''}>
                                    <SelectValue placeholder="Pilih tahun ajaran" />
                                </SelectTrigger>
                                <SelectContent>
                                    {academicYears.map((year) => (
                                        <SelectItem key={year.id} value={year.id}>
                                            {year.nama}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.academic_year_id && (
                                <p className="text-xs text-destructive mt-1">{errors.academic_year_id}</p>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium mb-1.5">
                                Tingkat
                            </label>
                            <Select value={educationLevelId} onValueChange={setEducationLevelId}>
                                <SelectTrigger className={errors.education_level_id ? 'border-destructive' : ''}>
                                    <SelectValue placeholder="Pilih tingkat" />
                                </SelectTrigger>
                                <SelectContent>
                                    {educationLevels.map((level) => (
                                        <SelectItem key={level.id} value={level.id}>
                                            {level.nama}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.education_level_id && (
                                <p className="text-xs text-destructive mt-1">{errors.education_level_id}</p>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium mb-1.5">
                                Wali Kelas
                            </label>
                            <Select value={homeroomTeacherId} onValueChange={setHomeroomTeacherId}>
                                <SelectTrigger className={errors.homeroom_teacher_id ? 'border-destructive' : ''}>
                                    <SelectValue placeholder="Pilih wali kelas" />
                                </SelectTrigger>
                                <SelectContent>
                                    {teachers.map((teacher) => (
                                        <SelectItem key={teacher.id} value={teacher.id}>
                                            {teacher.nama_lengkap}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.homeroom_teacher_id && (
                                <p className="text-xs text-destructive mt-1">{errors.homeroom_teacher_id}</p>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium mb-1.5">
                                Kapasitas
                            </label>
                            <Input
                                type="number"
                                value={capacity}
                                onChange={(e) => setCapacity(Number(e.target.value))}
                                placeholder="Contoh: 30"
                                className={errors.capacity ? 'border-destructive' : ''}
                            />
                            {errors.capacity && (
                                <p className="text-xs text-destructive mt-1">{errors.capacity}</p>
                            )}
                        </div>

                        <div className="flex items-center justify-between">
                            <div>
                                <label className="text-sm font-medium">Aktif</label>
                                <p className="text-xs text-muted-foreground mt-0.5">
                                    Kelas yang aktif dapat digunakan dalam proses belajar mengajar
                                </p>
                            </div>
                            <Switch checked={isActive} onCheckedChange={setIsActive} />
                        </div>
                    </div>

                    <div className="flex items-center justify-end gap-3">
                        <Link href="/academic/classes">
                            <Btn variant="outline" type="button">Batal</Btn>
                        </Link>
                        <Btn variant="primary" type="submit" disabled={saving}>
                            <Save className="w-4 h-4 mr-2" />
                            {saving ? 'Menyimpan...' : 'Simpan'}
                        </Btn>
                    </div>
                </form>
            </div>
        </>
    );
}

Form.layout = (page: React.ReactNode) => <DashboardLayout>{page}</DashboardLayout>;

export default Form;
