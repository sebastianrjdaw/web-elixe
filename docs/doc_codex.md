# Elixe Web — Contexto completo para Codex

## 1. Nombre del proyecto

**Repositorio / proyecto técnico:** `elixe-web`

**Producto:** Web comercial y plataforma interna básica para Elixe.

**Empresa:** Elixe.

**Ámbito inicial:** Galicia.

---

# 2. Objetivo general

Crear una aplicación web moderna para **Elixe**, empresa que instala y gestiona pantallas digitales en locales físicos para mostrar contenido, promociones y publicidad.

La web debe servir principalmente para:

1. Explicar claramente qué hace Elixe.
2. Captar locales interesados en instalar o activar pantallas.
3. Captar anunciantes interesados en aparecer en pantallas reales.
4. Mostrar de forma secundaria una red de pantallas disponibles.
5. Sincronizar pantallas desde Xibo mediante API.
6. Guardar leads en base de datos.
7. Enviar leads por email al equipo de Elixe.
8. Permitir una administración interna robusta para gestionar leads, contenidos, FAQs, configuración y sincronización.

La primera versión **no debe vender campañas online**, **no debe permitir pagos**, **no debe permitir subida de creatividades por anunciantes** y **no debe modificar Xibo**. Xibo se usará solo como fuente de datos de lectura.

---

# 3. Mensaje comercial principal

## Claim principal

**Publicidad local en pantallas reales.**

## Definición corta de Elixe

Elixe instala y gestiona pantallas digitales en locales para mostrar contenido, promociones y publicidad de forma sencilla.

## Tono de comunicación

El tono debe ser:

* cercano;
* comercial;
* profesional;
* claro;
* local;
* moderno.

Evitar un tono demasiado técnico o demasiado agresivo.

Evitar términos como:

* “monetizar”;
* “hazte rico”;
* promesas exageradas de ingresos;
* publicidad programática;
* DOOH como término principal.

Se puede hablar de ingresos, pero con prudencia.

Ejemplo:

> Genera ingresos con tu pantalla mostrando publicidad gestionada por Elixe.

---

# 4. Público objetivo

## 4.1 Locales / establecimientos

Locales físicos de Galicia que:

* ya tienen una pantalla;
* quieren instalar una pantalla;
* quieren mostrar contenido propio;
* quieren generar ingresos adicionales con publicidad;
* quieren que Elixe se encargue de la parte técnica.

Ejemplos actuales:

* bares;
* lavanderías;
* restaurantes;
* cafeterías;
* comercios;
* servicios locales.

## 4.2 Anunciantes / empresas

Empresas que quieren anunciarse en pantallas ubicadas en locales reales.

El mensaje para anunciantes debe centrarse en:

* llegar a clientes locales;
* ampliar horizontes de visibilidad;
* aparecer en zonas concretas;
* aparecer en tipos concretos de locales;
* recibir asesoramiento personalizado;
* solicitar propuesta bajo demanda.

---

# 5. Stack técnico requerido

Usar:

* Laravel;
* React;
* Inertia.js;
* TypeScript;
* Tailwind CSS;
* MySQL;
* Redis;
* Docker;
* Laravel Queue;
* Laravel Scheduler;
* Laravel Mail;
* Laravel HTTP Client;
* Laravel Storage;
* Laravel Starter Kit o Breeze con React;
* Filament para admin si resulta la opción más robusta y rápida.

## Decisiones técnicas

* El proyecto debe arrancar en Linux.
* Docker debe prepararse para desarrollo desde el inicio.
* Debe quedar documentado el despliegue futuro en VPS con Docker Compose.
* MySQL será la base de datos.
* Redis se usará para colas y cache.
* El admin debe ser robusto, preferiblemente con Filament.
* El frontend público debe usar React/Inertia.
* No crear una SPA separada.
* El backend no debe exponer credenciales de Xibo al frontend.
* Xibo se consulta únicamente desde Laravel.

---

# 6. Docker

Preparar entorno de desarrollo con Docker.

Servicios esperados:

* `app` / PHP-FPM;
* `nginx`;
* `mysql`;
* `redis`;
* `queue`;
* `scheduler`;
* `node` / Vite;
* `mailpit` o alternativa para desarrollo de emails.

Debe existir documentación para:

```bash
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan migrate --seed
docker compose exec app npm install
docker compose exec app npm run dev
```

También documentar una base para despliegue en VPS con Docker Compose, aunque el foco inicial sea desarrollo.

---

# 7. Arquitectura general

```text
Usuario público
    ↓
Laravel + Inertia + React
    ↓
Base de datos propia
    ↓
Admin / CMS / CRM
    ↓
Xibo API solo lectura
```

