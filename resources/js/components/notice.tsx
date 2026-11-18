import { cn } from '@/lib/utils';
import { type ReactNode } from 'react';

export function Notice({ children, className }: { children: ReactNode; className?: string }) {
    return <p className={cn('bg-sky/10 border-sky/30 text-foreground rounded-2xl border-2 px-4 py-3 text-sm', className)}>{children}</p>;
}
