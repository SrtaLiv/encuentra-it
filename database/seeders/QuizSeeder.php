<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        if (QuizQuestion::query()->exists()) {
            return;
        }

        $careerIds = Career::query()->pluck('id', 'slug');

        foreach ($this->questions() as $position => [$text, $options]) {
            $question = QuizQuestion::create(['text' => $text, 'position' => $position]);

            foreach (array_values($options) as $optionPosition => [$optionText, $careers]) {
                $option = $question->options()->create(['text' => $optionText, 'position' => $optionPosition]);
                $option->careers()->sync($careerIds->only($careers)->values());
            }
        }
    }

    // [question, [[option, career slugs], ...]]
    private function questions(): array
    {
        return [
            ['¿Qué te gustaría hacer en un día de trabajo típico?', [
                ['Construir cosas que la gente usa, como apps o webs', ['frontend-developer', 'backend-developer', 'mobile-developer']],
                ['Analizar datos y encontrar patrones', ['data-analyst', 'data-scientist', 'ai-engineer']],
                ['Proteger sistemas e investigar ataques', ['analista-soc', 'pentester', 'analista-grc']],
                ['Organizar equipos y proyectos', ['scrum-master', 'project-manager', 'product-manager']],
                ['Diseñar pantallas y experiencias', ['ux-ui-designer']],
                ['Comunicar, escribir y atraer gente', ['especialista-seo', 'marketing-digital', 'technical-writer', 'creador-de-contenido']],
            ]],
            ['¿Cómo te llevás con la idea de programar?', [
                ['Me encanta o quiero que sea mi herramienta principal', ['frontend-developer', 'backend-developer', 'mobile-developer', 'ai-engineer', 'data-scientist', 'devops-engineer', 'qa-automation', 'pentester']],
                ['Un poco está bien, pero no todo el día', ['data-analyst', 'cloud-engineer', 'analista-soc', 'especialista-seo', 'technical-writer']],
                ['Prefiero no programar', ['qa-manual', 'ux-ui-designer', 'product-manager', 'scrum-master', 'project-manager', 'analista-funcional', 'analista-grc', 'marketing-digital', 'soporte-it-sysadmin', 'creador-de-contenido']],
            ]],
            ['¿Qué disfrutás más?', [
                ['Encontrar errores y que todo quede perfecto', ['qa-manual', 'qa-automation', 'pentester']],
                ['Resolver problemas lógicos y acertijos', ['backend-developer', 'data-scientist', 'pentester', 'devops-engineer']],
                ['Lo visual: colores, formas y tipografías', ['frontend-developer', 'ux-ui-designer', 'mobile-developer']],
                ['Hablar con personas y entender qué necesitan', ['product-manager', 'analista-funcional', 'ux-ui-designer', 'scrum-master']],
                ['Automatizar tareas repetitivas', ['devops-engineer', 'qa-automation', 'cloud-engineer', 'soporte-it-sysadmin']],
            ]],
            ['Cuando aparece algo nuevo, preferís…', [
                ['Investigar a fondo cómo funciona por dentro', ['backend-developer', 'pentester', 'ai-engineer', 'soporte-it-sysadmin']],
                ['Probarlo rápido y ver resultados', ['frontend-developer', 'mobile-developer', 'marketing-digital', 'creador-de-contenido']],
                ['Hacer preguntas y armar un plan', ['project-manager', 'scrum-master', 'analista-funcional', 'product-manager']],
                ['Documentarlo y explicárselo a otros', ['technical-writer', 'analista-grc', 'qa-manual', 'creador-de-contenido']],
            ]],
            ['Te piden mejorar una tienda online. ¿Por dónde empezás?', [
                ['Mejoro el diseño para que comprar sea más fácil', ['ux-ui-designer', 'frontend-developer']],
                ['Miro los números: qué se vende y dónde abandonan', ['data-analyst', 'marketing-digital', 'product-manager']],
                ['Hago que aparezca primera en Google', ['especialista-seo', 'marketing-digital']],
                ['Reviso que sea segura y no roben datos', ['analista-soc', 'pentester', 'analista-grc']],
                ['Me aseguro de que no se caiga con mucho tráfico', ['devops-engineer', 'cloud-engineer', 'backend-developer']],
                ['Pruebo toda la compra buscando errores', ['qa-manual', 'qa-automation']],
            ]],
            ['¿Cómo te llevás con la matemática y la estadística?', [
                ['Me gustan y me gustaría usarlas', ['data-scientist', 'ai-engineer', 'data-analyst']],
                ['Lo justo y necesario', []],
                ['Prefiero evitarlas', ['ux-ui-designer', 'scrum-master', 'marketing-digital', 'technical-writer', 'qa-manual']],
            ]],
            ['¿Qué ambiente de trabajo te atrae más?', [
                ['Tranquilo y enfocado, con tareas claras', ['qa-manual', 'data-analyst', 'technical-writer', 'backend-developer']],
                ['Dinámico, con urgencias y adrenalina', ['analista-soc', 'devops-engineer', 'soporte-it-sysadmin']],
                ['Creativo, con ideas y prototipos', ['ux-ui-designer', 'marketing-digital', 'frontend-developer', 'product-manager', 'creador-de-contenido']],
                ['Coordinando con muchas personas', ['scrum-master', 'project-manager', 'analista-funcional']],
            ]],
            ['¿Qué tema te da más curiosidad?', [
                ['Inteligencia artificial', ['ai-engineer', 'data-scientist']],
                ['Hackers y seguridad', ['pentester', 'analista-soc', 'analista-grc']],
                ['Servidores, redes y la nube', ['cloud-engineer', 'devops-engineer', 'soporte-it-sysadmin']],
                ['Cómo se construyen las apps y webs', ['frontend-developer', 'backend-developer', 'mobile-developer', 'qa-automation']],
                ['Redes sociales y cómo crecen las marcas', ['marketing-digital', 'especialista-seo', 'creador-de-contenido']],
                ['Cómo nacen y crecen los productos digitales', ['product-manager', 'ux-ui-designer', 'analista-funcional']],
            ]],
            ['¿Qué resultado te dejaría más satisfecho/a?', [
                ['Ver funcionando algo que construí', ['frontend-developer', 'backend-developer', 'mobile-developer', 'devops-engineer']],
                ['Un informe que ayuda a tomar una buena decisión', ['data-analyst', 'analista-funcional', 'analista-grc']],
                ['Un equipo que entrega a tiempo y sin estrés', ['scrum-master', 'project-manager']],
                ['Gente que usa algo sin esfuerzo', ['ux-ui-designer', 'product-manager']],
                ['Encontrar lo que nadie vio: un bug o una vulnerabilidad', ['qa-manual', 'qa-automation', 'pentester', 'analista-soc']],
                ['Contenido que mucha gente lee o comparte', ['technical-writer', 'especialista-seo', 'marketing-digital', 'creador-de-contenido']],
            ]],
        ];
    }
}
