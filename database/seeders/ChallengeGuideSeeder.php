<?php

namespace Database\Seeders;

use App\Models\Career;
use Illuminate\Database\Seeder;

/**
 * guides explain how to approach a challenge, never the answer itself.
 * only fills challenges without a guide, so edits made in the admin survive a re-run.
 */
class ChallengeGuideSeeder extends Seeder
{
    public function run(): void
    {
        $careers = Career::query()->whereIn('slug', array_keys($this->guides()))->with('challenges')->get()->keyBy('slug');

        foreach ($this->guides() as $slug => $guides) {
            foreach ($careers->get($slug)?->challenges ?? [] as $challenge) {
                $guide = $guides[$challenge->title] ?? null;

                if (! $guide || $challenge->guide) {
                    continue;
                }

                [$before, $steps, $hints] = $guide;
                $challenge->update(['guide' => compact('before', 'steps', 'hints')]);
            }
        }
    }

    // career slug => [challenge title => [before[], steps[], hints[]]]
    private function guides(): array
    {
        return [
            'frontend-developer' => [
                'Tu tarjeta de presentación' => [
                    ['Un editor de código (VS Code es gratis).', 'Saber qué es una etiqueta HTML y qué es una regla CSS.', 'Una cuenta de GitHub.'],
                    [
                        'Creá una carpeta con dos archivos: index.html y styles.css.',
                        'Escribí la estructura en HTML: un encabezado con tu nombre, tu foto, un párrafo de bio y una lista de links.',
                        'Conectá el CSS desde el <head> y dale estilo: tipografía, colores y espaciado.',
                        'Abrí la página en el navegador, achicá la ventana y ajustá lo que se rompa en tamaño celular.',
                        'Subí la carpeta a un repositorio de GitHub y activá GitHub Pages en la configuración.',
                    ],
                    [
                        'Si la foto se ve gigante en el celular, buscá la propiedad max-width.',
                        'Para que el navegador del celular no achique la página entera, buscá la meta etiqueta "viewport".',
                    ],
                ],
                'Maquetá un diseño de Frontend Mentor' => [
                    ['Una cuenta gratuita en Frontend Mentor.', 'Haber hecho la tarjeta de presentación, o saber HTML y CSS básico.'],
                    [
                        'Elegí un desafío "Newbie" y descargá los archivos: imágenes, guía de estilo y diseños.',
                        'Leé la guía de estilo y anotá colores, tipografías y tamaños antes de escribir código.',
                        'Maquetá primero la versión mobile y después agregá los cambios para desktop.',
                        'Compará tu página con la imagen del diseño, poniéndolas una al lado de la otra.',
                        'Publicá el sitio y subí la solución en Frontend Mentor para recibir feedback.',
                    ],
                    [
                        'Para centrar la tarjeta en la pantalla, investigá flexbox o grid en el contenedor padre.',
                        'Si el diseño de desktop es distinto, buscá cómo funcionan las media queries con min-width.',
                    ],
                ],
            ],
            'backend-developer' => [
                'Calculadora por consola' => [
                    ['Tener instalado un lenguaje: Python, Node.js o PHP.', 'Saber qué son una variable, una condición y una función.'],
                    [
                        'Escribí un programa que pida un número, lo lea y lo muestre. Asegurate de que funcione antes de seguir.',
                        'Pedí el segundo número y la operación (+, -, *, /).',
                        'Usá condicionales para hacer la cuenta según la operación elegida.',
                        'Pensá qué puede hacer mal el usuario: escribir letras, dividir por cero, poner una operación que no existe.',
                        'Manejá cada uno de esos casos con un mensaje claro en lugar de que el programa se rompa.',
                    ],
                    [
                        'Lo que se lee por consola llega como texto: hay que convertirlo a número y esa conversión puede fallar.',
                        'Para no repetir código, pensá en una función que reciba los dos números y la operación.',
                    ],
                ],
                'Diseñá una base de datos' => [
                    ['Saber qué es una tabla, una fila y una columna.', 'Tener instalado SQLite (o usar un editor online de SQL).'],
                    [
                        'Listá las "cosas" del problema: libros, autores, socios y préstamos. Cada una suele ser una tabla.',
                        'Para cada tabla, escribí qué datos necesitás guardar (columnas).',
                        'Pensá las relaciones: ¿un libro tiene un autor o varios? ¿Un socio puede tener varios préstamos?',
                        'Dibujá el diagrama a mano o en draw.io antes de escribir SQL.',
                        'Escribí los CREATE TABLE, insertá datos de prueba y consultalos para comprobar que todo encaja.',
                    ],
                    [
                        'Cuando la relación es "muchos a muchos", suele hacer falta una tabla intermedia.',
                        'Un préstamo necesita saber quién, qué libro y cuándo: ¿qué columnas apuntan a otras tablas?',
                    ],
                ],
            ],
            'mobile-developer' => [
                'Hola mundo en tu celular' => [
                    ['Node.js instalado (para Expo) o el SDK de Flutter.', 'Un celular Android o iPhone con la app Expo Go, o un emulador.'],
                    [
                        'Seguí la guía oficial de instalación de la herramienta que elegiste, paso por paso.',
                        'Creá un proyecto nuevo con el comando que indica la guía.',
                        'Levantalo y abrilo en tu celular escaneando el QR, o en el emulador.',
                        'Cambiá el texto de la pantalla por tu nombre y mirá cómo se actualiza solo.',
                        'Agregá un botón y una variable de estado que cuente los clics.',
                    ],
                    [
                        'Si el celular no se conecta, verificá que la computadora y el celular estén en la misma red WiFi.',
                        'Para que el número cambie en pantalla, buscá cómo funciona el estado (useState en React Native, setState en Flutter).',
                    ],
                ],
                'App de notas' => [
                    ['Haber hecho el "Hola mundo".', 'Saber qué es una lista y qué es el estado de un componente.'],
                    [
                        'Dibujá en papel las dos partes: un campo para escribir y una lista de notas.',
                        'Hacé que el texto escrito se agregue a la lista al tocar un botón.',
                        'Mostrá la lista con un componente pensado para listas largas.',
                        'Investigá cómo guardar datos en el dispositivo y guardá la lista cada vez que cambia.',
                        'Al abrir la app, cargá las notas guardadas. Cerrala y abrila para comprobarlo.',
                    ],
                    [
                        'En React Native buscá AsyncStorage; en Flutter, shared_preferences.',
                        'El almacenamiento local guarda texto: vas a tener que convertir la lista a texto y volver.',
                    ],
                ],
            ],
            'data-analyst' => [
                'Tu presupuesto en una planilla' => [
                    ['Una cuenta de Google para usar Sheets (o Excel).', 'Tus gastos de un mes, aunque sean aproximados.'],
                    [
                        'Armá una tabla con columnas: fecha, descripción, categoría y monto.',
                        'Cargá todos los gastos del mes, una fila por gasto.',
                        'Definí pocas categorías (comida, transporte, servicios, ocio) y usalas siempre igual.',
                        'Creá una tabla dinámica que sume los montos por categoría.',
                        'Agregá un gráfico a partir de la tabla dinámica y escribí una conclusión de una línea.',
                    ],
                    [
                        'Si una categoría aparece dos veces en la tabla dinámica, revisá mayúsculas y espacios al escribirla.',
                        'Para que todos escriban las categorías igual, investigá la validación de datos con lista desplegable.',
                    ],
                ],
                'Primeras consultas SQL' => [
                    ['Nada: SQLBolt funciona en el navegador.'],
                    [
                        'Entrá a SQLBolt y hacé la lección 1 leyendo la explicación antes del ejercicio.',
                        'Resolvé cada ejercicio sin mirar la solución; si te trabás, releé la explicación.',
                        'Anotá en un documento cada comando nuevo con un ejemplo propio.',
                        'Avanzá hasta la lección 12.',
                    ],
                    [
                        'Si la consulta da error, leé el mensaje: casi siempre dice en qué palabra está el problema.',
                        'El orden importa: SELECT, FROM, WHERE, GROUP BY, ORDER BY.',
                    ],
                ],
            ],
            'data-scientist' => [
                'Explorá un dataset' => [
                    ['Una cuenta en Kaggle (los notebooks corren en el navegador, no hace falta instalar nada).', 'Python básico.'],
                    [
                        'Buscá la competencia "Titanic" en Kaggle y creá un notebook nuevo desde ahí.',
                        'Cargá el archivo de entrenamiento con pandas y mirá las primeras filas.',
                        'Revisá tipos de datos y cuántos valores faltan en cada columna.',
                        'Hacé 3 gráficos que respondan preguntas concretas, por ejemplo: ¿sobrevivieron más mujeres que hombres?',
                        'Escribí debajo de cada gráfico qué ves y qué te sorprendió.',
                    ],
                    [
                        'Los métodos info() y describe() de pandas te dan un resumen rápido.',
                        'Antes de graficar, escribí la pregunta que querés responder; el gráfico sale de la pregunta.',
                    ],
                ],
                'Estadística aplicada' => [
                    ['Haber explorado el dataset del Titanic.', 'Saber qué son el promedio y la mediana.'],
                    [
                        'Elegí 3 columnas numéricas, por ejemplo edad y tarifa.',
                        'Calculá promedio, mediana y desvío estándar de cada una.',
                        'Compará promedio y mediana: si son muy distintos, mirá un histograma de esa columna.',
                        'Explicá con tus palabras qué te dice cada número, como si se lo contaras a alguien que no sabe estadística.',
                    ],
                    [
                        'Si el promedio es mucho más alto que la mediana, buscá valores extremos en esa columna.',
                        'El desvío te dice qué tan dispersos están los datos, no si son buenos o malos.',
                    ],
                ],
            ],
            'ai-engineer' => [
                'Entendé un LLM por dentro' => [
                    ['Nada técnico: es un desafío de comprensión.'],
                    [
                        'Buscá una explicación introductoria de qué es un modelo de lenguaje (el curso de Hugging Face sirve).',
                        'Para cada concepto (token, ventana de contexto, alucinación) escribí una definición de una oración.',
                        'Inventá un ejemplo cotidiano para cada uno.',
                        'Releé tus definiciones: si usás palabras que vos no entenderías hace un mes, simplificalas.',
                    ],
                    [
                        'Pensá los tokens como pedacitos de palabras, no como palabras enteras.',
                        'Para la alucinación, pensá en por qué un modelo responde con seguridad algo que no sabe.',
                    ],
                ],
                'Tu primera llamada a una API de IA' => [
                    ['Python instalado.', 'Una cuenta en un proveedor de modelos con una API key.', 'Saber qué es una variable de entorno.'],
                    [
                        'Leé el quickstart de la documentación del proveedor que elegiste.',
                        'Guardá la API key en una variable de entorno, nunca escrita en el código.',
                        'Instalá la librería oficial del proveedor.',
                        'Escribí un script que envíe un mensaje y muestre la respuesta.',
                        'Subilo a GitHub y verificá que la key no quedó en ningún archivo.',
                    ],
                    [
                        'Si te da error de autenticación, revisá que la variable de entorno esté cargada en la misma terminal.',
                        'Agregá un archivo .gitignore antes del primer commit si usás un archivo .env.',
                    ],
                ],
            ],
            'analista-soc' => [
                'Redes en la práctica' => [
                    ['Una terminal: PowerShell o CMD en Windows, Terminal en Mac o Linux.'],
                    [
                        'Elegí 3 sitios conocidos.',
                        'Hacé ping a cada uno y anotá el tiempo de respuesta.',
                        'Hacé traceroute (tracert en Windows) y contá cuántos saltos hay.',
                        'Usá nslookup para ver la IP de cada dominio.',
                        'Escribí qué te muestra cada comando y para qué te serviría si un usuario dice "no me anda internet".',
                    ],
                    [
                        'Si ping no responde, no siempre significa que el sitio está caído: algunos bloquean ese tipo de tráfico.',
                        'En el traceroute, los asteriscos son saltos que no respondieron, no errores.',
                    ],
                ],
                'Cazá un phishing' => [
                    ['Tu carpeta de spam o ejemplos públicos de phishing (hay muchos en sitios de bancos y organismos oficiales).'],
                    [
                        'Juntá 3 mails sospechosos. No hagas clic en ningún link ni descargues adjuntos.',
                        'Mirá el remitente real (no solo el nombre que aparece) y comparalo con el dominio oficial.',
                        'Pasá el mouse sobre los links sin hacer clic y mirá a dónde apuntan.',
                        'Buscá señales de urgencia, errores de ortografía y pedidos de datos.',
                        'Armá una tabla por mail con cada pista encontrada.',
                    ],
                    [
                        'Un dominio parecido no es el mismo dominio: mirá letra por letra.',
                        'Los bancos y servicios serios nunca te piden la contraseña por mail.',
                    ],
                ],
            ],
            'pentester' => [
                'Primeros niveles de Bandit' => [
                    ['Una terminal con SSH (viene en Windows 10/11, Mac y Linux).', 'Saber qué es una línea de comandos.'],
                    [
                        'Leé la página del nivel 0 en OverTheWire y conectate por SSH con los datos que da.',
                        'En cada nivel, leé el objetivo y los comandos sugeridos al pie de la página.',
                        'Usá el comando man para entender cada comando antes de usarlo.',
                        'Anotá en un documento qué aprendiste en cada nivel.',
                        'Guardá las contraseñas solo para vos: no las publiques.',
                    ],
                    [
                        'Si un archivo tiene un nombre raro (con espacios o guiones), investigá cómo escaparlo en la terminal.',
                        'Cuando buscás un archivo entre muchos, el comando find tiene opciones para filtrar por tamaño o tipo.',
                    ],
                ],
                'Escaneá tu propia red' => [
                    ['Nmap instalado.', 'Estar en tu red de casa: escanear redes ajenas sin permiso puede ser ilegal.'],
                    [
                        'Averiguá tu IP local y el rango de tu red.',
                        'Hacé un escaneo de descubrimiento para ver qué dispositivos están conectados.',
                        'Elegí tu propia computadora y escaneá sus puertos.',
                        'Para cada puerto abierto, investigá qué servicio suele usarlo.',
                        'Escribí un informe corto: qué encontraste y si algo debería estar cerrado.',
                    ],
                    [
                        'Tu IP local suele empezar con 192.168 o 10.: el rango casi siempre termina en /24.',
                        'Si no reconocés un dispositivo, el fabricante que muestra Nmap puede darte una pista.',
                    ],
                ],
            ],
            'analista-grc' => [
                'Política de contraseñas' => [
                    ['Saber qué es la autenticación de dos factores.', 'Leer las recomendaciones de contraseñas de un organismo oficial (por ejemplo, NIST).'],
                    [
                        'Imaginá una empresa chica concreta: rubro, cantidad de empleados y qué sistemas usa.',
                        'Definí el largo mínimo y si se piden caracteres especiales, justificando cada decisión.',
                        'Decidí cada cuánto se cambian las contraseñas y por qué.',
                        'Sumá el uso de gestor de contraseñas y doble factor.',
                        'Escribilo en una página, en lenguaje que entienda cualquier empleado.',
                    ],
                    [
                        'Las recomendaciones actuales priorizan el largo sobre la complejidad.',
                        'Pensá qué pasa si la regla es tan molesta que la gente anota la contraseña en un papel.',
                    ],
                ],
                'Inventario de activos' => [
                    ['Saber qué es un activo de información: datos, sistemas, personas o proveedores que tienen valor.'],
                    [
                        'Elegí un negocio concreto, por ejemplo una clínica chica.',
                        'Listá los datos que maneja: historias clínicas, turnos, facturación.',
                        'Listá dónde viven esos datos: sistemas, computadoras, papel, servicios en la nube.',
                        'Sumá personas y proveedores clave.',
                        'Para cada activo, anotá quién es responsable y qué tan crítico es (alto, medio o bajo).',
                    ],
                    [
                        'Preguntate: si este activo desaparece mañana, ¿qué deja de funcionar?',
                        'No te olvides del papel: también es información.',
                    ],
                ],
            ],
            'devops-engineer' => [
                'Script de backup' => [
                    ['Una terminal Linux: WSL en Windows, o una máquina virtual.', 'Saber qué es una variable en Bash.'],
                    [
                        'Probá a mano el comando para comprimir una carpeta con tar.',
                        'Averiguá cómo obtener la fecha actual en un formato que sirva para nombrar archivos.',
                        'Escribí el script con esos dos pasos y probalo.',
                        'Agregá el borrado de backups viejos y probalo con archivos de prueba, no con datos reales.',
                        'Documentá en el README cómo usarlo y cómo programarlo con cron.',
                    ],
                    [
                        'El comando date acepta un formato, por ejemplo +%Y-%m-%d.',
                        'find tiene una opción para buscar archivos por antigüedad.',
                    ],
                ],
                'Ejercicios de Linux' => [
                    ['Una terminal Linux para probar lo que no sepas.'],
                    [
                        'Abrí la sección Linux del repositorio devops-exercises.',
                        'Respondé cada pregunta por escrito sin mirar la respuesta.',
                        'Las que no sepas, probalas en la terminal o buscalas en el manual (man).',
                        'Recién al final, compará con las respuestas y marcá en qué te equivocaste.',
                        'Repasá los temas donde más fallaste.',
                    ],
                    [
                        'Responder "no sé" está bien: anotalo y buscalo después, es parte del ejercicio.',
                        'man seguido de un comando te muestra su manual completo.',
                    ],
                ],
            ],
            'cloud-engineer' => [
                'Creá tu cuenta con alertas' => [
                    ['Un email y una tarjeta (la piden para verificar la cuenta aunque uses la capa gratuita).'],
                    [
                        'Elegí un proveedor: AWS, Azure o Google Cloud.',
                        'Creá la cuenta y activá la autenticación de dos factores en el usuario principal.',
                        'Buscá la sección de presupuestos (Budgets) antes de crear cualquier recurso.',
                        'Creá un presupuesto bajo, por ejemplo 5 dólares, con alerta por email.',
                        'Leé qué incluye la capa gratuita y qué no.',
                    ],
                    [
                        'La alerta avisa, pero no frena el gasto: por eso conviene revisar qué dejás encendido.',
                        'No uses el usuario principal para el día a día; más adelante vas a crear usuarios con menos permisos.',
                    ],
                ],
                'Sitio estático en la nube' => [
                    ['La cuenta con alertas configuradas.', 'Un archivo HTML simple.'],
                    [
                        'Buscá en la documentación oficial cómo alojar un sitio estático en el almacenamiento de objetos de tu proveedor.',
                        'Creá el contenedor (bucket) y subí tu index.html.',
                        'Configurá el acceso público solo para lectura del sitio.',
                        'Abrí la URL pública y verificá que se vea.',
                        'Anotá cada paso que hiciste: es tu documentación.',
                    ],
                    [
                        'Si ves un error de acceso denegado, revisá los permisos públicos del bucket.',
                        'Activá la opción de sitio web estático e indicá index.html como documento principal.',
                    ],
                ],
            ],
            'soporte-it-sysadmin' => [
                'Documentá tu PC' => [
                    ['Tu computadora.'],
                    [
                        'En Windows abrí "Información del sistema"; en Mac, "Acerca de esta Mac".',
                        'Anotá procesador, RAM, disco y versión del sistema operativo.',
                        'Averiguá tu dirección IP local con ipconfig (Windows) o ifconfig/ip (Mac y Linux).',
                        'Armá una ficha prolija con todos los datos.',
                    ],
                    [
                        'En Windows también podés usar el comando systeminfo en la terminal.',
                        'La IP que buscás es la del adaptador que estás usando: WiFi o cable.',
                    ],
                ],
                'Instalá una máquina virtual' => [
                    ['VirtualBox instalado.', 'La imagen ISO de Ubuntu descargada del sitio oficial.', 'Al menos 4 GB de RAM libres.'],
                    [
                        'Creá una máquina virtual nueva y asignale memoria y disco.',
                        'Montá la ISO de Ubuntu como disco de arranque.',
                        'Seguí el instalador de Ubuntu.',
                        'Una vez instalado, creá un usuario nuevo desde la terminal.',
                        'Iniciá sesión con ese usuario para comprobar que funciona.',
                    ],
                    [
                        'Si la máquina va muy lenta, revisá que la virtualización esté activada en la BIOS de tu PC.',
                        'El comando para crear usuarios en Ubuntu es adduser.',
                    ],
                ],
            ],
            'qa-manual' => [
                'Casos de prueba de un login' => [
                    ['Saber qué es un caso de prueba: ID, precondición, pasos, datos y resultado esperado.', 'Una planilla de Google Sheets o Excel.'],
                    [
                        'Abrí Sauce Demo y anotá los usuarios de prueba que muestra la página.',
                        'Armá la planilla con las columnas: ID, título, precondición, pasos, datos y resultado esperado.',
                        'Escribí primero el caso feliz: un login correcto.',
                        'Pensá qué puede salir mal en cada campo: vacío, incorrecto o con un usuario especial.',
                        'Ejecutá cada caso y agregá una columna con el resultado real.',
                    ],
                    [
                        'Mirá con atención la lista de usuarios: hay uno que la página muestra pero que no debería poder entrar.',
                        'Un buen caso tiene un solo objetivo: no mezcles "usuario vacío" y "contraseña mala" en el mismo.',
                        'Probá también combinaciones: usuario correcto con contraseña de otro usuario.',
                    ],
                ],
                'Reportá bugs' => [
                    ['Saber qué tiene un buen reporte de bug: título, pasos, resultado esperado, resultado real y evidencia.'],
                    [
                        'Entrá a Sauce Demo con el usuario problem_user.',
                        'Recorré la tienda como un usuario normal: mirá productos, agregá al carrito, comprá.',
                        'Cada vez que algo no se comporte como esperás, reproducilo una segunda vez.',
                        'Escribí el reporte con pasos tan claros que otra persona pueda repetirlo sin preguntarte nada.',
                        'Sacá una captura de pantalla para cada bug.',
                    ],
                    [
                        'Compará con lo que pasa si entrás con el usuario normal: la diferencia es el bug.',
                        'Mirá con atención las imágenes de los productos y los campos del formulario de compra.',
                    ],
                ],
            ],
            'qa-automation' => [
                'Tu primer test automático' => [
                    ['Node.js instalado.', 'Saber lo básico de JavaScript.'],
                    [
                        'Seguí la guía de instalación de Playwright (o Cypress) en la documentación oficial.',
                        'Corré el test de ejemplo que trae para verificar que todo funciona.',
                        'Escribí un test nuevo que abra Sauce Demo.',
                        'Completá usuario y contraseña, hacé clic en Login y verificá que aparecen los productos.',
                        'Corré el test y miralo pasar; después rompelo a propósito para ver cómo falla.',
                    ],
                    [
                        'Para encontrar los elementos, inspeccioná la página: los campos tienen atributos data-test.',
                        'Playwright trae un generador de código (codegen) que te muestra cómo se escribe cada acción.',
                    ],
                ],
                'Casos negativos' => [
                    ['Tener funcionando el test de login correcto.'],
                    [
                        'Listá los casos negativos: contraseña incorrecta, usuario bloqueado y campos vacíos.',
                        'Para cada uno, anotá qué mensaje de error muestra la página al hacerlo a mano.',
                        'Escribí un test por caso que verifique ese mensaje.',
                        'Si repetís código entre tests, extraelo a una función.',
                    ],
                    [
                        'No alcanza con verificar que "no entró": verificá el mensaje de error exacto.',
                        'Investigá cómo agrupar tests relacionados con describe.',
                    ],
                ],
            ],
            'ux-ui-designer' => [
                'Rediseñá una pantalla' => [
                    ['Una cuenta gratuita en Figma.', 'Una captura de la pantalla que te molesta.'],
                    [
                        'Pegá la captura en Figma y anotá encima qué te molesta y por qué.',
                        'Convertí cada molestia en un problema concreto, por ejemplo: "no encuentro el botón para pagar".',
                        'Diseñá tu versión al lado, resolviendo esos problemas.',
                        'Explicá cada cambio con una nota: qué cambiaste y qué problema resuelve.',
                    ],
                    [
                        'Un rediseño no es "hacerlo más lindo": cada cambio tiene que tener un motivo.',
                        'Usá auto layout en Figma para que los espaciados sean consistentes.',
                    ],
                ],
                'Daily UI x7' => [
                    ['Figma.', 'Anotarte gratis en dailyui.co para recibir las consignas.'],
                    [
                        'Leé la consigna del día y buscá 2 o 3 referencias, sin copiarlas.',
                        'Ponete un límite de tiempo (por ejemplo, una hora) para no estancarte.',
                        'Diseñá, exportá y publicá.',
                        'Repetí 7 días seguidos.',
                    ],
                    [
                        'Terminado es mejor que perfecto: el objetivo es el hábito.',
                        'Reutilizá componentes de días anteriores para ir más rápido.',
                    ],
                ],
            ],
            'product-manager' => [
                'Analizá una app que uses' => [
                    ['Una app que uses seguido.'],
                    [
                        'Escribí qué problema te resuelve a vos.',
                        'Definí para quién es: ¿quiénes la usan además de vos?',
                        'Investigá cómo gana plata: suscripción, publicidad, comisiones.',
                        'Proponé 3 métricas que mostrarían si le está yendo bien y explicá por qué.',
                        'Escribilo en una página.',
                    ],
                    [
                        'Si la app es gratis, preguntate quién paga y a cambio de qué.',
                        'Una buena métrica refleja que el usuario recibe valor, no solo que entró a la app.',
                    ],
                ],
                'Historias de usuario' => [
                    ['Saber el formato: "Como [rol] quiero [algo] para [beneficio]".'],
                    [
                        'Elegí una función nueva para la app que analizaste.',
                        'Pensá quiénes la usarían: puede haber más de un rol.',
                        'Escribí cada historia en el formato y con un beneficio real.',
                        'Agregá 2 o 3 criterios de aceptación a cada una: condiciones que se pueden verificar.',
                    ],
                    [
                        'Si el beneficio repite la acción ("quiero buscar para buscar"), todavía no encontraste el porqué.',
                        'Un criterio de aceptación se puede probar con un sí o un no.',
                    ],
                ],
            ],
            'scrum-master' => [
                'Leé la Guía Scrum' => [
                    ['La Guía Scrum, en scrumguides.org (tiene versión en español).'],
                    [
                        'Leela entera una vez sin tomar notas.',
                        'En una segunda lectura, anotá los roles, los eventos y los artefactos.',
                        'Armá un mapa o diagrama que los conecte: quién participa en qué evento y qué produce cada uno.',
                        'Escribí 3 dudas que te hayan quedado y buscá la respuesta en la misma guía.',
                    ],
                    [
                        'La guía es corta a propósito: casi todo lo que no dice queda a criterio del equipo.',
                        'Cada evento tiene una duración máxima: anotala en tu mapa.',
                    ],
                ],
                'Tablero Kanban personal' => [
                    ['Una cuenta gratuita en Trello o Jira.'],
                    [
                        'Creá un tablero con tres columnas: por hacer, haciendo y hecho.',
                        'Cargá tus tareas de la semana como tarjetas.',
                        'Ponete un límite: no más de 2 tarjetas en "haciendo" al mismo tiempo.',
                        'Movelas todos los días y sacá una captura al final de la semana.',
                        'Anotá qué te pasó con el límite.',
                    ],
                    [
                        'Si una tarea es enorme, partila en tarjetas más chicas.',
                        'El límite molesta al principio: eso te obliga a terminar antes de empezar otra cosa.',
                    ],
                ],
            ],
            'project-manager' => [
                'Planificá un evento' => [
                    ['Un evento real o imaginario con fecha (un meetup, un cumpleaños grande, una mudanza).'],
                    [
                        'Definí el alcance: qué incluye el evento y qué no.',
                        'Listá todas las tareas necesarias.',
                        'Asigná un responsable y una fecha a cada tarea.',
                        'Marcá qué tareas dependen de otras.',
                        'Revisá el plan de atrás para adelante, desde la fecha del evento.',
                    ],
                    [
                        'Si una tarea no tiene responsable, no se va a hacer.',
                        '"Fuera del alcance" es tan importante como lo que sí incluye: evita discusiones después.',
                    ],
                ],
                'Diagrama de Gantt' => [
                    ['El plan del evento.', 'Una planilla o una herramienta de Gantt gratuita.'],
                    [
                        'Pasá cada tarea a una fila con fecha de inicio y de fin.',
                        'Dibujá las barras en el calendario.',
                        'Marcá las dependencias: qué no puede empezar hasta que termine otra.',
                        'Identificá qué tareas, si se atrasan, atrasan todo el evento.',
                    ],
                    [
                        'En Google Sheets podés armar un Gantt simple con formato condicional.',
                        'Esas tareas que atrasan todo forman el camino crítico.',
                    ],
                ],
            ],
            'analista-funcional' => [
                'Relevá un proceso' => [
                    ['Un proceso que conozcas bien o que puedas preguntarle a alguien.'],
                    [
                        'Elegí el proceso y definí dónde empieza y dónde termina.',
                        'Listá quiénes participan.',
                        'Escribí cada paso en orden, con quién lo hace y qué necesita para hacerlo.',
                        'Marcá los momentos de decisión: "si pasa esto, entonces...".',
                        'Si podés, validalo con alguien que lo hace todos los días.',
                    ],
                    [
                        'Describí cómo funciona hoy, no cómo debería funcionar: las mejoras van en otro desafío.',
                        'Preguntá por las excepciones: ahí suelen estar los problemas.',
                    ],
                ],
                'Diagramalo' => [
                    ['El proceso relevado.', 'draw.io (gratis) o Bizagi Modeler.'],
                    [
                        'Aprendé los 4 elementos básicos de BPMN: evento, tarea, compuerta y flujo.',
                        'Dibujá una calle (carril) por cada participante.',
                        'Pasá cada paso a una tarea y cada decisión a una compuerta.',
                        'Revisá que todos los caminos lleguen a un evento de fin.',
                    ],
                    [
                        'Las tareas se nombran con verbo y objeto: "Registrar turno".',
                        'Cada compuerta necesita una pregunta y salidas con nombre.',
                    ],
                ],
            ],
            'especialista-seo' => [
                'Auditá un sitio' => [
                    ['El sitio de un negocio local.', 'Saber ver el código fuente de una página.'],
                    [
                        'Revisá el título y la meta descripción de 3 páginas: ¿dicen qué ofrece el negocio?',
                        'Mirá si cada página tiene un único encabezado principal (h1).',
                        'Pasá PageSpeed Insights y anotá los problemas principales.',
                        'Abrilo en el celular y anotá qué cuesta usar.',
                        'Ordená tus hallazgos por impacto y armá la lista de 10 mejoras.',
                    ],
                    [
                        'Buscá el nombre del negocio en Google y mirá cómo aparece: eso es lo que ve un cliente.',
                        'Si el título de la página dice solo "Inicio", ya encontraste una mejora.',
                    ],
                ],
                'Investigación de palabras clave' => [
                    ['Google y una planilla.'],
                    [
                        'Escribí 5 servicios o productos del negocio.',
                        'Para cada uno, escribilo en Google y anotá las sugerencias del autocompletado.',
                        'Bajá hasta "Búsquedas relacionadas" y sumá las que sirvan.',
                        'Clasificá cada palabra según la intención: informarse, comparar o comprar.',
                        'Estimá la dificultad mirando quiénes aparecen primeros.',
                    ],
                    [
                        'Las frases largas y específicas ("plomero urgente en Palermo") suelen ser más fáciles y más cercanas a la compra.',
                        'Si los primeros resultados son sitios gigantes, esa palabra es difícil para empezar.',
                    ],
                ],
            ],
            'marketing-digital' => [
                'Analizá una marca' => [
                    ['Una marca que sigas en redes.'],
                    [
                        'Revisá sus últimas 20 publicaciones.',
                        'Anotá formato, tema y la interacción de cada una (likes y comentarios).',
                        'Identificá a quién le habla y con qué tono.',
                        'Encontrá qué tipo de contenido funciona mejor y pensá por qué.',
                        'Armá una presentación corta con tus conclusiones.',
                    ],
                    [
                        'Compará los comentarios, no solo los likes: muestran qué genera conversación.',
                        'Fijate cada cuánto publica y si eso cambia los resultados.',
                    ],
                ],
                'Calendario de contenidos' => [
                    ['Un emprendimiento real o imaginario con un público definido.'],
                    [
                        'Definí un objetivo del mes: vender, ganar seguidores o educar.',
                        'Elegí 3 tipos de contenido que acompañen ese objetivo.',
                        'Repartilos en el mes con una frecuencia que puedas sostener.',
                        'Para cada publicación, anotá objetivo, formato, tema y llamado a la acción.',
                    ],
                    [
                        'Mejor 3 publicaciones por semana sostenidas que 7 durante una sola semana.',
                        'Si no podés decir para qué sirve una publicación, sacala del calendario.',
                    ],
                ],
            ],
            'technical-writer' => [
                'Explicá algo técnico simple' => [
                    ['Una cuenta de GitHub (y haberla creado vos alguna vez).'],
                    [
                        'Hacé el proceso vos de cero y anotá cada paso mientras lo hacés.',
                        'Escribí la guía para alguien que nunca lo hizo.',
                        'Agregá capturas en los pasos donde es fácil perderse.',
                        'Pedile a alguien que siga tu guía sin ayuda y anotá dónde se trabó.',
                        'Corregí esos puntos.',
                    ],
                    [
                        'Cada paso debería tener una sola acción.',
                        'Contá qué tiene que ver la persona después de cada paso, así sabe que va bien.',
                    ],
                ],
                'README de un proyecto' => [
                    ['Una cuenta de GitHub.', 'Saber Markdown básico.'],
                    [
                        'Buscá un repositorio chico con README corto o desactualizado.',
                        'Instalá y usá el proyecto siguiendo lo que dice; anotá lo que falta.',
                        'Reescribí el README: qué es, cómo se instala, cómo se usa y cómo contribuir.',
                        'Publicá tu versión en un fork o en un gist.',
                    ],
                    [
                        'Si para instalarlo tuviste que adivinar un paso, ese paso falta en el README.',
                        'Un ejemplo de uso vale más que tres párrafos de descripción.',
                    ],
                ],
            ],
            'creador-de-contenido' => [
                'Creá tu cuenta profesional' => [
                    ['Saber qué plataforma usa tu público (si no lo sabés, empezá por la que más consumís).'],
                    [
                        'Elegí una sola plataforma principal: después vas a poder sumar otras.',
                        'Elegí un nombre corto, fácil de escribir y que no te ate a un tema demasiado chico.',
                        'Usá una foto donde se te vea bien la cara.',
                        'Escribí la bio respondiendo: de qué hablás y para quién.',
                        'Pasásela a 2 personas y preguntales si entienden qué van a encontrar.',
                    ],
                    [
                        'Una bio como "Amo la tecnología" no le dice nada a quien te descubre.',
                        'Si dudás entre dos nombres, elegí el más fácil de dictar en voz alta.',
                    ],
                ],
                'Encontrá tu nicho' => [
                    ['Una planilla o un documento.'],
                    [
                        'Escribí 10 temas tech que te interesen o que estés aprendiendo.',
                        'Para cada tema, buscá 3 creadores que ya hablen de eso.',
                        'Anotá qué hacen bien y qué les falta: idioma, nivel, formato o enfoque.',
                        'Cruzá lo que te interesa con lo que falta.',
                        'Escribí tu nicho en una frase: tema + público + enfoque.',
                    ],
                    [
                        'Que haya otros creadores es buena señal: significa que hay público.',
                        'Aprender en público también es un nicho: contar lo que vas aprendiendo desde cero.',
                    ],
                ],
                'Subí tu primer video' => [
                    ['Tu celular.', 'Tu nicho definido.'],
                    [
                        'Elegí un tema que puedas explicar en menos de 60 segundos.',
                        'Escribí un guion corto: gancho, explicación y cierre.',
                        'Grabá frente a una ventana o con buena luz, en un lugar sin ruido.',
                        'Hacé como máximo 3 tomas y quedate con la mejor.',
                        'Publicalo, aunque no te convenza del todo.',
                    ],
                    [
                        'El gancho es la primera oración: tiene que decir por qué vale la pena seguir mirando.',
                        'Nadie más que vos va a notar los detalles que te molestan del primer video.',
                    ],
                ],
            ],
        ];
    }
}