Xibo no debe usarse como base de datos pública.
La aplicación debe sincronizar la información necesaria hacia su propia base de datos.

---

# 8. Integración con Xibo

## 8.1 Modo de integración

Xibo se usará mediante API REST con OAuth `client_credentials`.

La app Laravel debe tener un servicio:

```php
App\Services\Xibo\XiboService
```

Responsabilidades:

* obtener token OAuth;
* cachear token hasta expiración;
* consultar pantallas;
* normalizar datos;
* normalizar valores de tags con `trim()`;
* manejar errores de API;
* no llamar nunca a Xibo desde frontend.

## 8.2 Variables de entorno

```env
XIBO_CMS_URL=https://cms.elixe.es
XIBO_BASE_URL="${XIBO_CMS_URL}/api"
XIBO_CLIENT_ID=
XIBO_CLIENT_SECRET=
XIBO_TIMEOUT=20
```

## 8.3 Endpoints Xibo conocidos

```http
POST /api/authorize/access_token
GET /api/about
GET /api/clock
GET /api/display?start=0&length=100
GET /api/tag?start=0&length=100
GET /api/displaygroup?start=0&length=100
GET /api/schedule?start=0&length=100
GET /api/campaign?start=0&length=100
GET /api/layout?start=0&length=100
GET /api/library?start=0&length=100
GET /api/stats
```

Para el MVP son imprescindibles:

```http
POST /api/authorize/access_token
GET /api/display?start=0&length=100
GET /api/tag?start=0&length=100
GET /api/about
GET /api/clock
```

## 8.4 Alcance Xibo MVP

En el MVP:

* no crear campañas;
* no crear layouts;
* no subir creatividades;
* no modificar pantallas;
* no modificar tags en Xibo;
* no programar eventos;
* solo lectura.

---

# 9. Sincronización Xibo

## 9.1 Frecuencia

La sincronización automática debe ejecutarse **2 o 3 veces al día**.

También debe existir sincronización manual desde admin.

## 9.2 Comando Artisan

Crear comando:

```bash
php artisan xibo:sync-displays
```

Debe:

1. obtener displays desde Xibo;
2. leer tags relevantes;
3. actualizar o crear pantallas locales;
4. registrar logs de sincronización;
5. avisar en admin si faltan campos importantes;
6. no mostrar datos técnicos en público.

## 9.3 Botón admin

En admin debe existir botón:

```text
Sincronizar ahora
```

Debe requerir confirmación antes de ejecutarse.

---

# 10. Tags Xibo utilizadas

Xibo será la fuente principal para estos valores en MVP.

## 10.1 `loc_tipo`

Valores internos:

```text
bar
restaurante
cafeteria
lavanderia
gimnasio
peluqueria
clinica
tienda
hotel
supermercado
oficina
centro_comercial
farmacia
autoescuela
estanco
panaderia
coworking
otro
```

Valores públicos:

```text
Bar
Restaurante
Cafetería
Lavandería
Gimnasio
Peluquería
Clínica
Tienda
Hotel
Supermercado
Oficina
Centro comercial
Farmacia
Autoescuela
Estanco
Panadería
Coworking
Otro
```

## 10.2 `loc_sector`

Valores internos:

```text
hosteleria
servicios
salud
retail
ocio
turismo
empresa
transporte
educacion
otro
```

## 10.3 `web_visible`

Valores:

```text
true
false
```

## 10.4 `com_estado`

Valores:

```text
disponible
limitado
completo
pausado
mantenimiento
privado
```

En la web pública del MVP solo se mostrarán pantallas con:

```text
com_estado = disponible
```

---

# 11. Ejemplo real de JSON Xibo

Este es un ejemplo representativo recibido desde:

```http
GET /api/display?start=0&length=100
```

```json
{
  "displayId": 4,
  "address": "C-646,, C-646, 46, 15550 Valdoviño, A Coruña",
  "display": "ELIXE-002",
  "description": "Casa Marino",
  "licensed": 1,
  "loggedIn": 1,
  "latitude": 43.583251325335,
  "longitude": -8.1820912436517,
  "displayGroupId": 4,
  "timeZone": "Europe/Madrid",
  "tags": [
    {
      "tag": "loc_tipo",
      "tagId": 6,
      "value": "bar"
    },
    {
      "tag": "loc_sector",
      "tagId": 7,
      "value": "hosteleria"
    }
  ],
  "createdDt": "2026-04-22 15:23:32",
  "modifiedDt": "2026-07-07 21:38:22"
}
```

Importante: el JSON real incluye muchos más campos técnicos. No deben almacenarse ni mostrarse públicamente si no son necesarios.

---

# 12. Datos sensibles de Xibo

No mostrar nunca en frontend público:

