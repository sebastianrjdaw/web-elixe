# Flujo de preparación de pantallas

Estados: `borrador` → `pendiente_revision` → `aprobado` → `enviado_a_xibo` → `activo`. Los estados alternativos son `error_xibo` y `descartado`.

El usuario crea un borrador en **Admin → Altas de pantalla**, completa establecimiento, ubicación, instalación, publicación y revisión. Puede guardar y corregir mientras esté en borrador o revisión. El envío exige nombre interno y público, dirección, municipio, provincia, coordenadas, tipo, sector y estado comercial. La aprobación registra revisor y fecha.

Actualmente `aprobado` es el final operativo: no escribe en Xibo. Cuando exista integración verificada, el envío deberá guardar el ID remoto, sincronizar, vincular `screen_id`, verificar tags/coordenadas/publicación y conservar idempotencia.
