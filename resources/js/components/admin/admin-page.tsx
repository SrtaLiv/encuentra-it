import { Alert, AlertDescription } from '@/components/ui/alert';
import AppLayout from '@/layouts/app-layout';
import { type SharedData } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { type ReactNode } from 'react';

interface AdminPageProps {
    title: string;
    href: string;
    actions?: ReactNode;
    children: ReactNode;
}

export function AdminPage({ title, href, actions, children }: AdminPageProps) {
    const { flash } = usePage<SharedData>().props;

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Admin', href: '/admin' },
                { title, href },
            ]}
        >
            <Head title={`Admin · ${title}`} />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <div className="flex flex-wrap items-center justify-between gap-4">
                    <h1 className="text-2xl font-bold tracking-tight">{title}</h1>
                    {actions}
                </div>
                {flash.success && (
                    <Alert>
                        <AlertDescription>{flash.success}</AlertDescription>
                    </Alert>
                )}
                {flash.error && (
                    <Alert variant="destructive">
                        <AlertDescription>{flash.error}</AlertDescription>
                    </Alert>
                )}
                {children}
            </div>
        </AppLayout>
    );
}
