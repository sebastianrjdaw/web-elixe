# Contexto para Codex — Integración de plantilla HTML en Elixe Web

## Objetivo

Voy a proporcionar una plantilla HTML ya diseñada. Puede estar en formato `.zip` o descomprimida dentro de una carpeta llamada:

```text
/template

Codex debe analizar esa plantilla y usarla como referencia visual y funcional para mejorar el diseño de la aplicación Elixe Web, manteniendo toda la lógica ya definida del proyecto.

El objetivo no es copiar archivos sin criterio, sino adaptar el diseño de la plantilla al stack actual del proyecto:

Laravel
React
Inertia.js
TypeScript
Tailwind CSS
Filament Admin
MySQL
Redis
Docker

La aplicación debe quedar fluida, moderna, responsive, usable y sin errores JavaScript.

1. Ubicación de la plantilla

La plantilla estará disponible en una de estas formas:

Opción A — ZIP
/template/template.zip

En este caso, descomprimirla dentro de:

/template/extracted
Opción B — Carpeta ya descomprimida
/template

Puede contener archivos como:

index.html
about.html
contact.html
assets/
css/
js/
images/
fonts/
vendor/

Codex debe inspeccionar la estructura antes de modificar el proyecto.

2. Antes de implementar

Antes de tocar código del proyecto, Codex debe:

Analizar la estructura de /template.
Identificar:
páginas HTML principales;
componentes reutilizables;
estilos CSS;
scripts JS;
imágenes;
fuentes;
iconos;
animaciones;
dependencias externas.
Determinar qué partes son útiles para Elixe Web.
Crear un pequeño informe en:
/docs/template-analysis.md

Ese informe debe incluir:

- estructura encontrada
- assets relevantes
- secciones visuales aprovechables
- dependencias JS/CSS detectadas
- riesgos de integración
- plan para adaptar la plantilla a React/Inertia

No implementar directamente hasta haber analizado.

3. Principio de integración

La plantilla debe ser usada como referencia de diseño, no como una página HTML pegada tal cual.

Hay que convertirla a componentes React/Inertia limpios.

No se debe hacer esto:

copiar index.html entero dentro de un componente React
usar dangerouslySetInnerHTML
mezclar jQuery sin necesidad
duplicar estilos globales conflictivos
romper Tailwind o el layout existente

Sí se debe hacer esto:

extraer estructura visual
adaptar secciones a componentes React
mover assets necesarios a public/ o resources/
convertir clases y estilos a Tailwind cuando sea razonable
mantener funcionalidad Laravel/Inertia existente
eliminar scripts innecesarios
asegurar responsive real
4. Funcionalidad existente que debe conservarse

La integración visual no puede romper las funcionalidades ya definidas para Elixe Web.

Debe mantenerse:

landing pública
red de pantallas
mapa real Leaflet/OpenStreetMap
selección de pantallas
formulario único de asesoramiento
validación de formularios
captcha
emails
leads
admin
CMS
FAQs
textos legales
multidioma ES/GL
modo oscuro
sincronización Xibo

La plantilla solo debe mejorar:

diseño
estructura visual
fluidez
experiencia de usuario
animaciones
responsive
presentación comercial
5. Páginas a rediseñar con la plantilla

Codex debe aplicar la plantilla principalmente a la parte pública.

Páginas prioritarias
/

Landing principal.

/red-de-pantallas

Página de pantallas disponibles.

/asesoramiento

Formulario único.

/gracias

Confirmación tras formulario.

/privacidad
/cookies
/aviso-legal

Páginas legales.

Admin

No aplicar la plantilla pública al panel Filament/Admin salvo que sea necesario para coherencia de marca.
El admin debe seguir siendo funcional y claro.

6. Landing principal

La home debe seguir explicando claramente qué hace Elixe.

Claim principal:

Publicidad local en pantallas reales.

Definición:

Elixe instala y gestiona pantallas digitales en locales para mostrar contenido, promociones y publicidad de forma sencilla.

La plantilla debe adaptarse para construir una landing con estas secciones:

Hero principal
Qué hace Elixe
Para locales
Para anunciantes
Cómo funciona
Red de pantallas en Galicia
Preview del mapa
Preguntas frecuentes
CTA final a asesoramiento
Footer

CTA principal:

Solicitar asesoramiento

CTA secundario:

Ver red de pantallas
7. Página de red de pantallas

La plantilla debe mejorar visualmente esta página, pero mantener la funcionalidad.

Debe contener:

título claro
texto comercial
filtros por sector/tipo
mapa real Leaflet/OpenStreetMap
listado de pantallas
selección de pantallas
CTA a asesoramiento

Importante:

El mapa debe ser real, no placeholder.

Debe usar:

Leaflet + OpenStreetMap

No debe volver a aparecer un fondo simulado con rayas.

Si hay una sola pantalla, el mapa debe verse igualmente como mapa real.

8. Formulario de asesoramiento

La plantilla debe mejorar el formulario para hacerlo más claro y fluido.

El formulario empieza con tarjetas visuales:

Tengo un local
Quiero anunciarme
Tengo otra consulta

Debe conservar la lógica condicional:

venue
advertiser
other

Debe mostrar errores de validación correctamente.

Debe mantener:

campos obligatorios
captcha
privacidad
validación teléfono español
preferencia de contacto
horario preferido
pantallas seleccionadas desde localStorage/sessionStorage

El diseño debe ser:

limpio
claro
fácil en móvil
con pasos visuales si mejora la UX
sin hacerlo demasiado complejo
9. Assets

Codex debe copiar solo los assets necesarios de la plantilla.

Posibles destinos:

public/assets/template
resources/images
resources/css
resources/js

Debe evitar:

copiar assets innecesarios
duplicar librerías
cargar fuentes externas innecesarias
romper Vite
meter archivos enormes sin uso

Si la plantilla usa imágenes decorativas útiles, adaptarlas a Elixe.

La identidad visual debe respetar:

azules
temática mar
diseño claro y luminoso
aspecto SaaS moderno
cercano pero profesional
10. CSS y Tailwind

La prioridad es mantener Tailwind como sistema principal.

Si la plantilla incluye CSS propio:

Analizar si es necesario.
Migrar a Tailwind cuando sea razonable.
Si hay CSS complejo útil, aislarlo para evitar conflictos.
No contaminar estilos globales.
No romper modo oscuro.

Evitar clases globales genéricas problemáticas como:

.container {}
.btn {}
.card {}
.row {}
.col {}

si entran en conflicto con el proyecto.

11. JavaScript de la plantilla

Si la plantilla trae JS propio, Codex debe revisarlo antes de usarlo.

No incorporar scripts si:

dependen de jQuery sin necesidad
manipulan DOM directamente en conflicto con React
generan errores en consola
bloquean navegación Inertia
duplican funcionalidades existentes
no son necesarios

Si hay animaciones útiles, reimplementarlas preferiblemente con React/Tailwind o una librería adecuada.

No debe quedar ningún error en consola del navegador.

12. Animaciones y fluidez

La web debe sentirse moderna y fluida.

Se pueden implementar:

transiciones suaves
hover states
aparición de secciones
contadores animados
scroll suave
microinteracciones
menú móvil fluido
feedback visual en formularios

Pero evitar:

animaciones pesadas
bloqueos
parpadeos
saltos de layout
dependencias innecesarias
13. Responsive

La plantilla integrada debe funcionar perfectamente en:

móvil
tablet
desktop
pantallas grandes

Revisar especialmente:

header
menú móvil
hero
tarjetas
mapa
formulario
footer

El formulario debe ser cómodo en móvil.

El mapa no debe romper el layout.

14. Accesibilidad y UX

Codex debe cuidar:

contraste suficiente
labels en formularios
focus visible
botones accesibles
alt en imágenes
navegación con teclado
mensajes de error claros
estructura semántica

No usar solo color para comunicar estados.

15. Multidioma

La plantilla debe adaptarse al sistema multidioma existente:

español
gallego

No hardcodear textos directamente si ya existe CMS o estructura de traducciones.

Si se crean textos nuevos, deben tener versión ES/GL o quedar preparados para ambas.

16. CMS editable

Si la home ya usa bloques editables desde admin, la plantilla debe integrarse respetando esa estructura.

No convertir todo el contenido en HTML fijo.

Las secciones principales deben poder seguir gestionándose desde admin cuando aplique:

hero
bloque locales
bloque anunciantes
cómo funciona
FAQ
CTA
contacto
legales
17. Estado esperado final

Al terminar, la aplicación debe:

verse moderna y profesional
mantener la identidad de Elixe
ser clara para locales y anunciantes
tener navegación fluida
no mostrar errores JS
no romper Inertia
no romper Tailwind
no romper formularios
no romper admin
no romper sincronización Xibo
no exponer datos internos
funcionar en móvil
funcionar en desktop
18. Validaciones técnicas antes de finalizar

Codex debe ejecutar o dejar preparado:

npm run build
npm run lint
php artisan test
php artisan route:list
php artisan config:clear
php artisan view:clear

Si algún comando no existe, documentar qué falta.

Debe revisar consola del navegador en desarrollo y corregir errores JS.

19. Checklist de aceptación
Diseño
 La home se ve como una web comercial moderna.
 La plantilla ha sido adaptada, no pegada sin control.
 La identidad visual usa azul / mar / estilo luminoso.
 El diseño es responsive.
 El modo oscuro sigue funcionando si ya existe.
 Las animaciones son suaves y no bloquean.
Funcionalidad
 El formulario de asesoramiento sigue funcionando.
 Los errores de validación se muestran correctamente.
 El captcha sigue funcionando.
 Las pantallas seleccionadas se conservan al ir al formulario.
 La red de pantallas sigue cargando datos reales.
 El mapa es un mapa real de Leaflet/OpenStreetMap.
 El admin no se ha roto.
 La sincronización Xibo no se ha visto afectada.
Calidad JS
 No hay errores en consola.
 No hay dependencias JS innecesarias.
 No hay manipulación DOM incompatible con React.
 No hay conflictos con Inertia.
 npm run build funciona.
Seguridad
 No se exponen datos técnicos de Xibo.
 No se exponen secretos.
 No se muestran IDs internos al público.
 Los formularios siguen validados en backend.
20. Orden de trabajo recomendado
Paso 1 — Análisis

Analizar /template y crear:

/docs/template-analysis.md
Paso 2 — Plan de integración

Crear plan breve:

/docs/template-integration-plan.md

Debe indicar:

componentes a crear
assets a copiar
CSS a migrar
JS a descartar o adaptar
páginas afectadas
riesgos
Paso 3 — Layout base

Adaptar:

header
footer
layout público
menú móvil
estructura visual general
Paso 4 — Landing

Rediseñar la landing usando la plantilla como referencia.

Paso 5 — Red de pantallas

Rediseñar visualmente sin romper mapa real.

Paso 6 — Formulario

Rediseñar formulario y tarjetas iniciales.

Paso 7 — Legal / gracias

Adaptar páginas secundarias.

Paso 8 — QA

Ejecutar build, revisar consola, revisar responsive y corregir errores.

21. Instrucción final para Codex

Usa la plantilla ubicada en /template como inspiración visual para mejorar la aplicación Elixe Web.

No copies HTML de forma ciega. Convierte el diseño en componentes React/Inertia mantenibles, integrados con Laravel, Tailwind y el sistema actual.

La prioridad es que la web final sea:

fluida
usable
moderna
comercial
responsive
sin errores JS
sin romper funcionalidades existentes

Mantén siempre la funcionalidad actual de Elixe Web y mejora progresivamente la experiencia de usuario.