DROP TABLE IF EXISTS [dbo].[Publicacion];
DROP TABLE IF EXISTS [dbo].[Tema];
DROP TABLE IF EXISTS [dbo].[Foro];
DROP TABLE IF EXISTS [dbo].[LineasCompra];
DROP TABLE IF EXISTS [dbo].[Compra];
DROP TABLE IF EXISTS [dbo].[Reserva];
DROP TABLE IF EXISTS [dbo].[CestaCompra];
DROP TABLE IF EXISTS [dbo].[ListaDeseosVideojuego];
DROP TABLE IF EXISTS [dbo].[ListaDeseos];
DROP TABLE IF EXISTS [dbo].[Review];
DROP TABLE IF EXISTS [dbo].[Oferta];
DROP TABLE IF EXISTS [dbo].[Noticia];
DROP TABLE IF EXISTS [dbo].[Videojuego];
DROP TABLE IF EXISTS [dbo].[Categoria];
DROP TABLE IF EXISTS [dbo].[Productora];
DROP TABLE IF EXISTS [dbo].[Usuario];

CREATE TABLE [dbo].[Usuario] (
  [id] INT IDENTITY(1,1) PRIMARY KEY,
  [nick] VARCHAR(255) NOT NULL UNIQUE,
  [nombre] VARCHAR(255),
  [apellidos] VARCHAR(255),
  [email] VARCHAR(255) NOT NULL UNIQUE,
  [password] VARCHAR(255),
  [fecha_nacimiento] DATE,
  [telefono] VARCHAR(15) NOT NULL UNIQUE,
  [rol] VARCHAR(255)
);

CREATE TABLE [dbo].[Productora](
  [id] INT IDENTITY(1,1) PRIMARY KEY,
  [nombre] VARCHAR(255) NOT NULL UNIQUE,
  [descripcion] TEXT,
  [imagen] VARCHAR(255),
  [web] VARCHAR(255)
);

CREATE TABLE [dbo].[Categoria] (
  [id] INT IDENTITY(1,1) PRIMARY KEY,
  [nombre] VARCHAR(255) NOT NULL UNIQUE,
  [descripcion] TEXT
);

