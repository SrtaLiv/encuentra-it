import { CareerCard } from '@/components/career-card';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/public-layout';
import { type CareerSummary, type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { Check, Copy, RotateCcw } from 'lucide-react';
import { useState } from 'react';

type ScoredCareer = CareerSummary & { score: number };

export default function QuizResult({ careers }: { uuid: string; careers: ScoredCareer[] }) {
    const { auth } = usePage<SharedData>().props;
    const [copied, setCopied] = useState(false);
    const top = careers.slice(0, 3);
    const others = careers.slice(3).filter((career) => career.score > 0);

    const copyLink = async () => {
        try {
            await navigator.clipboard.writeText(window.location.href);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        } catch {
            setCopied(false);
        }
    };

    return (
        <PublicLayout>
            <Head title="Tu resultado" />

            <div className="mx-auto max-w-5xl px-4 py-12 md:py-20">
                <h1 className="text-3xl font-bold tracking-tight md:text-4xl">Estas carreras van con vos</h1>
                <p className="text-muted-foreground mt-3 max-w-2xl">
                    El porcentaje muestra cuánto coinciden tus respuestas con cada carrera. Es un punto de partida, no una sentencia: entrá a cada
                    una, mirá qué se hace en el día a día y probá el primer paso de su ruta.
                </p>

                {top.length === 0 ? (
                    <p className="text-muted-foreground mt-10">No pudimos calcular un resultado. Probá hacer el test de nuevo.</p>
                ) : (
                    <div className="mt-10 grid gap-4 md:grid-cols-3">
                        {top.map((career) => (
                            <CareerCard key={career.id} career={career} score={career.score} />
                        ))}
                    </div>
                )}

                <div className="mt-8 flex flex-col gap-3 sm:flex-row">
                    <Button variant="outline" onClick={copyLink}>
                        {copied ? <Check /> : <Copy />}
                        {copied ? 'Link copiado' : 'Copiar link del resultado'}
                    </Button>
                    <Button asChild variant="ghost">
                        <Link href={route('quiz.show')}>
                            <RotateCcw />
                            Hacer el test de nuevo
                        </Link>
                    </Button>
                </div>

                {!auth.user && (
                    <div className="bg-primary/5 border-primary/30 card-raised mt-10 p-6">
                        <h2 className="font-extrabold">Guardá tu resultado y tu progreso</h2>
                        <p className="text-muted-foreground mt-1 text-sm">
                            Creá una cuenta gratis para guardar este resultado y marcar los pasos que vas completando en cada ruta.
                        </p>
                        <Button asChild className="mt-4">
                            <Link href={route('register')}>Crear cuenta gratis</Link>
                        </Button>
                    </div>
                )}

                {others.length > 0 && (
                    <section className="mt-14">
                        <h2 className="text-xl font-extrabold">Otras opciones que también podrían gustarte</h2>
                        <div className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            {others.slice(0, 6).map((career) => (
                                <CareerCard key={career.id} career={career} score={career.score} />
                            ))}
                        </div>
                    </section>
                )}
            </div>
        </PublicLayout>
    );
}
