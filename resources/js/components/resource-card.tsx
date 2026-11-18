import { Badge } from '@/components/ui/badge';
import { type Resource, type SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import { ExternalLink } from 'lucide-react';

export function ResourceCard({ resource }: { resource: Resource }) {
    const { enums } = usePage<SharedData>().props;

    return (
        <a
            href={resource.url}
            target="_blank"
            rel="noopener noreferrer"
            className="group bg-card hover:border-primary/60 card-raised flex h-full flex-col gap-2 p-4 transition-colors"
        >
            <div className="flex items-start justify-between gap-2">
                <h3 className="leading-tight font-bold">{resource.title}</h3>
                <ExternalLink className="text-muted-foreground group-hover:text-brand mt-0.5 size-4 shrink-0" />
            </div>
            {resource.description && <p className="text-muted-foreground flex-1 text-sm">{resource.description}</p>}
            <div className="flex flex-wrap gap-1.5 pt-1">
                <Badge variant="secondary">{enums.resourceTypes[resource.type]}</Badge>
                <Badge variant="outline">{enums.levels[resource.level]}</Badge>
                <Badge variant="outline">{resource.language === 'es' ? 'Español' : 'Inglés'}</Badge>
                {!resource.is_free && <Badge variant="outline">Pago</Badge>}
            </div>
        </a>
    );
}
