import { AreaIcon } from '@/components/area-icon';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/public-layout';
import { type Area } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ArrowRight, Compass, ListChecks, Route } from 'lucide-react';

interface WelcomeProps {
    areas: Area[];
    stats: { careers: number; resources: number };
}

const steps = [
    { icon: Compass, title: 'Descubrí', text: 'Hacé el test y conocé las carreras que más van con vos.' },
    { icon: ListChecks, title: 'Entendé', text: 'Mirá qué hace cada profesional en su día a día y si necesita programar.' },
    { icon: Route, title: 'Avanzá', text: 'Seguí una ruta paso a paso con recursos gratuitos y desafíos para practicar.' },
];

export default function Welcome({ areas, stats }: WelcomeProps) {
    return (
        <PublicLayout>
            <Head title="Encontrá tu camino en IT" />

            <section className="mx-auto max-w-6xl px-4 pt-16 pb-20 md:pt-24">
                <div className="max-w-3xl">
                    <h1 className="text-4xl font-bold tracking-tight text-balance md:text-6xl">¿No sabés por dónde empezar en IT? Empezá acá.</h1>
                    <p className="text-muted-foreground mt-6 max-w-2xl text-lg text-pretty">
                        Hay {stats.careers} carreras en tecnología y muchas no requieren programar. Descubrí cuál va con vos, entendé qué hace cada
                        profesional y seguí una ruta clara con {stats.resources} recursos gratuitos y desafíos.
                    </p>
                    <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                        <Button asChild size="lg">
                            <Link href={route('quiz.show')}>
                                Hacer el test (3 minutos)
                                <ArrowRight />
                            </Link>
                        </Button>
                        <Button asChild size="lg" variant="outline">
                            <Link href={route('careers.index')}>Explorar carreras</Link>
                        </Button>
                    </div>
                </div>
            </section>

            <section className="bg-card border-y-2">
                <div className="mx-auto grid max-w-6xl gap-8 px-4 py-14 md:grid-cols-3">
                    {steps.map((step, index) => (
                        <div key={step.title} className="flex gap-4">
                            <span className="bg-primary text-primary-foreground flex size-10 shrink-0 items-center justify-center rounded-full font-extrabold">
                                {index + 1}
                            </span>
                            <div>
                                <h2 className="flex items-center gap-2 font-extrabold">
                                    <step.icon className="text-brand size-4" />
                                    {step.title}
                                </h2>
                                <p className="text-muted-foreground mt-1 text-sm">{step.text}</p>
                            </div>
                        </div>
                    ))}
                </div>
            </section>

            <section className="mx-auto max-w-6xl px-4 py-20">
                <h2 className="text-2xl font-bold tracking-tight md:text-3xl">Explorá por área</h2>
                <p className="text-muted-foreground mt-2">Elegí un área que te llame la atención y mirá qué carreras tiene.</p>
                <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {areas.map((area) => (
                        <Link
                            key={area.id}
                            href={route('careers.index', { area: area.slug })}
                            className="group bg-card hover:border-primary/60 card-raised flex flex-col gap-3 p-5 transition-colors"
                        >
                            <span className="bg-primary/10 text-brand flex size-10 items-center justify-center rounded-lg">
                                <AreaIcon name={area.icon} className="size-5" />
                            </span>
                            <h3 className="font-extrabold">{area.name}</h3>
                            <p className="text-muted-foreground flex-1 text-sm">{area.description}</p>
                            <span className="text-brand inline-flex items-center gap-1 text-sm font-bold">
                                {area.careers_count} {area.careers_count === 1 ? 'carrera' : 'carreras'}
                                <ArrowRight className="size-3.5 transition-transform group-hover:translate-x-0.5" />
                            </span>
                        </Link>
                    ))}
                </div>
            </section>

            <section className="mx-auto max-w-6xl px-4 pb-20">
                <div className="bg-primary text-primary-foreground flex flex-col items-start gap-6 rounded-2xl p-8 md:flex-row md:items-center md:justify-between md:p-12">
                    <div className="max-w-xl">
                        <h2 className="text-2xl font-bold md:text-3xl">No todo en IT es programar</h2>
                        <p className="mt-2 opacity-90">
                            QA manual, UX/UI, Scrum Master, Analista Funcional, GRC, marketing… Hay muchos caminos para entrar a tecnología sin
                            escribir código.
                        </p>
                    </div>
                    <Button asChild size="lg" variant="secondary" className="h-auto min-h-12 w-full py-3 text-center whitespace-normal md:w-auto">
                        <Link href={route('careers.index', { coding: 'none' })}>Ver carreras sin programación</Link>
                    </Button>
                </div>
            </section>
        </PublicLayout>
    );
}
