# Plan de profesionalización de Elixe Web

Fecha: 17 de julio de 2026  
Fuente: `docs/current-state-audit.md`  
Estado: **cambios funcionales implementados; pendiente de contenido y configuración de producción**

## Resultado de la ejecución funcional (17 de julio de 2026)

Se ha ejecutado el alcance funcional aprobado manteniendo Laravel + React/Inertia y las URLs españolas existentes. La entrega incluye estabilidad de dependencias y Docker, permisos de administrador, IDs públicos, formularios unificados e idempotentes, Xibo paginado y reconciliado, CRM con historial/filtros/respuestas, plantillas de email, resumen diario, URLs ES/GL, SEO y sitemap, diagnósticos de preparación y cobertura automatizada de los flujos críticos.

Siguen bloqueados por información externa, no por código: textos legales definitivos, razón social/NIF/domicilio, credenciales y remitentes SMTP de producción, claves Turnstile, dominio HTTPS final y validación de los nombres/criterios comerciales. El diagnóstico administrativo muestra estos pendientes sin exponer secretos.

## 1. Objetivo

Convertir la base actual en una web comercial y una herramienta operativa fiables sin reescribir el producto ni inventar alcance, audiencia o resultados. El plan conserva Laravel + React/Inertia, la base local de pantallas, Xibo en solo lectura, MySQL, Redis, Docker, el mapa real y los leads existentes.

La ejecución seguirá este orden:

```text
estabilidad
→ sistema visual
→ web comercial
→ formularios y conversión
→ panel administrativo
→ emails y plantillas
→ QA, tests y documentación
```

La auditoría y este plan forman la puerta previa. No se realizará la refactorización amplia hasta aprobar al menos la Fase 1.

## 2. Principios de ejecución

1. Evolución incremental con una aplicación desplegable al terminar cada fase.
2. Datos reales como única fuente de métricas públicas.
3. Xibo permanece en modo lectura; la web nunca usa sus credenciales directamente.
4. Un solo motor de formularios y validación, aunque existan varios puntos de entrada comerciales.
5. Backend Laravel como fuente de verdad de validación y autorización.
6. Componentes pequeños y reutilizables; no se reemplaza el monolito en un único cambio.
7. Accesibilidad, idioma, responsive, estados de carga/vacío/error y modo oscuro forman parte de cada entrega.
8. Dependencias nuevas solo cuando cubran una necesidad verificable.
9. No se implementan pagos, contratación automática, subida de creatividades ni Proof of Play no fiable.
10. Cada migración incluye estrategia de despliegue, compatibilidad y rollback.

## 3. Decisiones que requieren aprobación

### 3.1 Estrategia del admin

Recomendación: **mantener el admin React/Inertia actual** y profesionalizarlo. Ya cubre autenticación, CRM base, CMS, pantallas, diagnósticos y auditoría; migrarlo ahora a Filament añadiría riesgo y no corrige por sí solo los gaps de producto.

Se hará una decisión de arquitectura breve al inicio. Si se exige Filament, debe tratarse como una línea de trabajo separada y sumar aproximadamente 8–12 días, además de una migración de UX y tests. No se instalará Filament por inercia.

### 3.2 Estrategia de idioma

Debe acordarse si se prefieren:

- español sin prefijo y gallego en `/gl/...`; o
- ambos idiomas con prefijo, `/es/...` y `/gl/...`.

El prefijo explícito para ambos idiomas es más uniforme; mantener español sin prefijo reduce cambios y conserva URLs actuales. En ambos casos habrá locale persistente, canonical y hreflang correctos.

### 3.3 Contenido externo imprescindible

Antes del cierre de producción, Elixe debe facilitar o aprobar:

- razón social, NIF, domicilio y contacto legal;
- privacidad, cookies y aviso legal definitivos;
- logo utilizable en web/email y datos de contacto finales;
- destinatarios y remitentes SMTP de producción;
- nombres comerciales aceptados para tipos/sectores y estados;
- criterio exacto de “pantalla gestionada”, “activa” y “disponible”.

El código puede preparar los mecanismos, pero no debe inventar estos datos.

## 4. Roadmap resumido

Estimación para una persona con dedicación principal. Incluye implementación y tests de cada fase, no esperas externas.

| Fase | Alcance | Estimación | Dependencia |
|---|---|---:|---|
| 1 | Estabilidad y errores | 8–12 días | Aprobación del plan |
| 2 | Sistema visual | 4–6 días | Fase 1 |
| 3 | Web comercial | 6–9 días | Fases 1–2 y copy aprobado |
| 4 | Formularios y conversión | 5–8 días | Fases 1–3 |
| 5 | Panel administrativo | 8–12 días | Fases 1 y 4 |
| 6 | Emails y plantillas | 4–6 días | Fases 4–5 y datos de marca |
| 7 | QA, tests y documentación | 5–8 días | Todas las anteriores |
| **Total orientativo** |  | **40–61 días** |  |

