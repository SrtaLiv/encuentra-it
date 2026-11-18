import { AdminPage } from '@/components/admin/admin-page';
import { AdminTable } from '@/components/admin/admin-table';
import { DeleteButton } from '@/components/admin/delete-button';
import { CodingBadge } from '@/components/coding-badge';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { type Career } from '@/types';
import { Link } from '@inertiajs/react';
import { ExternalLink, Pencil, Plus } from 'lucide-react';

type CareerRow = Pick<Career, 'id' | 'name' | 'slug' | 'coding_level' | 'is_published' | 'area'> & {
    steps_count: number;
    challenges_count: number;
    resources_count: number;
};

export default function CareersIndex({ careers }: { careers: CareerRow[] }) {
    return (
        <AdminPage
            title="Carreras"
            href={route('admin.careers.index')}
            actions={
                <Button asChild>
                    <Link href={route('admin.careers.create')}>
                        <Plus />
                        Nueva carrera
                    </Link>
                </Button>
            }
        >
            <AdminTable headers={['Carrera', 'Área', 'Programación', 'Pasos', 'Desafíos', 'Recursos', 'Estado', '']} empty={careers.length === 0}>
                {careers.map((career) => (
                    <tr key={career.id}>
                        <td className="font-bold">{career.name}</td>
                        <td className="text-muted-foreground">{career.area?.name}</td>
                        <td>
                            <CodingBadge level={career.coding_level} />
                        </td>
                        <td className="tabular-nums">{career.steps_count}</td>
                        <td className="tabular-nums">{career.challenges_count}</td>
                        <td className="tabular-nums">{career.resources_count}</td>
                        <td>{career.is_published ? <Badge variant="secondary">Publicada</Badge> : <Badge variant="outline">Borrador</Badge>}</td>
                        <td className="text-right whitespace-nowrap">
                            {career.is_published && (
                                <Button asChild variant="ghost" size="icon" aria-label={`Ver ${career.name}`}>
                                    <a href={route('careers.show', career.slug)} target="_blank" rel="noopener noreferrer">
                                        <ExternalLink />
                                    </a>
                                </Button>
                            )}
                            <Button asChild variant="ghost" size="icon" aria-label={`Editar ${career.name}`}>
                                <Link href={route('admin.careers.edit', career.slug)}>
                                    <Pencil />
                                </Link>
                            </Button>
                            <DeleteButton url={route('admin.careers.destroy', career.slug)} label={career.name} />
                        </td>
                    </tr>
                ))}
            </AdminTable>
        </AdminPage>
    );
}
