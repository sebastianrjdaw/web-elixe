# Análisis de la plantilla HTML

## Estructura encontrada

La plantilla entregada es **TemplateMo 575 Leadership Event**, distribuida en un ZIP de 8,3 MB. Se ha descomprimido en `.template/extracted/templatemo_575_leadership_event/` para su inspección. Contiene una única landing `index.html`, hojas de estilo, scripts, fuentes, fotografías y un vídeo de fondo.

- `index.html`: navegación anclada y secciones hero, destacados, presentación, ponentes, agenda, precios, ubicación, contacto y footer.
- `css/`: Bootstrap 5, Bootstrap Icons y una hoja propia de unas 20 KB.
- `js/`: Bootstrap, jQuery, sticky nav, scroll por anclas y un script de navegación.
- `images/`: retratos, escenas de presentaciones y fotografías de auditorios.
- `videos/`: vídeo MP4 de fondo de 6,5 MB.
- `fonts/`: tipografía de iconos de Bootstrap.

## Assets relevantes

La composición de algunas fotografías y el tratamiento de imagen con overlays son aprovechables para comunicar alcance y contenido en pantallas. Los recursos más adecuados son:

- `images/terren-hurst-blgOFmPIlr0-unsplash.jpg`, para el hero con tratamiento oscuro.
- `images/schedule/business-woman-making-presentation-office.jpg`, para el bloque orientado a locales.
- `images/schedule/jason-goodman-bzqU01v-G54-unsplash.jpg`, para explicar el proceso de acompañamiento.

No se usarán el vídeo, los retratos, las fuentes de iconos ni el resto de fotografías. Los iconos de interfaz seguirán procediendo de `lucide-react`.

## Secciones visuales aprovechables

- Cabecera oscura, compacta y superpuesta al hero.
- Hero inmersivo con fondo fotográfico, overlay azul y claim destacado mediante subrayado de color.
- Tarjetas visuales con imagen, gradiente y contenido superpuesto.
- Alternancia de fondos claros azulados y blancos.
- Bloques de proceso con lectura secuencial.
- CTA final de alto contraste y footer de marca.

## Dependencias CSS y JS detectadas

- Bootstrap 5.1.3.
- Bootstrap Icons.
- Google Fonts (`DM Sans`).
- jQuery.
- Plugins de sticky navigation y scroll por anclas.
- JavaScript de Bootstrap.

Ninguna de estas dependencias se incorporará. El proyecto ya dispone de React, Inertia, Tailwind, Lucide y Leaflet; las interacciones se implementarán con estado React y CSS local en las capas de Tailwind.

## Riesgos de integración

- Bootstrap y su CSS global entrarían en conflicto con Tailwind y el admin.
- jQuery y los scripts que manipulan el DOM no son compatibles con el ciclo de vida de React/Inertia.
- El vídeo penaliza carga, consumo móvil y accesibilidad.
- Los textos y la semántica de evento no encajan con Elixe.
- El contraste y el menú móvil de la plantilla requieren una implementación accesible propia.
- Leaflet necesita conservar una altura explícita y no debe montarse antes de disponer del DOM.
- El contenido CMS y los payloads bilingües deben seguir siendo la fuente de los bloques editables.

## Adaptación a React/Inertia

Se traducirá el lenguaje visual a componentes existentes y nuevos del frontend: layout público responsive, marca, hero, tarjetas de audiencia, proceso, mapa, FAQ, CTA y footer. La home conservará los bloques CMS; red de pantallas conservará filtros, Leaflet y selección persistida; asesoramiento conservará su formulario condicional, validación y captcha. Las páginas de gracias y legales compartirán el nuevo layout. El admin permanecerá aislado visual y funcionalmente.