Se puede aprobar y ejecutar solo la Fase 1 como primer incremento. A partir de ahí, frontend comercial y admin pueden avanzar en paralelo si hay dos responsables, manteniendo los gates comunes.

## 5. Fase 1 — Estabilidad y errores

### Objetivo

Eliminar bloqueos de producción y dejar una base reproducible, segura y medible antes de ampliar diseño o funcionalidad.

### Trabajo

#### Repositorio y entorno

- Resolver los marcadores Git de `README.md` y retirar credenciales compartidas.
- Actualizar README con arranque, comandos, troubleshooting y creación segura del primer admin.
- Alinear la restricción PHP de Composer con el lock y documentar Docker como runtime canónico.
- Evitar artefactos root-owned mediante UID/GID de desarrollo o permisos controlados.
- Separar entrypoint de app, queue y Scheduler; Composer no se ejecutará en cada worker.
- Ejecutar migraciones desde un paso único de despliegue, no concurrentemente desde tres contenedores.
- Añadir healthchecks para app, queue y Scheduler.
- Configurar `Europe/Madrid` y verificar horarios con cambio estacional.

#### Calidad y dependencias

- Actualizar Vite/esbuild a una versión segura compatible con Node 22.
- Alinear los adaptadores Inertia en la misma generación.
- Añadir ESLint para TypeScript/React, scripts `lint` y `typecheck` y configuración mínima no conflictiva.
- Aplicar Pint y dejar `vendor/bin/pint --test` limpio.
- Preparar CI con Composer validate/audit, Pint, tests, npm audit, lint, TypeScript y build.

#### Seguridad y producción

- Corregir favicon y metadatos base para eliminar errores del navegador.
- Añadir headers de seguridad, host validation y configuración Nginx de producción.
- Restringir Vite a desarrollo local y no publicar 5173 fuera del entorno de desarrollo.
- Añadir cache inmutable para assets versionados y compresión HTTP.
- Crear un rol/gate mínimo de administración y tests de acceso.

#### Formularios heredados

- Retirar la duplicación de POST o hacer que las rutas heredadas deleguen en el flujo unificado.
- Mantener URLs `/locales` y `/anunciantes` como páginas comerciales, sin dos implementaciones independientes del formulario.
- Garantizar captcha, teléfono, privacidad, errores y bloqueo de doble envío en cualquier entrada.

#### Xibo y datos públicos

- Corregir TTL del token y sanitizar errores remotos.
- Implementar paginación hasta agotar displays.
- Añadir lock Redis y `withoutOverlapping` para Scheduler/manual.
- Añadir retry/backoff acotado y timeouts diferenciados.
- Reconciliar tags eliminados y distinguir creado/cambiado/sin cambios/omitido.
- Registrar errores parciales sin perder toda la corrida.
- Introducir un identificador público opaco y validar que solo se asocien pantallas públicamente visibles.
- Añadir tests de paginación, idempotencia, concurrencia, tags eliminados y token corto.

#### Legales

- Sustituir placeholders por contenido aprobado cuando esté disponible.
- Mientras falte contenido, impedir que producción los publique inadvertidamente mediante check de readiness o `noindex` temporal explícito.

### Entregables

- README y `.env.example` consistentes.
- Docker reproducible sin archivos root-owned.
- CI y scripts de calidad.
- Flujo de formulario único.
- Xibo robustecido sin operaciones de escritura.
- Tests de regresión P0.
- Nota de decisión del admin.

### Criterio de salida

- Tests, Pint, lint, TypeScript y build pasan en limpio.
- Composer/npm audit no dejan vulnerabilidades altas conocidas aplicables.
- Las tres entradas de contacto comparten captcha y validación.
- Dos sincronizaciones consecutivas sin cambios son idempotentes y no se solapan.
- Ninguna API pública expone PK, Xibo ID, códigos técnicos o pantallas ocultas.
- El despliegue no puede salir con legales de desarrollo por accidente.

## 6. Fase 2 — Sistema visual

### Objetivo

Formalizar la identidad actual en un sistema luminoso, costero, accesible y mantenible sin perder la personalidad ya conseguida.

### Trabajo

