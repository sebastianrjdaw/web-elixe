# Auditoría del estado actual de Elixe Web

Fecha de auditoría: 17 de julio de 2026  
Commit auditado: `ad79bec` (`main`)  
Entorno canónico: Docker Compose local

## 1. Resumen ejecutivo

Elixe Web ya dispone de una vertical funcional completa para el MVP: Xibo alimenta una base local, la red pública muestra pantallas reales en Leaflet, el formulario unificado crea leads y encola dos emails, y un admin Inertia permite consultar leads, pantallas, CMS y sincronizaciones. La aplicación arranca en Docker, las migraciones están aplicadas, el build de producción compila y los 21 tests existentes pasan.

No se considera preparada para producción todavía. Los principales bloqueos son:

- existen dos formularios públicos antiguos que duplican el flujo principal y eluden captcha, validación telefónica estricta y feedback de errores;
- el admin no muestra buena parte de los datos comerciales que sí se guardan en cada lead y carece de filtros operativos, historial visible y acciones rápidas;
- los textos legales siguen siendo placeholders de desarrollo;
- ES/GL es una localización parcial: el documento continúa en `lang="en"` y casi toda la interfaz permanece en español;
- no hay SEO por página, el título del navegador queda vacío tras montar Inertia y el favicon es un archivo vacío;
- faltan el sistema de plantillas de respuesta y el layout profesional de emails;
- la sincronización Xibo solo procesa los primeros 100 registros y no tiene lock, paginación completa ni reconciliación de tags eliminados;
- no existe lint frontend, Pint detecta 13 incidencias de estilo y Vite presenta dos avisos de seguridad;
- Docker genera artefactos como `root`, ejecuta instalación/migración desde tres servicios y hace fallar comandos equivalentes en el host.

Resultado agregado de hallazgos: **0 críticos confirmados, 14 altos, 19 medios y 4 bajos**. Los problemas altos deben tratarse como bloqueos de lanzamiento, aunque la vertical actual sea demostrable.

## 2. Método y alcance

Se revisaron todos los archivos propios del repositorio, excluyendo dependencias generadas. La auditoría incluyó:

- dependencias, configuración, rutas, modelos, migraciones, controladores, servicios, comandos, mailables, seeders y tests;
- frontend React/Inertia, CSS/Tailwind, navegación, formularios, mapa, admin, CMS, idiomas, modo oscuro, SEO y accesibilidad;
- Docker, MySQL, Redis, queue worker, Scheduler, Mailpit y Nginx;
- navegación real con Firefox headless en `/`, `/red-de-pantallas`, `/asesoramiento`, `/locales`, `/anunciantes` y `/?lang=gl`;
- smoke HTTP de todas las páginas públicas, APIs públicas y redirección del admin;
- tests, build, TypeScript, lint, Pint, auditoría de dependencias, rutas, Scheduler y migraciones.

No se enviaron formularios reales, emails ni sincronizaciones Xibo durante esta auditoría. El admin se contrastó por código y tests de integración; no se alteraron credenciales ni datos existentes.

## 3. Stack real y versiones

| Área | Estado real |
|---|---|
| Backend | Laravel 13.19.0 sobre PHP 8.4.23 en Docker |
| Frontend | React 18.3.1, TypeScript 5.9.3, Tailwind 3.4.19 |
| Inertia | `inertia-laravel` 2.0.24 y `@inertiajs/react` 1.3.0 |
| Bundler | Vite 5.4.21 |
| Mapa | Leaflet 1.9.4, React Leaflet 4.2.1 y OpenStreetMap |
| Persistencia | MySQL 8.4.9 |
| Cache, sesión y cola | Redis 7.4.9 |
| Runtime frontend | Node 22.23.1 y npm 10.9.8 en Docker |
| Infraestructura | Docker Compose 2.32.3, Nginx 1.27, PHP-FPM, queue, Scheduler y Mailpit |
| Admin | Admin propio en React/Inertia; **Filament no está instalado** |
| Idiomas | Tablas ES/GL y selector por query string; implementación pública parcial |
| Tema | Claro/oscuro propio en la web pública; admin solo claro |

La ausencia de Filament es una desviación del stack esperado, pero no es por sí sola un defecto funcional. El admin propio ya tiene una base válida. Migrarlo sin una decisión explícita sería una refactorización masiva; la recomendación es completar primero el admin Inertia y reevaluar Filament mediante una decisión de arquitectura separada.

