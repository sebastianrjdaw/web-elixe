# Elixe Web — Auditoría y profesionalización integral

## Propósito

Este documento debe usarse como contexto operativo para Codex. Su misión es analizar la estructura actual de `elixe-web`, detectar problemas y aplicar mejoras visuales, funcionales y operativas sin rehacer el proyecto sin criterio.

El objetivo es que Elixe se presente como una empresa gallega en crecimiento, cercana y profesional, con una web fluida, usable, creíble y preparada para escalar.

---

# 1. Contexto de negocio

Elixe instala y gestiona pantallas digitales en establecimientos físicos para mostrar:

- contenido propio del local;
- promociones;
- menús, avisos y eventos;
- publicidad de empresas locales;
- campañas segmentadas por zona y tipo de establecimiento.

La empresa conecta dos públicos:

## Establecimientos

Locales que ya tienen una pantalla o quieren instalar una, mejorar su comunicación y generar ingresos adicionales con publicidad gestionada.

## Anunciantes

Empresas que desean aparecer en pantallas reales, seleccionar zonas, tipos de locales o pantallas concretas y recibir una propuesta personalizada.

## Posicionamiento

Claim:

> Publicidad local en pantallas reales.

Definición:

> Elixe instala y gestiona pantallas digitales en locales para mostrar contenido, promociones y publicidad de forma sencilla.

La web debe transmitir cercanía, profesionalidad, tecnología, crecimiento y conocimiento del mercado local. No debe aparentar una cobertura mayor de la real ni usar métricas inventadas.

---

# 2. Stack esperado

Codex debe confirmar el stack real antes de modificarlo:

- Laravel;
- React;
- Inertia.js;
- TypeScript;
- Tailwind CSS;
- Filament;
- MySQL;
- Redis;
- Docker;
- Laravel Queue;
- Laravel Scheduler;
- Xibo API en modo lectura;
- Leaflet y OpenStreetMap;
- español y gallego;
- modo claro y oscuro.

---

# 3. Primera tarea obligatoria: auditoría

Antes de realizar cambios, analizar todo el repositorio y crear:

```text
/docs/current-state-audit.md
```

La auditoría debe incluir:

## Arquitectura

- versiones;
- estructura de carpetas;
- rutas;
- modelos;
- migraciones;
- servicios;
- jobs;
- mailables;
- panel admin;
- integración Xibo;
- configuración Docker;
- Redis y colas;
- Scheduler;
- sistema multidioma;
- CMS;
- mapa;
- tests.

## Estado funcional

Comprobar:

- home;
- navegación;
- secciones para locales y anunciantes;
- red de pantallas;
- mapa real;
- filtros;
- selección de pantallas;
- formulario;
- validaciones;
- captcha;
- emails;
- leads;
- admin;
- sincronización Xibo;
- FAQs;
- legales;
- multidioma;
- modo oscuro.

## Calidad frontend

Revisar:

- errores de consola;
- warnings de React;
- navegación Inertia;
- componentes duplicados;
- CSS conflictivo;
- responsive;
- accesibilidad;
- rendimiento;
- imágenes;
- saltos de layout;
- loaders;
- estados vacíos;
- UX móvil.

## Calidad backend

Revisar:

- Form Requests;
- controladores;
- servicios;
- seguridad;
- queries;
- caché;
- colas;
- auditoría;
- idempotencia del sync;
- exposición de datos internos;
- cobertura de tests.

Cada problema debe clasificarse como crítico, alto, medio o bajo e incluir solución, esfuerzo y prioridad.

---

# 4. Plan de profesionalización

Crear:

```text
/docs/professionalization-plan.md
```

Fases:

1. Estabilidad y errores.
2. Sistema visual.
3. Web comercial.
4. Formularios y conversión.
5. Panel administrativo.
6. Emails y plantillas.
7. QA, tests y documentación.

No realizar una refactorización masiva antes de aprobar el plan.

---

# 5. Profesionalización visual

## Identidad

La estética debe inspirarse en:

- azules;
- mar;
- costa;
- Valdoviño;
- fondos claros;
- blancos;
- detalles tecnológicos discretos.

Debe ser moderna, luminosa, SaaS y cercana. Evitar un diseño oscuro, agresivo o excesivamente corporativo.