- Definir tokens en Tailwind para azul océano, azul profundo, cyan, fondos, texto, estados y dark mode.
- Definir tipografía, escala de encabezados, spacing, radios, sombras, anchos y motion.
- Reducir el peso visual oscuro del hero y equilibrarlo con blancos/fondos marinos claros.
- Verificar contraste AA, focus, reduced motion y estados disabled/loading/error/success.
- Extraer progresivamente:

```text
PublicLayout
Header
MobileMenu
Footer
Hero
SectionHeading
PrimaryButton
SecondaryButton
FormField
FormError
StatusBadge
MetricCard
ScreenCard
MapPanel
FaqAccordion
CallToAction
LanguageSwitcher
ThemeToggle
```

- Separar layouts público/admin, tipos, páginas y componentes.
- Añadir `docs/design-system.md` con tokens, componentes, ejemplos y reglas de uso.

### Criterio de salida

- No hay estilos de botones/campos/tarjetas duplicados fuera de variantes justificadas.
- Componentes funcionan en claro/oscuro, móvil y teclado.
- La interfaz pública conserva una identidad coherente y más luminosa.
- El bundle empieza a dividirse por página y Leaflet carga de forma diferida.

## 7. Fase 3 — Web comercial

### Objetivo

Explicar Elixe con claridad, separar públicos y presentar la red real con credibilidad.

### Home

Implementar la secuencia acordada:

1. Hero con claim y tres CTAs: local, anunciante y asesoramiento.
2. Qué es Elixe.
3. Bloque para locales.
4. Bloque para anunciantes.
5. Cómo funciona con recorridos diferenciados.
6. Datos reales y glosario de métricas.
7. Preview real del mapa.
8. Control de contenido/publicidad.
9. FAQs en acordeón accesible.
10. CTA final.

### Para locales

- Contenido propio, promociones, menús, avisos y eventos.
- Instalación, mantenimiento y gestión técnica.
- Ingresos adicionales comunicados sin promesas.
- Categorías publicitarias no permitidas y espacio propio.
- Los cuatro pasos definidos en el contexto.

### Para anunciantes

- Zona, sector, pantallas concretas, objetivo y propuesta personalizada.
- Gestión de campaña y acompañamiento.
- Los cuatro pasos definidos en el contexto.
- Sección de adaptación creativa redactada como posibilidad, no como servicio incluido.
- CTA futuro “Solicitar dossier comercial” sin automatización ficticia.

### Red de pantallas

- Copy honesto para red pequeña/en crecimiento.
- `fitBounds`, filtros, resultados, selección, resumen y empty state.
- Nombres públicos y atributos permitidos únicamente.
- Filtros serializables en URL si aportan capacidad de compartir.
- Evaluar política de tiles OSM para el volumen previsto antes de producción.

### SEO e idioma

- Title/description únicos, canonical, Open Graph, alt/semántica y headings.
- Sitemap y robots por entorno.
- hreflang ES/GL y documento con locale real.
- Copy completo en ambos idiomas, incluidos formularios, errores, legales y emails donde se acuerde.

### Futuro documentado, no implementado

Crear una nota de alcance para Proof of Play: reproducciones, pantallas, fechas, duración, informe y condiciones para considerar el dato fiable. No se mostrará audiencia o reproducción hasta disponer de una fuente validada.

### Criterio de salida

- Un usuario entiende en el primer viewport qué hace Elixe y puede elegir su recorrido.
- Locales y anunciantes tienen páginas comerciales completas y distintas.
- Las únicas cifras visibles proceden de datos reales con definición documentada.
- Todas las páginas tienen SEO e idioma correctos.

## 8. Fase 4 — Formularios y conversión

### Objetivo

Convertir el formulario único en una solicitud guiada, accesible y resistente a duplicados.

### Trabajo

- Crear un único esquema de datos/tipos compartido por las variantes.
- Mantener tarjetas `venue`, `advertiser`, `other`.
- Para anunciantes, estructurar:

```text
Objetivo
Zona y pantallas
Presupuesto
Contacto
```

- Añadir objetivo de campaña sin pedir información innecesaria.
- Reutilizar `FormField`, `FormError`, selector de pantallas y resumen.
- Mantener datos al volver, cambiar de tipo o recibir 422.
- Hidratar y limpiar selección temporal correctamente.
- Añadir “deseleccionar todo” y evitar IDs obsoletos/ocultos.
- Asociar errores con `aria-describedby`, marcar `aria-invalid`, enfocar el primero y mostrar resumen vivo.
- Gestionar Turnstile expirado, error de red y reintento con copy claro.
- Añadir token idempotente por envío y transacción de lead/selección.
- Encolar emails `afterCommit`; un fallo de infraestructura no debe crear una UX ambigua.
- Confirmación específica por tipo en `/gracias`, sin revelar datos sensibles.

