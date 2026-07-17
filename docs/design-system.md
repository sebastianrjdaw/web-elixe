# Sistema de diseño de Elixe

## Dirección visual

El sistema combina fondos claros, azul marino, cian y referencias discretas al mar. La interfaz pública admite tema claro/oscuro; el admin prioriza densidad, lectura y velocidad operativa.

## Fundamentos

- Azul marino (`slate-950`/`sky-950`): cabeceras, hero y contraste de marca.
- Cian (`cyan-400`): acción principal y acentos.
- Azul (`sky-700`): enlaces y acciones secundarias.
- Blanco y grises `slate`: superficies, texto y divisores.
- Verde, ámbar y rojo: éxito, advertencia y error; nunca se usan como único indicador sin texto.
- Radio habitual: 8–16 px; botones principales en formato píldora cuando son CTA comerciales.
- Ancho de contenido: `max-w-7xl` en web y `max-w-6xl` en admin.

## Componentes vigentes

- `Layout`: cabecera, navegación responsive, selector ES/GL, tema, avisos y footer.
- `AdminLayout`: navegación lateral/escritorio, navegación horizontal/móvil y feedback.
- `Brand`, `Seo`, `Badge`, `Stat`, `Pagination`.
- `ScreenGrid`, `ScreensMap`: tarjetas seleccionables, estado vacío y mapa cargado de forma diferida.
- `TurnstileWidget`: captcha explícito con renovación de token.
- Formularios: `input`, `check`, `field-error`, `input-error`, `form-panel`.
- Superficies: `panel`, `feature-card`, `audience-card`, `faq-card`, `legal-card`.

## Reglas de interacción

1. Toda acción debe ser teclado-operable y tener etiqueta visible o `aria-label`.
2. Los errores se muestran junto al campo y el backend sigue siendo la fuente de verdad.
3. Botones en proceso quedan deshabilitados y cambian su texto cuando aplica.
4. Selecciones de pantallas usan `aria-pressed`, resumen persistente y sesión temporal.
5. Confirmaciones destructivas o de visibilidad requieren confirmación explícita.
6. Estados vacíos y advertencias explican el siguiente paso; no muestran métricas inventadas.

## Evolución

El frontend aún concentra varias páginas en `resources/js/app.tsx`. Los nuevos componentes deben extraerse por dominio cuando exista una modificación funcional real, evitando una reescritura masiva. El mapa ya es un chunk independiente para reducir la carga inicial.
