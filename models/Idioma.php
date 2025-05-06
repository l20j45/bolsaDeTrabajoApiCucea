<?php
// file: models/User.php
include_once '../library/Database.php';


class Idioma
{
    private $pdo;

    public $idEstudiante;
    public $idIdioma;
    public $nombreIdiomasAdicionales;
    public $listaDeIdiomas;


    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function readTodosLosIdiomas()
    {
        $query = "SELECT * FROM idiomasAdicionales";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readIdiomasDelUsuario()
    {
        $query = "select idiomasAdicionales.* from estudiante
        left join idiomasAdicionalesAlumnos on estudiante.idEstudiante = idiomasAdicionalesAlumnos.idEstudiante
        left join idiomasAdicionales on idiomasAdicionalesAlumnos.idIdiomasAdicionales = idiomasAdicionales.idIdiomasAdicionales
        where estudiante.idEstudiante = $this->idEstudiante";

        $stmt = $this->pdo->prepare($query);

        $stmt->execute();
        return $stmt;
    }

    public function readBuscarIdioma()
    {
        $query = "SELECT * FROM idiomasAdicionales WHERE nombreIdiomasAdicionales=:NombresIdiomasAdicionales";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":NombresIdiomasAdicionales", $this->nombreIdiomasAdicionales, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt;
    }

    public function createOne()
    {
        $this->nombreIdiomasAdicionales = filter_var($this->nombreIdiomasAdicionales, FILTER_UNSAFE_RAW);
        $check = $this->readBuscarIdioma();
        if ($check->rowCount() > 0) {

            return [
                'success' => false,
                'message' => 'Ya existe un idioma con ese nombre.',
                'extras' => $check->fetch()->idIdiomasAdicionales
            ];
        }

        $query = "INSERT INTO idiomasAdicionales SET nombreIdiomasAdicionales=:NombresIdiomasAdicionales";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":NombresIdiomasAdicionales", $this->nombreIdiomasAdicionales);
        if ($stmt->execute()) {
            $this->idIdioma = $this->pdo->lastInsertId();
            return [
                'success' => true,
                'message' => 'Se agrego el idioma con éxito.',
                'extras' => $this->pdo->lastInsertId()
            ];
        }
        return [
            'success' => false,
            'message' => 'Fallo al agregar el idioma.',
            'extras' => ''
        ];
    }

    public function createRelation()
    {
        $this->idEstudiante = filter_var($this->idEstudiante, FILTER_UNSAFE_RAW);
        $verificacion = $this->checkRelation();

        if ($verificacion["success"]) {
            $query = "INSERT INTO idiomasAdicionalesAlumnos SET idEstudiante=:idEstudiante, idIdiomasAdicionales=:idIdioma";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":idEstudiante", $this->idEstudiante);
            $stmt->bindParam(":idIdioma", $this->idIdioma);
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
        $this->nombreIdiomasAdicionales = filter_var($this->nombreIdiomasAdicionales, FILTER_UNSAFE_RAW);
        $this->idEstudiante = filter_var($this->idEstudiante, FILTER_UNSAFE_RAW);

        $query = "SELECT * FROM idiomasAdicionalesAlumnos where idEstudiante=:idEstudiante and idIdiomasAdicionales=:idIdioma";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":idEstudiante", $this->idEstudiante);
        $stmt->bindParam(":idIdioma", $this->idIdioma);
        $stmt->execute();
        $revision = $stmt;
        if ($revision->rowCount() > 0) {
            return [
                'success' => false,
                'message' => 'ya existia una relacion con ese estudiante y idioma.',
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

        if (empty($this->listaDeIdiomas)) {
            return false;
        }

        $idiomasArray = explode(',', $this->listaDeIdiomas);
        $idiomasLimpios = array_map(function($idioma) {
            return $this->pdo->quote(trim($idioma));
        }, $idiomasArray);
        $idiomasString = implode(',', $idiomasLimpios);


        $query = "DELETE FROM idiomasAdicionalesAlumnos 
              WHERE idEstudiante = :idEstudiante 
              AND idIdiomasAdicionales NOT IN (
                  SELECT idIdiomasAdicionales 
                  FROM idiomasAdicionales 
                  WHERE nombreIdiomasAdicionales IN ($idiomasString)
              )";

        try {



            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":idEstudiante", $this->idEstudiante, PDO::PARAM_INT);
            echo $stmt->debugDumpParams();
            return $stmt->execute();

        } catch (PDOException $e) {

            return false;
        }
    }


}