El PHP del host es 8.1.2 y no puede cargar las dependencias actuales, que requieren PHP `>=8.4.1`. El repositorio declara `php: ^8.3`, por lo que la restricción declarada no representa fielmente el lock actual. Docker sí satisface la plataforma.

## 4. Arquitectura e inventario

### Estructura

- `app/Http/Controllers/Web`: páginas públicas y alta de leads.
- `app/Http/Controllers/Public`: tres endpoints públicos de pantallas.
- `app/Http/Controllers/Admin`: autenticación, dashboard, leads, pantallas, sync, CMS, settings y diagnósticos.
- `app/Http/Requests`: tres Form Requests de leads.
- `app/Services/Xibo`: OAuth, lecturas y sincronización de displays.
- `app/Services/Diagnostics`: diagnóstico del entorno, Xibo y visibilidad pública.
- `app/Support/AuditLogger.php`: registro manual de acciones administrativas.
- `app/Mail`: confirmación al usuario y notificación interna, ambas en cola.
- `app/Console/Commands`: administración, diagnóstico, Xibo y resumen diario.
- `resources/js/app.tsx`: todo el frontend público y admin en un único archivo de 1.133 líneas.
- `resources/css/app.css`: sistema visual actual en 146 líneas de capas Tailwind.
- `database/migrations`: 12 migraciones aplicadas.
- `tests`: 21 tests y 101 aserciones.

No existe `app/Jobs`: el trabajo asíncrono se limita a mailables que implementan `ShouldQueue`. Tampoco existe una suite de tests JavaScript o E2E.

### Rutas

Hay 42 rutas propias:

- 13 rutas públicas de página/formulario;
- 4 endpoints API públicos;
- 3 rutas de login/logout;
- 22 rutas administrativas autenticadas.

`/pantallas` y `/red-de-pantallas` resuelven a la misma página. `/api/public/screens` y `/api/public/screens/map` también son alias idénticos.

### Modelos y datos

Modelos principales:

- `Screen`, `ScreenTag`, `SyncRun` y `DiagnosticRun`;
- `Lead` con relación muchos-a-muchos a pantallas;
- `ContentBlock`, `Faq`, `LegalPage` y `Setting`;
- `AuditLog` y `User`.

Estado local observado, sin exponer contenido personal:

| Dato | Cantidad |
|---|---:|
| Pantallas sincronizadas | 6 |
| Pantallas públicas | 3 |
| Leads | 4 |
| Bloques CMS | 4 |
| FAQs | 4 |
| Páginas legales | 3 |
| Ejecuciones Xibo | 13 |
| Jobs fallidos | 0 |

### Migraciones

Las 12 migraciones están aplicadas. Existen tablas para usuarios, pantallas, tags, leads, selección de pantallas, sync, auditoría, CMS y diagnósticos.

No existen aún `response_templates`, actividades/notas de lead ni una estructura de respuestas enviadas. La migración `2026_07_13_000001_align_elixe_mvp_schema.php` no implementa `down()`, por lo que no es reversible. El estado de lead conserva simultáneamente `new` y `nuevo` por compatibilidad histórica.

### Servicios, Xibo y Scheduler

`XiboService` solo hace OAuth y peticiones GET; no se encontraron escrituras a Xibo. El token se cachea y la sincronización usa `updateOrCreate`, por lo que la base es razonablemente segura para el MVP.

Scheduler ejecuta:

- Xibo a las 08:00, 14:00 y 20:00;
- `leads:send-daily-summary` a las 08:00.

La zona horaria de Laravel es UTC, no `Europe/Madrid`, y el comando de resumen diario solo imprime un contador: no envía ningún resumen.

### CMS y multidioma

El CMS es deliberadamente pequeño y adecuado como base: cuatro bloques de home, FAQs, legales y settings. Sin embargo, solo unas pocas cadenas se leen de esas tablas. Header, footer, formularios, pantallas, botones y gran parte de la home están hardcodeados en español.

El locale se decide en `PageController` solo para home y legales mediante `?lang=gl`; no se establece `app()->setLocale()`, no se conserva al navegar y no existen archivos `lang/es` o `lang/gl`. El HTML observado para gallego fue `<html lang="en">`.

## 5. Estado funcional

