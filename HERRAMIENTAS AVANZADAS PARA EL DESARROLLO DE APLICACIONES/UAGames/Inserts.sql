-- Insertar productoras
INSERT INTO productora (nombre) VALUES 
('EA'),
('Respawn'),
('EpicGames'),
('Ubisoft'),
('Activision'),
('Rockstar Games');

-- Insertar categorías
INSERT INTO categoria (nombre) VALUES 
('Acción'),
('Aventura'),
('Deportes'),
('Estrategia'),
('RPG'),
('Simulación');

-- Insertar videojuegos
INSERT INTO videojuego (titulo, productoraID, categoriaID, fecha_lanzamiento, precio, imagen, descripcion, plataforma) VALUES 
('FIFA 23', 1, 3, '2023-09-28', 59.99, 'assets/imagenes/videojuegos/Fifa23.jpg', 'Juego de futbol', 'ps5'),
('Battlefield 2042', 1, 1, '2021-10-22', 69.99, 'assets/imagenes/videojuegos/Battlefield2042.jpg', 'Juego de guerra moderna', 'pc'),
('Apex Legends', 2, 1, '2019-02-04', 49.99, 'assets/imagenes/videojuegos/ApexLegends.jpg', 'Juego shooter primera persona', 'pc'),
('Fortnite', 3, 1, '2017-07-25', 49.99, 'assets/imagenes/videojuegos/Fortnite.jpg', 'Juego shooter tercera persona', 'xbox'),
('Assassin''s Creed Valhalla', 4, 5, '2020-11-10', 59.99, 'assets/imagenes/videojuegos/AssassinsCreedValhalla.jpg', 'Modo historia', 'pc'),
('Call of Duty: Modern Warfare', 5, 1, '2019-10-25', 59.99, 'assets/imagenes/videojuegos/CODModernWarfare.jpg', 'Modo historia', 'Ps5'),
('Grand Theft Auto V', 6, 1, '2013-09-17', 29.99, 'assets/imagenes/videojuegos/GTAV.jpg', 'No recomendado para menores de 13', 'pc');


-- Insertar ofertas
INSERT INTO oferta (nombre, descuento, fecha_inicio, fecha_fin, videojuegoID, productoraID) VALUES
('Oferta de lanzamiento FIFA 23', 10, '2023-09-28', '2023-10-05', 1, 1),
('Oferta especial Battlefield 2042', 20, '2022-01-01', '2022-01-07', 2, 1),
('Oferta fin de año Apex Legends', 50, '2023-12-01', '2023-12-31', 3, 2),
('Oferta de verano Fortnite', 30, '2023-06-01', '2023-06-30', 4, 3),
('Oferta Black Friday Assassin''s Creed Valhalla', 40, '2023-11-24', '2023-11-26', 5, 4),
('Oferta Halloween Call of Duty: Modern Warfare', 25, '2023-10-27', '2023-10-31', 6, 5),
('Oferta de Navidad Grand Theft Auto V', 15, '2023-12-24', '2023-12-26', 7, 6);


--- Insertar usuarios
INSERT INTO usuario (nick, nombre, apellidos, email, password, fecha_nacimiento, telefono, admin) VALUES 
('cgsg4', 'Clara', 'Gonzalez', 'cgsg4@gcloud.ua.es', '1234', '1994-03-09', '123456789', 0),
('admin', 'Admin', 'Admin', 'admin@gcloud.ua.es', 'admin', '1994-03-09', '123456788', 1),
('aitorMaster', 'Aitor', 'Menta', 'aitor@gcloud.ua.es', '1234', '2003-02-10', '123456787', 0);

-- Insertar reviews
INSERT INTO review (puntuacion, comentario, fecha, usuarioID, videojuegoID) VALUES
(1, 'lo de siempre, no innovan', '2023-05-14', 1, 5);

--Insertar compra