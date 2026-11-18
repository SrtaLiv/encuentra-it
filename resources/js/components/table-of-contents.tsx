import { cn } from '@/lib/utils';
import { type MouseEvent, useEffect, useMemo, useState } from 'react';

export interface TocItem {
    id: string;
    title: string;
}

// a section is active once its top passes this line, just below the sticky header
const ACTIVE_OFFSET = 120;

function useActiveSection(ids: string[]) {
    const [activeId, setActiveId] = useState(ids[0]);

    useEffect(() => {
        const update = () => {
            const atBottom = window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 2;
            if (atBottom) {
                setActiveId(ids[ids.length - 1]);
                return;
            }

            const passed = ids.filter((id) => {
                const element = document.getElementById(id);
                return element !== null && element.getBoundingClientRect().top <= ACTIVE_OFFSET;
            });
            setActiveId(passed.at(-1) ?? ids[0]);
        };

        update();
        window.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update);

        return () => {
            window.removeEventListener('scroll', update);
            window.removeEventListener('resize', update);
        };
    }, [ids]);

    return activeId;
}

export function TableOfContents({ items, className }: { items: TocItem[]; className?: string }) {
    const ids = useMemo(() => items.map((item) => item.id), [items]);
    const activeId = useActiveSection(ids);

    const handleClick = (event: MouseEvent<HTMLAnchorElement>, id: string) => {
        const element = document.getElementById(id);
        if (!element) {
            return;
        }

        event.preventDefault();
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        element.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth' });
        history.replaceState(null, '', `#${id}`);
    };

    if (items.length === 0) {
        return null;
    }

    return (
        <nav aria-label="En esta página" className={className}>
            <p className="text-muted-foreground mb-3 text-xs font-bold tracking-wider uppercase">En esta página</p>
            <ul className="border-border space-y-1 border-l">
                {items.map((item) => (
                    <li key={item.id}>
                        <a
                            href={`#${item.id}`}
                            onClick={(event) => handleClick(event, item.id)}
                            aria-current={activeId === item.id ? 'location' : undefined}
                            className={cn(
                                '-ml-px block border-l-2 border-transparent py-1 pl-4 text-sm transition-colors',
                                activeId === item.id ? 'border-brand text-foreground font-bold' : 'text-muted-foreground hover:text-foreground',
                            )}
                        >
                            {item.title}
                        </a>
                    </li>
                ))}
            </ul>
        </nav>
    );
}
