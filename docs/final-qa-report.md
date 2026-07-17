# Informe final de QA funcional

Fecha: 17 de julio de 2026

## Automatización

Matriz ejecutada tras la implementación:

- PHPUnit: **31 pruebas y 144 aserciones** sobre flujos públicos, permisos, CRM, CMS, diagnóstico, API, Xibo y emails.
- TypeScript estricto: `npm run typecheck`.
- ESLint 10: `npm run lint`.
- Vite 8: build de producción y carga diferida del mapa.
- Laravel Pint: formato del backend.
- Composer audit y npm audit: sin vulnerabilidades conocidas en las dependencias instaladas.
- Docker: Compose válido e imagen PHP construida desde `composer.lock`.

## Casos críticos cubiertos

- Alta única ante reenvío con el mismo token.
- Validación del teléfono, privacidad, campos condicionales y Turnstile.
- Selección únicamente de pantallas públicas mediante ULID.
- Persistencia temporal de selección y redirección ES/GL.
- Acceso administrativo por rol.
- Cambio de estado, auditoría, respuesta por plantilla e historial.
- Paginación Xibo, bloqueo, limpieza de tags y retirada de pantallas ausentes.
- API pública sin claves de base de datos ni datos técnicos.
- Sitemap con URLs españolas y gallegas.

## Verificación manual de entrega

Se ejecutó un smoke test con Firefox headless sobre español y gallego: título dinámico, `lang`, canonical, navegación, contenido comercial, carga diferida de Leaflet, selección de tipo y ausencia de desbordamiento horizontal. No se observaron errores JavaScript propios de la aplicación.

Debe repetirse en el dominio de staging con credenciales reales: captcha, entrega SMTP, conexión Xibo, responsive en dispositivos físicos y navegación completa mediante teclado. El diagnóstico administrativo centraliza los bloqueos de producción.

## Pendientes externos

- Textos legales y datos fiscales definitivos.
- Dominio HTTPS final.
- Credenciales SMTP, Xibo y Turnstile.
- Logo/contacto definitivos para web y email.
- Aprobación comercial de taxonomías y criterios de métricas.

No se implementaron pagos, contratación automática, subida de creatividades ni Proof of Play no verificable.