```text
license
clientAddress
lanIpAddress
macAddress
currentMacAddress
xmrChannel
xmrPubKey
newCmsAddress
newCmsKey
teamViewerSerial
webkeySerial
storageAvailableSpace
storageTotalSpace
clientVersion
osVersion
deviceName
```

La aplicación puede usar `displayId` internamente, pero nunca debe mostrar IDs técnicos, códigos ni identificadores Xibo al público.

---

# 13. Modelo de pantalla local

La app debe guardar solo los datos necesarios y limpios.

## Tabla `screens`

Campos recomendados:

```text
id
xibo_display_id
internal_name
public_name
address
municipality
province
latitude
longitude
location_type
location_sector
web_visible_from_xibo
commercial_status
local_visibility_override
is_visible_publicly
logged_in
synced_at
created_at
updated_at
```

## Notas

* `xibo_display_id` es interno.
* `internal_name` puede venir de `display`, por ejemplo `ELIXE-002`.
* `public_name` puede venir de `description`, por ejemplo `Casa Marino`.
* En público se mostrará `public_name`, municipio y provincia.
* No se mostrará `internal_name`.
* No se mostrará `xibo_display_id`.
* No se mostrará dirección exacta como dato principal.
* Si faltan latitud o longitud, la pantalla no aparecerá en mapa público.
* Si faltan datos relevantes, debe aparecer aviso en admin para que se corrijan en Xibo.

## Regla pública

Una pantalla será pública si:

```text
web_visible_from_xibo = true
commercial_status = disponible
latitude no vacío
longitude no vacío
no existe override local que la oculte
```

## Override local

Desde admin se podrá ocultar una pantalla de la web mediante override local.

Esto:

* no modifica Xibo;
* requiere confirmación;
* queda auditado;
* manda sobre Xibo para la web pública.

Ejemplo:

```text
Xibo web_visible = true
Admin local ocultó pantalla
Resultado público = oculta
```

---

# 14. Pantallas sin datos completos

Si una pantalla no tiene:

* latitud;
* longitud;
* `loc_tipo`;
* `loc_sector`;
* `web_visible`;
* `com_estado`;

debe aparecer una advertencia en admin.

Ejemplo:

```text
La pantalla “ELIXE-002” no tiene coordenadas completas.
Corrige los datos en Xibo y vuelve a sincronizar.
```

---

# 15. Red de pantallas pública

La red de pantallas es una funcionalidad potente, pero no la protagonista de la home.

La prioridad de la home es explicar qué hace Elixe y llevar al asesoramiento.

## Página independiente

Debe existir página:

```http
GET /red-de-pantallas
```

O ruta equivalente:

```http
GET /pantallas
```

## En el menú

```text
Inicio
Para locales
Para anunciantes
Red de pantallas
Solicitar asesoramiento
```

## Visualización

En público:

* mapa con marcadores simples;
* listado opcional de pantallas;
* filtros por tipo de local;
* filtros por sector;
* botón para seleccionar pantalla;
* CTA hacia asesoramiento.

## Datos públicos por pantalla

Mostrar:

```text
nombre del local
municipio
provincia
tipo de local
sector
estado comercial: disponible
```

No mostrar:

```text
ID Xibo
código interno
IP
MAC
licencia
datos técnicos
estado técnico
última conexión
```

## Nombre visible

El nombre visible será el nombre real del local, por ejemplo:

```text
Casa Marino
```

## Ubicación pública

Mostrar:

```text
Casa Marino — Valdoviño, A Coruña
```

No mostrar dirección exacta como elemento principal.

---

# 16. Mapa

Usar por defecto:

```text
Leaflet + OpenStreetMap
```

Codex puede proponer alternativa si tiene mejor encaje, pero el MVP debe ser simple, sin dependencia obligatoria de Google Maps.

## Marcadores

MVP:

* marcadores simples;
* popup básico;
* botón “Seleccionar pantalla” o similar.

Popup ejemplo:

```text
Casa Marino
Bar · Hostelería
Valdoviño, A Coruña
[Seleccionar pantalla]
```

---

# 17. Selección de pantallas para anunciantes

El anunciante puede seleccionar pantallas desde la página de red.

Flujo simple:

```text
selecciona pantallas
pulsa solicitar asesoramiento
llega al formulario con esas pantallas preseleccionadas
```

Usar:

```text
localStorage/sessionStorage
```

No usar carrito complejo.

La selección es opcional. El anunciante también puede pedir asesoramiento sin seleccionar pantallas.

---

# 18. Landing pública

El MVP será principalmente una **landing larga** con secciones.

## Orden recomendado

