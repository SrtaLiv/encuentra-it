import { cn } from '@/lib/utils';
import { type Paginated } from '@/types';
import { Link } from '@inertiajs/react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

const itemClass = 'inline-flex h-10 min-w-10 items-center justify-center gap-1 rounded-xl border-2 px-3 text-sm font-bold tabular-nums';

export function Pagination({ paginator }: { paginator: Paginated<unknown> }) {
    if (paginator.last_page <= 1) {
        return null;
    }

    const lastIndex = paginator.links.length - 1;

    return (
        <nav className="flex flex-wrap justify-center gap-1.5" aria-label="Paginación">
            {paginator.links.map((link, index) => {
                // first and last links are always previous / next, whatever their translated label
                const isPrevious = index === 0;
                const isNext = index === lastIndex;
                const content = isPrevious ? (
                    <>
                        <ChevronLeft className="size-4" />
                        <span className="sr-only sm:not-sr-only">Anterior</span>
                    </>
                ) : isNext ? (
                    <>
                        <span className="sr-only sm:not-sr-only">Siguiente</span>
                        <ChevronRight className="size-4" />
                    </>
                ) : (
                    link.label
                );

                if (!link.url) {
                    return (
                        <span key={index} className={cn(itemClass, 'text-muted-foreground opacity-50')} aria-disabled="true">
                            {content}
                        </span>
                    );
                }

                return (
                    <Link
                        key={index}
                        href={link.url}
                        preserveScroll
                        aria-current={link.active ? 'page' : undefined}
                        className={cn(itemClass, link.active ? 'bg-primary text-primary-foreground border-primary' : 'bg-card hover:bg-accent')}
                    >
                        {content}
                    </Link>
                );
            })}
        </nav>
    );
}