| Requisito | Estado | Evidencia y observaciones |
|---|---|---|
| Home | Parcial alta | Claim correcto, propuesta clara, métricas reales, mapa, FAQ y CTA; faltan control publicitario, servicios creativos futuros y recorridos detallados |
| Navegación | Parcial | Header responsive y menú móvil; el idioma se pierde al navegar y no hay estado de ruta activa |
| Página para locales | Parcial baja | Existe, pero es básicamente un formulario antiguo sin recorrido comercial ni errores visibles |
| Página para anunciantes | Parcial baja | Existe con selección y formulario antiguo; no tiene solicitud guiada por objetivo/zona/presupuesto/contacto |
| Red de pantallas | Implementada | Texto, dos filtros, mapa real, tarjetas, selección y CTA |
| Mapa real | Implementado | 3 marcadores públicos cargados desde OpenStreetMap en la prueba |
| Filtros | Implementados | Sector y tipo en cliente; sin URL compartible ni filtro de provincia/municipio |
| Selección | Parcial | Guarda en `sessionStorage` y llega a asesoramiento; no se restaura visualmente al recargar ni se limpia tras completar |
| Formulario unificado | Implementado con gaps | Tipos `venue`, `advertiser`, `other`, condicionales, bloqueo durante envío, privacidad y gracias |
| Validación | Parcial alta | Backend correcto en `/asesoramiento`; las dos rutas heredadas tienen reglas más débiles y el frontend no anuncia errores de forma accesible |
| Captcha | Parcial | Turnstile existe solo en el formulario unificado y está desactivado en el entorno actual |
| Emails | Parcial | Dos mailables en cola; copy genérico, sin layout Elixe, sin idioma ni plantillas editables |
| Leads | Implementados | Persistencia, asociación de pantallas, estados y export básico |
| Admin | Parcial media | Dashboard, listados, CMS, sync y diagnósticos; faltan operación completa y responsive |
| Sincronización Xibo | Parcial alta | Solo lectura y registrada; faltan paginación, lock, retry, reconciliación e idempotencia fuerte |
| FAQs | Parcial | CMS bilingüe y render en home; no son acordeón y gran parte del contexto circundante no se traduce |
| Legales | No aptas para producción | Las tres páginas contienen avisos explícitos de texto inicial de desarrollo |
| Multidioma | Parcial baja | Solo contenido CMS seleccionado; idioma de documento y UI incorrectos |
| Modo oscuro | Parcial | Funciona en el layout público y persiste; admin no lo soporta y puede haber flash inicial |

Los datos públicos de Xibo están filtrados correctamente en cuanto a IDs de Xibo, dirección, licencia y payload. No obstante, el payload expone el ID incremental de la tabla `screens`, contrario a la nueva restricción de no mostrar IDs internos, y el backend acepta cualquier ID existente en `selected_screen_ids`, no solo pantallas públicas.

## 6. Calidad frontend

### Lo que funciona bien

- La home y el mapa tienen una presentación visual consistente y profesional.
- No se detectó overflow horizontal a 1.440 px ni al mínimo de 500 px admitido por Firefox headless.
- El menú móvil, el mapa y el formulario unificado renderizaron correctamente.
- Las imágenes decorativas están optimizadas razonablemente: las tres suman unos 289 KB.
- Existe `prefers-reduced-motion` y un focus visible global.
- TypeScript estricto pasa sin errores.
- No se observaron excepciones propias de React ni recursos fallidos en la home.

### Gaps confirmados

- Inertia deja `document.title` vacío tras montar las páginas.
- `public/favicon.ico` tiene 0 bytes y Firefox registra errores MIME del favicon.
- No hay `Head`, description, canonical, Open Graph, hreflang ni sitemap.
- Todo el frontend, incluido admin y Leaflet, entra en un único chunk de 490,28 KB (150,52 KB gzip).
- `app.tsx` mezcla tipos, layouts, páginas, formularios, mapa y admin; faltan los componentes reutilizables definidos en el contexto.
- El formulario unificado muestra errores visuales, pero no usa `aria-invalid`, `aria-describedby`, región viva, resumen ni foco al primer error.
- Los formularios antiguos tienen 0 controles con `id`; sus labels solo cubren checkboxes.
- La selección guardada no hidrata `ScreensPage` después de recargar.
- El mapa centra y hace zoom sobre la primera pantalla; no calcula bounds para toda la red.
- No hay code splitting, lazy loading de mapa o páginas, ni skeletons consistentes fuera del montaje inicial del mapa.
- Las tablas del admin usan `overflow-hidden`, no `overflow-x-auto`, y la navegación móvil solo enlaza Leads y Pantallas.
- El diseño es atractivo, pero el hero y navegación ocupan una proporción muy oscura respecto a la identidad luminosa solicitada y la foto principal comunica evento más que pantallas locales.

