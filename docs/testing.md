# Testing

Priority tests:

- Advice form creates a lead.
- Privacy acceptance is required.
- Spanish phone validation rejects invalid numbers.
- Internal lead email is queued.
- User confirmation email is queued.
- Admin can change lead status and audit the action.
- Xibo sync creates and updates screens.
- Xibo tags are trimmed.
- Public screens endpoint does not expose sensitive fields.
- Local visibility override hides a screen publicly.
- Admin screen override is audited.
- Content seeders create CMS, FAQ, legal and setting records.
- Admin can change lead receiver email and audit the action.

Run tests in Docker:

```bash
docker compose exec app php artisan test
```

Los tests usan SQLite en memoria mediante `phpunit.xml`. Esto evita que `RefreshDatabase` borre la base MySQL de desarrollo.

Si el usuario admin local desaparece tras una recreacion de volumen o una limpieza manual:

```bash
docker compose exec app php artisan elixe:create-admin admin@elixe.es --name="Admin Elixe"
```

El comando muestra una contraseña temporal aleatoria. Guárdala en un gestor de secretos y cámbiala si el entorno deja de ser estrictamente local.
