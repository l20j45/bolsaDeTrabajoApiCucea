<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;

$secretKey = '912aa7f9288261b46105ea2b9c9e1ca15d4bd75279011badf8c962ef468e32933ae571ff175f958692e6b2f7c0a9d4b8f3d187ff421d8cd31ce1138c6cfa593cc09f05a6b47df895e2e8e7ae25463424f5f660a12cf8120314c9b509b0f31194d764172af0ad11529f2e8939ab62f8f09102b9bbfaa17bfe422d5d7c1271c3513e6cf88c91146c54aabc1f32e6de132363301d5d0e38bd9fde532e5881468fd6f799671dfdea0c1f02a03773f042b3d3fc032f18751d303f46d800b2d3ed6e8abd4ec7f0c713ddb42cc340783e3bedee69c0a312b8925235b2c4cce802998f621d052e3ad5e70da2e8524407b18a6d6a2886938f17498bc70ac1eb8b68f36de6';

function generarJWT($usuarioId) {
    $payload = [
        'iss' => 'bolsaUdg.com',
        'iat' => time(),
        'exp' => time() + (60 * 60), // Expira en una hora
        'sub' => $usuarioId
    ];
    return JWT::encode($payload, $secretKey, 'HS256');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioId = $_POST['idEstudiante'] ?? null;
    if ($usuarioId) {
        echo json_encode(['token' => generarJWT($usuarioId)]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Falta usuario_id']);
    }
}
?>