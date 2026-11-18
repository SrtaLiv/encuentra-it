import { FilterChip } from '@/components/filter-chip';
import { Pagination } from '@/components/pagination';
import { RandomButton } from '@/components/random-button';
import { ResourceCard } from '@/components/resource-card';
import { SearchInput } from '@/components/search-input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import PublicLayout from '@/layouts/public-layout';
import { type Career, type Level, type Paginated, type Resource, type ResourceType, type SharedData } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';

interface Filters {
    type?: ResourceType;
    level?: Level;
    language?: 'es' | 'en';
    career?: string;
    free?: string | boolean;
    q?: string;
}

interface ResourcesIndexProps {
    resources: Paginated<Resource>;
    careers: Pick<Career, 'id' | 'name' | 'slug'>[];
    filters: Filters;
}

const ALL = 'all';

export default function ResourcesIndex({ resources, careers, filters }: ResourcesIndexProps) {
    const { enums } = usePage<SharedData>().props;
    const url = route('resources.index');
    const withFilter = (changes: Filters) => route('resources.index', { ...filters, ...changes, page: undefined });
    const isFree = filters.free === true || filters.free === '1';

    const visit = (changes: Filters) => router.get(withFilter(changes), {}, { preserveState: true, preserveScroll: true });

    return (
        <PublicLayout>
            <Head title="Recursos" />

            <div className="mx-auto max-w-6xl px-4 py-12">
                <div className="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Recursos y desafíos</h1>
                        <p className="text-muted-foreground mt-2">
                            Cursos, guías, repositorios y desafíos curados por la comunidad. La mayoría son gratis.
                        </p>
                        <div className="mt-4 flex flex-wrap gap-2">
                            <RandomButton type="recurso">Recurso al azar</RandomButton>
                            <RandomButton type="desafio">Desafío al azar</RandomButton>
                        </div>
                    </div>
                    <SearchInput url={url} params={{ ...filters, q: undefined }} initial={filters.q} placeholder="Buscar recurso…" />
                </div>

                <div className="mt-8 flex flex-wrap gap-2">
                    <FilterChip href={withFilter({ type: undefined })} active={!filters.type}>
                        Todos
                    </FilterChip>
                    {(Object.entries(enums.resourceTypes) as [ResourceType, string][]).map(([value, label]) => (
                        <FilterChip key={value} href={withFilter({ type: value })} active={filters.type === value}>
                            {label}
                        </FilterChip>
                    ))}
                </div>

                <div className="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <Select value={filters.career ?? ALL} onValueChange={(value) => visit({ career: value === ALL ? undefined : value })}>
                        <SelectTrigger aria-label="Carrera">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={ALL}>Todas las carreras</SelectItem>
                            {careers.map((career) => (
                                <SelectItem key={career.id} value={career.slug}>
                                    {career.name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>

                    <Select value={filters.level ?? ALL} onValueChange={(value) => visit({ level: value === ALL ? undefined : (value as Level) })}>
                        <SelectTrigger aria-label="Nivel">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={ALL}>Todos los niveles</SelectItem>
                            {(Object.entries(enums.levels) as [Level, string][]).map(([value, label]) => (
                                <SelectItem key={value} value={value}>
                                    {label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>

                    <Select
                        value={filters.language ?? ALL}
                        onValueChange={(value) => visit({ language: value === ALL ? undefined : (value as 'es' | 'en') })}
                    >
                        <SelectTrigger aria-label="Idioma">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={ALL}>Cualquier idioma</SelectItem>
                            <SelectItem value="es">Español</SelectItem>
                            <SelectItem value="en">Inglés</SelectItem>
                        </SelectContent>
                    </Select>

                    <FilterChip href={withFilter({ free: isFree ? undefined : '1' })} active={isFree}>
                        Solo gratuitos
                    </FilterChip>
                </div>

                <p className="text-muted-foreground mt-6 text-sm">
                    {resources.total} {resources.total === 1 ? 'recurso' : 'recursos'}
                </p>

                {resources.data.length === 0 ? (
                    <div className="mt-12 text-center">
                        <p className="text-muted-foreground">No encontramos recursos con esos filtros.</p>
                        <Link href={url} className="text-brand mt-2 inline-block text-sm font-bold">
                            Limpiar filtros
                        </Link>
                    </div>
                ) : (
                    <div className="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        {resources.data.map((resource) => (
                            <ResourceCard key={resource.id} resource={resource} />
                        ))}
                    </div>
                )}

                <div className="mt-10">
                    <Pagination paginator={resources} />
                </div>
            </div>
        </PublicLayout>
    );
}
