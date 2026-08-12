import React, { useState } from 'react';
import { useForm, usePage } from '@inertiajs/react';
import AccountSettingsLayout from '../../../Layouts/AccountSettingsLayout';
import { Card, CardHeader, CardTitle, CardContent, CardDescription } from '../../../Components/ui/card';
import { Switch } from '../../../Components/ui/switch';
import { Mail, Bell, Smartphone, Save } from 'lucide-react';
import { Btn } from '../../../Components/ui/btn';

export default function NotificationsIndex() {
    const { preferences } = usePage<any>().props as any;
    const { data, setData, put, processing, recentlySuccessful, isDirty } = useForm({
        email_newsletter: preferences?.email_newsletter ?? true,
        email_updates: preferences?.email_updates ?? false,
        email_marketing: preferences?.email_marketing ?? true,
        push_comments: preferences?.push_comments ?? true,
        push_mentions: preferences?.push_mentions ?? true,
        push_messages: preferences?.push_messages ?? false,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put('/account/notifications', { preserveScroll: true });
    };

    return (
        <AccountSettingsLayout 
            title="Notifikasi" 
            description="Kelola cara Anda menerima notifikasi dan pembaruan."
        >
            <form onSubmit={submit} className="space-y-6">
                
                {/* Email Notifications */}
                <Card>
                    <CardHeader className="border-b border-border-subtle pb-4">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <span className="w-6 h-6 rounded bg-surface-muted flex items-center justify-center">
                                <Mail className="w-3.5 h-3.5 text-muted-foreground" />
                            </span>
                            Notifikasi Email
                        </CardTitle>
                        <CardDescription className="text-xs mt-1.5 ml-8">
                            Kontrol email mana yang dikirim ke alamat email utama Anda.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="pt-6 space-y-6">
                        <div className="flex items-center justify-end md:justify-between w-full">
                            <div className="space-y-0.5">
                                <h3 className="text-sm font-medium">Buletin Mingguan</h3>
                                <p className="text-sm text-muted-foreground">Terima ringkasan mingguan dari cerita dan berita teratas.</p>
                            </div>
                            <Switch 
                                checked={data.email_newsletter}
                                onCheckedChange={(checked) => setData('email_newsletter', checked)}
                            />
                        </div>
                        <div className="flex items-center justify-end md:justify-between w-full">
                            <div className="space-y-0.5">
                                <h3 className="text-sm font-medium">Pembaruan Akun</h3>
                                <p className="text-sm text-muted-foreground">Notifikasi penting tentang keamanan dan penagihan akun Anda.</p>
                            </div>
                            <Switch 
                                checked={data.email_updates}
                                onCheckedChange={(checked) => setData('email_updates', checked)}
                            />
                        </div>
                        <div className="flex items-center justify-end md:justify-between w-full">
                            <div className="space-y-0.5">
                                <h3 className="text-sm font-medium">Pemasaran & Penawaran</h3>
                                <p className="text-sm text-muted-foreground">Email promosi, penawaran khusus, dan undangan acara.</p>
                            </div>
                            <Switch 
                                checked={data.email_marketing}
                                onCheckedChange={(checked) => setData('email_marketing', checked)}
                            />
                        </div>
                    </CardContent>
                </Card>

                {/* Push Notifications */}
                <Card>
                    <CardHeader className="border-b border-border-subtle pb-4">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <span className="w-6 h-6 rounded bg-surface-muted flex items-center justify-center">
                                <Bell className="w-3.5 h-3.5 text-muted-foreground" />
                            </span>
                            Notifikasi Push
                        </CardTitle>
                        <CardDescription className="text-xs mt-1.5 ml-8">
                            Kontrol notifikasi yang dikirim langsung ke browser atau perangkat Anda.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="pt-6 space-y-6">
                        <div className="flex items-center justify-end md:justify-between w-full">
                            <div className="space-y-0.5">
                                <h3 className="text-sm font-medium">Komentar</h3>
                                <p className="text-sm text-muted-foreground">Dapatkan notifikasi saat seseorang berkomentar di postingan Anda.</p>
                            </div>
                            <Switch 
                                checked={data.push_comments}
                                onCheckedChange={(checked) => setData('push_comments', checked)}
                            />
                        </div>
                        <div className="flex items-center justify-end md:justify-between w-full">
                            <div className="space-y-0.5">
                                <h3 className="text-sm font-medium">Sebutan</h3>
                                <p className="text-sm text-muted-foreground">Dapatkan notifikasi saat Anda disebut dalam komentar atau postingan.</p>
                            </div>
                            <Switch 
                                checked={data.push_mentions}
                                onCheckedChange={(checked) => setData('push_mentions', checked)}
                            />
                        </div>
                        <div className="flex items-center justify-end md:justify-between w-full">
                            <div className="space-y-0.5">
                                <h3 className="text-sm font-medium">Pesan Langsung</h3>
                                <p className="text-sm text-muted-foreground">Dapatkan notifikasi saat Anda menerima pesan langsung baru.</p>
                            </div>
                            <Switch 
                                checked={data.push_messages}
                                onCheckedChange={(checked) => setData('push_messages', checked)}
                            />
                        </div>
                    </CardContent>
                    <div className="px-6 py-4 border-t border-border-subtle flex justify-end gap-3 bg-surface-muted/10 rounded-b-lg">
                        {recentlySuccessful && (
                            <span className="text-sm text-green-600 self-center font-medium">Preferensi disimpan.</span>
                        )}
                        <Btn 
                            type="submit"
                            loading={processing}
                            disabled={!isDirty || processing}
                            icon={<Save className="w-4 h-4" />}
                        >
                            Simpan Preferensi
                        </Btn>
                    </div>
                </Card>

            </form>
        </AccountSettingsLayout>
    );
}