## 7. Calidad backend y seguridad

### Lo que funciona bien

- Los tres puntos de entrada usan Form Requests; el unificado tiene reglas condicionales completas.
- CSRF, sesiones Laravel, regeneración de sesión al iniciar sesión y throttling de POST están presentes.
- Los emails implementan `ShouldQueue` y Redis está activo.
- La salida pública de pantallas excluye dirección, IDs de Xibo, nombres internos, licencia y payload técnico.
- La auditoría registra cambios de estado, reenvíos, CSV, CMS, settings, visibilidad, sync y diagnósticos.
- Composer no reporta advisories conocidos.
- Xibo permanece en modo lectura.

### Gaps confirmados

- La creación de lead, asociación de pantallas y encolado no forman una operación transaccional/idempotente.
- Cualquier usuario de `users` accede a todo el admin; no hay roles, policies ni autorización por acción.
- `TrustHosts` está deshabilitado y Nginx no añade CSP, HSTS, `X-Content-Type-Options`, frame policy, referrer policy ni permissions policy. También expone `X-Powered-By`.
- La verificación Turnstile no captura timeouts/excepciones de red; una caída externa puede producir error 500.
- Los errores GET de Xibo incluyen el body remoto completo y pueden persistirlo en `sync_runs`.
- El token Xibo se guarda dentro de un `Cache::remember` y después el propio `remember` vuelve a escribirlo con 50 minutos, pudiendo sobreescribir el TTL calculado.
- Xibo solo solicita `length=100`, no elimina tags que desaparecen, no marca pantallas ausentes, cuenta como actualizados registros sin cambios y permite ejecuciones concurrentes.
- Home y APIs repiten consultas completas sin cache; los endpoints públicos no definen cache HTTP específica.
- El admin solo transmite al frontend una fracción del lead: omite sector, zona, presupuesto, datos de local, campaña, fechas y varios flags.
- El CSV también exporta un subconjunto pequeño y no incluye las pantallas seleccionadas.

## 8. Hallazgos priorizados

Escala de esfuerzo: XS < 0,5 día; S 0,5–1 día; M 2–4 días; L 5–8 días.  
Prioridad: P0 bloquea producción; P1 siguiente incremento; P2 mejora planificada.

### Severidad alta

| ID | Problema | Solución propuesta | Esfuerzo | Prioridad |
|---|---|---|---:|---:|
| H-01 | `/locales` y `/anunciantes` mantienen formularios duplicados sin captcha, errores visibles ni la validación estricta del flujo principal | Unificar UI y backend; convertir esas páginas en recorridos comerciales que reutilicen el mismo formulario/Request o redirijan de forma compatible | M | P0 |
| H-02 | El admin no muestra la mayoría de datos guardados del lead ni tiene filtros UI, historial, notas o acciones rápidas | Payload completo y seguro, filtros, ficha operativa, actividades, llamadas/WhatsApp/email y export coherente | L | P0 |
| H-03 | Privacidad, cookies y aviso legal son placeholders de desarrollo visibles públicamente | Incorporar textos aprobados, datos fiscales y guard de despliegue que impida publicar placeholders | S + validación legal | P0 |
| H-04 | ES/GL es parcial, no persiste en navegación y el documento gallego declara `lang="en"` | Middleware de locale, rutas/URLs coherentes, catálogo de traducciones, persistencia y pruebas ES/GL | L | P1 |
| H-05 | SEO técnico ausente: título runtime vacío, sin metas/canonical/OG/hreflang/sitemap y favicon vacío | Componente SEO por página, favicon real, sitemap/robots y metadatos localizados | M | P0 |
| H-06 | No existen `response_templates`, respuestas editables ni layout HTML profesional; los emails son genéricos | Migración, modelo/CRUD, layout Blade Elixe, plantillas por tipo/idioma, previews y tests | L | P1 |
| H-07 | Sync Xibo sin paginación completa, lock, retry, transacción, reconciliación o TTL fiable | Servicio paginado, lock Redis, `withoutOverlapping`, retry/backoff, sync de tags por diff, métricas reales y tests de idempotencia | L | P0 |
| H-08 | La API pública expone IDs incrementales y el POST acepta pantallas internas/ocultas si se conoce su ID | Identificador público opaco y validación contra el scope `publiclyVisible`; migrar selección sin exponer PK | M | P0 |
| H-09 | `npm audit` reporta 1 vulnerabilidad alta y 1 moderada; Vite dev escucha `0.0.0.0:5173` | Actualizar Vite/esbuild de forma compatible, restringir exposición dev y verificar build/E2E | M | P0 |
| H-10 | Docker crea `public/build`/`public/hot` como root y ejecuta Composer/migraciones en app, queue y scheduler | UID/GID de desarrollo, entrypoints por rol, instalación en build y migración única controlada | M | P0 |
| H-11 | No existe `npm run lint`; Pint falla en 13 archivos y no hay gates de calidad frontend | ESLint TypeScript/React, scripts `lint`/`typecheck`, Pint limpio y pipeline CI | M | P0 |
| H-12 | Las páginas de locales/anunciantes no explican beneficios, control publicitario, pasos ni acompañamiento | Rediseñar ambos recorridos con contenido honesto, pasos diferenciados, FAQs y CTA al formulario único | L | P1 |
| H-13 | El admin no es usable en móvil: navegación incompleta y tablas recortadas | Navegación responsive completa, tablas con scroll/tarjetas y acciones táctiles | M | P1 |
| H-14 | `README.md` contiene marcadores de conflicto Git y publica una contraseña de desarrollo compartida | Resolver conflicto, eliminar credenciales, documentar creación segura y rotar cualquier credencial reutilizada | S | P0 |

