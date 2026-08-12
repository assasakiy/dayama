import React, { useState } from 'react';
import AccountSettingsLayout from '../../../Layouts/AccountSettingsLayout';
import { Card, CardHeader, CardTitle, CardContent, CardDescription } from '../../../Components/ui/card';
import { Download, FileJson, Mail, HardDriveDownload } from 'lucide-react';
import { useForm, usePage } from '@inertiajs/react';
import { Btn } from '../../../Components/ui/btn';

export default function ExportIndex() {
    const [requestSent, setRequestSent] = useState(false);
    const { processing } = useForm();

    const requestExport = () => {
        // Delay hiding the form to allow the native form submission to complete
        setTimeout(() => {
            setRequestSent(true);
        }, 500);
    };

    return (
        <AccountSettingsLayout 
            title="Ekspor Data" 
            description="Unduh salinan data pribadi dan konten Anda."
        >
            <div className="space-y-6">
                
                <Card>
                    <CardHeader className="border-b border-border-subtle pb-4">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <span className="w-6 h-6 rounded bg-surface-muted flex items-center justify-center">
                                <Download className="w-3.5 h-3.5 text-muted-foreground" />
                            </span>
                            Permintaan Arsip Data
                        </CardTitle>
                        <CardDescription className="text-xs mt-1.5 ml-8">
                            Minta arsip lengkap data akun Anda.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="pt-6">
                        
                        {!requestSent ? (
                            <div className="space-y-6">
                                <p className="text-sm text-muted-foreground">
                                    Anda dapat meminta file yang berisi data pribadi, preferensi, dan akun terhubung. 
                                    Arsip Anda akan disiapkan dan diunduh langsung ke perangkat Anda.
                                </p>
                                
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div className="flex items-start gap-3 p-4 border border-border-subtle rounded-lg bg-surface-muted/30">
                                        <FileJson className="w-5 h-5 text-primary mt-0.5" />
                                        <div>
                                            <h4 className="text-sm font-medium">Dapat Dibaca Mesin</h4>
                                            <p className="text-xs text-muted-foreground mt-1">Data diekspor dalam format JSON dan CSV.</p>
                                        </div>
                                    </div>
                                    <div className="flex items-start gap-3 p-4 border border-border-subtle rounded-lg bg-surface-muted/30">
                                        <Download className="w-5 h-5 text-primary mt-0.5" />
                                        <div>
                                            <h4 className="text-sm font-medium">Unduh Instan</h4>
                                            <p className="text-xs text-muted-foreground mt-1">File Anda akan diunduh langsung ke perangkat Anda.</p>
                                        </div>
                                    </div>
                                </div>

                                <div className="flex justify-end pt-2">
                                    <form method="POST" action="/account/export" onSubmit={requestExport}>
                                        <input type="hidden" name="_token" value={usePage().props.csrf_token as string} />
                                        <Btn 
                                            type="submit"
                                            loading={processing}
                                            disabled={processing}
                                            icon={<HardDriveDownload className="w-4 h-4" />}
                                        >
                                            Minta Arsip Data
                                        </Btn>
                                    </form>
                                </div>
                            </div>
                        ) : (
                            <div className="py-8 flex flex-col items-center justify-center text-center space-y-4">
                                <h3 className="text-lg font-bold">Permintaan Diunduh</h3>
                                <div className="mt-4 flex gap-4">
                                    <button 
                                        onClick={() => setRequestSent(false)}
                                        className="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors"
                                    >
                                        Kembali
                                    </button>
                                </div>
                            </div>
                        )}

                    </CardContent>
                </Card>

            </div>
        </AccountSettingsLayout>
    );
}
