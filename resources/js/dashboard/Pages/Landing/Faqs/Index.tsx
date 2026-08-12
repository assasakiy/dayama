import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Input } from '@dashboard/Components/ui/input';
import { Textarea } from '@dashboard/Components/ui/textarea';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogClose, DialogFooter } from '@dashboard/Components/ui/dialog';
import { Switch } from '@dashboard/Components/ui/switch';
import ConfirmDialog from '@dashboard/Components/ui/confirm-dialog';
import { Btn } from '@dashboard/Components/ui/btn';
import {
    Plus,
    Pencil,
    Trash2,
    HelpCircle,
    Save,
    GripVertical,
    Eye,
    EyeOff,
    X,
} from 'lucide-react';

interface Faq {
    id: string;
    question: string;
    answer: string;
    category: string | null;
    is_active: boolean;
    sort_order: number;
    created_at: string;
    updated_at: string;
}

export default function FaqIndex({ faqs }: { faqs: Faq[] }) {
    const [modalOpen, setModalOpen] = useState(false);
    const [editFaq, setEditFaq] = useState<Faq | null>(null);
    const [deleteTarget, setDeleteTarget] = useState<Faq | null>(null);
    const [submitting, setSubmitting] = useState(false);

    const [question, setQuestion] = useState('');
    const [answer, setAnswer] = useState('');
    const [category, setCategory] = useState('umum');
    const [isActive, setIsActive] = useState(true);

    const isEdit = !!editFaq;

    const resetForm = () => {
        setQuestion('');
        setAnswer('');
        setCategory('umum');
        setIsActive(true);
        setEditFaq(null);
    };

    const openCreate = () => {
        resetForm();
        setModalOpen(true);
    };

    const openEdit = (faq: Faq) => {
        setEditFaq(faq);
        setQuestion(faq.question);
        setAnswer(faq.answer);
        setCategory(faq.category || 'umum');
        setIsActive(faq.is_active);
        setModalOpen(true);
    };

    const handleSubmit = () => {
        setSubmitting(true);
        const data = { question, answer, category, is_active: isActive };

        if (isEdit && editFaq) {
            router.put(`/landing/faqs/${editFaq.id}`, data, {
                preserveScroll: true,
                onFinish: () => { setSubmitting(false); setModalOpen(false); resetForm(); },
            });
        } else {
            router.post('/landing/faqs', data, {
                preserveScroll: true,
                onFinish: () => { setSubmitting(false); setModalOpen(false); resetForm(); },
            });
        }
    };

    const handleDelete = () => {
        if (!deleteTarget) return;
        router.delete(`/landing/faqs/${deleteTarget.id}`, {
            preserveScroll: true,
            onFinish: () => setDeleteTarget(null),
        });
    };

    return (
        <DashboardLayout>
            <Head title="Manajemen FAQ" />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-foreground">Manajemen FAQ</h1>
                        <p className="text-sm text-muted-foreground mt-1">
                            Kelola pertanyaan yang sering diajukan. FAQ ini dapat ditampilkan di berbagai halaman landing.
                        </p>
                    </div>
                    <Btn onClick={openCreate} icon={<Plus className="w-4 h-4" />}>
                        Tambah FAQ
                    </Btn>
                </div>

                {/* FAQ List */}
                <div className="space-y-3">
                    {faqs.map((faq, index) => (
                        <div
                            key={faq.id}
                            className="group flex items-start gap-4 rounded-lg border border-border-subtle bg-background p-5 transition-all hover:shadow-sm hover:border-primary/20"
                        >
                            {/* Number */}
                            <div className="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-primary/10 text-primary text-sm font-bold">
                                {index + 1}
                            </div>

                            {/* Content */}
                            <div className="flex-1 min-w-0">
                                <div className="flex items-center gap-2 mb-1">
                                    <span className="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-secondary/10 text-secondary uppercase tracking-wider">
                                        {faq.category || 'umum'}
                                    </span>
                                    <h3 className="font-semibold text-foreground">{faq.question}</h3>
                                    {!faq.is_active && (
                                        <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-zinc-500/10 text-zinc-500">
                                            <EyeOff className="w-3 h-3" /> Nonaktif
                                        </span>
                                    )}
                                </div>
                                <p className="text-sm text-muted-foreground line-clamp-2">{faq.answer}</p>
                            </div>

                            {/* Actions */}
                            <div className="flex-shrink-0 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <Btn variant="ghost" size="sm" onClick={() => openEdit(faq)} icon={<Pencil className="w-4 h-4" />}>
                                    Edit
                                </Btn>
                                <Btn variant="ghost" size="sm" onClick={() => setDeleteTarget(faq)} className="text-destructive hover:text-destructive" icon={<Trash2 className="w-4 h-4" />}>
                                    Hapus
                                </Btn>
                            </div>
                        </div>
                    ))}

                    {faqs.length === 0 && (
                        <div className="text-center py-16 text-muted-foreground rounded-lg border border-border-subtle bg-background">
                            <HelpCircle className="w-12 h-12 mx-auto mb-4 opacity-40" />
                            <p className="text-lg font-medium">Belum ada FAQ</p>
                            <p className="text-sm">Klik "Tambah FAQ" untuk menambahkan pertanyaan pertama.</p>
                        </div>
                    )}
                </div>
            </div>

            {/* Create/Edit Modal */}
            <Dialog open={modalOpen} onOpenChange={(v) => { if (!v) { setModalOpen(false); resetForm(); } }}>
                <DialogContent className="max-w-2xl max-h-[90vh] flex flex-col p-0 gap-0">
                    <DialogHeader className="flex flex-row items-center justify-between px-6 py-4 border-b border-border-subtle mb-0 shrink-0">
                        <DialogTitle className="text-base">{isEdit ? 'Edit FAQ' : 'Tambah FAQ Baru'}</DialogTitle>
                        <DialogClose className="p-1 rounded-md text-muted-foreground hover:text-foreground hover:bg-surface-muted transition-colors">
                            <X className="w-4 h-4" />
                        </DialogClose>
                    </DialogHeader>

                    <div className="flex flex-col flex-1 min-h-0">
                        <div className="space-y-4 px-6 py-4 overflow-y-auto flex-1">
                        <div className="space-y-1.5">
                            <label className="text-sm font-medium">Pertanyaan</label>
                            <Input
                                value={question}
                                onChange={e => setQuestion(e.target.value)}
                                placeholder="Bagaimana cara mendaftar?"
                            />
                        </div>
                        <div className="space-y-1.5">
                            <label className="text-sm font-medium">Kategori</label>
                            <Input
                                value={category}
                                onChange={e => setCategory(e.target.value)}
                                placeholder="Misal: psb, donasi, umum"
                            />
                            <p className="text-[11px] text-muted-foreground mt-1">Kategori ini digunakan untuk memilah FAQ mana yang akan ditampilkan di halaman tertentu.</p>
                        </div>
                        <div className="space-y-1.5">
                            <label className="text-sm font-medium">Jawaban</label>
                            <Textarea
                                value={answer}
                                onChange={e => setAnswer(e.target.value)}
                                placeholder="Pendaftaran dapat dilakukan secara online..."
                                rows={4}
                            />
                        </div>
                        <div className="flex items-center justify-between">
                            <label className="text-sm font-medium">Aktif</label>
                            <Switch checked={isActive} onCheckedChange={setIsActive} />
                        </div>
                    </div>

                    <div className="px-6 py-4 bg-surface-muted/30 border-t border-border-subtle shrink-0 flex items-center justify-end gap-3">
                        <DialogClose asChild>
                            <Btn variant="outline" type="button" onClick={() => { setModalOpen(false); resetForm(); }}>Batal</Btn>
                        </DialogClose>
                        <Btn onClick={handleSubmit} disabled={submitting || !question.trim() || !answer.trim()} icon={<Save className="w-4 h-4" />}>
                            {submitting ? 'Menyimpan...' : isEdit ? 'Perbarui' : 'Simpan'}
                        </Btn>
                    </div>
                </div>
                </DialogContent>
            </Dialog>

            {/* Delete Confirmation */}
            <ConfirmDialog
                open={!!deleteTarget}
                onOpenChange={() => setDeleteTarget(null)}
                onConfirm={handleDelete}
                title="Hapus FAQ"
                message={`Apakah Anda yakin ingin menghapus FAQ "${deleteTarget?.question}"?`}
            />
        </DashboardLayout>
    );
}