### Severidad media

| ID | Problema | Solución propuesta | Esfuerzo | Prioridad |
|---|---|---|---:|---:|
| M-01 | La selección no se restaura en el mapa tras recargar y queda obsoleta después del envío | Hook/estado único de selección, hidratación, limpieza tras éxito y opción “deseleccionar todo” | S | P1 |
| M-02 | El mapa usa la primera pantalla como centro y no tiene estado vacío completo | `fitBounds`, zoom máximo para redes pequeñas, skeleton y empty state explicativo | S | P1 |
| M-03 | Frontend monolítico y bundle único de 490 KB | Separar layouts, componentes y páginas; resolución dinámica Inertia y lazy load de Leaflet/admin | L | P1 |
| M-04 | Adaptadores Inertia en majors diferentes (Laravel 2, React 1) | Alinear versiones y cubrir navegación/formularios con regresión | M | P1 |
| M-05 | Los errores del formulario no se anuncian ni reciben foco | `aria-invalid`, `aria-describedby`, resumen vivo, foco y tests de teclado | S | P1 |
| M-06 | Aplicación y Scheduler usan UTC para un negocio gallego | Configurar `Europe/Madrid`, validar DST y documentar horarios | XS | P0 |
| M-07 | Faltan headers de seguridad y se expone versión de PHP | Middleware/Nginx para headers, ocultar versión y habilitar `TrustHosts` en producción | S | P1 |
| M-08 | Nginx no define estrategia HTTPS, cache de assets, compresión ni healthchecks de app/queue/scheduler | Configuración de producción separada, healthchecks, cache inmutable, gzip/brotli si aplica y reverse proxy TLS | M | P1 |
| M-09 | Todo usuario es administrador total | Rol mínimo `admin`, policies/gates y tests de autorización | M | P1 |
| M-10 | Auditoría sin UI y con eventos incompletos | Timeline por lead, índice de auditoría y eventos de creación, confirmación y respuestas | M | P1 |
| M-11 | `leads:send-daily-summary` no envía nada pese a su nombre | Crear mailable/notification real, cola, destinatarios y test Scheduler | S | P1 |
| M-12 | Estados duplicados `new`/`nuevo` y migración de alineación no reversible | Normalizar estado, migrar datos y añadir `down()` seguro donde sea viable | S | P1 |
| M-13 | Home/APIs repiten queries sin cache y no tienen política HTTP | Cache corta invalidable tras sync/CMS, ETag o cache headers y medición de queries | M | P2 |
| M-14 | Métricas “en red”, “activas” y “disponibles” mezclan criterios técnicos/comerciales | Definir glosario y mostrar solo métricas comerciales verificadas | S | P1 |
| M-15 | Cobertura sin JS/E2E, accesibilidad, SEO, captcha real, éxito Xibo, filtros ni emails renderizados | Matriz Feature/Unit/Browser, coverage report y smoke automatizado | L | P1 |
| M-16 | Turnstile puede convertir una caída de red en 500 | Capturar excepciones, mensaje recuperable, logs sin token y política fail-closed | S | P0 |
| M-17 | Los errores GET de Xibo guardan el body remoto completo | Sanitizar mensajes, usar códigos/correlation ID y log seguro restringido | S | P1 |
| M-18 | El CMS solo controla una parte menor de la home; el resto está hardcodeado | Delimitar contenido editable útil sin CMS genérico y mover copy comercial/i18n seleccionado | M | P2 |
| M-19 | Alta, asociación y enqueue no son idempotentes; un fallo parcial favorece leads duplicados | Transacción para datos, token de envío/idempotency key y dispatch después de commit | M | P1 |

