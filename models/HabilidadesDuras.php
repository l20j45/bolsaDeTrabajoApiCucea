<?php
// file: models/User.php
include_once '../library/Database.php';


class HabilidadesDuras
{
    private $pdo;

    public $idEstudiante;
    public $idHabilidadesDuras;
    public $nombreHabilidadesDuras;
    public $listaDeHabilidadesDuras;


    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function readTodosLasHabilidades()
    {
        $query = "SELECT * FROM habilidadesDuras";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readHabilidadesDurasDelUsuario()
    {
        $query = "select habilidadesDuras.* from estudiante
        left join habilidadesDurasAlumnos on estudiante.idEstudiante = habilidadesDurasAlumnos.idEstudiante
        left join habilidadesDuras on habilidadesDurasAlumnos.idHabilidadesDuras = habilidadesDuras.idHabilidadesDuras
        where estudiante.idEstudiante = $this->idEstudiante";

        $stmt = $this->pdo->prepare($query);

        $stmt->execute();
        return $stmt;
    }

    public function readBuscarHabilidad()
    {
        $query = "SELECT * FROM habilidadesDuras WHERE nombreHabilidadesDuras=:nombreHabilidadesDuras";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":nombreHabilidadesDuras", $this->nombreHabilidadesDuras, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt;
    }

    public function createOne()
    {
        $this->nombreHabilidadesDuras = filter_var($this->nombreHabilidadesDuras, FILTER_UNSAFE_RAW);
        $check = $this->readBuscarHabilidad();
        if ($check->rowCount() > 0) {

            return [
                'success' => false,
                'message' => 'Ya existe una Habilidad con ese nombre.',
                'extras' => $check->fetch()->idHabilidadesDuras
            ];
        }

        $query = "INSERT INTO habilidadesDuras SET nombreHabilidadesDuras=:nombreHabilidadesDuras";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":nombreHabilidadesDuras", $this->nombreHabilidadesDuras);
        if ($stmt->execute()) {
            $this->idHabilidadesDuras = $this->pdo->lastInsertId();
            return [
                'success' => true,
                'message' => 'Se agrego la habilidad con éxito.',
                'extras' => $this->pdo->lastInsertId()
            ];
        }
        return [
            'success' => false,
            'message' => 'Fallo al agregar la habilidad.',
            'extras' => ''
        ];
    }

    public function createRelation()
    {
        $this->idEstudiante = filter_var($this->idEstudiante, FILTER_UNSAFE_RAW);
        $verificacion = $this->checkRelation();

        if ($verificacion["success"]) {
            $query = "INSERT INTO habilidadesDurasAlumnos SET idEstudiante=:idEstudiante, idHabilidadesDura=:idHabilidadesDuras";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":idEstudiante", $this->idEstudiante);
            $stmt->bindParam(":idHabilidadesDuras", $this->idHabilidadesDuras);
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'se creo la relación exitosamente.',
                    'extras' => ''
                ];
            }
        }
        return [
            'success' => false,
            'message' => 'No se creo la relacion.',
            'extras' => ''
        ];
    }

    public function checkRelation()
    {
        $this->nombreHabilidadesDuras = filter_var($this->nombreHabilidadesDuras, FILTER_UNSAFE_RAW);
        $this->idEstudiante = filter_var($this->idEstudiante, FILTER_UNSAFE_RAW);

        $query = "SELECT * FROM habilidadesDurasAlumnos where idEstudiante=:idEstudiante and idHabilidadesDura=:idHabilidadesDura";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":idEstudiante", $this->idEstudiante);
        $stmt->bindParam(":idHabilidadesDura", $this->idHabilidadesDuras);
        $stmt->execute();
        $revision = $stmt;
        if ($revision->rowCount() > 0) {
            return [
                'success' => false,
                'message' => 'ya existia una relacion con ese estudiante y la habilidad blanda.',
                'extras' => ''
            ];
        }
        return [
            'success' => true,
            'message' => 'no existe ninguna relacion.',
            'extras' => ''
        ];
    }


    public function delete()
    {
        $this->idEstudiante = filter_var($this->idEstudiante, FILTER_VALIDATE_INT);

        if (!$this->idEstudiante || $this->idEstudiante <= 0) {
            return false;
        }

        if (empty($this->listaDeHabilidadesDuras)) {
            return false;
        }

        $habilidadesArray = explode(',', $this->listaDeHabilidadesDuras);
        $habilidadesLimpios = array_map(function ($habilidades) {
            return $this->pdo->quote(trim($habilidades));
        }, $habilidadesArray);
        $implodeString = implode(',', $habilidadesLimpios);


        $query = "DELETE FROM habilidadesDurasAlumnos 
              WHERE idEstudiante = :idEstudiante 
              AND idHabilidadesDuras NOT IN (
                  SELECT idHabilidadesDuras 
                  FROM habilidadesDuras 
                  WHERE nombreHabilidadesDuras IN ($implodeString)
              )";

        try {


            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":idEstudiante", $this->idEstudiante, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {

            return false;
        }
    }


}