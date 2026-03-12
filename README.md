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

## API de OpenWeatherMap

Se utilizan tres endpoints de la API gratuita de OpenWeatherMap:

### 1. API de Geocodificación
Convierte el nombre de una ciudad en coordenadas (latitud y longitud).
```
GET /geo/1.0/direct?q={ciudad}&limit=1&appid={API_KEY}
```

### 2. Tiempo actual
Devuelve los datos meteorológicos del momento para unas coordenadas.
```
GET /data/2.5/weather?lat={lat}&lon={lon}&units=metric&lang=es&appid={API_KEY}
```

### 3. Previsión por horas y semanal
Devuelve 40 registros con datos cada 3 horas durante 5 días.
```
GET /data/2.5/forecast?lat={lat}&lon={lon}&units=metric&lang=es&appid={API_KEY}
```

### Parámetros comunes

| Parámetro | Valor | Descripción |
|-----------|-------|-------------|
| `units` | `metric` | Temperaturas en °C |
| `lang` | `es` | Descripciones del tiempo en español |
| `appid` | clave | API key de OpenWeatherMap |

### Gráficas con QuickChart

Las gráficas se generan usando **QuickChart.io**, un servicio gratuito que recibe la configuración del gráfico en la URL y devuelve una imagen PNG. No requiere JavaScript:

```php
$config = json_encode(['type' => 'line', 'data' => [...]]);
$url = 'https://quickchart.io/chart?c=' . urlencode($config);
// Se usa directamente en un <img src="...">
```

---

## Docker y docker-compose

La aplicación usa **dos contenedores** que se comunican a través de una red interna de Docker:

### Contenedor `php_app`
- Imagen base: `php:8.2-apache`
- Extensiones instaladas: `pdo`, `pdo_mysql`, `curl`
- Módulos de Apache activados: `ssl`, `rewrite`
- Expone los puertos 80 y 443
- Monta el código como volumen para ver cambios sin reconstruir

### Contenedor `mariadb`
- Imagen: `mariadb:10.11`
- Ejecuta `bd.sql` automáticamente la primera vez
- Los datos persisten en un volumen nombrado `datos_mariadb`
- Tiene un healthcheck que verifica que la BD está lista antes de arrancar PHP

### Comunicación entre contenedores
Dentro de la red Docker los contenedores se comunican por el nombre del servicio. Por eso en `config.php` el host de la BD es `mariadb` y no `localhost`.

### Comandos útiles

```bash
# Construir y arrancar
docker compose up -d --build

# Arrancar sin reconstruir
docker compose up -d

# Parar y eliminar contenedores
docker compose down

# Ver estado de los contenedores
docker compose ps

# Ver logs del contenedor PHP
docker compose logs php_app

# Arreglar permisos si hay error 403
docker compose exec php_app chmod -R 755 /var/www/html
docker compose exec php_app chown -R www-data:www-data /var/www/html
```

---

## Instalación en local

**1. Clona el repositorio:**
```bash
git clone https://github.com/dfmanosalvan22/tiempo-aws.git
cd tiempo-aws
```

**2. Añade tu API key en `config.php`:**
```php
define('API_KEY', 'TU_API_KEY_AQUI');
```
> Consigue una clave gratuita en https://openweathermap.org/api. Tarda hasta 2 horas en activarse.

**3. Arranca los contenedores:**
```bash
docker compose up -d --build
```

**4. Abre el navegador en:**
```
http://localhost
```

---

## Despliegue en AWS

### Infraestructura utilizada
- **Servicio:** AWS EC2
- **Instancia:** t2.micro (Free Tier)
- **Sistema operativo:** Ubuntu 24.04 LTS
- **IP elástica:** 34.206.249.154
- **Dominio:** dfmanosalvan.es (registrado en OVH)

### Paso 1 — Crear la VPC y subred
En AWS Academy la VPC por defecto no tiene subredes configuradas. Se creó una nueva desde VPC → Crear VPC → seleccionando la opción "VPC y mucho más":
- CIDR: `10.0.0.0/16`
- 1 subred pública
- Sin subredes privadas ni puertas de enlace NAT

