# Librería Horizonte

Portal académico desarrollado con HTML, CSS, JavaScript, PHP y MySQL. Permite
consultar el catálogo de libros, explorar el directorio de autores y enviar
mensajes mediante un formulario que almacena la información en la tabla
`contacto`.

## Tecnologías

- HTML5 semántico y accesible.
- CSS3 adaptable para computadoras, tabletas y celulares.
- JavaScript sin dependencias.
- PHP 8.1 o superior.
- MySQL o MariaDB.
- PDO con consultas preparadas.

## Funcionalidades

- Catálogo con todos los libros de la base proporcionada.
- Búsqueda por título o descripción.
- Filtro por categoría y ordenamiento.
- Directorio completo de autores con búsqueda.
- Formulario de contacto con validación en cliente y servidor.
- Persistencia de contactos mediante `POST` y PDO.
- Patrón POST/Redirect/GET para evitar envíos duplicados.
- Protección CSRF, campo antispam y escape de datos con `htmlspecialchars`.
- Navegación, etiquetas y mensajes completamente en español.

## Estructura

```text
Proyecto_Final_Libreria/
├── assets/
│   ├── css/estilos.css
│   └── js/app.js
├── config/
│   ├── configuracion.php
│   └── configuracion.ejemplo.php
├── database/dblibreria.sql
├── includes/
│   ├── bootstrap.php
│   ├── conexion.php
│   ├── funciones.php
│   ├── header.php
│   └── footer.php
├── autores.php
├── contacto.php
├── index.php
└── REQUISITOS_CUMPLIDOS.md
```

## Instalación local con XAMPP

1. Instala XAMPP con Apache, PHP y MySQL.
2. Copia la carpeta `Proyecto_Final_Libreria` dentro de `C:\xampp\htdocs`.
3. Inicia Apache y MySQL desde el panel de XAMPP.
4. Abre `http://localhost/phpmyadmin`.
5. Selecciona **Importar** y carga `database/dblibreria.sql`.
6. Si MySQL utiliza un usuario o contraseña diferente, copia
   `config/configuracion.ejemplo.php` como
   `config/configuracion.local.php` y coloca allí las credenciales.
7. Visita `http://localhost/Proyecto_Final_Libreria/`.

La configuración predeterminada utiliza:

```text
Servidor: localhost
Puerto: 3306
Base de datos: dblibreria
Usuario: root
Contraseña: vacía
```

## Publicación en un hosting PHP/MySQL

1. Crea una base MySQL desde el panel del hosting.
2. Importa `database/dblibreria.sql` con phpMyAdmin.
3. Copia `config/configuracion.ejemplo.php` como
   `config/configuracion.local.php`.
4. Actualiza en ese archivo el servidor, nombre de base, usuario y contraseña
   suministrados por el hosting.
5. Sube todos los archivos del proyecto al directorio público del servidor.
6. No subas `configuracion.local.php` al repositorio público de GitHub.
7. Comprueba las páginas de libros, autores y contacto antes de entregar.

## Evidencia de los elementos solicitados

- `GET`: filtros de `index.php` y búsqueda de `autores.php`.
- `POST`: envío del formulario en `contacto.php`.
- `foreach`: categorías, libros y autores.
- `PDO`: `includes/conexion.php`.
- `PDO query`: carga de categorías y contadores.
- Consultas preparadas: búsquedas y registro de contactos.
- `count()`: total de resultados del catálogo.
- `sizeof()`: total de categorías.
- CSS: `assets/css/estilos.css`.
- JavaScript: `assets/js/app.js`.

## Seguridad

Las credenciales reales deben guardarse únicamente en
`config/configuracion.local.php`, archivo excluido por `.gitignore`. Las
consultas con datos del usuario utilizan sentencias preparadas y todo contenido
mostrado en HTML se escapa para reducir riesgos de inyección y XSS.
