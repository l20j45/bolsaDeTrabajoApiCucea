<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");

// Manejo específico para solicitudes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
// file: controllers/UserController.php
include_once '../models/Idioma.php';

$idiomas = new Idioma();

$request = $_SERVER["REQUEST_METHOD"];

switch ($request) {
    case 'GET':
        if (isset($_GET['idiomas']) and $_GET['idiomas'] == 'todos') {
            $stmt = $idiomas->readTodosLosIdiomas();
            $num = $stmt->rowCount();
            if ($num > 0) {
                $response = array("message" => "ok", "data" => array());
                while ($result = $stmt->fetch()) {
                    $response["data"][] = array(
                        "idIdioma" => $result->idIdiomasAdicionales,
                        "nombresIdiomasAdicionales" => $result->nombreIdiomasAdicionales
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

            $idiomas->idEstudiante = $_GET['idEstudiante'];
            $stmt = $idiomas->readIdiomasDelUsuario();
            $num = $stmt->rowCount();

            if ($num > 0) {
                $response = array("message" => "ok", "data" => array());
                while ($result = $stmt->fetch()) {
                    $response["data"][] = array(
                        "idIdioma" => $result->idIdiomasAdicionales,
                        "nombresIdiomasAdicionales" => $result->nombreIdiomasAdicionales
                    );
                }

                http_response_code(200);
                echo json_encode($response);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No user found."));
            }
            break;
        } else if (isset($_GET['idioma']) and $_GET['idioma'] != '') {
            $idiomas->nombreIdiomasAdicionales = $_GET['idioma'];
            $stmt = $idiomas->readBuscarIdioma();
            $num = $stmt->rowCount();
            if ($num > 0) {
                $response = array("message" => "ok", "data" => array());
                while ($result = $stmt->fetch()) {
                    $response["data"][] = array(
                        "idIdioma" => $result->idIdiomasAdicionales,
                        "nombresIdiomasAdicionales" => $result->nombreIdiomasAdicionales
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            exit;
        }
        $data = file_get_contents("php://input");
        $data = json_decode($data);
        $idEstudiante = $data->idEstudiante;

        $idiomas->idEstudiante = $idEstudiante;
        $data->listas = implode(',', $data->valores);
        $idiomas->listaDeIdiomas = $data->listas;
        if ($idiomas->listaDeIdiomas == '') {
            $idiomas->reset();
            http_response_code(200);
            echo json_encode(['success' => false, 'message' => 'Se borraron todas las habilidades.']);
            exit;
        }
        $delete = $idiomas->delete();

        foreach ($data->valores as $idioma) {

            if (empty($idioma) || $idEstudiante === null) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Faltan campos requeridos: idioma e idEstudiante.']);
                exit;
            }

            $idiomas->nombreIdiomasAdicionales = $idioma;
            $idiomaCreadoId = $idiomas->createOne();
            if ($idiomaCreadoId["success"]) {
                $idiomas->idIdioma = $idiomaCreadoId["extras"];
                if ($idiomas->createRelation()) {
                    http_response_code(201);
                    echo json_encode(['success' => true, 'message' => 'Idioma y relación creados exitosamente.']);
                } else {
                    http_response_code(503);
                    echo json_encode(['success' => false, 'message' => 'No se pudo crear la relación pero si el idioma.']);
                }
            } elseif ($idiomaCreadoId["success"] == false) {
                $idiomas->idIdioma = $idiomaCreadoId["extras"];
                if ($idiomas->createRelation()) {
                    http_response_code(201);
                    echo json_encode(['success' => true, 'message' => 'Relación creada exitosamente y el idioma si existia.']);
                } else {
                    http_response_code(503);
                    echo json_encode(['success' => false, 'message' => 'No se pudo crear la relación el idioma si existia, pero no se creo la relacion.']);
                }
            } else {
                http_response_code(503);
                echo json_encode(['success' => false, 'message' => 'No se pudo crear el idioma.']);
            }
        }
        break;

//    case 'PUT':
//        $data = file_get_contents("php://input");
//        $data = json_decode($data);
//        if (!empty($data->idEstudiante) && !empty($data->nombre) && !empty($data->correo)) {
//            $estudiante->idEstudiante = $data->idEstudiante;
//            $estudiante->nombre = $data->nombre;
//            $estudiante->apellidoPaterno = $data->apellidoPaterno;
//            $estudiante->apellidoMaterno = $data->apellidoMaterno;
//            $estudiante->telefono = $data->telefono;
//            $estudiante->correo = $data->correo;
//            $estudiante->carrera = $data->carrera;
//            $estudiante->estatus = $data->estatus;
//            $estudiante->semestre = $data->semestre;
//            $estudiante->foto = $data->foto;
//            $estudiante->curriculum = $data->curriculum;
//            $estudiante->descripcion = $data->descripcion;
//            $estudiante->puestoDeseado = $data->puestoDeseado;
//            if ($estudiante->update()) {
//                http_response_code(200);
//                echo json_encode(array("message" => "User was updated."));
//            } else {
//                http_response_code(503);
//                echo json_encode(array("message" => "Unable to update user."));
//            }
//        } else {
//            http_response_code(400);
//            echo json_encode(array("message" => "Unable to update user. Data is incomplete."));
//        }
//        break;
//
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        $idiomas->idEstudiante = $data->idEstudiante;
        $idiomas->listaDeIdiomas = $data->listaDeIdiomas;

        if ($idiomas->delete()) {
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