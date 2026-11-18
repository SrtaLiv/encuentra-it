import { AreaIcon } from '@/components/area-icon';
import { CodingBadge } from '@/components/coding-badge';
import { FilterChip } from '@/components/filter-chip';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import PublicLayout from '@/layouts/public-layout';
import { type Career, type CareerSummary, type Challenge, type RandomType, type Resource, type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { ArrowRight, Dices, ExternalLink, Flag, Target } from 'lucide-react';
import { type ReactNode } from 'react';

type CareerLink = Pick<Career, 'id' | 'name' | 'slug'>;

type RandomProps =
    | { mode: RandomType | null; type: 'carrera'; item: CareerSummary | null }
    | { mode: RandomType | null; type: 'recurso'; item: (Resource & { careers: CareerLink[] }) | null }
    | { mode: RandomType | null; type: 'desafio'; item: (Challenge & { career: CareerLink }) | null };

const modes: { value: RandomType | null; label: string }[] = [
    { value: null, label: 'Sorpresa' },
    { value: 'carrera', label: 'Carrera' },
    { value: 'recurso', label: 'Recurso' },
    { value: 'desafio', label: 'Desafío' },
];

const typeLabels: Record<RandomType, string> = {
    carrera: 'Carrera',
    recurso: 'Recurso',
    desafio: 'Desafío',
};

function Eyebrow({ children }: { children: ReactNode }) {
    return <p className="text-brand inline-flex items-center gap-2 text-sm font-bold">{children}</p>;
}

function CareerResult({ career }: { career: CareerSummary }) {
    return (
        <>
            {career.area && (
                <Eyebrow>
                    <AreaIcon name={career.area.icon} className="size-4" />
                    {career.area.name}
                </Eyebrow>
            )}
            <h2 className="mt-2 text-3xl font-bold tracking-tight">{career.name}</h2>
            <p className="text-muted-foreground mt-3 text-lg">{career.summary}</p>
            <div className="mt-4 flex flex-wrap items-center gap-3 text-sm">
                <CodingBadge level={career.coding_level} />
                {!!career.challenges_count && (
                    <span className="text-muted-foreground inline-flex items-center gap-1.5">
                        <Target className="size-4" />
                        {career.challenges_count} desafíos
                    </span>
                )}
            </div>
            <Button asChild variant="outline" className="mt-6">
                <Link href={route('careers.show', career.slug)}>
                    Ver carrera
                    <ArrowRight />
                </Link>
            </Button>
        </>
    );
}

function ResourceResult({ resource }: { resource: Resource & { careers: CareerLink[] } }) {
    const { enums } = usePage<SharedData>().props;

    return (
        <>
            <Eyebrow>{enums.resourceTypes[resource.type]}</Eyebrow>
            <h2 className="mt-2 text-3xl font-bold tracking-tight">{resource.title}</h2>
            {resource.description && <p className="text-muted-foreground mt-3 text-lg">{resource.description}</p>}
            <div className="mt-4 flex flex-wrap gap-1.5">
                <Badge variant="outline">{enums.levels[resource.level]}</Badge>
                <Badge variant="outline">{resource.language === 'es' ? 'Español' : 'Inglés'}</Badge>
                <Badge variant="outline">{resource.is_free ? 'Gratis' : 'Pago'}</Badge>
            </div>
            {resource.careers.length > 0 && (
                <p className="text-muted-foreground mt-4 text-sm">
                    Sirve para{' '}
                    {resource.careers.map((career, index) => (
                        <span key={career.id}>
                            {index > 0 && ', '}
                            <Link href={route('careers.show', career.slug)} className="text-brand font-bold hover:underline">
                                {career.name}
                            </Link>
                        </span>
                    ))}
                </p>
            )}
            <Button asChild variant="outline" className="mt-6">
                <a href={resource.url} target="_blank" rel="noopener noreferrer">
                    Abrir recurso
                    <ExternalLink />
                </a>
            </Button>
        </>
    );
}

function ChallengeResult({ challenge }: { challenge: Challenge & { career: CareerLink } }) {
    const { enums } = usePage<SharedData>().props;

    return (
        <>
            <Eyebrow>
                <Target className="size-4" />
                Desafío de {challenge.career.name}
            </Eyebrow>
            <h2 className="mt-2 text-3xl font-bold tracking-tight">{challenge.title}</h2>
            <div className="mt-3">
                <Badge variant="outline">{enums.levels[challenge.level]}</Badge>
            </div>
            <p className="text-muted-foreground mt-4 text-lg leading-relaxed">{challenge.description}</p>
            <p className="mt-4 flex gap-2">
                <Flag className="text-brand mt-1 size-4 shrink-0" />
                <span>
                    <span className="font-bold">Entregable:</span> {challenge.deliverable}
                </span>
            </p>
            <Button asChild variant="outline" className="mt-6">
                <Link href={`${route('careers.show', challenge.career.slug)}#desafios`}>
                    Ver en la carrera
                    <ArrowRight />
                </Link>
            </Button>
        </>
    );
}

function Result(props: RandomProps) {
    if (!props.item) {
        return <p className="text-muted-foreground">Todavía no hay nada cargado acá.</p>;
    }

    switch (props.type) {
        case 'carrera':
            return <CareerResult career={props.item} />;
        case 'recurso':
            return <ResourceResult resource={props.item} />;
        case 'desafio':
            return <ChallengeResult challenge={props.item} />;
    }
}

export default function Random(props: RandomProps) {
    const { mode, type, item } = props;
    const next = route('random', { tipo: mode ?? undefined, sin: mode && item ? item.id : undefined });

    return (
        <PublicLayout>
            <Head title="Al azar" />

            <div className="mx-auto max-w-2xl px-4 py-12">
                <h1 className="flex items-center gap-3 text-3xl font-bold tracking-tight">
                    <Dices className="text-brand size-8" />
                    Al azar
                </h1>
                <p className="text-muted-foreground mt-2">Descubrí algo que no estabas buscando. Tocá el dado las veces que quieras.</p>

                <div className="mt-6 flex flex-wrap gap-2">
                    {modes.map((option) => (
                        <FilterChip key={option.label} href={route('random', { tipo: option.value ?? undefined })} active={mode === option.value}>
                            {option.label}
                        </FilterChip>
                    ))}
                </div>

                <article key={`${type}-${item?.id}`} className="bg-card card-raised mt-6 p-6 md:p-8">
                    {!mode && <p className="text-muted-foreground mb-4 text-xs font-bold tracking-wider uppercase">{typeLabels[type]}</p>}
                    <Result {...props} />
                </article>

                <Button asChild size="lg" className="mt-6 w-full sm:w-auto">
                    <Link href={next} preserveScroll>
                        <Dices />
                        Otro al azar
                    </Link>
                </Button>
            </div>
        </PublicLayout>
    );
}
