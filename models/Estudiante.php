<?php
// file: models/User.php
include_once '../library/Database.php';
include '../library/utils.php';
class Estudiante
{
    private $pdo;

    public $idEstudiante;
    public $uuid;
    public $codigoAlumno;
    public $password;
    public $nombre;
    public $apellidoPaterno;
    public $apellidoMaterno;
    public $telefono;
    public $correo;
    public $carrera;
    public $estado;
    public $semestre;
    public $foto;
    public $curriculum;


    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function read()
    {
        $query = "SELECT * FROM estudiante";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne()
    {
        $query = "SELECT * FROM estudiante WHERE idEstudiante=:idEstudiante";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":idEstudiante", $this->idEstudiante, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function create()
    {
        $this->uuid = generarUuid();
        $this->codigoAlumno = filter_var($this->codigoAlumno, FILTER_UNSAFE_RAW);
        $this->nombre = filter_var($this->nombre, FILTER_UNSAFE_RAW);
        $this->password = filter_var($this->password, FILTER_UNSAFE_RAW);
        $this->apellidoPaterno = filter_var($this->apellidoPaterno, FILTER_UNSAFE_RAW);
        $this->apellidoMaterno = filter_var($this->apellidoMaterno, FILTER_UNSAFE_RAW);
        $this->telefono = filter_var($this->telefono, FILTER_UNSAFE_RAW);
        $this->correo = filter_var($this->correo, FILTER_UNSAFE_RAW);
        $query = "INSERT INTO estudiante SET uuid=:uuid, codigoAlumno=:codigoAlumno, password=:password, nombre=:nombre, apellidoPaterno=:apellidoPaterno, apellidoMaterno=:apellidoMaterno, correo=:correo";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":uuid", $this->uuid);
        $stmt->bindParam(":codigoAlumno", $this->codigoAlumno);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":apellidoPaterno", $this->apellidoPaterno);
        $stmt->bindParam(":apellidoMaterno", $this->apellidoMaterno);
        $stmt->bindParam(":telefono", $this->telefono);
        $stmt->bindParam(":correo", $this->correo);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update()
    {
        $this->idEstudiante = filter_var($this->idEstudiante, FILTER_UNSAFE_RAW);
        $this->nombre = filter_var($this->nombre, FILTER_UNSAFE_RAW);
        $this->apellidoPaterno = filter_var($this->apellidoPaterno, FILTER_UNSAFE_RAW);
        $this->apellidoMaterno = filter_var($this->apellidoMaterno, FILTER_UNSAFE_RAW);
        $this->telefono = filter_var($this->telefono, FILTER_UNSAFE_RAW);
        $this->correo = filter_var($this->correo, FILTER_UNSAFE_RAW);
        $this->carrera = filter_var($this->carrera, FILTER_UNSAFE_RAW);
        $this->estado = filter_var($this->estado, FILTER_UNSAFE_RAW);
        $this->semestre = filter_var($this->semestre, FILTER_UNSAFE_RAW);
        $this->foto = filter_var($this->foto, FILTER_UNSAFE_RAW);
        $this->curriculum = filter_var($this->curriculum, FILTER_UNSAFE_RAW);
        if ($this->idEstudiante > 0) {

            $query = "UPDATE estudiante SET nombre=:nombre, apellidoPaterno=:apellidoPaterno, apellidoMaterno=:apellidoMaterno, telefono=:telefono, correo=:correo, carrera=:carrera, estado=:estado, semestre=:semestre, foto=:foto, curriculum=:curriculum WHERE idEstudiante=:idEstudiante";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":nombre", $this->nombre);
            $stmt->bindParam(":apellidoPaterno", $this->apellidoPaterno);
            $stmt->bindParam(":apellidoMaterno", $this->apellidoMaterno);
            $stmt->bindParam(":telefono", $this->telefono);
            $stmt->bindParam(":correo", $this->correo);
            $stmt->bindParam(":carrera", $this->carrera);
            $stmt->bindParam(":estado", $this->estado);
            $stmt->bindParam(":semestre", $this->semestre);
            $stmt->bindParam(":foto", $this->foto);
            $stmt->bindParam(":curriculum", $this->curriculum);
            $stmt->bindParam(":idEstudiante", $this->idEstudiante, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return true;
            }
        }
        return false;
    }

    public function delete()
    {
        $this->idEstudiante = filter_var($this->idEstudiante, FILTER_SANITIZE_NUMBER_INT);

        if ($this->idEstudiante > 0) {
            $query = "DELETE FROM estudiante WHERE idEstudiante=:idEstudiante";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":idEstudiante", $this->idEstudiante, PDO::PARAM_INT);
            if ($stmt->execute()) {
                return true;
            }
        }
        return false;
    }

    public function emailExiste()
    {
        $query = "SELECT COUNT(*) FROM estudiante WHERE correo = :correo";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":correo", $this->correo);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function codigoAlumnoExiste()
    {
        $query = "SELECT COUNT(*) FROM estudiante WHERE codigoAlumno = :codigoAlumno";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":codigoAlumno", $this->codigoAlumno);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }
}