import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import AccountSettingsLayout from '../../../Layouts/AccountSettingsLayout';
import { Card, CardHeader, CardTitle, CardContent, CardDescription } from '../../../Components/ui/card';
import { AlertTriangle, Trash2 } from 'lucide-react';
import { Btn } from '../../../Components/ui/btn';

export default function DeleteIndex() {
    const [confirmText, setConfirmText] = useState('');
    const { data, setData, delete: destroy, processing, errors } = useForm({
        password: '',
    });
    
    // Hardcoded confirmation word for safety
    const requiredWord = 'DELETE';
    const isConfirmMatch = confirmText === requiredWord;

    const handleDelete = (e: React.FormEvent) => {
        e.preventDefault();
        if (!isConfirmMatch || !data.password) return;
        
        destroy('/account/delete', {
            preserveScroll: true,
        });
    };

    return (
        <AccountSettingsLayout 
            title="Delete Account" 
            description="Permanently remove your account and all associated data."
        >
            <div className="space-y-6">
                
                <Card className="border-destructive/20">
                    <CardHeader className="border-b border-border-subtle pb-4 bg-destructive/5">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2 text-destructive">
                            <span className="w-6 h-6 rounded bg-destructive/10 flex items-center justify-center">
                                <AlertTriangle className="w-3.5 h-3.5 text-destructive" />
                            </span>
                            Danger Zone
                        </CardTitle>
                        <CardDescription className="text-xs mt-1.5 ml-8 text-destructive/80">
                            Proceed with extreme caution.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="pt-6">
                        
                        <div className="space-y-6">
                            <div className="p-4 bg-destructive/5 border border-destructive/20 rounded-lg space-y-2">
                                <h4 className="text-sm font-semibold text-destructive">This action cannot be undone</h4>
                                <ul className="text-sm text-destructive/90 list-disc list-inside space-y-1">
                                    <li>All your published posts and drafts will be permanently deleted.</li>
                                    <li>Your profile, comments, and interactions will be removed.</li>
                                    <li>Your username will become available for others to register.</li>
                                    <li>Any active subscriptions will be canceled immediately.</li>
                                </ul>
                            </div>

                            <form onSubmit={handleDelete} className="space-y-4 max-w-md">
                                <div className="space-y-2.5">
                                    <label className="text-sm font-medium">
                                        Please type <span className="font-bold font-mono bg-surface-muted px-1 py-0.5 rounded text-foreground">{requiredWord}</span> to confirm
                                    </label>
                                    <input
                                        type="text"
                                        value={confirmText}
                                        onChange={(e) => setConfirmText(e.target.value)}
                                        placeholder={requiredWord}
                                        className="w-full px-3 py-2.5 border border-border rounded-lg bg-background focus:ring-2 focus:ring-destructive/20 focus:border-destructive text-sm transition-all"
                                    />
                                </div>

                                <div className="space-y-2.5">
                                    <label className="text-sm font-medium">
                                        Current Password
                                    </label>
                                    <input
                                        type="password"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        placeholder="Enter your password"
                                        className="w-full px-3 py-2.5 border border-border rounded-lg bg-background focus:ring-2 focus:ring-destructive/20 focus:border-destructive text-sm transition-all"
                                    />
                                    {errors.password && <p className="text-sm text-destructive">{errors.password}</p>}
                                </div>
                                
                                <Btn 
                                    type="submit"
                                    loading={processing}
                                    disabled={!isConfirmMatch || !data.password || processing}
                                    variant="danger"
                                    className="w-full"
                                    icon={<Trash2 className="w-4 h-4" />}
                                >
                                    Permanently Delete Account
                                </Btn>
                            </form>
                        </div>

                    </CardContent>
                </Card>

            </div>
        </AccountSettingsLayout>
    );
}
