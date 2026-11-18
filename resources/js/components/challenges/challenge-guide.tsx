import { Button } from '@/components/ui/button';
import { type ChallengeGuide as Guide } from '@/types';
import { ChevronDown, Lightbulb, ListChecks, Map } from 'lucide-react';
import { useState } from 'react';

// hints open one at a time so people try first and only ask for as much help as they need
function Hints({ hints }: { hints: string[] }) {
    const [shown, setShown] = useState(0);

    return (
        <div>
            <p className="mb-2 flex items-center gap-2 font-extrabold">
                <Lightbulb className="text-brand size-4" />
                ¿Te trabaste?
            </p>
            <ol className="space-y-2">
                {hints.slice(0, shown).map((hint, index) => (
                    <li key={index} className="bg-primary/10 border-primary/30 rounded-xl border-2 px-3 py-2">
                        <span className="font-extrabold">Pista {index + 1}:</span> {hint}
                    </li>
                ))}
            </ol>
            {shown < hints.length && (
                <Button type="button" variant="outline" size="sm" className="mt-2" onClick={() => setShown(shown + 1)}>
                    <Lightbulb />
                    Ver pista {shown + 1} de {hints.length}
                </Button>
            )}
        </div>
    );
}

export function ChallengeGuide({ guide }: { guide: Guide }) {
    const hasContent = guide.before.length + guide.steps.length + guide.hints.length > 0;

    if (!hasContent) {
        return null;
    }

    return (
        <details className="group bg-card mt-3 rounded-xl border-2 text-sm">
            <summary className="flex cursor-pointer list-none items-center gap-2 px-3 py-2.5 font-extrabold [&::-webkit-details-marker]:hidden">
                <Map className="text-brand size-4" />
                ¿Cómo lo encaro?
                <ChevronDown className="text-muted-foreground ml-auto size-4 transition-transform group-open:rotate-180" />
            </summary>

            <div className="space-y-5 border-t-2 px-3 py-4">
                {guide.before.length > 0 && (
                    <div>
                        <p className="mb-2 flex items-center gap-2 font-extrabold">
                            <ListChecks className="text-brand size-4" />
                            Antes de empezar
                        </p>
                        <ul className="text-muted-foreground list-disc space-y-1 pl-6">
                            {guide.before.map((item, index) => (
                                <li key={index}>{item}</li>
                            ))}
                        </ul>
                    </div>
                )}

                {guide.steps.length > 0 && (
                    <div>
                        <p className="mb-2 font-extrabold">Paso a paso</p>
                        <ol className="space-y-2">
                            {guide.steps.map((step, index) => (
                                <li key={index} className="flex gap-3">
                                    <span className="bg-muted flex size-6 shrink-0 items-center justify-center rounded-full text-xs font-extrabold tabular-nums">
                                        {index + 1}
                                    </span>
                                    <span className="pt-0.5">{step}</span>
                                </li>
                            ))}
                        </ol>
                    </div>
                )}

                {guide.hints.length > 0 && <Hints hints={guide.hints} />}
            </div>
        </details>
    );
}