CREATE TABLE [dbo].[Videojuego] (
  [id] INT IDENTITY(1,1) PRIMARY KEY,
  [titulo] VARCHAR(255) NOT NULL UNIQUE,
  [descripcion] TEXT,
  [fecha_lanzamiento] DATE,
  [plataforma] VARCHAR(255),
  [precio] DECIMAL(10,2),
  [imagen] VARCHAR(255),
  [productoraID] INT NOT NULL,
  [categoriaID] INT NOT NULL,
  FOREIGN KEY ([productoraID]) REFERENCES Productora([id]) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY ([categoriaID]) REFERENCES Categoria([id]) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE [dbo].[Noticia] (
  [id] INT IDENTITY(1,1) PRIMARY KEY,
  [titulo] VARCHAR(255),
  [fecha_public] DATE,
  [contenido] TEXT,
  [productoraID] INT NOT NULL,
  FOREIGN KEY ([productoraID]) REFERENCES Productora([id]) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE [dbo].[Oferta] (
  [id] INT IDENTITY(1,1) PRIMARY KEY,
  [nombre] VARCHAR(255) UNIQUE NOT NULL,
  [descuento] DECIMAL(5,2),
  [fecha_inicio] DATE,
  [fecha_fin] DATE,
  [videojuegoID] INT NOT NULL,
  [productoraID] INT NOT NULL,
  FOREIGN KEY ([videojuegoID]) REFERENCES Videojuego([id]) ON DELETE CASCADE ON UPDATE NO ACTION,
  FOREIGN KEY ([productoraID]) REFERENCES Productora([id]) ON DELETE NO ACTION ON UPDATE NO ACTION
);


CREATE TABLE [dbo].[Review] (
  [id] INT IDENTITY(1,1) PRIMARY KEY,
  [puntuacion] INT,
  [comentario] TEXT,
  [fecha] DATE,
  [usuarioID] INT NOT NULL,
  [videojuegoID] INT NOT NULL,
  FOREIGN KEY ([usuarioID]) REFERENCES Usuario([id]) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY ([videojuegoID]) REFERENCES Videojuego([id]) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE [dbo].[ListaDeseos] (
  [id] INT IDENTITY(1,1) PRIMARY KEY,
  [nombre] VARCHAR(255),
  [descripcion] TEXT,
  [usuarioID] INT NOT NULL,
  FOREIGN KEY ([usuarioID]) REFERENCES Usuario([id])
);

CREATE TABLE [dbo].[ListaDeseosVideojuego] (
 [listaDeseosID] INT NOT NULL,
 [videojuegoID] INT NOT NULL,
 FOREIGN KEY ([listaDeseosID]) REFERENCES ListaDeseos([id]) ON DELETE CASCADE ON UPDATE CASCADE,
 FOREIGN KEY ([videojuegoID]) REFERENCES Videojuego([id]) ON DELETE CASCADE ON UPDATE CASCADE,
 CONSTRAINT PK_ListaDeseosVideojuego PRIMARY KEY ([listaDeseosID],[videojuegoID])
);

CREATE TABLE [dbo].[CestaCompra] (
  [usuarioID] INT NOT NULL,
  [videojuegoID] INT NOT NULL,
  [fecha] DATE,
  FOREIGN KEY ([usuarioID]) REFERENCES Usuario([id]) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY ([videojuegoID]) REFERENCES Videojuego([id]) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT PK_CestaCompra PRIMARY KEY ([usuarioID],[videojuegoID])
);


CREATE TABLE [dbo].[Reserva] (
  [id] INT IDENTITY(1,1) PRIMARY KEY,
  [fecha] DATE,
  [fechaEntrega] DATE,
  [pagado] DECIMAL(10,2),
  [usuarioID] INT NOT NULL,
  [videojuegoID] INT NOT NULL,
  FOREIGN KEY ([usuarioID]) REFERENCES Usuario([id]) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY ([videojuegoID]) REFERENCES Videojuego([id]) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE [dbo].[Compra] (
  [id] INT IDENTITY(1,1) PRIMARY KEY,
  [fecha] DATE,
  [usuarioID] INT NOT NULL,
  FOREIGN KEY ([usuarioID]) REFERENCES Usuario([id]) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE [dbo].[LineasCompra] (
 [id] INT IDENTITY(1,1) NOT NULL,
 [importe] DECIMAL(10,2),
 [videojuegoID] INT NOT NULL,
 [cantidad] INT,
 [compraID] INT NOT NULL,
 FOREIGN KEY ([videojuegoID]) REFERENCES Videojuego([id]) ON DELETE CASCADE ON UPDATE CASCADE,
 FOREIGN KEY ([compraID]) REFERENCES Compra([id]) ON DELETE CASCADE ON UPDATE CASCADE,
 CONSTRAINT PK_LineasCompra PRIMARY KEY ([id],[compraID])
);


CREATE TABLE [dbo].[Foro] (
  [id] INT IDENTITY(1,1) PRIMARY KEY,
  [nombre] VARCHAR(255) NOT NULL
);

CREATE TABLE [dbo].[Tema] (
  [id] INT IDENTITY(1,1) PRIMARY KEY,
  [titulo] VARCHAR(255) NOT NULL,
  [foroID] INT NOT NULL,
  FOREIGN KEY ([foroID]) REFERENCES Foro([id]) ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE [dbo].[Publicacion](
  [id] INT IDENTITY(1,1) PRIMARY KEY,
  [text] TEXT NOT NULL,
  [temaID] INT NOT NULL,
  [usuarioID] INT NOT NULL,
  FOREIGN KEY ([temaID]) REFERENCES Tema([id]) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY ([usuarioID]) REFERENCES Usuario([id]) ON DELETE CASCADE ON UPDATE CASCADE
);