# Flujo del panel administrativo

## Acceso

Solo usuarios con `is_admin=true` pueden entrar. `php artisan elixe:create-admin` crea o actualiza un administrador y genera una contraseña temporal si no se proporciona una. Los usuarios existentes al aplicar la migración conservan acceso; los nuevos usuarios nacen sin privilegios.

## Bandeja de leads

La bandeja permite buscar por nombre, email o teléfono y combinar filtros de tipo, estado, fechas, provincia, municipio, presupuesto, contacto preferido y pantalla seleccionada. La exportación CSV respeta los mismos filtros.

Estados operativos:

```text
nuevo → contactado → cita_agendada → en_estudio → ganado
                                              ↘ perdido
                                                descartado
```

`new` se mantiene por compatibilidad con datos antiguos. Los cambios registran usuario, fecha, valores anterior/nuevo e historial legible en la ficha.

## Ficha y acciones

- Copiar email o teléfono.
- Abrir email, llamada o WhatsApp.
- Cambiar estado.
- Ver preferencias, presupuesto, zona y pantallas.
- Reenviar la notificación interna.
- Enviar una respuesta compatible con tipo e idioma.
- Consultar el historial de creación, estados y emails.

## Pantallas y Xibo

Xibo es solo lectura. Una sincronización manual queda auditada y usa el mismo bloqueo que el scheduler. Los avisos explican qué etiqueta o dato debe corregirse en Xibo. El override local únicamente oculta; mostrar de nuevo vuelve a delegar la decisión en los datos de Xibo.

## Diagnóstico

Diagnósticos comprueba entorno, conexión Xibo, visibilidad del mapa y preparación de producción. Los avisos de producción incluyen textos legales provisionales, HTTP, debug, captcha y plantillas ausentes.
