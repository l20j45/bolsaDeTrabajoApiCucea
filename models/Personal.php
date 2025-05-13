<?php
// file: models/User.php
include_once '../library/Database.php';
include '../library/utils.php';

class Personal
{
    private $pdo;

    public $idTrabajador;
    public $uuid;
    public $codigoAlumno;
    public $password;
    public $nombre;
    public $apellidoPaterno;
    public $apellidoMaterno;
    public $telefono;
    public $correo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function login($correo, $password)
    {
        $query = "SELECT idTrabajador, correo, password FROM trabajadores WHERE correo = :correo";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":correo", $correo);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($password == $row['password']) {
                $this->idTrabajador = $row['idTrabajador'];
                $this->correo = $row['correo'];
                return true;
            }
        }
        return false;
    }

//    public function read()
//    {
//        $query = "SELECT * FROM estudiante";
//        $stmt = $this->pdo->prepare($query);
//        $stmt->execute();
//        return $stmt;
//    }
//
    public function readOne()
    {
        $query = "SELECT * FROM trabajadores WHERE idTrabajador=:idTrabajador";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":idTrabajador", $this->idTrabajador, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function leerTodosLosAlumnos()
    {
        $query = "SELECT
    e.*,
    GROUP_CONCAT(DISTINCT ia.nombreIdiomasAdicionales) AS idiomas,
    GROUP_CONCAT(DISTINCT hb.nombreHabilidadesBlandas) AS habilidadesBlandas,
    GROUP_CONCAT(DISTINCT hd.nombreHabilidadesDuras) AS habilidadesDuras
FROM
    bolsaDeTrabajoCucea.estudiante AS e
        LEFT JOIN
    bolsaDeTrabajoCucea.idiomasAdicionalesAlumnos AS iaa ON e.idEstudiante = iaa.idEstudiante
        LEFT JOIN
    bolsaDeTrabajoCucea.idiomasAdicionales AS ia ON iaa.ididiomasAdicionales = ia.idIdiomasAdicionales
        LEFT JOIN
    bolsaDeTrabajoCucea.habilidadesBlandasAlumnos AS hba ON e.idEstudiante = hba.idEstudiante
        LEFT JOIN
    bolsaDeTrabajoCucea.habilidadesBlandas AS hb ON hba.idHabilidadesBlandas = hb.idHabilidadesBlandas
        LEFT JOIN
    bolsaDeTrabajoCucea.habilidadesDurasAlumnos AS hda ON e.idEstudiante = hda.idEstudiante
        LEFT JOIN
    bolsaDeTrabajoCucea.habilidadesDuras AS hd ON hda.idHabilidadesDuras = hd.idHabilidadesDuras
GROUP BY
    e.idEstudiante;
";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt;
    }
//
//    public function enumData($table)
//    {
//
//        $query = "SELECT column_type FROM information_schema.columns WHERE table_name = 'estudiante' AND column_name = '$table';";
//        $stmt = $this->pdo->prepare($query);
//        $stmt->execute();
//        return $stmt;
//    }
//
//    public function enumCarrera()
//    {
//        $query = "SELECT column_type FROM information_schema.columns WHERE table_name = 'estudiante' AND column_name = 'carrera';";
//        $stmt = $this->pdo->prepare($query);
//        $stmt->execute();
//        return $stmt;
//    }

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
        $query = "INSERT INTO estudiante SET uuid=:uuid, codigoAlumno=:codigoAlumno, password=:password, nombre=:nombre, apellidoPaterno=:apellidoPaterno, apellidoMaterno=:apellidoMaterno, correo=:correo, telefono=:telefono";
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

    public function update(): bool
    {
        $this->idEstudiante = filter_var($this->idEstudiante, FILTER_UNSAFE_RAW);
        $this->nombre = filter_var($this->nombre, FILTER_UNSAFE_RAW);
        $this->apellidoPaterno = filter_var($this->apellidoPaterno, FILTER_UNSAFE_RAW);
        $this->apellidoMaterno = filter_var($this->apellidoMaterno, FILTER_UNSAFE_RAW);
        $this->telefono = filter_var($this->telefono, FILTER_UNSAFE_RAW);
        $this->correo = filter_var($this->correo, FILTER_UNSAFE_RAW);
        $this->carrera = filter_var($this->carrera, FILTER_UNSAFE_RAW);
        $this->estatus = filter_var($this->estatus, FILTER_UNSAFE_RAW);
        $this->semestre = filter_var($this->semestre, FILTER_UNSAFE_RAW);
        $this->foto = filter_var($this->foto, FILTER_UNSAFE_RAW);
        $this->curriculum = filter_var($this->curriculum, FILTER_UNSAFE_RAW);
        $this->descripcion = filter_var($this->descripcion, FILTER_UNSAFE_RAW);
        $this->puestoDeseado = filter_var($this->puestoDeseado, FILTER_UNSAFE_RAW);

        if ($this->idEstudiante > 0) {
            $query = "UPDATE estudiante SET nombre=:nombre, apellidoPaterno=:apellidoPaterno, apellidoMaterno=:apellidoMaterno, telefono=:telefono, correo=:correo, carrera=:carrera, estatus=:estatus, semestre=:semestre, foto=:foto, curriculum=:curriculum, descripcion=:descripcion, puestoDeseado=:puestoDeseado WHERE idEstudiante=:idEstudiante";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(":nombre", $this->nombre);
            $stmt->bindParam(":apellidoPaterno", $this->apellidoPaterno);
            $stmt->bindParam(":apellidoMaterno", $this->apellidoMaterno);
            $stmt->bindParam(":telefono", $this->telefono);
            $stmt->bindParam(":correo", $this->correo);
            $stmt->bindParam(":carrera", $this->carrera);
            $stmt->bindParam(":estatus", $this->estatus);
            $stmt->bindParam(":semestre", $this->semestre);
            $stmt->bindParam(":foto", $this->foto);
            $stmt->bindParam(":curriculum", $this->curriculum);
            $stmt->bindParam(":descripcion", $this->descripcion);
            $stmt->bindParam(":puestoDeseado", $this->puestoDeseado);
            $stmt->bindParam(":idEstudiante", $this->idEstudiante, PDO::PARAM_INT);
            if ($stmt->execute()) {
                return true;
            }
        }
        return false;
    }

//    public function updateArchivos(): bool
//    {
//        $this->foto = filter_var($this->foto, FILTER_UNSAFE_RAW);
//        $this->curriculum = filter_var($this->curriculum, FILTER_UNSAFE_RAW);
//
//        if ($this->idEstudiante > 0) {
//            $query = "UPDATE estudiante SET foto=:foto, curriculum=:curriculum WHERE idEstudiante=:idEstudiante";
//            $stmt = $this->pdo->prepare($query);
//            $stmt->bindParam(":foto", $this->foto);
//            $stmt->bindParam(":curriculum", $this->curriculum);
//            $stmt->bindParam(":idEstudiante", $this->idEstudiante, PDO::PARAM_INT);
//
//            if ($stmt->execute()) {
//                return true;
//            }
//        }
//        return false;
//    }
//
//    public function delete()
//    {
//        $this->idEstudiante = filter_var($this->idEstudiante, FILTER_SANITIZE_NUMBER_INT);
//
//        if ($this->idEstudiante > 0) {
//            $query = "DELETE FROM estudiante WHERE idEstudiante=:idEstudiante";
//            $stmt = $this->pdo->prepare($query);
//            $stmt->bindParam(":idEstudiante", $this->idEstudiante, PDO::PARAM_INT);
//            if ($stmt->execute()) {
//                return true;
//            }
//        }
//        return false;
//    }
//
//    public function emailExiste()
//    {
//        $query = "SELECT COUNT(*) FROM estudiante WHERE correo = :correo";
//        $stmt = $this->pdo->prepare($query);
//        $stmt->bindParam(":correo", $this->correo);
//        $stmt->execute();
//        return $stmt->fetchColumn() > 0;
//    }
//
//    public function codigoAlumnoExiste()
//    {
//        $query = "SELECT COUNT(*) FROM estudiante WHERE codigoAlumno = :codigoAlumno";
//        $stmt = $this->pdo->prepare($query);
//        $stmt->bindParam(":codigoAlumno", $this->codigoAlumno);
//        $stmt->execute();
//
//        return $stmt->fetchColumn() > 0;
//    }




}