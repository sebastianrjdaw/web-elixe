# Plan de integración de la plantilla

## Componentes y áreas a adaptar

- `Layout`: cabecera pública sticky, marca, selector ES/GL, modo oscuro persistente, menú móvil y footer.
- `Home`: hero inspirado en la plantilla, propuesta de valor, tarjetas para locales/anunciantes, proceso, red y mapa, FAQ y CTA final.
- `ScreensMap` y `ScreenGrid`: nueva envolvente visual manteniendo Leaflet/OpenStreetMap y los datos reales.
- `ScreensPage`: cabecera comercial, filtro por sector y tipo, selección múltiple persistida y CTA al formulario.
- `AdvicePage`: tarjetas de entrada, formulario por bloques, labels accesibles, feedback de validación y selección de pantallas.
- `Thanks` y `LegalPage`: cabeceras y paneles coherentes con el layout público.

## Assets a copiar

Solo se copiarán tres fotografías JPG a `public/assets/template/`, con nombres semánticos. Se descartan el vídeo, avatares, icon fonts y fotografías no usadas.

## CSS a migrar

Se recrearán en Tailwind y en clases encapsuladas de `@layer components` el hero con overlay, las tarjetas editoriales, botones redondeados, ritmos de sección y transiciones. No se copiará CSS de Bootstrap ni la hoja global de TemplateMo.

## JavaScript a descartar o adaptar

- Descartar jQuery, Bootstrap JS, sticky plugin y click-scroll.
- Implementar menú móvil, modo oscuro y disclosure de FAQ mediante React.
- Mantener Leaflet/React Leaflet y la persistencia de pantallas seleccionadas.
- Integrar Turnstile mediante su API explícita solo cuando esté habilitado.

## Páginas afectadas

Principalmente `/`, `/red-de-pantallas`, `/asesoramiento`, `/gracias`, `/privacidad`, `/cookies` y `/aviso-legal`. `/locales` y `/anunciantes` heredarán el layout y el sistema visual sin alterar sus endpoints. El área `/admin` conservará su layout separado.

## Riesgos y mitigación

- Evitar regresiones funcionales limitando cambios de backend a lo imprescindible.
- Conservar nombres de campos y endpoints del formulario.
- Mantener altura y montaje cliente del mapa.
- Aislar estilos públicos para no afectar las tablas y formularios admin.
- Verificar con build de Vite, pruebas Laravel y listado de rutas; documentar la ausencia de lint si no existe script.
