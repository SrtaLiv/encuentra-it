import { AreaIcon } from '@/components/area-icon';
import { CareerCard } from '@/components/career-card';
import { FilterChip } from '@/components/filter-chip';
import { RandomButton } from '@/components/random-button';
import { SearchInput } from '@/components/search-input';
import PublicLayout from '@/layouts/public-layout';
import { type Area, type CareerSummary, type CodingLevel, type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

interface Filters {
    area?: string;
    coding?: CodingLevel;
    q?: string;
}

interface CareersIndexProps {
    careers: CareerSummary[];
    areas: Pick<Area, 'id' | 'name' | 'slug' | 'icon'>[];
    filters: Filters;
}

export default function CareersIndex({ careers, areas, filters }: CareersIndexProps) {
    const { enums } = usePage<SharedData>().props;
    const url = route('careers.index');
    const withFilter = (changes: Filters) => route('careers.index', { ...filters, ...changes });

    return (
        <PublicLayout>
            <Head title="Carreras" />

            <div className="mx-auto max-w-6xl px-4 py-12">
                <div className="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Carreras en tecnología</h1>
                        <p className="text-muted-foreground mt-2">Conocé qué hace cada profesional, si necesita programar y cómo empezar.</p>
                        <div className="mt-4">
                            <RandomButton type="carrera">Carrera al azar</RandomButton>
                        </div>
                    </div>
                    <SearchInput
                        url={url}
                        params={{ area: filters.area, coding: filters.coding }}
                        initial={filters.q}
                        placeholder="Buscar carrera…"
                    />
                </div>

                <div className="mt-8 flex flex-wrap gap-2">
                    <FilterChip href={withFilter({ area: undefined })} active={!filters.area}>
                        Todas las áreas
                    </FilterChip>
                    {areas.map((area) => (
                        <FilterChip key={area.id} href={withFilter({ area: area.slug })} active={filters.area === area.slug}>
                            <AreaIcon name={area.icon} className="size-3.5" />
                            {area.name}
                        </FilterChip>
                    ))}
                </div>

                <div className="mt-3 flex flex-wrap gap-2">
                    <FilterChip href={withFilter({ coding: undefined })} active={!filters.coding}>
                        Cualquier nivel de programación
                    </FilterChip>
                    {(Object.entries(enums.codingLevels) as [CodingLevel, string][]).map(([value, label]) => (
                        <FilterChip key={value} href={withFilter({ coding: value })} active={filters.coding === value}>
                            {label}
                        </FilterChip>
                    ))}
                </div>

                {careers.length === 0 ? (
                    <div className="mt-16 text-center">
                        <p className="text-muted-foreground">No encontramos carreras con esos filtros.</p>
                        <Link href={url} className="text-brand mt-2 inline-block text-sm font-bold">
                            Limpiar filtros
                        </Link>
                    </div>
                ) : (
                    <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {careers.map((career) => (
                            <CareerCard key={career.id} career={career} />
                        ))}
                    </div>
                )}
            </div>
        </PublicLayout>
    );
}
