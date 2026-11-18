import { ProgressBar } from '@/components/progress-bar';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, type Career, type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowRight, CheckCircle2, Compass, ExternalLink } from 'lucide-react';

interface DashboardProps {
    latestResult: { uuid: string; created_at: string } | null;
    topCareers: (Pick<Career, 'id' | 'name' | 'slug' | 'summary'> & { score: number })[];
    progress: { name: string; slug: string; completed: number; total: number }[];
    portfolio: { id: number; title: string; career: { name: string; slug: string }; evidence_url: string | null; decision: string | null }[];
}

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Mi panel', href: '/dashboard' }];

export default function Dashboard({ latestResult, topCareers, progress, portfolio }: DashboardProps) {
    const { auth } = usePage<SharedData>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Mi panel" />

            <div className="flex flex-col gap-8 p-4 md:p-6">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight">Hola, {auth.user?.name}</h1>
                    <p className="text-muted-foreground mt-1">Este es tu camino en IT hasta ahora.</p>
                </div>

                <section>
                    <h2 className="mb-4 text-lg font-extrabold">Tu test vocacional</h2>
                    {latestResult ? (
                        <div className="grid gap-4 md:grid-cols-3">
                            {topCareers.map((career) => (
                                <Link
                                    key={career.id}
                                    href={route('careers.show', career.slug)}
                                    className="bg-card hover:border-primary/60 card-raised p-5 transition-colors"
                                >
                                    <div className="flex items-start justify-between gap-2">
                                        <h3 className="font-extrabold">{career.name}</h3>
                                        <span className="text-brand font-bold tabular-nums">{career.score}%</span>
                                    </div>
                                    <p className="text-muted-foreground mt-2 text-sm">{career.summary}</p>
                                </Link>
                            ))}
                            <div className="flex gap-2 md:col-span-3">
                                <Button asChild variant="outline" size="sm">
                                    <Link href={route('quiz.result', latestResult.uuid)}>Ver resultado completo</Link>
                                </Button>
                                <Button asChild variant="ghost" size="sm">
                                    <Link href={route('quiz.show')}>Hacer el test de nuevo</Link>
                                </Button>
                            </div>
                        </div>
                    ) : (
                        <div className="bg-card card-raised flex flex-col items-start gap-4 p-6">
                            <Compass className="text-brand size-8" />
                            <p>Todavía no hiciste el test. En 3 minutos descubrís qué carreras van con vos.</p>
                            <Button asChild>
                                <Link href={route('quiz.show')}>
                                    Hacer el test
                                    <ArrowRight />
                                </Link>
                            </Button>
                        </div>
                    )}
                </section>

                <section>
                    <h2 className="mb-4 text-lg font-extrabold">Tus rutas en progreso</h2>
                    {progress.length === 0 ? (
                        <p className="text-muted-foreground">
                            Entrá a una{' '}
                            <Link href={route('careers.index')} className="text-brand font-bold">
                                carrera
                            </Link>{' '}
                            y marcá los pasos de su ruta a medida que avanzás.
                        </p>
                    ) : (
                        <div className="grid gap-4 md:grid-cols-2">
                            {progress.map((item) => {
                                const percent = item.total ? Math.round((item.completed / item.total) * 100) : 0;

                                return (
                                    <Link
                                        key={item.slug}
                                        href={route('careers.show', item.slug)}
                                        className="bg-card hover:border-primary/60 card-raised p-5 transition-colors"
                                    >
                                        <div className="flex justify-between gap-2">
                                            <h3 className="font-extrabold">{item.name}</h3>
                                            <span className="text-muted-foreground text-sm">
                                                {item.completed}/{item.total}
                                            </span>
                                        </div>
                                        <ProgressBar value={percent} label={`Progreso en ${item.name}`} className="mt-3" />
                                    </Link>
                                );
                            })}
                        </div>
                    )}
                </section>

                <section>
                    <h2 className="mb-1 text-lg font-extrabold">Tu portfolio de desafíos</h2>
                    <p className="text-muted-foreground mb-4 text-sm">
                        Todo lo que hiciste. Usalo para mostrar tu experiencia práctica en entrevistas.
                    </p>
                    {portfolio.length === 0 ? (
                        <p className="text-muted-foreground">
                            Todavía no completaste desafíos. Entrá a una{' '}
                            <Link href={route('careers.index')} className="text-brand font-bold">
                                carrera
                            </Link>{' '}
                            y empezá por el primero.
                        </p>
                    ) : (
                        <ul className="card-raised divide-y">
                            {portfolio.map((item) => (
                                <li key={item.id} className="flex flex-wrap items-center justify-between gap-3 p-4">
                                    <div className="flex items-start gap-3">
                                        <CheckCircle2 className="text-brand mt-0.5 size-5 shrink-0" />
                                        <div>
                                            <p className="font-bold">{item.title}</p>
                                            {item.decision && <p className="text-muted-foreground mt-1 line-clamp-2 text-sm">“{item.decision}”</p>}
                                            <Link
                                                href={route('careers.show', item.career.slug)}
                                                className="text-muted-foreground text-sm hover:underline"
                                            >
                                                {item.career.name}
                                            </Link>
                                        </div>
                                    </div>
                                    {item.evidence_url && (
                                        <Button asChild variant="outline" size="sm">
                                            <a href={item.evidence_url} target="_blank" rel="noopener noreferrer">
                                                <ExternalLink />
                                                Ver entrega
                                            </a>
                                        </Button>
                                    )}
                                </li>
                            ))}
                        </ul>
                    )}
                </section>
            </div>
        </AppLayout>
    );
}