### Severidad baja

| ID | Problema | Solución propuesta | Esfuerzo | Prioridad |
|---|---|---|---:|---:|
| L-01 | Alias API duplicados, `welcome.blade.php` sin uso y tests `Example` residuales | Depurar rutas/archivos y convertir tests de ejemplo en smoke útil | S | P2 |
| L-02 | Vite avisa que las URLs absolutas de tres fondos no se resuelven en build | Importar assets desde Vite o documentar explícitamente su servido desde `public` | XS | P2 |
| L-03 | Copy administrativo sin acentos, FAQ sin acordeón y estados vacíos admin pobres | Revisión de microcopy y componentes de disclosure/empty state | M | P2 |
| L-04 | Proof of Play, media kit y servicios creativos no tienen una nota de alcance futura específica | Añadir roadmap sin implementar métricas ni promesas no verificadas | S | P2 |

## 9. QA ejecutada

### Resultado en Docker

| Comando | Resultado |
|---|---|
| `php artisan test` | OK — 21 tests, 101 aserciones |
| `npm run build` | OK — chunk JS 490,28 KB; tres warnings de assets públicos |
| `npx tsc --noEmit` | OK |
| `npm run lint` | Falla — script inexistente |
| `vendor/bin/pint --test` | Falla — 13 archivos con estilo pendiente |
| `php artisan route:list --except-vendor` | OK — 42 rutas |
| `php artisan schedule:list` | OK — 4 eventos |
| `php artisan config:clear` | OK |
| `php artisan cache:clear` | OK |
| `php artisan migrate:status` | OK — 12 migraciones aplicadas |
| `composer validate --strict` | OK |
| `composer audit` | OK — sin advisories |
| `npm audit --omit=dev` | Falla — 1 alta y 1 moderada en Vite/esbuild |
| `docker compose config --quiet` | OK |

### Smoke HTTP y navegador

- `/`, `/locales`, `/anunciantes`, `/red-de-pantallas`, `/asesoramiento`, `/gracias` y las tres legales respondieron 200.
- `/admin` respondió 302 hacia login sin sesión.
- Las cuatro APIs públicas respondieron 200.
- Home, red y asesoramiento se renderizaron en Firefox headless en escritorio y viewport móvil de 500 px, sin overflow horizontal.
- La home cargó todos sus recursos medidos y el mapa mostró 3 marcadores.
- No se observaron errores propios de React. Firefox registró errores internos de carga de favicon, explicados por el archivo ICO vacío.
- Al forzar el formulario vacío se mostraron ocho errores, pero ninguno quedó asociado mediante ARIA ni se movió el foco.
- Al seleccionar una pantalla, `sessionStorage` conservó el ID y asesoramiento la recuperó; al recargar la red, la selección visual volvió a cero.

### Limitaciones de verificación

- No se probó envío real de Turnstile, SMTP externo ni Xibo real para evitar efectos externos.
- No existe harness E2E que capture `console.error` de forma repetible en CI.
- No se hizo auditoría legal del contenido ni prueba con tecnologías asistivas físicas.
- La revisión responsive automatizada llegó a 500 px por el mínimo de la instancia Firefox; el CSS se inspeccionó además para el breakpoint inferior.

## 10. Conclusión y puerta de aprobación

La base técnica merece evolucionarse, no reescribirse. La prioridad es estabilizar un único recorrido de conversión, hacer operable la información ya capturada, cerrar producción/seguridad/SEO y robustecer Xibo antes de ampliar el diseño.

No se ha iniciado la refactorización solicitada porque el contexto exige aprobar primero el plan. La secuencia y estimaciones propuestas están en `docs/professionalization-plan.md`.