### Paso 2 — Crear la instancia EC2
- AMI: Ubuntu Server 24.04 LTS
- Tipo: t2.micro
- Par de claves RSA en formato `.pem` para acceso SSH
- Grupo de seguridad con reglas de entrada para los puertos 22, 80 y 443

### Paso 3 — Asignar IP elástica
Se asignó una IP elástica desde EC2 → Red y seguridad → Direcciones IP elásticas y se asoció a la instancia para que la IP no cambie al reiniciar.

### Paso 4 — Instalar Docker
```bash
sudo apt update
sudo apt install -y ca-certificates curl
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
sudo usermod -aG docker ubuntu
```
> Versión instalada: **Docker 29.3.0**

### Paso 5 — Subir el código
Desde PowerShell en el ordenador local:
```powershell
scp -i "ruta/tiempo-key.pem" -r "ruta/tiempo_app_iaw" ubuntu@34.206.249.154:/home/ubuntu/
```

### Paso 6 — Arreglar permisos y arrancar
```bash
cd tiempo_app_iaw
docker compose up -d --build
docker compose exec php_app chmod -R 755 /var/www/html
docker compose exec php_app chown -R www-data:www-data /var/www/html
```

### Paso 7 — Configurar el dominio en OVH
En el panel de OVH → Zona DNS se modificaron los registros A:
- Subdominio `@` → `34.206.249.154`
- Subdominio `www` → `34.206.249.154`

### Paso 8 — Arranque automático al iniciar la instancia
```bash
sudo crontab -e
```
Línea añadida:
```
@reboot sleep 15 && systemctl restart docker && cd /home/ubuntu/tiempo_app_iaw && /usr/bin/docker compose up -d
```

---

## Configuración SSL/HTTPS

Se usó **Certbot** con **Let's Encrypt** para obtener un certificado SSL gratuito.

### Instalación de Certbot
```bash
sudo apt install -y certbot python3-certbot-apache
```

### Generación del certificado
Hay que parar Docker primero para liberar el puerto 80:
```bash
docker compose stop
sudo certbot certonly --standalone -d dfmanosalvan.es -d www.dfmanosalvan.es
```

El certificado queda en:
- `/etc/letsencrypt/live/dfmanosalvan.es/fullchain.pem`
- `/etc/letsencrypt/live/dfmanosalvan.es/privkey.pem`

> Válido hasta el **09/06/2026**. La renovación es automática.

### Configuración de Apache
Se creó `apache/ssl.conf` con dos VirtualHost:
- **Puerto 80:** redirige automáticamente a HTTPS
- **Puerto 443:** sirve la aplicación con el certificado SSL

Los certificados se montan en el contenedor como volumen de solo lectura:
```yaml
volumes:
  - /etc/letsencrypt:/etc/letsencrypt:ro
```

---

## Problemas conocidos y soluciones

### Error 403 Forbidden al acceder a la web
**Causa:** Apache no tiene permisos para leer los archivos de `/var/www/html`.

**Solución:**
```bash
docker compose exec php_app chmod -R 755 /var/www/html
docker compose exec php_app chown -R www-data:www-data /var/www/html
```

### Los puertos no se mapean al reiniciar el laboratorio de AWS Academy
**Causa:** Docker queda en estado inconsistente al reiniciar la instancia y no mapea correctamente los puertos 80 y 443.

**Solución manual:**
```bash
sudo systemctl restart docker
docker compose up -d
```
**Solución permanente:** el cron job `@reboot` descrito en el despliegue lo resuelve automáticamente.

### Puerto 80 ocupado por Apache de la instancia
**Causa:** Ubuntu trae Apache instalado por defecto y ocupa el puerto 80 antes de que Docker pueda usarlo.

**Solución:**
```bash
sudo systemctl stop apache2
sudo systemctl disable apache2
```

### La ciudad no se encuentra con una API key recién creada
**Causa:** Las API keys gratuitas de OpenWeatherMap tardan hasta 2 horas en activarse.

**Solución:** Esperar y volver a intentarlo pasado ese tiempo.

---

## URLs de entrega

- **Repositorio GitHub:** https://github.com/dfmanosalvan22/tiempo-aws
- **Aplicación:** https://dfmanosalvan.es
