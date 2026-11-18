import { Notice } from '@/components/notice';
import { ProgressBar } from '@/components/progress-bar';
import { Checkbox } from '@/components/ui/checkbox';
import { cn } from '@/lib/utils';
import { type Level, type RoadmapStep, type SharedData } from '@/types';
import { Link, router, usePage } from '@inertiajs/react';

interface RoadmapProps {
    steps: RoadmapStep[];
    completedStepIds: number[];
}

export function Roadmap({ steps, completedStepIds }: RoadmapProps) {
    const { auth, enums } = usePage<SharedData>().props;
    const levels = Object.keys(enums.levels) as Level[];
    const completed = steps.filter((step) => completedStepIds.includes(step.id)).length;
    const progress = steps.length ? Math.round((completed / steps.length) * 100) : 0;

    const toggle = (step: RoadmapStep) => {
        router.post(route('steps.toggle', step.id), {}, { preserveScroll: true, preserveState: true });
    };

    if (steps.length === 0) {
        return <p className="text-muted-foreground text-sm">Todavía no cargamos la ruta de esta carrera.</p>;
    }

    return (
        <div>
            {auth.user ? (
                <div className="mb-6">
                    <div className="text-muted-foreground mb-2 flex justify-between text-sm">
                        <span>
                            {completed} de {steps.length} pasos completados
                        </span>
                        <span>{progress}%</span>
                    </div>
                    <ProgressBar value={progress} label="Progreso de la ruta" />
                </div>
            ) : (
                <Notice className="mb-6">
                    <Link href={route('register')} className="text-link font-bold underline underline-offset-2">
                        Creá una cuenta
                    </Link>{' '}
                    para marcar los pasos que vas completando.
                </Notice>
            )}

            <div className="space-y-8">
                {levels.map((level) => {
                    const levelSteps = steps.filter((step) => step.level === level);

                    if (levelSteps.length === 0) {
                        return null;
                    }

                    return (
                        <div key={level}>
                            <h3 className="text-muted-foreground mb-3 text-xs font-extrabold tracking-wider uppercase">{enums.levels[level]}</h3>
                            <ol className="space-y-3">
                                {levelSteps.map((step) => {
                                    const done = completedStepIds.includes(step.id);

                                    return (
                                        <li
                                            key={step.id}
                                            className={cn('bg-card card-raised flex gap-3 p-4', done && 'border-primary/40 bg-primary/5')}
                                        >
                                            {auth.user && (
                                                <Checkbox
                                                    checked={done}
                                                    onCheckedChange={() => toggle(step)}
                                                    aria-label={`Marcar "${step.title}" como completado`}
                                                    className="mt-0.5"
                                                />
                                            )}
                                            <div>
                                                <p className={cn('font-bold', done && 'text-muted-foreground line-through')}>{step.title}</p>
                                                {step.description && <p className="text-muted-foreground mt-1 text-sm">{step.description}</p>}
                                            </div>
                                        </li>
                                    );
                                })}
                            </ol>
                        </div>
                    );
                })}
            </div>
        </div>
    );
}
