import { AreaIcon } from '@/components/area-icon';
import { ChallengeList } from '@/components/challenge-list';
import { CodingBadge } from '@/components/coding-badge';
import { ResourceCard } from '@/components/resource-card';
import { Roadmap } from '@/components/roadmap';
import { TableOfContents } from '@/components/table-of-contents';
import { Badge } from '@/components/ui/badge';
import PublicLayout from '@/layouts/public-layout';
import { type Career, type ChallengeCompletion, type Resource } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Briefcase, CheckCircle2, Target } from 'lucide-react';
import { type ReactNode } from 'react';

interface CareerShowProps {
    career: Required<Pick<Career, 'area' | 'steps' | 'challenges' | 'resources'>> & Career;
    completedStepIds: number[];
    completedChallenges: Record<number, ChallengeCompletion>;
}

interface SectionData {
    id: string;
    title: string;
    visible: boolean;
    content: ReactNode;
}

function Section({ id, title, children }: { id: string; title: string; children: ReactNode }) {
    return (
        <section id={id} className="mt-12 scroll-mt-24">
            <h2 className="mb-4 text-xl font-extrabold">{title}</h2>
            {children}
        </section>
    );
}

function ResourceGrid({ resources }: { resources: Resource[] }) {
    return (
        <div className="grid gap-3 sm:grid-cols-2">
            {resources.map((resource) => (
                <ResourceCard key={resource.id} resource={resource} />
            ))}
        </div>
    );
}

export default function CareerShow({ career, completedStepIds, completedChallenges }: CareerShowProps) {
    const practicePlatforms = career.resources.filter((resource) => resource.type === 'challenge');
    const jobBoards = career.resources.filter((resource) => resource.type === 'job_board');
    const learning = career.resources.filter((resource) => resource.type !== 'challenge' && resource.type !== 'job_board');

    const sections: SectionData[] = [
        {
            id: 'que-hace',
            title: '¿Qué hace?',
            visible: true,
            content: <p className="leading-relaxed whitespace-pre-line">{career.description}</p>,
        },
        {
            id: 'dia-de-trabajo',
            title: 'Un día de trabajo',
            visible: career.daily_tasks.length > 0,
            content: (
                <ul className="grid gap-3 sm:grid-cols-2">
                    {career.daily_tasks.map((task) => (
                        <li key={task} className="flex gap-2">
                            <CheckCircle2 className="text-brand mt-0.5 size-5 shrink-0" />
                            <span>{task}</span>
                        </li>
                    ))}
                </ul>
            ),
        },
        {
            id: 'que-aprender',
            title: 'Qué tenés que aprender',
            visible: career.skills.length > 0,
            content: (
                <div className="flex flex-wrap gap-2">
                    {career.skills.map((skill) => (
                        <Badge key={skill} variant="secondary" className="px-3 py-1 text-sm font-bold">
                            {skill}
                        </Badge>
                    ))}
                </div>
            ),
        },
        {
            id: 'desafios',
            title: 'Desafíos prácticos',
            visible: true,
            content: (
                <>
                    <p className="text-muted-foreground -mt-2 mb-4">
                        Aprendé haciendo. Cada desafío tiene una tarea concreta y un entregable para saber que lo terminaste.
                    </p>
                    <ChallengeList challenges={career.challenges} completed={completedChallenges} />
                </>
            ),
        },
        {
            id: 'ruta',
            title: 'Ruta de aprendizaje',
            visible: true,
            content: <Roadmap steps={career.steps} completedStepIds={completedStepIds} />,
        },
        {
            id: 'recursos',
            title: 'Recursos para aprender',
            visible: learning.length > 0,
            content: <ResourceGrid resources={learning} />,
        },
        {
            id: 'mas-practica',
            title: 'Más práctica en otras plataformas',
            visible: practicePlatforms.length > 0,
            content: <ResourceGrid resources={practicePlatforms} />,
        },
        {
            id: 'salida-laboral',
            title: 'Salida laboral',
            visible: true,
            content: (
                <>
                    <div className="bg-card card-raised p-6">
                        {career.job_outlook && <p className="leading-relaxed">{career.job_outlook}</p>}
                        {career.job_titles.length > 0 && (
                            <>
                                <p className="mt-4 flex items-center gap-2 text-sm font-bold">
                                    <Briefcase className="text-brand size-4" />
                                    Buscá estos puestos en las bolsas de empleo:
                                </p>
                                <div className="mt-2 flex flex-wrap gap-2">
                                    {career.job_titles.map((title) => (
                                        <Badge key={title} variant="outline" className="text-sm">
                                            {title}
                                        </Badge>
                                    ))}
                                </div>
                            </>
                        )}
                    </div>
                    {jobBoards.length > 0 && (
                        <div className="mt-4">
                            <ResourceGrid resources={jobBoards} />
                        </div>
                    )}
                </>
            ),
        },
    ];

    const visibleSections = sections.filter((section) => section.visible);

    return (
        <PublicLayout>
            <Head title={career.name} />

            <div className="mx-auto max-w-6xl px-4 py-12 lg:grid lg:grid-cols-[13rem_minmax(0,1fr)] lg:gap-12">
                <aside className="hidden lg:block">
                    <TableOfContents items={visibleSections} className="sticky top-24 mt-36" />
                </aside>

                <div className="max-w-4xl">
                    <Link
                        href={route('careers.index')}
                        className="text-muted-foreground hover:text-foreground inline-flex items-center gap-1 text-sm"
                    >
                        <ArrowLeft className="size-4" />
                        Todas las carreras
                    </Link>

                    <header className="mt-6">
                        <Link
                            href={route('careers.index', { area: career.area.slug })}
                            className="text-brand inline-flex items-center gap-2 text-sm font-bold"
                        >
                            <AreaIcon name={career.area.icon} className="size-4" />
                            {career.area.name}
                        </Link>
                        <h1 className="mt-2 text-3xl font-bold tracking-tight md:text-4xl">{career.name}</h1>
                        <p className="text-muted-foreground mt-3 text-lg">{career.summary}</p>
                        <div className="mt-4 flex flex-wrap items-center gap-3">
                            <CodingBadge level={career.coding_level} />
                            {career.challenges.length > 0 && (
                                <a href="#desafios" className="text-brand inline-flex items-center gap-1.5 text-sm font-bold hover:underline">
                                    <Target className="size-4" />
                                    {career.challenges.length} desafíos prácticos
                                </a>
                            )}
                        </div>
                    </header>

                    {visibleSections.map((section) => (
                        <Section key={section.id} id={section.id} title={section.title}>
                            {section.content}
                        </Section>
                    ))}
                </div>
            </div>
        </PublicLayout>
    );
}
