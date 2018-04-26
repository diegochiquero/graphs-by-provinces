<?php

require 'cabses.php';
require 'oConexion.class.php';

//Creamos el objeto conexión y abrimos la conexión
$conexion = new oConexion("localhost", "empresasDB", "your_user", "your_password", "utf8", NULL, NULL);
$conexion->abrir();

$accion = filter_input(INPUT_POST, "accion");

if ($accion == "seleComuni") {

    $cSQL = "SELECT
  CCAA
FROM
  EMPRESAS
GROUP BY
  CCAA";

    $stmt = $conexion->obtenerConexion()->prepare($cSQL);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($ccaa);

    //Formamos el select
    $html = '<option value="-1">Seleccione una comunidad...</option>';
    while ($stmt->fetch()) {
        $html.='<option value="' . $ccaa . '">' . $ccaa . '</option>';
    }

    $stmt->close();
    $conexion->cerrar();

    echo json_encode($html);
}

if ($accion == "ccaa") {

    $comunidad = filter_input(INPUT_POST, "comunidad");

    $cSQL = "SELECT
  PROVINCIA
FROM
  EMPRESAS
WHERE
  CCAA=?
GROUP BY
  PROVINCIA";

    $stmt = $conexion->obtenerConexion()->prepare($cSQL);
    $stmt->bind_param('s', $comunidad);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($provincias);

    $html = '';
    while ($stmt->fetch()) {
        $html.='<input type="checkbox" name="provincias" value="' . $provincias . '" style="margin:10px" checked>' . $provincias . '<br>';
    }

    $html.='<button id="btnVer" type="button">Ver</button>';
    $stmt->close();
    $conexion->cerrar();

    echo json_encode($html);
}

if ($accion == "datos") {

    $aProvincias = $_POST['provincias'];

    //Comenzamos a crear el objeto que va a recibir el api de google
    $grafi = '[["Provincias", "Superficie", "Población", "Empresas"],';

    //Cabecera tabla
    $html = '<div class="divTable">';
    $html.= '<div class="divHead">';
    $html.= '<div class="divCell centrado" style="width: 25%;">Provincia</div>';
    $html.= '<div class="divCell centrado" style="width: 15%;">Superficie</div>';
    $html.= '<div class="divCell centrado" style="width: 15%;">Población</div>';
    $html.= '<div class="divCell centrado" style="width: 15%;">Empresas</div>';
    $html.= '</div>';

    foreach ($aProvincias as $valor) {

        $cSQL = "SELECT
  PROVINCIA,
  SUM(SUPERFICIE) AS SUPERFICIE,
  SUM(POB14) AS POBLACION,
  SUM(EMPRESAS) AS EMPRESAS
FROM
  EMPRESAS
WHERE
  PROVINCIA=?
GROUP BY
  PROVINCIA";

        $stmt = $conexion->obtenerConexion()->prepare($cSQL);
        $stmt->bind_param('s', $valor);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($pro, $sup, $pob, $emp);

        if ($stmt->fetch()) {
            $html.= '<div class="divRow">';
            $html.= '<div class="divCell centrado">' . $pro . '</div>';
            $html.= '<div class="divCell centrado ">' . $sup . '</div>';
            $html.= '<div class="divCell centrado ">' . $pob . '</div>';
            $html.= '<div class="divCell centrado">' . $emp . '</div>';
            $html.= '</div>';

            $grafi.= '["' . $pro . '", ' . $sup . ',' . $pob . ',' . $emp . ' ],';
        }
    }
    unset($valor); // $valor permanece después del foreach luego se recomienda destruirla

    $html.='</div>';
    $grafi = rtrim($grafi, ',') . ']'; //Eliminamos la última coma
    $stmt->close();
    $conexion->cerrar();

    /* [['Year', 'Sales', 'Expenses', 'Profit'],
      ['2014', 1000, 400, 200],
      ['2015', 1170, 460, 250],
      ['2016', 660, 1120, 300]] */

    echo json_encode(array("datos" => $html, "grafica" => $grafi));
}
