# Emails y plantillas de respuesta

## Tipos

- Confirmación automática al contacto según `type` y `locale`.
- Notificación interna de nueva solicitud.
- Respuesta comercial seleccionada desde la ficha del lead.
- Resumen diario encolado a las 08:00 (`Europe/Madrid`).

Todas usan el layout HTML de `resources/views/emails/layouts/elixe.blade.php` y se envían por la cola configurada.

## Variables permitidas

```text
{{contact_name}}
{{business_name}}
{{email}}
{{phone}}
{{lead_type}}
```

El renderizador solo sustituye estas variables; no ejecuta Blade ni HTML guardado en base de datos. El cuerpo y los datos del lead se escapan antes de generar HTML, y el asunto elimina saltos de línea.

## Edición

Las plantillas se gestionan en `/admin/response-templates`. La clave de una plantilla existente es inmutable porque las confirmaciones automáticas se resuelven con:

```text
automatic_{venue|advertiser|other}_{es|gl}
```

Desactivar una automática activa el mensaje de respaldo incluido en código. Los seeders crean plantillas ausentes pero nunca sobrescriben cambios del admin.

## Operación

Verifica SMTP, remitente, destinatario interno y worker antes de producción. Una respuesta queda registrada cuando se acepta en la cola; los fallos posteriores se consultan con `php artisan queue:failed`.
