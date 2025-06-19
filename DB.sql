-- Tabla para Usuarios del sistema
CREATE TABLE jjjc_usuario(
usuario_id SERIAL PRIMARY KEY,
usuario_nom1 VARCHAR(50) NOT NULL,
usuario_nom2 VARCHAR(50) NOT NULL,
usuario_ape1 VARCHAR(50) NOT NULL,
usuario_ape2 VARCHAR(50) NOT NULL,
usuario_tel INT NOT NULL, 
usuario_direc VARCHAR(150) NOT NULL,
usuario_dpi VARCHAR(13) NOT NULL,
usuario_correo VARCHAR(100) NOT NULL,
usuario_contra LVARCHAR(1056) NOT NULL,
usuario_token LVARCHAR(1056) NOT NULL,
usuario_fecha_creacion DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
usuario_fecha_contra DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
usuario_fotografia LVARCHAR(2056),
usuario_situacion SMALLINT DEFAULT 1
);

-- Tabla para Aplicaciones
CREATE TABLE jjjc_aplicacion(
app_id SERIAL PRIMARY KEY,
app_nombre_largo VARCHAR(250) NOT NULL,
app_nombre_mediano VARCHAR(150) NOT NULL,
app_nombre_corto VARCHAR(50) NOT NULL,
app_fecha_creacion DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
app_situacion SMALLINT DEFAULT 1
);

-- Tabla para Permisos
CREATE TABLE jjjc_permiso(
permiso_id SERIAL PRIMARY KEY, 
permiso_app_id INT NOT NULL,
permiso_nombre VARCHAR(150) NOT NULL,
permiso_clave VARCHAR(250) NOT NULL,
permiso_desc VARCHAR(250) NOT NULL,
permiso_fecha DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
permiso_situacion SMALLINT DEFAULT 1,
FOREIGN KEY (permiso_app_id) REFERENCES jjjc_aplicacion(app_id)
);

-- Tabla para Asignación de Permisos
CREATE TABLE jjjc_asig_permisos(
asignacion_id SERIAL PRIMARY KEY,
asignacion_usuario_id INT NOT NULL,
asignacion_app_id INT NOT NULL,
asignacion_permiso_id INT NOT NULL,
asignacion_fecha DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
asignacion_quitar_fechaPermiso DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
asignacion_usuario_asigno INT NOT NULL,
asignacion_motivo VARCHAR(250) NOT NULL,
asignacion_situacion SMALLINT DEFAULT 1,
FOREIGN KEY (asignacion_usuario_id) REFERENCES jjjc_usuario(usuario_id),
FOREIGN KEY (asignacion_app_id) REFERENCES jjjc_aplicacion(app_id),
FOREIGN KEY (asignacion_permiso_id) REFERENCES jjjc_permiso(permiso_id)
);

-- Tabla para Rutas
CREATE TABLE jjjc_rutas(
ruta_id SERIAL PRIMARY KEY,
ruta_app_id INT NOT NULL,
ruta_nombre LVARCHAR(1056) NOT NULL,
ruta_descripcion VARCHAR(250) NOT NULL,
ruta_situacion SMALLINT DEFAULT 1,
FOREIGN KEY (ruta_app_id) REFERENCES jjjc_aplicacion(app_id)
);

-- Tabla para Historial de Actividad
CREATE TABLE jjjc_historial_act(
historial_id SERIAL PRIMARY KEY,
historial_usuario_id INT NOT NULL,
historial_fecha DATETIME YEAR TO MINUTE,
historial_ruta INT NOT NULL,
historial_ejecucion LVARCHAR(1056) NOT NULL,
historial_status SMALLINT,
historial_situacion SMALLINT DEFAULT 1,
FOREIGN KEY (historial_usuario_id) REFERENCES jjjc_usuario(usuario_id),
FOREIGN KEY (historial_ruta) REFERENCES jjjc_rutas(ruta_id)
);


-- Tabla para Personal para la Dotación 
CREATE TABLE jjjc_personal_dot(
per_id SERIAL PRIMARY KEY,
per_nom1 VARCHAR(50) NOT NULL,
per_nom2 VARCHAR(50) NOT NULL,
per_ape1 VARCHAR(50) NOT NULL,
per_ape2 VARCHAR(50) NOT NULL,
per_dpi VARCHAR(13) NOT NULL,
per_tel INT NOT NULL,
per_direc VARCHAR(150) NOT NULL,
per_correo VARCHAR(100) NOT NULL,
per_puesto VARCHAR(100) NOT NULL,
per_area VARCHAR(100) NOT NULL,
per_fecha_ing DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
per_situacion SMALLINT DEFAULT 1
);

-- Tabla para dotacion de la Industria Militar de Guatemala
CREATE TABLE jjjc_dot_img(
prenda_id SERIAL PRIMARY KEY,
prenda_nombre VARCHAR(100) NOT NULL,
prenda_desc VARCHAR(250),
prenda_fecha_crea DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
prenda_situacion SMALLINT DEFAULT 1
);

