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
