# FelipeTempApp — Aplicación del Tiempo

Aplicación web desarrollada en **PHP 8.2** con arquitectura **MVC** que permite consultar el tiempo atmosférico de cualquier ciudad del mundo utilizando la API de OpenWeatherMap. Desplegada en **AWS EC2** con **Docker** y accesible mediante dominio propio con **HTTPS**.

---

## Tabla de contenidos

- [Descripción del proyecto](#descripción-del-proyecto)
- [Tecnologías utilizadas](#tecnologías-utilizadas)
- [Arquitectura MVC](#arquitectura-mvc)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Explicación de cada archivo](#explicación-de-cada-archivo)
- [Base de datos](#base-de-datos)
- [API de OpenWeatherMap](#api-de-openweathermap)
- [Docker y docker-compose](#docker-y-docker-compose)
- [Instalación en local](#instalación-en-local)
- [Despliegue en AWS](#despliegue-en-aws)
- [Configuración SSL/HTTPS](#configuración-sslhttps)
- [Problemas conocidos y soluciones](#problemas-conocidos-y-soluciones)
- [URLs de entrega](#urls-de-entrega)

---

## Descripción del proyecto

La aplicación permite al usuario:

- Buscar cualquier ciudad del mundo mediante un formulario
- Si la ciudad no existe, se informa al usuario en pantalla
- Si la ciudad existe, ofrece tres tipos de consulta:
  - **Tiempo actual:** temperatura, sensación térmica, viento, humedad, presión, nubosidad, amanecer y atardecer
  - **Previsión por horas:** evolución del tiempo en las próximas 24 horas con gráfica
  - **Previsión semanal:** resumen de los próximos 5 días con gráfica
- Todas las consultas quedan registradas en una base de datos **MariaDB**
- Página de **historial** con todas las consultas realizadas y ranking de ciudades más consultadas
- Gráficas generadas con **QuickChart** sin necesidad de JavaScript

---

## Tecnologías utilizadas

| Tecnología | Versión | Para qué se usa |
|-----------|---------|----------------|
| PHP | 8.2 | Lenguaje principal del backend |
| Apache | 2.4.66 | Servidor web |
| MariaDB | 10.11 | Base de datos |
| Docker | 29.3.0 | Contenedores |
| Docker Compose | v2 | Orquestación de contenedores |
| Bootstrap | 5.3.3 | Estilos y diseño responsive |
| Bootstrap Icons | 1.11.3 | Iconos |
| QuickChart | — | Generación de gráficas como imagen |
| OpenWeatherMap API | 2.5 | Datos meteorológicos |
| Let's Encrypt | — | Certificado SSL gratuito |
| Ubuntu | 24.04 LTS | Sistema operativo de la instancia AWS |
| AWS EC2 | t2.micro | Servidor en la nube |

---

## Arquitectura MVC

La aplicación sigue el patrón **Modelo Vista Controlador (MVC)**, que separa la aplicación en tres capas:

```
Usuario (navegador)
        │
        ▼
   index.php  ←── Punto de entrada único
        │
        ▼
ControladorPrincipal  ←── Lee la URL y decide qué hacer
        │
   ┌────┴────┐
   ▼         ▼
 API       DAO (ConsultaDAO)
   │         │
   │         ▼
   │      Base de datos MariaDB
   │
   ▼
Vista (vista_*.php)  ←── Genera el HTML que ve el usuario
```

- **Modelo:** `ConsultaDAO.php` y `Conexion.php` — gestionan los datos de la base de datos
- **Vista:** archivos `vista_*.php` — generan el HTML que ve el usuario
- **Controlador:** `ControladorPrincipal.php` — recibe las peticiones, llama a la API o al DAO y decide qué vista cargar

La ventaja de MVC es que cada parte tiene una responsabilidad clara:
- Si cambia la base de datos, solo tocas el Modelo
- Si cambia el diseño, solo tocas la Vista
- Si cambia la lógica, solo tocas el Controlador

---

## Estructura del proyecto

```
tiempo_app_iaw/
├── index.php                          ← Punto de entrada
├── config.php                         ← Configuración global
├── Dockerfile                         ← Imagen Docker
├── docker-compose.yml                 ← Orquestación de contenedores
├── Controller/
│   └── ControladorPrincipal.php       ← Controlador principal
├── DB/
│   ├── Conexion.php                   ← Conexión PDO a MariaDB
│   └── bd.sql                         ← Script de creación de la BD
├── Model/
│   └── ConsultaDAO.php                ← Operaciones con la BD
└── View/
    ├── layout_base.php                ← Cabecera y navegación comunes
    ├── vista_inicio.php               ← Formulario de búsqueda
    ├── vista_opciones.php             ← Opciones de consulta
    ├── vista_actual.php               ← Tiempo actual
    ├── vista_horas.php                ← Previsión por horas
    ├── vista_semana.php               ← Previsión semanal
    └── vista_historial.php            ← Historial de consultas
```

---

## Explicación de cada archivo

### `index.php`
Punto de entrada único de la aplicación. Todas las peticiones del navegador pasan por aquí. Su única función es cargar la configuración, cargar el controlador y ejecutarlo. Es intencionadamente muy corto para mantener la separación de responsabilidades del patrón MVC.

### `config.php`
Contiene todas las constantes de configuración: la API key de OpenWeatherMap y las credenciales de la base de datos. Al centralizarlo en un único archivo, si se necesita cambiar algún valor solo hay que tocarlo aquí.

### `Controller/ControladorPrincipal.php`
Es el núcleo de la aplicación. Lee el parámetro `?accion=` de la URL y decide qué hacer:

| Acción | Método | Descripción |
|--------|--------|-------------|
| `inicio` | `mostrarInicio()` | Muestra el formulario de búsqueda |
| `buscar` | `buscarCiudad()` | Llama a la API de geocodificación |
| `actual` | `mostrarActual()` | Llama a la API de tiempo actual |
| `horas` | `mostrarHoras()` | Llama a la API de previsión por horas |
| `semana` | `mostrarSemana()` | Llama a la API de previsión semanal |
| `historial` | `mostrarHistorial()` | Consulta el historial en la BD |

Las llamadas a la API se realizan con **cURL** en lugar de `file_get_contents` porque en Docker `file_get_contents` para URLs externas suele estar bloqueado por la configuración de PHP.

### `DB/Conexion.php`
Gestiona la conexión a la base de datos usando **PDO** (PHP Data Objects). Implementa el patrón **Singleton**: solo existe una única conexión durante toda la ejecución, evitando abrir múltiples conexiones innecesarias. El constructor es privado para que nadie pueda crear instancias desde fuera, obligando a usar `Conexion::obtener()`.

### `Model/ConsultaDAO.php`
DAO (Data Access Object): es el único lugar donde se escribe SQL en toda la aplicación. Tiene tres métodos:
- `guardar()` — registra cada consulta en la BD usando consultas preparadas
- `obtenerHistorial()` — devuelve todas las consultas ordenadas por fecha
- `obtenerRanking()` — devuelve las 5 ciudades más consultadas

Usa **consultas preparadas** con `prepare()` y `execute()` para prevenir inyección SQL.

### `View/layout_base.php`
Contiene el HTML común a todas las páginas interiores: la etiqueta `<head>` con Bootstrap e iconos, y la barra de navegación superior con el logo, el nombre de la ciudad activa y los enlaces a las tres vistas. Todas las vistas interiores lo incluyen con `include`.

### `View/vista_inicio.php`
Formulario de búsqueda con campo de texto y botón. Si el controlador detecta que la ciudad no existe, muestra un mensaje de error. Tiene su propio HTML completo porque es la única vista que no usa el `layout_base`.

### `View/vista_opciones.php`
Muestra tres tarjetas con las opciones de consulta disponibles para la ciudad encontrada: tiempo actual, por horas y semanal. Cada tarjeta es un enlace que lleva a la vista correspondiente pasando las coordenadas en la URL.

### `View/vista_actual.php`
Muestra los datos meteorológicos del momento: temperatura principal con icono, sensación térmica, mínima y máxima, y seis datos adicionales en cuadrícula (humedad, presión, viento, nubosidad, amanecer y atardecer).

### `View/vista_horas.php`
Muestra una **gráfica de línea** con la temperatura de las próximas 8 franjas horarias (cada 3 horas = 24 horas vista) y una tabla con todos los detalles. La gráfica se genera con QuickChart enviando la configuración en la URL como imagen.

### `View/vista_semana.php`
Muestra una **gráfica de barras** con la temperatura de cada día de la semana y una tabla con los detalles de cada día. De cada día se toma el registro de las 12:00 como representativo, o el primero disponible si no hay dato de mediodía.

### `View/vista_historial.php`
Muestra el ranking de las ciudades más consultadas y una tabla con el historial completo de todas las consultas realizadas, con la posibilidad de repetir cualquier consulta anterior directamente.

---

## Base de datos

Se utiliza **MariaDB 10.11** con una única tabla llamada `consultas`.

### Diseño de la tabla

```sql
CREATE TABLE consultas (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    ciudad         VARCHAR(100) NOT NULL,
    pais           VARCHAR(10)  NOT NULL DEFAULT '',
    latitud        DECIMAL(9,6) NOT NULL,
    longitud       DECIMAL(9,6) NOT NULL,
    tipo_consulta  ENUM('actual','horas','semana') NOT NULL,
    fecha_consulta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
```

### Descripción de los campos

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | INT AUTO_INCREMENT | Identificador único de cada consulta |
| `ciudad` | VARCHAR(100) | Nombre de la ciudad consultada |
| `pais` | VARCHAR(10) | Código de país ISO (ej: ES, JP, US) |
| `latitud` | DECIMAL(9,6) | Latitud obtenida de la API de geocodificación |
| `longitud` | DECIMAL(9,6) | Longitud obtenida de la API de geocodificación |
| `tipo_consulta` | ENUM | Tipo de consulta: actual, horas o semana |
| `fecha_consulta` | DATETIME | Fecha y hora automática al insertar |

La tabla se crea automáticamente la primera vez que arranca el contenedor de MariaDB gracias al volumen montado en `docker-compose.yml`:
```yaml
- ./DB/bd.sql:/docker-entrypoint-initdb.d/bd.sql:ro
```

---
