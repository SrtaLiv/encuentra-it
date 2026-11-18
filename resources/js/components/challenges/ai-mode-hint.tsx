import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { type AiMode, type Challenge, type SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import { Brain, Check, Copy, GraduationCap, Sparkles } from 'lucide-react';
import { useState } from 'react';

const styles: Record<AiMode, { icon: typeof Brain; className: string }> = {
    none: { icon: Brain, className: 'bg-amber-50 border-amber-200 text-amber-900 dark:bg-amber-950/40 dark:border-amber-900 dark:text-amber-200' },
    tutor: { icon: GraduationCap, className: 'bg-sky/10 border-sky/30 text-foreground' },
    allowed: { icon: Sparkles, className: 'bg-primary/15 border-primary/40 text-foreground' },
};

function tutorPrompt(challenge: Challenge) {
    return [
        'Estoy aprendiendo y quiero resolver este desafío por mi cuenta.',
        'No me des la solución ni código completo. Haceme preguntas y dame una sola pista a la vez, solo cuando te la pida.',
        'Si me equivoco, no me corrijas directo: preguntame por qué elegí eso.',
        '',
        `Desafío: ${challenge.title}`,
        challenge.description,
    ].join('\n');
}

export function AiModeHint({ challenge }: { challenge: Challenge }) {
    const { enums } = usePage<SharedData>().props;
    const [copied, setCopied] = useState(false);
    const { icon: Icon, className } = styles[challenge.ai_mode];

    const copyPrompt = async () => {
        try {
            await navigator.clipboard.writeText(tutorPrompt(challenge));
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        } catch {
            setCopied(false);
        }
    };

    return (
        <div className={cn('mt-3 rounded-xl border-2 px-3 py-2 text-sm', className)}>
            <p className="flex gap-2">
                <Icon className="mt-0.5 size-4 shrink-0" />
                <span>
                    <span className="font-extrabold">{enums.aiModes[challenge.ai_mode]}.</span> {enums.aiModeHints[challenge.ai_mode]}
                </span>
            </p>
            {challenge.ai_mode === 'tutor' && (
                <Button type="button" variant="outline" size="sm" onClick={copyPrompt} className="mt-2 ml-6">
                    {copied ? <Check /> : <Copy />}
                    {copied ? 'Prompt copiado' : 'Copiar prompt de tutor'}
                </Button>
            )}
        </div>
    );
}
