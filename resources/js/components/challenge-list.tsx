import { ChallengeCard } from '@/components/challenges/challenge-card';
import { Notice } from '@/components/notice';
import { type Challenge, type ChallengeCompletion, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';

interface ChallengeListProps {
    challenges: Challenge[];
    // challenge id => what the user submitted when completing it
    completed: Record<number, ChallengeCompletion>;
}

export function ChallengeList({ challenges, completed }: ChallengeListProps) {
    const { auth } = usePage<SharedData>().props;
    const doneCount = challenges.filter((challenge) => challenge.id in completed).length;

    if (challenges.length === 0) {
        return <p className="text-muted-foreground text-sm">Todavía no cargamos desafíos para esta carrera.</p>;
    }

    return (
        <div>
            {auth.user ? (
                <p className="text-muted-foreground mb-4 text-sm">
                    Completaste {doneCount} de {challenges.length}. Tus explicaciones y entregas forman tu portfolio en{' '}
                    <Link href={route('dashboard')} className="text-brand font-bold">
                        Mi panel
                    </Link>
                    .
                </p>
            ) : (
                <Notice className="mb-4">
                    <Link href={route('register')} className="text-link font-bold underline underline-offset-2">
                        Creá una cuenta
                    </Link>{' '}
                    para marcar los desafíos que completás, explicar lo que hiciste y desbloquear las soluciones.
                </Notice>
            )}

            <ol className="space-y-3">
                {challenges.map((challenge, index) => (
                    <ChallengeCard key={challenge.id} challenge={challenge} number={index + 1} completion={completed[challenge.id] ?? null} />
                ))}
            </ol>
        </div>
    );
}
