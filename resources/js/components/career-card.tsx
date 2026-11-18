import { AreaIcon } from '@/components/area-icon';
import { CodingBadge } from '@/components/coding-badge';
import { type CareerSummary } from '@/types';
import { Link } from '@inertiajs/react';
import { ArrowRight, Target } from 'lucide-react';

export function CareerCard({ career, score }: { career: CareerSummary; score?: number }) {
    return (
        <Link
            href={route('careers.show', career.slug)}
            className="group bg-card hover:border-primary/60 card-raised flex h-full flex-col gap-3 p-5 transition-colors"
        >
            <div className="flex items-start justify-between gap-3">
                <div className="flex items-center gap-2">
                    {career.area && (
                        <span className="bg-primary/10 text-brand flex size-8 shrink-0 items-center justify-center rounded-lg">
                            <AreaIcon name={career.area.icon} className="size-4" />
                        </span>
                    )}
                    <h3 className="leading-tight font-extrabold">{career.name}</h3>
                </div>
                {score !== undefined && <span className="text-brand shrink-0 text-lg font-bold tabular-nums">{score}%</span>}
            </div>
            <p className="text-muted-foreground flex-1 text-sm">{career.summary}</p>
            <div className="flex items-center justify-between">
                <div className="flex flex-wrap items-center gap-2">
                    <CodingBadge level={career.coding_level} />
                    {!!career.challenges_count && (
                        <span className="text-muted-foreground inline-flex items-center gap-1 text-xs">
                            <Target className="size-3.5" />
                            {career.challenges_count} desafíos
                        </span>
                    )}
                </div>
                <ArrowRight className="text-muted-foreground group-hover:text-brand size-4 transition-colors" />
            </div>
        </Link>
    );
}
