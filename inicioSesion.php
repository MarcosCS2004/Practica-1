<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Inicio de Sesión</title>
    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            text-align: center;
        }


        h1 {
            color: #007bff;
        }


        input,button {
            width: 90%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }


        input:hover,button:hover {
            border: 1px solid #007bff;
        }


        button {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            width: 30%;
        }

        button:hover {
            background-color: #0056b3; 
        }


        p {
            text-align: center;
            font-weight: bold;

            margin-bottom: 5px;
        }
        
    </style>
</head>
<body>
    <h1>Inicio de Sesión</h1>
    <form method="post">
        <p>Usuario:</p>
        <input type="text" name="usuario" />
        <p>Contraseña:</p>
        <input type="password" name="contrasenia" />
        <p><button type="submit">Iniciar Sesión</button></p>
    </form>
    <?php
    //se crea las variables resultado y resultado 2 para guardar los datos de la consulta
    $resultado = '';
    $resultado2 ='';
        //se recogen los datos del post del inicio de sesio y se guarda en variables
    if (isset($_POST['usuario']) && isset($_POST['contrasenia'])) {
        $user = $_POST['usuario'];
        $paswd = $_POST['contrasenia'];
        try {
            //se conecta a la tabla usuarios de la  BBDD 
            $usuario = 'root';
            $con = new PDO('mysql:dbname=videojuegos;host=localhost;charset=utf8', $usuario);
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $con->prepare("SELECT COUNT(*) FROM usuarios WHERE nombre = :usr AND contrasenia = :pass");
            $stmt2 = $con->prepare("SELECT rol FROM usuarios WHERE nombre = :usr AND contrasenia = :pass");
            $stmt->bindValue('usr', $user);
            $stmt->bindValue('pass', $paswd);
            $stmt->execute();
            $resultado = $stmt->fetchColumn();
            $stmt2->bindValue('usr', $user);
            $stmt2->bindValue('pass', $paswd);
            $stmt2->execute();
            $resultado2 = $stmt2->fetchColumn();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    //se comprueba el producto de los resultados 
    //dependiendo de esto se enviara a un pagina o otra 
    if ($resultado == 0) {
        echo 'contraseña o usuario incorrecto';
    } else if ($resultado == 1) {
        if ($resultado2 == 'administrador') {
            session_start();
            $_SESSION["usuario"]=$_POST["usuario"];
            $_SESSION["rol"]=$resultado2;
            header('Location: gestioBBDDAdmin.php');
        } else {
            session_start();
            $_SESSION["usuario"]=$_POST["usuario"];
            $_SESSION["rol"]=$resultado2;
            header('Location: gestioBBDDLimitado.php');
        }
    }
?>

</body>
</html>