### Tests

- Casos válidos e inválidos de los tres tipos.
- Teléfono normalizado y privacidad.
- Captcha success/failure/timeout.
- Pantallas públicas, ocultas, inexistentes y selección obsoleta.
- Doble envío/idempotencia.
- Fallo de cola después de persistir.
- Navegación por teclado y asociación de errores.

### Criterio de salida

- No existe más de una implementación de validación por canal.
- Un doble clic o retry de red no genera dos leads.
- Todos los errores son comprensibles, accesibles y preservan los datos.
- La página de gracias y los emails corresponden al tipo de solicitud.

## 9. Fase 5 — Panel administrativo

### Objetivo

Convertir el admin en una bandeja operativa diaria, manteniendo el stack aprobado.

### Dashboard

- Leads nuevos, pendientes, semana y por tipo.
- Pantallas visibles/incompletas.
- Último sync y alertas de fallos.
- Acciones rápidas a leads, pantallas, sync y plantillas.

### Bandeja de leads

- Columnas requeridas: tipo, nombre, empresa/local, teléfono, email, municipio, estado, fecha y última acción.
- Filtros de estado, tipo, fecha, provincia, municipio, presupuesto, contacto y pantallas.
- Filtros persistentes, paginación y export que respeten el mismo query.
- Empty states y responsive mediante tabla scrollable o tarjetas móviles.

### Ficha de lead

- Todos los datos capturados, agrupados y con valores humanos.
- Estados normalizados:

```text
nuevo
contactado
cita_agendada
en_estudio
ganado
perdido
descartado
```

- Copiar email/teléfono, abrir llamada/WhatsApp, cambiar estado, responder, reenviar y exportar.
- Notas y timeline de acciones, con usuario y fecha.
- Pantallas seleccionadas usando nombre público y enlace interno seguro.

### Auditoría

- Mantener `audit_logs` como registro técnico inmutable.
- Añadir, si es necesario, `lead_activities` para notas, llamadas y respuestas de negocio.
- UI de historial con permisos.
- Auditar cambios de estado, email, export, settings, sync, visibilidad y cambios de plantilla.

### Pantallas

- Filtros de visibilidad, comercial, sincronización, provincia, municipio, tipo y sector.
- Avisos accionables que indiquen qué corregir en Xibo.
- Sync manual en cola, confirmación, lock y feedback de progreso.

### Decisión Filament

Si al terminar esta fase el admin Inertia sigue siendo costoso de mantener, se reevaluará Filament con una prueba acotada sobre un recurso no crítico. No se mezclará una migración de framework con la corrección funcional de esta fase.

### Entregable documental

- `docs/admin-workflow.md` con estados, permisos, filtros, acciones, auditoría y operación diaria.

### Criterio de salida

- El equipo puede resolver un lead sin consultar directamente la base o el email original.
- El historial explica quién hizo qué y cuándo.
- Filtros y export devuelven el mismo conjunto.
- Pantallas incompletas indican una corrección concreta en Xibo.
- Admin usable desde móvil para las acciones esenciales.

## 10. Fase 6 — Emails y plantillas

### Objetivo

Crear comunicaciones de marca consistentes, editables, auditadas y seguras.

### Modelo

Crear `response_templates` con los campos propuestos:

```text
id
name
lead_type
subject_es
subject_gl
body_es
body_gl
is_active
created_by
updated_by
timestamps
```

Evaluar campos adicionales solo si son necesarios: clave estable, propósito (`automatic`/`manual`) y versión.

### Plantillas iniciales

- recepción de local;
- recepción de anunciante;
- recepción de otra consulta;
- solicitud de información;
- propuesta de llamada;
- confirmación de cita;
- seguimiento;
- solicitud no viable;
- agradecimiento.

### Layout y envío

- Crear `resources/views/emails/layouts/elixe.blade.php` adaptando el ejemplo a logo, colores, contacto e idioma.
- CSS inline compatible, preheader, fallback textual y enlaces seguros.
- Variables permitidas mediante una lista explícita; no ejecutar Blade arbitrario guardado en base.
- Preview admin con datos ficticios y envío de prueba controlado.
- Respuesta automática específica para local/anunciante/otra consulta.
- Respuesta manual desde ficha, con auditoría de destinatario, plantilla y resultado.
- Resumen diario real, en cola, con conteos y enlaces al admin.
- Reintentos, failed jobs y documentación operativa.

### Tests

- Render de cada plantilla y fallback ES/GL.
- Escapado de variables y HTML permitido.
- Destinatarios/remitentes.
- Queue, retry y failed job.
- Auditoría de envío y reenvío.

