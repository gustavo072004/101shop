# 101 Shop Sprint

Catálogo de productos y accesorios con administración privada. Conserva el login Breeze, recuperación de contraseña, personal operativo y bitácora del proyecto original. Actualizado a Laravel 12 y PHP 8.3; no incluye vehículos.

## Funciones
- Público: búsqueda, categorías, detalle, precio final y WhatsApp. Sin compras ni administración.
- Administrador: CRUD de marcas, categorías y productos (alta, consulta, modificación, baja lógica y reactivación), publicación, imagen, stock, precio de ingreso, valor real y precio final.
- Vendedor: catálogo privado y perfil; no puede consultar costos ni modificar productos o categorías.
- Una cuenta desactivada pierde el acceso incluso con sesión previa. No hay registro público.
- Fotos JPG/PNG/WebP de hasta 2 MB se guardan en PostgreSQL para persistir entre despliegues.
- Las categorías inactivas y los productos dados de baja o no publicados desaparecen del catálogo público.

## Datos iniciales
Ocho productos y precios finales de la captura suministrada. No se inventaron costos internos ni existencias: los costos están pendientes y el stock inicia en cero. Las fotos reales se cargan desde Productos; inicialmente se usa una imagen de sustitución. Los códigos PR-001 a PR-008 se asignaron para identificar los productos.

## Instalación
Requiere PHP 8.3, Composer, Node 22 y PostgreSQL. Para pruebas se usa SQLite.

```sh
composer install
npm ci
cp .env.example .env
php artisan key:generate
# Configurar PostgreSQL, ADMIN_EMAIL y ADMIN_PASSWORD (mínimo 12 caracteres) en .env.
php artisan migrate --seed
npm run build
php artisan serve
```

Marcas se administran en /marcas y son una selección obligatoria de una marca activa en el formulario del producto.

Rutas: /publico/catalogo, /login, /dashboard, /catalogo, /productos y /categorias. Tras iniciar sesión como Administrador, los CRUD aparecen en el menú y en el panel. No se muestran en la página pública.

WHATSAPP_PHONE centraliza el número. El administrador inicial se crea una sola vez: ejecutar seed de nuevo no cambia su contraseña ni sobrescribe productos existentes. Los importes de ingreso, costo real y precio final se capturan independientemente: no se aplica una fórmula de margen no confirmada.

## Verificación
```sh
php artisan test --compact
composer audit
npm audit
npm run build
php artisan route:cache
php artisan view:cache
```

GitHub Actions comprueba tests, assets, auditoría PHP y migraciones PostgreSQL. La restricción privada usa cuentas autorizadas, no una lista de computadoras.

## Despliegue gratuito
Incluye Dockerfile y render.yaml para Render Free, con PostgreSQL de Neon. Crear el proyecto de base de datos y configurar DATABASE_URL con sslmode=require, APP_KEY (php artisan key:generate --show), APP_URL HTTPS y las variables de administrador/correo en Render. No subir .env ni bases locales. APP_DEBUG=false y SESSION_SECURE_COOKIE=true en producción.

Render Free suspende el servicio tras inactividad y limita SMTP en puertos 25, 465 y 587. Configurar un proveedor compatible en el puerto 2525; la entrega real de recuperación necesita credenciales de correo y remitente verificado. MAIL_MAILER=log solo sirve en desarrollo. https://render.com/docs/free — https://neon.com/pricing

No se ejecutó Docker localmente ni se verificó entrega de correo externo. La publicación web requiere las cuentas y variables del hosting. Las fotos están en base de datos; las sesiones locales al contenedor se pierden al reiniciar (se vuelve a iniciar sesión).
