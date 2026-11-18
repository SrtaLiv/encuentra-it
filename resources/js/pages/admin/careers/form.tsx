import { AdminPage } from '@/components/admin/admin-page';
import { FormField } from '@/components/admin/form-field';
import { LevelItemsEditor, type LevelItemInput } from '@/components/admin/level-items-editor';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { type Area, type Career, type CodingLevel, type SharedData } from '@/types';
import { useForm, usePage } from '@inertiajs/react';
import { type FormEvent } from 'react';

interface CareerFormProps {
    career: (Career & Required<Pick<Career, 'steps' | 'challenges'>>) | null;
    areas: Pick<Area, 'id' | 'name'>[];
}

const toLines = (items?: string[]) => (items ?? []).join('\n');
const fromLines = (text: string) =>
    text
        .split('\n')
        .map((line) => line.trim())
        .filter(Boolean);

export default function CareerForm({ career, areas }: CareerFormProps) {
    const { enums } = usePage<SharedData>().props;

    const { data, setData, post, put, transform, processing, errors } = useForm({
        area_id: career?.area_id ? String(career.area_id) : '',
        name: career?.name ?? '',
        slug: career?.slug ?? '',
        summary: career?.summary ?? '',
        description: career?.description ?? '',
        coding_level: career?.coding_level ?? ('none' as CodingLevel),
        daily_tasks: toLines(career?.daily_tasks),
        skills: toLines(career?.skills),
        job_titles: toLines(career?.job_titles),
        job_outlook: career?.job_outlook ?? '',
        is_published: career?.is_published ?? true,
        steps: (career?.steps ?? []).map((step) => ({ ...step, description: step.description ?? '' })) as LevelItemInput[],
        challenges: (career?.challenges ?? []) as LevelItemInput[],
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();

        transform((form) => ({
            ...form,
            daily_tasks: fromLines(form.daily_tasks),
            skills: fromLines(form.skills),
            job_titles: fromLines(form.job_titles),
        }));

        if (career) {
            put(route('admin.careers.update', career.slug));
            return;
        }

        post(route('admin.careers.store'));
    };

    const errorBag = errors as Record<string, string>;

    return (
        <AdminPage title={career ? `Editar ${career.name}` : 'Nueva carrera'} href={route('admin.careers.index')}>
            <form onSubmit={submit} className="grid max-w-3xl gap-6">
                <div className="grid gap-6 sm:grid-cols-2">
                    <FormField label="Nombre" htmlFor="name" error={errors.name}>
                        <Input id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} required maxLength={100} />
                    </FormField>
                    <FormField label="Slug" htmlFor="slug" error={errors.slug} hint="Vacío = se genera desde el nombre.">
                        <Input id="slug" value={data.slug} onChange={(e) => setData('slug', e.target.value)} maxLength={100} />
                    </FormField>
                </div>

                <div className="grid gap-6 sm:grid-cols-2">
                    <FormField label="Área" error={errors.area_id}>
                        <Select value={data.area_id} onValueChange={(value) => setData('area_id', value)}>
                            <SelectTrigger>
                                <SelectValue placeholder="Elegí un área" />
                            </SelectTrigger>
                            <SelectContent>
                                {areas.map((area) => (
                                    <SelectItem key={area.id} value={String(area.id)}>
                                        {area.name}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </FormField>
                    <FormField label="¿Necesita programar?" error={errors.coding_level}>
                        <Select value={data.coding_level} onValueChange={(value) => setData('coding_level', value as CodingLevel)}>
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                {(Object.entries(enums.codingLevels) as [CodingLevel, string][]).map(([value, label]) => (
                                    <SelectItem key={value} value={value}>
                                        {label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </FormField>
                </div>

                <FormField label="Resumen" htmlFor="summary" error={errors.summary} hint="Una línea. Se muestra en las tarjetas.">
                    <Input id="summary" value={data.summary} onChange={(e) => setData('summary', e.target.value)} required maxLength={255} />
                </FormField>

                <FormField label="¿Qué hace?" htmlFor="description" error={errors.description}>
                    <Textarea
                        id="description"
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        required
                        rows={5}
                        maxLength={5000}
                    />
                </FormField>

                <FormField label="Un día de trabajo" htmlFor="daily_tasks" error={errors.daily_tasks} hint="Una tarea por línea.">
                    <Textarea id="daily_tasks" value={data.daily_tasks} onChange={(e) => setData('daily_tasks', e.target.value)} rows={4} />
                </FormField>

                <div className="grid gap-6 sm:grid-cols-2">
                    <FormField label="Skills" htmlFor="skills" error={errors.skills} hint="Una por línea.">
                        <Textarea id="skills" value={data.skills} onChange={(e) => setData('skills', e.target.value)} rows={6} />
                    </FormField>
                    <FormField label="Puestos para buscar" htmlFor="job_titles" error={errors.job_titles} hint="Uno por línea.">
                        <Textarea id="job_titles" value={data.job_titles} onChange={(e) => setData('job_titles', e.target.value)} rows={6} />
                    </FormField>
                </div>

                <FormField label="Salida laboral" htmlFor="job_outlook" error={errors.job_outlook}>
                    <Textarea
                        id="job_outlook"
                        value={data.job_outlook}
                        onChange={(e) => setData('job_outlook', e.target.value)}
                        rows={3}
                        maxLength={2000}
                    />
                </FormField>

                <div className="grid gap-3">
                    <h2 className="font-extrabold">Ruta de aprendizaje</h2>
                    <LevelItemsEditor
                        items={data.steps}
                        onChange={(steps) => setData('steps', steps)}
                        errors={errorBag}
                        field="steps"
                        itemLabel="paso"
                    />
                </div>

                <div className="grid gap-3">
                    <div>
                        <h2 className="font-extrabold">Desafíos prácticos</h2>
                        <p className="text-muted-foreground text-sm">
                            Tareas concretas con un entregable verificable. Ordenalas de la más fácil a la más difícil.
                        </p>
                    </div>
                    <LevelItemsEditor
                        items={data.challenges}
                        onChange={(challenges) => setData('challenges', challenges)}
                        errors={errorBag}
                        field="challenges"
                        itemLabel="desafío"
                        withDeliverable
                    />
                </div>

                <div className="flex items-center gap-2">
                    <Checkbox
                        id="is_published"
                        checked={data.is_published}
                        onCheckedChange={(checked) => setData('is_published', checked === true)}
                    />
                    <Label htmlFor="is_published">Publicada</Label>
                </div>

                <div>
                    <Button disabled={processing}>{career ? 'Guardar cambios' : 'Crear carrera'}</Button>
                </div>
            </form>
        </AdminPage>
    );
}