```text
1. Hero principal
2. Explicación breve de qué hace Elixe
3. Bloque para locales
4. Bloque para anunciantes
5. Cómo funciona
6. Red en Galicia / contadores
7. Preview pequeño del mapa
8. FAQs
9. CTA a solicitar asesoramiento
```

## Hero

Debe usar el claim:

```text
Publicidad local en pantallas reales.
```

Subcopy sugerido:

```text
Elixe instala y gestiona pantallas digitales en locales para mostrar contenido, promociones y publicidad de forma sencilla.
```

CTA principal:

```text
Solicitar asesoramiento
```

CTA secundario:

```text
Ver red de pantallas
```

---

# 19. Página / sección para locales

Mensaje prioritario:

1. mejorar la comunicación del local;
2. generar ingresos con la pantalla.

Debe explicar:

* si ya tienes una pantalla, Elixe puede ayudarte a activarla;
* si no tienes pantalla, Elixe puede estudiar una solución;
* puedes mostrar contenido propio;
* puedes mostrar promociones, menús, avisos o eventos;
* Elixe gestiona la parte técnica;
* puedes generar ingresos mostrando publicidad gestionada.

## Contenido propio

Indicar claramente:

```text
Tu pantalla también puede mostrar contenido propio del local: promociones, menús, avisos, eventos o mensajes para tus clientes.
```

## Gestión técnica

Indicar:

```text
Elixe se encarga de la configuración, gestión de contenidos y mantenimiento.
```

---

# 20. Página / sección para anunciantes

Mensaje prioritario:

```text
Llega a clientes locales y amplía la visibilidad de tu negocio en pantallas reales.
```

Debe explicar:

* publicidad local en pantallas reales;
* presencia en locales físicos;
* selección de zonas;
* selección de tipos de locales;
* asesoramiento personalizado;
* campañas bajo solicitud.

No incluir precios fijos.

Usar:

```text
Solicita una propuesta personalizada.
```

---

# 21. Formulario único de asesoramiento

Debe existir una página única:

```http
GET /asesoramiento
POST /asesoramiento
```

El formulario debe empezar con tarjetas visuales:

```text
Tengo un local
Quiero anunciarme
Tengo otra consulta
```

## 21.1 Comportamiento

Según la tarjeta seleccionada se muestran campos específicos.

El formulario:

* guarda lead en base de datos;
* envía email interno;
* envía email de confirmación al usuario;
* usa captcha;
* exige privacidad;
* valida teléfono español;
* muestra página de gracias.

## 21.2 Contacto

Preferencia de contacto:

```text
Llamada
Email
WhatsApp
Me da igual
```

WhatsApp usa el mismo teléfono.

Horario preferido:

```text
Mañana
Mediodía
Tarde
Me da igual
```

## 21.3 Privacidad

Casilla obligatoria:

```text
Acepto la política de privacidad y el tratamiento de mis datos para que Elixe pueda contactar conmigo.
```

No incluir newsletter en MVP.

## 21.4 Captcha

Usar preferentemente:

```text
Cloudflare Turnstile
```

Si Codex justifica otra opción más cómoda, puede proponerla.

---

# 22. Formulario para locales

Campos:

```text
tipo de solicitud = local
nombre del local
nombre de contacto
teléfono
email
provincia
municipio
tipo de local
tiene pantalla actualmente
quiere que Elixe proporcione pantalla
quiere controlar/seleccionar publicidad
horario preferido para llamada
preferencia de contacto
mensaje libre
aceptación privacidad
captcha
```

## Tipo de local

Selector basado en `loc_tipo`:

```text
Bar
Restaurante
Cafetería
Lavandería
Gimnasio
Peluquería
Clínica
Tienda
Hotel
Supermercado
Oficina
Centro comercial
Farmacia
Autoescuela
Estanco
Panadería
Coworking
Otro
```

---

# 23. Formulario para anunciantes

Campos:

```text
tipo de solicitud = advertiser
nombre de empresa
nombre de contacto
teléfono
email
sector de actividad
zona de interés
presupuesto orientativo
pantallas seleccionadas opcionales
horario preferido para llamada
preferencia de contacto
mensaje libre
aceptación privacidad
captcha
```

## Sector de actividad

Lista cerrada:

```text
Hostelería
Comercio local
Salud y bienestar
Servicios profesionales
Inmobiliaria
Automoción
Educación
Eventos
Turismo
Ocio
Otro
```

## Zona de interés

Selector por provincia/municipio disponible según pantallas sincronizadas.

Ámbito inicial:

```text
A Coruña
Lugo
Ourense
Pontevedra
```

## Presupuesto orientativo

```text
Menos de 100 €
100 - 300 €
Más de 300 €
```

No mostrar precios cerrados en la web.

---

