import React from 'react';
import { Head } from '@inertiajs/react';
import DashboardLayout from '@dashboard/Layouts/DashboardLayout';

interface Props {
    // Add props here
}

function Index({}: Props) {
    return (
        <>
            <Head title="Analitik" />
            <div className="flex flex-col items-center justify-center py-20 text-center">
                <div className="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-4">
                    <BarChart3 className="w-8 h-8 text-primary" />
                </div>
                <h1 className="text-2xl font-bold mb-2">Analitik</h1>
                <p className="text-muted-foreground max-w-md">
                    Halaman ini sedang dalam pengembangan.
                </p>
            </div>
        </>
    );
}

Index.layout = (page: React.ReactNode) => <DashboardLayout>{page}</DashboardLayout>;

export default Index;
