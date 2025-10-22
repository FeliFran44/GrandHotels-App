# Grand Hotels Lux – Plataforma de Seguridad Operativa

Este proyecto brinda a la cadena Grand Hotels Lux una plataforma web para la gestión diaria de seguridad: planificación de eventos, registro de novedades y accidentes, administración de personal e inventario, y reportes ejecutivos con exportación a PDF.

La aplicación está construida con Laravel (renderizado server‑side con Blade), MySQL como base de datos y Vite únicamente como pipeline de assets (CSS/JS). No es una SPA.

## Contenidos
- Descripción y arquitectura
- Módulos y secciones
- Modelos y base de datos
- Rutas y vistas principales
- Reportes de Accidentes (web y PDF)
- Configuración de categorías y gravedad
- Instalación y configuración (desarrollo y producción)
- Despliegue en dominio corporativo
- Mantenimiento, seguridad y FAQ

---

## Descripción y arquitectura
- Framework: Laravel 12 (PHP 8.2+), Blade (SSR), DomPDF para PDF.
- BD: MySQL 8.
- Frontend: HTML generado por Blade; estilos con Bootstrap/utilidades; Vite compila assets.
- JS puntual: Chart.js (para gráficas en la vista de reportes web).
- Archivos: adjuntos guardados en `storage/app/public` y publicados vía `storage:link`.

Estructura (resumen)
- Controladores: `app/Http/Controllers/*`
- Modelos: `app/Models/*`
- Middleware: `app/Http/Middleware/*` (incluye `CheckCoordinatorRole` alias `is.coordinator`)
- Vistas Blade: `resources/views/**/*`
- Rutas: `routes/web.php`
- Configuración: `config/*` (categorías y gravedades en `config/accidentes.php`)
- Migraciones: `database/migrations/*`

## Módulos y secciones
- Dashboard: novedades generales, accesos rápidos y Personal en vivo por hotel.
- Planificación de eventos: alta y visualización de eventos con fechas/observaciones/adjuntos.
- Comunicados y respuestas: publicación de novedades corporativas o por hotel con seguimiento.
- Personal de seguridad: dotación, horarios, días libres, vacaciones.
- Inventario y mantenimiento: estado de ítems, últimas/próximas fechas de mantenimiento.
- Accidentes/Incidentes: registro, clasificación por categoría y severidad (gravedad), adjuntos.
- Capacitaciones: registro de capacitaciones del personal con tipo, fecha, duración y resultados.
- Reportes: analítica de accidentes (web con gráficas y PDF ejecutivo).
- Archivo General y Auditoría (acceso de Coordinador).

## Modelos y base de datos (tablas principales)
- `hoteles` (Hotel): nombre, color (opcional para gráficas), relaciones.
- `users` (User): autenticación; `rol` (Gerente/Coordinador) y `hotel_id` (si es Gerente).
- `accidentes` (Accidente):
  - `hotel_id`, `user_id`, `tipo` (Accidente/Incidente), `categoria` (taxonomía corporativa), `gravedad` (Baja/Media/Alta), `fecha_evento`, `descripcion`, `involucrados`, `acciones_tomadas`.
- `eventos` (Evento): `hotel_id`, `user_id`, `titulo`, `tipo`, `fecha_inicio`, `fecha_fin`, observaciones; adjuntos polimórficos.
- `capacitaciones` (Capacitacion): `hotel_id`, `user_id`, `titulo`, `tipo`, `fecha_inicio`, `duracion_aproximada`, `instructor`, `participantes`, `resultados`; adjuntos polimórficos.
- `inventario` (Inventario): estado, última/próxima fecha de mantenimiento, nombre y datos del ítem.
- `archivos` (Archivo): adjuntos polimórficos (`archivable`).

> El esquema se crea con migraciones (`database/migrations`) al ejecutar `php artisan migrate`.

## Rutas y vistas principales
- Autenticación y verificación: todas las rutas de negocio están bajo `auth` + `verified` (ver `routes/web.php`).
- Middleware `is.coordinator`: restringe rutas de Coordinador (hoteles, archivo general, auditoría y reportes).

Ubicación por módulo
- Accidentes: `app/Http/Controllers/AccidenteController.php`, vistas en `resources/views/accidentes/*` (create, edit, index, show).
- Planificación: `app/Http/Controllers/EventoController.php`, vistas en `resources/views/planificacion/*`.
- Capacitaciones: `app/Http/Controllers/CapacitacionController.php`, vistas en `resources/views/capacitaciones/*` (create, edit, index, show).
- Reportes: `app/Http/Controllers/ReporteController.php`, vistas en `resources/views/reportes/index.blade.php` (web) y `resources/views/reportes/pdf.blade.php` (PDF).

