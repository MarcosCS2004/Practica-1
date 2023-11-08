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

        table,
        th,
        td {
            border: 1px solid #ccc;
            color: black;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
            /* Centra el contenido de las celdas */
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
            text-decoration: none;
        }

        .boton-tabla:hover {
            background-color: #31b0d5;
        }
    </style>

</head>

<body>
    <form method="post" id="menu">
        <a href="gestionUsuarios.php">Gestion de usuarios</a>
        <button type="submit" name="cerrarSesion">Cerrar Sesión</button>
    </form>
    <br>
    <form method="post" enctype="multipart/form-data">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" id="nombre" /><br>

        <label for="genero">Género:</label>
        <input type="text" name="genero" id="genero" /><br>

        <label for="desarrolladora">Desarrolladora:</label>
        <input type="text" name="desarrolladora" id="desarrolladora" /><br>

        <label for="anio">Anio:</label>
        <input type="number" name="anio" id="anio"/><br>

        <label for="imagen">Contraportada:</label>
        <input type="file" name="imagen" id="imagen" /><br>

        <label for="blob">Portada:</label>
        <input type="file" name="blob" id="blob" /><br>

        <button type="submit" style="background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Subir</button>
    </form>
    <br>

    <?php
    session_start(); // Asegúrate de que se haya iniciado la sesión
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['rol'])) {
        header('Location: inicioSesion.php'); // Si no hay sesión iniciada, redirige a la página de inicio de sesión
    }
    
    // Verifica el rol del usuario
    if ($_SESSION['rol'] !== 'administrador') {
        header('Location: gestioBBDDLimitado.php'); // Si el rol no es administrador, redirige a otra página
        exit(); // Asegúrate de que no se procese más contenido en esta página
    }
    //cerrar sesion y redirigir a la pagina de inicio de sesion
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cerrarSesion'])) {
        
        session_destroy();
        header('Location: inicioSesion.php'); 
    }
    //este es el php mediante el cual se introducen los datos a la BBDD
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = $_POST['nombre'];
        $genero = $_POST['genero'];
        $desarrolladora = $_POST['desarrolladora'];
        $anio=$_POST['anio'];
        //los distintos controles de fallos dependiendo del campo en el que haya fallo
        if (empty($nombre) || empty($genero) || empty($desarrolladora)) {
            echo "Todos los campos son obligatorios.";
        } else {
            //la gestion de la introduccion de la imagen por ruta
            if (is_uploaded_file($_FILES['imagen']['tmp_name'])) {
                $nombreDirectorio = "Imagenes/";
                $nombreFichero = $_FILES['imagen']['name'];
                $completo = $nombreDirectorio . $nombreFichero;
                $tipo = $_FILES['imagen']['type'];
                //la gestion de la introduccion de la imagen tipo BLOB
                if (in_array($tipo, ['image/png', 'image/jpeg'])) {
                    if (is_dir($nombreDirectorio)) {
                        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $completo)) {
                            if (is_uploaded_file($_FILES['blob']['tmp_name'])) {
                                $blob = file_get_contents($_FILES['blob']['tmp_name']);
                                try {
                                    $usuario = 'root';
                                    $con = new PDO('mysql:dbname=videojuegos;host=localhost;charset=utf8', $usuario);
                                    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
                                    $stmt = $con->prepare("INSERT INTO videojuegos (nombre, genero, desarrolladora, AnioSalida, contraportada, portada) VALUES (:nombre, :genero, :desarrolladora, :AnioSalida, :contraportada, :portada)");
                                    $stmt->bindValue(':nombre', $nombre);
                                    $stmt->bindValue(':genero', $genero);
                                    $stmt->bindValue(':desarrolladora', $desarrolladora);
                                    $stmt->bindValue(':AnioSalida', $anio);
                                    $stmt->bindValue(':contraportada', $completo);
                                    $stmt->bindValue(':portada', $blob);
                                    $stmt->execute();
                                } catch (PDOException $e) {
                                    echo $e->getMessage();
                                }
                            } else {
                                echo "El archivo blob no se ha subido correctamente.";
                            }
                        } else {
                            echo "Error al subir la imagen.";
                        }
                    } else {
                        echo "El directorio de imágenes no existe.";
                    }
                } else {
                    echo "Tipo de archivo no permitido. Solo se permiten imágenes PNG y JPEG.";
                }
            } else {
                echo "Debes subir una imagen.";
            }
        }
    }
?>    

    <?php
    //La tabla con la que se muestran los datos .Es
    try {
        $usuario = 'root';
        $con = new PDO('mysql:dbname=videojuegos;host=localhost;charset=utf8', $usuario);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $con->prepare('SELECT * FROM videojuegos');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        echo '<table>';
        echo '<tr><th>ID</th><th>Nombre</th><th>Genero</th><th>Desarrolladora</th><th>Anio Salida</th><th>RutaImagen</th><th>Binario Imagen</th><th>Mas Informacion</th><th>Descargar Imagen</th></tr>';

        while ($prove = $stmt->fetch()) {
            $id = $prove['ID'];
            $nombre = $prove['Nombre'];
            $genero = $prove['Genero'];
            $desarrolladora = $prove['Desarrolladora'];
            $anio = $prove['AnioSalida'];
            $rutaImagen = $prove['Contraportada'];
            $BinarioImagen  = $prove['Portada'];
            $imgDescodificada = base64_encode($BinarioImagen);

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