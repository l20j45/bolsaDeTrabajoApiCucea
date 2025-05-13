<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");

// Manejo específico para solicitudes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
// file: controllers/UserController.php
include_once '../models/Estudiante.php';

$estudiante = new Estudiante();
$request = $_SERVER["REQUEST_METHOD"];

switch ($request) {
    case 'GET':
        if (isset($_GET['registros']) and $_GET['registros'] == 'todos') {
            $stmt = $estudiante->read();
            $num = $stmt->rowCount();

            if ($num > 0) {
                $response = array("message" => "ok", "data" => array());
                while ($result = $stmt->fetch()) {
                    $response["data"][] = array(
                        "idEstudiante" => $result->idEstudiante,
                        "uuid" => $result->uuid,
                        "codigoAlumno" => $result->codigoAlumno,
                        "nombre" => $result->nombre,
                        "apellidoPaterno" => $result->apellidoPaterno,
                        "apellidoMaterno" => $result->apellidoMaterno,
                        "telefono" => $result->telefono,
                        "correo" => $result->correo,
                        "carrera" => $result->carrera,
                        "estatus" => $result->estatus,
                        "semestre" => $result->semestre,
                        "foto" => $result->foto,
                        "curriculum" => $result->curriculum,
                        "descripcion" => $result->descripcion,
                        "puestoDeseado" => $result->puestoDeseado
                    );
                }
                http_response_code(200);
                echo json_encode($response);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No users found."));
            }
            break;
        }
        else if (isset($_GET['idEstudiante']) and $_GET['idEstudiante'] != '') {
            $estudiante->idEstudiante = $_GET['idEstudiante'];
            $stmt = $estudiante->readOne();
            $num = $stmt->rowCount();

            if ($num > 0) {
                $response = array("message" => "ok", "data" => array());
                while ($result = $stmt->fetch()) {
                    $response["data"][] = array(
                        "idEstudiante" => $result->idEstudiante,
                        "uuid" => $result->uuid,
                        "codigoAlumno" => $result->codigoAlumno,
                        "nombre" => $result->nombre,
                        "apellidoPaterno" => $result->apellidoPaterno,
                        "apellidoMaterno" => $result->apellidoMaterno,
                        "telefono" => $result->telefono,
                        "correo" => $result->correo,
                        "carrera" => $result->carrera,
                        "estatus" => $result->estatus,
                        "semestre" => $result->semestre,
                        "foto" => $result->foto,
                        "curriculum" => $result->curriculum,
                        "descripcion" => $result->descripcion,
                        "puestoDeseado" => $result->puestoDeseado

                    );
                }

                http_response_code(200);
                echo json_encode($response);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No user found."));
            }
            break;
        }
        else if (isset($_GET['enum']) and $_GET['enum'] != '') {
            if ($_GET['enum'] == 'carrera'){

                $stmt = $estudiante->enumData('carrera');
            }
            else if ($_GET['enum'] == 'estatus'){

                $stmt = $estudiante->enumData('estatus');
            }
            $num = $stmt->rowCount();


            if ($num > 0) {
                $result = $stmt->fetch();
                if ($result) {
                    $string = str_replace(["enum(", ")"], "", $result->column_type);
                    $array = explode("','", str_replace("'", "", $string));
                    $array = array_filter($array);
                    foreach ($array as &$value) {
                        $value = mb_convert_encoding($value, 'UTF-8', 'auto');
                    }
                } else {
                    echo "No se encontraron resultados";
                }
                $response = array(
                    "message" => "ok",
                    "data" => $array
                );
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(200);
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Dato no encontrado."));
            }
            break;
        }

    case 'POST':

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recibir los datos del formulario
            $correo = isset($_POST['correo']) ? $_POST['correo'] : '';
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
            $apellidoPaterno = isset($_POST['apellidoPaterno']) ? $_POST['apellidoPaterno'] : '';
            $apellidoMaterno = isset($_POST['apellidoMaterno']) ? $_POST['apellidoMaterno'] : '';
            $codigoAlumno = isset($_POST['codigoAlumno']) ? $_POST['codigoAlumno'] : '';
            $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $estudiante->codigoAlumno = $correo;
            $estudiante->nombre = $nombre;
            $estudiante->apellidoPaterno = $apellidoPaterno;
            $estudiante->apellidoMaterno = $apellidoMaterno;
            $estudiante->telefono = $telefono;
            $estudiante->correo = $correo;
            $estudiante->password = $password;

            // Validar datos
            if (empty($correo) || empty($nombre) || empty($apellidoPaterno) ||
                empty($codigoAlumno) || empty($password)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Faltan campos requeridos']);
                exit;
            }

            if ($estudiante->emailExiste()) {
                http_response_code(409); // Conflict
                echo json_encode(array("message" => "Ya existe un usuario con este correo."));
            }
            else if ($estudiante->codigoAlumnoExiste()) {
                http_response_code(409); // Conflict
                echo json_encode(array("message" => "Ya existe un usuario con este codigo."));
            }else
            if ($estudiante->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "User was created."));
            }
        } else {
            // Método no permitido
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
        break;

    case 'PUT':
        $data = file_get_contents("php://input");
        $data = json_decode($data);
        if (!empty($data->idEstudiante) && !empty($data->nombre) && !empty($data->correo)) {
            $estudiante->idEstudiante = $data->idEstudiante;
            $estudiante->nombre = $data->nombre;
            $estudiante->apellidoPaterno = $data->apellidoPaterno;
            $estudiante->apellidoMaterno = $data->apellidoMaterno;
            $estudiante->telefono = $data->telefono;
            $estudiante->correo = $data->correo;
            $estudiante->carrera = $data->carrera;
            $estudiante->estatus = $data->estatus;
            $estudiante->semestre = $data->semestre;
            $estudiante->foto = $data->foto;
            $estudiante->curriculum = $data->curriculum;
            $estudiante->descripcion = $data->descripcion;
            $estudiante->puestoDeseado = $data->puestoDeseado;
            if ($estudiante->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "User was updated."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update user."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to update user. Data is incomplete."));
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        $estudiante->idEstudiante = $data->idEstudiante;

        if ($estudiante->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "User was deleted."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete user."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed."));
        break;
}