### Entregable documental

- `docs/email-templates.md` con catálogo, variables, idioma, preview y operación.

### Criterio de salida

- Cada lead recibe una confirmación apropiada y profesional.
- El equipo puede responder con plantilla sin editar código.
- Ninguna plantilla puede ejecutar contenido arbitrario.
- Todos los envíos manuales quedan auditados.

## 11. Fase 7 — QA, tests y documentación

### Objetivo

Cerrar regresiones, verificar producción y entregar una base operable por otra persona.

### Automatización

- Feature tests de rutas, permisos, leads, admin, CMS, Xibo, emails, locale y APIs.
- Unit tests de normalizadores, reglas, tokens públicos e idempotencia.
- Browser tests para navegación Inertia, mapa, filtros, selección, formulario, tema y admin.
- Captura automática de `console.error`, warnings React y requests fallidas.
- Axe u otra comprobación accesible acotada, más revisión manual de teclado/lector.
- Lighthouse o medición equivalente para rendimiento, SEO y accesibilidad.
- Coverage como indicador, no como sustituto de escenarios.

### Matriz manual

- Home y recorridos ES/GL.
- 360/390/768/1024/1440 px.
- Mapa con cero, una y varias pantallas.
- Filtros, selección, recarga, volver y completar.
- Los tres tipos de formulario, errores, captcha, retry y gracias.
- Email cliente/interno, respuesta manual y resumen.
- Admin: permisos, filtros, estados, historial, export y pantallas.
- Xibo: éxito, fallo, timeout, solape y datos incompletos.
- Modo oscuro, reduced motion, teclado y zoom 200 %.

### Comandos de cierre

```bash
php artisan test
vendor/bin/pint --test
npm run lint
npm run typecheck
npm run build
php artisan route:list
php artisan schedule:list
php artisan config:clear
php artisan cache:clear
composer audit
npm audit
```

### Documentación final

Crear o actualizar:

```text
docs/current-state-audit.md
docs/professionalization-plan.md
docs/design-system.md
docs/admin-workflow.md
docs/email-templates.md
docs/final-qa-report.md
docs/architecture.md
docs/database.md
docs/xibo-integration.md
README.md
.env.example
```

`docs/final-qa-report.md` incluirá versión/commit, entorno, comandos, resultados, pruebas manuales, issues conocidos y decisión de go/no-go.

### Criterio de salida

- Cero errores o warnings propios de la aplicación en consola.
- Todos los gates automatizados pasan.
- No quedan hallazgos altos abiertos.
- Los medios aceptados tienen owner y fecha.
- Documentación reproducible desde clon limpio.
- Checklist de despliegue y rollback validado.

## 12. Migraciones y despliegue

Orden recomendado por release:

1. Backup y verificación de migraciones.
2. Migraciones aditivas: identificador público, roles, plantillas y actividades.
3. Backfill idempotente y validado.
4. Despliegue compatible con esquema viejo/nuevo cuando sea necesario.
5. Activación de nuevas rutas/lecturas.
6. Retirada posterior de estados/rutas heredados, nunca en el mismo paso que el backfill.

La cola se reiniciará de forma controlada después de cada despliegue. Scheduler y sync manual compartirán lock. Las migraciones no se ejecutarán desde todos los contenedores al arrancar.

## 13. Riesgos y mitigación

| Riesgo | Mitigación |
|---|---|
| Cambios de Xibo o payload irregular | Fixtures anonimizadas, normalizador aislado y sync tolerante a parciales |
| Refactor frontend demasiado grande | Extracción por página/componente, tests antes de mover lógica |
| Idioma rompe URLs o SEO | Estrategia aprobada primero, redirects 301 y canonical/hreflang testados |
| Emails editables introducen XSS/template injection | Variables allowlist, HTML sanitizado y preview |
| Nuevo admin retrasa web comercial | Mantener Inertia y separar backlog; no migrar framework durante features |
| Datos legales no llegan a tiempo | Readiness check y no-go de producción, nunca contenido inventado |
| Métricas interpretadas como audiencia | Glosario público y prohibición de estimaciones no validadas |

## 14. Aprobación propuesta

La aprobación mínima recomendada es:

1. aprobar la estrategia de **mantener admin Inertia**;
2. elegir la estrategia de URLs ES/GL;
3. autorizar la **Fase 1 completa**;
4. identificar responsable de textos legales y datos de marca.

Al terminar Fase 1 se presentará un checkpoint con tests, diff, riesgos restantes y estimación actualizada antes de iniciar el sistema visual.
