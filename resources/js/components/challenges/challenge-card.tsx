import { AiModeHint } from '@/components/challenges/ai-mode-hint';
import { ChallengeGuide } from '@/components/challenges/challenge-guide';
import { CompletionForm } from '@/components/challenges/completion-form';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { type Challenge, type ChallengeCompletion, type SharedData } from '@/types';
import { router, usePage } from '@inertiajs/react';
import { Bot, CheckCircle2, ExternalLink, Flag, KeyRound, Lock, Undo2 } from 'lucide-react';

interface ChallengeCardProps {
    challenge: Challenge;
    number: number;
    completion: ChallengeCompletion | null;
}

function Disclosure({ title, icon: Icon, children }: { title: string; icon: typeof KeyRound; children: React.ReactNode }) {
    return (
        <details className="group bg-card mt-3 rounded-xl border-2 text-sm">
            <summary className="flex cursor-pointer list-none items-center gap-2 px-3 py-2 font-bold">
                <Icon className="text-brand size-4" />
                {title}
            </summary>
            <div className="border-t-2 px-3 py-3 leading-relaxed whitespace-pre-line">{children}</div>
        </details>
    );
}

// paragraphs wrapped in quotes are material to work on (like an ai answer to audit), so they read as a quote
function ChallengeDescription({ text }: { text: string }) {
    return (
        <div className="mt-2 space-y-3 text-sm leading-relaxed">
            {text.split(/(?:\r?\n\s*){2,}/).map((paragraph, index) => {
                const quoted = paragraph.trim().match(/^"([\s\S]+)"$/);

                if (quoted) {
                    return (
                        <figure key={index} className="bg-background rounded-xl border-2 border-dashed px-4 py-3">
                            <figcaption className="text-muted-foreground mb-1 flex items-center gap-1.5 text-xs font-extrabold tracking-[0.8px] uppercase">
                                <Bot className="size-3.5" />
                                Respuesta de la IA
                            </figcaption>
                            <blockquote className="text-foreground">{quoted[1]}</blockquote>
                        </figure>
                    );
                }

                return (
                    <p key={index} className="text-muted-foreground whitespace-pre-line">
                        {paragraph}
                    </p>
                );
            })}
        </div>
    );
}

export function ChallengeCard({ challenge, number, completion }: ChallengeCardProps) {
    const { auth, enums } = usePage<SharedData>().props;
    const done = completion !== null;

    const undo = () => {
        router.delete(route('challenges.undo', challenge.id), { preserveScroll: true });
    };

    return (
        <li className={cn('bg-card card-raised p-5', done && 'border-primary/40 bg-primary/5')}>
            <div className="flex items-start gap-3">
                <span
                    className={cn(
                        'flex size-8 shrink-0 items-center justify-center rounded-full text-sm font-extrabold',
                        done ? 'bg-primary text-primary-foreground' : 'bg-muted',
                    )}
                >
                    {done ? <CheckCircle2 className="size-4" /> : number}
                </span>
                <div className="min-w-0 flex-1">
                    <div className="flex flex-wrap items-center gap-2">
                        <h3 className="font-extrabold">{challenge.title}</h3>
                        <Badge variant="outline">{enums.levels[challenge.level]}</Badge>
                    </div>
                    <ChallengeDescription text={challenge.description} />
                    <p className="mt-3 flex gap-2 text-sm">
                        <Flag className="text-brand mt-0.5 size-4 shrink-0" />
                        <span>
                            <span className="font-bold">Entregable:</span> {challenge.deliverable}
                        </span>
                    </p>

                    {!done && <AiModeHint challenge={challenge} />}

                    {!done && challenge.guide && <ChallengeGuide guide={challenge.guide} />}

                    {!done && challenge.has_solution && (
                        <p className="text-muted-foreground mt-3 flex items-center gap-2 text-sm">
                            <Lock className="size-4 shrink-0" />
                            La solución se desbloquea cuando completás el desafío.
                        </p>
                    )}

                    {done && completion.reflection && (
                        <Disclosure title="Tu explicación" icon={CheckCircle2}>
                            <span className="font-bold">Qué decidiste:</span> {completion.reflection.decision}
                            {'\n\n'}
                            <span className="font-bold">Qué te costó:</span> {completion.reflection.difficulty}
                            {completion.reflection.next && (
                                <>
                                    {'\n\n'}
                                    <span className="font-bold">Qué harías distinto:</span> {completion.reflection.next}
                                </>
                            )}
                        </Disclosure>
                    )}

                    {done && challenge.solution && (
                        <Disclosure title="Compará con la solución" icon={KeyRound}>
                            {challenge.solution}
                        </Disclosure>
                    )}

                    {auth.user && done && (
                        <div className="mt-4 flex flex-wrap items-center gap-2">
                            {completion.evidence_url && (
                                <Button asChild variant="outline" size="sm">
                                    <a href={completion.evidence_url} target="_blank" rel="noopener noreferrer">
                                        <ExternalLink />
                                        Ver mi entrega
                                    </a>
                                </Button>
                            )}
                            <Button variant="ghost" size="sm" onClick={undo}>
                                <Undo2 />
                                Deshacer
                            </Button>
                        </div>
                    )}

                    {auth.user && !done && <CompletionForm challenge={challenge} />}
                </div>
            </div>
        </li>
    );
}
