import React from 'react';
import { Head } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';
import { Building2, Users, ArrowLeftRight, Fingerprint } from 'lucide-react';

interface Props {
    total_institutions: number;
    total_persons: number;
    total_transfers: number;
    total_person_index: number;
}

function Index({ total_institutions, total_persons, total_transfers, total_person_index }: Props) {
    const cards = [
        { label: 'Total Institusi', value: total_institutions, icon: Building2 },
        { label: 'Total Person', value: total_persons, icon: Users },
        { label: 'Total Transfer', value: total_transfers, icon: ArrowLeftRight },
        { label: 'Total NIK Index', value: total_person_index, icon: Fingerprint },
    ];

    return (
        <>
            <Head title="Statistik Yayasan" />

            <div className="mb-6">
                <h1 className="text-2xl font-bold">Statistik Yayasan</h1>
                <p className="text-muted-foreground text-sm mt-1">
                    Ringkasan data yayasan
                </p>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {cards.map((card) => (
                    <div key={card.label} className="bg-background rounded-xl border border-border-subtle p-6">
                        <div className="flex items-center gap-3 mb-3">
                            <div className="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                                <card.icon className="w-5 h-5 text-primary" />
                            </div>
                            <span className="text-sm font-medium text-muted-foreground">{card.label}</span>
                        </div>
                        <p className="text-3xl font-bold">{card.value.toLocaleString('id-ID')}</p>
                    </div>
                ))}
            </div>
        </>
    );
}

Index.layout = (page: React.ReactNode) => <DashboardLayout>{page}</DashboardLayout>;

export default Index;
