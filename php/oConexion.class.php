<?php

class oConexion {

    private $servidor;
    private $baseDatos;
    private $usuario;
    private $clave;
    private $charset;
    private $idConexion;
    private $conexion;
    private $codError;
    private $error;

    /**
     * Constructor
     */
    public function __construct($servidor, $bd, $usuario, $clave, $charset, $conexion, $id) {
        $this->servidor = $servidor;
        $this->baseDatos = $bd;
        $this->usuario = $usuario;
        $this->clave = $clave;
        $this->charset = $charset;
        $this->conexion = $conexion;
        $this->idConexion = $id;
    }

    /**
     * Abre la conexión con la base de datos
     * @return bool Devuelve TRUE si se pudo realizar la conexión o FALSE en caso contrario
     * @access public
     */
    public function abrir() {
        if (!is_null($this->idConexion)) {
            die('ERROR: la base de datos está abierta.');
        }
        mysqli_report(MYSQLI_REPORT_STRICT);
        try {
            $oConni = new mysqli($this->servidor, $this->usuario, $this->clave, $this->baseDatos) or die("Error al conectar.");
        } catch (Exception $ex) {
            if ($ex->getCode() == 1045) {
                $this->codError = $ex->getCode();
                $this->error = $ex->getMessage();
                return (FALSE);
            }
            //die($ex->getMessage());
        }
        $oConni->set_charset($this->charset);
        $this->conexion = $oConni;
        $this->idConexion = $oConni->thread_id;

        return (TRUE);
    }

    /**
     * Cierra la conexión con la base de datos
     * @return bool Devuelve TRUE si se pudo realizar la desconexión o FALSE en caso contrario
     * @access public
     */
    public function cerrar() {
        if (is_null($this->idConexion)) {
            return ("estaba cerrada");
        }
        if ($this->conexion->kill($this->idConexion)) {
            $this->conexion->close();
            $this->conexion = NULL;
            $this->idConexion = NULL;
            return ("acabo de cerrar");
        } else {
            return ("no pude cerrar");
        }
    }

    /**
     * Obtiene la dirección del servidor que alberga la base de datos
     * @return string
     * @access public
     */
    public function obtenerServidor() {
        return $this->servidor;
    }

    /**
     * Obtiene el nombre de la base de datos sobre la que se realiza la conexión
     * @return string
     * @access public
     */
    public function obtenerBaseDatos() {
        return $this->baseDatos;
    }

    /**
     * Obtiene la identificación del usuario propietario de la conexión
     * @return string
     * @access public
     */
    public function obtenerUsuario() {
        return $this->usuario;
    }

    /**
     * Obtiene la clave de acceso del usuario
     * @return string
     * @access public
     */
    public function obtenerClave() {
        return $this->clave;
    }

    /**
     * Obtiene el charset de la conexión
     * @return string
     * @access public
     */
    public function obtenerCharset() {
        return $this->charset;
    }

    /**
     * Obtiene el identificador de la conexión
     * @return resource
     * @access public
     */
    public function obtenerIdConexion() {
        return $this->idConexion;
    }

    /**
     * Obtiene el identificador de la conexión
     * @return resource
     * @access public
     */
    public function obtenerConexion() {
        return $this->conexion;
    }

}
