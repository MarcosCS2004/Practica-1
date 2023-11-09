<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Limitada</title>
    <style>
    body {
        background-image: url('imagenes/FondoVideojuegos1.jpg');
        background-size: cover;
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

    p {
        text-align: center;
        font-size: 24px;
        padding: 20px;
        border-radius: 10px;
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

    form {
        width: 50%;
        font-size: 24px;
        padding: 20px;
        background-color: rgba(0, 0, 0, 0.7);
        border-radius: 10px;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    table, th, td {
        border: 1px solid #ccc;
        color: black;
    }

    th, td {
        padding: 10px;
        text-align: center;
    }

    th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    tr:nth-child(odd) {
        background-color: #f5f5f5;
    }

    tr:nth-child(even) {
        background-color: #e0e0e0;
    }

    .boton-tabla {
        background-color: #5bc0de;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 5px 10px;
        cursor: pointer;
        text-decoration:none;
        margin-bottom:15px;
    }


    .boton-tabla:hover {
        background-color: #31b0d5;
    }
</style>
</head>
<body>
    <form method="post"  id="menu"> 
    <?php
        session_start();
        $usuario=$_SESSION['usuario'];
        echo "<p>Usuario: $usuario</p>";
        ?>
        <button  type="submit" name="cerrarSesion">Cerrar Sesión</button>
    </form>



<?php

// Si no hay sesión iniciada, redirige a la página de inicio de sesión
if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
    header('Location: inicioSesion.php'); 
}
//se comprueba que el dato enviado es desde un post  y que se ha pulsado cerrar sesion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cerrarSesion'])) {
    session_start();
    session_destroy();
    header('Location: inicioSesion.php'); 
}

try {
    //conexion con la tabla de la base de datos 
    $usuario = 'root';
    $con = new PDO('mysql:dbname=videojuegos;host=localhost;charset=utf8', $usuario);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //consulta a la tabla 
    $stmt = $con->prepare('SELECT * FROM videojuegos');
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    //creacion de la tabla en php para mostrar los datos de sql
    echo '<table>';
    echo '<tr><th>ID</th><th>Nombre</th><th>Genero</th><th>Desarrolladora</th><th>Anio Salida</th><th>Contraportada</th><th>Portada</th><th>Mas Informacion</th><th>Descargar Imagen</th></tr>';

    while ($prove = $stmt->fetch()) {
        //se guardan los valores de la consultas en estas variables
        $id = $prove['ID'];
        $nombre = $prove['Nombre'];
        $genero = $prove['Genero'];
        $desarrolladora = $prove['Desarrolladora'];
        $anio = $prove['AnioSalida'];
        $rutaImagen = $prove['Contraportada'];
        $BinarioImagen  = $prove['Portada'];
        $imgDescodificada = base64_encode($BinarioImagen);
        //se crea un fila con cada dato de la BBDD mientras se recorre la consulta
        echo '<tr>';
        echo "<td>$id</td><td>$nombre</td><td>$genero</td><td>$desarrolladora</td><td>$anio</td>"; 
        echo "<td><img src='$rutaImagen' width='200' height='200'></td>";
        echo "<td><img src='data:image/jpeg;base64," . $imgDescodificada . "' width='200' height='200'/></td>";
        echo "<td><a href='videojuegos/$id' class='boton-tabla'>Mostrar más</a></td>";
        echo "<td><a href='$rutaImagen' download='$rutaImagen' class='boton-tabla'>Descargar archivo</a></td>";
        echo '</tr>';
    }

    echo '</table>';
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage() . "<br>";
}
?>

</body>
</html>