# 24. Página de gracias

Después de enviar formulario:

```http
GET /gracias
```

Debe mostrar mensaje de confirmación y CTA:

```text
Volver al inicio
```

Mensaje sugerido:

```text
Gracias por contactar con Elixe. Hemos recibido tu solicitud y nuestro equipo la revisará para ponerse en contacto contigo.
```

---

# 25. Emails

## 25.1 Email interno

El lead se enviará a un único email configurable desde admin.

Debe indicar claramente si viene de:

```text
local
anunciante
otra consulta
```

Variables iniciales:

```env
ELIXE_LEADS_EMAIL=info@elixe.es
MAIL_FROM_ADDRESS=no-reply@elixe.es
MAIL_FROM_NAME="Equipo Elixe"
```

El email receptor debe poder cambiarse desde admin con validación y auditoría.

## 25.2 Email de confirmación al usuario

Enviar email breve y profesional.

Remitente:

```text
Equipo Elixe
```

Texto sugerido:

```text
Gracias por contactar con Elixe. Hemos recibido tu solicitud y nuestro equipo la revisará para ponerse en contacto contigo.
```

El email automático no necesita variar por idioma en MVP.

## 25.3 Queue

Los emails deben enviarse mediante Laravel Queue usando Redis.

---

# 26. Leads

## Tabla `leads`

Campos recomendados:

```text
id
type
status
business_name
company_name
contact_name
phone
email
province
municipality
location_type
has_screen
wants_elixe_screen
wants_ad_control
activity_sector
interest_zone
budget_range
preferred_contact_method
preferred_call_time
message
privacy_accepted_at
captcha_verified_at
created_at
updated_at
```

## Tipos

```text
venue
advertiser
other
```

## Estados

```text
nuevo
contactado
cita_agendada
en_estudio
ganado
perdido
descartado
```

Los estados deben poder editarse desde admin.

## Pantallas seleccionadas

Tabla pivote:

```text
lead_screen
```

Campos:

```text
id
lead_id
screen_id
created_at
updated_at
```

Solo para leads de anunciantes.

---

# 27. Admin

Debe existir admin desde MVP.

Se puede usar Filament si acelera y robustece el desarrollo.

## 27.1 Autenticación

* varios usuarios admin;
* no roles avanzados en MVP;
* todos los usuarios admin tienen acceso al panel;
* preparar estructura para roles futuro si es razonable, pero no complicar.

## 27.2 Prioridades del admin

Primera prioridad:

```text
gestión de leads
```

También debe incluir:

```text
pantallas sincronizadas
logs de sincronización
contenido editable
FAQs
configuración de contacto
configuración de email receptor
usuarios admin
```

---

# 28. Admin — Leads

Funcionalidades:

* listar leads;
* filtrar por tipo;
* filtrar por estado;
* ver detalle;
* cambiar estado;
* ver pantallas seleccionadas;
* reenviar email interno;
* exportar CSV;
* ver historial de cambios de estado;
* ver fecha de creación;
* ver datos de contacto.

No hace falta en MVP:

* asignar responsable;
* prioridades;
* comentarios múltiples;
* notificaciones internas en panel.

Email basta como notificación.

---

# 29. Admin — Pantallas

Columnas:

```text
nombre interno
nombre público/local
municipio
provincia
tipo de local
sector
visible web
estado comercial
online/offline interno
última sincronización
```

Acciones:

* ver detalle;
* ocultar/mostrar mediante override local;
* confirmar antes de cambiar visibilidad;
* ver advertencias de campos faltantes;
* ejecutar sincronización manual.

La edición de datos principales debe hacerse en Xibo, no en la app.

Si faltan coordenadas o tags, el admin debe avisar:

```text
Corrige estos datos en Xibo y vuelve a sincronizar.
```

---

# 30. Admin — Contenido editable / CMS

Debe existir CMS visual sencillo desde admin.

## 30.1 Idiomas

Idiomas MVP:

```text
español
gallego
```

Idioma principal:

```text
español
```

URLs:

* sin prefijo `/es`;
* sin prefijo `/gl`;
* usar selector interno de idioma.

Los formularios deben estar traducidos ES/GL.

El editor debe permitir contenido ES/GL.

## 30.2 Contenido editable

Editable desde admin:

```text
textos de home
textos de locales
textos de anunciantes
FAQ
datos de contacto
textos legales
imágenes básicas
secciones activas/inactivas
```

## 30.3 Editor visual sencillo

Cada bloque/sección puede tener:

```text
clave
título ES
título GL
subtítulo ES
subtítulo GL
contenido ES
contenido GL
imagen
activo
orden
updated_by
created_at
updated_at
```

No hace falta versionado completo.
Guardar `updated_by`.

