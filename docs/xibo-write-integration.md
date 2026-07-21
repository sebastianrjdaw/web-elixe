# Integración de escritura Xibo

Estado: **desactivada**. El repositorio no incluye Swagger de la instalación ni prueba suficiente de endpoints compatibles.

Para habilitarla se requiere: archivar el Swagger y versión; comprobar creación/actualización de display y asignación de tags; probar en una instancia no productiva; añadir una capacidad `send-to-xibo`; solicitar confirmación explícita; usar `internal_code` y `xibo_display_id` como claves idempotentes; registrar respuestas saneadas; distinguir creación, tags y sincronización; y ofrecer reintento solo cuando no pueda duplicar la pantalla.

Nunca deben enviarse tokens al frontend ni guardarse secretos en auditoría. Los errores visibles serán humanos y el detalle técnico quedará en logs protegidos.
