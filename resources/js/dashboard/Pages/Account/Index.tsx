import React from 'react';
import { Link } from '@inertiajs/react';
import AccountSettingsLayout, { menuGroups } from '../../Layouts/AccountSettingsLayout';
import { Card, CardHeader, CardTitle, CardContent, CardDescription } from '../../Components/ui/card';
import { usePermissions } from '@dashboard/hooks/usePermissions';

export default function AccountIndex() {
    const { can } = usePermissions();

    const filteredMenuGroups = menuGroups.map(group => ({
        ...group,
        items: group.items.filter(item => !item.permission || can(item.permission))
    })).filter(group => group.items.length > 0);

    return (
        <AccountSettingsLayout 
            title="Pengaturan" 
            description="Kelola pengaturan dan preferensi akun Anda."
        >
            <div className="hidden md:block space-y-8">
                {filteredMenuGroups.map((group, groupIndex) => (
                    <div key={groupIndex} className="space-y-4">
                        <h3 className="text-sm font-semibold text-muted-foreground uppercase tracking-wider">
                            {group.title}
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            {group.items.map((item, itemIndex) => (
                                <Link key={itemIndex} href={item.href}>
                                    <Card className={`h-full transition-all hover:border-primary/50 hover:shadow-sm ${item.destructive ? 'hover:border-destructive hover:bg-destructive/5' : 'hover:bg-surface-muted/50'}`}>
                                        <CardContent className="p-5 flex items-start gap-4">
                                            <div className={`w-10 h-10 rounded-lg flex items-center justify-center shrink-0 ${item.destructive ? 'bg-destructive/10 text-destructive' : 'bg-primary/10 text-primary'}`}>
                                                <item.icon className="w-5 h-5" />
                                            </div>
                                            <div>
                                                <h4 className={`text-sm font-semibold ${item.destructive ? 'text-destructive' : 'text-foreground'}`}>
                                                    {item.label}
                                                </h4>
                                                <p className="text-xs text-muted-foreground mt-1">
                                                    Kelola pengaturan dan konfigurasi {item.label.toLowerCase()} Anda.
                                                </p>
                                            </div>
                                        </CardContent>
                                    </Card>
                                </Link>
                            ))}
                        </div>
                    </div>
                ))}
            </div>
        </AccountSettingsLayout>
    );
}