## 30.4 Secciones activables

Desde admin se debe poder activar/desactivar:

```text
hero
bloque locales
bloque anunciantes
cómo funciona
contadores
preview mapa
FAQ
CTA asesoramiento
```

No hace falta editar el orden en MVP salvo que sea sencillo.

---

# 31. FAQs

FAQs desde admin:

* crear;
* editar;
* ordenar;
* activar/desactivar;
* separar por categoría.

Categorías:

```text
general
locales
anunciantes
```

Ejemplos de preguntas:

```text
¿Necesito tener una pantalla?
¿Elixe instala la pantalla?
¿Puedo mostrar contenido de mi local?
¿Quién decide qué publicidad aparece?
¿Cuánto puedo ganar?
¿Cuánto cuesta anunciarse?
¿Puedo anunciarme solo en una zona?
¿Puedo elegir tipos de locales?
```

---

# 32. Textos legales

Páginas editables:

```text
Política de privacidad
Cookies
Aviso legal
```

No hace falta incluir aviso especial de “placeholder legal” en el producto final.

Rutas:

```http
GET /privacidad
GET /cookies
GET /aviso-legal
```

---

# 33. Configuración admin

Debe permitir editar:

```text
email visible
teléfono visible
horario de atención
dirección comercial/fiscal si aplica
redes sociales si aplica
email receptor de leads
```

El cambio de email receptor debe:

* validar formato;
* quedar auditado;
* registrar usuario que lo cambió.

---

# 34. Auditoría

Registrar acciones importantes:

```text
usuario cambió estado de lead
usuario reenvió email de lead
usuario exportó CSV
usuario ocultó/mostró pantalla
usuario ejecutó sync manual
usuario editó contenido
usuario editó FAQ
usuario editó textos legales
usuario cambió email receptor de leads
```

Tabla sugerida:

```text
audit_logs
```

Campos:

```text
id
user_id
action
auditable_type
auditable_id
old_values JSON nullable
new_values JSON nullable
ip_address nullable
user_agent nullable
created_at
```

---

# 35. Sync logs

Tabla:

```text
sync_runs
```

Campos:

```text
id
source
status
started_at
finished_at
records_found
records_created
records_updated
records_skipped
error_message
triggered_by_user_id nullable
created_at
updated_at
```

Estados:

```text
running
success
failed
partial
```

---

# 36. Dashboard admin

Debe existir dashboard básico con métricas:

```text
leads nuevos
leads esta semana
pantallas visibles
última sincronización
formularios por tipo
pantallas con datos incompletos
```

---

# 37. Multidioma

Idiomas:

```text
es
gl
```

## Requisitos

* español idioma principal;
* gallego incluido desde el principio;
* formularios traducidos;
* contenido editable con campos ES/GL;
* textos iniciales ES/GL incluidos en seeders;
* emails automáticos pueden ser únicos en español en MVP;
* URLs sin prefijo de idioma;
* selector de idioma en frontend.

---

# 38. Diseño visual

## Identidad

Elixe tiene logo propio.
Los colores van en una línea azul / temática mar, vinculada a Valdoviño.

## Estilo

* claro;
* luminoso;
* moderno;
* SaaS;
* cercano;
* profesional.

## Modo oscuro

Incluir modo oscuro seleccionable.

## Animaciones

Incluir animaciones suaves:

* aparición de tarjetas;
* contadores;
* transiciones;
* scroll suave;
* microinteracciones.

Evitar animaciones pesadas.

---

# 39. Rutas públicas

```http
GET /
GET /red-de-pantallas
GET /asesoramiento
POST /asesoramiento
GET /gracias
GET /privacidad
GET /cookies
GET /aviso-legal
```

Opcionales si ayudan a SEO:

```http
GET /locales
GET /anunciantes
```

Aunque el MVP sea landing, pueden existir rutas dedicadas si Codex lo considera mejor.

---

# 40. API pública interna para React

Endpoints JSON:

```http
GET /api/public/network-summary
GET /api/public/screens/map
GET /api/public/screen-filters
```

## `GET /api/public/network-summary`

Respuesta ejemplo:

```json
{
  "visibleScreens": 6,
  "availableScreens": 4,
  "provinces": ["A Coruña", "Lugo"],
  "sectors": ["hosteleria", "servicios"]
}
```

## `GET /api/public/screens/map`

Respuesta ejemplo:

```json
[
  {
    "id": 1,
    "name": "Casa Marino",
    "municipality": "Valdoviño",
    "province": "A Coruña",
    "latitude": 43.583251325335,
    "longitude": -8.1820912436517,
    "locationType": "bar",
    "locationSector": "hosteleria",
    "commercialStatus": "disponible"
  }
]
```

