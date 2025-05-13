<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

include_once '../models/Personal.php';

$secretKey = 'tu_clave_secreta';

$personal = new Personal();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $correo = isset($_POST['correo']) ? $_POST['correo'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';


    if (empty($correo) || empty($password)) {
        http_response_code(400);
        echo json_encode(["message" => "Correo y contraseña requeridos."]);
        exit;
    }

    if ($personal->login($correo, $password)) {
        // Usuario autenticado, generar JWT
        $payload = [
            'iss' => 'bolsaUdg.com',
            'iat' => time(),
            'exp' => time() + (60 * 60), // Expira en 1 hora
            'sub' => $personal->idTrabajador
        ];
        $jwt = JWT::encode($payload, $secretKey, 'HS256');
        $stmt = $personal->readOne();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $response = array("message" => "Login exitoso", "data" => array(), "token" => $jwt);
            while ($result = $stmt->fetch()) {
                $response["data"][] = array(
                    "idTrabajador" => $result->idTrabajador,
                    "uuid" => $result->uuid,
                    "codigoPersonal" => $result->codigoPersonal,
                    "nombre" => $result->nombre,
                    "apellidoPaterno" => $result->apellidoPaterno,
                    "apellidoMaterno" => $result->apellidoMaterno,
                    "telefono" => $result->telefono,
                    "correo" => $result->correo,
                );
            }

            http_response_code(200);
            echo json_encode($response);
        }
    } else {
        http_response_code(401);
        echo json_encode(["message" => "Credenciales incorrectas."]);
    }
}
?>