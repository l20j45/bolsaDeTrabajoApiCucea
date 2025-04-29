<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
// file: controllers/UserController.php
include_once '../models/User.php';

$user = new User();
$request = $_SERVER["REQUEST_METHOD"];

switch ($request) {
    case 'GET':
        $stmt = $user->read();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $response = array("message" => "ok", "data" => array());
            while ($result = $stmt->fetch()) {
                $response["data"][] = array(
                    "id" => $result->id,
                    "name" => $result->name,
                    "email" => $result->email,
                    "phone" => $result->phone,
                    "created_at" => $result->created_at
                );
            }

            http_response_code(200);
            echo json_encode($response);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "No users found."));
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->name) && !empty($data->email)) {
            $user->name = $data->name;
            $user->email = $data->email;
            $user->phone = $data->phone;

            // Verificar si el email ya existe
            if ($user->emailExists()) {
                http_response_code(409); // Conflict
                echo json_encode(array("message" => "User already exists with this email."));
            } else if ($user->create()) {
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

        if (!empty($data->id) && !empty($data->name) && !empty($data->email)) {
            $user->id = $data->id;
            $user->name = $data->name;
            $user->email = $data->email;
            $user->phone = $data->phone;

            if ($user->update()) {
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
        $user->id = $data->id;

        if ($user->delete()) {
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