No devolver:

```text
xibo_display_id
internal_name
display codes
IP
MAC
licencia
payload técnico
```

---

# 41. Seguridad

Requisitos:

* no exponer credenciales Xibo;
* no llamar a Xibo desde frontend;
* validar formularios;
* captcha obligatorio;
* rate limiting en formulario;
* validación de teléfono español;
* aceptación privacidad obligatoria;
* sanitizar entradas;
* proteger admin con login;
* auditar acciones importantes;
* no exponer datos técnicos de Xibo;
* usar queues para email;
* usar variables `.env` para secretos;
* no commitear secretos.

---

# 42. Tests

Incluir tests desde el inicio.

Prioridad:

```text
formulario crea lead
formulario envía email interno
formulario envía confirmación
captcha requerido
privacidad requerida
validación teléfono español
sync Xibo crea/actualiza pantallas
sync Xibo normaliza tags con trim
pantallas públicas no exponen datos sensibles
override local oculta pantalla
admin puede cambiar estado de lead
auditoría registra acciones importantes
```

---

# 43. Seeders

Crear seeders para:

* usuario admin inicial;
* contenido informativo inicial ES/GL;
* FAQs iniciales ES/GL;
* configuración de contacto;
* configuración de email receptor;
* textos legales iniciales;
* secciones de landing.

No crear pantallas de ejemplo por defecto si la app está conectada a Xibo.
Las pantallas deben venir de la API.

Puede haber factory/mock para tests, pero no seed productivo de pantallas.

## Usuario admin inicial

Crear comando o seeder para usuario admin.

Opción recomendada:

```bash
php artisan elixe:create-admin
```

---

# 44. Documentación interna

Crear carpeta:

```text
/docs
```

Archivos esperados:

```text
docs/product-context.md
docs/architecture.md
docs/xibo-integration.md
docs/database.md
docs/development-plan.md
docs/docker.md
docs/admin.md
docs/testing.md
```

---

# 45. Plan por fases

Codex debe trabajar primero en documentación y estructura.

## Fase 0 — Documentación y estructura

Objetivo:

* crear estructura de proyecto;
* documentar arquitectura;
* documentar decisiones;
* preparar Docker;
* preparar Laravel + React + Inertia;
* preparar admin base.

Entregables:

```text
README.md
/docs/product-context.md
/docs/architecture.md
/docs/xibo-integration.md
/docs/development-plan.md
docker-compose.yml
.env.example
estructura Laravel inicial
auth básica
```

## Fase 1 — Vertical slice funcional

Objetivo:

```text
Xibo sync → pantalla en BD → mapa público → formulario → lead en admin → email enviado
```

Entregables:

* `XiboService`;
* comando `xibo:sync-displays`;
* modelo `Screen`;
* logs de sync;
* página red de pantallas;
* formulario asesoramiento;
* guardado de leads;
* emails;
* admin leads;
* admin pantallas;
* tests básicos.

## Fase 2 — CMS / contenido

Entregables:

* editor visual sencillo;
* bloques landing;
* FAQs;
* textos legales;
* datos de contacto;
* imágenes básicas;
* ES/GL;
* secciones activables.

## Fase 3 — Admin robusto

Entregables:

* dashboard métricas;
* auditoría;
* CSV;
* reenvío emails;
* configuración receptor leads;
* override de visibilidad con confirmación;
* avisos de pantallas incompletas.

## Fase 4 — Mejora visual

Entregables:

* landing pulida;
* animaciones suaves;
* modo oscuro;
* diseño SaaS azul/mar;
* UX móvil;
* mejoras de mapa.

## Fase 5 — Futuro no MVP

No implementar todavía:

* pagos online;
* área privada de anunciantes;
* subida de creatividades;
* creación automática de campañas Xibo;
* programación de anuncios en Xibo;
* estadísticas proof of play para anunciantes;
* roles avanzados;
* asignación de responsables;
* comentarios múltiples en leads.

---

# 46. Gestión por agentes

Codex debe organizar el trabajo por agentes o áreas:

## Agente 1 — Arquitectura Laravel

Responsable de:

* estructura Laravel;
* configuración;
* migraciones;
* modelos;
* servicios base;
* Docker;
* `.env.example`;
* documentación técnica.

## Agente 2 — Integración Xibo

Responsable de:

* `XiboService`;
* autenticación OAuth;
* sync displays;
* normalización de tags;
* sync logs;
* manejo de errores;
* tests de integración/mocks.

## Agente 3 — Frontend público

Responsable de:

* landing;
* página red de pantallas;
* mapa;
* selección de pantallas;
* formulario asesoramiento;
* modo oscuro;
* diseño responsive;
* ES/GL.