## Reportes de Accidentes (web y PDF)
- Web (`/reportes`):
  - Filtros: Hotel, Rango de fechas y Categoría (tomadas de `config/accidentes.php`).
  - Gráficas (Chart.js):
    - Barras apiladas por día (una serie por hotel).
    - Doughnut con totales por hotel.
  - Listas de apoyo: Accidentes por categoría, Top categorías por hotel y “Conclusiones rápidas”.
- PDF (DomPDF; solo Accidentes/Incidentes):
  - Resumen global: totales por hotel; accidentes por categoría; top categorías por hotel; tendencia diaria por hotel (tabla por fecha/hotel).
  - Detalle por hotel: todas las filas del período con columnas Fecha, Tipo, Categoría, Gravedad, Descripción y Reportado por; además, resúmenes por categoría y por gravedad para ese hotel.
  - Nota: DomPDF no ejecuta JS; por eso las gráficas se representan como tablas.

## Configuración de categorías y gravedad
- Archivo: `config/accidentes.php`
  - `categorias`: taxonomía corporativa (ej.: Caídas mismo nivel, Caídas distinto nivel, Golpes objetos, Pisadas objetos, Cortes, Pinchazos, Atrapamientos, Sobreesfuerzos, Movimientos repetitivos, Quemaduras, Frío / calor, Químicos, Eléctricos, Tránsito interno, Tránsito externo, Incendios, Explosiones, Fauna, Agresiones, In itinere, Otro).
  - `gravedades`: `['Baja','Media','Alta']`.
- Formularios de Accidentes (alta/edición) usan estas listas; el backend valida contra ellas.

---

## Instalación y configuración (entorno agnóstico)
Requisitos
- PHP 8.2+ con extensiones comunes de Laravel (mbstring, openssl, pdo_mysql, tokenizer, ctype, json, dom, fileinfo, gd o imagick recomendado).
- Composer 2.x
- Node.js 18+ y npm (para compilar assets con Vite)
- MySQL 8 (o MariaDB compatible)

Pasos
1) Clonar el repositorio
```
cd /ruta/a/GrandHotels-App
```
2) Instalar dependencias
```
composer install
npm ci   # o npm install
```
3) Variables de entorno
```
cp .env.example .env   # en Windows: copy .env.example .env
```
Editar `.env` y definir:
- `APP_NAME=GrandHotelsLux`
- `APP_URL=https://tu-dominio.corporativo` (o `http://localhost` en dev)
- `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `FILESYSTEM_DISK=public`

4) Clave, storage y migraciones
```
php artisan key:generate
php artisan storage:link
php artisan migrate
```

5) Compilar assets
```
npm run build   # producción
# o npm run dev para desarrollo
```

6) Servir la aplicación
- Desarrollo: `php artisan serve` (http://127.0.0.1:8000)
- Producción (Apache/Nginx): apuntar el vhost al directorio `public/` y asegurar permisos en `storage/`.
- Docker (opcional; Sail): `composer require laravel/sail --dev && php artisan sail:install && ./vendor/bin/sail up`

> Nota sobre XAMPP: se usó en ejemplos locales de Windows, pero NO es requisito. Este proyecto funciona en cualquier stack PHP/MySQL bien configurado.

---

## Despliegue en dominio corporativo
- Requisitos del host: PHP 8.2+, MySQL 8, Apache o Nginx.
- Pasos recomendados en servidor:
  1) `composer install --no-dev --optimize-autoloader`
  2) `npm ci && npm run build` (o compilar local y subir `public/build`)
  3) Configurar `.env` (APP_URL y credenciales DB)
  4) `php artisan key:generate` (si aplica) y `php artisan migrate --force`
  5) `php artisan storage:link`
  6) `php artisan config:cache && php artisan route:cache` (opcional)
- Vhost apuntando a `public/`, HTTPS recomendado.

## Mantenimiento y seguridad
- Roles: Coordinador (corporativo) vs Gerente (por hotel). El Coordinador accede a reportes y módulos globales.
- Validaciones de formularios en controladores; middleware `is.coordinator` en rutas sensibles.
- Respaldos: base MySQL y `storage/app/public` (adjuntos).
- Auditoría y logs: revisar `storage/logs/laravel.log`.
- Actualizaciones: mantener Composer/npm al día; ejecutar migraciones tras cambios de esquema.

## Preguntas frecuentes
- ¿Dónde cambio las categorías de accidentes? → `config/accidentes.php`.
- ¿Cómo agrego severidades? → `config/accidentes.php` (`gravedades`).
- ¿Por qué el PDF no trae gráficas? → DomPDF no ejecuta JS; se incluyen tablas equivalentes.
- ¿Cómo cambio el dominio? → `APP_URL` en `.env` y vhost a `public/`.
- No veo adjuntos → `php artisan storage:link` y permisos en `storage/`.

---

