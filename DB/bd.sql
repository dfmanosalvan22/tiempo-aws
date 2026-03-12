CREATE DATABASE IF NOT EXISTS tiempo_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tiempo_app;

DROP TABLE IF EXISTS consultas;
CREATE TABLE consultas (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    ciudad         VARCHAR(100) NOT NULL,
    pais           VARCHAR(10)  NOT NULL DEFAULT '',
    latitud        DECIMAL(9,6) NOT NULL,
    longitud       DECIMAL(9,6) NOT NULL,
    tipo_consulta  ENUM('actual','horas','semana') NOT NULL,
    fecha_consulta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
