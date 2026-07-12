import React from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@dashboard/Components/ui/dialog';
import { Button } from '@dashboard/Components/ui/button';
import { AlertTriangle } from 'lucide-react';

interface ConfirmDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    title: string;
    message: string;
    confirmLabel?: string;
    cancelLabel?: string;
    variant?: 'danger' | 'primary';
    onConfirm: () => void;
}

export default function ConfirmDialog({
    open,
    onOpenChange,
    title,
    message,
    confirmLabel = 'Delete',
    cancelLabel = 'Cancel',
    variant = 'danger',
    onConfirm,
}: ConfirmDialogProps) {
    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="max-w-sm gap-0">
                <DialogHeader className="flex flex-row items-center mb-0">
                    <DialogTitle className="flex items-center gap-2 text-base">
                        <span className={`w-8 h-8 rounded-full flex items-center justify-center shrink-0 ${
                            variant === 'danger' ? 'bg-danger/10 text-danger' : 'bg-primary/10 text-primary'
                        }`}>
                            <AlertTriangle className="w-4 h-4" />
                        </span>
                        {title}
                    </DialogTitle>
                </DialogHeader>
                <div className="px-4 sm:px-6 py-4 overflow-y-auto flex-1">
                    <p className="text-sm text-muted-foreground">{message}</p>
                </div>
                <DialogFooter className="mt-0">
                    <Button type="button" variant="outline" onClick={() => onOpenChange(false)}>
                        {cancelLabel}
                    </Button>
                    <Button
                        type="button"
                        variant={variant === 'danger' ? 'destructive' : 'default'}
                        onClick={() => { onConfirm(); onOpenChange(false); }}
                    >
                        {confirmLabel}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}