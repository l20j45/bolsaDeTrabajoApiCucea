<?php
// file: models/User.php
include_once '../library/Database.php';


class HabilidadesBlandas
{
    private $pdo;

    public $idEstudiante;
    public $idHabilidadesBlandas;
    public $nombreHabilidadesBlandas;
    public $listaDeHabilidadesBlandas;


    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function readTodosLasHabilidades()
    {
        $query = "SELECT * FROM habilidadesBlandas";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readHabilidadesBlandasDelUsuario()
    {
        $query = "select habilidadesBlandas.* from estudiante
        left join habilidadesBlandasAlumnos on estudiante.idEstudiante = habilidadesBlandasAlumnos.idEstudiante
        left join habilidadesBlandas on habilidadesBlandasAlumnos.idHabilidadesBlandas = habilidadesBlandas.idHabilidadesBlandas
        where estudiante.idEstudiante = $this->idEstudiante";

        $stmt = $this->pdo->prepare($query);

        $stmt->execute();
        return $stmt;
    }

    public function readBuscarHabilidad()
    {
        $query = "SELECT * FROM habilidadesBlandas WHERE nombreHabilidadesBlandas=:nombreHabilidadesBlandas";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":nombreHabilidadesBlandas", $this->nombreHabilidadesBlandas, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt;
    }

    public function createOne()
    {
        $this->nombreHabilidadesBlandas = filter_var($this->nombreHabilidadesBlandas, FILTER_UNSAFE_RAW);
        $check = $this->readBuscarHabilidad();
        if ($check->rowCount() > 0) {

            return [
                'success' => false,
                'message' => 'Ya existe una Habilidad con ese nombre.',
                'extras' => $check->fetch()->idHabilidadesBlandas
            ];
        }

        $query = "INSERT INTO habilidadesBlandas SET nombreHabilidadesBlandas=:nombreHabilidadesBlandas";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":nombreHabilidadesBlandas", $this->nombreHabilidadesBlandas);
        if ($stmt->execute()) {
            $this->idHabilidadesBlandas = $this->pdo->lastInsertId();
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
            $query = "INSERT INTO habilidadesBlandasAlumnos SET idEstudiante=:idEstudiante, idHabilidadesBlandas=:idHabilidadesBlandas";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":idEstudiante", $this->idEstudiante);
            $stmt->bindParam(":idHabilidadesBlandas", $this->idHabilidadesBlandas);
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
        $this->nombreHabilidadesBlandas = filter_var($this->nombreHabilidadesBlandas, FILTER_UNSAFE_RAW);
        $this->idEstudiante = filter_var($this->idEstudiante, FILTER_UNSAFE_RAW);

        $query = "SELECT * FROM habilidadesBlandasAlumnos where idEstudiante=:idEstudiante and idHabilidadesBlandas=:idHabilidadesBlandas";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":idEstudiante", $this->idEstudiante);
        $stmt->bindParam(":idHabilidadesBlandas", $this->idHabilidadesBlandas);
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

        if (empty($this->listaDeHabilidadesBlandas)) {
            return false;
        }

        $habilidadesArray = explode(',', $this->listaDeHabilidadesBlandas);
        $habilidadesLimpios = array_map(function ($habilidades) {
            return $this->pdo->quote(trim($habilidades));
        }, $habilidadesArray);
        $implodeString = implode(',', $habilidadesLimpios);


        $query = "DELETE FROM habilidadesBlandasAlumnos 
              WHERE idEstudiante = :idEstudiante 
              AND idHabilidadesBlandas NOT IN (
                  SELECT idHabilidadesBlandas 
                  FROM habilidadesBlandas 
                  WHERE nombreHabilidadesBlandas IN ($implodeString)
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