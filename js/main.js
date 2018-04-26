/* global google */
$(document).ready(function () {
    cargarComunidades();
});

function cargarComunidades() {
    $.ajax({
        type: 'POST',
        url: 'php/obtenerCCAA.php',
        data: {"accion": "seleComuni"},
        success: function (data) {
            var respuesta = $.parseJSON(data);
            $('#seleComunidad').html(respuesta);
        }
    });
}

$('#seleComunidad').change(function () {
    console.log($('select[name=seleComunidad]').val());
    $.ajax({
        type: 'POST',
        url: 'php/obtenerCCAA.php',
        data: {"accion": "ccaa", "comunidad": $('select[name=seleComunidad]').val()},
        success: function (data) {
            var respuesta = $.parseJSON(data);
            $('#izquierdoInf').css("display", "block");
            $('#divProvincias').html(respuesta);
        }
    });
});

$(document).on('click', '#btnVer', function () {
    var aProvincias = [];
    $('#divProvincias input:checked').each(function () {
        aProvincias.push($(this).val());
    });

    $.ajax({
        type: 'POST',
        url: 'php/obtenerCCAA.php',
        data: {"accion": "datos", "provincias": aProvincias}
    }).done(function (data) {
        var respuesta = $.parseJSON(data);
        var datosG = $.parseJSON(respuesta.grafica);
        console.log(datosG);
        $('#derecho').css("display", "block");
        $('#divDatos').html(respuesta.datos);
        drawChart(datosG);
    });
});

google.load("visualization", "1.1", {packages: ["bar"]});
//google.setOnLoadCallback(drawChart);//No es necesario ya que la función es llamada una vez recibida la petición ajax
function drawChart(datosG) {
    var data = google.visualization.arrayToDataTable(datosG);
    var options = {
        chart: {
            title: 'Gráfica ',
            subtitle: 'Por provincias'
        }
    };
    var chart = new google.charts.Bar(document.getElementById('divChart'));
    chart.draw(data, options);
}
