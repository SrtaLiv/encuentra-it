import { FormField } from '@/components/admin/form-field';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { type Challenge } from '@/types';
import { useForm } from '@inertiajs/react';
import { CheckCircle2 } from 'lucide-react';
import { type FormEvent, useId, useState } from 'react';

const MIN_LENGTH = 20;

export function CompletionForm({ challenge }: { challenge: Challenge }) {
    const [open, setOpen] = useState(false);
    const id = useId();
    const { data, setData, post, processing, errors, reset } = useForm({
        evidence_url: '',
        reflection: { decision: '', difficulty: '', next: '' },
    });
    const errorBag = errors as Record<string, string>;

    const setReflection = (key: keyof typeof data.reflection, value: string) => {
        setData('reflection', { ...data.reflection, [key]: value });
    };

    const submit = (event: FormEvent) => {
        event.preventDefault();
        post(route('challenges.complete', challenge.id), {
            preserveScroll: true,
            onSuccess: () => {
                reset();
                setOpen(false);
            },
        });
    };

    if (!open) {
        return (
            <Button type="button" className="mt-4" onClick={() => setOpen(true)}>
                <CheckCircle2 />
                Lo hice
            </Button>
        );
    }

    const remaining = (value: string) => Math.max(0, MIN_LENGTH - value.trim().length);

    return (
        <form onSubmit={submit} className="bg-background/60 mt-4 space-y-4 rounded-xl border-2 p-4">
            <div>
                <p className="font-extrabold">Antes de marcarlo, explicalo con tus palabras</p>
                <p className="text-muted-foreground text-sm">
                    Si lo podés explicar, lo aprendiste. Esto queda en tu portfolio para usar en entrevistas.
                </p>
            </div>

            <FormField
                label="¿Qué decidiste y por qué?"
                htmlFor={`${id}-decision`}
                error={errorBag['reflection.decision']}
                hint={
                    remaining(data.reflection.decision) > 0
                        ? `Mínimo ${MIN_LENGTH} caracteres (faltan ${remaining(data.reflection.decision)}).`
                        : undefined
                }
            >
                <Textarea
                    id={`${id}-decision`}
                    value={data.reflection.decision}
                    onChange={(e) => setReflection('decision', e.target.value)}
                    placeholder="Ej: elegí probar primero los casos de error porque…"
                    rows={2}
                    maxLength={1000}
                    required
                />
            </FormField>

            <FormField
                label="¿Qué te costó o qué salió mal?"
                htmlFor={`${id}-difficulty`}
                error={errorBag['reflection.difficulty']}
                hint={
                    remaining(data.reflection.difficulty) > 0
                        ? `Mínimo ${MIN_LENGTH} caracteres (faltan ${remaining(data.reflection.difficulty)}).`
                        : undefined
                }
            >
                <Textarea
                    id={`${id}-difficulty`}
                    value={data.reflection.difficulty}
                    onChange={(e) => setReflection('difficulty', e.target.value)}
                    placeholder="Ej: no sabía cómo… y lo resolví…"
                    rows={2}
                    maxLength={1000}
                    required
                />
            </FormField>

            <FormField label="¿Qué harías distinto? (opcional)" htmlFor={`${id}-next`} error={errorBag['reflection.next']}>
                <Textarea
                    id={`${id}-next`}
                    value={data.reflection.next}
                    onChange={(e) => setReflection('next', e.target.value)}
                    rows={2}
                    maxLength={1000}
                />
            </FormField>

            <FormField label="Link a tu entrega (opcional)" htmlFor={`${id}-url`} error={errors.evidence_url}>
                <Input
                    id={`${id}-url`}
                    type="url"
                    value={data.evidence_url}
                    onChange={(e) => setData('evidence_url', e.target.value)}
                    placeholder="https://"
                    maxLength={500}
                />
            </FormField>

            <div className="flex flex-wrap gap-2">
                <Button disabled={processing}>
                    <CheckCircle2 />
                    Completar desafío
                </Button>
                <Button type="button" variant="ghost" onClick={() => setOpen(false)} disabled={processing}>
                    Cancelar
                </Button>
            </div>
        </form>
    );
}
