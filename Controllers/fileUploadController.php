<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
// file: controllers/uploadController.php
include_once '../models/Estudiante.php';

$request = $_SERVER["REQUEST_METHOD"];

switch ($request) {
    case 'POST':
        // Verificar que sea una petición de carga de archivo
        if (isset($_FILES) && !empty($_FILES)) {
            $response = array("message" => "", "filePath" => "");

            // Obtener información del archivo
            $file = $_FILES['file'];
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileError = $file['error'];
            $fileType = $file['type'];

            // Verificar tipo de archivo (PDF o imagen)
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowed = array('jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx');

            // Verificar que el tipo de archivo sea permitido
            if (in_array($fileExt, $allowed)) {
                // Verificar que no haya errores en la carga
                if ($fileError === 0) {
                    // Verificar tamaño del archivo (máximo 5MB)
                    if ($fileSize < 5000000) {
                        // Crear nombre único para el archivo
                        $fileNewName = uniqid('', true) . "." . $fileExt;

                        // Determinar la carpeta de destino según el tipo de archivo
                        $uploadDir = '../uploads/';
                        if ($fileExt == 'pdf') {
                            $uploadDir .= 'curriculum/';
                        } else {
                            $uploadDir .= 'fotos/';
                        }

                        // Crear directorio si no existe
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        $fileDestination = $uploadDir . $fileNewName;

                        // Mover el archivo al destino
                        if (move_uploaded_file($fileTmpName, $fileDestination)) {
                            // Si es necesario, actualizar el registro del estudiante

                            if (isset($_POST['idEstudiante']) && !empty($_POST['idEstudiante'])) {

                                $estudiante = new Estudiante();
                                $estudiante->idEstudiante = $_POST['idEstudiante'];

                                // Leer datos actuales del estudiante
                                $stmt = $estudiante->readOne();

                                if ($stmt->rowCount() > 0) {
                                    $result = $stmt->fetch();

                                    // Actualizar campo de foto o curriculum según tipo de archivo
                                    $relativePath = str_replace('../', '', $fileDestination);
                                    if ($fileExt == 'pdf') {
                                        $estudiante->curriculum = $relativePath;
                                        $estudiante->foto = $result->foto;
                                    } else {
                                        $estudiante->foto = $relativePath;
                                        $estudiante->curriculum = $result->curriculum;
                                    }

                                    // Actualizar registro
                                    if ($estudiante->updateArchivos()) {
                                        $response["message"] = "Archivo subido y registro actualizado con éxito.";
                                        $response["filePath"] = $relativePath;
                                        http_response_code(200);
                                    } else {
                                        $response["message"] = "Archivo subido pero no se pudo actualizar el registro.";
                                        $response["filePath"] = $relativePath;
                                        http_response_code(206); // Partial Content
                                    }
                                } else {
                                    $response["message"] = "Archivo subido pero no se encontró el estudiante.";
                                    $response["filePath"] = str_replace('../', '', $fileDestination);
                                    http_response_code(206); // Partial Content
                                }
                            } else {
                                $response["message"] = "Archivo subido con éxito.";
                                $response["filePath"] = str_replace('../', '', $fileDestination);

                                http_response_code(200);
                            }
                        } else {
                            $response["message"] = "Error al mover el archivo.";
                            http_response_code(500);
                        }
                    } else {
                        $response["message"] = "El archivo es demasiado grande. Máximo 5MB.";
                        http_response_code(413); // Payload Too Large
                    }
                } else {
                    $response["message"] = "Error en la carga del archivo: " . $fileError;
                    http_response_code(400);
                }
            } else {
                $response["message"] = "Tipo de archivo no permitido. Solo se permiten: jpg, jpeg, png, pdf";
                http_response_code(415); // Unsupported Media Type
            }

            echo json_encode($response);
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "No se recibió ningún archivo."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método no permitido."));
        break;
}