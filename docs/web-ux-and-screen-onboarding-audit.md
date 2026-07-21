# Auditoría UX, administración y alta de pantallas

## Stack confirmado

Laravel 10, React 18 con Inertia 2, TypeScript, Tailwind CSS, MySQL, Redis, Docker, colas y scheduler de Laravel, Leaflet/OpenStreetMap y contenido ES/GL. El panel es propio (no Filament). La integración Xibo usa OAuth2 `client_credentials` y consultas de solo lectura.

## Web pública

La home ya presenta el mensaje «Publicidad local en pantallas reales», diferencia locales y anunciantes, explica el proceso, usa métricas obtenidas de la base de datos, muestra una vista previa del mapa, FAQ y CTA. La red ofrece mapa, filtros, selección y envío al asesoramiento. Los payloads públicos usan ULID y no incluyen ID Xibo, IP, MAC, licencia ni información del player. `Screen::publiclyVisible()` exige coordenadas, `web_visible=true`, estado `disponible` y ausencia de ocultación local.

La navegación, el layout responsive, los estados vacíos, la carga diferida del mapa, los metadatos SEO, sitemap, robots y páginas legales ya están presentes. Hay selector ES/GL y tema claro/oscuro. Riesgos pendientes de validación manual: contraste en ambos temas, recorrido completo con lector de pantalla, navegación de teclado del mapa, Core Web Vitals y pruebas visuales en dispositivos reales.

## Administración

El panel dispone de dashboard, leads con historial y plantillas, pantallas sincronizadas, contenido, FAQ, legales, configuración, diagnósticos y auditoría. Antes de este cambio no existía una entidad intermedia para preparar altas: el equipo dependía de corregir datos en Xibo. Los permisos actuales son binarios (`is_admin`); sirven para el MVP, pero conviene migrar a capacidades específicas antes de habilitar escritura.

## Xibo

`XiboService` autentica, pagina y lee `/about`, `/clock`, `/display`, `/tag` y `/displaygroup`. `SyncDisplays` normaliza tags y actualiza la réplica local. No existe `swagger.json` ni documentación versionada que demuestre endpoints de creación/actualización de displays y tags. Por ello la capacidad de escritura real queda **no verificada** y no se implementa. El nuevo flujo termina en `aprobado`, sin llamada remota.

## Decisión

Se aplica el modo seguro: entidad independiente, borrador editable, envío a revisión, aprobación explícita y auditoría. La escritura Xibo solo podrá añadirse tras guardar y revisar el Swagger de la instalación, definir permisos granulares, idempotencia, mocks y rollback operativo.
