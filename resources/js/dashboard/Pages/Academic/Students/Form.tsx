import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Input } from '@dashboard/Components/ui/input';
import { Btn } from '@dashboard/Components/ui/btn';
import { Button } from '@dashboard/Components/ui/button';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@dashboard/Components/ui/tabs';
import { DuplicateNikDialog } from '@dashboard/Components/DuplicateNikDialog';
import { useNikDuplicateCheck } from '@dashboard/Hooks/useNikDuplicateCheck';
import { Save, Camera, Upload, FileText, ArrowLeft, Plus, Trash2 } from 'lucide-react';

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

interface StudentData {
    id: string;
    person_id: string;
    nis: string;
    nisn: string | null;
    angkatan: string | null;
    status: string | null;
    nama_ibu_kandung: string | null;
    tempat_tinggal: string | null;
    nomor_kk: string | null;
    nomor_kip: string | null;
    cita_cita: string | null;
    hobi: string | null;
    waktu_tempuh_menit: number | null;
    is_locked: boolean;
    person: PersonData | null;
}

interface Option { id: string; nama?: string; name?: string }

interface Props {
    student?: StudentData | null;
    persons: PersonData[];
    contactTypes?: Option[];
    addressTypes?: Option[];
}

interface ContactRow { contact_type_id: string; value: string }

const AGAMA = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
const STATUS_OPTIONS = ['aktif', 'lulus', 'mutasi', 'keluar', 'dikeluarkan', 'meninggal'];

// ── Component ──

