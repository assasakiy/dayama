import React, { useState } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@dashboard/Components/ui/dialog';
import { Button } from '@dashboard/Components/ui/button';
import { Btn } from '@dashboard/Components/ui/btn';
import { AlertTriangle, CopyCheck } from 'lucide-react';

export interface DuplicateEntry {
    institution_id: string;
    person_id: string;
    institution_name: string;
}

interface Props {
    nik: string;
    duplicates: DuplicateEntry[];
    open: boolean;
    onOpenChange: (open: boolean) => void;
    onTarikData?: (copiedPerson: { id: string; nama_lengkap: string; nik: string; gender?: string; tempat_lahir?: string; tanggal_lahir?: string; agama?: string; photo?: string } | null) => void;
    onCreateNew?: () => void;
}

export function DuplicateNikDialog({ nik, duplicates, open, onOpenChange, onTarikData, onCreateNew }: Props) {
    const [loadingId, setLoadingId] = useState<string | null>(null);

    const handleTarikData = async (entry: DuplicateEntry) => {
        setLoadingId(entry.person_id);
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch('/persons/copy-from', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token || '',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ nik, source_person_id: entry.person_id }),
            });
            const data = await res.json();
            if (data.person) {
                onTarikData?.(data.person);
                onOpenChange(false);
            }
        } catch {
            // silent
        } finally {
            setLoadingId(null);
        }
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle className="flex items-center gap-2">
                        <AlertTriangle className="w-5 h-5 text-amber-500" />
                        NIK sudah terdaftar
                    </DialogTitle>
                    <DialogDescription>
                        NIK <strong>{nik}</strong> sudah terdaftar di lembaga lain. Salin data dari lembaga tersebut?
                    </DialogDescription>
                </DialogHeader>

                <div className="space-y-2">
                    {duplicates.map((d) => (
                        <div key={d.institution_id} className="flex items-center justify-between p-3 rounded-lg border border-border-subtle bg-muted/30">
                            <div>
                                <p className="text-sm font-medium">{d.institution_name}</p>
                                <p className="text-xs text-muted-foreground">
                                    Person: {d.person_id.slice(0, 8)}...
                                </p>
                            </div>
                            <Btn
                                size="sm"
                                onClick={() => handleTarikData(d)}
                                disabled={loadingId !== null}
                                loading={loadingId === d.person_id}
                                icon={<CopyCheck className="w-4 h-4" />}
                            >
                                Tarik Data
                            </Btn>
                        </div>
                    ))}
                </div>

                <DialogFooter className="flex items-center justify-end gap-2">
                    <Button type="button" variant="outline" onClick={() => { onOpenChange(false); onCreateNew?.(); }}>
                        Tetap Buat Baru
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
