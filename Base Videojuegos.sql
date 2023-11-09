CREATE TABLE Videojuegos (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(255),
    Genero VARCHAR(50),
    Desarrolladora VARCHAR(255),
	AnioSalida INT,
    Contraportada VARCHAR(255),
     Portada BLOB
);

CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    contrasenia VARCHAR(255) NOT NULL,
    rol VARCHAR(20) NOT NULL
);


INSERT INTO usuarios (nombre, contrasenia, rol) VALUES ('admin1', 'adminpass', 'administrador');

