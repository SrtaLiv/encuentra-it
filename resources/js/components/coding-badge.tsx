import { cn } from '@/lib/utils';
import { type CodingLevel, type SharedData } from '@/types';
import { usePage } from '@inertiajs/react';

const styles: Record<CodingLevel, string> = {
    none: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
    basic: 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300',
    core: 'bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300',
};

export function CodingBadge({ level, className }: { level: CodingLevel; className?: string }) {
    const { enums } = usePage<SharedData>().props;

    return (
        <span className={cn('inline-flex items-center rounded-lg px-2.5 py-0.5 text-xs font-bold', styles[level], className)}>
            {enums.codingLevels[level]}
        </span>
    );
}
