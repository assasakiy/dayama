import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Input } from '@dashboard/Components/ui/input';
import { Switch } from '@dashboard/Components/ui/switch';
import { Btn } from '@dashboard/Components/ui/btn';
import { Button } from '@dashboard/Components/ui/button';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@dashboard/Components/ui/tabs';
import { DuplicateNikDialog } from '@dashboard/Components/DuplicateNikDialog';
import { useNikDuplicateCheck } from '@dashboard/Hooks/useNikDuplicateCheck';
import { Save, Camera, Upload, ArrowLeft, Plus, Trash2 } from 'lucide-react';

// ── Types ──

interface PersonData {
    id: string;
    nama_lengkap: string;
    nik: string | null;
    gender: string | null;
    tempat_lahir: string | null;
    tanggal_lahir: string | null;
    agama: string | null;
    photo: string | null;
    contacts?: { id: string; contact_type_id: string; value: string; is_primary: boolean; type?: { id: string; nama: string } | null }[];
    addresses?: { id: string; alamat: string | null; provinsi: string | null; kabupaten_kota: string | null; kecamatan: string | null; desa_kelurahan: string | null; kode_pos: string | null; is_primary: boolean }[];
}

interface EmployeeData {
    id: string;
    person_id: string;
    nip: string | null;
    nuptk: string | null;
    employment_status_id: string | null;
    department_id: string | null;
    sudah_sertifikasi: boolean;
    nomor_sertifikat_pendidik: string | null;
    jam_mengajar_per_minggu: number | null;
    person: PersonData | null;
    employment_status: { id: string; nama: string } | null;
    department: { id: string; name: string } | null;
}

interface Option { id: string; nama?: string; name?: string }

interface Props {
    employee?: EmployeeData | null;
    employmentStatuses: Option[];
    departments?: Option[];
    contactTypes?: Option[];
    addressTypes?: Option[];
}

interface ContactRow { contact_type_id: string; value: string }

const AGAMA = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];

// ── Component ──

