<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carta de Videojuego</title>
    <style>
    body {
        background-image: url('http://localhost/Practica/Imagenes/FondoVideojuegos3.jpg'); 
        background-color: #08101b;
        background-attachment: fixed;
        background-position: center center; 
        background-repeat: no-repeat;
        color: white; 
        font-family: Arial, sans-serif;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center; 
        height: 100vh; 
        margin: 0;
    }

    #carta {
        width: 500px;
        background-color: rgba(255, 255, 255, 0.7);
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        margin-top: 100px;
    }

    #carta h2 {
    font-size: 24px;
    margin: 0;
    padding: 10px;
    color: #FF5733; 
    font-weight: bold; 
}

#carta p {
    font-size: 18px;
    margin: 0;
    padding: 10px;
    color: #347CFF;
    font-style: italic;
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
        a.volver-btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #007BFF; 
    color: #fff; 
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: background-color 0.3s ease; 
}

a.volver-btn:hover {
    background-color: #0056b3; 
}
</style>

</head>
<body>
    <?php
     session_start();
//se comprueba si el id que se ha pasado es valido
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    echo "No se proporcionó un ID válido.";
}
//se muestra la carta con los datos del ID seleccionado
try {
    $usuario = 'root';
    $con = new PDO('mysql:dbname=videojuegos;host=localhost;charset=utf8', $usuario);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $con->prepare('SELECT * FROM videojuegos WHERE ID=:ID');
    $stmt->bindValue(':ID', $id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    //se crea el div con la carta y los datos extras 
    echo "<div id=\"carta\">";

    while ($prove = $stmt->fetch()) {
            $nombre = $prove['Nombre'];
            $genero = $prove['Genero'];
            $desarrolladora = $prove['Desarrolladora'];
            $anio = $prove['AnioSalida'];
            $rutaImagen = $prove['Contraportada'];
            $BinarioImagen  = $prove['Portada'];

        echo"<h2>$nombre</h2>";
        echo "<img src='data:image/jpeg;base64," . base64_encode($BinarioImagen) . "' width='500' height='500'>";
        echo"<p>$anio</p>";
        echo"<p>$genero</p>";
        echo"<p>$desarrolladora</p>";
       

        // Verifica si el usuario ha iniciado sesión y su rol 
        //de esta forma sabe a que pagina redirigir al usuario al volver
        if (isset($_SESSION['usuario']) && isset($_SESSION['rol'])) {
            if ($_SESSION['rol'] === 'administrador') {
              echo"<a class='volver-btn' href='/Practica/gestioBBDDAdmin.php'>Volver</a>";
            } else {
                echo"<a class='volver-btn' href='/Practica/gestioBBDDLimitado.php'>Volver</a>";            }
        } 

        
    }

    echo "</div>";

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage() . "<br>";
}
?>
</body>
</html>
