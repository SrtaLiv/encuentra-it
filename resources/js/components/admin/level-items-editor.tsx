import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { type AiMode, type ChallengeGuide, type Level, type SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import { ArrowDown, ArrowUp, Plus, Trash2 } from 'lucide-react';

export type LevelItemInput = {
    id: number | null;
    level: Level;
    title: string;
    description: string;
    deliverable?: string;
    ai_mode?: AiMode;
    solution?: string | null;
    guide?: ChallengeGuide | null;
};

interface LevelItemsEditorProps {
    items: LevelItemInput[];
    onChange: (items: LevelItemInput[]) => void;
    errors: Record<string, string>;
    // key used by the backend for validation errors, e.g. "steps" or "challenges"
    field: string;
    itemLabel: string;
    withDeliverable?: boolean;
}

const GUIDE_FIELDS: { key: keyof ChallengeGuide; label: string; placeholder: string }[] = [
    { key: 'before', label: 'Antes de empezar', placeholder: 'Qué tiene que saber o tener (uno por línea)' },
    { key: 'steps', label: 'Paso a paso', placeholder: 'Cómo encarar el proceso, sin dar la respuesta (uno por línea)' },
    { key: 'hints', label: 'Pistas', placeholder: 'Se muestran de a una cuando la persona se traba (una por línea)' },
];

// one textarea line per item; blank lines are dropped on save
function GuideEditor({ guide, onChange }: { guide: ChallengeGuide | null; onChange: (guide: ChallengeGuide) => void }) {
    const current: ChallengeGuide = guide ?? { before: [], steps: [], hints: [] };
    const filled = current.before.length + current.steps.length + current.hints.length > 0;

    return (
        <details className="rounded-xl border-2 text-sm">
            <summary className="cursor-pointer px-3 py-2 font-bold">Guía "¿Cómo lo encaro?" {filled ? '(cargada)' : '(vacía)'}</summary>
            <div className="grid gap-3 border-t-2 p-3">
                {GUIDE_FIELDS.map((field) => (
                    <label key={field.key} className="grid gap-1">
                        <span className="text-xs font-bold">{field.label}</span>
                        <Textarea
                            value={current[field.key].join('\n')}
                            onChange={(e) => onChange({ ...current, [field.key]: e.target.value.split('\n') })}
                            placeholder={field.placeholder}
                            rows={3}
                        />
                    </label>
                ))}
            </div>
        </details>
    );
}

export function LevelItemsEditor({ items, onChange, errors, field, itemLabel, withDeliverable = false }: LevelItemsEditorProps) {
    const { enums } = usePage<SharedData>().props;

    const update = (index: number, changes: Partial<LevelItemInput>) => {
        onChange(items.map((item, i) => (i === index ? { ...item, ...changes } : item)));
    };

    const move = (index: number, direction: -1 | 1) => {
        const target = index + direction;

        if (target < 0 || target >= items.length) {
            return;
        }

        const next = [...items];
        [next[index], next[target]] = [next[target], next[index]];
        onChange(next);
    };

    const add = () => {
        const level = items.at(-1)?.level ?? 'beginner';
        onChange([
            ...items,
            {
                id: null,
                level,
                title: '',
                description: '',
                ...(withDeliverable ? { deliverable: '', ai_mode: 'tutor' as AiMode, solution: '' } : {}),
            },
        ]);
    };

    const errorFor = (index: number) =>
        ['title', 'level', 'description', 'deliverable', 'ai_mode', 'solution'].map((key) => errors[`${field}.${index}.${key}`]).find(Boolean);

    return (
        <div className="space-y-3">
            {items.map((item, index) => (
                <div key={item.id ?? `new-${index}`} className="bg-card card-raised space-y-3 p-4">
                    <div className="flex flex-col gap-3 sm:flex-row">
                        <Select value={item.level} onValueChange={(value) => update(index, { level: value as Level })}>
                            <SelectTrigger className="sm:w-44" aria-label="Nivel">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                {(Object.entries(enums.levels) as [Level, string][]).map(([value, label]) => (
                                    <SelectItem key={value} value={value}>
                                        {label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <Input
                            value={item.title}
                            onChange={(e) => update(index, { title: e.target.value })}
                            placeholder={`Título del ${itemLabel}`}
                            aria-label={`Título del ${itemLabel}`}
                            maxLength={255}
                            required
                        />
                        <div className="flex shrink-0">
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                onClick={() => move(index, -1)}
                                disabled={index === 0}
                                aria-label="Subir"
                            >
                                <ArrowUp />
                            </Button>
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                onClick={() => move(index, 1)}
                                disabled={index === items.length - 1}
                                aria-label="Bajar"
                            >
                                <ArrowDown />
                            </Button>
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                onClick={() => onChange(items.filter((_, i) => i !== index))}
                                aria-label={`Quitar ${itemLabel}`}
                            >
                                <Trash2 className="text-destructive" />
                            </Button>
                        </div>
                    </div>
                    <Textarea
                        value={item.description}
                        onChange={(e) => update(index, { description: e.target.value })}
                        placeholder={withDeliverable ? 'Instrucciones: qué tiene que hacer la persona' : 'Descripción (opcional)'}
                        aria-label={`Descripción del ${itemLabel}`}
                        rows={withDeliverable ? 3 : 2}
                        maxLength={withDeliverable ? 3000 : 2000}
                        required={withDeliverable}
                    />
                    {withDeliverable && (
                        <Input
                            value={item.deliverable ?? ''}
                            onChange={(e) => update(index, { deliverable: e.target.value })}
                            placeholder="Entregable: cómo sabe que terminó (ej: link al video publicado)"
                            aria-label="Entregable"
                            maxLength={500}
                            required
                        />
                    )}
                    {withDeliverable && (
                        <div className="grid gap-3 sm:grid-cols-[14rem_1fr]">
                            <Select value={item.ai_mode ?? 'tutor'} onValueChange={(value) => update(index, { ai_mode: value as AiMode })}>
                                <SelectTrigger aria-label="Uso de IA">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    {(Object.entries(enums.aiModes) as [AiMode, string][]).map(([value, label]) => (
                                        <SelectItem key={value} value={value}>
                                            {label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <Textarea
                                value={item.solution ?? ''}
                                onChange={(e) => update(index, { solution: e.target.value })}
                                placeholder="Solución (opcional): se muestra solo después de completar el desafío"
                                aria-label="Solución"
                                rows={2}
                                maxLength={3000}
                            />
                        </div>
                    )}
                    {withDeliverable && <GuideEditor guide={item.guide ?? null} onChange={(guide) => update(index, { guide })} />}
                    <InputError message={errorFor(index)} />
                </div>
            ))}
            <Button type="button" variant="outline" onClick={add}>
                <Plus />
                Agregar {itemLabel}
            </Button>
        </div>
    );
}
