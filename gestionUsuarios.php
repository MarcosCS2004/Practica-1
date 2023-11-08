<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion usuarios</title>
</head>
<style>
    body {
        background-image: url('http://localhost/Practica/Imagenes/FondosVideojuegos2.jpg'); 
        background-color:#d4d5da;
        background-attachment: fixed;
        background-position: center center; 
        background-repeat: no-repeat;
        color: white; 
        font-family: Arial, sans-serif;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center; 
    }
    #menu {
        display: flex;
        background-color: #333;
        padding: 10px;
        border-radius: 5px;
        width: 100%;
        justify-content: center;
        align-items: center;
    }

    #menu a {
        color: white;
        text-decoration: none;
        padding: 10px;
        margin: 0 10px;
    }

    #menu button {
        background-color: #d9534f;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        cursor: pointer;
    }

    #menu button:hover {
        background-color: #c9302c;
    }
    form{
        width:50%;
        font-size: 24px;
        padding: 20px;
        background-color: rgba(0, 0, 0, 0.7); 
        border-radius: 10px;
    }
</style>
<body>
<form method="post" id="menu">
        <a href="gestioBBDDAdmin.php">Gestion de BBDD</a>
    </form>
<form method="post">
        <p>Nombre: <input type="text" name="nombre" /></p>
        <p>contrasenia: <input type="text" name="contrasenia" /></p>
        <p>Rol: <select id="opciones" name="rol">
            <option value="limitado">Limitado</option>
            <option value="administrador">Administrador</option>
        </select></p>
        <button type="submit">Crear</button>
    </form>
</body>
<?php
    session_start(); 
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
        header('Location: inicioSesion.php'); // Si no hay sesión iniciada, redirige a la página de inicio de sesión
    }
    
    // Verifica el rol del usuario
    if ($_SESSION['rol'] !== 'administrador') {
        header('Location: gestioBBDDLimitado.php'); // Si el rol no es administrador, redirige a otra página
        exit(); //  no se procesa más contenido en esta página
    }
    //al igual que antes se comprueba que los datos se reciben de un post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //se guardan los datos introducidos mediante el formulario
    $nombre = $_POST['nombre'];
    $contrasenia = $_POST['contrasenia'];
    $rol = $_POST['rol'];
    //se comprueba que los datos sean validos
    if (empty($nombre) || empty($contrasenia)) {
        echo "Nombre y contraseña son campos obligatorios.";
    } else {
        if (strlen($nombre) < 3) {
            echo "El nombre debe tener al menos 3 caracteres.";
        } elseif (strlen($contrasenia) < 6) {
            echo "La contraseña debe tener al menos 6 caracteres.";
        } else {
            try {
                //se conecta con la BBDD
                $usuario = 'root';
                $con = new PDO('mysql:dbname=videojuegos;host=localhost;charset=utf8', $usuario);
                $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                //se ejecuta la consulta ,intrudciendole los valores intrducidos por el usuario
                $stmt = $con->prepare("INSERT INTO usuarios (nombre, contrasenia, rol) VALUES (:nombre, :contrasenia, :rol)");
                $stmt->bindValue(':nombre', $nombre);
                $stmt->bindValue(':contrasenia', $contrasenia);
                $stmt->bindValue(':rol', $rol);
                $stmt->execute();

                echo "Registro exitoso";
            } catch (PDOException $e) {
                echo "Error al insertar en la base de datos: " . $e->getMessage();
            }
        }
    }
}
?>


</html>