## Sistema de diseño

Normalizar:

- colores;
- tipografías;
- escalas de espaciado;
- botones;
- tarjetas;
- formularios;
- badges;
- alertas;
- loaders;
- estados vacíos;
- focus;
- modo oscuro.

Crear componentes reutilizables:

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

No duplicar estilos o markup.

---

# 6. Estructura recomendada de la home

1. Hero.
2. Explicación de Elixe.
3. Bloque para locales.
4. Bloque para anunciantes.
5. Cómo funciona.
6. Datos reales de la red.
7. Preview del mapa.
8. Control de contenidos y publicidad.
9. FAQs.
10. CTA final.

## Hero

Título:

> Publicidad local en pantallas reales.

Subtítulo:

> Elixe instala y gestiona pantallas digitales en establecimientos de Galicia para mostrar contenido, promociones y publicidad de forma sencilla.

CTAs:

```text
Quiero una pantalla en mi local
Quiero anunciarme
Solicitar asesoramiento
```

## Datos de confianza

Mostrar únicamente cifras reales:

- pantallas gestionadas;
- municipios;
- sectores;
- red en crecimiento.

No inventar impresiones, audiencia ni ingresos.

---

# 7. Recorridos separados

## Para locales

Comunicar:

- contenido propio;
- promociones;
- menús;
- avisos;
- instalación;
- mantenimiento;
- gestión técnica;
- ingresos adicionales;
- control sobre categorías publicitarias.

Pasos:

```text
1. Cuéntanos cómo es tu local.
2. Evaluamos la pantalla y la ubicación.
3. Configuramos y gestionamos el sistema.
4. Muestras contenido propio y publicidad autorizada.
```

## Para anunciantes

Comunicar:

- publicidad local;
- zonas;
- tipos de locales;
- pantallas concretas;
- propuesta personalizada;
- gestión de campaña;
- asesoramiento.

Pasos:

```text
1. Elige zona, sector o pantallas.
2. Cuéntanos el objetivo.
3. Recibe una propuesta.
4. Elixe prepara y activa la campaña.
```

---

# 8. Mejoras inspiradas en la competencia

Adaptar, sin copiar diseños ni textos:

## Mapa como planificador

Debe permitir:

- filtrar;
- ver resultados;
- seleccionar;
- deseleccionar;
- guardar selección temporal;
- mostrar resumen;
- ir al formulario.

## Solicitud guiada

El formulario de anunciante debe sentirse como una solicitud de campaña:

```text
Objetivo
Zona
Presupuesto
Contacto
```

## Control de publicidad

Explicar que:

- Elixe revisa los anuncios;
- el local puede indicar categorías no permitidas;
- el local conserva espacio para contenido propio;
- la programación está gestionada.

## Servicios creativos

Dejar preparada una sección para diseño o adaptación de imágenes y vídeos, sin afirmar que está incluido si no se ha definido.

## Media kit

Preparar un CTA futuro:

```text
Solicitar dossier comercial
```

## Proof of Play

Documentar para una fase futura:

- número de reproducciones;
- pantallas;
- fechas;
- duración;
- informe para anunciantes.

No implementar datos no fiables.

---

# 9. Página de pantallas

Debe incluir:

- mapa real Leaflet/OpenStreetMap;
- filtros por sector y tipo;
- listado o tarjetas;
- selección de pantallas;
- CTA;
- loader;
- estado vacío;
- responsive.

Texto recomendado:

> Consulta las pantallas disponibles para campañas locales en Galicia.

Datos públicos:

- nombre del local;
- municipio;
- provincia;
- tipo;
- sector;
- disponibilidad.

No mostrar IDs, códigos internos, IP, MAC, licencia, estado técnico ni datos de Xibo.

Con una red pequeña:

- ajustar zoom;
- explicar que está creciendo;
- no exagerar;
- destacar ubicaciones verificadas.

---

# 10. Formularios

Tipos:

```text
venue
advertiser
other
```

Tarjetas:

```text
Tengo un local
Quiero anunciarme
Tengo otra consulta
```

Los formularios deben:

- ser responsive;
- conservar datos;
- mostrar errores;
- bloquear doble envío;
- usar captcha;
- validar privacidad;
- validar teléfono;
- tener feedback claro;
- crear lead;
- enviar emails por queue;
- mostrar página de gracias.

Backend Laravel es la fuente principal de validación.

---

# 11. Mejora del panel admin

El admin debe ser una herramienta operativa.

## Dashboard

Mostrar:

- leads nuevos;
- pendientes;
- esta semana;
- por tipo;
- pantallas visibles;
- pantallas incompletas;
- último sync;
- acciones rápidas.

## Bandeja de solicitudes

Columnas:

- tipo;
- nombre;
- empresa/local;
- teléfono;
- email;
- municipio;
- estado;
- fecha;
- última acción.

Filtros:

- estado;
- tipo;
- fecha;
- provincia;
- municipio;
- presupuesto;
- preferencia de contacto;
- pantallas seleccionadas.

## Flujo

```text
nuevo
contactado
cita_agendada
en_estudio
ganado
perdido
descartado
```

Acciones rápidas:

- copiar email;
- copiar teléfono;
- abrir llamada;
- abrir WhatsApp;
- cambiar estado;
- enviar respuesta;
- reenviar;
- exportar;
- ver historial;
- ver pantallas seleccionadas.

## Historial

Registrar usuario, fecha y acción para:

- cambio de estado;
- email enviado;
- reenvío;
- exportación;
- cambio de configuración;
- sincronización;
- visibilidad de pantalla.

## Pantallas

Mostrar:

- nombre;
- municipio;
- provincia;
- tipo;
- sector;
- visibilidad;
- estado comercial;
- sincronización;
- advertencias.

Si faltan datos, indicar qué corregir en Xibo.

---

# 12. Plantillas de respuesta

Crear un sistema de plantillas editables.

Tabla sugerida:

```text
response_templates
```

Campos:

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

Plantillas:

- recepción de local;
- recepción de anunciante;
- solicitud de información;
- propuesta de llamada;
- confirmación de cita;
- seguimiento;
- solicitud no viable;
- agradecimiento.

---

# 13. Plantilla HTML profesional de email

Crear:

```text
resources/views/emails/layouts/elixe.blade.php
```

Ejemplo base:

```html
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>{{ $subject ?? 'Equipo Elixe' }}</title>
</head>
<body style="margin:0;background:#f3f7fb;font-family:Arial,Helvetica,sans-serif;color:#18324a;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:24px 12px;">
<tr><td align="center">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#fff;border-radius:16px;overflow:hidden;border:1px solid #dce8f2;">
<tr>
<td style="padding:28px 32px;background:#0f4c81;color:#fff;">
    <div style="font-size:24px;font-weight:700;">Elixe</div>
    <div style="margin-top:6px;font-size:14px;">Publicidad local en pantallas reales.</div>
</td>
</tr>
<tr>
<td style="padding:32px;">
    <h1 style="margin:0 0 18px;font-size:24px;color:#17324d;">{{ $heading }}</h1>
    <div style="font-size:16px;line-height:1.7;color:#405a70;">{!! $content !!}</div>

    @if(!empty($actionUrl) && !empty($actionText))
    <table role="presentation" cellspacing="0" cellpadding="0" style="margin-top:28px;">
    <tr><td style="border-radius:10px;background:#0f6ca8;">
        <a href="{{ $actionUrl }}" style="display:inline-block;padding:13px 22px;color:#fff;text-decoration:none;font-weight:700;">
            {{ $actionText }}
        </a>
    </td></tr>
    </table>
    @endif
</td>
</tr>
<tr>
<td style="padding:22px 32px;background:#eef6fb;color:#60788d;font-size:13px;line-height:1.6;">
    <strong>Equipo Elixe</strong><br>
    Gestión de pantallas digitales y publicidad local en Galicia.
</td>
</tr>
</table>
</td></tr>
</table>
</body>
</html>
```

Adaptar logo, colores, contacto, idioma y compatibilidad.

---

# 14. Ejemplos de respuesta

## Local

Asunto:

```text
Hemos recibido la solicitud de tu local
```

Cuerpo:

> Hola, {{ contact_name }}:
>
> Gracias por contactar con Elixe. Hemos recibido la información de {{ business_name }} y nuestro equipo la revisará para valorar la opción más adecuada para tu establecimiento.
>
> Tendremos en cuenta si ya dispones de pantalla, el tipo de local y la ubicación. Nos pondremos en contacto contigo para explicarte cómo gestionamos la instalación, el contenido propio y la publicidad.
>
> Un saludo,
>
> Equipo Elixe

## Anunciante

Asunto:

```text
Hemos recibido tu solicitud de campaña
```

Cuerpo:

> Hola, {{ contact_name }}:
>
> Gracias por pensar en Elixe para dar visibilidad a {{ company_name }}.
>
> Revisaremos las zonas, tipos de establecimientos y pantallas seleccionadas para preparar una propuesta adaptada.
>
> Nuestro equipo se pondrá en contacto contigo para conocer mejor el objetivo de la campaña.
>
> Un saludo,
>
> Equipo Elixe

---

# 15. CMS y multidioma

Revisar si los contenidos están hardcodeados, en traducciones o en base de datos.

Mantener:

```text
es
gl
```

Debe haber coherencia en:

- web;
- formularios;
- errores;
- FAQs;
- legales;
- emails;
- selector de idioma.

No crear un CMS excesivamente genérico.

---

# 16. Rendimiento, SEO y accesibilidad

## Rendimiento

Revisar:

- bundle;
- imágenes;
- mapa;
- lazy loading;
- cache;
- Redis;
- consultas;
- colas;
- fuentes.

## SEO

Revisar:

- títulos;
- meta descriptions;
- Open Graph;
- favicon;
- canonical;
- sitemap;
- robots;
- semántica;
- encabezados;
- alt;
- URLs.

## Accesibilidad

Comprobar:

- teclado;
- focus;
- labels;
- contraste;
- errores;
- botones;
- menús;
- modo oscuro;
- idioma del documento.

---

# 17. QA

Ejecutar:

```bash
php artisan test
npm run build
npm run lint
php artisan route:list
php artisan config:clear
php artisan cache:clear
```

Verificar manualmente:

- home;
- móvil;
- navegación;
- mapa;
- filtros;
- selección;
- formulario;
- captcha;
- errores;
- página de gracias;
- email;
- admin;
- estados;
- plantillas;
- exportación;
- sincronización.

No debe quedar ningún error JavaScript en consola.

---

# 18. Entregables

Crear o actualizar:

```text
/docs/current-state-audit.md
/docs/professionalization-plan.md
/docs/design-system.md
/docs/admin-workflow.md
/docs/email-templates.md
/docs/final-qa-report.md
README.md
.env.example
```

Entregar también:

- código;
- migraciones;
- seeders;
- tests;
- plantillas;
- instrucciones;
- decisiones;
- tareas pendientes.

---

# 19. Restricciones

No:

- reescribir todo sin justificar;
- eliminar funcionalidad;
- modificar Xibo;
- exponer datos internos;
- inventar cobertura o audiencia;
- añadir pagos;
- añadir contratación automática;
- añadir dependencias innecesarias;
- copiar HTML sin convertirlo;
- introducir errores React/Inertia;
- romper el admin.

---

# 20. Definición de terminado

El trabajo estará terminado cuando:

- la web explique claramente qué hace Elixe;
- existan recorridos diferenciados;
- el diseño sea profesional y cercano;
- la cobertura real se presente con honestidad;
- el mapa sea funcional;
- los formularios sean claros;
- los leads se gestionen con rapidez;
- existan plantillas de respuesta;
- el admin tenga un flujo eficiente;
- no haya errores JS;
- el proyecto sea responsive;
- los tests pasen;
- la documentación esté actualizada.

---

# 21. Instrucción final para Codex

Analiza primero el repositorio real de `elixe-web`.

No asumas que todo este contexto está implementado. Contrasta requisito por requisito con el código.

Trabaja en este orden:

```text
auditoría
→ plan
→ estabilidad
→ sistema visual
→ web comercial
→ formularios
→ admin
→ emails
→ QA
```

El objetivo es que Elixe deje de parecer un proyecto experimental y se presente como una empresa local en crecimiento con una ejecución sólida, cuidada, creíble y preparada para escalar.
