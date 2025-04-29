<?php
function generarUuid()
{
    if (function_exists('com_create_guid') === true)
        return trim(com_create_guid(), '{}');

    $data = openssl_random_pseudo_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function guardarArchivo($archivo, $codigoEmpresa)
{
    $fileName = basename($archivo['name']);
    $type = $archivo['type'];
    $error = $archivo['error'];
    $img_size = $archivo['size'];
    if ($img_size > 12500000) {
        echo "archivo grande";
        return ["error", "tu archivo es muy grande"];
        exit;
    }
    if ($error !== 0) {
        echo "Hay algun error en el archivo";
        return ["error", $error];
        exit;
    }

    if ($type != 'application/pdf') {
        echo "Error: Solo se permiten archivos PDF.";
        return ["error", "solo se aceptan pdf´s"];
        exit;
    }

    if (!is_dir($codigoEmpresa)) {
        mkdir($codigoEmpresa, 0777, true);
    }

    $uploadFile = $codigoEmpresa . $fileName;
    if (move_uploaded_file($archivo['tmp_name'], $uploadFile)) {
        echo "El archivo PDF ha sido subido correctamente.<br>";
        return ["exito", $fileName];
    } else {
        echo "Error al subir el archivo.";
        return ["error", "no se subio el archivo"];
        exit;
    }
}

?>