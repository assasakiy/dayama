import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Input } from '@dashboard/Components/ui/input';
import { Btn } from '@dashboard/Components/ui/btn';
import { ArrowLeft, Save } from 'lucide-react';

interface Subject {
    id: string;
    nama: string;
    kode: string | null;
}

interface Props {
    subject?: Subject;
}

function Form({ subject }: Props) {
    const isEditing = !!subject;
    const [nama, setNama] = useState(subject?.nama || '');
    const [kode, setKode] = useState(subject?.kode || '');
    const [saving, setSaving] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        setSaving(true);

        const data = { nama, kode };
        const url = isEditing ? `/academic/subjects/${subject.id}` : '/academic/subjects';
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
            <Head title={isEditing ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran'} />

            <div className="max-w-2xl mx-auto">
                <Link
                    href="/academic/subjects"
                    className="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground transition-colors mb-6"
                >
                    <ArrowLeft className="w-4 h-4" />
                    Kembali
                </Link>

                <h1 className="text-2xl font-bold mb-1">
                    {isEditing ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran'}
                </h1>
                <p className="text-muted-foreground text-sm mb-8">
                    {isEditing
                        ? 'Ubah informasi mata pelajaran'
                        : 'Buat mata pelajaran baru'}
                </p>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="bg-background rounded-xl border border-border-subtle p-6 space-y-5">
                        <div>
                            <label className="block text-sm font-medium mb-1.5">
                                Nama Mata Pelajaran
                            </label>
                            <Input
                                value={nama}
                                onChange={(e) => setNama(e.target.value)}
                                placeholder="Contoh: Matematika"
                                className={errors.nama ? 'border-destructive' : ''}
                            />
                            {errors.nama && (
                                <p className="text-xs text-destructive mt-1">{errors.nama}</p>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium mb-1.5">
                                Kode
                            </label>
                            <Input
                                value={kode}
                                onChange={(e) => setKode(e.target.value)}
                                placeholder="Contoh: MTK"
                                className={errors.kode ? 'border-destructive' : ''}
                            />
                            {errors.kode && (
                                <p className="text-xs text-destructive mt-1">{errors.kode}</p>
                            )}
                        </div>
                    </div>

                    <div className="flex items-center justify-end gap-3">
                        <Link href="/academic/subjects">
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