-- Tabla para Tallas
CREATE TABLE jjjc_tallas_dot(
talla_id SERIAL PRIMARY KEY,
talla_nombre VARCHAR(20) NOT NULL,
talla_desc VARCHAR(100),
talla_categoria VARCHAR(50),
talla_tipo VARCHAR(30),
talla_situacion SMALLINT DEFAULT 1
);

-- Tabla para Inventario de la Dotación
CREATE TABLE jjjc_inv_dot(
inv_id SERIAL PRIMARY KEY,
inv_prenda_id INT NOT NULL,
inv_talla_id INT NOT NULL,
inv_cant_disp INT NOT NULL DEFAULT 0,
inv_cant_total INT NOT NULL DEFAULT 0,
inv_fecha_ing DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
inv_lote VARCHAR(50),
inv_observ VARCHAR(250),
inv_situacion SMALLINT DEFAULT 1,
FOREIGN KEY (inv_prenda_id) REFERENCES jjjc_dot_img(prenda_id),
FOREIGN KEY (inv_talla_id) REFERENCES jjjc_tallas_dot(talla_id)
);

-- Tabla para Pedidos de la Dotación
CREATE TABLE jjjc_ped_dot(
ped_id SERIAL PRIMARY KEY,
ped_per_id INT NOT NULL,
ped_prenda_id INT NOT NULL,
ped_talla_id INT NOT NULL,
ped_cant_sol INT NOT NULL,
ped_fecha_sol DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
ped_observ VARCHAR(250),
ped_estado VARCHAR(20) DEFAULT 'PENDIENTE',
ped_situacion SMALLINT DEFAULT 1,
FOREIGN KEY (ped_per_id) REFERENCES jjjc_personal_dot(per_id),
FOREIGN KEY (ped_prenda_id) REFERENCES jjjc_dot_img(prenda_id),
FOREIGN KEY (ped_talla_id) REFERENCES jjjc_tallas_dot(talla_id)
);

-- Tabla para Entregas de  la Dotación
CREATE TABLE jjjc_ent_dot(
ent_id SERIAL PRIMARY KEY,
ent_per_id INT NOT NULL,
ent_ped_id INT NOT NULL,
ent_inv_id INT NOT NULL,
ent_cant_ent INT NOT NULL,
ent_fecha_ent DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
ent_usuario_ent INT NOT NULL,
ent_observ VARCHAR(250),
ent_situacion SMALLINT DEFAULT 1,
FOREIGN KEY (ent_per_id) REFERENCES jjjc_personal_dot(per_id),
FOREIGN KEY (ent_ped_id) REFERENCES jjjc_ped_dot(ped_id),
FOREIGN KEY (ent_inv_id) REFERENCES jjjc_inv_dot(inv_id),
FOREIGN KEY (ent_usuario_ent) REFERENCES jjjc_usuario(usuario_id)
);

-- Tabla para Control Anual de la Dotacion 
CREATE TABLE jjjc_dot_anual(
dot_anual_id SERIAL PRIMARY KEY,
dot_anual_per_id INT NOT NULL,
dot_anual_anio INT NOT NULL,
dot_anual_cant_ent INT NOT NULL DEFAULT 0,
dot_anual_fecha_ult_ent DATETIME YEAR TO SECOND,
dot_anual_situacion SMALLINT DEFAULT 1,
FOREIGN KEY (dot_anual_per_id) REFERENCES jjjc_personal_dot(per_id)
);


INSERT INTO jjjc_usuario (usuario_id,usuario_nom1,usuario_nom2,usuario_ape1,usuario_ape2,usuario_tel,usuario_direc,
usuario_dpi,usuario_correo,usuario_contra,usuario_token,usuario_fecha_creacion,usuario_fecha_contra,
usuario_fotografia,usuario_situacion) VALUES(1,'Jose','Jose','Juarezq','Juarez',42189352,'Zona 13','3749310030963',
'josesoteca584@gmail.com','12345678',
'6853110657a8d','2025-06-18 19:18:30','2025-06-18 00:00:00','storage/fotosUsuarios/3749310030101.jpg',1)
GO

INSERT INTO jjjc_aplicacion (app_id,app_nombre_largo,app_nombre_mediano,app_nombre_corto,app_fecha_creacion,app_situacion) 
VALUES(1,'Dotacion','Dotacion','DOTACION','2025-06-18 20:53:31',1)
GO


INSERT INTO jjjc_permiso (permiso_id,permiso_app_id,permiso_nombre,permiso_clave,permiso_desc,permiso_fecha,permiso_situacion) 
VALUES(1,1,'Admin','ADMIN','Admin','2025-06-18 21:09:47',1)
GO

INSERT INTO jjjc_permiso (asignacion_id,asignacion_usuario_id,asignacion_app_id,asignacion_permiso_id,asignacion_fecha,asignacion_quitar_fechapermiso,asignacion_usuario_asigno,asignacion_motivo,asignacion_situacion) 
VALUES(1,1,1,1,'2025-06-18 21:10:11','2025-06-18 21:10:11',1,'Xcdsf',1)
GO

