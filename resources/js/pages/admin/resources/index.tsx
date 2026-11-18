import { AdminPage } from '@/components/admin/admin-page';
import { AdminTable } from '@/components/admin/admin-table';
import { DeleteButton } from '@/components/admin/delete-button';
import { Pagination } from '@/components/pagination';
import { SearchInput } from '@/components/search-input';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { type Paginated, type Resource, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { ExternalLink, Pencil, Plus } from 'lucide-react';

export default function ResourcesIndex({ resources, search }: { resources: Paginated<Resource>; search: string }) {
    const { enums } = usePage<SharedData>().props;

    return (
        <AdminPage
            title="Recursos"
            href={route('admin.resources.index')}
            actions={
                <Button asChild>
                    <Link href={route('admin.resources.create')}>
                        <Plus />
                        Nuevo recurso
                    </Link>
                </Button>
            }
        >
            <SearchInput url={route('admin.resources.index')} params={{}} initial={search} placeholder="Buscar por título o URL…" />

            <AdminTable headers={['Recurso', 'Tipo', 'Nivel', 'Idioma', 'Carreras', 'Estado', '']} empty={resources.data.length === 0}>
                {resources.data.map((resource) => (
                    <tr key={resource.id}>
                        <td>
                            <p className="font-bold">{resource.title}</p>
                            <a
                                href={resource.url}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="text-muted-foreground hover:text-brand inline-flex max-w-xs items-center gap-1 truncate text-xs"
                            >
                                <span className="truncate">{resource.url}</span>
                                <ExternalLink className="size-3 shrink-0" />
                            </a>
                        </td>
                        <td className="whitespace-nowrap">{enums.resourceTypes[resource.type]}</td>
                        <td className="whitespace-nowrap">{enums.levels[resource.level]}</td>
                        <td className="uppercase">{resource.language}</td>
                        <td className="tabular-nums">{resource.careers_count}</td>
                        <td>{resource.is_published ? <Badge variant="secondary">Publicado</Badge> : <Badge variant="outline">Oculto</Badge>}</td>
                        <td className="text-right whitespace-nowrap">
                            <Button asChild variant="ghost" size="icon" aria-label={`Editar ${resource.title}`}>
                                <Link href={route('admin.resources.edit', resource.id)}>
                                    <Pencil />
                                </Link>
                            </Button>
                            <DeleteButton url={route('admin.resources.destroy', resource.id)} label={resource.title} />
                        </td>
                    </tr>
                ))}
            </AdminTable>

            <Pagination paginator={resources} />
        </AdminPage>
    );
}
