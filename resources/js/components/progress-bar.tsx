import { cn } from '@/lib/utils';

export function ProgressBar({ value, label, className }: { value: number; label: string; className?: string }) {
    const percent = Math.min(100, Math.max(0, Math.round(value)));

    return (
        <div
            className={cn('bg-muted h-4 overflow-hidden rounded-full', className)}
            role="progressbar"
            aria-label={label}
            aria-valuenow={percent}
            aria-valuemin={0}
            aria-valuemax={100}
        >
            <div className="bg-primary relative h-full rounded-full transition-all" style={{ width: `${percent}%` }}>
                {percent > 0 && <span className="absolute inset-x-2 top-1 h-1 rounded-full bg-white/40" />}
            </div>
        </div>
    );
}
