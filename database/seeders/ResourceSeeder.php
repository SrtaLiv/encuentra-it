<?php

namespace Database\Seeders;

use App\Models\Career;
use App\Models\Resource;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $careerIds = Career::query()->pluck('id', 'slug');

        foreach ($this->resources() as [$title, $url, $type, $level, $language, $isFree, $careers, $description]) {
            $resource = Resource::updateOrCreate(['url' => $url], [
                'title' => $title,
                'type' => $type,
                'level' => $level,
                'language' => $language,
                'is_free' => $isFree,
                'description' => $description,
            ]);

            $ids = $careers === ['*'] ? $careerIds->values() : $careerIds->only($careers)->values();
            $resource->careers()->sync($ids);
        }
    }

    // [title, url, type, level, language, is_free, career slugs, description]
    private function resources(): array
    {
        $dev = ['frontend-developer', 'backend-developer', 'mobile-developer'];

        return [
            // desarrollo
            ['freeCodeCamp en español', 'https://www.freecodecamp.org/espanol/', 'course', 'beginner', 'es', true, [...$dev, 'data-analyst', 'qa-automation'], 'Cursos y certificaciones gratuitas de desarrollo web, JavaScript, Python y datos.'],
            ['The Odin Project', 'https://www.theodinproject.com/', 'course', 'beginner', 'en', true, ['frontend-developer', 'backend-developer'], 'Currículum full stack gratuito basado en proyectos.'],
            ['MDN Web Docs', 'https://developer.mozilla.org/es/', 'guide', 'beginner', 'es', true, ['frontend-developer'], 'La documentación de referencia de HTML, CSS y JavaScript.'],
            ['Documentación de React en español', 'https://es.react.dev/', 'guide', 'intermediate', 'es', true, ['frontend-developer', 'mobile-developer'], 'Documentación oficial de React con tutoriales interactivos.'],
            ['CS50x de Harvard', 'https://cs50.harvard.edu/x/', 'course', 'beginner', 'en', true, [...$dev, 'data-scientist'], 'El curso introductorio de ciencias de la computación más famoso del mundo.'],
            ['Libros de programación gratuitos en español', 'https://github.com/EbookFoundation/free-programming-books/blob/main/books/free-programming-books-es.md', 'repository', 'beginner', 'es', true, [...$dev, 'data-scientist', 'devops-engineer'], 'Lista enorme de libros gratuitos organizados por tema.'],
            ['Aprendizaje basado en proyectos', 'https://github.com/practical-tutorials/project-based-learning', 'repository', 'intermediate', 'en', true, [...$dev, 'ai-engineer'], 'Tutoriales para construir proyectos reales en distintos lenguajes.'],
            ['App Ideas', 'https://github.com/florinpop17/app-ideas', 'challenge', 'beginner', 'en', true, $dev, 'Ideas de apps para practicar, ordenadas por dificultad.'],
            ['Frontend Mentor', 'https://www.frontendmentor.io/challenges', 'challenge', 'beginner', 'en', true, ['frontend-developer'], 'Desafíos con diseños profesionales para maquetar.'],
            ['Exercism', 'https://exercism.org/', 'challenge', 'beginner', 'en', true, [...$dev, 'qa-automation'], 'Ejercicios en más de 60 lenguajes con mentoría gratuita.'],
            ['Codewars', 'https://www.codewars.com/', 'challenge', 'beginner', 'en', true, ['backend-developer', 'frontend-developer'], 'Katas de programación para entrenar lógica.'],
            ['AdventJS', 'https://adventjs.dev/es', 'challenge', 'beginner', 'es', true, ['frontend-developer', 'backend-developer'], 'Retos de JavaScript y TypeScript en español.'],
            ['Retos de programación', 'https://retosdeprogramacion.com/', 'challenge', 'beginner', 'es', true, $dev, 'Retos semanales de lógica en español, para cualquier lenguaje.'],
            ['SQLBolt', 'https://sqlbolt.com/', 'course', 'beginner', 'en', true, ['backend-developer', 'data-analyst', 'analista-funcional'], 'Lecciones interactivas de SQL directo en el navegador.'],
            ['Android Basics with Compose', 'https://developer.android.com/courses/android-basics-compose/course', 'course', 'beginner', 'en', true, ['mobile-developer'], 'Curso oficial de Google para crear apps Android.'],
            ['Documentación de React Native', 'https://reactnative.dev/docs/getting-started', 'guide', 'intermediate', 'en', true, ['mobile-developer'], 'Guía oficial para crear apps con React Native.'],
            ['Documentación de Flutter', 'https://docs.flutter.dev/get-started', 'guide', 'intermediate', 'en', true, ['mobile-developer'], 'Guía oficial para crear apps multiplataforma con Flutter.'],
            ['Roadmap Frontend', 'https://roadmap.sh/frontend', 'roadmap', 'beginner', 'en', true, ['frontend-developer'], 'Mapa visual de todo lo que podés aprender en frontend.'],
            ['Roadmap Backend', 'https://roadmap.sh/backend', 'roadmap', 'beginner', 'en', true, ['backend-developer'], 'Mapa visual de todo lo que podés aprender en backend.'],
            ['Roadmap React Native', 'https://roadmap.sh/react-native', 'roadmap', 'beginner', 'en', true, ['mobile-developer'], 'Mapa visual para desarrollo mobile con React Native.'],

            // ia y datos
            ['Kaggle Learn', 'https://www.kaggle.com/learn', 'course', 'beginner', 'en', true, ['data-analyst', 'data-scientist', 'ai-engineer'], 'Micro-cursos prácticos de Python, pandas, SQL y machine learning.'],
            ['Competencias de Kaggle', 'https://www.kaggle.com/competitions', 'challenge', 'intermediate', 'en', true, ['data-scientist', 'ai-engineer'], 'Problemas reales de datos para competir y aprender.'],
            ['Power BI en Microsoft Learn', 'https://learn.microsoft.com/es-es/training/powerplatform/power-bi', 'course', 'beginner', 'es', true, ['data-analyst'], 'Rutas oficiales y gratuitas para aprender Power BI.'],
            ['Google Data Analytics', 'https://www.coursera.org/professional-certificates/google-data-analytics', 'course', 'beginner', 'en', false, ['data-analyst'], 'Certificado profesional de Google. Tiene subtítulos en español y becas disponibles.'],
            ['Elements of AI', 'https://www.elementsofai.com/', 'course', 'beginner', 'en', true, ['ai-engineer', 'data-scientist', 'product-manager'], 'Introducción a la IA sin programar. Disponible en varios idiomas, incluido español.'],
            ['Machine Learning Crash Course', 'https://developers.google.com/machine-learning/crash-course', 'course', 'intermediate', 'en', true, ['data-scientist', 'ai-engineer'], 'Curso intensivo de machine learning de Google.'],
            ['Machine Learning Specialization', 'https://www.coursera.org/specializations/machine-learning-introduction', 'course', 'intermediate', 'en', false, ['data-scientist', 'ai-engineer'], 'La especialización de Andrew Ng. Se puede auditar gratis.'],
            ['Practical Deep Learning (fast.ai)', 'https://course.fast.ai/', 'course', 'intermediate', 'en', true, ['ai-engineer', 'data-scientist'], 'Deep learning práctico, de arriba hacia abajo.'],
            ['Hugging Face Learn', 'https://huggingface.co/learn', 'course', 'intermediate', 'en', true, ['ai-engineer'], 'Cursos gratuitos de LLMs, agentes y modelos open source.'],
            ['Roadmap Data Analyst', 'https://roadmap.sh/data-analyst', 'roadmap', 'beginner', 'en', true, ['data-analyst'], 'Mapa visual para convertirte en Data Analyst.'],
            ['Roadmap AI y Data Scientist', 'https://roadmap.sh/ai-data-scientist', 'roadmap', 'beginner', 'en', true, ['data-scientist'], 'Mapa visual para ciencia de datos e IA.'],
            ['Roadmap AI Engineer', 'https://roadmap.sh/ai-engineer', 'roadmap', 'intermediate', 'en', true, ['ai-engineer'], 'Mapa visual para construir productos con IA.'],

            // ciberseguridad
            ['TryHackMe', 'https://tryhackme.com/', 'challenge', 'beginner', 'en', true, ['analista-soc', 'pentester'], 'Laboratorios guiados de ciberseguridad. Muchas salas gratuitas.'],
            ['Hack The Box Academy', 'https://academy.hackthebox.com/', 'course', 'intermediate', 'en', true, ['pentester', 'analista-soc'], 'Módulos de seguridad ofensiva y defensiva. Tiene contenido gratuito.'],
            ['OverTheWire: Bandit', 'https://overthewire.org/wargames/bandit/', 'challenge', 'beginner', 'en', true, ['pentester', 'analista-soc', 'devops-engineer', 'soporte-it-sysadmin'], 'Wargame para aprender la terminal de Linux nivel por nivel.'],
            ['picoCTF', 'https://picoctf.org/', 'challenge', 'beginner', 'en', true, ['pentester'], 'CTF educativo para principiantes de la Universidad Carnegie Mellon.'],
            ['PortSwigger Web Security Academy', 'https://portswigger.net/web-security', 'course', 'intermediate', 'en', true, ['pentester', 'backend-developer'], 'Laboratorios gratuitos de vulnerabilidades web.'],
            ['LetsDefend', 'https://letsdefend.io/', 'challenge', 'beginner', 'en', true, ['analista-soc'], 'Simulador de SOC para practicar análisis de alertas. Tiene plan gratuito.'],
            ['OWASP Top 10', 'https://owasp.org/www-project-top-ten/', 'guide', 'intermediate', 'en', true, ['pentester', 'backend-developer', 'analista-grc'], 'Los 10 riesgos de seguridad web más críticos.'],
            ['Cisco Networking Academy', 'https://www.netacad.com/', 'course', 'beginner', 'es', true, ['analista-soc', 'soporte-it-sysadmin', 'analista-grc', 'pentester'], 'Cursos gratuitos de redes y ciberseguridad, muchos en español.'],
            ['NIST Cybersecurity Framework', 'https://www.nist.gov/cyberframework', 'guide', 'intermediate', 'en', true, ['analista-grc'], 'Marco de referencia para gestionar riesgos de ciberseguridad.'],
            ['HackerOne', 'https://www.hackerone.com/', 'challenge', 'advanced', 'en', true, ['pentester'], 'Programas de bug bounty para encontrar vulnerabilidades reales.'],
            ['Roadmap Ciberseguridad', 'https://roadmap.sh/cyber-security', 'roadmap', 'beginner', 'en', true, ['analista-soc', 'pentester', 'analista-grc'], 'Mapa visual para empezar en ciberseguridad.'],

            // infraestructura
            ['DevOps Exercises', 'https://github.com/bregman-arie/devops-exercises', 'repository', 'intermediate', 'en', true, ['devops-engineer', 'cloud-engineer', 'soporte-it-sysadmin'], 'Más de 2600 preguntas y ejercicios de Linux, Docker, Kubernetes, cloud y más.'],
            ['90 Days of DevOps', 'https://github.com/MichaelCade/90DaysOfDevOps', 'repository', 'beginner', 'en', true, ['devops-engineer'], 'Plan de 90 días para aprender DevOps, con traducciones a otros idiomas.'],
            ['Linux Journey', 'https://linuxjourney.com/', 'course', 'beginner', 'en', true, ['soporte-it-sysadmin', 'devops-engineer', 'analista-soc', 'pentester'], 'Aprendé Linux paso a paso, gratis.'],
            ['SadServers', 'https://sadservers.com/', 'challenge', 'intermediate', 'en', true, ['soporte-it-sysadmin', 'devops-engineer'], 'Servidores rotos para practicar troubleshooting real.'],
            ['Docker: Get Started', 'https://docs.docker.com/get-started/', 'guide', 'beginner', 'en', true, ['devops-engineer', 'backend-developer'], 'Guía oficial para empezar con contenedores.'],
            ['Tutoriales de Kubernetes', 'https://kubernetes.io/es/docs/tutorials/', 'guide', 'intermediate', 'es', true, ['devops-engineer', 'cloud-engineer'], 'Tutoriales oficiales de Kubernetes en español.'],
            ['Killercoda', 'https://killercoda.com/', 'challenge', 'intermediate', 'en', true, ['devops-engineer', 'cloud-engineer'], 'Escenarios interactivos de Linux, Docker y Kubernetes en el navegador.'],
            ['GitHub Actions', 'https://docs.github.com/es/actions', 'guide', 'intermediate', 'es', true, ['devops-engineer', 'qa-automation'], 'Documentación oficial para automatizar CI/CD.'],
            ['Tutoriales de Terraform', 'https://developer.hashicorp.com/terraform/tutorials', 'guide', 'advanced', 'en', true, ['devops-engineer', 'cloud-engineer'], 'Tutoriales oficiales de infraestructura como código.'],
            ['AWS Skill Builder', 'https://skillbuilder.aws/', 'course', 'beginner', 'en', true, ['cloud-engineer'], 'Cursos oficiales de AWS, incluida la preparación para Cloud Practitioner.'],
            ['Azure en Microsoft Learn', 'https://learn.microsoft.com/es-es/training/azure/', 'course', 'beginner', 'es', true, ['cloud-engineer'], 'Rutas oficiales de Azure en español, incluida AZ-900.'],
            ['Google Cloud Skills Boost', 'https://www.cloudskillsboost.google/', 'course', 'beginner', 'en', true, ['cloud-engineer'], 'Laboratorios y cursos oficiales de Google Cloud.'],
            ['Google IT Support', 'https://www.coursera.org/professional-certificates/google-it-support', 'course', 'beginner', 'en', false, ['soporte-it-sysadmin'], 'Certificado profesional de soporte IT de Google.'],
            ['Roadmap DevOps', 'https://roadmap.sh/devops', 'roadmap', 'beginner', 'en', true, ['devops-engineer'], 'Mapa visual para convertirte en DevOps.'],
            ['Roadmap Linux', 'https://roadmap.sh/linux', 'roadmap', 'beginner', 'en', true, ['soporte-it-sysadmin', 'devops-engineer'], 'Mapa visual para dominar Linux.'],
            ['Roadmap AWS', 'https://roadmap.sh/aws', 'roadmap', 'intermediate', 'en', true, ['cloud-engineer'], 'Mapa visual de los servicios principales de AWS.'],

            // qa
            ['Test Automation University', 'https://testautomationu.applitools.com/', 'course', 'beginner', 'en', true, ['qa-automation', 'qa-manual'], 'Cursos gratuitos de testing y automatización.'],
            ['Documentación de Playwright', 'https://playwright.dev/docs/intro', 'guide', 'intermediate', 'en', true, ['qa-automation'], 'Framework moderno de tests end-to-end.'],
            ['Documentación de Cypress', 'https://docs.cypress.io/', 'guide', 'intermediate', 'en', true, ['qa-automation'], 'Framework popular de testing web.'],
            ['Postman Learning Center', 'https://learning.postman.com/', 'guide', 'beginner', 'en', true, ['qa-manual', 'qa-automation', 'backend-developer'], 'Aprendé a probar APIs con Postman.'],
            ['ISTQB', 'https://www.istqb.org/', 'guide', 'intermediate', 'en', true, ['qa-manual', 'qa-automation'], 'Syllabus gratuito de la certificación de testing más reconocida. El examen es pago.'],
            ['Sauce Demo', 'https://www.saucedemo.com/', 'challenge', 'beginner', 'en', true, ['qa-manual', 'qa-automation'], 'Tienda de prueba para practicar testing manual y automatizado.'],
            ['Automation Exercise', 'https://automationexercise.com/', 'challenge', 'beginner', 'en', true, ['qa-manual', 'qa-automation'], 'Sitio completo con casos de prueba sugeridos.'],
            ['DemoQA', 'https://demoqa.com/', 'challenge', 'beginner', 'en', true, ['qa-automation'], 'Formularios y componentes para practicar automatización.'],
            ['Restful Booker', 'https://restful-booker.herokuapp.com/', 'challenge', 'intermediate', 'en', true, ['qa-manual', 'qa-automation'], 'API de prueba para practicar testing de APIs.'],
            ['Ministry of Testing', 'https://www.ministryoftesting.com/', 'community', 'beginner', 'en', true, ['qa-manual', 'qa-automation'], 'La comunidad de testing más grande del mundo.'],
            ['Roadmap QA', 'https://roadmap.sh/qa', 'roadmap', 'beginner', 'en', true, ['qa-manual', 'qa-automation'], 'Mapa visual para convertirte en QA.'],

            // diseño y producto
            ['Laws of UX', 'https://lawsofux.com/', 'guide', 'beginner', 'en', true, ['ux-ui-designer', 'product-manager', 'frontend-developer'], 'Principios de psicología aplicados al diseño de interfaces.'],
            ['Nielsen Norman Group', 'https://www.nngroup.com/articles/', 'guide', 'intermediate', 'en', true, ['ux-ui-designer', 'product-manager'], 'Artículos de referencia sobre UX e investigación de usuarios.'],
            ['Figma Learn', 'https://help.figma.com/', 'guide', 'beginner', 'en', true, ['ux-ui-designer'], 'Tutoriales oficiales de Figma.'],
            ['Daily UI', 'https://www.dailyui.co/', 'challenge', 'beginner', 'en', true, ['ux-ui-designer'], '100 días de desafíos de diseño de interfaces.'],
            ['Google UX Design', 'https://www.coursera.org/professional-certificates/google-ux-design', 'course', 'beginner', 'en', false, ['ux-ui-designer'], 'Certificado profesional de UX de Google. Tiene becas disponibles.'],
            ['Roadmap UX Design', 'https://roadmap.sh/ux-design', 'roadmap', 'beginner', 'en', true, ['ux-ui-designer'], 'Mapa visual para diseño UX.'],
            ['Roadmap Product Manager', 'https://roadmap.sh/product-manager', 'roadmap', 'beginner', 'en', true, ['product-manager'], 'Mapa visual para Product Management.'],

            // gestión y negocios
            ['Agile Coach de Atlassian', 'https://www.atlassian.com/es/agile', 'guide', 'beginner', 'es', true, ['scrum-master', 'project-manager', 'product-manager', 'analista-funcional', 'qa-manual'], 'Guías claras de Scrum, Kanban y metodologías ágiles en español.'],
            ['La Guía Scrum', 'https://scrumguides.org/', 'guide', 'beginner', 'en', true, ['scrum-master', 'product-manager', 'project-manager'], 'La guía oficial de Scrum. Tiene traducción al español.'],
            ['Scrum.org Open Assessments', 'https://www.scrum.org/open-assessments', 'challenge', 'intermediate', 'en', true, ['scrum-master', 'product-manager'], 'Evaluaciones gratuitas para practicar la certificación PSM.'],
            ['Google Project Management', 'https://www.coursera.org/professional-certificates/google-project-management', 'course', 'beginner', 'en', false, ['project-manager'], 'Certificado profesional de gestión de proyectos de Google.'],
            ['Historias de usuario (Mountain Goat)', 'https://www.mountaingoatsoftware.com/agile/user-stories', 'guide', 'beginner', 'en', true, ['analista-funcional', 'product-manager'], 'Cómo escribir buenas historias de usuario.'],
            ['Tutorial de BPMN (Camunda)', 'https://camunda.com/bpmn/', 'guide', 'intermediate', 'en', true, ['analista-funcional'], 'Introducción al modelado de procesos con BPMN.'],

            // marketing y contenido
            ['Guía de SEO para principiantes de Google', 'https://developers.google.com/search/docs/fundamentals/seo-starter-guide?hl=es', 'guide', 'beginner', 'es', true, ['especialista-seo', 'marketing-digital', 'technical-writer'], 'La guía oficial de Google para empezar con SEO.'],
            ['Google Search Central', 'https://developers.google.com/search/docs?hl=es', 'guide', 'intermediate', 'es', true, ['especialista-seo'], 'Documentación oficial de búsqueda de Google.'],
            ['Beginner\'s Guide to SEO (Moz)', 'https://moz.com/beginners-guide-to-seo', 'guide', 'beginner', 'en', true, ['especialista-seo'], 'Guía clásica y completa para aprender SEO.'],
            ['Ahrefs Academy', 'https://ahrefs.com/academy', 'course', 'intermediate', 'en', true, ['especialista-seo'], 'Cursos gratuitos de SEO y marketing de contenidos.'],
            ['Semrush Academy', 'https://www.semrush.com/academy/', 'course', 'intermediate', 'en', true, ['especialista-seo', 'marketing-digital'], 'Cursos gratuitos de SEO, contenido y publicidad.'],
            ['Google Skillshop', 'https://skillshop.withgoogle.com/', 'course', 'beginner', 'es', true, ['marketing-digital', 'especialista-seo', 'creador-de-contenido'], 'Certificaciones gratuitas de Google Ads y Analytics, en español.'],
            ['HubSpot Academy', 'https://academy.hubspot.com/', 'course', 'beginner', 'en', true, ['marketing-digital', 'creador-de-contenido'], 'Certificaciones gratuitas de inbound marketing, email y contenido.'],
            ['Meta Business: centro de aprendizaje', 'https://www.facebook.com/business/learn', 'course', 'beginner', 'es', true, ['marketing-digital'], 'Cursos oficiales de publicidad en Facebook e Instagram.'],
            ['Technical Writing de Google', 'https://developers.google.com/tech-writing', 'course', 'beginner', 'en', true, ['technical-writer', 'analista-funcional'], 'Cursos gratuitos de escritura técnica.'],
            ['Write the Docs', 'https://www.writethedocs.org/guide/', 'community', 'beginner', 'en', true, ['technical-writer'], 'Comunidad y guía sobre documentación de software.'],
            ['Markdown Guide', 'https://www.markdownguide.org/', 'guide', 'beginner', 'en', true, ['technical-writer'], 'Todo sobre Markdown, el formato de la documentación moderna.'],
            ['Roadmap Technical Writer', 'https://roadmap.sh/technical-writer', 'roadmap', 'beginner', 'en', true, ['technical-writer'], 'Mapa visual para convertirte en Technical Writer.'],

            ['YouTube Creators', 'https://www.youtube.com/creators/', 'guide', 'beginner', 'es', true, ['creador-de-contenido'], 'Recursos oficiales de YouTube para crear y hacer crecer un canal.'],
            ['TikTok Creator Portal', 'https://www.tiktok.com/creators/creator-portal/', 'guide', 'beginner', 'en', true, ['creador-de-contenido'], 'Guías oficiales de TikTok para creadores.'],
            ['Entrenamiento de DaVinci Resolve', 'https://www.blackmagicdesign.com/products/davinciresolve/training', 'course', 'intermediate', 'en', true, ['creador-de-contenido'], 'Cursos oficiales y gratuitos del editor de video DaVinci Resolve.'],

            // comunidades
            ['Stack Overflow en español', 'https://es.stackoverflow.com/', 'community', 'beginner', 'es', true, [...$dev, 'data-analyst', 'data-scientist', 'qa-automation'], 'Preguntas y respuestas de programación en español.'],
            ['DEV Community', 'https://dev.to/', 'community', 'beginner', 'en', true, [...$dev, 'technical-writer', 'creador-de-contenido', 'devops-engineer'], 'Comunidad para leer y publicar artículos sobre tecnología.'],

            // empleo
            ['Get on Board', 'https://www.getonbrd.com/', 'job_board', 'beginner', 'es', true, ['*'], 'Empleos tech en Latinoamérica, muchos remotos y con sueldo publicado.'],
            ['LinkedIn Empleos', 'https://www.linkedin.com/jobs/', 'job_board', 'beginner', 'es', true, ['*'], 'La red profesional más usada para buscar trabajo.'],
            ['Torre', 'https://torre.ai/', 'job_board', 'beginner', 'es', true, ['*'], 'Plataforma de empleo remoto con foco en Latinoamérica.'],
            ['Workana', 'https://www.workana.com/', 'job_board', 'beginner', 'es', true, ['frontend-developer', 'backend-developer', 'ux-ui-designer', 'especialista-seo', 'marketing-digital', 'technical-writer', 'creador-de-contenido'], 'Proyectos freelance en español para sumar experiencia.'],
            ['Remote OK', 'https://remoteok.com/', 'job_board', 'intermediate', 'en', true, ['*'], 'Empleos 100% remotos de todo el mundo.'],
            ['We Work Remotely', 'https://weworkremotely.com/', 'job_board', 'intermediate', 'en', true, ['*'], 'Una de las bolsas de trabajo remoto más grandes.'],
        ];
    }
}
