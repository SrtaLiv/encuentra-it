import InputError from '@/components/input-error';
import { ProgressBar } from '@/components/progress-bar';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/public-layout';
import { cn } from '@/lib/utils';
import { Head, router } from '@inertiajs/react';
import { ArrowLeft, LoaderCircle } from 'lucide-react';
import { useState } from 'react';

interface Question {
    id: number;
    text: string;
    options: { id: number; text: string }[];
}

export default function QuizShow({ questions }: { questions: Question[] }) {
    const [current, setCurrent] = useState(0);
    const [answers, setAnswers] = useState<Record<number, number>>({});
    const [processing, setProcessing] = useState(false);
    const [error, setError] = useState<string>();

    if (questions.length === 0) {
        return (
            <PublicLayout>
                <Head title="Test vocacional" />
                <div className="mx-auto max-w-2xl px-4 py-24 text-center">
                    <h1 className="text-2xl font-bold">El test todavía no está disponible</h1>
                    <p className="text-muted-foreground mt-2">Volvé pronto, estamos preparando las preguntas.</p>
                </div>
            </PublicLayout>
        );
    }

    const question = questions[current];
    const isLast = current === questions.length - 1;
    const progress = Math.round((Object.keys(answers).length / questions.length) * 100);

    const submit = (finalAnswers: Record<number, number>) => {
        router.post(
            route('quiz.store'),
            { answers: finalAnswers },
            {
                onStart: () => setProcessing(true),
                onFinish: () => setProcessing(false),
                onError: (errors) => setError(errors.answers ?? 'Algo salió mal. Probá de nuevo.'),
            },
        );
    };

    const choose = (optionId: number) => {
        if (processing) {
            return;
        }

        const next = { ...answers, [question.id]: optionId };
        setAnswers(next);

        if (isLast) {
            submit(next);
            return;
        }

        setCurrent(current + 1);
    };

    return (
        <PublicLayout>
            <Head title="Test vocacional" />

            <div className="mx-auto max-w-2xl px-4 py-12 md:py-20">
                <div className="mb-8">
                    <div className="text-muted-foreground mb-2 flex justify-between text-sm">
                        <span>
                            Pregunta {current + 1} de {questions.length}
                        </span>
                        <span>{progress}%</span>
                    </div>
                    <ProgressBar value={progress} label="Progreso del test" />
                </div>

                <h1 className="text-2xl font-bold tracking-tight text-balance md:text-3xl">{question.text}</h1>

                <div className="mt-8 grid gap-3">
                    {question.options.map((option) => (
                        <button
                            key={option.id}
                            type="button"
                            disabled={processing}
                            onClick={() => choose(option.id)}
                            className={cn(
                                'bg-card hover:bg-accent card-raised p-4 text-left font-bold transition-colors disabled:opacity-60',
                                answers[question.id] === option.id && 'border-sky bg-sky/10 text-link',
                            )}
                        >
                            {option.text}
                        </button>
                    ))}
                </div>

                <InputError message={error} className="mt-4" />

                <div className="mt-8 flex items-center justify-between">
                    <Button variant="ghost" onClick={() => setCurrent(current - 1)} disabled={current === 0 || processing}>
                        <ArrowLeft />
                        Anterior
                    </Button>
                    {processing && (
                        <span className="text-muted-foreground flex items-center gap-2 text-sm">
                            <LoaderCircle className="size-4 animate-spin" />
                            Calculando tu resultado…
                        </span>
                    )}
                    {!processing && answers[question.id] && !isLast && (
                        <Button variant="outline" onClick={() => setCurrent(current + 1)}>
                            Siguiente
                        </Button>
                    )}
                </div>
            </div>
        </PublicLayout>
    );
}
