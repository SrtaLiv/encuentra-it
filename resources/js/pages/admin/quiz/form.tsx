import { AdminPage } from '@/components/admin/admin-page';
import { CareerPicker } from '@/components/admin/career-picker';
import { FormField } from '@/components/admin/form-field';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { type Career } from '@/types';
import { useForm } from '@inertiajs/react';
import { Plus, Trash2 } from 'lucide-react';
import { type FormEvent } from 'react';

type OptionInput = {
    id: number | null;
    text: string;
    career_ids: number[];
};

interface QuizFormProps {
    question: { id: number; text: string; position: number; options: OptionInput[] } | null;
    careers: Pick<Career, 'id' | 'name'>[];
}

const MAX_OPTIONS = 8;

export default function QuizForm({ question, careers }: QuizFormProps) {
    const { data, setData, post, put, processing, errors } = useForm({
        text: question?.text ?? '',
        position: question?.position ?? 0,
        options: question?.options ?? [
            { id: null, text: '', career_ids: [] },
            { id: null, text: '', career_ids: [] },
        ],
    });

    const errorBag = errors as Record<string, string>;

    const updateOption = (index: number, changes: Partial<OptionInput>) => {
        setData(
            'options',
            data.options.map((option, i) => (i === index ? { ...option, ...changes } : option)),
        );
    };

    const submit = (event: FormEvent) => {
        event.preventDefault();

        if (question) {
            put(route('admin.quiz.update', question.id));
            return;
        }

        post(route('admin.quiz.store'));
    };

    return (
        <AdminPage title={question ? 'Editar pregunta' : 'Nueva pregunta'} href={route('admin.quiz.index')}>
            <form onSubmit={submit} className="grid max-w-4xl gap-6">
                <div className="grid gap-6 sm:grid-cols-[1fr_8rem]">
                    <FormField label="Pregunta" htmlFor="text" error={errors.text}>
                        <Input id="text" value={data.text} onChange={(e) => setData('text', e.target.value)} required maxLength={255} />
                    </FormField>
                    <FormField label="Orden" htmlFor="position" error={errors.position}>
                        <Input
                            id="position"
                            type="number"
                            min={0}
                            max={999}
                            value={data.position}
                            onChange={(e) => setData('position', Number(e.target.value))}
                            required
                        />
                    </FormField>
                </div>

                <div className="grid gap-4">
                    <h2 className="font-extrabold">Opciones</h2>
                    <InputError message={errors.options} />
                    {data.options.map((option, index) => (
                        <div key={option.id ?? `new-${index}`} className="bg-card card-raised space-y-4 p-4">
                            <div className="flex gap-2">
                                <Input
                                    value={option.text}
                                    onChange={(e) => updateOption(index, { text: e.target.value })}
                                    placeholder={`Opción ${index + 1}`}
                                    aria-label={`Texto de la opción ${index + 1}`}
                                    required
                                    maxLength={255}
                                />
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="icon"
                                    disabled={data.options.length <= 2}
                                    onClick={() =>
                                        setData(
                                            'options',
                                            data.options.filter((_, i) => i !== index),
                                        )
                                    }
                                    aria-label="Quitar opción"
                                >
                                    <Trash2 className="text-destructive" />
                                </Button>
                            </div>
                            <div>
                                <p className="text-muted-foreground mb-2 text-xs">Suma puntos a:</p>
                                <CareerPicker
                                    careers={careers}
                                    selected={option.career_ids}
                                    onChange={(ids) => updateOption(index, { career_ids: ids })}
                                />
                            </div>
                            <InputError message={errorBag[`options.${index}.text`] ?? errorBag[`options.${index}.career_ids`]} />
                        </div>
                    ))}
                    {data.options.length < MAX_OPTIONS && (
                        <div>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => setData('options', [...data.options, { id: null, text: '', career_ids: [] }])}
                            >
                                <Plus />
                                Agregar opción
                            </Button>
                        </div>
                    )}
                </div>

                <div>
                    <Button disabled={processing}>{question ? 'Guardar cambios' : 'Crear pregunta'}</Button>
                </div>
            </form>
        </AdminPage>
    );
}
