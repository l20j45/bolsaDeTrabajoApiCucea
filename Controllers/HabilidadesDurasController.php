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

include_once '../models/HabilidadesDuras.php';


$habilidadDura = new HabilidadesDuras();

$request = $_SERVER["REQUEST_METHOD"];

switch ($request) {
    case 'GET':
        if (isset($_GET['habilidadesDuras']) and $_GET['habilidadesDuras'] == 'todas') {
            $stmt = $habilidadDura->readTodosLasHabilidades();
            $num = $stmt->rowCount();
            if ($num > 0) {
                $response = array("message" => "ok", "data" => array());
                while ($result = $stmt->fetch()) {
                    $response["data"][] = array(
                        "idHabilidadesDuras" => $result->idHabilidadesDuras,
                        "nombresHabilidadesDuras" => $result->nombreHabilidadesDuras
                    );
                }
                http_response_code(200);
                echo json_encode($response);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No users found."));
            }
            break;
        } else if (isset($_GET['idEstudiante']) and $_GET['idEstudiante'] != '') {

            $habilidadDura->idEstudiante = $_GET['idEstudiante'];
            $stmt = $habilidadDura->readHabilidadesDurasDelUsuario();
            $num = $stmt->rowCount();

            if ($num > 0) {
                $response = array("message" => "ok", "data" => array());
                while ($result = $stmt->fetch()) {
                    $response["data"][] = array(
                        "idHabilidadesDuras" => $result->idHabilidadesDuras,
                        "nombresHabilidadesDuras" => $result->nombreHabilidadesDuras
                    );
                }

                http_response_code(200);
                echo json_encode($response);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No user found."));
            }
            break;
        } else if (isset($_GET['habilidad']) and $_GET['habilidad'] != '') {
            $habilidadDura->nombreHabilidadesDuras = $_GET['habilidad'];
            $stmt = $habilidadDura->readBuscarHabilidad();
            $num = $stmt->rowCount();
            if ($num > 0) {
                $response = array("message" => "ok", "data" => array());
                while ($result = $stmt->fetch()) {
                    $response["data"][] = array(
                        "idHabilidadesDuras" => $result->idHabilidadesDuras,
                        "nombresHabilidadesDuras" => $result->nombreHabilidadesDuras
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

        $habilidadDura->idEstudiante = $idEstudiante;
        $data->listas = implode(',', $data->valores);
        $habilidadDura->listaDeHabilidadesDuras = $data->listas;
        if ($habilidadDura->listaDeHabilidadesDuras == '') {
            $habilidadDura->reset();
            http_response_code(200);
            echo json_encode(['success' => false, 'message' => 'Se borraron todas las habilidades.']);
            exit;
        }
        $delete = $habilidadDura->delete();

        foreach ($data->valores as $habilidadDuraItem) {

            if (empty($habilidadDuraItem) || $idEstudiante === null) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Faltan campos requeridos: habilidad e idEstudiante.']);
                exit;
            }

            $habilidadDura->nombreHabilidadesDuras = $habilidadDuraItem;
            $habilidadCreadaId = $habilidadDura->createOne();
            if ($habilidadCreadaId["success"]) {
                $habilidadDura->idHabilidadesDuras = $habilidadCreadaId["extras"];
                if ($habilidadDura->createRelation()) {
                    http_response_code(201);
                    echo json_encode(['success' => true, 'message' => 'Habilidad y relación creados exitosamente.']);
                } else {
                    http_response_code(503);
                    echo json_encode(['success' => false, 'message' => 'No se pudo crear la relación pero si el Habilidad.']);
                }
            } elseif ($habilidadCreadaId["success"] == false) {
                $habilidadDura->idHabilidadesDuras = $habilidadCreadaId["extras"];
                if ($habilidadDura->createRelation()) {
                    http_response_code(201);
                    echo json_encode(['success' => true, 'message' => 'Relación creada exitosamente y el Habilidad si existia.']);
                } else {
                    http_response_code(503);
                    echo json_encode(['success' => false, 'message' => 'No se pudo crear la relación el Habilidad si existia, pero no se creo la relacion.']);
                }
            } else {
                http_response_code(503);
                echo json_encode(['success' => false, 'message' => 'No se pudo crear el Habilidad.']);
            }
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        $habilidadDura->idEstudiante = $data->idEstudiante;
        $habilidadDura->listaDeHabilidadesDuras = $data->listaDeHabilidadesDuras;

        if ($habilidadDura->delete()) {
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