## Agente 4 — Admin / CRM / CMS

Responsable de:

* Filament/admin;
* leads;
* pantallas;
* contenidos;
* FAQs;
* legales;
* configuración;
* dashboard;
* auditoría.

## Agente 5 — Formularios / Emails / Seguridad

Responsable de:

* validaciones;
* captcha;
* rate limiting;
* mailables;
* queues;
* confirmación usuario;
* email interno;
* privacidad;
* tests.

## Agente 6 — QA / Tests / Documentación

Responsable de:

* feature tests;
* unit tests;
* revisar exposición de datos sensibles;
* documentación `/docs`;
* checklist de aceptación;
* instrucciones de despliegue.

---

# 47. Checklist de aceptación MVP

## Público

* [ ] La home explica claramente qué hace Elixe.
* [ ] Existe CTA principal “Solicitar asesoramiento”.
* [ ] La landing tiene bloques para locales y anunciantes.
* [ ] Existe página de red de pantallas.
* [ ] El mapa muestra solo pantallas disponibles.
* [ ] El mapa no muestra datos técnicos.
* [ ] Se pueden seleccionar pantallas y pasar al formulario.
* [ ] El formulario tiene tarjetas iniciales.
* [ ] El formulario valida privacidad, captcha y teléfono.
* [ ] Se muestra página de gracias.

## Xibo

* [ ] Laravel obtiene token OAuth.
* [ ] Laravel consulta `/api/display`.
* [ ] Laravel sincroniza pantallas.
* [ ] Los tags se normalizan con `trim()`.
* [ ] Las pantallas incompletas generan aviso en admin.
* [ ] La sincronización automática se ejecuta 2 o 3 veces al día.
* [ ] Existe sincronización manual desde admin.
* [ ] Xibo no se modifica desde la app.

## Leads

* [ ] El lead se guarda en BD.
* [ ] El lead se envía por email interno.
* [ ] El usuario recibe confirmación.
* [ ] Los leads aparecen en admin.
* [ ] El estado del lead puede cambiarse.
* [ ] Los cambios de estado quedan registrados.
* [ ] Se puede exportar CSV.
* [ ] Se puede reenviar email interno.

## Admin

* [ ] Hay login admin.
* [ ] Hay varios usuarios admin.
* [ ] Hay dashboard básico.
* [ ] Hay gestión de leads.
* [ ] Hay gestión de pantallas.
* [ ] Hay override local de visibilidad con confirmación.
* [ ] Hay CMS visual sencillo.
* [ ] Hay gestión de FAQs.
* [ ] Hay gestión de textos legales.
* [ ] Hay configuración de contacto.
* [ ] Hay configuración de email receptor.
* [ ] Las acciones importantes quedan auditadas.

## Diseño

* [ ] Diseño claro y luminoso.
* [ ] Estética azul/mar.
* [ ] Responsive móvil.
* [ ] Animaciones suaves.
* [ ] Modo oscuro seleccionable.
* [ ] ES/GL disponible.

## Seguridad

* [ ] No se exponen secretos.
* [ ] No se exponen datos técnicos Xibo.
* [ ] El formulario tiene captcha.
* [ ] Hay rate limiting.
* [ ] Hay validación de datos.
* [ ] Admin protegido.
* [ ] Emails por queue.

---

# 48. Prompt operativo para Codex

Usa este contexto para construir el proyecto `elixe-web`.

Primero genera documentación y estructura, no empieces directamente con todo el código de negocio. Crea un plan de trabajo por fases y por agentes, siguiendo las secciones anteriores.

Prioriza una arquitectura limpia, mantenible y robusta:

1. Laravel + React + Inertia + TypeScript.
2. Admin robusto preferentemente con Filament.
3. Docker de desarrollo.
4. MySQL + Redis.
5. Integración Xibo solo lectura.
6. Sincronización manual y programada.
7. Landing pública moderna.
8. Formulario único de asesoramiento.
9. CRM básico de leads.
10. CMS visual sencillo para contenido, FAQs y legales.
11. Auditoría de acciones importantes.
12. Tests desde el inicio.

No implementes funcionalidades futuras como pagos, campañas automáticas, subida de creatividades o programación en Xibo.

El objetivo inicial es entregar una vertical funcional:

```text
Xibo sync → pantalla en BD → red pública/mapa → formulario → lead guardado → email enviado → lead visible en admin
```

Antes de crear código masivo, genera:

```text
README.md
/docs/product-context.md
/docs/architecture.md
/docs/xibo-integration.md
/docs/development-plan.md
.env.example
docker-compose.yml inicial
plan por fases
checklist técnico
```

Después implementa fase por fase, manteniendo tests y documentación actualizados.
