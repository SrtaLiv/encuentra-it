import { AdminPage } from '@/components/admin/admin-page';
import { AdminTable } from '@/components/admin/admin-table';
import { DeleteButton } from '@/components/admin/delete-button';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';
import { Pencil, Plus } from 'lucide-react';

interface QuestionRow {
    id: number;
    text: string;
    position: number;
    options_count: number;
}

export default function QuizIndex({ questions }: { questions: QuestionRow[] }) {
    return (
        <AdminPage
            title="Test vocacional"
            href={route('admin.quiz.index')}
            actions={
                <Button asChild>
                    <Link href={route('admin.quiz.create')}>
                        <Plus />
                        Nueva pregunta
                    </Link>
                </Button>
            }
        >
            <p className="text-muted-foreground max-w-3xl text-sm">
                Cada opción suma puntos a las carreras que marques. El resultado muestra qué porcentaje de su puntaje máximo obtuvo cada carrera, así
                que conviene que todas aparezcan en varias preguntas.
            </p>

            <AdminTable headers={['Orden', 'Pregunta', 'Opciones', '']} empty={questions.length === 0}>
                {questions.map((question) => (
                    <tr key={question.id}>
                        <td className="tabular-nums">{question.position}</td>
                        <td className="font-bold">{question.text}</td>
                        <td className="tabular-nums">{question.options_count}</td>
                        <td className="text-right whitespace-nowrap">
                            <Button asChild variant="ghost" size="icon" aria-label="Editar pregunta">
                                <Link href={route('admin.quiz.edit', question.id)}>
                                    <Pencil />
                                </Link>
                            </Button>
                            <DeleteButton url={route('admin.quiz.destroy', question.id)} label={question.text} />
                        </td>
                    </tr>
                ))}
            </AdminTable>
        </AdminPage>
    );
}
