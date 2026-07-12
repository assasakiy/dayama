import React from 'react';
import { useForm, usePage } from '@inertiajs/react';
import AccountSettingsLayout from '../../../Layouts/AccountSettingsLayout';
import { Card, CardHeader, CardTitle, CardContent, CardDescription } from '../../../Components/ui/card';
import { Btn } from '../../../Components/ui/btn';
import { Palette, Sun, Moon, Monitor, Save, CheckCircle2 } from 'lucide-react';

export default function AppearanceIndex() {
    const { preferences } = usePage<any>().props as any;
    const { data, setData, put, processing, recentlySuccessful, isDirty } = useForm({
        theme: preferences?.theme || 'system',
        color_scheme: preferences?.color_scheme || 'blue',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put('/dashboard/account/appearance', { preserveScroll: true });
    };

    return (
        <AccountSettingsLayout
            title="Appearance"
            description="Customize the look and feel of your workspace."
        >
            <form onSubmit={submit} className="space-y-6">
                <Card>
                    <CardHeader className="border-b border-border-subtle pb-4">
                        <CardTitle className="text-sm font-semibold flex items-center gap-2">
                            <span className="w-6 h-6 rounded bg-surface-muted flex items-center justify-center">
                                <Palette className="w-3.5 h-3.5 text-muted-foreground" />
                            </span>
                            Theme Preference
                        </CardTitle>
                        <CardDescription className="text-xs mt-1.5 ml-8">
                            Select or customize your UI theme.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="pt-6 space-y-6">
                        <div className="flex flex-col gap-4">
                            <label className="text-sm font-medium">Interface Theme</label>
                            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                {([['light', 'Light', Sun], ['dark', 'Dark', Moon], ['system', 'System', Monitor]] as const).map(([val, label, Icon]) => (
                                    <button
                                        key={val}
                                        type="button"
                                        onClick={() => setData('theme', val)}
                                        className={`flex flex-col items-center justify-center p-5 border rounded-xl gap-3 transition-all ${
                                            data.theme === val
                                            ? 'border-primary bg-primary/5 text-primary ring-1 ring-primary/20'
                                            : 'border-border bg-background hover:bg-surface-muted text-muted-foreground'
                                        }`}
                                    >
                                        <Icon className="w-8 h-8" />
                                        <span className="text-sm font-semibold">{label}</span>
                                    </button>
                                ))}
                            </div>
                            <p className="text-sm text-muted-foreground mt-2">
                                If 'System' is selected, the theme will automatically match your operating system's appearance.
                            </p>
                        </div>
                    </CardContent>
                    <div className="px-6 py-4 border-t border-border-subtle flex items-center justify-end gap-3 bg-surface-muted/10 rounded-b-lg">
                        {recentlySuccessful && (
                            <span className="text-sm text-green-600 flex items-center gap-1.5 font-medium">
                                <CheckCircle2 className="w-4 h-4" /> Saved!
                            </span>
                        )}
                        <Btn
                            type="submit"
                            loading={processing}
                            disabled={!isDirty || processing}
                            icon={<Save className="w-4 h-4" />}
                        >
                            Save Preferences
                        </Btn>
                    </div>
                </Card>
            </form>
        </AccountSettingsLayout>
    );
}
