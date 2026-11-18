import { AdminPage } from '@/components/admin/admin-page';
import { CareerPicker } from '@/components/admin/career-picker';
import { FormField } from '@/components/admin/form-field';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { type Career, type Level, type Resource, type ResourceType, type SharedData } from '@/types';
import { useForm, usePage } from '@inertiajs/react';
import { type FormEvent } from 'react';

interface ResourceFormProps {
    resource: (Resource & { career_ids: number[] }) | null;
    careers: Pick<Career, 'id' | 'name'>[];
}

export default function ResourceForm({ resource, careers }: ResourceFormProps) {
    const { enums } = usePage<SharedData>().props;

    const { data, setData, post, put, processing, errors } = useForm({
        title: resource?.title ?? '',
        url: resource?.url ?? '',
        description: resource?.description ?? '',
        type: resource?.type ?? ('course' as ResourceType),
        level: resource?.level ?? ('beginner' as Level),
        language: resource?.language ?? ('es' as 'es' | 'en'),
        is_free: resource?.is_free ?? true,
        is_published: resource?.is_published ?? true,
        career_ids: resource?.career_ids ?? [],
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();

        if (resource) {
            put(route('admin.resources.update', resource.id));
            return;
        }

        post(route('admin.resources.store'));
    };

    return (
        <AdminPage title={resource ? 'Editar recurso' : 'Nuevo recurso'} href={route('admin.resources.index')}>
            <form onSubmit={submit} className="grid max-w-3xl gap-6">
                <FormField label="Título" htmlFor="title" error={errors.title}>
                    <Input id="title" value={data.title} onChange={(e) => setData('title', e.target.value)} required maxLength={255} />
                </FormField>

                <FormField label="URL" htmlFor="url" error={errors.url}>
                    <Input
                        id="url"
                        type="url"
                        value={data.url}
                        onChange={(e) => setData('url', e.target.value)}
                        required
                        maxLength={500}
                        placeholder="https://"
                    />
                </FormField>

                <FormField label="Descripción" htmlFor="description" error={errors.description}>
                    <Textarea
                        id="description"
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        rows={3}
                        maxLength={1000}
                    />
                </FormField>

                <div className="grid gap-6 sm:grid-cols-3">
                    <FormField label="Tipo" error={errors.type}>
                        <Select value={data.type} onValueChange={(value) => setData('type', value as ResourceType)}>
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                {(Object.entries(enums.resourceTypes) as [ResourceType, string][]).map(([value, label]) => (
                                    <SelectItem key={value} value={value}>
                                        {label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </FormField>
                    <FormField label="Nivel" error={errors.level}>
                        <Select value={data.level} onValueChange={(value) => setData('level', value as Level)}>
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                {(Object.entries(enums.levels) as [Level, string][]).map(([value, label]) => (
                                    <SelectItem key={value} value={value}>
                                        {label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </FormField>
                    <FormField label="Idioma" error={errors.language}>
                        <Select value={data.language} onValueChange={(value) => setData('language', value as 'es' | 'en')}>
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="es">Español</SelectItem>
                                <SelectItem value="en">Inglés</SelectItem>
                            </SelectContent>
                        </Select>
                    </FormField>
                </div>

                <FormField label="Carreras relacionadas" error={errors.career_ids}>
                    <CareerPicker careers={careers} selected={data.career_ids} onChange={(ids) => setData('career_ids', ids)} />
                </FormField>

                <div className="flex flex-wrap gap-6">
                    <div className="flex items-center gap-2">
                        <Checkbox id="is_free" checked={data.is_free} onCheckedChange={(checked) => setData('is_free', checked === true)} />
                        <Label htmlFor="is_free">Gratuito</Label>
                    </div>
                    <div className="flex items-center gap-2">
                        <Checkbox
                            id="is_published"
                            checked={data.is_published}
                            onCheckedChange={(checked) => setData('is_published', checked === true)}
                        />
                        <Label htmlFor="is_published">Publicado</Label>
                    </div>
                </div>

                <div>
                    <Button disabled={processing}>{resource ? 'Guardar cambios' : 'Crear recurso'}</Button>
                </div>
            </form>
        </AdminPage>
    );
}