function Form({ student, persons, contactTypes = [] }: Props) {
    const isEditing = !!student;
    const person = isEditing ? student?.person : null;
    const primaryAddress = person?.addresses?.find(a => a.is_primary) ?? person?.addresses?.[0];

    // Person state
    const [personId, setPersonId] = useState(student?.person_id || '');
    const [namaLengkap, setNamaLengkap] = useState(person?.nama_lengkap || '');
    const [nik, setNik] = useState(person?.nik || '');
    const [gender, setGender] = useState(person?.gender || '');
    const [tempatLahir, setTempatLahir] = useState(person?.tempat_lahir || '');
    const [tanggalLahir, setTanggalLahir] = useState(person?.tanggal_lahir || '');
    const [agama, setAgama] = useState(person?.agama || '');

    // Student state
    const [nis, setNis] = useState(student?.nis || '');
    const [nisn, setNisn] = useState(student?.nisn || '');
    const [angkatan, setAngkatan] = useState(student?.angkatan || '');
    const [status, setStatus] = useState(student?.status || 'aktif');
    const [namaIbuKandung, setNamaIbuKandung] = useState(student?.nama_ibu_kandung || '');
    const [tempatTinggal, setTempatTinggal] = useState(student?.tempat_tinggal || '');
    const [nomorKk, setNomorKk] = useState(student?.nomor_kk || '');
    const [nomorKip, setNomorKip] = useState(student?.nomor_kip || '');
    const [citaCita, setCitaCita] = useState(student?.cita_cita || '');
    const [hobi, setHobi] = useState(student?.hobi || '');
    const [waktuTempuh, setWaktuTempuh] = useState(student?.waktu_tempuh_menit?.toString() || '');
    const [isLocked, setIsLocked] = useState(student?.is_locked || false);

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

    // When person selected (create mode), populate fields
    const selectedPerson = persons.find(p => p.id === personId);
    useEffect(() => {
        if (!isEditing && selectedPerson) {
            setNamaLengkap(selectedPerson.nama_lengkap || '');
            setNik(selectedPerson.nik || '');
            setGender(selectedPerson.gender || '');
            setTempatLahir(selectedPerson.tempat_lahir || '');
            setTanggalLahir(selectedPerson.tanggal_lahir || '');
            setAgama(selectedPerson.agama || '');
        }
    }, [personId]);

    // Contact helpers
    const addContact = () => setContacts(c => [...c, { contact_type_id: '', value: '' }]);
    const removeContact = (i: number) => setContacts(c => c.filter((_, idx) => idx !== i));
    const updateContact = (i: number, field: keyof ContactRow, val: string) =>
        setContacts(c => c.map((r, idx) => idx === i ? { ...r, [field]: val } : r));

    // Progress
    const filledCount = [namaLengkap, nis, alamat, namaIbuKandung].filter(Boolean).length;
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
            // Student
            nis,
            nisn: nisn || null,
            angkatan: angkatan || null,
            status: status || null,
            nama_ibu_kandung: namaIbuKandung || null,
            tempat_tinggal: tempatTinggal || null,
            nomor_kk: nomorKk || null,
            nomor_kip: nomorKip || null,
            cita_cita: citaCita || null,
            hobi: hobi || null,
            waktu_tempuh_menit: waktuTempuh ? parseInt(waktuTempuh, 10) : null,
            is_locked: isLocked,
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

        const url = isEditing && student ? `/academic/students/${student.id}` : '/academic/students';
        const method = isEditing ? 'put' : 'post';

        router[method](url, data, {
            onSuccess: () => setSaving(false),
            onError: (err) => { setErrors(err); setSaving(false); },
        });
    };

    const photoSrc = isEditing ? person?.photo : photo;

    return (
        <DashboardLayout>
            <Head title={isEditing ? 'Edit Siswa' : 'Tambah Siswa'} />

            <div className="space-y-6 max-w-7xl mx-auto">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <Btn variant="ghost" size="sm" onClick={() => router.visit('/academic/students')} icon={<ArrowLeft className="w-4 h-4" />} />
                        <div>
                            <h1 className="text-xl font-semibold tracking-tight">{isEditing ? 'Edit Siswa' : 'Tambah Siswa'}</h1>
                            <p className="text-sm text-muted-foreground">{isEditing ? 'Ubah data siswa' : 'Buat data siswa baru'}</p>
                        </div>
                    </div>
                    <Btn onClick={() => handleSubmit()} disabled={saving} loading={saving} icon={<Save className="w-4 h-4" />}>
                        {saving ? 'Menyimpan...' : 'Simpan'}
                    </Btn>
                </div>

                <div className="flex gap-6 items-start">
                    {/* Left — Tabs & Content */}
                    <div className="flex-1 min-w-0">
                        <Tabs defaultValue="pribadi" className="w-full">
                            <TabsList className="mb-5">
                                <TabsTrigger value="pribadi">Data Pribadi</TabsTrigger>
                                <TabsTrigger value="akademik">Akademik</TabsTrigger>
                                <TabsTrigger value="alamat">Alamat & Tinggal</TabsTrigger>
                                <TabsTrigger value="wali">Orang Tua</TabsTrigger>
                                <TabsTrigger value="kontak">Kontak</TabsTrigger>
                            </TabsList>

                            <div className="rounded-xl border border-border-subtle bg-background p-6">
                                {/* ═══ DATA PRIBADI ═══ */}
                                <TabsContent value="pribadi" className="m-0 focus-visible:outline-none focus-visible:ring-0">
                                    <div className="grid grid-cols-2 gap-3.5">
                                        <div className="col-span-2">
                                            <Input label="Nama lengkap *" value={namaLengkap} onChange={e => setNamaLengkap(e.target.value)} placeholder="Sesuai akte kelahiran" error={errors.nama_lengkap} />
                                        </div>
                                        <Input label="NIK" value={nik} onChange={e => setNik(e.target.value)} placeholder="16 digit NIK" error={errors.nik} />
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
                                        <Input label="Cita-cita" value={citaCita} onChange={e => setCitaCita(e.target.value)} placeholder="Dokter, guru, dll" />
                                        <Input label="Hobi" value={hobi} onChange={e => setHobi(e.target.value)} placeholder="Membaca, olahraga, dll" />
                                    </div>
                                </TabsContent>

                                {/* ═══ AKADEMIK ═══ */}
                                <TabsContent value="akademik" className="m-0 focus-visible:outline-none focus-visible:ring-0">
                                    <div className="space-y-5">
                                        <div>
                                            <p className="text-sm font-medium text-foreground mb-3">Identitas siswa</p>
                                            <div className="grid grid-cols-2 gap-3.5">
                                                <Input label="NIS *" value={nis} onChange={e => setNis(e.target.value)} placeholder="Nomor induk siswa" error={errors.nis} />
                                                <Input label="NISN" value={nisn} onChange={e => setNisn(e.target.value)} placeholder="Nomor induk siswa nasional" />
                                                <Input label="Angkatan" value={angkatan} onChange={e => setAngkatan(e.target.value)} placeholder="Contoh: 2026" />
                                                <div>
                                                    <label className="block text-xs text-muted-foreground mb-1">Status</label>
                                                    <select value={status} onChange={e => setStatus(e.target.value)}
                                                        className="flex h-9 w-full rounded-md border border-border-subtle bg-background px-3 py-1.5 text-sm capitalize">
                                                        {STATUS_OPTIONS.map(s => <option key={s} value={s}>{s}</option>)}
                                                    </select>
                                                </div>
                                                <Input label="Nomor KK" value={nomorKk} onChange={e => setNomorKk(e.target.value)} placeholder="Nomor kartu keluarga" />
                                                <Input label="Nomor KIP" value={nomorKip} onChange={e => setNomorKip(e.target.value)} placeholder="Nomor KIP (jika ada)" />
                                            </div>
                                        </div>
                                        <div className="border-t border-border-subtle pt-5">
                                            <div className="flex items-center justify-between">
                                                <div>
                                                    <p className="text-sm font-medium">Data terkunci</p>
                                                    <p className="text-xs text-muted-foreground mt-0.5">Data terkunci tidak dapat diubah oleh operator</p>
                                                </div>
                                                <input type="checkbox" checked={isLocked} onChange={e => setIsLocked(e.target.checked)} className="w-4 h-4 rounded border-border-strong accent-primary" />
                                            </div>
                                        </div>
                                    </div>
                                </TabsContent>

                                {/* ═══ ALAMAT & TINGGAL ═══ */}
                                <TabsContent value="alamat" className="m-0 focus-visible:outline-none focus-visible:ring-0">
                                    <div className="grid grid-cols-2 gap-3.5">
                                        <div className="col-span-2">
                                            <Input label="Alamat jalan" value={alamat} onChange={e => setAlamat(e.target.value)} placeholder="Jalan, nomor rumah, RT/RW" />
                                        </div>
                                        <Input label="Desa / Kelurahan" value={desaKelurahan} onChange={e => setDesaKelurahan(e.target.value)} placeholder="Nama desa" />
                                        <Input label="Kecamatan" value={kecamatan} onChange={e => setKecamatan(e.target.value)} placeholder="Nama kecamatan" />
                                        <Input label="Kabupaten / Kota" value={kabupatenKota} onChange={e => setKabupatenKota(e.target.value)} placeholder="Nama kab/kota" />
                                        <Input label="Provinsi" value={provinsi} onChange={e => setProvinsi(e.target.value)} placeholder="Nama provinsi" />
                                        <Input label="Kode pos" value={kodePos} onChange={e => setKodePos(e.target.value)} placeholder="Kode pos" />
                                        <Input label="Tempat tinggal" value={tempatTinggal} onChange={e => setTempatTinggal(e.target.value)} placeholder="Bersama orang tua, asrama, kos" />
                                        <Input label="Waktu tempuh (menit)" type="number" value={waktuTempuh} onChange={e => setWaktuTempuh(e.target.value)} placeholder="0" min={0} />
                                    </div>
                                </TabsContent>

                                {/* ═══ ORANG TUA ═══ */}
                                <TabsContent value="wali" className="m-0 focus-visible:outline-none focus-visible:ring-0">
                                    <div className="space-y-5">
                                        <div>
                                            <p className="text-sm font-medium text-foreground mb-3">Data ibu kandung</p>
                                            <div className="grid grid-cols-2 gap-3.5">
                                                <div className="col-span-2">
                                                    <Input label="Nama ibu kandung" value={namaIbuKandung} onChange={e => setNamaIbuKandung(e.target.value)} placeholder="Nama lengkap ibu kandung" />
                                                </div>
                                            </div>
                                        </div>
                                        <div className="border-t border-border-subtle pt-4">
                                            <p className="text-xs text-muted-foreground">Data ayah, wali, dan hubungan keluarga lainnya dapat dikelola melalui halaman detail Person setelah data siswa disimpan.</p>
                                        </div>
                                    </div>
                                </TabsContent>

                                {/* ═══ KONTAK ═══ */}
                                <TabsContent value="kontak" className="m-0 focus-visible:outline-none focus-visible:ring-0">
                                    <div className="space-y-3">
                                        <p className="text-sm font-medium text-foreground">Data kontak siswa / wali</p>
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
                                                    <Input value={c.value} onChange={e => updateContact(i, 'value', e.target.value)} placeholder="Nomor / email / username" />
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
                                <p>NIS: {nis || '-'}</p>
                                <p>NISN: {nisn || '-'}</p>
                                <p>Status: <span className="capitalize">{status || '-'}</span></p>
                                <p>Angkatan: {angkatan || '-'}</p>
                            </div>
                        </div>

                        <div className="bg-background border border-border-subtle rounded-xl p-4 space-y-2">
                            <p className="text-xs font-medium text-foreground">Dokumen cepat</p>
                            {['Akte', 'KK', 'Ijazah'].map(d => (
                                <button key={d} type="button" className="w-full text-xs text-left text-muted-foreground hover:text-foreground transition-colors py-1 inline-flex items-center gap-1.5">
                                    <FileText className="w-3 h-3" /> {d}
                                </button>
                            ))}
                        </div>

                        <Link href="/academic/students" className="w-full inline-flex items-center justify-center h-9 px-4 border border-border-subtle bg-background text-foreground rounded-md text-sm font-medium hover:bg-surface-muted transition-all">
                            Batal
                        </Link>

                        <Btn type="button" disabled={saving} className="w-full" onClick={() => handleSubmit()}>
                            <Save className="w-4 h-4 mr-1.5" />
                            {saving ? 'Menyimpan...' : 'Simpan siswa'}
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