function Form({ employee, employmentStatuses, departments = [], contactTypes = [] }: Props) {
    const isEditing = !!employee;
    const person = isEditing ? employee?.person : null;
    const primaryAddress = person?.addresses?.find(a => a.is_primary) ?? person?.addresses?.[0];

    // Person state
    const [namaLengkap, setNamaLengkap] = useState(person?.nama_lengkap || '');
    const [nik, setNik] = useState(person?.nik || '');
    const [gender, setGender] = useState(person?.gender || '');
    const [tempatLahir, setTempatLahir] = useState(person?.tempat_lahir || '');
    const [tanggalLahir, setTanggalLahir] = useState(person?.tanggal_lahir || '');
    const [agama, setAgama] = useState(person?.agama || '');
    const [photo, setPhoto] = useState(person?.photo || '');

    // Employee state
    const [nip, setNip] = useState(employee?.nip || '');
    const [nuptk, setNuptk] = useState(employee?.nuptk || '');
    const [employmentStatusId, setEmploymentStatusId] = useState(employee?.employment_status_id || '');
    const [departmentId, setDepartmentId] = useState(employee?.department_id || '');
    const [sudahSertifikasi, setSudahSertifikasi] = useState(employee?.sudah_sertifikasi ?? false);
    const [nomorSertifikat, setNomorSertifikat] = useState(employee?.nomor_sertifikat_pendidik || '');
    const [jamMengajar, setJamMengajar] = useState(employee?.jam_mengajar_per_minggu?.toString() || '');

    // Contact state
    const [contacts, setContacts] = useState<ContactRow[]>(() => {
        const existing = person?.contacts?.map(c => ({ contact_type_id: c.contact_type_id, value: c.value })) || [];
        return existing.length > 0 ? existing : [{ contact_type_id: '', value: '' }];
    });

    // Address state
    const [alamat, setAlamat] = useState(primaryAddress?.alamat || '');
    const [provinsi, setProvinsi] = useState(primaryAddress?.provinsi || '');
    const [kabupatenKota, setKabupatenKota] = useState(primaryAddress?.kabupaten_kota || '');
    const [kecamatan, setKecamatan] = useState(primaryAddress?.kecamatan || '');
    const [desaKelurahan, setDesaKelurahan] = useState(primaryAddress?.desa_kelurahan || '');
    const [kodePos, setKodePos] = useState(primaryAddress?.kode_pos || '');

    const [saving, setSaving] = useState(false);
    const [errors, setErrors] = useState<Record<string, string>>({});

    // NIK duplicate check
    const [nikDialogOpen, setNikDialogOpen] = useState(false);
    const { duplicates, check, reset } = useNikDuplicateCheck();

    useEffect(() => {
        if (nik && nik.length >= 16) check(nik);
        else reset();
    }, [nik]);

    useEffect(() => {
        if (duplicates.length > 0) setNikDialogOpen(true);
    }, [duplicates]);

    // Contact helpers
    const addContact = () => setContacts(c => [...c, { contact_type_id: '', value: '' }]);
    const removeContact = (i: number) => setContacts(c => c.filter((_, idx) => idx !== i));
    const updateContact = (i: number, field: keyof ContactRow, val: string) =>
        setContacts(c => c.map((r, idx) => idx === i ? { ...r, [field]: val } : r));

    // Progress
    const filledCount = [namaLengkap, nip || nuptk, sudahSertifikasi ? 'y' : '', alamat].filter(Boolean).length;
    const progressPct = Math.round((filledCount / 4) * 100);

    const handleSubmit = (e?: React.FormEvent) => {
        e?.preventDefault();
        setSaving(true);

        const validContacts = contacts.filter(c => c.contact_type_id && c.value);

        const data: Record<string, any> = {
            // Person
            nama_lengkap: namaLengkap,
            nik: nik || null,
            gender: gender || null,
            tempat_lahir: tempatLahir || null,
            tanggal_lahir: tanggalLahir || null,
            agama: agama || null,
            // Employee
            nip: nip || null,
            nuptk: nuptk || null,
            employment_status_id: employmentStatusId || null,
            department_id: departmentId || null,
            sudah_sertifikasi: sudahSertifikasi,
            nomor_sertifikat_pendidik: nomorSertifikat || null,
            jam_mengajar_per_minggu: jamMengajar ? parseInt(jamMengajar, 10) : null,
            // Contacts
            contacts: validContacts,
            // Address
            alamat: alamat || null,
            provinsi: provinsi || null,
            kabupaten_kota: kabupatenKota || null,
            kecamatan: kecamatan || null,
            desa_kelurahan: desaKelurahan || null,
            kode_pos: kodePos || null,
        };

        const url = isEditing && employee ? `/hr/employees/${employee.id}` : '/hr/employees';
        const method = isEditing ? 'put' : 'post';

        router[method](url, data, {
            onSuccess: () => setSaving(false),
            onError: (err) => { setErrors(err); setSaving(false); },
        });
    };

    const photoSrc = isEditing ? person?.photo : photo;

    return (
        <DashboardLayout>
            <Head title={isEditing ? 'Edit Guru / Staf' : 'Tambah Guru / Staf'} />

            <div className="space-y-6 max-w-7xl mx-auto">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <Btn variant="ghost" size="sm" onClick={() => router.visit('/hr/employees')} icon={<ArrowLeft className="w-4 h-4" />} />
                        <div>
                            <h1 className="text-xl font-semibold tracking-tight">{isEditing ? 'Edit Guru / Staf' : 'Tambah Guru / Staf'}</h1>
                            <p className="text-sm text-muted-foreground">{isEditing ? 'Ubah data guru / staf' : 'Buat data guru / staf baru'}</p>
                        </div>
                    </div>
                    <Btn onClick={() => handleSubmit()} disabled={saving} loading={saving} icon={<Save className="w-4 h-4" />}>
                        {saving ? 'Menyimpan...' : 'Simpan'}
                    </Btn>
                </div>

                <div className="flex gap-6 items-start">
                    {/* Left — Tabs & Content */}
                    <div className="flex-1 min-w-0">
                        <Tabs defaultValue="kepegawaian" className="w-full">
                            <TabsList className="mb-5">
                                <TabsTrigger value="pribadi">Data Pribadi</TabsTrigger>
                                <TabsTrigger value="kepegawaian">Kepegawaian</TabsTrigger>
                                <TabsTrigger value="sertifikasi">Sertifikasi</TabsTrigger>
                                <TabsTrigger value="kontak">Kontak</TabsTrigger>
                                <TabsTrigger value="alamat">Alamat</TabsTrigger>
                            </TabsList>

                            <div className="rounded-xl border border-border-subtle bg-background p-6">
                                {/* ═══ DATA PRIBADI ═══ */}
                                <TabsContent value="pribadi" className="m-0 focus-visible:outline-none focus-visible:ring-0">
                                    <div className="grid grid-cols-2 gap-3.5">
                                        <div className="col-span-2">
                                            <Input label="Nama lengkap *" value={namaLengkap} onChange={e => setNamaLengkap(e.target.value)} placeholder="Nama lengkap sesuai KTP" error={errors.nama_lengkap} />
                                        </div>
                                        <Input label="NIK" value={nik} onChange={e => setNik(e.target.value)} placeholder="16 digit NIK (otomatis deteksi jika sudah terdaftar)" error={errors.nik} />
                                        <div>
                                            <label className="block text-xs text-muted-foreground mb-1">Jenis kelamin</label>
                                            <div className="flex gap-2">
                                                {[{ val: 'L', label: 'Laki-laki' }, { val: 'P', label: 'Perempuan' }].map(({ val, label }) => (
                                                    <button key={val} type="button" onClick={() => setGender(gender === val ? '' : val)}
                                                        className={`flex-1 h-9 rounded-md text-sm font-medium border transition-all ${gender === val ? 'bg-primary text-primary-foreground border-primary' : 'bg-background border-border-subtle text-muted-foreground hover:border-primary/50'}`}>
                                                        {label}
                                                    </button>
                                                ))}
                                            </div>
                                        </div>
                                        <Input label="Tempat lahir" value={tempatLahir} onChange={e => setTempatLahir(e.target.value)} placeholder="Kota / Kabupaten" />
                                        <div>
                                            <label className="block text-xs text-muted-foreground mb-1">Tanggal lahir</label>
                                            <input type="date" value={tanggalLahir} onChange={e => setTanggalLahir(e.target.value)}
                                                className="w-full h-9 px-3 text-sm rounded-md border border-border-subtle bg-background focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all" />
                                        </div>
                                        <div>
                                            <label className="block text-xs text-muted-foreground mb-1">Agama</label>
                                            <select value={agama} onChange={e => setAgama(e.target.value)}
                                                className="flex h-9 w-full rounded-md border border-border-subtle bg-background px-3 py-1.5 text-sm">
                                                <option value="">Pilih agama</option>
                                                {AGAMA.map(a => <option key={a} value={a}>{a}</option>)}
                                            </select>
                                        </div>
                                    </div>
                                </TabsContent>

                                {/* ═══ KEPEGAWAIAN ═══ */}
                                <TabsContent value="kepegawaian" className="m-0 focus-visible:outline-none focus-visible:ring-0">
                                    <div className="grid grid-cols-2 gap-3.5">
                                        <Input label="NIP" value={nip} onChange={e => setNip(e.target.value)} placeholder="Nomor Induk Pegawai" error={errors.nip} />
                                        <Input label="NUPTK" value={nuptk} onChange={e => setNuptk(e.target.value)} placeholder="Nomor Unik PTK" />
                                        <div>
                                            <label className="block text-xs text-muted-foreground mb-1">Status Kepegawaian</label>
                                            <select value={employmentStatusId} onChange={e => setEmploymentStatusId(e.target.value)}
                                                className="flex h-9 w-full rounded-md border border-border-subtle bg-background px-3 py-1.5 text-sm">
                                                <option value="">Pilih status</option>
                                                {employmentStatuses.map(s => <option key={s.id} value={s.id}>{s.nama}</option>)}
                                            </select>
                                        </div>
                                        <div>
                                            <label className="block text-xs text-muted-foreground mb-1">Departemen</label>
                                            <select value={departmentId} onChange={e => setDepartmentId(e.target.value)}
                                                className="flex h-9 w-full rounded-md border border-border-subtle bg-background px-3 py-1.5 text-sm">
                                                <option value="">Pilih departemen</option>
                                                {departments.map(d => <option key={d.id} value={d.id}>{d.nama || d.name}</option>)}
                                            </select>
                                        </div>
                                    </div>
                                </TabsContent>

                                {/* ═══ SERTIFIKASI ═══ */}
                                <TabsContent value="sertifikasi" className="m-0 focus-visible:outline-none focus-visible:ring-0">
                                    <div className="space-y-5">
                                        <div className="bg-background border border-border-subtle rounded-lg p-4">
                                            <div className="flex items-center justify-between">
                                                <div>
                                                    <p className="text-sm font-medium">Sudah Sertifikasi</p>
                                                    <p className="text-xs text-muted-foreground mt-0.5">Status sertifikasi pendidik</p>
                                                </div>
                                                <Switch checked={sudahSertifikasi} onCheckedChange={setSudahSertifikasi} />
                                            </div>
                                        </div>
                                        {sudahSertifikasi && (
                                            <div className="grid grid-cols-2 gap-3.5">
                                                <div className="col-span-2">
                                                    <Input label="Nomor Sertifikat Pendidik" value={nomorSertifikat} onChange={e => setNomorSertifikat(e.target.value)} placeholder="Nomor sertifikat" />
                                                </div>
                                            </div>
                                        )}
                                        <div className="grid grid-cols-2 gap-3.5 mt-4">
                                            <Input label="Jam mengajar/minggu" type="number" value={jamMengajar} onChange={e => setJamMengajar(e.target.value)} placeholder="0" min={0} max={168} />
                                        </div>
                                    </div>
                                </TabsContent>

                                {/* ═══ KONTAK ═══ */}
                                <TabsContent value="kontak" className="m-0 focus-visible:outline-none focus-visible:ring-0">
                                    <div className="space-y-3">
                                        <p className="text-sm font-medium text-foreground">Data kontak</p>
                                        {contacts.map((c, i) => (
                                            <div key={i} className="flex gap-2 items-start">
                                                <div className="w-40 shrink-0">
                                                    <select value={c.contact_type_id} onChange={e => updateContact(i, 'contact_type_id', e.target.value)}
                                                        className="flex h-9 w-full rounded-md border border-border-subtle bg-background px-3 py-1.5 text-sm">
                                                        <option value="">Tipe</option>
                                                        {contactTypes.map(t => <option key={t.id} value={t.id}>{t.nama}</option>)}
                                                    </select>
                                                </div>
                                                <div className="flex-1">
                                                    <Input value={c.value} onChange={e => updateContact(i, 'value', e.target.value)} placeholder="Nomor / alamat email / username" />
                                                </div>
                                                {contacts.length > 1 && (
                                                    <button type="button" onClick={() => removeContact(i)} className="h-9 w-9 shrink-0 inline-flex items-center justify-center rounded-md border border-border-subtle text-muted-foreground hover:text-destructive hover:border-destructive transition-all">
                                                        <Trash2 className="w-4 h-4" />
                                                    </button>
                                                )}
                                            </div>
                                        ))}
                                        <Button type="button" variant="outline" size="sm" onClick={addContact} className="gap-1.5">
                                            <Plus className="w-3.5 h-3.5" /> Tambah kontak
                                        </Button>
                                    </div>
                                </TabsContent>

                                {/* ═══ ALAMAT ═══ */}
                                <TabsContent value="alamat" className="m-0 focus-visible:outline-none focus-visible:ring-0">
                                    <div className="grid grid-cols-2 gap-3.5">
                                        <div className="col-span-2">
                                            <Input label="Alamat" value={alamat} onChange={e => setAlamat(e.target.value)} placeholder="Jalan, nomor rumah, RT/RW" />
                                        </div>
                                        <Input label="Desa / Kelurahan" value={desaKelurahan} onChange={e => setDesaKelurahan(e.target.value)} placeholder="Nama desa" />
                                        <Input label="Kecamatan" value={kecamatan} onChange={e => setKecamatan(e.target.value)} placeholder="Nama kecamatan" />
                                        <Input label="Kabupaten / Kota" value={kabupatenKota} onChange={e => setKabupatenKota(e.target.value)} placeholder="Nama kab/kota" />
                                        <Input label="Provinsi" value={provinsi} onChange={e => setProvinsi(e.target.value)} placeholder="Nama provinsi" />
                                        <Input label="Kode pos" value={kodePos} onChange={e => setKodePos(e.target.value)} placeholder="Kode pos" />
                                    </div>
                                </TabsContent>
                            </div>
                        </Tabs>
                    </div>

                    {/* Right Sidebar */}
                    <div className="w-[200px] shrink-0 flex flex-col gap-4 sticky top-4">
                        <div className="bg-background border border-border-subtle rounded-xl p-4 flex flex-col items-center gap-3">
                            <div className="w-[120px] h-[120px] rounded-full bg-surface-muted border-2 border-dashed border-border-strong flex items-center justify-center text-muted-foreground">
                                {photoSrc ? (
                                    <img src={photoSrc} alt="" className="w-full h-full rounded-full object-cover" />
                                ) : (
                                    <Camera className="w-7 h-7" />
                                )}
                            </div>
                            <button type="button" className="w-full text-sm font-medium text-primary hover:text-primary/80 transition-colors inline-flex items-center justify-center gap-1.5">
                                <Upload className="w-3.5 h-3.5" />
                                Unggah foto
                            </button>
                            <p className="text-[11px] text-muted-foreground text-center">JPG/PNG, maks 2MB</p>
                        </div>

                        <div className="bg-background border border-border-subtle rounded-xl p-4">
                            <p className="text-xs text-muted-foreground mb-2">Kelengkapan data</p>
                            <div className="w-full h-1.5 bg-surface-muted rounded-full overflow-hidden">
                                <div className="h-full bg-yellow-500 rounded-full transition-all" style={{ width: `${progressPct}%` }} />
                            </div>
                            <p className="text-[11px] text-muted-foreground mt-1.5">{filledCount} dari 4 bagian terisi</p>
                        </div>

                        <div className="bg-background border border-border-subtle rounded-xl p-4 space-y-2">
                            <p className="text-xs font-medium text-foreground">Ringkasan</p>
                            <div className="text-xs space-y-1.5 text-muted-foreground">
                                <p>NIP: {nip || '-'}</p>
                                <p>NUPTK: {nuptk || '-'}</p>
                                <p>Status: {employmentStatuses.find(s => s.id === employmentStatusId)?.nama || '-'}</p>
                                <p>Dept: {departments.find(d => d.id === departmentId)?.nama || departments.find(d => d.id === departmentId)?.name || '-'}</p>
                            </div>
                        </div>

                        <Link href="/hr/employees" className="w-full inline-flex items-center justify-center h-9 px-4 border border-border-subtle bg-background text-foreground rounded-md text-sm font-medium hover:bg-surface-muted transition-all">
                            Batal
                        </Link>

                        <Btn type="button" disabled={saving} className="w-full" onClick={() => handleSubmit()}>
                            <Save className="w-4 h-4 mr-1.5" />
                            {saving ? 'Menyimpan...' : 'Simpan'}
                        </Btn>
                    </div>
                </div>
            </div>

            <DuplicateNikDialog
                nik={nik}
                duplicates={duplicates}
                open={nikDialogOpen}
                onOpenChange={setNikDialogOpen}
                onTarikData={(cp) => {
                    if (cp) { setNamaLengkap(cp.nama_lengkap); setNik(cp.nik); setGender(cp.gender || ''); setTempatLahir(cp.tempat_lahir || ''); setTanggalLahir(cp.tanggal_lahir || ''); setAgama(cp.agama || ''); setPhoto(cp.photo || ''); }
                    reset();
                }}
                onCreateNew={() => { setNikDialogOpen(false); reset(); }}
            />
        </DashboardLayout>
    );
}

export default Form;
