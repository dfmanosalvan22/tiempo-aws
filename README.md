# FelipeApp — Aplicación del Tiempo

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
