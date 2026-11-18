import { AdminPage } from '@/components/admin/admin-page';
import { FormField } from '@/components/admin/form-field';
import { AreaIcon, areaIcons } from '@/components/area-icon';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { type Area } from '@/types';
import { useForm } from '@inertiajs/react';
import { type FormEvent } from 'react';

export default function AreaForm({ area }: { area: Area | null }) {
    const { data, setData, post, put, processing, errors } = useForm({
        name: area?.name ?? '',
        slug: area?.slug ?? '',
        description: area?.description ?? '',
        icon: area?.icon ?? 'compass',
        position: area?.position ?? 0,
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();

        if (area) {
            put(route('admin.areas.update', area.id));
            return;
        }

        post(route('admin.areas.store'));
    };

    return (
        <AdminPage title={area ? `Editar ${area.name}` : 'Nueva área'} href={route('admin.areas.index')}>
            <form onSubmit={submit} className="grid max-w-2xl gap-6">
                <FormField label="Nombre" htmlFor="name" error={errors.name}>
                    <Input id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} required maxLength={100} />
                </FormField>

                <FormField label="Slug" htmlFor="slug" error={errors.slug} hint="Se usa en la URL. Si lo dejás vacío se genera desde el nombre.">
                    <Input id="slug" value={data.slug} onChange={(e) => setData('slug', e.target.value)} maxLength={100} />
                </FormField>

                <FormField label="Descripción" htmlFor="description" error={errors.description}>
                    <Textarea
                        id="description"
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        required
                        maxLength={1000}
                    />
                </FormField>

                <div className="grid gap-6 sm:grid-cols-2">
                    <FormField label="Icono" error={errors.icon}>
                        <Select value={data.icon} onValueChange={(value) => setData('icon', value)}>
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                {Object.keys(areaIcons).map((icon) => (
                                    <SelectItem key={icon} value={icon}>
                                        <span className="flex items-center gap-2">
                                            <AreaIcon name={icon} className="size-4" />
                                            {icon}
                                        </span>
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </FormField>

                    <FormField label="Orden" htmlFor="position" error={errors.position}>
                        <Input
                            id="position"
                            type="number"
                            min={0}
                            max={999}
                            value={data.position}
                            onChange={(e) => setData('position', Number(e.target.value))}
                            required
                        />
                    </FormField>
                </div>

                <div>
                    <Button disabled={processing}>{area ? 'Guardar cambios' : 'Crear área'}</Button>
                </div>
            </form>
        </AdminPage>
    );
}
