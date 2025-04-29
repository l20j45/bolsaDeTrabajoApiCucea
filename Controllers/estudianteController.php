<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
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
                        "estado" => $result->estado,
                        "semestre" => $result->semestre,
                        "foto" => $result->foto,
                        "curriculum" => $result->curriculum
                    );
                }
                http_response_code(200);
                echo json_encode($response);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No users found."));
            }
            break;
        } else {
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
                        "estado" => $result->estado,
                        "semestre" => $result->semestre,
                        "foto" => $result->foto,
                        "curriculum" => $result->curriculum
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

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->nombre) && !empty($data->correo)) {
            $estudiante->codigoAlumno = $data->codigoAlumno;
            $estudiante->nombre = $data->nombre;
            $estudiante->apellidoPaterno = $data->apellidoPaterno;
            $estudiante->apellidoMaterno = $data->apellidoMaterno;
            $estudiante->telefono = $data->telefono;
            $estudiante->correo = $data->correo;
            $estudiante->password = $data->password;

            // Verificar si el email ya existe
            if ($estudiante->emailExiste()) {
                http_response_code(409); // Conflict
                echo json_encode(array("message" => "User already exists with this email."));
            }
            if ($estudiante->codigoAlumnoExiste()) {
                http_response_code(409); // Conflict
                echo json_encode(array("message" => "User already exists with this email."));
            }else if ($estudiante->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "User was created."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create user."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create user. Data incomplete."));
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->idEstudiante) && !empty($data->nombre) && !empty($data->correo)) {
            $estudiante->idEstudiante = $data->idEstudiante;
            $estudiante->nombre = $data->nombre;
            $estudiante->apellidoPaterno = $data->apellidoPaterno;
            $estudiante->apellidoMaterno = $data->apellidoMaterno;
            $estudiante->telefono = $data->telefono;
            $estudiante->correo = $data->correo;
            $estudiante->carrera = $data->carrera;
            $estudiante->estado = $data->estado;
            $estudiante->semestre = $data->semestre;
            $estudiante->foto = $data->foto;
            $estudiante->curriculum = $data->curriculum;
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