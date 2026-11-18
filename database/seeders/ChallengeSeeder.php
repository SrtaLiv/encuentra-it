<?php

namespace Database\Seeders;

use App\Models\Career;
use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    private const AUDIT_TITLE = 'Auditá a la IA';

    // fundamentals people must own without help; everything else defaults by level
    private const WITHOUT_AI = [
        'Tu tarjeta de presentación',
        'Calculadora por consola',
        'Diseñá una base de datos',
        'Primeras consultas SQL',
        'Estadística aplicada',
        'Entendé un LLM por dentro',
        'Redes en la práctica',
        'Cazá un phishing',
        'Primeros niveles de Bandit',
        'Política de contraseñas',
        'Ejercicios de Linux',
        'Documentá tu PC',
        'Casos de prueba de un login',
        'Reportá bugs',
        'Analizá una app que uses',
        'Leé la Guía Scrum',
        'Relevá un proceso',
        'Explicá algo técnico simple',
        'Encontrá tu nicho',
    ];

    public function run(): void
    {
        $careers = Career::query()->whereIn('slug', array_keys($this->challenges()))->get()->keyBy('slug');

        foreach ($this->challenges() as $slug => $challenges) {
            $career = $careers->get($slug);

            if (! $career) {
                continue;
            }

            if ($career->challenges()->exists()) {
                $this->addAuditToExisting($career);

                continue;
            }

            foreach ($this->withAudit($slug, $challenges) as $index => $challenge) {
                $career->challenges()->create([...$challenge, 'position' => $index]);
            }
        }
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function withAudit(string $slug, array $challenges): array
    {
        $rows = array_map(fn (array $challenge) => $this->row(...$challenge), $challenges);
        $audit = $this->auditRow($slug);

        if (! $audit) {
            return $rows;
        }

        // the audit fits right after the beginner block: enough context to spot errors
        $beginners = count(array_filter($rows, fn (array $row) => $row['level'] === 'beginner'));
        array_splice($rows, $beginners, 0, [$audit]);

        return $rows;
    }

    private function addAuditToExisting(Career $career): void
    {
        $audit = $this->auditRow($career->slug);

        if (! $audit || $career->challenges()->where('title', self::AUDIT_TITLE)->exists()) {
            return;
        }

        $career->challenges()->create([...$audit, 'position' => (int) $career->challenges()->max('position') + 1]);
    }

    private function row(string $level, string $title, string $description, string $deliverable): array
    {
        $aiMode = match (true) {
            in_array($title, self::WITHOUT_AI, true) => 'none',
            $level === 'advanced' => 'allowed',
            default => 'tutor',
        };

        return compact('level', 'title', 'description', 'deliverable') + ['ai_mode' => $aiMode, 'solution' => null];
    }

    private function auditRow(string $slug): ?array
    {
        $audit = $this->audits()[$slug] ?? null;

        if (! $audit) {
            return null;
        }

        [$answer, $solution] = $audit;

        return [
            'level' => 'intermediate',
            'ai_mode' => 'none',
            'title' => self::AUDIT_TITLE,
            'description' => "Alguien le preguntó a una IA y recibió esta respuesta. Tiene 3 errores. Encontralos sin usar IA, corregilos y buscá una fuente confiable (documentación oficial, un libro o un sitio reconocido) que respalde cada corrección.

\"{$answer}\"",
            'deliverable' => 'Los 3 errores, la corrección de cada uno y la fuente que la respalda.',
            'solution' => $solution,
        ];
    }

    // career slug => [[level, title, instructions, deliverable], ...]
    private function challenges(): array
    {
        return [
            'frontend-developer' => [
                ['beginner', 'Tu tarjeta de presentación', 'Creá una página con tu nombre, una foto, una breve bio y links a tus redes. Solo HTML y CSS, sin frameworks. Tiene que verse bien en el celular.', 'Página publicada en GitHub Pages o Netlify.'],
                ['beginner', 'Maquetá un diseño de Frontend Mentor', 'Elegí un desafío "Newbie" de Frontend Mentor y replicalo lo más fiel posible, incluida la versión mobile.', 'Link al sitio publicado y al repositorio.'],
                ['intermediate', 'Lista de tareas con JavaScript', 'Hacé una app de tareas: agregar, marcar como hecha, borrar y filtrar. Que las tareas se guarden en localStorage y no se pierdan al recargar.', 'App publicada y repositorio con README.'],
                ['intermediate', 'Consumí una API pública', 'Usá una API gratuita (clima, Pokémon, películas) y mostrá los datos con un buscador. Manejá el estado de carga y los errores.', 'App publicada que muestre carga, error y resultados.'],
                ['advanced', 'Proyecto en React con rutas', 'Armá una app en React con al menos 3 pantallas, navegación, un formulario validado y datos de una API.', 'Repositorio con README que explique cómo correrlo + link al deploy.'],
                ['advanced', 'Auditoría de accesibilidad', 'Pasá Lighthouse por uno de tus proyectos y corregí todo lo que puedas hasta superar 90 en Accesibilidad y Performance.', 'Captura del reporte antes y después.'],
            ],
            'backend-developer' => [
                ['beginner', 'Calculadora por consola', 'Programá una calculadora que pida dos números y una operación por consola, y maneje errores como dividir por cero o escribir letras.', 'Repositorio con el código.'],
                ['beginner', 'Diseñá una base de datos', 'Modelá la base de una biblioteca: libros, autores, socios y préstamos. Creá las tablas en SQL e insertá datos de prueba.', 'Script SQL + diagrama de tablas.'],
                ['intermediate', 'Tu primera API REST', 'Creá una API de tareas con endpoints para listar, crear, editar y borrar. Validá los datos y devolvé códigos HTTP correctos.', 'Repositorio + colección de Postman o documentación de endpoints.'],
                ['intermediate', 'Login con autenticación', 'Agregá registro y login a tu API. Las contraseñas se guardan hasheadas y los endpoints de tareas solo funcionan con sesión o token.', 'Repositorio con instrucciones para probar el login.'],
                ['advanced', 'Tests automatizados', 'Escribí tests para los endpoints principales de tu API, incluidos los casos de error.', 'Repositorio donde los tests pasan con un solo comando.'],
                ['advanced', 'API en producción', 'Publicá tu API con base de datos en un servicio gratuito o barato, con variables de entorno y HTTPS.', 'URL pública de la API funcionando.'],
            ],
            'mobile-developer' => [
                ['beginner', 'Hola mundo en tu celular', 'Instalá el entorno (Expo o Flutter), creá una app con tu nombre y un botón que cuente clics, y probala en tu propio celular.', 'Captura o video de la app corriendo en tu celular.'],
                ['beginner', 'App de notas', 'Hacé una app donde puedas escribir notas y verlas en una lista. Que se guarden al cerrar la app.', 'Repositorio + video corto mostrando la app.'],
                ['intermediate', 'App con varias pantallas', 'Armá una app con navegación entre al menos 3 pantallas: lista, detalle y formulario.', 'Repositorio + video del flujo completo.'],
                ['intermediate', 'Consumí una API', 'Mostrá datos de una API pública con carga, error y "tirar para actualizar".', 'Repositorio + video de la app.'],
                ['advanced', 'Usá una función nativa', 'Sumá cámara, geolocalización o notificaciones a una de tus apps.', 'Video mostrando la función nativa funcionando.'],
                ['advanced', 'Publicá una versión de prueba', 'Generá un build instalable y compartilo con 3 personas para que lo prueben. Juntá su feedback.', 'Link de descarga o de testing + resumen del feedback.'],
            ],
            'data-analyst' => [
                ['beginner', 'Tu presupuesto en una planilla', 'Registrá tus gastos de un mes en Google Sheets, categorizalos y armá una tabla dinámica y un gráfico por categoría.', 'Link a la planilla (en modo lectura).'],
                ['beginner', 'Primeras consultas SQL', 'Completá las lecciones 1 a 12 de SQLBolt.', 'Captura del progreso completado.'],
                ['intermediate', 'Analizá un dataset público', 'Descargá un dataset de Kaggle o de datos abiertos de tu país y respondé 5 preguntas concretas usando SQL o planillas.', 'Documento con las 5 preguntas, cómo las respondiste y las conclusiones.'],
                ['intermediate', 'Tu primer dashboard', 'Con ese dataset, armá un dashboard en Looker Studio o Power BI con filtros y al menos 4 visualizaciones.', 'Link al dashboard publicado.'],
                ['advanced', 'Limpieza con Python', 'Tomá un dataset "sucio" (duplicados, nulos, formatos mezclados) y limpialo con pandas en un notebook.', 'Notebook publicado en GitHub o Kaggle.'],
                ['advanced', 'Caso de negocio completo', 'Elegí un problema real (ventas, abandono, encuestas) y presentá un informe: pregunta, datos, análisis, conclusiones y recomendación.', 'Presentación o artículo publicado.'],
            ],
            'data-scientist' => [
                ['beginner', 'Explorá un dataset', 'En un notebook, cargá el dataset del Titanic de Kaggle y describilo: tipos de datos, nulos, distribuciones y 3 gráficos.', 'Notebook publicado.'],
                ['beginner', 'Estadística aplicada', 'Con ese dataset, calculá promedio, mediana y desvío de 3 variables y explicá con tus palabras qué significan.', 'Notebook con las explicaciones.'],
                ['intermediate', 'Tu primer modelo', 'Entrená un modelo de clasificación para el Titanic y subí tu predicción a la competencia de Kaggle.', 'Captura de tu puntaje en Kaggle.'],
                ['intermediate', 'Mejorá tu modelo', 'Probá al menos 3 algoritmos y feature engineering. Compará resultados con validación cruzada.', 'Notebook con la tabla comparativa.'],
                ['advanced', 'Proyecto con datos propios', 'Elegí un problema que te interese, conseguí los datos, entrená un modelo y explicá sus limitaciones.', 'Repositorio con README de conclusiones.'],
                ['advanced', 'Contalo para no técnicos', 'Escribí un artículo explicando tu proyecto para alguien que no sabe de datos.', 'Artículo publicado en un blog, LinkedIn o Medium.'],
            ],
            'ai-engineer' => [
                ['beginner', 'Entendé un LLM por dentro', 'Escribí con tus palabras qué es un token, una ventana de contexto y una alucinación, con un ejemplo de cada uno.', 'Post o documento publicado.'],
                ['beginner', 'Tu primera llamada a una API de IA', 'Con Python, enviá un prompt a una API de un modelo de lenguaje y mostrá la respuesta por consola.', 'Repositorio con el script (sin la API key).'],
                ['intermediate', 'Chatbot con memoria', 'Hacé un chatbot por consola o web que recuerde la conversación y tenga un rol definido (por ejemplo, tutor de inglés).', 'Repositorio + video corto de una conversación.'],
                ['intermediate', 'Preguntale a tus documentos (RAG)', 'Cargá 3 o más PDFs, dividilos en fragmentos, generá embeddings y respondé preguntas citando la fuente.', 'Repositorio + ejemplos de preguntas y respuestas.'],
                ['advanced', 'Evaluá tu sistema', 'Armá un set de 20 preguntas con respuesta esperada y medí qué tan bien responde tu RAG. Probá mejorarlo.', 'Tabla de resultados antes y después.'],
                ['advanced', 'Publicalo', 'Deployá tu app de IA con una interfaz simple y límites de uso para no gastar de más.', 'URL pública funcionando.'],
            ],
            'analista-soc' => [
                ['beginner', 'Redes en la práctica', 'Usá ping, traceroute y nslookup contra 3 sitios y explicá qué te muestra cada comando.', 'Documento con capturas y explicaciones.'],
                ['beginner', 'Cazá un phishing', 'Juntá 3 mails sospechosos (de tu spam o ejemplos públicos) y señalá las pistas que indican que son phishing.', 'Documento con el análisis de cada mail.'],
                ['intermediate', 'Analizá tráfico con Wireshark', 'Capturá tráfico mientras navegás y encontrá una consulta DNS, un handshake TCP y una petición HTTP.', 'Capturas comentadas.'],
                ['intermediate', 'Completá una sala de Blue Team', 'Terminá una sala gratuita de SOC o análisis de logs en TryHackMe o LetsDefend.', 'Captura de la sala completada.'],
                ['advanced', 'Informe de incidente', 'Resolvé un caso de investigación (en LetsDefend o similar) y escribí un informe: qué pasó, cómo lo detectaste y qué recomendás.', 'Informe en PDF o publicado.'],
            ],
            'pentester' => [
                ['beginner', 'Primeros niveles de Bandit', 'Resolvé los niveles 0 a 10 de OverTheWire Bandit y anotá qué comando aprendiste en cada uno.', 'Tus notas publicadas (sin las contraseñas).'],
                ['beginner', 'Escaneá tu propia red', 'Instalá Nmap y escaneá tu red de casa. Identificá dispositivos y puertos abiertos. Solo en tu red, nunca en redes ajenas.', 'Informe con lo que encontraste y qué significa.'],
                ['intermediate', 'Laboratorios de SQL injection', 'Completá los laboratorios de nivel Apprentice de SQL injection en PortSwigger Web Security Academy.', 'Captura de los laboratorios resueltos.'],
                ['intermediate', 'Tu primera máquina', 'Comprometé una máquina fácil en TryHackMe o Hack The Box.', 'Writeup paso a paso (de una máquina retirada o pública).'],
                ['advanced', 'Participá en un CTF', 'Anotate en un CTF online (como picoCTF) y resolvé al menos 5 retos.', 'Writeups de los retos resueltos.'],
                ['advanced', 'Informe de pentest profesional', 'Sobre una máquina vulnerable de laboratorio, escribí un informe con resumen ejecutivo, hallazgos, severidad y recomendaciones.', 'Informe en PDF.'],
            ],
            'analista-grc' => [
                ['beginner', 'Política de contraseñas', 'Redactá una política de contraseñas para una empresa chica: largo, renovación, gestor de contraseñas y doble factor.', 'Documento de 1 página.'],
                ['beginner', 'Inventario de activos', 'Listá los activos de información de un negocio imaginario (una clínica, una tienda): datos, sistemas, personas y proveedores.', 'Planilla con el inventario.'],
                ['intermediate', 'Matriz de riesgos', 'Para esos activos, identificá 10 riesgos y evaluá probabilidad e impacto. Proponé un control para cada uno.', 'Matriz de riesgos en una planilla.'],
                ['intermediate', 'Checklist de ISO 27001', 'Elegí 10 controles del Anexo A de ISO 27001 y evaluá si el negocio imaginario los cumple.', 'Checklist con estado y acciones.'],
                ['advanced', 'Plan de respuesta a incidentes', 'Escribí qué tiene que hacer la empresa si sufre un ransomware: roles, pasos, comunicación y recuperación.', 'Documento del plan.'],
            ],
            'devops-engineer' => [
                ['beginner', 'Script de backup', 'Escribí un script en Bash que comprima una carpeta, le ponga la fecha al nombre y borre los backups de más de 7 días.', 'Repositorio con el script y un README.'],
                ['beginner', 'Ejercicios de Linux', 'Resolvé 20 preguntas de la sección Linux de devops-exercises sin mirar las respuestas, y después corregite.', 'Tus respuestas en un repositorio.'],
                ['intermediate', 'Dockerizá una app', 'Escribí un Dockerfile para una app simple y un docker-compose con la app y una base de datos.', 'Repositorio que levante todo con un solo comando.'],
                ['intermediate', 'Pipeline de CI', 'Configurá GitHub Actions para que en cada push se corran los tests y se construya la imagen de Docker.', 'Repositorio con el badge del pipeline en verde.'],
                ['advanced', 'Infraestructura como código', 'Creá con Terraform un servidor y una red en un proveedor cloud (usando la capa gratuita) y destruilo al terminar.', 'Repositorio con el código de Terraform.'],
                ['advanced', 'Kubernetes local', 'Deployá tu app en un cluster local (kind o minikube) con un Deployment, un Service y variables de entorno.', 'Repositorio con los manifiestos + captura funcionando.'],
            ],
            'cloud-engineer' => [
                ['beginner', 'Creá tu cuenta con alertas', 'Abrí una cuenta en AWS, Azure o GCP con la capa gratuita y configurá una alerta de presupuesto antes de crear cualquier cosa.', 'Captura de la alerta de presupuesto configurada.'],
                ['beginner', 'Sitio estático en la nube', 'Publicá una página HTML usando almacenamiento de objetos (S3, Blob Storage o Cloud Storage).', 'URL del sitio publicado.'],
                ['intermediate', 'Servidor con acceso seguro', 'Creá una máquina virtual, conectate por SSH con llave y configurá el firewall para abrir solo los puertos necesarios.', 'Documento con los pasos y capturas.'],
                ['intermediate', 'Permisos mínimos', 'Creá un usuario que solo pueda leer un bucket específico y probá que no puede hacer nada más.', 'Política de permisos + capturas de las pruebas.'],
                ['advanced', 'Arquitectura de 3 capas', 'Diseñá y deployá una app con frontend, backend y base de datos gestionada. Dibujá el diagrama.', 'Diagrama de arquitectura + URL funcionando.'],
                ['advanced', 'Simulacro de certificación', 'Hacé un examen de práctica de la certificación fundamentals de tu proveedor y repasá cada error.', 'Puntaje obtenido y lista de temas a reforzar.'],
            ],
            'soporte-it-sysadmin' => [
                ['beginner', 'Documentá tu PC', 'Averiguá y anotá los componentes de tu computadora: procesador, RAM, disco, sistema operativo y dirección IP.', 'Ficha técnica de tu equipo.'],
                ['beginner', 'Instalá una máquina virtual', 'Con VirtualBox, instalá Ubuntu en una máquina virtual y creá un usuario nuevo.', 'Captura de la VM funcionando.'],
                ['intermediate', 'Resolvé tickets ficticios', 'Escribí cómo resolverías 5 problemas típicos: "no tengo internet", "la impresora no imprime", "olvidé mi contraseña", "la PC está lenta", "no me llegan mails".', 'Documento con un procedimiento paso a paso para cada ticket.'],
                ['intermediate', 'Servidor de archivos', 'En tu VM, configurá una carpeta compartida accesible desde otra computadora de tu red.', 'Pasos documentados + captura.'],
                ['advanced', 'Troubleshooting real', 'Resolvé 3 escenarios en SadServers.', 'Captura de los escenarios resueltos y qué aprendiste de cada uno.'],
            ],
            'qa-manual' => [
                ['beginner', 'Casos de prueba de un login', 'Escribí 10 casos de prueba para el login de Sauce Demo, incluidos casos negativos (contraseña mala, campos vacíos, usuario bloqueado).', 'Planilla con ID, pasos, datos y resultado esperado.'],
                ['beginner', 'Reportá bugs', 'Explorá Sauce Demo con el usuario "problem_user" y reportá al menos 5 bugs con título, pasos, resultado esperado, resultado real y captura.', 'Documento o planilla con los 5 reportes.'],
                ['intermediate', 'Plan de pruebas', 'Escribí un plan de pruebas para la compra completa de Automation Exercise: alcance, qué se prueba, qué no, riesgos y criterios de salida.', 'Documento del plan.'],
                ['intermediate', 'Probá una API sin código', 'Con Postman, probá los endpoints de Restful Booker: crear, consultar, modificar y borrar una reserva. Verificá los códigos de respuesta.', 'Colección de Postman exportada.'],
                ['advanced', 'Ciclo completo en Jira', 'Creá un proyecto gratuito en Jira, cargá tus casos y bugs y simulá un sprint de testing.', 'Capturas del tablero y de los reportes.'],
            ],
            'qa-automation' => [
                ['beginner', 'Tu primer test automático', 'Instalá Playwright o Cypress y escribí un test que haga login en Sauce Demo y verifique que aparecen los productos.', 'Repositorio con el test pasando.'],
                ['beginner', 'Casos negativos', 'Automatizá los casos de login fallido: contraseña incorrecta, usuario bloqueado y campos vacíos.', 'Repositorio con los tests.'],
                ['intermediate', 'Flujo de compra end-to-end', 'Automatizá la compra completa: agregar al carrito, checkout y confirmación. Usá Page Object Model.', 'Repositorio organizado con page objects.'],
                ['intermediate', 'Tests de API', 'Automatizá con código las pruebas de Restful Booker (crear, consultar, editar y borrar) con validaciones del body.', 'Repositorio con los tests de API.'],
                ['advanced', 'Tests en CI', 'Configurá GitHub Actions para correr tu suite en cada push y guardar el reporte como artefacto.', 'Repositorio con el pipeline en verde.'],
            ],
            'ux-ui-designer' => [
                ['beginner', 'Rediseñá una pantalla', 'Elegí una pantalla de una app que uses y te moleste. Rediseñala en Figma y explicá qué cambiaste y por qué.', 'Link al archivo de Figma con el antes y el después.'],
                ['beginner', 'Daily UI x7', 'Hacé 7 días seguidos de desafíos de Daily UI.', 'Los 7 diseños publicados en Behance, Dribbble o LinkedIn.'],
                ['intermediate', 'Entrevistá a 3 personas', 'Elegí un problema cotidiano (organizar gastos, turnos médicos), entrevistá a 3 personas y sacá conclusiones.', 'Resumen de hallazgos y una persona usuaria.'],
                ['intermediate', 'Prototipo navegable', 'Con lo que aprendiste, diseñá un flujo de 5 pantallas o más y conectalas en un prototipo de Figma.', 'Link al prototipo.'],
                ['advanced', 'Test de usabilidad', 'Probá tu prototipo con 3 personas, anotá dónde se trabaron y hacé una segunda versión.', 'Informe de hallazgos y prototipo actualizado.'],
                ['advanced', 'Caso de estudio', 'Contá el proceso completo: problema, investigación, decisiones, iteraciones y resultado.', 'Caso publicado en Behance, Notion o tu portfolio.'],
            ],
            'product-manager' => [
                ['beginner', 'Analizá una app que uses', 'Elegí una app y escribí: qué problema resuelve, para quién, cómo gana plata y cuáles son sus métricas clave.', 'Documento de 1 página.'],
                ['beginner', 'Historias de usuario', 'Escribí 10 historias de usuario con criterios de aceptación para una nueva función de esa app.', 'Documento con las historias.'],
                ['intermediate', 'Priorizá un backlog', 'Con 15 ideas de mejora, priorizalas con el método RICE y justificá el top 3.', 'Planilla con el puntaje RICE.'],
                ['intermediate', 'Entrevistas de descubrimiento', 'Hablá con 5 personas usuarias sobre su problema (no sobre tu solución) y sacá patrones.', 'Resumen de insights.'],
                ['advanced', 'PRD completo', 'Escribí un documento de producto para una función: problema, objetivo, métricas de éxito, alcance, fuera de alcance y riesgos.', 'PRD publicado en Notion o Google Docs.'],
            ],
            'scrum-master' => [
                ['beginner', 'Leé la Guía Scrum', 'Leé la Guía Scrum completa y armá un mapa con roles, eventos y artefactos.', 'Mapa o diagrama publicado.'],
                ['beginner', 'Tablero Kanban personal', 'Organizá tus tareas de una semana en un tablero Kanban (Trello o Jira) con límite de trabajo en curso.', 'Captura del tablero al final de la semana.'],
                ['intermediate', 'Facilitá una retrospectiva', 'Facilitá una retro con amigos, familia o un grupo de estudio sobre algo que hicieron juntos. Usá una dinámica concreta.', 'Resumen de la retro y acciones acordadas.'],
                ['intermediate', 'Simulacro de PSM I', 'Hacé el Open Assessment de Scrum.org hasta sacar 100% tres veces seguidas.', 'Captura de los resultados.'],
                ['advanced', 'Sprint real', 'Sumate a un proyecto de la comunidad u open source y facilitá un sprint completo: planning, dailies, review y retro.', 'Informe del sprint con aprendizajes.'],
            ],
            'project-manager' => [
                ['beginner', 'Planificá un evento', 'Planificá un evento real o imaginario (un meetup, un cumpleaños grande): alcance, tareas, responsables y fechas.', 'Plan del proyecto.'],
                ['beginner', 'Diagrama de Gantt', 'Pasá ese plan a un diagrama de Gantt con dependencias entre tareas.', 'Captura o link al Gantt.'],
                ['intermediate', 'Registro de riesgos', 'Identificá 8 riesgos del proyecto, su probabilidad, impacto y plan de respuesta.', 'Planilla del registro de riesgos.'],
                ['intermediate', 'Reporte de estado', 'Escribí un reporte semanal para un cliente: avance, desvíos, riesgos y próximos pasos, en una página.', 'Reporte de ejemplo.'],
                ['advanced', 'Gestioná un proyecto real', 'Coordiná un proyecto chico de punta a punta (de la comunidad, voluntariado o personal) y hacé un cierre con lecciones aprendidas.', 'Documento de cierre del proyecto.'],
            ],
            'analista-funcional' => [
                ['beginner', 'Relevá un proceso', 'Elegí un proceso real (inscripción en una facultad, pedir un turno) y describí paso a paso cómo funciona hoy.', 'Documento con el proceso actual.'],
                ['beginner', 'Diagramalo', 'Diagramá ese proceso en BPMN con draw.io o Bizagi Modeler.', 'Diagrama exportado.'],
                ['intermediate', 'Proponé una mejora', 'Detectá 3 problemas del proceso y diseñá cómo sería con un sistema que lo resuelva.', 'Diagrama del proceso mejorado + justificación.'],
                ['intermediate', 'Documento de requerimientos', 'Escribí los requerimientos funcionales y no funcionales del sistema, con historias de usuario y criterios de aceptación.', 'Documento de requerimientos.'],
                ['advanced', 'Wireframes y validación', 'Armá wireframes de las pantallas clave y validalos con 2 personas que usen el proceso.', 'Wireframes + feedback recibido.'],
            ],
            'especialista-seo' => [
                ['beginner', 'Auditá un sitio', 'Elegí el sitio de un negocio local y revisá títulos, meta descripciones, encabezados, velocidad y versión mobile.', 'Informe con 10 mejoras priorizadas.'],
                ['beginner', 'Investigación de palabras clave', 'Para ese negocio, armá una lista de 30 palabras clave con intención de búsqueda y dificultad estimada.', 'Planilla de keywords.'],
                ['intermediate', 'Escribí un artículo optimizado', 'Creá un blog gratuito y publicá un artículo optimizado para una palabra clave de cola larga.', 'Link al artículo publicado.'],
                ['intermediate', 'Search Console', 'Conectá tu blog a Google Search Console, enviá el sitemap y verificá que tus páginas se indexen.', 'Captura de Search Console con páginas indexadas.'],
                ['advanced', 'Crecé durante 3 meses', 'Publicá al menos 2 artículos por mes y medí impresiones, clics y posiciones.', 'Informe de evolución con capturas.'],
            ],
            'marketing-digital' => [
                ['beginner', 'Analizá una marca', 'Elegí una marca que sigas y analizá su estrategia en redes: público, tono, formatos, frecuencia y qué contenido funciona mejor.', 'Análisis en una presentación corta.'],
                ['beginner', 'Calendario de contenidos', 'Armá un calendario de un mes para un emprendimiento real o imaginario, con objetivo por publicación.', 'Planilla del calendario.'],
                ['intermediate', 'Campaña de mail', 'Creá una cuenta gratuita en una herramienta de email marketing y diseñá una secuencia de bienvenida de 3 mails.', 'Capturas de los mails y de la secuencia.'],
                ['intermediate', 'Planificá una campaña paga', 'Diseñá una campaña de Meta Ads o Google Ads: objetivo, público, presupuesto, anuncios y métricas. No hace falta lanzarla.', 'Plan de campaña documentado.'],
                ['advanced', 'Experimento A/B', 'Probá dos versiones de un mismo contenido (título, imagen o llamado a la acción) y medí cuál rinde mejor.', 'Resultados con conclusiones.'],
                ['advanced', 'Certificate gratis', 'Completá una certificación gratuita de Google Skillshop o HubSpot Academy.', 'Link o captura del certificado.'],
            ],
            'technical-writer' => [
                ['beginner', 'Explicá algo técnico simple', 'Escribí una guía para alguien que nunca lo hizo: "cómo crear una cuenta de GitHub y subir tu primer archivo".', 'Guía publicada en un blog o en GitHub.'],
                ['beginner', 'README de un proyecto', 'Elegí un repositorio open source con mala documentación y reescribí su README: qué es, cómo instalarlo y cómo usarlo.', 'Tu versión del README publicada.'],
                ['intermediate', 'Documentá una API', 'Elegí una API pública y escribí la documentación de 3 endpoints con ejemplos de request y response.', 'Documentación publicada.'],
                ['intermediate', 'Tutorial paso a paso', 'Escribí un tutorial de algo que hayas aprendido hace poco, con capturas y errores comunes.', 'Tutorial publicado.'],
                ['advanced', 'Contribuí a open source', 'Hacé un pull request que mejore la documentación de un proyecto open source.', 'Link al pull request.'],
            ],
            'creador-de-contenido' => [
                ['beginner', 'Creá tu cuenta profesional', 'Elegí una plataforma principal (YouTube, TikTok, Instagram o LinkedIn) y creá una cuenta dedicada: nombre claro, foto, bio que diga de qué hablás y para quién.', 'Link a tu perfil.'],
                ['beginner', 'Encontrá tu nicho', 'Escribí 10 temas tech que te interesen. Para cada uno, buscá 3 creadores que ya hablen de eso y anotá qué les falta. Elegí un nicho concreto (por ejemplo: "QA para principiantes en español").', 'Documento con tu nicho elegido y por qué.'],
                ['beginner', 'Subí tu primer video', 'Grabá con el celular un video de menos de 60 segundos explicando algo de tu nicho. No importa que no salga perfecto: lo importante es publicarlo.', 'Link a tu primer video publicado.'],
                ['intermediate', '7 días seguidos', 'Publicá un contenido por día durante 7 días. Probá distintos formatos: tutorial, opinión, error común, "un día en mi vida aprendiendo".', 'Links a las 7 publicaciones.'],
                ['intermediate', 'Mejorá el gancho', 'Mirá la retención de tus videos e identificá dónde se va la gente. Rehacé tu peor video con un gancho nuevo en los primeros 3 segundos.', 'Comparación de métricas entre las dos versiones.'],
                ['intermediate', 'Edición con subtítulos', 'Editá un video con cortes dinámicos, subtítulos y una miniatura o portada pensada para el clic.', 'Link al video editado.'],
                ['advanced', 'Analizá tus números', 'Después de 30 días, revisá tus métricas: qué contenido funcionó, en qué horarios y con qué formato. Definí tu estrategia para el mes siguiente.', 'Informe de 1 página con datos y próximos pasos.'],
                ['advanced', 'Colaborá con otro creador', 'Proponé y hacé un contenido en conjunto con alguien de tu nicho o de la comunidad.', 'Link al contenido colaborativo.'],
            ],
        ];
    }

    // career slug => [answer with 3 planted errors, solution]
    private function audits(): array
    {
        return [
            'frontend-developer' => [
                'Con display: flex los elementos se ordenan en columna por defecto. Para centrar un elemento horizontal y verticalmente alcanza con text-align: center. Y en JavaScript let y const son equivalentes: las dos permiten reasignar el valor.',
                "1. Flexbox ordena en fila por defecto (flex-direction: row).\n2. text-align: center solo centra contenido en línea y en horizontal. Para centrar en ambos ejes se usa, por ejemplo, display: flex con justify-content y align-items en center.\n3. const no permite reasignar la variable; let sí.\nFuente: MDN Web Docs.",
            ],
            'backend-developer' => [
                'Para guardar contraseñas de forma segura, encriptalas con MD5. Un endpoint que crea un recurso tiene que responder 200 OK. Y si usás un ORM, la inyección SQL es imposible en cualquier caso.',
                "1. Las contraseñas no se encriptan: se hashean con un algoritmo lento y con salt como bcrypt o Argon2. MD5 está roto.\n2. Al crear un recurso lo correcto es 201 Created.\n3. Un ORM protege si usás sus consultas parametrizadas, pero las consultas crudas con datos concatenados siguen siendo vulnerables.\nFuentes: OWASP Password Storage Cheat Sheet, MDN (códigos HTTP).",
            ],
            'mobile-developer' => [
                'Las apps hechas con React Native son páginas web dentro de un WebView. Para publicar en Google Play solo tenés que subir el código fuente del proyecto. Y Flutter usa JavaScript como lenguaje.',
                "1. React Native dibuja componentes nativos reales, no un WebView.\n2. A Google Play se sube un build firmado (Android App Bundle) desde una cuenta de desarrollador, no el código fuente.\n3. Flutter usa Dart.\nFuentes: documentación de React Native, Flutter y Google Play Console.",
            ],
            'data-analyst' => [
                'La mediana siempre da igual que el promedio. En SQL, WHERE se usa para filtrar los resultados después de un GROUP BY. Y si dos variables tienen una correlación de 0,9, eso prueba que una causa la otra.',
                "1. Mediana y promedio solo coinciden en distribuciones simétricas; con valores extremos se separan.\n2. Para filtrar después de agrupar se usa HAVING. WHERE filtra filas antes de agrupar.\n3. Correlación no implica causalidad: puede haber una tercera variable o pura coincidencia.\nFuentes: documentación de SQL, cualquier libro de estadística introductoria.",
            ],
            'data-scientist' => [
                'Si tu modelo tiene 99% de accuracy en los datos de entrenamiento, ya está listo para producción. Para evaluarlo conviene usar los mismos datos con los que lo entrenaste. Y en un dataset donde el 95% de los casos son negativos, accuracy es la mejor métrica.',
                "1. Un 99% en entrenamiento puede ser sobreajuste (overfitting). Hay que medir con datos que el modelo no vio.\n2. Se evalúa con un conjunto de validación o test separado, o con validación cruzada.\n3. Con clases desbalanceadas, un modelo que siempre dice negativo ya tiene 95% de accuracy. Conviene usar precision, recall o F1.\nFuente: documentación de scikit-learn (model evaluation).",
            ],
            'ai-engineer' => [
                'Los modelos de lenguaje buscan en internet cada vez que responden, así que su información siempre está actualizada. Subir la temperatura hace que las respuestas sean más precisas. Y RAG consiste en reentrenar el modelo con tus documentos.',
                "1. Un modelo solo sabe lo que vio hasta su fecha de corte. Busca en internet únicamente si se le da una herramienta para hacerlo.\n2. Más temperatura significa respuestas más variadas y aleatorias, no más precisas.\n3. RAG no reentrena nada: recupera fragmentos relevantes de tus documentos en cada consulta y se los pasa al modelo como contexto.\nFuentes: documentación de los proveedores de modelos, curso de Hugging Face.",
            ],
            'analista-soc' => [
                'El puerto 443 se usa para HTTP sin cifrar. Si el antivirus no detecta nada, el equipo está limpio. Y un falso positivo es un ataque real que no fue detectado.',
                "1. El 443 es HTTPS (cifrado). HTTP sin cifrar usa el 80.\n2. Que no haya detecciones no significa que no haya compromiso: hay malware que evade antivirus.\n3. Eso es un falso negativo. Un falso positivo es una alerta que no corresponde a un ataque real.\nFuentes: IANA (registro de puertos), material de Blue Team de TryHackMe.",
            ],
            'pentester' => [
                'Escanear con Nmap cualquier IP pública es legal porque la información es pública. La inyección SQL solo afecta a bases de datos MySQL. Y usar HTTPS protege a una web contra XSS.',
                "1. Escanear o atacar sistemas sin autorización escrita puede ser ilegal. Siempre hace falta permiso y un alcance definido.\n2. La inyección SQL afecta a cualquier motor SQL (PostgreSQL, SQL Server, Oracle, SQLite...).\n3. HTTPS cifra el tráfico, pero no impide XSS: eso se resuelve validando y escapando los datos.\nFuentes: OWASP Top 10, PortSwigger Web Security Academy.",
            ],
            'analista-grc' => [
                'ISO 27001 es una ley obligatoria para todas las empresas. El riesgo se calcula sumando la cantidad de activos y de empleados. Y una vez que aprobás la auditoría de certificación, el certificado vale para siempre.',
                "1. ISO 27001 es un estándar voluntario, no una ley (aunque algunos clientes o sectores lo exijan).\n2. El riesgo se evalúa combinando probabilidad e impacto.\n3. El certificado dura 3 años y requiere auditorías de seguimiento periódicas.\nFuente: ISO (iso.org), ISO/IEC 27001.",
            ],
            'devops-engineer' => [
                'Un contenedor de Docker incluye su propio kernel completo, igual que una máquina virtual. CI significa desplegar automáticamente a producción. Y en un Dockerfile, cada instrucción RUN se vuelve a ejecutar cada vez que arranca el contenedor.',
                "1. Los contenedores comparten el kernel del sistema anfitrión; las máquinas virtuales tienen el suyo.\n2. CI (integración continua) es integrar y probar el código automáticamente. Desplegar es CD.\n3. RUN se ejecuta al construir la imagen. Lo que corre al arrancar el contenedor es CMD o ENTRYPOINT.\nFuente: documentación oficial de Docker.",
            ],
            'cloud-engineer' => [
                'En la capa gratuita de AWS nunca te pueden cobrar, uses lo que uses. Una región y una zona de disponibilidad son lo mismo. Y darles permisos de administrador a todos los usuarios es la práctica recomendada para evitar bloqueos.',
                "1. La capa gratuita tiene límites; si los superás o usás servicios que no están incluidos, te cobran. Por eso se configuran alertas de presupuesto.\n2. Una región contiene varias zonas de disponibilidad, que son centros de datos separados.\n3. Lo recomendado es el principio de mínimo privilegio.\nFuente: documentación de AWS (Free Tier, Regions and AZs, IAM best practices).",
            ],
            'soporte-it-sysadmin' => [
                'La dirección 192.168.1.10 es una IP pública. El comando ping sirve para saber qué puertos tiene abiertos un servidor. Y en Linux, chmod 777 es la forma recomendada de arreglar problemas de permisos.',
                "1. 192.168.x.x es un rango privado (RFC 1918), no es accesible desde internet.\n2. ping usa ICMP para saber si un equipo responde. Para ver puertos se usan herramientas como nmap o ss.\n3. 777 le da a cualquiera permiso total, incluido escribir y ejecutar. Es inseguro: hay que dar solo los permisos necesarios.\nFuentes: RFC 1918, páginas de manual de chmod y ping.",
            ],
            'qa-manual' => [
                'Si todos los casos de prueba pasan, el software no tiene bugs. Las pruebas de regresión se hacen una sola vez, al principio del proyecto. Y un bug de severidad alta siempre tiene prioridad alta.',
                "1. El testing muestra la presencia de defectos, no su ausencia (principio de ISTQB).\n2. La regresión se repite después de cada cambio para verificar que no se rompió lo que ya funcionaba.\n3. Severidad (impacto técnico) y prioridad (urgencia de negocio) son distintas: un error grave en una función que casi nadie usa puede tener prioridad baja.\nFuente: syllabus ISTQB Foundation Level.",
            ],
            'qa-automation' => [
                'Conviene automatizar el 100% de las pruebas, incluidas las exploratorias. Para esperar a que cargue un elemento lo mejor es un sleep de 5 segundos fijo. Y los tests end-to-end son más rápidos que los unitarios.',
                "1. No todo conviene automatizar; las pruebas exploratorias son humanas por naturaleza. Se automatiza lo repetitivo y estable.\n2. Las esperas fijas hacen los tests lentos y frágiles. Se usan esperas explícitas o automáticas (Playwright y Cypress las traen).\n3. Los E2E son los más lentos; por eso la pirámide de testing tiene muchos unitarios y pocos E2E.\nFuentes: documentación de Playwright, Test Automation University.",
            ],
            'ux-ui-designer' => [
                'Si a vos te resulta fácil de usar, a los usuarios también. Para hacer una prueba de usabilidad necesitás al menos 100 personas. Y el texto gris claro sobre fondo blanco es ideal porque se ve más limpio.',
                "1. Vos no sos el usuario: tu contexto y tu conocimiento son distintos. Por eso se investiga y se prueba con personas reales.\n2. Con unas 5 personas se detecta la mayoría de los problemas de usabilidad (Nielsen Norman Group).\n3. El gris claro sobre blanco suele no tener contraste suficiente. WCAG pide al menos 4.5:1 para texto normal.\nFuentes: Nielsen Norman Group, WCAG 2.1.",
            ],
            'product-manager' => [
                'El Product Manager decide solo qué se construye, sin necesidad de hablar con usuarios. La métrica más importante de cualquier producto es la cantidad de descargas. Y un roadmap es una promesa de fechas exactas que no se puede cambiar.',
                "1. El PM decide con evidencia: habla con usuarios y trabaja con diseño, desarrollo y negocio.\n2. Las descargas son una métrica de vanidad. Importan más la activación, la retención o la que refleje el valor real del producto.\n3. Un roadmap comunica dirección y prioridades, y se ajusta a medida que se aprende.\nFuentes: roadmap.sh/product-manager, Atlassian (product management).",
            ],
            'scrum-master' => [
                'El Scrum Master es el jefe del equipo y es quien asigna las tareas. La Daily dura una hora para revisar todo en detalle. Y en Scrum un sprint puede durar hasta 3 meses.',
                "1. El Scrum Master no es un jefe: sirve al equipo, que se autogestiona y decide cómo hacer el trabajo.\n2. La Daily Scrum dura como máximo 15 minutos.\n3. Un sprint dura un mes o menos.\nFuente: La Guía Scrum (scrumguides.org).",
            ],
            'project-manager' => [
                'El camino crítico es la lista de las tareas más difíciles del proyecto. Si un proyecto se atrasa, sumar más personas siempre lo acelera. Y el alcance, el tiempo y el costo se pueden cambiar por separado sin que se afecten entre sí.',
                "1. El camino crítico es la secuencia más larga de tareas dependientes; define la duración mínima del proyecto.\n2. Sumar gente a un proyecto atrasado suele atrasarlo más (ley de Brooks): hay que incorporarla y coordinarla.\n3. Alcance, tiempo y costo están conectados (la triple restricción): cambiar uno afecta a los otros.\nFuentes: PMI (PMBOK), The Mythical Man-Month.",
            ],
            'analista-funcional' => [
                'Un requerimiento no funcional describe qué hace el sistema, por ejemplo "el usuario puede registrarse". Las historias de usuario se escriben con el formato "El sistema debe...". Y en BPMN el rombo representa una tarea.',
                "1. \"El usuario puede registrarse\" es funcional. Los no funcionales describen cualidades: rendimiento, seguridad, disponibilidad.\n2. El formato habitual es \"Como [rol] quiero [algo] para [beneficio]\".\n3. En BPMN el rombo es una compuerta (decisión); la tarea es un rectángulo redondeado.\nFuentes: Mountain Goat Software (user stories), especificación BPMN 2.0.",
            ],
            'especialista-seo' => [
                'Repetir la palabra clave muchas veces en el texto mejora el posicionamiento. La etiqueta meta keywords es uno de los factores más importantes para Google. Y si una página está en el sitemap, Google la indexa sí o sí.',
                "1. Repetir la palabra clave de forma artificial (keyword stuffing) va contra las políticas de Google y puede perjudicar.\n2. Google no usa la meta keywords para posicionar.\n3. El sitemap ayuda a descubrir páginas, pero no garantiza que se indexen.\nFuente: Google Search Central.",
            ],
            'marketing-digital' => [
                'El CTR se calcula dividiendo las ventas por las visitas. Cuanto más amplio es el público de una campaña, mejores resultados da. Y en un test A/B conviene cambiar varias cosas a la vez para ir más rápido.',
                "1. El CTR es clics dividido impresiones. Ventas sobre visitas es la tasa de conversión.\n2. Un público más amplio no garantiza resultados: importa que sea relevante para la oferta.\n3. En un test A/B se cambia una sola variable; si cambiás varias, no sabés cuál produjo el efecto.\nFuentes: Google Skillshop, HubSpot Academy.",
            ],
            'technical-writer' => [
                'Una buena documentación usa la mayor cantidad posible de términos técnicos para sonar profesional. El README es opcional en los proyectos open source. Y conviene escribir en voz pasiva porque es más clara.',
                "1. La buena documentación usa el lenguaje más simple posible para su audiencia y explica los términos necesarios.\n2. El README es la puerta de entrada al proyecto: qué es, cómo instalarlo y cómo usarlo.\n3. La voz activa suele ser más clara y directa que la pasiva.\nFuente: curso de Technical Writing de Google.",
            ],
            'creador-de-contenido' => [
                'Da igual a qué hora publiques: el algoritmo le muestra todo a todos tus seguidores. Comprar seguidores es una buena forma de empezar porque mejora el alcance. Y la retención de un video no importa, lo único que cuenta son los likes.',
                "1. Los algoritmos no muestran todo a todos: priorizan según interés e interacción.\n2. Los seguidores comprados no interactúan, bajan la tasa de interacción y van contra las reglas de las plataformas.\n3. La retención (cuánto tiempo miran) es una de las señales más importantes para que un video se recomiende.\nFuentes: YouTube Creators, TikTok Creator Portal.",
            ],
        ];
    }
}
