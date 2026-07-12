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
            title="Export Data" 
            description="Download a copy of your personal data and content."
        >
            <div className="space-y-6">
                
                <Card>
                    <CardHeader className="border-b border-border-subtle pb-4">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <span className="w-6 h-6 rounded bg-surface-muted flex items-center justify-center">
                                <Download className="w-3.5 h-3.5 text-muted-foreground" />
                            </span>
                            Data Archive Request
                        </CardTitle>
                        <CardDescription className="text-xs mt-1.5 ml-8">
                            Request a complete archive of your account data.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="pt-6">
                        
                        {!requestSent ? (
                            <div className="space-y-6">
                                <p className="text-sm text-muted-foreground">
                                    You can request a file containing your personal data, preferences, and connected accounts. 
                                    Your archive will be prepared and downloaded instantly to your device.
                                </p>
                                
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div className="flex items-start gap-3 p-4 border border-border-subtle rounded-lg bg-surface-muted/30">
                                        <FileJson className="w-5 h-5 text-primary mt-0.5" />
                                        <div>
                                            <h4 className="text-sm font-medium">Machine Readable</h4>
                                            <p className="text-xs text-muted-foreground mt-1">Data is exported in JSON and CSV formats.</p>
                                        </div>
                                    </div>
                                    <div className="flex items-start gap-3 p-4 border border-border-subtle rounded-lg bg-surface-muted/30">
                                        <Download className="w-5 h-5 text-primary mt-0.5" />
                                        <div>
                                            <h4 className="text-sm font-medium">Instant Download</h4>
                                            <p className="text-xs text-muted-foreground mt-1">Your file will be downloaded directly to your device.</p>
                                        </div>
                                    </div>
                                </div>

                                <div className="flex justify-end pt-2">
                                    <form method="POST" action="/dashboard/account/export" onSubmit={requestExport}>
                                        <input type="hidden" name="_token" value={usePage().props.csrf_token as string} />
                                        <Btn 
                                            type="submit"
                                            loading={processing}
                                            disabled={processing}
                                            icon={<HardDriveDownload className="w-4 h-4" />}
                                        >
                                            Request Data Archive
                                        </Btn>
                                    </form>
                                </div>
                            </div>
                        ) : (
                            <div className="py-8 flex flex-col items-center justify-center text-center space-y-4">
                                <h3 className="text-lg font-bold">Request Downloaded</h3>
                                <div className="mt-4 flex gap-4">
                                    <button 
                                        onClick={() => setRequestSent(false)}
                                        className="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors"
                                    >
                                        Go back
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
