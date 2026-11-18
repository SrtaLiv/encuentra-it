import { cn } from '@/lib/utils';
import { Link } from '@inertiajs/react';
import { type ReactNode } from 'react';

export function FilterChip({ href, active, children }: { href: string; active: boolean; children: ReactNode }) {
    return (
        <Link
            href={href}
            preserveScroll
            preserveState
            className={cn(
                'inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-sm transition-colors',
                active ? 'bg-primary text-primary-foreground border-primary' : 'hover:bg-accent',
            )}
        >
            {children}
        </Link>
    );
}
