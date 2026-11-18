import { AdminPage } from '@/components/admin/admin-page';
import { AdminTable } from '@/components/admin/admin-table';
import { DeleteButton } from '@/components/admin/delete-button';
import { AreaIcon } from '@/components/area-icon';
import { Button } from '@/components/ui/button';
import { type Area } from '@/types';
import { Link } from '@inertiajs/react';
import { Pencil, Plus } from 'lucide-react';

export default function AreasIndex({ areas }: { areas: Area[] }) {
    return (
        <AdminPage
            title="Áreas"
            href={route('admin.areas.index')}
            actions={
                <Button asChild>
                    <Link href={route('admin.areas.create')}>
                        <Plus />
                        Nueva área
                    </Link>
                </Button>
            }
        >
            <AdminTable headers={['Orden', 'Área', 'Carreras', '']} empty={areas.length === 0}>
                {areas.map((area) => (
                    <tr key={area.id}>
                        <td className="tabular-nums">{area.position}</td>
                        <td>
                            <span className="flex items-center gap-2 font-bold">
                                <AreaIcon name={area.icon} className="text-brand size-4" />
                                {area.name}
                            </span>
                        </td>
                        <td>{area.careers_count}</td>
                        <td className="text-right whitespace-nowrap">
                            <Button asChild variant="ghost" size="icon" aria-label={`Editar ${area.name}`}>
                                <Link href={route('admin.areas.edit', area.id)}>
                                    <Pencil />
                                </Link>
                            </Button>
                            <DeleteButton url={route('admin.areas.destroy', area.id)} label={area.name} />
                        </td>
                    </tr>
                ))}
            </AdminTable>
        </AdminPage>
    );
}
