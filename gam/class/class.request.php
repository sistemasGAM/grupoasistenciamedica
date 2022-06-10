<!-- Nombre del sistema: AEMEH
Creador: Huziel Reyes
Aerea: Sistemas
Propietario: AEMEH
Lider del proyecto : Javier Torres
Fecha de inicio: 18 de Marzo del 2021 -->
<?php

require('conect.php');
class requests
{
    /* metodo tipos 
    Selección de tipos de clientes Inst.
    Ejem: Triage**
    */

    /* ------------------TIPOS-------------- */
    /* Trae los dos tipos de clientes institucionales  */
    public function tipos()
    {
        require('functions.request/tipos.php');
    }
    /* ------------------TIPOS-------------- */
    /* metodo clientes institucionales 
    Trae todo los clinetes depende el tipo si es triaje o servicios cronicos
    */
    /* ---------------------CLIENTES INSTITUCIONALES-------------------- */
    public function clienteInstitucional($id)

    {
        require('functions.request/clienteInstitucional.php');
    }
    /* ---------------------CLIENTES INSTITUCIONALES-------------------- */
    /* fin metodo clientes institucionales */
    /* metodo tarer cuentas-clientes 
    Relacion de clientes y cuentas
    */
    /* -----------------------------TRAER CUENTAS---------------------------- */
    public function tarerCuentas($id)
    {
        require('functions.request/tarerCuentas.php');
    }
    /* -----------------------------TRAER CUENTAS---------------------------- */
    /*  metodo suggestions 
        Buscador de todos los servicios
        Este buscador se encuentra en la vista clientes institucionales, aprtado cuenta-Relacion precio servicio
    */
    /* ----------------------------BUSCADOR DE SERVICIOS-------------------------- */
    public function suggestion($key)
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM servicios";
        $sql = mysqli_query($newCon, $query);
        while ($arreglo = mysqli_fetch_array($sql)) {
            $options[] = $arreglo[1];
        }
        if ($term = $key ?? '') {
            $matches = array_filter($options, function ($option) use ($term) {
                return strpos(strtolower($option), $term) !== false;
            });
            header('Content-Type: text/javascript');
            echo json_encode(array_values($matches));
        }
    }
    /* ----------------------------BUSCADOR DE SERVICIOS-------------------------- */
    /*  metodo suggestions2
     Buscador de servicios depende cliente y banco
     Se encuentra en la Vista Visista domiciliara al momento de agragar un servicio
     */
    /* ----------------------------BUSCADOR DE SERVICIOS ASIGNADOS A CLIENTE CUENTA-------------------------- */
    public function suggestions($key, $cli, $banc)
    /* Obtengo palabra clave, banco y cliente */
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM precio_servicio A JOIN cliente_cuenta B ON A.idClienteCuenta = B.id JOIN cuenta C ON B.idCuenta = C.id WHERE idCliente = '$cli' AND nombre = '$banc'";
        $sql = mysqli_query($newCon, $query);
        while ($arreglo = mysqli_fetch_array($sql)) {
            $options[] = $arreglo[2] . "$" . $arreglo[8];
        }
        if ($term = $key ?? '') {
            $matches = array_filter($options, function ($option) use ($term) {
                return strpos(strtolower($option), $term) !== false;
            });
            header('Content-Type: text/javascript');
            echo json_encode(array_values($matches));
        }
    }
    /* ----------------------------BUSCADOR DE SERVICIOS ASIGNADOS A CLIENTE CUENTA-------------------------- */
    /* metodo carrito
     Seccion para seleccionar y guardar los servicios en una caja tipo carrito de compras
     El metodo funciona en la vista de Visita domiciliaria al pinchar el boton agregar servicio
    */
    /* ------------------------CARRITO-------------------------- */
    public function cart($serv, $autoriza, $deducible, $cantidad)
    {
        session_start();
        $id = session_id();
        $db = new con;
        $newCon = $db->sql();
        $porciones = explode("/", $serv);
        $serv = $porciones[0];
        $precio = $porciones[1];
        $mult = $precio * $cantidad;
        $total = $mult + $deducible;
        $query = "INSERT INTO `cart` (`id`, `serv`, `session`, `no- auto`, `precio`, `deducible`, `total`, `cantidad`) VALUES (NULL, '$serv', '$id', '$autoriza','$precio','$deducible','$total','$cantidad')";
        $queryTwo = "SELECT * FROM cart WHERE session = '$id'";
        $sql = mysqli_query($newCon, $query);
        $sqlTwo = mysqli_query($newCon, $queryTwo);
?>
        <table class="table">
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>No.</th>
                    <th>Precio</th>
                    <th>Deducible</th>

                    <th>Total</th>

                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php
                    while ($array = mysqli_fetch_array($sqlTwo)) {
                    ?>
                <tr>
                    <td><?php echo $array[1]; ?></td>
                    <td><?php echo $array[3]; ?></td>
                    <td>$<?php echo $array[4]; ?></td>
                    <td>$<?php echo $array[5]; ?></td>

                    <td>$<?php echo $array[6]; ?></td>

                    <td>
                        <!-- Metodo ajax para borrar los sevicios en tiempo real -->
                        <form id="formularioCartDelete<?php echo $array[0]; ?>" action="../models/reception.borrarCarrito.model.php" method="post"><input style="display: none;" name="id" type="text" value="<?php echo $array[0]; ?>"><button type="submit" id="btnEnviarCartDelete<?php echo $array[0]; ?>" class="btn btn-danger"><i class="far fa-trash-alt"></i></button></form>
                        <script>
                            /* Funcion Ajax  */
                            $(document).ready(function() {
                                $("#formularioCartDelete<?php echo $array[0]; ?>").bind("submit", function() {
                                    var btnEnviar = $("#btnEnviarCartDelete<?php echo $array[0]; ?>");
                                    $.ajax({
                                        type: $(this).attr("method"),
                                        url: $(this).attr("action"),
                                        data: $(this).serialize(),
                                        beforeSend: function() {
                                            btnEnviar.val("Enviando");
                                            btnEnviar.attr("disabled", "disabled");
                                        },
                                        complete: function(data) {
                                            btnEnviar.val("Iniciar");
                                            btnEnviar.removeAttr("disabled");
                                        },
                                        success: function(data) {
                                            $(".respuestaCart").html(data);
                                        },
                                        error: function(data) {
                                            alert("Problemas al tratar de enviar el formulario");
                                        },
                                    });
                                    return false;
                                });
                            });
                        </script>
                    </td>
                </tr>

            <?php
                    }
            ?>
            </tbody>
        </table>
    <?php

    }
    /* ------------------------CARRITO-------------------------- */
    /* metodo borrar elemento del carrito
    Boton simple en la tabla de carrito 
    */

    /* -----------------------ELIMINAR ELEMENTO DEL CARRITO---------------------- */
    public function deleteElementCart($id)/* Obtenemos la id del servicio */
    {
        session_start();
        $idSession = session_id();
        $db = new con;
        $newCon = $db->sql();
        $query = "DELETE FROM `cart` WHERE `cart`.`id` = '$id'";
        $queryTwo = "SELECT * FROM cart WHERE session = '$idSession'";
        $sql = mysqli_query($newCon, $query);
        $sqlTwo = mysqli_query($newCon, $queryTwo);
    ?>
    <!-- Devuelbe de nuevo la tabla para el metodo ajax -->
        <table class="table">
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>No.</th>
                    <th>Precio</th>
                    <th>Deducible</th>
                    <th>Total</th>
                    <th>Opciones</th>

                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php
                    while ($array = mysqli_fetch_array($sqlTwo)) {
                    ?>
                <tr>
                    <td><?php echo $array[1]; ?></td>
                    <td><?php echo $array[3]; ?></td>
                    <td>$<?php echo $array[4]; ?></td>
                    <td>$<?php echo $array[5]; ?></td>
                    <td>$<?php echo $array[6]; ?></td>
                    <td>
                        <form id="formularioCartDelete<?php echo $array[0]; ?>" action="../models/reception.borrarCarrito.model.php" method="post"><input style="display: none;" name="id" type="text" value="<?php echo $array[0]; ?>"><button type="submit" id="btnEnviarCartDelete<?php echo $array[0]; ?>" class="btn btn-danger"><i class="far fa-trash-alt"></i></button></form>
                        <script>
                            /* Funcion Ajax  */
                            $(document).ready(function() {
                                $("#formularioCartDelete<?php echo $array[0]; ?>").bind("submit", function() {
                                    var btnEnviar = $("#btnEnviarCartDelete<?php echo $array[0]; ?>");
                                    $.ajax({
                                        type: $(this).attr("method"),
                                        url: $(this).attr("action"),
                                        data: $(this).serialize(),
                                        beforeSend: function() {
                                            btnEnviar.val("Enviando");
                                            btnEnviar.attr("disabled", "disabled");
                                        },
                                        complete: function(data) {
                                            btnEnviar.val("Iniciar");
                                            btnEnviar.removeAttr("disabled");
                                        },
                                        success: function(data) {
                                            $(".respuestaCart").html(data);
                                        },
                                        error: function(data) {
                                            alert("Problemas al tratar de enviar el formulario");
                                        },
                                    });
                                    return false;
                                });
                            });
                        </script>
                    </td>
                </tr>

            <?php
                    }
            ?>

            </tbody>
        </table>
    <?php
    }
    /* -----------------------ELIMINAR ELEMENTO DEL CARRITO---------------------- */
    /* Metodo vaciar tabla
    Borra el registro del carrito de compras al finalizar la programacion de la consultas
    */
    /* -----------------------------------Vaciar tabla carrito temporal--------------------- */
    public function vaciartabla()
    {

        $ids = session_id();
        $db = new con;
        $newCon = $db->sql();
        $query = "DELETE FROM `cart` WHERE `session` = '$ids'";
        $sql = mysqli_query($newCon, $query);
    }
    /* -----------------------------------Vaciar tabla carrito temporal--------------------- */
    /* Metodo traer pacientes 
    Filtra los pacientes registrados en tiempo real
    */
    /* -----------------------Buscador de pacientes-------------------------- */
    public function patients($key)
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM pacientes";
        $sql = mysqli_query($newCon, $query);
        while ($arreglo = mysqli_fetch_array($sql)) {
            $options[] = $arreglo[4] . "/" . $arreglo[5]; /* Union de la clave del servicio con el precio bruto */
        }
        if ($term = $key ?? '') {/* Comprobamos cada palabra clabe ingresada */
            $matches = array_filter($options, function ($option) use ($term) {
                return strpos(strtolower($option), $term) !== false;
            });
            header('Content-Type: text/javascript');
            echo json_encode(array_values($matches));
        }
    }
    /* -----------------------Buscador de pacientes-------------------------- */
    /* Metodo tarer autorizaciònes, Descontinuado*/
    /* ----------------Autorizaciones-------------------------- */
    public function auth()
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM autorizacion";
        $sql = mysqli_query($newCon, $query);

    ?>
        <select class="form-control" name="autoriza" id="autoriza">
            <?php
            while ($arreglo = mysqli_fetch_array($sql)) {


            ?>
                <option name="autorizacion" value="<?php echo $arreglo[1]; ?>"><?php echo $arreglo[1]; ?></option>

            <?php } ?>
        </select>
        <br>
    <?php
    }
    /* ----------------Autorizaciones-------------------------- */
    /* Metodo llenar formulario
    Registro de la programacion general
    Formulario principal de la vista Visita domiciliaria
    */
    /* ---------------------FORMULARIO PRINCIPAL---------------------------- */
    public function formulario(
        $solicitado,
        $nombresol,
        $fechap,
        $mconsul,
        $languageTwo,
        $optionsRadios,
        $noaut,
        $observaciones,
        $obs,
        $dedu,
        $clients,
        $cuentas,
        $autorizacion,
        $numeroaut,
        $tiposervi /* obtenemos todos los datos ingresados en el formulario de Alta de eventos */
    ) {
        session_start();
        $ids = session_id();
        $db = new con;
        $newCon = $db->sql();
        setlocale(LC_TIME, "es_MX");
        $porciones = explode("/", $languageTwo);
        $patient = $porciones[0];
        if (empty($observaciones)) {
            $now = date_create()->format('Y-m-d H:i:s');
        } else {
            $now = $observaciones;
        }
        $querywhats = "SELECT * FROM pacientes WHERE nomina = '$patient'";
        $query = "INSERT INTO `alta_de_eventos` (`id`, `cli_ist`, `banco`, `solicitado_por`, `nombre_solici`, `fecha_prog`, `nombre_paciente`, `motivos`, `triage`, `folio`, `obs`, `deducible`, `t_autori`, `no_aut`, `session`, `timeserv`) VALUES
     (NULL, '$clients', ' $cuentas', '$nombresol', '$solicitado', '$fechap', '$patient', '$mconsul', '$optionsRadios', '$noaut', '$obs', '$dedu', '$autorizacion', '$numeroaut', '$ids', '$now');";
        /* Select donde se comprueban el tipo de servicio ejemplo si es consulta */
        $queryValidate = "SELECT * FROM cart A JOIN servicios B ON A.serv = B.nombre WHERE QUALITY = '1' AND session = '$ids'";
        $queryValidateEnfermeria = "SELECT * FROM cart A JOIN servicios B ON A.serv = B.nombre WHERE QUALITY = '4' AND session = '$ids'";
        $queryValidateSuperHos = "SELECT * FROM cart A JOIN servicios B ON A.serv = B.nombre WHERE QUALITY = '20' AND session = '$ids'";
        /* Ejecutatamos las sentencias SQL */
        $sqlwhats = mysqli_query($newCon, $querywhats);
        $sqlValidate = mysqli_query($newCon, $queryValidate);
        $sqlValidateEnfermeria = mysqli_query($newCon, $queryValidateEnfermeria);
        $sqlValidateSuperHos = mysqli_query($newCon, $queryValidateSuperHos);
        while ($arreglo = mysqli_fetch_array($sqlwhats)) {
            $whatsapp = $arreglo['telefono'];
        }
        $whatsappexplode = explode("/", $whatsapp);
        $whatsapp = $whatsappexplode[0];
        /* Depende el valor obtenido, decidimos si es consulta */
        if ($f = mysqli_fetch_assoc($sqlValidate)) {
            $tiposervi = "Consulta";
        } elseif ($f = mysqli_fetch_assoc($sqlValidateEnfermeria)) {
            $tiposervi = "Enfermeria";
        } elseif ($f = mysqli_fetch_assoc($sqlValidateSuperHos)) {
            $tiposervi = "Supervisión Hospitalaria";
        } else {

            $tiposervi = "Consulta";
        }
        $queryasig = "INSERT INTO `asignacion` (`id`, `cliente`, `status`, `delay`, `doctor`, `session`,`tiposerv`) VALUES (NULL, '$languageTwo', 'Pendiente', 'A tiempo', 'null', '$ids','$tiposervi')";
        $sql = mysqli_query($newCon, $query);
        $sqlasign = mysqli_query($newCon, $queryasig);
        $queryExport = "INSERT INTO history_services SELECT * FROM cart WHERE session = '$ids'";
        $sqlExport = mysqli_query($newCon, $queryExport);
        session_regenerate_id();

    ?>
        <div class="alert alert-dismissible alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Extito!</strong> Evento registrado. <a href="reception.php" class="btn btn-warning">Crear nuevo registro</a>.
        </div>

        <?php
        ?>
        <script>
            window.open('https://api.whatsapp.com/send?phone=+52<?php echo $whatsapp; ?>&text=Hola <?php echo $languageTwo; ?> se le ha asignado una consulta nueva, revise su sesión en Grupo Asistencia Medica, entre al siguente enlace para continuar: https://grupoasistenciamedica.com/gam/.', '_blank');
            location.reload();
        </script>
    <?php
    }
    /* ---------------------FORMULARIO PRINCIPAL---------------------------- */
    /* Numera los pendientes solo visual 
    Metodo encontrado en la vista panel de programacion- boton Servicios pendientes de asignaciòn
    */
    /* --------------------------NUMERAR PENDIENTES---------------------------- */
    public function pendientes()
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion A JOIN alta_de_eventos B ON A.session = B.session WHERE doctor = 'null' AND (tiposerv= 'Consulta' OR tiposerv = 'Supervisión Hospitalaria') AND B.nombre_solici <> 'ASEGURADORA'";
        $sql = mysqli_query($newCon, $query);
        $total = mysqli_num_rows($sql);
        echo $total;
    }
    public function pendientesSolicitud()
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion A JOIN alta_de_eventos B ON A.session = B.session WHERE doctor = 'null' AND (tiposerv= 'Consulta' OR tiposerv = 'Supervisión Hospitalaria') AND B.nombre_solici = 'ASEGURADORA'";
        $sql = mysqli_query($newCon, $query);
        $total = mysqli_num_rows($sql);
        echo $total;
    }
    public function pendientesEnfermeria()
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM `asignacion` WHERE doctor = 'null' AND tiposerv= 'Enfermeria'";
        $sql = mysqli_query($newCon, $query);
        $total = mysqli_num_rows($sql);
        echo $total;
    }
    /*   --------------------------NUMERAR PENDIENTES---------------------------- */
    /* Trae la lista de pendientes en la tabla de programación
    Se encuentra en la vista de Panel de programaciòn en el boton Servicios completados
    */
    /*  ----------------------------LISTAR PENDIENTES----------------------------- */
    public function traerpendientes()
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion C JOIN alta_de_eventos D ON C.session = D.session WHERE doctor = 'null' AND (tiposerv= 'Consulta' OR tiposerv= 'Supervisión Hospitalaria') AND D.nombre_solici <> 'ASEGURADORA'";
        $sql = mysqli_query($newCon, $query);
    ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Cliente</th>
                    <th>Tiempo</th>
                    <th>Fecha programada</th>
                    <th>Asignar doctor</th>
                </tr>
            </thead>
            <tbody>
                <?php
                /* Comparacion de fechas para las consultas */
                while ($arreglo = mysqli_fetch_array($sql)) {
                    $fecha = strftime("%Y-%m-%d %R");
                    $onlyconsonants = str_replace("T", " ", $arreglo[12]);
                    $d1 = new DateTime($fecha);
                    $d2 = new DateTime($onlyconsonants);
                    if ($d1 > $d2) {
                        $compare = "La fecha ya ha pasado";
                    } else {
                        $compare = "A tiempo";
                    }
                ?>
                    <tr>
                        <td><?php echo $arreglo[0]; ?></td>
                        <td><?php echo $arreglo[1]; ?></td>
                        <td><?php echo $compare; ?></td>
                        <td><?php echo $onlyconsonants; ?></td>
                        <td>
                            <!-- Configuracion de la botonera  -->
                            <form id="asignardoc<?php echo $arreglo[0]; ?>" action="../models/programacionTraerDocs.php" method="post">
                                <input style="display: none;" name="patient" type="text" value="<?php echo $arreglo[1]; ?>">
                                <input style="display: none;" name="time" type="text" value="<?php echo $arreglo['fecha_prog']; ?>">
                                <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[0]; ?>"><button type="submit" id="btnEnviarAsignDoc<?php echo $arreglo[0]; ?>" class="btn btn-danger"><i class="fas fa-notes-medical"></i></button>
                            </form>
                            <script>
                                /* Funcion Ajax  */
                                $(document).ready(function() {
                                    $("#asignardoc<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                        var btnEnviar = $("#btnEnviarAsignDoc<?php echo $arreglo[0]; ?>");
                                        $.ajax({
                                            type: $(this).attr("method"),
                                            url: $(this).attr("action"),
                                            data: $(this).serialize(),
                                            beforeSend: function() {
                                                btnEnviar.val("Enviando");
                                                btnEnviar.attr("disabled", "disabled");
                                            },
                                            complete: function(data) {
                                                btnEnviar.val("Iniciar");
                                                btnEnviar.removeAttr("disabled");
                                            },
                                            success: function(data) {
                                                $(".respuestaAsign").html(data);
                                            },
                                            error: function(data) {
                                                alert("Problemas al tratar de enviar el formulario");
                                            },
                                        });
                                        return false;
                                    });
                                });
                            </script>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    <?php
    }
    /* Funcion para asignar el doctor a una consulta */ 
    public function traerpendientesSolicitud()
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion C JOIN alta_de_eventos D ON C.session = D.session WHERE doctor = 'null' AND (tiposerv= 'Consulta' OR tiposerv= 'Supervisión Hospitalaria') AND D.nombre_solici = 'ASEGURADORA'";
        $sql = mysqli_query($newCon, $query);
    ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Cliente</th>


                        <th>Asignar doctor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    /* comparador de fechas */
                    while ($arreglo = mysqli_fetch_array($sql)) {
                        $from = $arreglo[10];
                        $mailS = explode("/", $from);


                        $fecha = strftime("%Y-%m-%d %R");
                        $onlyconsonants = str_replace("T", " ", $arreglo[12]);
                        $d1 = new DateTime($fecha);
                        $d2 = new DateTime($onlyconsonants);
                        if ($d1 > $d2) {
                            $compare = "La fecha ya ha pasado";
                        } else {
                            $compare = "A tiempo";
                        }
                    ?>
                        <tr>
                            <td><?php echo $arreglo[0]; ?></td>
                            <td><?php echo $arreglo[1]; ?></td>

                            <td>
                                <!-- Configuracion de la botonera  -->
                                <form id="asignardoc<?php echo $arreglo[0]; ?>" action="../models/programacionTraerDocs.php" method="post">
                                    <input style="display: none;" name="patient" type="text" value="<?php echo $arreglo[1]; ?>">
                                    <input style="display: none;" name="session" type="text" value="<?php echo $arreglo[5]; ?>">
                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[0]; ?>">
                                    <input style="display: none;" type="text" name="mailS" value="<?php echo trim($mailS[2]); ?>">
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="">Confirmar hora y fecha</label>
                                                <input type="datetime-local" required class="form-control" name="time" id="" aria-describedby="helpId" value="<?php echo $arreglo['fecha_prog']; ?>" placeholder="Ingresa el horario para continuar">
                                                <small id="helpId" class="form-text text-muted">Ingrese una fecha correcta</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <button type="submit" id="btnEnviarAsignDoc<?php echo $arreglo[0]; ?>" class="btn btn-danger"><i class="fas fa-notes-medical"></i> Seleccinar médico</button>

                                        </div>
                                    
                                </form>
                                <!-- boton cancelar solicitud -->
                                <form id="cancel<?php echo $arreglo[0]; ?>" action="../models/programacionCancelarSolicitud.php" method="post">
                                    <input style="display: none;" name="session" type="text" value="<?php echo $arreglo[5]; ?>">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                                Cancelar consulta <i class="fas fa-ban"></i>
                                            </button>
                                            <div class="collapse" id="collapseExample">
                                                <div class="card card-body">
                                                    <input required type="text" class="form-control" name="motivos" id="" aria-describedby="helpId" placeholder="">
                                                    <small id="helpId" class="form-text text-muted">Explica los motivos de su cancelación</small>
                                                    <button style="margin-top: 15px;" type="submit" id="btnCancel<?php echo $arreglo[0]; ?>" class="btn btn-warning"><i class="fas fa-ban"></i> Cancelar solicitud</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </form>
                                </div>
                                <script>
                                    /* Funcion Ajax  */
                                    $(document).ready(function() {
                                        $("#asignardoc<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                            var btnEnviar = $("#btnEnviarAsignDoc<?php echo $arreglo[0]; ?>");
                                            $.ajax({
                                                type: $(this).attr("method"),
                                                url: $(this).attr("action"),
                                                data: $(this).serialize(),
                                                beforeSend: function() {
                                                    btnEnviar.val("Enviando");
                                                    btnEnviar.attr("disabled", "disabled");
                                                },
                                                complete: function(data) {
                                                    btnEnviar.val("Iniciar");
                                                    btnEnviar.removeAttr("disabled");
                                                },
                                                success: function(data) {
                                                    $(".respuestaAsign").html(data);
                                                },
                                                error: function(data) {
                                                    alert("Problemas al tratar de enviar el formulario");
                                                },
                                            });
                                            return false;
                                        });
                                    });
                                </script>
                                <script>
                                    /* Funcion Ajax  */
                                    $(document).ready(function() {
                                        $("#cancel<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                            var btnEnviar = $("#btnCancel<?php echo $arreglo[0]; ?>");
                                            $.ajax({
                                                type: $(this).attr("method"),
                                                url: $(this).attr("action"),
                                                data: $(this).serialize(),
                                                beforeSend: function() {
                                                    btnEnviar.val("Enviando");
                                                    btnEnviar.attr("disabled", "disabled");
                                                },
                                                complete: function(data) {
                                                    btnEnviar.val("Iniciar");
                                                    btnEnviar.removeAttr("disabled");
                                                },
                                                success: function(data) {
                                                    $(".respuestaAsign").html(data);
                                                },
                                                error: function(data) {
                                                    alert("Problemas al tratar de enviar el formulario");
                                                },
                                            });
                                            return false;
                                        });
                                    });
                                </script>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

    <?php
    /* Solicitud de la aseguradora en el panel de programación */
    }
    public function traerpendientesSolicitudAseguradora($id)
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion C JOIN alta_de_eventos D ON C.session = D.session JOIN cliente_ist E ON D.cli_ist = E.id WHERE (tiposerv= 'Consulta' OR tiposerv= 'Supervisión Hospitalaria') AND D.nombre_solici = 'ASEGURADORA' AND E.nombre = '$id'";
        $sql = mysqli_query($newCon, $query);
    ?>
        <div class="table-responsive">
            <table id="t-sol" style="border-radius: 15px;" class="table table-striped table-light table-bordered table-hover table-sm dataTable no-footer">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th></th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($arreglo = mysqli_fetch_array($sql)) {
/* Lista todas las solicitudes */
                    ?>
                        <tr>
                            <td><?php echo $arreglo[0]; ?></td>
                            <td>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <h5>Fecha de solicitud</h5>
                                        <p><?php echo $arreglo[12]; ?></p>
                                        <h5>Paciente</h5>
                                        <p><?php echo $arreglo[1]; ?></p>
                                        <h5>Solicitante</h5>
                                        <p><?php echo $arreglo[10]; ?></p>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <h5>Estado de la solicitud</h5>
                                        <?php
                                        if ($arreglo[4] == 'null') {
                                        ?>
                                            <p class="text-danger">En espera de asignación</p>
                                        <?php
                                        } else {
                                        ?>
                                            <p class="text-danger"><?php echo $arreglo[2]; ?></p>
                                        <?php } ?>
                                    </div>
                                </div>


                            </td>



                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            <script>
                /* Orden de la lista */
                $(document).ready(function() {
                    $('#t-sol').DataTable({
                        "order": [
                            [0, "desc"]
                        ]
                    });
                });
            </script>
        </div>

    <?php
    }
    /* Lista los pendientes de enfermeria no es visible */
    public function traerpendientesEnfermeria()
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion C JOIN alta_de_eventos D ON C.session = D.session WHERE doctor = 'null' AND tiposerv= 'Enfermeria'";
        $sql = mysqli_query($newCon, $query);
    ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Cliente</th>
                    <th>Tiempo</th>
                    <th>Fecha Inicial</th>
                    <th>Fecha Final</th>


                    <th>Asignar enfermer@</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($arreglo = mysqli_fetch_array($sql)) {
                    $fecha = strftime("%Y-%m-%d %R");
                    $onlyconsonants = str_replace("T", " ", $arreglo[12]);
                    $onlyconsonants2 = str_replace("T", " ", $arreglo['timeserv']);
                    $f1 = new DateTime($onlyconsonants);
                    $f2 = new DateTime($onlyconsonants2);


                    $d1 = new DateTime($fecha);
                    $d2 = new DateTime($onlyconsonants);
                    if ($d1 > $d2) {
                        $compare = "La fecha ya ha pasado";
                    } else {
                        $compare = "A tiempo";
                    }
                ?>
                    <tr>
                        <td><?php echo $arreglo[0]; ?></td>
                        <td><?php echo $arreglo[1]; ?></td>
                        <td><?php echo $compare; ?></td>
                        <td><?php echo $onlyconsonants; ?></td>
                        <td><?php echo $onlyconsonants2; ?></td>

                        <td>
                            <!-- Configuracion de la botonera  -->
                            <form id="asignardoc<?php echo $arreglo[0]; ?>" action="../models/programacionTraerEnf.php" method="post"><input style="display: none;" name="id" type="text" value="<?php echo $arreglo[0]; ?>"><button type="submit" id="btnEnviarAsignDoc<?php echo $arreglo[0]; ?>" class="btn btn-danger"><i class="fas fa-notes-medical"></i></button></form>
                            <script>
                                /* Funcion Ajax  */
                                $(document).ready(function() {
                                    $("#asignardoc<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                        var btnEnviar = $("#btnEnviarAsignDoc<?php echo $arreglo[0]; ?>");
                                        $.ajax({
                                            type: $(this).attr("method"),
                                            url: $(this).attr("action"),
                                            data: $(this).serialize(),
                                            beforeSend: function() {
                                                btnEnviar.val("Enviando");
                                                btnEnviar.attr("disabled", "disabled");
                                            },
                                            complete: function(data) {
                                                btnEnviar.val("Iniciar");
                                                btnEnviar.removeAttr("disabled");
                                            },
                                            success: function(data) {
                                                $(".respuestaAsign").html(data);
                                            },
                                            error: function(data) {
                                                alert("Problemas al tratar de enviar el formulario");
                                            },
                                        });
                                        return false;
                                    });
                                });
                            </script>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    <?php
    }
    /*  ----------------------------LISTAR PENDIENTES----------------------------- */
    /* Selecciona el ususario tipo doctor 
    Es una funcion encontrada al pinhar el boton Servicios pendientes de asignaciòn en la vista panel de programacion
    */
    /* ---------------------------------TRAER DOCTORES------------------------------- */
    public function traerDocs($iddoc, $patient, $time, $session, $mailS)
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM users WHERE rol = 'Médico visitador' ";
        $sql = mysqli_query($newCon, $query);
    ?>
        <!-- Configuracion dinamica de tablas con buscador y paginación -->
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
        <table class="table" id="example">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Mèdico</th>
                    <th>Asignar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($arreglo = mysqli_fetch_array($sql)) {
                ?>
                    <tr>
                        <td><?php echo $arreglo[0]; ?></td>
                        <td><?php echo $arreglo[1]; ?></td>
                        <td>
                            <form id="asignardoc<?php echo $arreglo[0]; ?>" action="../models/programacionAsignarDoc.php" method="post">
                                <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[0]; ?>">
                                <input style="display: none;" name="iddoc" type="text" value="<?php echo $iddoc; ?>">
                                <input style="display: none;" name="whats" type="text" value="<?php echo $arreglo[5]; ?>">
                                <input style="display: none;" name="patient" type="text" value="<?php echo $patient; ?>">
                                <input style="display: none;" name="time" type="text" value="<?php echo $time; ?>">
                                <input style="display: none;" name="session" type="text" value="<?php echo $session; ?>">
                                <input style="display: none;" type="text" name="mail" value="<?php echo $mailS; ?>">
                                <div class="form-group">
                                  <label for="">Motivos de cambio</label>
                                  <input type="text"
                                    class="form-control" required name="motivos" id="" aria-describedby="helpId" placeholder="">
                                  
                                </div>
                                <button type="submit" id="btnEnviarAsignDoc<?php echo $arreglo[0]; ?>" class="btn btn-danger"><i class="fas fa-plus"></i></button>
                            </form>
                            <script>
                                /* Funcion Ajax  */
                                $(document).ready(function() {
                                    $("#asignardoc<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                        var btnEnviar = $("#btnEnviarAsignDoc<?php echo $arreglo[0]; ?>");
                                        $.ajax({
                                            type: $(this).attr("method"),
                                            url: $(this).attr("action"),
                                            data: $(this).serialize(),
                                            beforeSend: function() {
                                                btnEnviar.val("Enviando");
                                                btnEnviar.attr("disabled", "disabled");
                                            },
                                            complete: function(data) {
                                                btnEnviar.val("Iniciar");
                                                btnEnviar.removeAttr("disabled");
                                            },
                                            success: function(data) {
                                                $(".respuestaAsign").html(data);
                                            },
                                            error: function(data) {
                                                alert("Problemas al tratar de enviar el formulario");
                                            },
                                        });
                                        return false;
                                    });
                                });
                            </script>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <script>
            /* Funcion Ajax  */
            $(document).ready(function() {
                $('#example').DataTable();
            });
        </script>
    <?php
    }
    public function traerEnfer($iddoc)
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM users WHERE rol = 'Enfermero' ";
        $sql = mysqli_query($newCon, $query);
    ?>
        <!-- Configuracion dinamica de tablas con buscador y paginación -->
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
        <table class="table" id="example">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Enfermer@</th>
                    <th>Asignar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($arreglo = mysqli_fetch_array($sql)) {
                ?>
                    <tr>
                        <td><?php echo $arreglo[0]; ?></td>
                        <td><?php echo $arreglo[1]; ?></td>
                        <td>
                            <form id="asignardoc<?php echo $arreglo[0]; ?>" action="../models/programacionAsignarDoc.php" method="post">
                                <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[0]; ?>">
                                <input style="display: none;" name="iddoc" type="text" value="<?php echo $iddoc; ?>">
                                <button type="submit" id="btnEnviarAsignDoc<?php echo $arreglo[0]; ?>" class="btn btn-danger"><i class="fas fa-plus"></i></button>
                            </form>
                            <script>
                                /* Funcion Ajax  */
                                $(document).ready(function() {
                                    $("#asignardoc<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                        var btnEnviar = $("#btnEnviarAsignDoc<?php echo $arreglo[0]; ?>");
                                        $.ajax({
                                            type: $(this).attr("method"),
                                            url: $(this).attr("action"),
                                            data: $(this).serialize(),
                                            beforeSend: function() {
                                                btnEnviar.val("Enviando");
                                                btnEnviar.attr("disabled", "disabled");
                                            },
                                            complete: function(data) {
                                                btnEnviar.val("Iniciar");
                                                btnEnviar.removeAttr("disabled");
                                            },
                                            success: function(data) {
                                                $(".respuestaAsign").html(data);
                                            },
                                            error: function(data) {
                                                alert("Problemas al tratar de enviar el formulario");
                                            },
                                        });
                                        return false;
                                    });
                                });
                            </script>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <script>
            /* Funcion Ajax  */
            $(document).ready(function() {
                $('#example').DataTable();
            });
        </script>
    <?php
    }
    /* ---------------------------------TRAER DOCTORES------------------------------- */
    /* Metodo para asignar doctores a una consulta proceso anidado del metodo anterior */
    /* -----------------FUNCION ASIGNAR DOCTOR-------------------- */
    public function asignarDoc($id, $iddoc, $whats, $patient, $time, $session, $mail, $motivos)
    {

        session_start();
        setlocale(LC_TIME, "es_MX");
        $db = new con;
        $newCon = $db->sql();
        $now = date_create()->format('Y-m-d H:i:s');
        $name = $_SESSION['name']."/Asignacion de Doctor Nuevo";
        $queryAsig = "UPDATE `asignacion` SET `doctor` = '$id' WHERE `asignacion`.`id` = '$iddoc'";
        $queryTime = "UPDATE `alta_de_eventos` SET `fecha_prog` = '$time' WHERE `alta_de_eventos`.`session` = '$session';";
        $queryM = "INSERT INTO `motivos` (`id`, `id_session`, `motivo`, `usuario`, `fecha`) VALUES (NULL, '$id', '$motivos', '$name', '$now');";
        $sqlAsign = mysqli_query($newCon, $queryAsig);
        $sqlUpdateTime = mysqli_query($newCon, $queryTime);
        $sqlM = mysqli_query($newCon, $queryM);


    ?>

        <script>
            window.open('https://api.whatsapp.com/send?phone=+52<?php echo $whats; ?>&text=Hola Doctor se le ha asignado una consulta nueva, revise su sesión en Grupo Asistencia Medica %20*Del paciente* %20 <?php echo $patient; ?> %20 *A la hora:* %20 <?php echo $time ?>%20 Entre al siguente enlace para continuar: https://grupoasistenciamedica.com/gam/.', '_blank');
            location.reload();
        </script>
    <?php
    }
    /* -----------------FUNCION ASIGNAR DOCTOR-------------------- */
    /* Muestra las consultas asignadas 
    Es el panel principal del panel de programacion
    */
    /* -------------------CONSULTAS PROGRAMADAS----------------------- */
    public function sasignados()
    {
        $time = new requests;
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion C JOIN alta_de_eventos D ON C.session = D.session JOIN users E ON C.doctor = E.id WHERE doctor != 'null' AND tiposerv= 'Consulta' AND status != 'Finalizada' GROUP BY C.id ORDER BY C.id DESC LIMIT 50";
        $sql = mysqli_query($newCon, $query);
    ?>
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
        <div class="table-responsive">
            <table class="table  table-striped  table-sm" id="exampleT">
                <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th>Tiempo</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($arreglo = mysqli_fetch_array($sql)) {
                    ?>
                        <tr>
                            <!-- Colores dependiendo el status del evento -->
                            <?php
                            if ($arreglo[2] === "Cancelada") {
                                $color = "text-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color = "text-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color = "text-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color = "text-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color = "text-info";
                            }

                            if ($arreglo[2] === "Cancelada") {
                                $color3 = "-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color3 = "-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color3 = "-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color3 = "-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color3 = "-info";
                            }


                            /* Funciones de comparacion de fechas  */
                            $fecha = strftime("%Y-%m-%d %R");
                            $onlyconsonants = str_replace("T", " ", $arreglo[12]);
                            $d1 = new DateTime($fecha);
                            $d2 = new DateTime($onlyconsonants);
                            if ($d1 > $d2) {
                                $compare = "La fecha ya ha pasado <br><h5><i style='color: #ff0833;' class='fas fa-stopwatch'></i> </h5>";
                                $color2 = "bg-primary";
                            } else {
                                $compare = "A tiempo <br><br><h5><i style='color: #029f00;' class='far fa-clock'></i></h5>";
                                $color2 = "bg-success";
                            }
                            ?>
                            <td>
                                <center>

                                    <i class="fas fa-circle <?php echo $color; ?>"></i>
                                </center>
                            </td>
                            <td class="">
                                <center>
                                    <h5><?php echo $arreglo[0]; ?></h5>

                                </center>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <h4><?php echo $arreglo[1]; ?></h4>
                                <br>
                                <p style="color: black;"><strong><i class="fas fa-user-md"></i> </strong><?php echo $arreglo[24]; ?></p>
                                <br>
                                <div class="collapse" id="collapseExample<?php echo $arreglo[0]; ?>">

                                    <div class="container">

                                        <div class="container">
                                            <div class="row">


                                                <div style="margin: 5px;">
                                                    <form id="asignardoc<?php echo $arreglo[0]; ?>" action="../models/programacionTraerDocs.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[0]; ?>">
                                                       
                                                        <button type="submit" id="btnEnviarAsignDoc<?php echo $arreglo[0]; ?>" class="btn btn-danger" title="Cambiar Doctor" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-user-md"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <div style="margin: 5px;">
                                                    <form id="edit<?php echo $arreglo[0]; ?>" action="../models/programacionedit.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnedit<?php echo $arreglo[0]; ?>" class="btn btn-info" title="Editar datos"  data-toggle="modal" data-target="#docs">
                                                            <i class="far fa-edit"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <div style="margin: 5px;">
                                                    <form id="trash<?php echo $arreglo[0]; ?>" action="../models/programacionTrash.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <input style="display: none;" name="rute" type="text" value="motivos">
                                                        <button type="submit" id="btnTrash<?php echo $arreglo[0]; ?>" class="btn btn-primary" title="Borrar datos" data-toggle="modal" data-target="#docs" >
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    <div class="trashRespose"></div>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#trash<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnTrash<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                <div style="margin: 5px;">
                                                    <form id="search<?php echo $arreglo[0]; ?>" action="../models/doctorVerDetalles.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnSearch<?php echo $arreglo[0]; ?>" class="btn btn-dark" title="Buscar datos" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#search<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnSearch<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>

                                                <div style="margin: 5px;">
                                                    <form id="note<?php echo $arreglo[0]; ?>" action="../models/programacionNota.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnEnviarNote<?php echo $arreglo[0]; ?>" class="btn btn-success" title="Añadir Nota" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-notes-medical"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#note<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnEnviarNote<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>

                                            </div>

                                        </div>

                                    </div>
                                    <script>
                                        /* Funcion Ajax  */
                                        $(document).ready(function() {
                                            $("#asignardoc<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                var btnEnviar = $("#btnEnviarAsignDoc<?php echo $arreglo[0]; ?>");
                                                $.ajax({
                                                    type: $(this).attr("method"),
                                                    url: $(this).attr("action"),
                                                    data: $(this).serialize(),
                                                    beforeSend: function() {
                                                        btnEnviar.val("Enviando");
                                                        btnEnviar.attr("disabled", "disabled");
                                                    },
                                                    complete: function(data) {
                                                        btnEnviar.val("Iniciar");
                                                        btnEnviar.removeAttr("disabled");
                                                    },
                                                    success: function(data) {
                                                        $(".respuestaDocs").html(data);
                                                    },
                                                    error: function(data) {
                                                        alert("Problemas al tratar de enviar el formulario");
                                                    },
                                                });
                                                return false;
                                            });
                                        });
                                    </script>
                                    <script>
                                        /* Funcion Ajax  */
                                        $(document).ready(function() {
                                            $("#edit<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                var btnEnviar = $("#btnedit<?php echo $arreglo[0]; ?>");
                                                $.ajax({
                                                    type: $(this).attr("method"),
                                                    url: $(this).attr("action"),
                                                    data: $(this).serialize(),
                                                    beforeSend: function() {
                                                        btnEnviar.val("Enviando");
                                                        btnEnviar.attr("disabled", "disabled");
                                                    },
                                                    complete: function(data) {
                                                        btnEnviar.val("Iniciar");
                                                        btnEnviar.removeAttr("disabled");
                                                    },
                                                    success: function(data) {
                                                        $(".respuestaDocs").html(data);
                                                    },
                                                    error: function(data) {
                                                        alert("Problemas al tratar de enviar el formulario");
                                                    },
                                                });
                                                return false;
                                            });
                                        });
                                    </script>


                                    <br>
                                </div>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <center>
                                    <h5 class="text-dark"><?php echo $arreglo[2]; ?></h5>
                                    <br>
                                    <p style="color: black;"><i class="far fa-clock"></i> <?php echo $onlyconsonants; ?></p>
                                </center>
                                <br>
                                <br>

                                <div class="collapse" style="padding-top: 5px;" id="collapseExample<?php echo $arreglo[0]; ?>">

                                    <div class="container">



                                        <center>
                                            <div style="margin: 5px;">
                                                <form action="../models/reception.cart2.php" method="POST" id="ser<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" value="<?php echo $arreglo[5]; ?>" name="session">
                                                    <button type="submit" id="btnServ<?php echo $arreglo[0]; ?>" class="btn btn-warning " title="Servicios" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-briefcase-medical"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#ser<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnServ<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>


                                    </div>



                                    <br>
                                </div>
                            </td>
                            <td class="">
                                <center>
                                    <h5><?php echo  $compare; ?></h5>



                                </center>
                                <br>
                                <br>
                                <br>

                                <div class="collapse" id="collapseExample<?php echo $arreglo[0]; ?>">

                                    <div class="container">



                                        <center>
                                            <div style="margin: 5px;">
                                                <form action="../models/programacionTraerStatus.php" method="POST" id="status<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="sumbit" id="btnEnviarStatus<?php echo $arreglo[0]; ?>" class="btn btn-primary " title="Cancelar Operación" data-toggle="modal" data-target="#docs"><i class="fas fa-toggle-off"></i></button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#status<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnEnviarStatus<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>


                                    </div>



                                    <br>
                                </div>
                            </td>
                            <td>


                                <button class="btn btn-dark" title="Menú" type="button" data-toggle="collapse" data-target="#collapseExample<?php echo $arreglo[0]; ?>" aria-expanded="false" aria-controls="collapseExample">
                                    <i class="fas fa-bars"></i>
                                </button>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>

                                <div class="collapse" id="collapseExample<?php echo $arreglo[0]; ?>">
                                    <br>
                                    <div class="container">




                                        <center>
                                            <div style="margin: 5px;">
                                                <form action="../models/programacionVerBitacora.php" method="POST" id="Bitacora<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="sumbit" id="btnEnviarBitacora<?php echo $arreglo[0]; ?>" class="btn btn-info " title="Bitacora" data-toggle="modal" data-target="#docs"><i class="fas fa-book-medical"></i></button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#Bitacora<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnEnviarBitacora<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>

                                    </div>



                                    <br>
                                </div>



                                <!-- onfiguracion de la botonera -->

                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            /* Funcion Ajax  */
            $(document).ready(function() {
                $('#exampleT').DataTable({
                    "order": [
                        [1, "desc"]
                    ]
                });
            });
        </script>
    <?php
    }
    /* Lista todas las consultas asignadas sin finalizar en el panel de programacion */
    public function sasignadosall()
    {
        $time = new requests;
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion C JOIN alta_de_eventos D ON C.session = D.session JOIN users E ON C.doctor = E.id WHERE doctor != 'null' AND tiposerv= 'Consulta' ORDER BY C.id DESC";
        $sql = mysqli_query($newCon, $query);
    ?>
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
        <div class="table-responsive">
            <table class="table  table-striped table-sm" id="exampleTall">
                <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th>Tiempo</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($arreglo = mysqli_fetch_array($sql)) {
                    ?>
                        <tr>
                            <!-- Colores dependiendo el status del evento -->
                            <?php
                            if ($arreglo[2] === "Cancelada") {
                                $color = "text-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color = "text-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color = "text-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color = "text-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color = "text-info";
                            }

                            if ($arreglo[2] === "Cancelada") {
                                $color3 = "-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color3 = "-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color3 = "-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color3 = "-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color3 = "-info";
                            }


                            /* Funciones de comparacion de fechas  */
                            $fecha = strftime("%Y-%m-%d %R");
                            $onlyconsonants = str_replace("T", " ", $arreglo[12]);
                            $d1 = new DateTime($fecha);
                            $d2 = new DateTime($onlyconsonants);
                            if ($d1 > $d2) {
                                $compare = "La fecha ya ha pasado <br><br><h5><i style='color: #ff0833;' class='fas fa-stopwatch'></i> </h5>";
                                $color2 = "bg-primary";
                            } else {
                                $compare = "A tiempo <br><br><h5><i style='color: #029f00;' class='far fa-clock'></i></h5>";
                                $color2 = "bg-success";
                            }
                            ?>
                            <!-- diseño de la interfaz y botones con funciones en AJAX -->
                            <td>
                                <center>

                                    <i class="fas fa-circle <?php echo $color; ?>"></i>
                                </center>
                            </td>
                            <td class="">
                                <center>
                                    <h5><?php echo $arreglo[0]; ?></h5>

                                </center>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <h4><?php echo $arreglo[1]; ?></h4>
                                <br>
                                <p style="color: black;"><strong><i class="fas fa-user-md"></i> </strong><?php echo $arreglo[24]; ?></p>
                                <br>
                                <p style="color: black;"><strong><i class="fas fa-barcode"></i> </strong><?php echo $arreglo[9]; ?></p>

                            </td>
                            <td class="<?php echo $color3; ?>">
                                <center>
                                    <h5 class="text-dark"><?php echo $arreglo[2]; ?></h5>
                                    <br>
                                    <p style="color: black;"><i class="far fa-clock"></i> <?php echo $onlyconsonants; ?></p>
                                </center>


                            </td>
                            <td class="">
                                <center>
                                    <h5><?php echo  $compare; ?></h5>



                                </center>
                            </td>
                            <td>


                                <button class="btn btn-dark" type="button" data-toggle="collapse" data-target="#collapseExample<?php echo $arreglo[0]; ?>" aria-expanded="false" aria-controls="collapseExample">
                                    <i class="fas fa-bars"></i>
                                </button>
                                <div style="border-radius: 15px; background-color: #343a40;" class="collapse" id="collapseExample<?php echo $arreglo[0]; ?>">
                                    <br>
                                    <div class="container">

                                        <div class="container">
                                            <div class="row">

                                                <!-- boton asignar doctor -->
                                                <div style="margin: 5px;">
                                                    <form id="asignardocx<?php echo $arreglo[0]; ?>" action="../models/programacionTraerDocs.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[0]; ?>">
                                                        <button type="submit" id="btnEnviarAsignDocx<?php echo $arreglo[0]; ?>" class="btn btn-danger" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-user-md"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <!-- boton ver detalles -->
                                                <div style="margin: 5px;">
                                                    <form target="_blank" id="" action="./print.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnSearchx<?php echo $arreglo[0]; ?>" class="btn btn-success">
                                                            <i class="fas fa-print"></i>
                                                        </button>
                                                    </form>

                                                </div>
                                                <!-- editar consulta -->
                                                <div style="margin: 5px;">
                                                    <form id="editx<?php echo $arreglo[0]; ?>" action="../models/programacionedit.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btneditx<?php echo $arreglo[0]; ?>" class="btn btn-info" data-toggle="modal" data-target="#docs">
                                                            <i class="far fa-edit"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <!-- Boton eliminar consulta -->
                                                <div style="margin: 5px;">
                                                    <form id="trashx<?php echo $arreglo[0]; ?>" action="../models/programacionTrash.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnTrashx<?php echo $arreglo[0]; ?>" class="btn btn-primary">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    <div class="trashRespose"></div>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#trashx<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnTrashx<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".trashRespose").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                <!-- Ver detalles de la consulta -->
                                                <div style="margin: 5px;">
                                                    <form id="searchx<?php echo $arreglo[0]; ?>" action="../models/doctorVerDetalles.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnSearchx<?php echo $arreglo[0]; ?>" class="btn btn-dark" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#searchx<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnSearchx<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                        <!-- Boton escribir comentarios -->
                                                <div style="margin: 5px;">
                                                    <form id="notex<?php echo $arreglo[0]; ?>" action="../models/programacionNota.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnEnviarNotex<?php echo $arreglo[0]; ?>" class="btn btn-light" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-notes-medical"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#notex<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnEnviarNotex<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>

                                            </div>
                                            <div class="dropdown-divider"></div>
                                        </div>
                                        <br>
                                        <center>
                                            <!-- boton ver servicios -->
                                            <div style="margin: 5px;">
                                                <form action="../models/reception.cart2.php" method="POST" id="serx<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" value="<?php echo $arreglo[5]; ?>" name="session">
                                                    <button type="submit" id="btnServx<?php echo $arreglo[0]; ?>" class="btn btn-warning " data-toggle="modal" data-target="#docs">
                                                        Servicios <i class="fas fa-briefcase-medical"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#serx<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnServx<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>
                                        <br>
                                        <center>
                                            <!-- Boton cambiar status -->
                                            <div style="margin: 5px;">
                                                <form action="../models/programacionTraerStatus.php" method="POST" id="statusx<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="sumbit" id="btnEnviarStatusx<?php echo $arreglo[0]; ?>" class="btn btn-primary " data-toggle="modal" data-target="#docs">Cambiar Status <i class="fas fa-toggle-off"></i></button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#statusx<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnEnviarStatusx<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>
                                        <br>

                                        <center>
                                            <!-- Boton ver bitacora -->
                                            <div style="margin: 5px;">
                                                <form action="../models/programacionVerBitacora.php" method="POST" id="Bitacorax<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="sumbit" id="btnEnviarBitacorax<?php echo $arreglo[0]; ?>" class="btn btn-info " data-toggle="modal" data-target="#docs">Bitacora <i class="fas fa-book-medical"></i></button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#Bitacorax<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnEnviarBitacorax<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>

                                    </div>
                                    <script>
                                        /* Funcion Ajax  */
                                        $(document).ready(function() {
                                            $("#asignardocx<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                var btnEnviar = $("#btnEnviarAsignDocx<?php echo $arreglo[0]; ?>");
                                                $.ajax({
                                                    type: $(this).attr("method"),
                                                    url: $(this).attr("action"),
                                                    data: $(this).serialize(),
                                                    beforeSend: function() {
                                                        btnEnviar.val("Enviando");
                                                        btnEnviar.attr("disabled", "disabled");
                                                    },
                                                    complete: function(data) {
                                                        btnEnviar.val("Iniciar");
                                                        btnEnviar.removeAttr("disabled");
                                                    },
                                                    success: function(data) {
                                                        $(".respuestaDocs").html(data);
                                                    },
                                                    error: function(data) {
                                                        alert("Problemas al tratar de enviar el formulario");
                                                    },
                                                });
                                                return false;
                                            });
                                        });
                                    </script>
                                    <script>
                                        /* Funcion Ajax  */
                                        $(document).ready(function() {
                                            $("#editx<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                var btnEnviar = $("#btneditx<?php echo $arreglo[0]; ?>");
                                                $.ajax({
                                                    type: $(this).attr("method"),
                                                    url: $(this).attr("action"),
                                                    data: $(this).serialize(),
                                                    beforeSend: function() {
                                                        btnEnviar.val("Enviando");
                                                        btnEnviar.attr("disabled", "disabled");
                                                    },
                                                    complete: function(data) {
                                                        btnEnviar.val("Iniciar");
                                                        btnEnviar.removeAttr("disabled");
                                                    },
                                                    success: function(data) {
                                                        $(".respuestaDocs").html(data);
                                                    },
                                                    error: function(data) {
                                                        alert("Problemas al tratar de enviar el formulario");
                                                    },
                                                });
                                                return false;
                                            });
                                        });
                                    </script>


                                    <br>
                                </div>


                                <!-- onfiguracion de la botonera -->

                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            <script>
                $(document).ready(function() {
                    var heights = $(".well").map(function() {
                        return $(this).height();
                    }).get();

                    maxHeight = Math.max.apply(null, heights);

                    $(".well").height(maxHeight);
                });
            </script>
        </div>

        <script>
            /* Funcion Ajax  */
            $(document).ready(function() {
                $('#exampleTall').DataTable();
            });
        </script>
    <?php
    }
    /* Servicios asignados de enfermeria */
    public function sasignadosenfermeria()
    {
        $time = new requests;
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion C JOIN alta_de_eventos D ON C.session = D.session JOIN users E ON C.doctor = E.id WHERE doctor != 'null' AND tiposerv= 'Enfermeria' AND status != 'Finalizada'";
        $sql = mysqli_query($newCon, $query);
    ?>
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm" id="exampleT">
                <thead class="thead-dark">
                    <tr>
                        <th>No.</th>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th>Tiempo</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($arreglo = mysqli_fetch_array($sql)) {
                    ?>
                        <tr>
                            <!-- Colores dependiendo el status del evento -->
                            <?php
                            if ($arreglo[2] === "Cancelada") {
                                $color = "bg-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color = "bg-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color = "bg-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color = "bg-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color = "bg-info";
                            }

                            if ($arreglo[2] === "Cancelada") {
                                $color3 = "-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color3 = "-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color3 = "-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color3 = "-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color3 = "-info";
                            }


                            /* Funciones de comparacion de fechas  */
                            $fecha = strftime("%Y-%m-%d %R");
                            $onlyconsonants = str_replace("T", " ", $arreglo[12]);
                            $onlyconsonants2 = str_replace("T", " ", $arreglo['timeserv']);
                            $d1 = new DateTime($fecha);
                            $d2 = new DateTime($onlyconsonants);
                            if ($d1 > $d2) {
                                $compare = "La fecha ya ha pasado <br><br><h5><i style='color: white;' class='fas fa-stopwatch'></i> </h5>";
                                $color2 = "bg-primary";
                            } else {
                                $compare = "A tiempo <br><br><h5><i style='color: white;' class='far fa-clock'></i></h5>";
                                $color2 = "bg-success";
                            }
                            ?>
                            <td style="color: white;" class="<?php echo $color; ?>">
                                <center>
                                    <h5><?php echo $arreglo[0]; ?></h5>
                                </center>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <h4><?php echo $arreglo[1]; ?></h4>
                                <br>
                                <p style="color: black;"><strong><i class="fas fa-user-nurse"></i> </strong><?php echo $arreglo[24]; ?></p>
                                <br>
                                <p style="color: black;"><i class="far fa-clock"></i> Fecha inicial: <?php echo $onlyconsonants; ?></p>
                                <br>
                                <p style="color: black;"><i class="fas fa-clock"></i> Fecha final: <?php echo $onlyconsonants2; ?></p>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <center>
                                    <h4 style="margin-top: 90px;" class="text-dark"><?php echo $arreglo[2]; ?></h4>
                                </center>


                            </td>
                            <td class="<?php echo $color2; ?>">
                                <center>
                                    <h5 style="color: white; margin-top: 75px;"><?php echo  $compare; ?></h5>
                                </center> <br>
                            </td>
                            <td class="table-light">
                                <!-- onfiguracion de la botonera -->
                                <div class="container">

                                    <div class="container">
                                        <div class="row">


                                            <div style="margin: 5px;">
                                                <form id="asignardoc<?php echo $arreglo[0]; ?>" action="../models/programacionTraerDocs.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[0]; ?>">
                                                    <button type="submit" id="btnEnviarAsignDoc<?php echo $arreglo[0]; ?>" class="btn btn-danger" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-user-md"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="edit<?php echo $arreglo[0]; ?>" action="../models/programacionedit.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="btnedit<?php echo $arreglo[0]; ?>" class="btn btn-info" data-toggle="modal" data-target="#docs">
                                                        <i class="far fa-edit"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="trash<?php echo $arreglo[0]; ?>" action="../models/programacionTrash.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="btnTrash<?php echo $arreglo[0]; ?>" class="btn btn-primary">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <div class="trashRespose"></div>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#trash<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnTrash<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".trashRespose").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="search<?php echo $arreglo[0]; ?>" action="../models/doctorVerDetalles.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="btnSearch<?php echo $arreglo[0]; ?>" class="btn btn-dark" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#search<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnSearch<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>

                                            <div style="margin: 5px;">
                                                <form id="note<?php echo $arreglo[0]; ?>" action="../models/programacionNota.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="btnEnviarNote<?php echo $arreglo[0]; ?>" class="btn btn-light" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-notes-medical"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#note<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnEnviarNote<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>

                                        </div>
                                    </div>
                                    <br>
                                    <center>
                                        <div class="well" style="margin: 5px;">
                                            <form action="../models/reception.cart2.php" method="POST" id="ser<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" value="<?php echo $arreglo[5]; ?>" name="session">
                                                <button type="submit" id="btnServ<?php echo $arreglo[0]; ?>" class="btn btn-outline-warning btn-lg btn-block" data-toggle="modal" data-target="#docs">
                                                    Servicios <i class="fas fa-briefcase-medical"></i>
                                                </button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#ser<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#btnServ<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>
                                    <br>
                                    <center>
                                        <div class="well" style="margin: 5px;">
                                            <form action="../models/programacionTraerStatus.php" method="POST" id="status<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                <button type="sumbit" id="btnEnviarStatus<?php echo $arreglo[0]; ?>" class="btn btn-outline-primary btn-lg btn-block" data-toggle="modal" data-target="#docs">Cambiar Status <i class="fas fa-toggle-off"></i></button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#status<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#btnEnviarStatus<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>
                                    <br>

                                    <center>
                                        <div class="well" style="margin: 5px;">
                                            <form action="../models/programacionVerBitacora.php" method="POST" id="Bitacora<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                <button type="sumbit" id="btnEnviarBitacora<?php echo $arreglo[0]; ?>" class="btn btn-outline-info btn-lg btn-block" data-toggle="modal" data-target="#docs">Bitacora <i class="fas fa-book-medical"></i></button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#Bitacora<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#btnEnviarBitacora<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>

                                </div>
                                <script>
                                    /* Funcion Ajax  */
                                    $(document).ready(function() {
                                        $("#asignardoc<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                            var btnEnviar = $("#btnEnviarAsignDoc<?php echo $arreglo[0]; ?>");
                                            $.ajax({
                                                type: $(this).attr("method"),
                                                url: $(this).attr("action"),
                                                data: $(this).serialize(),
                                                beforeSend: function() {
                                                    btnEnviar.val("Enviando");
                                                    btnEnviar.attr("disabled", "disabled");
                                                },
                                                complete: function(data) {
                                                    btnEnviar.val("Iniciar");
                                                    btnEnviar.removeAttr("disabled");
                                                },
                                                success: function(data) {
                                                    $(".respuestaDocs").html(data);
                                                },
                                                error: function(data) {
                                                    alert("Problemas al tratar de enviar el formulario");
                                                },
                                            });
                                            return false;
                                        });
                                    });
                                </script>
                                <script>
                                    /* Funcion Ajax  */
                                    $(document).ready(function() {
                                        $("#edit<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                            var btnEnviar = $("#btnedit<?php echo $arreglo[0]; ?>");
                                            $.ajax({
                                                type: $(this).attr("method"),
                                                url: $(this).attr("action"),
                                                data: $(this).serialize(),
                                                beforeSend: function() {
                                                    btnEnviar.val("Enviando");
                                                    btnEnviar.attr("disabled", "disabled");
                                                },
                                                complete: function(data) {
                                                    btnEnviar.val("Iniciar");
                                                    btnEnviar.removeAttr("disabled");
                                                },
                                                success: function(data) {
                                                    $(".respuestaDocs").html(data);
                                                },
                                                error: function(data) {
                                                    alert("Problemas al tratar de enviar el formulario");
                                                },
                                            });
                                            return false;
                                        });
                                    });
                                </script>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            /* Funcion Ajax  */
            $(document).ready(function() {
                $('#exampleT').DataTable();
            });
        </script>
    <?php
    }
    /* -------------------CONSULTAS PROGRAMADAS----------------------- */
    /* Muestra las consultas asignadas (otros)  */
    /* ---------------------------OTROS---------------------------------- */
    public function sasignadostres()
    {
        $time = new requests;
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion C JOIN alta_de_eventos D ON C.session = D.session WHERE tiposerv= 'Otro'";
        $sql = mysqli_query($newCon, $query);
    ?>
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
        <div class="table-responsive">
            <table class="table  table-striped table-sm" id="exampleT2">
                <thead class="thead-dark">
                    <tr>
                        <th>No.</th>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th>Tiempo</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($arreglo = mysqli_fetch_array($sql)) {
                    ?>
                        <tr>
                            <!-- Colores dependiendo el status del evento -->
                            <?php
                            if ($arreglo[2] === "Cancelada") {
                                $color = "text-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color = "text-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color = "text-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color = "text-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color = "text-info";
                            }

                            if ($arreglo[2] === "Cancelada") {
                                $color3 = "-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color3 = "-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color3 = "-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color3 = "-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color3 = "-info";
                            }


                            /* Funciones de comparacion de fechas  */
                            $fecha = strftime("%Y-%m-%d %R");
                            $onlyconsonants = str_replace("T", " ", $arreglo[12]);
                            $d1 = new DateTime($fecha);
                            $d2 = new DateTime($onlyconsonants);
                            if ($d1 > $d2) {
                                $compare = "La fecha ya ha pasado <br><br><h5><i style='color: #ff0833;' class='fas fa-stopwatch'></i> </h5>";
                                $color2 = "bg-primary";
                            } else {
                                $compare = "A tiempo <br><br><h5><i style='color: #029f00;' class='far fa-clock'></i></h5>";
                                $color2 = "bg-success";
                            }
                            ?>
                            <td class="">
                                <center>
                                    <h5><?php echo $arreglo[0]; ?></h5>
                                    <br>
                                    <i class="fas fa-circle <?php echo $color; ?>"></i>
                                </center>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <h4><?php echo $arreglo[1]; ?></h4>
                                <br>
                                <p style="color: black;"><strong><i class="fas fa-user-md"></i> </strong><?php echo $arreglo[24]; ?></p>

                            </td>
                            <td class="<?php echo $color3; ?>">
                                <center>
                                    <h5 class="text-dark"><?php echo $arreglo[2]; ?></h5>
                                    <br>
                                    <p style="color: black;"><i class="far fa-clock"></i> <?php echo $onlyconsonants; ?></p>
                                </center>


                            </td>
                            <td class="">
                                <center>
                                    <h5><?php echo  $compare; ?></h5>



                                </center>
                            </td>
                            <td>


                                <button class="btn btn-dark" type="button" data-toggle="collapse" data-target="#collapseExample<?php echo $arreglo[0]; ?>" aria-expanded="false" aria-controls="collapseExample">
                                    <i class="fas fa-bars"></i>
                                </button>
                                <div style="border-radius: 15px; background-color: #343a40;" class="collapse" id="collapseExample<?php echo $arreglo[0]; ?>">
                                    <br>
                                    <div class="container">

                                        <div class="container">
                                            <div class="row">



                                                <div style="margin: 5px;">
                                                    <form id="edit<?php echo $arreglo[0]; ?>" action="../models/programacionedit.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnedit<?php echo $arreglo[0]; ?>" class="btn btn-info" data-toggle="modal" data-target="#docs">
                                                            <i class="far fa-edit"></i>
                                                        </button>
                                                    </form>
                                                </div>

                                                <div style="margin: 5px;">
                                                    <form id="search<?php echo $arreglo[0]; ?>" action="../models/doctorVerDetalles.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnSearch<?php echo $arreglo[0]; ?>" class="btn btn-dark" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#search<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnSearch<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>

                                                <div style="margin: 5px;">
                                                    <form id="note<?php echo $arreglo[0]; ?>" action="../models/programacionNota.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnEnviarNote<?php echo $arreglo[0]; ?>" class="btn btn-light" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-notes-medical"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#note<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnEnviarNote<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>

                                            </div>
                                            <div class="dropdown-divider"></div>
                                        </div>
                                        <br>
                                        <center>
                                            <div style="margin: 5px;">
                                                <form action="../models/reception.cart2.php" method="POST" id="ser<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" value="<?php echo $arreglo[5]; ?>" name="session">
                                                    <button type="submit" id="btnServ<?php echo $arreglo[0]; ?>" class="btn btn-warning " data-toggle="modal" data-target="#docs">
                                                        Servicios <i class="fas fa-briefcase-medical"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#ser<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnServ<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>
                                        <br>
                                        <center>
                                            <div style="margin: 5px;">
                                                <form action="../models/programacionTraerStatus.php" method="POST" id="status<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="sumbit" id="btnEnviarStatus<?php echo $arreglo[0]; ?>" class="btn btn-primary " data-toggle="modal" data-target="#docs">Cambiar Status <i class="fas fa-toggle-off"></i></button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#status<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnEnviarStatus<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>
                                        <br>

                                        <center>
                                            <div style="margin: 5px;">
                                                <form action="../models/programacionVerBitacora.php" method="POST" id="Bitacora<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="sumbit" id="btnEnviarBitacora<?php echo $arreglo[0]; ?>" class="btn btn-info " data-toggle="modal" data-target="#docs">Bitacora <i class="fas fa-book-medical"></i></button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#Bitacora<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnEnviarBitacora<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>

                                    </div>
                                    <script>
                                        /* Funcion Ajax  */
                                        $(document).ready(function() {
                                            $("#asignardoc<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                var btnEnviar = $("#btnEnviarAsignDoc<?php echo $arreglo[0]; ?>");
                                                $.ajax({
                                                    type: $(this).attr("method"),
                                                    url: $(this).attr("action"),
                                                    data: $(this).serialize(),
                                                    beforeSend: function() {
                                                        btnEnviar.val("Enviando");
                                                        btnEnviar.attr("disabled", "disabled");
                                                    },
                                                    complete: function(data) {
                                                        btnEnviar.val("Iniciar");
                                                        btnEnviar.removeAttr("disabled");
                                                    },
                                                    success: function(data) {
                                                        $(".respuestaDocs").html(data);
                                                    },
                                                    error: function(data) {
                                                        alert("Problemas al tratar de enviar el formulario");
                                                    },
                                                });
                                                return false;
                                            });
                                        });
                                    </script>
                                    <script>
                                        /* Funcion Ajax  */
                                        $(document).ready(function() {
                                            $("#edit<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                var btnEnviar = $("#btnedit<?php echo $arreglo[0]; ?>");
                                                $.ajax({
                                                    type: $(this).attr("method"),
                                                    url: $(this).attr("action"),
                                                    data: $(this).serialize(),
                                                    beforeSend: function() {
                                                        btnEnviar.val("Enviando");
                                                        btnEnviar.attr("disabled", "disabled");
                                                    },
                                                    complete: function(data) {
                                                        btnEnviar.val("Iniciar");
                                                        btnEnviar.removeAttr("disabled");
                                                    },
                                                    success: function(data) {
                                                        $(".respuestaDocs").html(data);
                                                    },
                                                    error: function(data) {
                                                        alert("Problemas al tratar de enviar el formulario");
                                                    },
                                                });
                                                return false;
                                            });
                                        });
                                    </script>


                                    <br>
                                </div>


                                <!-- onfiguracion de la botonera -->

                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            /* Funcion Ajax  */
            $(document).ready(function() {
                $('#exampleT2').DataTable();
            });
        </script>
    <?php
    }
    /* ---------------------------OTROS---------------------------------- */
    /* metodo carrito2 
    En este metodo se muestran los servicios ya asignados a cada evento
    Encontarda en la vista de visita domiciliaria
    */
    /* -------------------------CARRITO VISITA DOMICILIARIA----------------------------- */
    public function cart2($serv, $autoriza, $id)
    {
        session_start();
        $db = new con;
        $newCon = $db->sql();
        $query = "INSERT INTO `history_services` (`id`, `serv`, `session`, `no- auto`) VALUES (NULL, '$serv', '$id', '$autoriza')";
        $queryTwo = "SELECT * FROM history_services WHERE session = '$id'";
        if (empty($serv && $autoriza)) {
            $sqlTwo = mysqli_query($newCon, $queryTwo);
        } else {
            $sql = mysqli_query($newCon, $query);
            $sqlTwo = mysqli_query($newCon, $queryTwo);
        }

    ?>
        <div class="jumbotron">
            <center>
                <h3>Servicios adjuntos <i class="fas fa-hand-holding-medical"></i></h3>
            </center>
            <br>

            <table class="table">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th>No.</th>
                        <th>Cantidad</th>
                        <th>Deducible</th>
                        <th>Opciones</th>


                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <?php
                        while ($array = mysqli_fetch_array($sqlTwo)) {
                        ?>
                    <tr>
                        <td><?php echo $array[1]; ?></td>
                        <td><?php echo $array[3]; ?></td>
                        <td><?php echo $array[7]; ?></td>
                        <td>$ <?php echo $array[5]; ?></td>
                        <td>
                            <?php if ($_SESSION['rol'] === "Administrador") {
                            ?>
                                <form id="formularioCartDelete<?php echo $array[0]; ?>" action="../models/reception.borrarCarrito2.model.php" method="post">
                                    <input style="display: none;" name="id" type="text" value="<?php echo $array[0]; ?>">
                                    <input style="display: none;" type="text" name="session" value="<?php echo $id; ?>">
                                    <button type="submit" id="btnEnviarCartDelete<?php echo $array[0]; ?>" class="btn btn-danger">
                                        <i class="far fa-trash-alt"></i></button>
                                </form>
                            <?php
                            } else {
                            }
                            ?>

                            <script>
                                /* Funcion Ajax  */
                                $(document).ready(function() {
                                    $("#formularioCartDelete<?php echo $array[0]; ?>").bind("submit", function() {
                                        var btnEnviar = $("#btnEnviarCartDelete<?php echo $array[0]; ?>");
                                        $.ajax({
                                            type: $(this).attr("method"),
                                            url: $(this).attr("action"),
                                            data: $(this).serialize(),
                                            beforeSend: function() {
                                                btnEnviar.val("Enviando");
                                                btnEnviar.attr("disabled", "disabled");
                                            },
                                            complete: function(data) {
                                                btnEnviar.val("Iniciar");
                                                btnEnviar.removeAttr("disabled");
                                            },
                                            success: function(data) {
                                                $(".respuestaDocs").html(data);
                                            },
                                            error: function(data) {
                                                alert("Problemas al tratar de enviar el formulario");
                                            },
                                        });
                                        return false;
                                    });
                                });
                            </script>
                        </td>
                    </tr>

                <?php
                        }
                ?>

                </tbody>
            </table>
        </div>
    <?php

    }
    /* -------------------------CARRITO VISITA DOMICILIARIA----------------------------- */
    /* metodo borrar elemento del carrito2 */
    /*  -----------------------------BORRAR ELEMENTO DEL CARRITO----------------------------------- */
    public function deleteElementCart2($id, $idSession)
    {

        $db = new con;
        $newCon = $db->sql();
        $query = "DELETE FROM `history_services` WHERE `history_services`.`id` = '$id'";
        $queryTwo = "SELECT * FROM history_services WHERE session = '$idSession'";
        $sql = mysqli_query($newCon, $query);
        $sqlTwo = mysqli_query($newCon, $queryTwo);
    ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>No.</th>
                    <th>Opciones</th>

                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php
                    while ($array = mysqli_fetch_array($sqlTwo)) {
                    ?>
                <tr>
                    <td><?php echo $array[1]; ?></td>
                    <td><?php echo $array[3]; ?></td>
                    <td>
                        <form id="formularioCartDelete<?php echo $array[0]; ?>" action="../models/reception.borrarCarrito2.model.php" method="post">
                            <input style="display: none;" name="id" type="text" value="<?php echo $array[0]; ?>">
                            <input style="display: none;" type="text" name="session" value="<?php echo $id; ?>">
                            <button type="submit" id="btnEnviarCartDelete<?php echo $array[0]; ?>" class="btn btn-danger">
                                <i class="far fa-trash-alt"></i></button>
                        </form>
                        <script>
                            /* Funcion Ajax  */
                            $(document).ready(function() {
                                $("#formularioCartDelete<?php echo $array[0]; ?>").bind("submit", function() {
                                    var btnEnviar = $("#btnEnviarCartDelete<?php echo $array[0]; ?>");
                                    $.ajax({
                                        type: $(this).attr("method"),
                                        url: $(this).attr("action"),
                                        data: $(this).serialize(),
                                        beforeSend: function() {
                                            btnEnviar.val("Enviando");
                                            btnEnviar.attr("disabled", "disabled");
                                        },
                                        complete: function(data) {
                                            btnEnviar.val("Iniciar");
                                            btnEnviar.removeAttr("disabled");
                                        },
                                        success: function(data) {
                                            $(".respuestaCart").html(data);
                                        },
                                        error: function(data) {
                                            alert("Problemas al tratar de enviar el formulario");
                                        },
                                    });
                                    return false;
                                });
                            });
                        </script>
                    </td>
                </tr>

            <?php
                    }
            ?>

            </tbody>
        </table>
    <?php
    }
    /*  -----------------------------BORRAR ELEMENTO DEL CARRITO----------------------------------- */
    /* metodo editar evento 
    funcion opara editar eventos de manera dinamica
    Se encuentra en el panel de programacion tras el boton de editar
    */
    /* -------------------------------------EDITAR EVENTO------------------------------------ */
    public function edtEvent($id)
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM alta_de_eventos A JOIN cliente_ist B ON A.cli_ist = B.id WHERE session = '$id'";
        $queryClienteIns = "SELECT * FROM `cliente_ist`";
        $queryBancos = "SELECT * FROM `cuenta`";
        $queryAutoriza = "SELECT * FROM `autorizacion`";
        $sql = mysqli_query($newCon, $query);
        $sqlClienteIns = mysqli_query($newCon, $queryClienteIns);
        $sqlBancos = mysqli_query($newCon, $queryBancos);
        $sqlAutorizaciones = mysqli_query($newCon, $queryAutoriza);
        while ($arreglo = mysqli_fetch_array($sql)) {
            /* Obtenemos todos los datos del formulario a editar */
            $idT = $arreglo[0];
            $clienteIstitucional = $arreglo[17];
            $idClienteIstitucional = $arreglo[1];
            $banco = $arreglo[2];
            $solicitante = $arreglo[3];
            $solicitadoPor = $arreglo[4];
            $fechaProgramada = $arreglo[5];
            $nombreDelPaciente = $arreglo[6];
            $motivos = $arreglo[7];
            $triage = $arreglo[8];
            $folio = $arreglo[9];
            $observaciones = $arreglo[10];
            $deducible = $arreglo[11];
            $tipoDeAutorizacion = $arreglo[12];
            $numeroDeAutorizacion = $arreglo[13];
            $session = $arreglo[14];
        }
    ?>
        <div class="jumbotron">
            <center>
                <h3>Editar consulta <i class="fas fa-pills"></i></h3>
            </center>

        </div>
        <form id="add" action="../models/editarEvento.model.php" method="post">
            <input type="text" name="session" value="<?php echo $id ?>" style="display: none;">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Cliente Institucional</label>
                        <select class="form-control" name="clienteIn" id="">
                            <option selected value="<?php echo $idClienteIstitucional; ?>"><?php echo $clienteIstitucional; ?></option>
                            <?php
                            while ($arreglo = mysqli_fetch_array($sqlClienteIns)) {
                            ?>
                                <option value="<?php echo $arreglo[0]; ?>"><?php echo $arreglo[1]; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Banco</label>
                        <select class="form-control" name="banco" id="">
                            <option selected value="<?php echo $banco; ?>"><?php echo $banco; ?></option>
                            <?php
                            while ($arreglo = mysqli_fetch_array($sqlBancos)) {
                            ?>
                                <option value="<?php echo $arreglo[1]; ?>"><?php echo $arreglo[1]; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <hr>
            <br>
            <h3>Identificacion del solicitante <i class="fas fa-notes-medical"></i></h3>
            <br>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Solicitado por:</label>
                        <input type="text" class="form-control" value="<?php echo $solicitadoPor ?>" name="solicitadoPor" id="" aria-describedby="helpId" placeholder="">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Nombre Solicitante:</label>
                        <input type="text" class="form-control" value="<?php echo $solicitante ?>" name="NombreSolicitante" id="" aria-describedby="helpId" placeholder="">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="">Fecha Programada:</label>
                        <input type="datetime-local" required class="form-control" name="fechaProgramada" id="" aria-describedby="helpId"><small><?php echo $fechaProgramada; ?></small>
                    </div>
                </div>
            </div>
            <hr>
            <br>
            <h3>Paciente <i class="fas fa-procedures"></i></h3>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Nombre/ Nomina del Paciente:</label>
                        <input type="text" value="<?php echo $nombreDelPaciente ?>" readonly class="form-control" name="nombrePaciente" id="" aria-describedby="helpId" placeholder="">

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Motivo de consulta:</label>
                        <textarea class="form-control" name="motivoConsulta" id="" rows="3"><?php echo $motivos ?></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Triage:</label>
                        <select class="form-control" name="triage" id="">
                            <option selected value="<?php echo $triage ?>"><?php echo $triage ?></option>
                            <option value="roja">Roja</option>
                            <option value="amarilla">Amarilla</option>
                            <option value="verde">Verde</option>
                            <option value="negra">Negra</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Folio:</label>
                        <input type="text" value="<?php echo $folio; ?>" class="form-control" name="folio" id="" aria-describedby="helpId" placeholder="">

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">

                        <input style="display: none;" type="text" value="<?php echo $deducible ?>" class="form-control" name="deducible" id="" aria-describedby="helpId" placeholder="">

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">

                        <input style="display: none;" type="number" value="<?php echo $numeroDeAutorizacion ?>" class="form-control" name="noAutorizacion" id="" aria-describedby="helpId" placeholder="">

                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="">Observaciones:</label>
                        <textarea class="form-control" name="observaciones" id="" rows="3"><?php echo $observaciones ?></textarea>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">

                        <select style="display: none;" class="form-control" name="tipoAutorizacion" id="">
                            <option value="<?php echo $tipoDeAutorizacion ?>"><?php echo $tipoDeAutorizacion ?></option>
                            <?php
                            while ($arreglo = mysqli_fetch_array($sqlAutorizaciones)) {
                            ?>
                                <option value="<?php echo $arreglo[0]; ?>"><?php echo $arreglo[1]; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <center><button id="btnEnviarAdd" type="submit" class="btn btn-primary"> Guardar </button></center>
        </form>
        <div class="respuesta"></div>
        <script>
            /* Funcion Ajax  */
            $(document).ready(function() {
                $("#add").bind("submit", function() {
                    var btnEnviar = $("#btnEnviarAdd");
                    $.ajax({
                        type: $(this).attr("method"),
                        url: $(this).attr("action"),
                        data: $(this).serialize(),
                        beforeSend: function() {
                            btnEnviar.val("Enviando");
                            btnEnviar.attr("disabled", "disabled");
                        },
                        complete: function(data) {
                            btnEnviar.val("Iniciar");
                            btnEnviar.removeAttr("disabled");
                        },
                        success: function(data) {
                            $(".respuesta").html(data);
                        },
                        error: function(data) {
                            alert("Problemas al tratar de enviar el formulario");
                        },
                    });
                    return false;
                });
            });
        </script>
    <?php
    }
    /* -------------------------------------EDITAR EVENTO------------------------------------ */
    /* Lista y cuenta los eventos finalizados exitosamente 
    Es la funcion del boton servicios Completados de la vista panel de programacion
    */
    /* ---------------------------------SERVICIOS COMPLETOS-------------------------------- */
    public function serviciosCompletos()
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM `asignacion` WHERE status= 'Finalizada'";
        $sql = mysqli_query($newCon, $query);
        $total = mysqli_num_rows($sql);
        echo $total;
    }
    public function serviciosCompletosEnf()
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM `asignacion` WHERE status= 'Finalizada' AND tiposerv = 'Enfermeria'";
        $sql = mysqli_query($newCon, $query);
        $total = mysqli_num_rows($sql);
        echo $total;
    }
    /* ---------------------------------SERVICIOS COMPLETOS-------------------------------- */
    /* muestra los eventos finalizados exitosamente anidado del metodo anterior*/
    /* ------------------------------------LISTAR SERVICIOS COMPLETOS---------------------------- */
    public function traerCompletos()
    {
        $time = new requests;
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion C JOIN alta_de_eventos D ON C.session = D.session JOIN users E ON C.doctor = E.id JOIN consulta F ON C.session = F.session WHERE doctor != 'null' AND tiposerv= 'Consulta' AND status = 'Finalizada' GROUP BY C.id ORDER BY C.id DESC LIMIT 10 ";
        $sql = mysqli_query($newCon, $query);
    ?>

        <div class="table-responsive">
            <table class="table  table-striped table-sm" id="exampleTa">
                <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th>Tiempo</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($arreglo = mysqli_fetch_array($sql)) {
                    ?>
                        <tr>
                            <!-- Colores dependiendo el status del evento -->
                            <?php
                            if ($arreglo[2] === "Cancelada") {
                                $color = "text-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color = "text-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color = "text-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color = "text-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color = "text-info";
                            }

                            if ($arreglo[2] === "Cancelada") {
                                $color3 = "-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color3 = "-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color3 = "-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color3 = "-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color3 = "-info";
                            }


                            /* Funciones de comparacion de fechas  */
                            $fecha = strftime("%Y-%m-%d %R");
                            $onlyconsonants = str_replace("T", " ", $arreglo[12]);
                            $d1 = new DateTime($fecha);
                            $d2 = new DateTime($onlyconsonants);
                            if ($d1 > $d2) {
                                $compare = "La fecha ya ha pasado <br><br><h5><i style='color: #ff0833;' class='fas fa-stopwatch'></i> </h5>";
                                $color2 = "bg-primary";
                            } else {
                                $compare = "A tiempo <br><br><h5><i style='color: #029f00;' class='far fa-clock'></i></h5>";
                                $color2 = "bg-success";
                            }
                            ?>
                            <td>
                                <center>

                                    <i class="fas fa-circle <?php echo $color; ?>"></i>
                                </center>
                            </td>
                            <td class="">
                                <center>
                                    <h5><?php echo $arreglo[0]; ?></h5>

                                </center>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <h4><?php echo $arreglo[1]; ?></h4>
                                <br>
                                <?php
                                $porciones = explode("/",  $arreglo[3]);
                                $d1 = date($porciones[0]); // porción1
                                $d2 = date($porciones[1]); // porción2
                                $d3 = date($arreglo['date']);
                                ?>
                                <p style="color: black;"><strong><i class="fas fa-user-md"></i> </strong><?php echo $arreglo[24]; ?></p>
                                <br>
                                <?php
                                if ($d3 < $d2) {
                                ?>
                                    <p style="color: black;"><strong><i class="far fa-clock"></i> Consulta cerrada a tiempo </strong></p>
                                    <br>
                                <?php
                                } else {
                                ?>
                                    <p style="color: black;"><strong><i class="far fa-clock"></i> Consulta cerrada a destiempo</strong></p>
                                    <br>
                                <?php
                                }
                                ?>


                            </td>
                            <td class="<?php echo $color3; ?>">
                                <center>
                                    <h5 class="text-dark"><?php echo $arreglo[2]; ?></h5>
                                    <br>
                                    <p style="color: black;"><i class="far fa-clock"></i> <?php echo $onlyconsonants; ?></p>
                                </center>


                            </td>
                            <td class="">
                                <center>
                                    <h5><?php echo  $compare; ?></h5>



                                </center>
                            </td>
                            <td>


                                <button class="btn btn-dark" type="button" data-toggle="collapse" data-target="#collapseExample<?php echo $arreglo[0]; ?>" aria-expanded="false" aria-controls="collapseExample">
                                    <i class="fas fa-bars"></i>
                                </button>
                                <div style="border-radius: 15px; background-color: #343a40;" class="collapse" id="collapseExample<?php echo $arreglo[0]; ?>">
                                    <br>
                                    <div class="container">

                                        <div class="container">
                                            <div class="row">


                                                <div style="margin: 5px;">
                                                    <form id="searchs<?php echo $arreglo[0]; ?>" action="../models/doctorVerDetalles.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnSearchs<?php echo $arreglo[0]; ?>" class="btn btn-dark" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#searchs<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnSearchs<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                <div style="margin: 5px;">
                                                    <form target="_blank" id="searchx<?php echo $arreglo[0]; ?>" action="./print.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnSearchx<?php echo $arreglo[0]; ?>" class="btn btn-success" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-print"></i>
                                                        </button>
                                                    </form>

                                                </div>

                                                <div style="margin: 5px;">
                                                    <form id="notes<?php echo $arreglo[0]; ?>" action="../models/programacionNota.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnEnviarNotes<?php echo $arreglo[0]; ?>" class="btn btn-light" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-notes-medical"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#notes<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnEnviarNotes<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>

                                            </div>
                                            <div class="dropdown-divider"></div>
                                        </div>
                                        <br>
                                        <center>
                                            <div style="margin: 5px;">
                                                <form action="../models/reception.cart2.php" method="POST" id="sers<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" value="<?php echo $arreglo[5]; ?>" name="session">
                                                    <button type="submit" id="btnServs<?php echo $arreglo[0]; ?>" class="btn btn-warning " data-toggle="modal" data-target="#docs">
                                                        Servicios <i class="fas fa-briefcase-medical"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#sers<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnServs<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>
                                        <br>


                                        <center>
                                            <div style="margin: 5px;">
                                                <form action="../models/programacionVerBitacora.php" method="POST" id="Bitacoras<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="sumbit" id="btnEnviarBitacoras<?php echo $arreglo[0]; ?>" class="btn btn-info " data-toggle="modal" data-target="#docs">Bitacora <i class="fas fa-book-medical"></i></button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#Bitacoras<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnEnviarBitacoras<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>

                                    </div>
                                    <script>
                                        /* Funcion Ajax  */
                                        $(document).ready(function() {
                                            $("#asignardoc<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                var btnEnviar = $("#btnEnviarAsignDoc<?php echo $arreglo[0]; ?>");
                                                $.ajax({
                                                    type: $(this).attr("method"),
                                                    url: $(this).attr("action"),
                                                    data: $(this).serialize(),
                                                    beforeSend: function() {
                                                        btnEnviar.val("Enviando");
                                                        btnEnviar.attr("disabled", "disabled");
                                                    },
                                                    complete: function(data) {
                                                        btnEnviar.val("Iniciar");
                                                        btnEnviar.removeAttr("disabled");
                                                    },
                                                    success: function(data) {
                                                        $(".respuestaDocs").html(data);
                                                    },
                                                    error: function(data) {
                                                        alert("Problemas al tratar de enviar el formulario");
                                                    },
                                                });
                                                return false;
                                            });
                                        });
                                    </script>
                                    <script>
                                        /* Funcion Ajax  */
                                        $(document).ready(function() {
                                            $("#edit<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                var btnEnviar = $("#btnedit<?php echo $arreglo[0]; ?>");
                                                $.ajax({
                                                    type: $(this).attr("method"),
                                                    url: $(this).attr("action"),
                                                    data: $(this).serialize(),
                                                    beforeSend: function() {
                                                        btnEnviar.val("Enviando");
                                                        btnEnviar.attr("disabled", "disabled");
                                                    },
                                                    complete: function(data) {
                                                        btnEnviar.val("Iniciar");
                                                        btnEnviar.removeAttr("disabled");
                                                    },
                                                    success: function(data) {
                                                        $(".respuestaDocs").html(data);
                                                    },
                                                    error: function(data) {
                                                        alert("Problemas al tratar de enviar el formulario");
                                                    },
                                                });
                                                return false;
                                            });
                                        });
                                    </script>


                                    <br>
                                </div>


                                <!-- onfiguracion de la botonera -->

                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            /* Funcion Ajax  */
            $(document).ready(function() {
                $('#exampleTa').DataTable({
                    "order": [
                        [1, "desc"]
                    ]
                });
            });
        </script>

    <?php
    }
    /* Servicios de asignacion de enfermeria inconcluso */
    public function traerCompletosEnf()
    {
        $time = new requests;
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion C JOIN alta_de_eventos D ON C.session = D.session JOIN users E ON C.doctor = E.id WHERE doctor != 'null' AND tiposerv= 'Consulta' AND status = 'Enfermeria'";
        $sql = mysqli_query($newCon, $query);
    ?>
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm" id="exampleTa">
                <thead class="thead-dark">
                    <tr>
                        <th>No.</th>
                        <th>Cliente</th>
                        <th>Status</th>

                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($arreglo = mysqli_fetch_array($sql)) {
                    ?>
                        <tr>
                            <!-- Colores dependiendo el status del evento -->
                            <?php
                            if ($arreglo[2] === "Cancelada") {
                                $color = "bg-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color = "bg-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color = "bg-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color = "bg-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color = "bg-info";
                            }

                            if ($arreglo[2] === "Cancelada") {
                                $color3 = "-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color3 = "-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color3 = "-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color3 = "-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color3 = "-info";
                            }


                            /* Funciones de comparacion de fechas  */
                            $fecha = strftime("%Y-%m-%d %R");
                            $onlyconsonants = str_replace("T", " ", $arreglo[12]);
                            $d1 = new DateTime($fecha);
                            $d2 = new DateTime($onlyconsonants);
                            if ($d1 > $d2) {
                                $compare = "La fecha ya ha pasado <br><br><h5><i style='color: white;' class='fas fa-stopwatch'></i> </h5>";
                                $color2 = "bg-primary";
                            } else {
                                $compare = "A tiempo <br><br><h5><i style='color: white;' class='far fa-clock'></i></h5>";
                                $color2 = "bg-success";
                            }
                            ?>
                            <td style="color: white;" class="<?php echo $color; ?>">
                                <center>
                                    <h5><?php echo $arreglo[0]; ?></h5>
                                </center>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <h4><?php echo $arreglo[1]; ?></h4>
                                <br>
                                <p style="color: black;"><strong><i class="fas fa-user-md"></i> </strong><?php echo $arreglo[24]; ?></p>
                                <br>
                                <p style="color: black;"><i class="far fa-clock"></i> <?php echo $onlyconsonants; ?></p>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <center>
                                    <h4 style="margin-top: 90px;" class="text-dark"><?php echo $arreglo[2]; ?></h4>
                                </center>


                            </td>

                            <td class="table-light">
                                <!-- configuracion de la botonera -->
                                <div class="container">

                                    <div class="container">
                                        <div class="row">


                                            <div style="margin: 5px;">
                                                <form id="2asignardoc<?php echo $arreglo[0]; ?>" action="../models/programacionTraerDocs.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[0]; ?>">
                                                    <button type="submit" id="2btnEnviarAsignDoc<?php echo $arreglo[0]; ?>" class="btn btn-danger" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-user-md"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="2edit<?php echo $arreglo[0]; ?>" action="../models/programacionedit.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnedit<?php echo $arreglo[0]; ?>" class="btn btn-info" data-toggle="modal" data-target="#docs">
                                                        <i class="far fa-edit"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="2trash<?php echo $arreglo[0]; ?>" action="../models/programacionTrash.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnTrash<?php echo $arreglo[0]; ?>" class="btn btn-primary">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <div class="trashRespose"></div>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#2trash<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#2btnTrash<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".trashRespose").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="2search<?php echo $arreglo[0]; ?>" action="../models/doctorVerDetalles.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnSearch<?php echo $arreglo[0]; ?>" class="btn btn-dark" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#2search<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#2btnSearch<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>

                                            <div style="margin: 5px;">
                                                <form id="2note<?php echo $arreglo[0]; ?>" action="../models/programacionNota.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnEnviarNote<?php echo $arreglo[0]; ?>" class="btn btn-light" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-notes-medical"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#2note<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#2btnEnviarNote<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>

                                        </div>
                                    </div>
                                    <br>
                                    <center>
                                        <div style="margin: 5px;">
                                            <form action="../models/reception.cart2.php" method="POST" id="2ser<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" value="<?php echo $arreglo[5]; ?>" name="session">
                                                <button type="submit" id="2btnServ<?php echo $arreglo[0]; ?>" class="btn btn-outline-warning btn-lg btn-block" data-toggle="modal" data-target="#docs">
                                                    Servicios <i class="fas fa-briefcase-medical"></i>
                                                </button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#2ser<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#2btnServ<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>
                                    <br>
                                    <center>
                                        <div style="margin: 5px;">
                                            <form action="../models/programacionTraerStatus.php" method="POST" id="2status<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                <button type="sumbit" id="2btnEnviarStatus<?php echo $arreglo[0]; ?>" class="btn btn-outline-primary btn-lg btn-block" data-toggle="modal" data-target="#docs">Cambiar Status <i class="fas fa-toggle-off"></i></button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#2status<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#2btnEnviarStatus<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>
                                    <br>

                                    <center>
                                        <div style="margin: 5px;">
                                            <form action="../models/programacionVerBitacora.php" method="POST" id="2Bitacora<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                <button type="sumbit" id="2btnEnviarBitacora<?php echo $arreglo[0]; ?>" class="btn btn-outline-info btn-lg btn-block" data-toggle="modal" data-target="#docs">Bitacora <i class="fas fa-book-medical"></i></button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#2Bitacora<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#2btnEnviarBitacora<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>

                                </div>
                                <script>
                                    /* Funcion Ajax  */
                                    $(document).ready(function() {
                                        $("#2asignardoc<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                            var btnEnviar = $("#2btnEnviarAsignDoc<?php echo $arreglo[0]; ?>");
                                            $.ajax({
                                                type: $(this).attr("method"),
                                                url: $(this).attr("action"),
                                                data: $(this).serialize(),
                                                beforeSend: function() {
                                                    btnEnviar.val("Enviando");
                                                    btnEnviar.attr("disabled", "disabled");
                                                },
                                                complete: function(data) {
                                                    btnEnviar.val("Iniciar");
                                                    btnEnviar.removeAttr("disabled");
                                                },
                                                success: function(data) {
                                                    $(".respuestaDocs").html(data);
                                                },
                                                error: function(data) {
                                                    alert("Problemas al tratar de enviar el formulario");
                                                },
                                            });
                                            return false;
                                        });
                                    });
                                </script>
                                <script>
                                    /* Funcion Ajax  */
                                    $(document).ready(function() {
                                        $("#2edit<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                            var btnEnviar = $("#2btnedit<?php echo $arreglo[0]; ?>");
                                            $.ajax({
                                                type: $(this).attr("method"),
                                                url: $(this).attr("action"),
                                                data: $(this).serialize(),
                                                beforeSend: function() {
                                                    btnEnviar.val("Enviando");
                                                    btnEnviar.attr("disabled", "disabled");
                                                },
                                                complete: function(data) {
                                                    btnEnviar.val("Iniciar");
                                                    btnEnviar.removeAttr("disabled");
                                                },
                                                success: function(data) {
                                                    $(".respuestaDocs").html(data);
                                                },
                                                error: function(data) {
                                                    alert("Problemas al tratar de enviar el formulario");
                                                },
                                            });
                                            return false;
                                        });
                                    });
                                </script>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            /* Funcion Ajax  */
            $(document).ready(function() {
                $('#exampleTa').DataTable();
            });
        </script>
    <?php
    }
    /* ------------------------------------LISTAR SERVICIOS COMPLETOS---------------------------- */
    /* Selecciona el tema actual */
    /* ----------------------THEME PIKER----------------------- */
    public function temerPiker()
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM themes";
        $sql = mysqli_query($newCon, $query);
        while ($arreglo = mysqli_fetch_array($sql)) {
            echo $archive = $arreglo[2];
        }
    }
    /* ----------------------THEME PIKER----------------------- */
    /* ---------------------------FUNCION DETALLES DE PACIENTES------------------- */
    /* Trae los detalles de los pacientes en funcion de su nomina */
    public function detallePatient($id)
    {
        $db = new con;
        $newCon = $db->sql();
        $porciones = explode("/", $id);
        $patient = $porciones[0];
        $query = "SELECT * FROM `pacientes` WHERE nomina = '$patient'";
        $query2 = "SELECT * FROM asignacion C JOIN alta_de_eventos D ON C.session = D.session JOIN users E ON C.doctor = E.id JOIN consulta F ON C.session = F.session WHERE doctor != 'null' AND tiposerv= 'Consulta' AND status = 'Finalizada' AND C.cliente = '$id' GROUP BY C.id ORDER BY C.id DESC LIMIT 3";
        $sql2 = mysqli_query($newCon, $query2);

        $sql = mysqli_query($newCon, $query);
        /* listar con funcion WHILE  */
        while ($arreglo = mysqli_fetch_array($sql)) {
            $nomina = $arreglo[4];
            $nom = $arreglo[5];
            $ap = $arreglo[6];
            $am = $arreglo[7];
            $calle = $arreglo[9];
            $noex = $arreglo[10];
            $noin = $arreglo[11];
            $ref = $arreglo[12];
            $cp = $arreglo[13];
            $colonia = $arreglo[14];
            $mun = $arreglo[15];
            $estado = $arreglo[16];
            $tele = $arreglo[18];
        }
        $telefonos = explode("/", $tele);
        $tele1 = $telefonos[0];
        $tele2 = $telefonos[1];
        $tele3 = $telefonos[2];
    ?>
        <!-- Imprimir en html -->
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Nomina</label>
                    <input readonly type="text" value="<?php echo $nomina ?>" class="form-control" name="" id="uno" aria-describedby="helpId" placeholder="">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Nombre</label>
                    <input readonly type="text" value="<?php echo $nom . " " . $ap . " " . $am ?>" class="form-control" name="" id="dos" aria-describedby="helpId" placeholder="">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="">Calle</label>
                    <input readonly type="text" value="<?php echo $calle ?>" class="form-control" name="" id="tres" aria-describedby="helpId" placeholder="">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="">No.Exterior</label>
                    <input readonly type="text" value="<?php echo $noex ?>" class="form-control" name="" id="cuatro" aria-describedby="helpId" placeholder="">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="">No.Interior</label>
                    <input readonly type="text" value="<?php echo $noin ?>" class="form-control" name="" id="cinco" aria-describedby="helpId" placeholder="">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Referencias</label>
                    <textarea readonly class="form-control" name="" id="" rows="3"><?php echo $ref ?></textarea>

                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">C.P.</label>


                    <textarea readonly class="form-control" name="" id="" rows="3"><?php echo $cp ?></textarea>


                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Colonia</label>
                    <input readonly type="text" value="<?php echo $colonia ?>" class="form-control" name="" id="ocho" aria-describedby="helpId" placeholder="">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Delegacion o municipio</label>
                    <input readonly type="text" value="<?php echo $mun ?>" class="form-control" name="" id="nueve" aria-describedby="helpId" placeholder="">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Estado</label>
                    <input readonly type="text" value="<?php echo $estado ?>" class="form-control" name="" id="diez" aria-describedby="helpId" placeholder="">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Telefono 1</label>
                    <input readonly type="text" value="<?php echo $tele1 ?>" class="form-control" name="" id="once" aria-describedby="helpId" placeholder="">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Telefono 2</label>
                    <input readonly type="text" value="<?php echo $tele2 ?>" class="form-control" name="" id="once" aria-describedby="helpId" placeholder="">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Telefono 3</label>
                    <input readonly type="text" value="<?php echo $tele3 ?>" class="form-control" name="" id="once" aria-describedby="helpId" placeholder="">
                </div>
            </div>
        </div>
        <hr>
        <center>
            <h3>Últimas citas</h3>
        </center>

        <div class="table-responsive">
            <table class="table  table-striped table-sm" id="exampleTa">
                <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th>Tiempo</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($arreglo = mysqli_fetch_array($sql2)) {
                    ?>
                        <tr>
                            <!-- Colores dependiendo el status del evento -->
                            <?php
                            if ($arreglo[2] === "Cancelada") {
                                $color = "text-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color = "text-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color = "text-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color = "text-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color = "text-info";
                            }

                            if ($arreglo[2] === "Cancelada") {
                                $color3 = "-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color3 = "-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color3 = "-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color3 = "-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color3 = "-info";
                            }


                            /* Funciones de comparacion de fechas  */
                            $fecha = strftime("%Y-%m-%d %R");
                            $onlyconsonants = str_replace("T", " ", $arreglo[12]);
                            $d1 = new DateTime($fecha);
                            $d2 = new DateTime($onlyconsonants);
                            if ($d1 > $d2) {
                                $compare = "La fecha ya ha pasado <br><br><h5><i style='color: #ff0833;' class='fas fa-stopwatch'></i> </h5>";
                                $color2 = "bg-primary";
                            } else {
                                $compare = "A tiempo <br><br><h5><i style='color: #029f00;' class='far fa-clock'></i></h5>";
                                $color2 = "bg-success";
                            }
                            ?>
                            <td>
                                <center>

                                    <i class="fas fa-circle <?php echo $color; ?>"></i>
                                </center>
                            </td>
                            <td class="">
                                <center>
                                    <h5><?php echo $arreglo[0]; ?></h5>

                                </center>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <h4><?php echo $arreglo[1]; ?></h4>
                                <br>
                                <?php
                                $porciones = explode("/",  $arreglo[3]);
                                $d1 = date($porciones[0]); // porción1
                                $d2 = date($porciones[1]); // porción2
                                $d3 = date($arreglo['date']);
                                ?>
                                <p style="color: black;"><strong><i class="fas fa-user-md"></i> </strong><?php echo $arreglo[24]; ?></p>
                                <br>
                                <?php
                                if ($d3 < $d2) {
                                ?>
                                    <p style="color: black;"><strong><i class="far fa-clock"></i> Consulta cerrada a tiempo </strong></p>
                                    <br>
                                <?php
                                } else {
                                ?>
                                    <p style="color: black;"><strong><i class="far fa-clock"></i> Consulta cerrada a destiempo</strong></p>
                                    <br>
                                <?php
                                }
                                ?>


                            </td>
                            <td class="<?php echo $color3; ?>">
                                <center>
                                    <h5 class="text-dark"><?php echo $arreglo[2]; ?></h5>
                                    <br>
                                    <p style="color: black;"><i class="far fa-clock"></i> <?php echo $onlyconsonants; ?></p>
                                </center>


                            </td>
                            <td class="">
                                <center>
                                    <h5><?php echo  $compare; ?></h5>



                                </center>
                            </td>
                            <td>


                                <button class="btn btn-dark" type="button" data-toggle="collapse" data-target="#collapseExample<?php echo $arreglo[0]; ?>" aria-expanded="false" aria-controls="collapseExample">
                                    <i class="fas fa-bars"></i>
                                </button>
                                <div style="border-radius: 15px; background-color: #343a40;" class="collapse" id="collapseExample<?php echo $arreglo[0]; ?>">
                                    <br>
                                    <div class="container">

                                        <div class="container">
                                            <div class="row">


                                                <div style="margin: 5px;">
                                                    <form id="searchs<?php echo $arreglo[0]; ?>" action="../models/doctorVerDetalles.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnSearchs<?php echo $arreglo[0]; ?>" class="btn btn-dark" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#searchs<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnSearchs<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                <div style="margin: 5px;">
                                                    <form target="_blank" id="searchx<?php echo $arreglo[0]; ?>" action="./print.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnSearchx<?php echo $arreglo[0]; ?>" class="btn btn-success" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-print"></i>
                                                        </button>
                                                    </form>

                                                </div>

                                                <div style="margin: 5px;">
                                                    <form id="notes<?php echo $arreglo[0]; ?>" action="../models/programacionNota.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnEnviarNotes<?php echo $arreglo[0]; ?>" class="btn btn-light" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-notes-medical"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#notes<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnEnviarNotes<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>

                                            </div>
                                            <div class="dropdown-divider"></div>
                                        </div>
                                        <br>
                                        <center>
                                            <div style="margin: 5px;">
                                                <form action="../models/reception.cart2.php" method="POST" id="sers<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" value="<?php echo $arreglo[5]; ?>" name="session">
                                                    <button type="submit" id="btnServs<?php echo $arreglo[0]; ?>" class="btn btn-warning " data-toggle="modal" data-target="#docs">
                                                        Servicios <i class="fas fa-briefcase-medical"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#sers<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnServs<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>
                                        <br>


                                        <center>
                                            <div style="margin: 5px;">
                                                <form action="../models/programacionVerBitacora.php" method="POST" id="Bitacoras<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="sumbit" id="btnEnviarBitacoras<?php echo $arreglo[0]; ?>" class="btn btn-info " data-toggle="modal" data-target="#docs">Bitacora <i class="fas fa-book-medical"></i></button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#Bitacoras<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnEnviarBitacoras<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>

                                    </div>
                                    <script>
                                        /* Funcion Ajax  */
                                        $(document).ready(function() {
                                            $("#asignardoc<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                var btnEnviar = $("#btnEnviarAsignDoc<?php echo $arreglo[0]; ?>");
                                                $.ajax({
                                                    type: $(this).attr("method"),
                                                    url: $(this).attr("action"),
                                                    data: $(this).serialize(),
                                                    beforeSend: function() {
                                                        btnEnviar.val("Enviando");
                                                        btnEnviar.attr("disabled", "disabled");
                                                    },
                                                    complete: function(data) {
                                                        btnEnviar.val("Iniciar");
                                                        btnEnviar.removeAttr("disabled");
                                                    },
                                                    success: function(data) {
                                                        $(".respuestaDocs").html(data);
                                                    },
                                                    error: function(data) {
                                                        alert("Problemas al tratar de enviar el formulario");
                                                    },
                                                });
                                                return false;
                                            });
                                        });
                                    </script>
                                    <script>
                                        /* Funcion Ajax  */
                                        $(document).ready(function() {
                                            $("#edit<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                var btnEnviar = $("#btnedit<?php echo $arreglo[0]; ?>");
                                                $.ajax({
                                                    type: $(this).attr("method"),
                                                    url: $(this).attr("action"),
                                                    data: $(this).serialize(),
                                                    beforeSend: function() {
                                                        btnEnviar.val("Enviando");
                                                        btnEnviar.attr("disabled", "disabled");
                                                    },
                                                    complete: function(data) {
                                                        btnEnviar.val("Iniciar");
                                                        btnEnviar.removeAttr("disabled");
                                                    },
                                                    success: function(data) {
                                                        $(".respuestaDocs").html(data);
                                                    },
                                                    error: function(data) {
                                                        alert("Problemas al tratar de enviar el formulario");
                                                    },
                                                });
                                                return false;
                                            });
                                        });
                                    </script>


                                    <br>
                                </div>


                                <!-- onfiguracion de la botonera -->

                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            /* Funcion Ajax  */
            $(document).ready(function() {
                $('#exampleTa').DataTable({
                    "order": [
                        [1, "desc"]
                    ]
                });
            });
        </script>

    <?php
    }
    /*  ------------------------------HISTORIAL DE PACIENTES------------------------------------------- */
    /* Metodo para mostrar un menu donde se pueden visualizar el historial de consultas para los pacientes  */
    public function noVisitados()
    {
        $db = new con;
        $newCon = $db->sql();

        $query = "SELECT * FROM consulta A JOIN asignacion B ON A.session = B.session JOIN users C ON B.doctor = C.id";
        $queryCounDoc = "SELECT *, COUNT( B.doctor ) AS total FROM consulta A JOIN asignacion B ON A.session = B.session JOIN users C ON B.doctor = C.id GROUP BY B.doctor ORDER BY total DESC";
        $queryCountPatient = "SELECT *, COUNT( B.cliente ) AS total FROM consulta A JOIN asignacion B ON A.session = B.session JOIN users C ON B.doctor = C.id GROUP BY B.cliente ORDER BY total DESC";
        $queryCountBancs = "SELECT *, COUNT( D.banco ) AS total FROM consulta A JOIN asignacion B ON A.session = B.session JOIN users C ON B.doctor = C.id JOIN alta_de_eventos D ON A.session = D.session GROUP BY D.banco ORDER BY total DESC";
        $queryCountCl = "SELECT *, COUNT( D.banco ) AS total FROM consulta A JOIN asignacion B ON A.session = B.session JOIN users C ON B.doctor = C.id JOIN alta_de_eventos D ON A.session = D.session JOIN cliente_ist E ON D.cli_ist = E.id GROUP BY D.banco ORDER BY total DESC";
        $sql = mysqli_query($newCon, $query);
        /* $sqlContDoc = mysqli_query($newCon, $queryCounDoc);
        $sqlContPatient = mysqli_query($newCon, $queryCountPatient);
        $sqlContBancs = mysqli_query($newCon, $queryCountBancs);
        $sqlContCl = mysqli_query($newCon, $queryCountCl); */
    ?>
        <!-- Debuelve un select dinamico -->

        <div class="jumbotron">
            <div class="table-responsive">
                <table class="table" id="consul">
                    <thead>
                        <tr class="bg-primary text-light">
                            <th>ID</th>
                            <th>Paciente</th>
                            <th>Atendido por</th>
                            <th>Fecha</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($arreglo = mysqli_fetch_array($sql)) {
                        ?>
                            <tr>
                                <td scope="row"><?php echo $arreglo[0]; ?></td>
                                <td><?php echo $arreglo[16]; ?></td>
                                <td><?php echo $arreglo[23]; ?></td>
                                <td><?php echo $arreglo[14]; ?></td>
                                <td>
                                <div style="margin: 5px;">
                                                    <form target="_blank" id="searchx<?php echo $arreglo[0]; ?>" action="./print.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo['session']; ?>">
                                                        <button type="submit" id="btnSearchx<?php echo $arreglo[0]; ?>" class="btn btn-success" >
                                                            <i class="fas fa-print"></i>
                                                        </button>
                                                    </form>

                                                </div>
                                    <div style="margin: 5px;">
                                        <form id="search<?php echo $arreglo[0]; ?>" action="../models/doctorVerDetalles.php" method="post">
                                            <input style="display: none;" name="id" type="text" value="<?php echo $arreglo['session']; ?>">
                                            <button type="submit" id="btnSearch<?php echo $arreglo[0]; ?>" class="btn btn-dark" data-toggle="modal" data-target="#completos">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </form>
                                        <script>
                                            /* Funcion Ajax  */
                                            $(document).ready(function() {
                                                $("#search<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                    var btnEnviar = $("#btnSearch<?php echo $arreglo[0]; ?>");
                                                    $.ajax({
                                                        type: $(this).attr("method"),
                                                        url: $(this).attr("action"),
                                                        data: $(this).serialize(),
                                                        beforeSend: function() {
                                                            btnEnviar.val("Enviando");
                                                            btnEnviar.attr("disabled", "disabled");
                                                        },
                                                        complete: function(data) {
                                                            btnEnviar.val("Iniciar");
                                                            btnEnviar.removeAttr("disabled");
                                                        },
                                                        success: function(data) {
                                                            $(".respuestaCom").html(data);
                                                        },
                                                        error: function(data) {
                                                            alert("Problemas al tratar de enviar el formulario");
                                                        },
                                                    });
                                                    return false;
                                                });
                                            });
                                        </script>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <script>
                    /* Funcion Ajax  */
                    $(document).ready(function() {
                        $('#consul').DataTable();
                    });
                </script>
            </div>

        </div>
        <center>
            <h3>Consultas por médico</h3>
        </center>
        <br>

        <div class="jumbotron">
            <div class="table-responsive">
                <table class="table" id="medic">
                    <thead>
                        <tr class="bg-success text-light">
                            <th>Doctor</th>
                            <th>Consultas realizadas</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($arreglo = mysqli_fetch_array($sqlContDoc)) {
                        ?>
                            <tr>
                                <td scope="row"><?php echo $arreglo['name']; ?></td>
                                <td><?php echo $arreglo['total']; ?></td>

                                <td>
                                    <div style="margin: 5px;">
                                        <form id="consul<?php echo $arreglo[0]; ?>" action="../models/doctorPerConsulta.php" method="post">
                                            <input style="display: none;" name="id" type="text" value="<?php echo $arreglo['name']; ?>">
                                            <button type="submit" id="btnConsul<?php echo $arreglo[0]; ?>" class="btn btn-dark" data-toggle="modal" data-target="#completos">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </form>
                                        <script>
                                            /* Funcion Ajax  */
                                            $(document).ready(function() {
                                                $("#consul<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                    var btnEnviar = $("#btnConsul<?php echo $arreglo[0]; ?>");
                                                    $.ajax({
                                                        type: $(this).attr("method"),
                                                        url: $(this).attr("action"),
                                                        data: $(this).serialize(),
                                                        beforeSend: function() {
                                                            btnEnviar.val("Enviando");
                                                            btnEnviar.attr("disabled", "disabled");
                                                        },
                                                        complete: function(data) {
                                                            btnEnviar.val("Iniciar");
                                                            btnEnviar.removeAttr("disabled");
                                                        },
                                                        success: function(data) {
                                                            $(".respuestaCom").html(data);
                                                        },
                                                        error: function(data) {
                                                            alert("Problemas al tratar de enviar el formulario");
                                                        },
                                                    });
                                                    return false;
                                                });
                                            });
                                        </script>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <script>
                    /* Funcion Ajax  */
                    $(document).ready(function() {
                        $('#medic').DataTable();
                    });
                </script>
            </div>

        </div>

        <center>
            <h3>Consultas por paciente</h3>
        </center>
        <br>
        <div class="jumbotron">
            <div class="table-responsive">
                <table class="table" id="patient">
                    <thead>
                        <tr class="bg-info text-light">
                            <th>Paciente</th>
                            <th>Consultas realizadas</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($arreglo = mysqli_fetch_array($sqlContPatient)) {
                        ?>
                            <tr>
                                <td scope="row"><?php echo $arreglo['cliente']; ?></td>
                                <td><?php echo $arreglo['total']; ?></td>

                                <td>
                                    <div style="margin: 5px;">
                                        <form id="patient<?php echo $arreglo[0]; ?>" action="../models/patientPerConsulta.php" method="post">
                                            <input style="display: none ;" name="id" type="text" value="<?php echo $arreglo['cliente']; ?>">
                                            <button type="submit" id="btnpatient<?php echo $arreglo[0]; ?>" class="btn btn-dark" data-toggle="modal" data-target="#completos">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </form>
                                        <script>
                                            /* Funcion Ajax  */
                                            $(document).ready(function() {
                                                $("#patient<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                    var btnEnviar = $("#btnpatient<?php echo $arreglo[0]; ?>");
                                                    $.ajax({
                                                        type: $(this).attr("method"),
                                                        url: $(this).attr("action"),
                                                        data: $(this).serialize(),
                                                        beforeSend: function() {
                                                            btnEnviar.val("Enviando");
                                                            btnEnviar.attr("disabled", "disabled");
                                                        },
                                                        complete: function(data) {
                                                            btnEnviar.val("Iniciar");
                                                            btnEnviar.removeAttr("disabled");
                                                        },
                                                        success: function(data) {
                                                            $(".respuestaCom").html(data);
                                                        },
                                                        error: function(data) {
                                                            alert("Problemas al tratar de enviar el formulario");
                                                        },
                                                    });
                                                    return false;
                                                });
                                            });
                                        </script>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <script>
                    /* Funcion Ajax  */
                    $(document).ready(function() {
                        $('#patient').DataTable();
                    });
                </script>
            </div>

        </div>

        <center>
            <h3>Consultas por cuenta</h3>
        </center>
        <br>
        <div class="jumbotron">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr class="bg-danger text-light">
                            <th>Cuenta</th>
                            <th>Tramites realizados</th>
                            <th>Opciones</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($arreglo = mysqli_fetch_array($sqlContBancs)) {
                        ?>
                            <tr>
                                <td scope="row"><?php echo $arreglo['banco']; ?></td>
                                <td><?php echo $arreglo['total']; ?></td>

                                <td>
                                    <div style="margin: 5px;">
                                        <form id="cuenta<?php echo $arreglo[0]; ?>" action="../models/patientPerCuenta.php" method="post">
                                            <input style="display: none ;" name="id" type="text" value="<?php echo $arreglo['banco']; ?>">
                                            <button type="submit" id="btnCuenta<?php echo $arreglo[0]; ?>" class="btn btn-dark" data-toggle="modal" data-target="#completos">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </form>
                                        <script>
                                            /* Funcion Ajax  */
                                            $(document).ready(function() {
                                                $("#cuenta<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                    var btnEnviar = $("#btnCuenta<?php echo $arreglo[0]; ?>");
                                                    $.ajax({
                                                        type: $(this).attr("method"),
                                                        url: $(this).attr("action"),
                                                        data: $(this).serialize(),
                                                        beforeSend: function() {
                                                            btnEnviar.val("Enviando");
                                                            btnEnviar.attr("disabled", "disabled");
                                                        },
                                                        complete: function(data) {
                                                            btnEnviar.val("Iniciar");
                                                            btnEnviar.removeAttr("disabled");
                                                        },
                                                        success: function(data) {
                                                            $(".respuestaCom").html(data);
                                                        },
                                                        error: function(data) {
                                                            alert("Problemas al tratar de enviar el formulario");
                                                        },
                                                    });
                                                    return false;
                                                });
                                            });
                                        </script>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>

        <center>
            <h3>Consultas por cliente institucional</h3>
        </center>
        <br>
        <div class="jumbotron">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr class="bg-dark text-light">
                            <th>Cliente Institucional</th>
                            <th>Tramites realizados</th>
                            <th>Opciones</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($arreglo = mysqli_fetch_array($sqlContCl)) {
                        ?>
                            <tr>
                                <td scope="row"><?php echo $arreglo[48]; ?></td>
                                <td><?php echo $arreglo['total']; ?></td>

                                <td>
                                    <div style="margin: 5px;">
                                        <form id="cli<?php echo $arreglo[0]; ?>" action="../models/cliPerConsulta.php" method="post">
                                            <input style="display: none ;" name="id" type="text" value="<?php echo $arreglo[48]; ?>">
                                            <button type="submit" id="btnCli<?php echo $arreglo[0]; ?>" class="btn btn-dark" data-toggle="modal" data-target="#completos">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </form>
                                        <script>
                                            /* Funcion Ajax  */
                                            $(document).ready(function() {
                                                $("#cli<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                    var btnEnviar = $("#btnCli<?php echo $arreglo[0]; ?>");
                                                    $.ajax({
                                                        type: $(this).attr("method"),
                                                        url: $(this).attr("action"),
                                                        data: $(this).serialize(),
                                                        beforeSend: function() {
                                                            btnEnviar.val("Enviando");
                                                            btnEnviar.attr("disabled", "disabled");
                                                        },
                                                        complete: function(data) {
                                                            btnEnviar.val("Iniciar");
                                                            btnEnviar.removeAttr("disabled");
                                                        },
                                                        success: function(data) {
                                                            $(".respuestaCom").html(data);
                                                        },
                                                        error: function(data) {
                                                            alert("Problemas al tratar de enviar el formulario");
                                                        },
                                                    });
                                                    return false;
                                                });
                                            });
                                        </script>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>


    <?php
    }
    /*     -----------------------------------Traer consultas por cada doctor-------------------------------- */
    public function consultasPerDoctor($id)
    
    {
        $time = new requests;
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion C JOIN alta_de_eventos D ON C.session = D.session JOIN users E ON C.doctor = E.id WHERE name = '$id' AND tiposerv= 'Consulta' AND status = 'Finalizada'";
        $sql = mysqli_query($newCon, $query);
    ?>
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm" id="exampleTa">
                <thead class="thead-dark">
                    <tr>
                        <th>No.</th>
                        <th>Cliente</th>
                        <th>Status</th>

                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($arreglo = mysqli_fetch_array($sql)) {
                    ?>
                        <tr>
                            <!-- Colores dependiendo el status del evento -->
                            <?php
                            if ($arreglo[2] === "Cancelada") {
                                $color = "bg-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color = "bg-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color = "bg-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color = "bg-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color = "bg-info";
                            }

                            if ($arreglo[2] === "Cancelada") {
                                $color3 = "-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color3 = "-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color3 = "-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color3 = "-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color3 = "-info";
                            }


                            /* Funciones de comparacion de fechas  */
                            $fecha = strftime("%Y-%m-%d %R");
                            $onlyconsonants = str_replace("T", " ", $arreglo[12]);
                            $d1 = new DateTime($fecha);
                            $d2 = new DateTime($onlyconsonants);
                            if ($d1 > $d2) {
                                $compare = "La fecha ya ha pasado <br><br><h5><i style='color: white;' class='fas fa-stopwatch'></i> </h5>";
                                $color2 = "bg-primary";
                            } else {
                                $compare = "A tiempo <br><br><h5><i style='color: white;' class='far fa-clock'></i></h5>";
                                $color2 = "bg-success";
                            }
                            ?>
                            <td style="color: white;" class="<?php echo $color; ?>">
                                <center>
                                    <h5><?php echo $arreglo[0]; ?></h5>
                                </center>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <h4><?php echo $arreglo[1]; ?></h4>
                                <br>
                                <p style="color: black;"><strong><i class="fas fa-user-md"></i> </strong><?php echo $arreglo[24]; ?></p>
                                <br>
                                <p style="color: black;"><i class="far fa-clock"></i> <?php echo $onlyconsonants; ?></p>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <center>
                                    <h4 style="margin-top: 90px;" class="text-dark"><?php echo $arreglo[2]; ?></h4>
                                </center>


                            </td>

                            <td class="table-light">
                                <!-- onfiguracion de la botonera -->
                                <div class="container">

                                    <div class="container">
                                        <div class="row">


                                            <div style="margin: 5px;">
                                                <form id="2asignardoc<?php echo $arreglo[0]; ?>" action="../models/programacionTraerDocs.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[0]; ?>">
                                                    <button type="submit" id="2btnEnviarAsignDoc<?php echo $arreglo[0]; ?>" class="btn btn-danger" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-user-md"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="2edit<?php echo $arreglo[0]; ?>" action="../models/programacionedit.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnedit<?php echo $arreglo[0]; ?>" class="btn btn-info" data-toggle="modal" data-target="#docs">
                                                        <i class="far fa-edit"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="2trash<?php echo $arreglo[0]; ?>" action="../models/programacionTrash.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnTrash<?php echo $arreglo[0]; ?>" class="btn btn-primary">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <div class="trashRespose"></div>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#2trash<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#2btnTrash<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".trashRespose").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="2search<?php echo $arreglo[0]; ?>" action="../models/doctorVerDetalles.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnSearch<?php echo $arreglo[0]; ?>" class="btn btn-dark" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#2search<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#2btnSearch<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>

                                            <div style="margin: 5px;">
                                                <form id="2note<?php echo $arreglo[0]; ?>" action="../models/programacionNota.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnEnviarNote<?php echo $arreglo[0]; ?>" class="btn btn-light" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-notes-medical"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#2note<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#2btnEnviarNote<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>

                                        </div>
                                    </div>
                                    <br>
                                    <center>
                                        <div style="margin: 5px;">
                                            <form action="../models/reception.cart2.php" method="POST" id="2ser<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" value="<?php echo $arreglo[5]; ?>" name="session">
                                                <button type="submit" id="2btnServ<?php echo $arreglo[0]; ?>" class="btn btn-outline-warning btn-lg btn-block" data-toggle="modal" data-target="#docs">
                                                    Servicios <i class="fas fa-briefcase-medical"></i>
                                                </button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#2ser<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#2btnServ<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>
                                    <br>
                                    <center>
                                        <div style="margin: 5px;">
                                            <form action="../models/programacionTraerStatus.php" method="POST" id="2status<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                <button type="sumbit" id="2btnEnviarStatus<?php echo $arreglo[0]; ?>" class="btn btn-outline-primary btn-lg btn-block" data-toggle="modal" data-target="#docs">Cambiar Status <i class="fas fa-toggle-off"></i></button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#2status<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#2btnEnviarStatus<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>
                                    <br>

                                    <center>
                                        <div style="margin: 5px;">
                                            <form action="../models/programacionVerBitacora.php" method="POST" id="2Bitacora<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                <button type="sumbit" id="2btnEnviarBitacora<?php echo $arreglo[0]; ?>" class="btn btn-outline-info btn-lg btn-block" data-toggle="modal" data-target="#docs">Bitacora <i class="fas fa-book-medical"></i></button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#2Bitacora<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#2btnEnviarBitacora<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>

                                </div>
                                <script>
                                    /* Funcion Ajax  */
                                    $(document).ready(function() {
                                        $("#2asignardoc<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                            var btnEnviar = $("#2btnEnviarAsignDoc<?php echo $arreglo[0]; ?>");
                                            $.ajax({
                                                type: $(this).attr("method"),
                                                url: $(this).attr("action"),
                                                data: $(this).serialize(),
                                                beforeSend: function() {
                                                    btnEnviar.val("Enviando");
                                                    btnEnviar.attr("disabled", "disabled");
                                                },
                                                complete: function(data) {
                                                    btnEnviar.val("Iniciar");
                                                    btnEnviar.removeAttr("disabled");
                                                },
                                                success: function(data) {
                                                    $(".respuestaDocs").html(data);
                                                },
                                                error: function(data) {
                                                    alert("Problemas al tratar de enviar el formulario");
                                                },
                                            });
                                            return false;
                                        });
                                    });
                                </script>
                                <script>
                                    /* Funcion Ajax  */
                                    $(document).ready(function() {
                                        $("#2edit<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                            var btnEnviar = $("#2btnedit<?php echo $arreglo[0]; ?>");
                                            $.ajax({
                                                type: $(this).attr("method"),
                                                url: $(this).attr("action"),
                                                data: $(this).serialize(),
                                                beforeSend: function() {
                                                    btnEnviar.val("Enviando");
                                                    btnEnviar.attr("disabled", "disabled");
                                                },
                                                complete: function(data) {
                                                    btnEnviar.val("Iniciar");
                                                    btnEnviar.removeAttr("disabled");
                                                },
                                                success: function(data) {
                                                    $(".respuestaDocs").html(data);
                                                },
                                                error: function(data) {
                                                    alert("Problemas al tratar de enviar el formulario");
                                                },
                                            });
                                            return false;
                                        });
                                    });
                                </script>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            /* Funcion Ajax  */
            $(document).ready(function() {
                $('#exampleTa').DataTable();
            });
        </script>
    <?php
    }
    /*  --------------------------------Traer consultas por paciente--------------------------------------------------------------- */
    public function consultasPerPatient($id)
    {
        $time = new requests;
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion C JOIN alta_de_eventos D ON C.session = D.session JOIN users E ON C.doctor = E.id WHERE cliente = '$id' AND tiposerv= 'Consulta' AND status = 'Finalizada'";
        $sql = mysqli_query($newCon, $query);
    ?>
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm" id="exampleTa">
                <thead class="thead-dark">
                    <tr>
                        <th>No.</th>
                        <th>Cliente</th>
                        <th>Status</th>

                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($arreglo = mysqli_fetch_array($sql)) {
                    ?>
                        <tr>
                            <!-- Colores dependiendo el status del evento -->
                            <?php
                            if ($arreglo[2] === "Cancelada") {
                                $color = "bg-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color = "bg-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color = "bg-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color = "bg-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color = "bg-info";
                            }

                            if ($arreglo[2] === "Cancelada") {
                                $color3 = "-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color3 = "-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color3 = "-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color3 = "-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color3 = "-info";
                            }


                            /* Funciones de comparacion de fechas  */
                            $fecha = strftime("%Y-%m-%d %R");
                            $onlyconsonants = str_replace("T", " ", $arreglo[12]);
                            $d1 = new DateTime($fecha);
                            $d2 = new DateTime($onlyconsonants);
                            if ($d1 > $d2) {
                                $compare = "La fecha ya ha pasado <br><br><h5><i style='color: white;' class='fas fa-stopwatch'></i> </h5>";
                                $color2 = "bg-primary";
                            } else {
                                $compare = "A tiempo <br><br><h5><i style='color: white;' class='far fa-clock'></i></h5>";
                                $color2 = "bg-success";
                            }
                            ?>
                            <td style="color: white;" class="<?php echo $color; ?>">
                                <center>
                                    <h5><?php echo $arreglo[0]; ?></h5>
                                </center>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <h4><?php echo $arreglo[1]; ?></h4>
                                <br>
                                <p style="color: black;"><strong><i class="fas fa-user-md"></i> </strong><?php echo $arreglo[24]; ?></p>
                                <br>
                                <p style="color: black;"><i class="far fa-clock"></i> <?php echo $onlyconsonants; ?></p>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <center>
                                    <h4 style="margin-top: 90px;" class="text-dark"><?php echo $arreglo[2]; ?></h4>
                                </center>


                            </td>

                            <td class="table-light">
                                <!-- onfiguracion de la botonera -->
                                <div class="container">

                                    <div class="container">
                                        <div class="row">


                                            <div style="margin: 5px;">
                                                <form id="2asignardoc<?php echo $arreglo[0]; ?>" action="../models/programacionTraerDocs.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[0]; ?>">
                                                    <button type="submit" id="2btnEnviarAsignDoc<?php echo $arreglo[0]; ?>" class="btn btn-danger" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-user-md"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="2edit<?php echo $arreglo[0]; ?>" action="../models/programacionedit.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnedit<?php echo $arreglo[0]; ?>" class="btn btn-info" data-toggle="modal" data-target="#docs">
                                                        <i class="far fa-edit"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="2trash<?php echo $arreglo[0]; ?>" action="../models/programacionTrash.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnTrash<?php echo $arreglo[0]; ?>" class="btn btn-primary">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <div class="trashRespose"></div>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#2trash<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#2btnTrash<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".trashRespose").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="2search<?php echo $arreglo[0]; ?>" action="../models/doctorVerDetalles.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnSearch<?php echo $arreglo[0]; ?>" class="btn btn-dark" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#2search<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#2btnSearch<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>

                                            <div style="margin: 5px;">
                                                <form id="2note<?php echo $arreglo[0]; ?>" action="../models/programacionNota.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnEnviarNote<?php echo $arreglo[0]; ?>" class="btn btn-light" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-notes-medical"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#2note<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#2btnEnviarNote<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>

                                        </div>
                                    </div>
                                    <br>
                                    <center>
                                        <div style="margin: 5px;">
                                            <form action="../models/reception.cart2.php" method="POST" id="2ser<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" value="<?php echo $arreglo[5]; ?>" name="session">
                                                <button type="submit" id="2btnServ<?php echo $arreglo[0]; ?>" class="btn btn-outline-warning btn-lg btn-block" data-toggle="modal" data-target="#docs">
                                                    Servicios <i class="fas fa-briefcase-medical"></i>
                                                </button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#2ser<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#2btnServ<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>
                                    <br>
                                    <center>
                                        <div style="margin: 5px;">
                                            <form action="../models/programacionTraerStatus.php" method="POST" id="2status<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                <button type="sumbit" id="2btnEnviarStatus<?php echo $arreglo[0]; ?>" class="btn btn-outline-primary btn-lg btn-block" data-toggle="modal" data-target="#docs">Cambiar Status <i class="fas fa-toggle-off"></i></button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#2status<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#2btnEnviarStatus<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>
                                    <br>

                                    <center>
                                        <div style="margin: 5px;">
                                            <form action="../models/programacionVerBitacora.php" method="POST" id="2Bitacora<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                <button type="sumbit" id="2btnEnviarBitacora<?php echo $arreglo[0]; ?>" class="btn btn-outline-info btn-lg btn-block" data-toggle="modal" data-target="#docs">Bitacora <i class="fas fa-book-medical"></i></button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#2Bitacora<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#2btnEnviarBitacora<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>

                                </div>
                                <script>
                                    /* Funcion Ajax  */
                                    $(document).ready(function() {
                                        $("#2asignardoc<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                            var btnEnviar = $("#2btnEnviarAsignDoc<?php echo $arreglo[0]; ?>");
                                            $.ajax({
                                                type: $(this).attr("method"),
                                                url: $(this).attr("action"),
                                                data: $(this).serialize(),
                                                beforeSend: function() {
                                                    btnEnviar.val("Enviando");
                                                    btnEnviar.attr("disabled", "disabled");
                                                },
                                                complete: function(data) {
                                                    btnEnviar.val("Iniciar");
                                                    btnEnviar.removeAttr("disabled");
                                                },
                                                success: function(data) {
                                                    $(".respuestaDocs").html(data);
                                                },
                                                error: function(data) {
                                                    alert("Problemas al tratar de enviar el formulario");
                                                },
                                            });
                                            return false;
                                        });
                                    });
                                </script>
                                <script>
                                    /* Funcion Ajax  */
                                    $(document).ready(function() {
                                        $("#2edit<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                            var btnEnviar = $("#2btnedit<?php echo $arreglo[0]; ?>");
                                            $.ajax({
                                                type: $(this).attr("method"),
                                                url: $(this).attr("action"),
                                                data: $(this).serialize(),
                                                beforeSend: function() {
                                                    btnEnviar.val("Enviando");
                                                    btnEnviar.attr("disabled", "disabled");
                                                },
                                                complete: function(data) {
                                                    btnEnviar.val("Iniciar");
                                                    btnEnviar.removeAttr("disabled");
                                                },
                                                success: function(data) {
                                                    $(".respuestaDocs").html(data);
                                                },
                                                error: function(data) {
                                                    alert("Problemas al tratar de enviar el formulario");
                                                },
                                            });
                                            return false;
                                        });
                                    });
                                </script>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            /* Funcion Ajax  */
            $(document).ready(function() {
                $('#exampleTa').DataTable();
            });
        </script>
    <?php
    }
    /* ------------------------------------------Traer consultas por cuenta--------------------------------------------- */
    public function consultasPerCuenta($id)
    {
        $time = new requests;
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion C JOIN alta_de_eventos D ON C.session = D.session JOIN users E ON C.doctor = E.id WHERE banco = '$id' AND tiposerv= 'Consulta' AND status = 'Finalizada'";
        $sql = mysqli_query($newCon, $query);
    ?>
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm" id="exampleTa">
                <thead class="thead-dark">
                    <tr>
                        <th>No.</th>
                        <th>Cliente</th>
                        <th>Status</th>

                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($arreglo = mysqli_fetch_array($sql)) {
                    ?>
                        <tr>
                            <!-- Colores dependiendo el status del evento -->
                            <?php
                            if ($arreglo[2] === "Cancelada") {
                                $color = "bg-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color = "bg-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color = "bg-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color = "bg-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color = "bg-info";
                            }

                            if ($arreglo[2] === "Cancelada") {
                                $color3 = "-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color3 = "-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color3 = "-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color3 = "-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color3 = "-info";
                            }


                            /* Funciones de comparacion de fechas  */
                            $fecha = strftime("%Y-%m-%d %R");
                            $onlyconsonants = str_replace("T", " ", $arreglo[12]);
                            $d1 = new DateTime($fecha);
                            $d2 = new DateTime($onlyconsonants);
                            if ($d1 > $d2) {
                                $compare = "La fecha ya ha pasado <br><br><h5><i style='color: white;' class='fas fa-stopwatch'></i> </h5>";
                                $color2 = "bg-primary";
                            } else {
                                $compare = "A tiempo <br><br><h5><i style='color: white;' class='far fa-clock'></i></h5>";
                                $color2 = "bg-success";
                            }
                            ?>
                            <td style="color: white;" class="<?php echo $color; ?>">
                                <center>
                                    <h5><?php echo $arreglo[0]; ?></h5>
                                </center>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <h4><?php echo $arreglo[1]; ?></h4>
                                <br>
                                <p style="color: black;"><strong><i class="fas fa-user-md"></i> </strong><?php echo $arreglo[24]; ?></p>
                                <br>
                                <p style="color: black;"><i class="far fa-clock"></i> <?php echo $onlyconsonants; ?></p>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <center>
                                    <h4 style="margin-top: 90px;" class="text-dark"><?php echo $arreglo[2]; ?></h4>
                                </center>


                            </td>

                            <td class="table-light">
                                <!-- onfiguracion de la botonera -->
                                <div class="container">

                                    <div class="container">
                                        <div class="row">


                                            <div style="margin: 5px;">
                                                <form id="2asignardoc<?php echo $arreglo[0]; ?>" action="../models/programacionTraerDocs.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[0]; ?>">
                                                    <button type="submit" id="2btnEnviarAsignDoc<?php echo $arreglo[0]; ?>" class="btn btn-danger" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-user-md"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="2edit<?php echo $arreglo[0]; ?>" action="../models/programacionedit.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnedit<?php echo $arreglo[0]; ?>" class="btn btn-info" data-toggle="modal" data-target="#docs">
                                                        <i class="far fa-edit"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="2trash<?php echo $arreglo[0]; ?>" action="../models/programacionTrash.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnTrash<?php echo $arreglo[0]; ?>" class="btn btn-primary">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <div class="trashRespose"></div>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#2trash<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#2btnTrash<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".trashRespose").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="2search<?php echo $arreglo[0]; ?>" action="../models/doctorVerDetalles.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnSearch<?php echo $arreglo[0]; ?>" class="btn btn-dark" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#2search<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#2btnSearch<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>

                                            <div style="margin: 5px;">
                                                <form id="2note<?php echo $arreglo[0]; ?>" action="../models/programacionNota.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnEnviarNote<?php echo $arreglo[0]; ?>" class="btn btn-light" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-notes-medical"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#2note<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#2btnEnviarNote<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>

                                        </div>
                                    </div>
                                    <br>
                                    <center>
                                        <div style="margin: 5px;">
                                            <form action="../models/reception.cart2.php" method="POST" id="2ser<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" value="<?php echo $arreglo[5]; ?>" name="session">
                                                <button type="submit" id="2btnServ<?php echo $arreglo[0]; ?>" class="btn btn-outline-warning btn-lg btn-block" data-toggle="modal" data-target="#docs">
                                                    Servicios <i class="fas fa-briefcase-medical"></i>
                                                </button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#2ser<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#2btnServ<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>
                                    <br>
                                    <center>
                                        <div style="margin: 5px;">
                                            <form action="../models/programacionTraerStatus.php" method="POST" id="2status<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                <button type="sumbit" id="2btnEnviarStatus<?php echo $arreglo[0]; ?>" class="btn btn-outline-primary btn-lg btn-block" data-toggle="modal" data-target="#docs">Cambiar Status <i class="fas fa-toggle-off"></i></button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#2status<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#2btnEnviarStatus<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>
                                    <br>

                                    <center>
                                        <div style="margin: 5px;">
                                            <form action="../models/programacionVerBitacora.php" method="POST" id="2Bitacora<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                <button type="sumbit" id="2btnEnviarBitacora<?php echo $arreglo[0]; ?>" class="btn btn-outline-info btn-lg btn-block" data-toggle="modal" data-target="#docs">Bitacora <i class="fas fa-book-medical"></i></button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#2Bitacora<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#2btnEnviarBitacora<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>

                                </div>
                                <script>
                                    /* Funcion Ajax  */
                                    $(document).ready(function() {
                                        $("#2asignardoc<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                            var btnEnviar = $("#2btnEnviarAsignDoc<?php echo $arreglo[0]; ?>");
                                            $.ajax({
                                                type: $(this).attr("method"),
                                                url: $(this).attr("action"),
                                                data: $(this).serialize(),
                                                beforeSend: function() {
                                                    btnEnviar.val("Enviando");
                                                    btnEnviar.attr("disabled", "disabled");
                                                },
                                                complete: function(data) {
                                                    btnEnviar.val("Iniciar");
                                                    btnEnviar.removeAttr("disabled");
                                                },
                                                success: function(data) {
                                                    $(".respuestaDocs").html(data);
                                                },
                                                error: function(data) {
                                                    alert("Problemas al tratar de enviar el formulario");
                                                },
                                            });
                                            return false;
                                        });
                                    });
                                </script>
                                <script>
                                    /* Funcion Ajax  */
                                    $(document).ready(function() {
                                        $("#2edit<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                            var btnEnviar = $("#2btnedit<?php echo $arreglo[0]; ?>");
                                            $.ajax({
                                                type: $(this).attr("method"),
                                                url: $(this).attr("action"),
                                                data: $(this).serialize(),
                                                beforeSend: function() {
                                                    btnEnviar.val("Enviando");
                                                    btnEnviar.attr("disabled", "disabled");
                                                },
                                                complete: function(data) {
                                                    btnEnviar.val("Iniciar");
                                                    btnEnviar.removeAttr("disabled");
                                                },
                                                success: function(data) {
                                                    $(".respuestaDocs").html(data);
                                                },
                                                error: function(data) {
                                                    alert("Problemas al tratar de enviar el formulario");
                                                },
                                            });
                                            return false;
                                        });
                                    });
                                </script>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            /* Funcion Ajax  */
            $(document).ready(function() {
                $('#exampleTa').DataTable();
            });
        </script>
    <?php
    }
    /* ------------------------Consultas por pacientes-------------------------------- */
    public function consultasPerCliente($id)
    {
        $time = new requests;
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion C JOIN alta_de_eventos D ON C.session = D.session JOIN users E ON C.doctor = E.id JOIN cliente_ist A ON D.cli_ist = A.id WHERE A.nombre = '$id' AND tiposerv= 'Consulta' AND status = 'Finalizada'";
        $sql = mysqli_query($newCon, $query);
    ?>
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm" id="exampleTa">
                <thead class="thead-dark">
                    <tr>
                        <th>No.</th>
                        <th>Cliente</th>
                        <th>Status</th>

                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($arreglo = mysqli_fetch_array($sql)) {
                    ?>
                        <tr>
                            <!-- Colores dependiendo el status del evento -->
                            <?php
                            if ($arreglo[2] === "Cancelada") {
                                $color = "bg-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color = "bg-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color = "bg-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color = "bg-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color = "bg-info";
                            }

                            if ($arreglo[2] === "Cancelada") {
                                $color3 = "-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color3 = "-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color3 = "-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color3 = "-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color3 = "-info";
                            }


                            /* Funciones de comparacion de fechas  */
                            $fecha = strftime("%Y-%m-%d %R");
                            $onlyconsonants = str_replace("T", " ", $arreglo[12]);
                            $d1 = new DateTime($fecha);
                            $d2 = new DateTime($onlyconsonants);
                            if ($d1 > $d2) {
                                $compare = "La fecha ya ha pasado <br><br><h5><i style='color: white;' class='fas fa-stopwatch'></i> </h5>";
                                $color2 = "bg-primary";
                            } else {
                                $compare = "A tiempo <br><br><h5><i style='color: white;' class='far fa-clock'></i></h5>";
                                $color2 = "bg-success";
                            }
                            ?>
                            <td style="color: white;" class="<?php echo $color; ?>">
                                <center>
                                    <h5><?php echo $arreglo[0]; ?></h5>
                                </center>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <h4><?php echo $arreglo[1]; ?></h4>
                                <br>
                                <p style="color: black;"><strong><i class="fas fa-user-md"></i> </strong><?php echo $arreglo[24]; ?></p>
                                <br>
                                <p style="color: black;"><i class="far fa-clock"></i> <?php echo $onlyconsonants; ?></p>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <center>
                                    <h4 style="margin-top: 90px;" class="text-dark"><?php echo $arreglo[2]; ?></h4>
                                </center>


                            </td>

                            <td class="table-light">
                                <!-- onfiguracion de la botonera -->
                                <div class="container">

                                    <div class="container">
                                        <div class="row">


                                            <div style="margin: 5px;">
                                                <form id="2asignardoc<?php echo $arreglo[0]; ?>" action="../models/programacionTraerDocs.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[0]; ?>">
                                                    <button type="submit" id="2btnEnviarAsignDoc<?php echo $arreglo[0]; ?>" class="btn btn-danger" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-user-md"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="2edit<?php echo $arreglo[0]; ?>" action="../models/programacionedit.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnedit<?php echo $arreglo[0]; ?>" class="btn btn-info" data-toggle="modal" data-target="#docs">
                                                        <i class="far fa-edit"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="2trash<?php echo $arreglo[0]; ?>" action="../models/programacionTrash.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnTrash<?php echo $arreglo[0]; ?>" class="btn btn-primary">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <div class="trashRespose"></div>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#2trash<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#2btnTrash<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".trashRespose").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                            <div style="margin: 5px;">
                                                <form id="2search<?php echo $arreglo[0]; ?>" action="../models/doctorVerDetalles.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnSearch<?php echo $arreglo[0]; ?>" class="btn btn-dark" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#2search<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#2btnSearch<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>

                                            <div style="margin: 5px;">
                                                <form id="2note<?php echo $arreglo[0]; ?>" action="../models/programacionNota.php" method="post">
                                                    <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="submit" id="2btnEnviarNote<?php echo $arreglo[0]; ?>" class="btn btn-light" data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-notes-medical"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#2note<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#2btnEnviarNote<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>

                                        </div>
                                    </div>
                                    <br>
                                    <center>
                                        <div style="margin: 5px;">
                                            <form action="../models/reception.cart2.php" method="POST" id="2ser<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" value="<?php echo $arreglo[5]; ?>" name="session">
                                                <button type="submit" id="2btnServ<?php echo $arreglo[0]; ?>" class="btn btn-outline-warning btn-lg btn-block" data-toggle="modal" data-target="#docs">
                                                    Servicios <i class="fas fa-briefcase-medical"></i>
                                                </button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#2ser<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#2btnServ<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>
                                    <br>
                                    <center>
                                        <div style="margin: 5px;">
                                            <form action="../models/programacionTraerStatus.php" method="POST" id="2status<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                <button type="sumbit" id="2btnEnviarStatus<?php echo $arreglo[0]; ?>" class="btn btn-outline-primary btn-lg btn-block" data-toggle="modal" data-target="#docs">Cambiar Status <i class="fas fa-toggle-off"></i></button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#2status<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#2btnEnviarStatus<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>
                                    <br>

                                    <center>
                                        <div style="margin: 5px;">
                                            <form action="../models/programacionVerBitacora.php" method="POST" id="2Bitacora<?php echo $arreglo[0]; ?>">
                                                <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                <button type="sumbit" id="2btnEnviarBitacora<?php echo $arreglo[0]; ?>" class="btn btn-outline-info btn-lg btn-block" data-toggle="modal" data-target="#docs">Bitacora <i class="fas fa-book-medical"></i></button>
                                            </form>
                                            <script>
                                                /* Funcion Ajax  */
                                                $(document).ready(function() {
                                                    $("#2Bitacora<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                        var btnEnviar = $("#2btnEnviarBitacora<?php echo $arreglo[0]; ?>");
                                                        $.ajax({
                                                            type: $(this).attr("method"),
                                                            url: $(this).attr("action"),
                                                            data: $(this).serialize(),
                                                            beforeSend: function() {
                                                                btnEnviar.val("Enviando");
                                                                btnEnviar.attr("disabled", "disabled");
                                                            },
                                                            complete: function(data) {
                                                                btnEnviar.val("Iniciar");
                                                                btnEnviar.removeAttr("disabled");
                                                            },
                                                            success: function(data) {
                                                                $(".respuestaDocs").html(data);
                                                            },
                                                            error: function(data) {
                                                                alert("Problemas al tratar de enviar el formulario");
                                                            },
                                                        });
                                                        return false;
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </center>

                                </div>
                                <script>
                                    /* Funcion Ajax  */
                                    $(document).ready(function() {
                                        $("#2asignardoc<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                            var btnEnviar = $("#2btnEnviarAsignDoc<?php echo $arreglo[0]; ?>");
                                            $.ajax({
                                                type: $(this).attr("method"),
                                                url: $(this).attr("action"),
                                                data: $(this).serialize(),
                                                beforeSend: function() {
                                                    btnEnviar.val("Enviando");
                                                    btnEnviar.attr("disabled", "disabled");
                                                },
                                                complete: function(data) {
                                                    btnEnviar.val("Iniciar");
                                                    btnEnviar.removeAttr("disabled");
                                                },
                                                success: function(data) {
                                                    $(".respuestaDocs").html(data);
                                                },
                                                error: function(data) {
                                                    alert("Problemas al tratar de enviar el formulario");
                                                },
                                            });
                                            return false;
                                        });
                                    });
                                </script>
                                <script>
                                    /* Funcion Ajax  */
                                    $(document).ready(function() {
                                        $("#2edit<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                            var btnEnviar = $("#2btnedit<?php echo $arreglo[0]; ?>");
                                            $.ajax({
                                                type: $(this).attr("method"),
                                                url: $(this).attr("action"),
                                                data: $(this).serialize(),
                                                beforeSend: function() {
                                                    btnEnviar.val("Enviando");
                                                    btnEnviar.attr("disabled", "disabled");
                                                },
                                                complete: function(data) {
                                                    btnEnviar.val("Iniciar");
                                                    btnEnviar.removeAttr("disabled");
                                                },
                                                success: function(data) {
                                                    $(".respuestaDocs").html(data);
                                                },
                                                error: function(data) {
                                                    alert("Problemas al tratar de enviar el formulario");
                                                },
                                            });
                                            return false;
                                        });
                                    });
                                </script>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            $(document).ready(function() {
                $('#exampleTa').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ]
                });
            });
        </script>
    <?php
    }
    /* ----------------------------------------------Reportes generales--------------------------------------- */
    /* Lista todos los reportes generales para area de facturacion */
    public function all($f1, $f2)
    {
        $db = new con;
        $newCon = $db->sql();
        /* $query = "SELECT * FROM cart A JOIN alta_de_eventos B ON A.session = B.session JOIN asignacion C ON C.session = B.session JOIN users D ON D.id = C.doctor JOIN pacientes F ON F.nomina = B.nombre_paciente"; */
        $query = "SELECT * FROM cart A JOIN alta_de_eventos B ON A.session = B.session JOIN asignacion C ON C.session = B.session JOIN users D ON D.id = C.doctor ORDER BY A.id DESC LIMIT 100;";
        $queryCompare = "SELECT * FROM cart A JOIN alta_de_eventos B ON A.session = B.session JOIN asignacion C ON C.session = B.session JOIN users D ON D.id = C.doctor WHERE fecha_prog BETWEEN '$f1' AND '$f2' ORDER BY A.id DESC ";
        if (empty($f1)) {
            $sql = mysqli_query($newCon, $query);
        } else {
            $sql = mysqli_query($newCon, $queryCompare);
        }


    ?>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">

        <div class="col-12">
        <div class="container">
            <div class="jumbotron jumbotron-fluid">
                <div class="container-fluid" >
                <div class="table-responsive">
                <table id="example" class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th>F.S</th>
                        <th>No.A</th>
                        <th>Nomina</th>
                        <th>Paciente</th>
                        <th>M.A</th>
                        <th>Deducible</th>
                        <th>Estatus</th>
                        <th>Banco</th>
                        <th>A.E</th>
                        <!-- <th>Alcaldia</th>
                        <th>Telefono</th>
                        <th>Cuenta</th> -->
                        <th>Diagnostico</th>


                    </tr>
                </thead>
                <tbody>
                    <?php while ($arreglo = mysqli_fetch_array($sql)) { ?>
                        <tr>
                            <td><?php echo $arreglo[1]; ?></td>
                            <td><?php echo $arreglo[13]; ?></td>
                            <td><?php echo $arreglo[3]; ?></td>
                            <td><?php echo $arreglo[14]; ?></td>
                            <td><?php echo $arreglo[25]; ?></td>
                            <td><?php echo $arreglo[32]; ?></td>
                            <td><?php echo $arreglo[5]; ?></td>
                            <td><?php echo $arreglo[26]; ?></td>
                            <td><?php echo $arreglo['banco']; ?></td>
                            <td><?php echo $arreglo['folio']; ?></td>
                            <!-- <td><?php /* echo $arreglo['municipio'];  */?></td>
                            <td><?php /* echo $arreglo['telefono']; */ ?></td>
                            <td></td> -->
                            <td><?php echo $arreglo['motivos']; ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>

            </table>
                </div>
                </div>
            
            </div>
        </div>
        
        </div>
        

        <!-- Funciones para imprimir en diferentes formatos -->

        <script>
            $(document).ready(function() {
                $('#example').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5'
                    ]
                });
            });
        </script>

    <?php
    }
    /* ----------------------------------------------Reportes generales--------------------------------------- */
    /*Lista los reportes de servicios en general*/
    public function all2($f1, $f2)
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM history_services A JOIN alta_de_eventos B ON A.session = B.session JOIN asignacion C ON A.session = C.session JOIN users E ON C.doctor = E.id JOIN pacientes F ON B.nombre_paciente = F.nomina";
        $queryCompare = "SELECT * FROM history_services A JOIN alta_de_eventos B ON A.session = B.session JOIN asignacion C ON A.session = C.session JOIN consulta D ON A.session = D.session JOIN users E ON C.doctor = E.id WHERE date BETWEEN '$f1' AND '$f2'";
        if (empty($f1)) {
            $sql = mysqli_query($newCon, $query);
        } else {
            $sql = mysqli_query($newCon, $queryCompare);
        }


    ?>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">



        <div class="table-responsive">
            <table id="example2" class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th>Fecha de servicio</th>
                        <th>No.A</th>
                        <th>No.A.E</th>
                        <th>Nomina</th>
                        <th>Paciente</th>
                        <th>Acaldia</th>
                        <th>Diagnostico</th>
                        <th>Deducible</th>
                        <th>Estatus</th>
                        <th>Cuenta</th>
                        <th>Medico</th>
                        <th>Observaciónes</th>


                    </tr>
                </thead>
                <tbody>
                    <?php while ($arreglo = mysqli_fetch_array($sql)) { ?>
                        <tr>
                            <td><?php echo $arreglo['serv']; ?></td>
                            <td><?php echo $arreglo['fecha_prog']; ?></td>
                            <td><?php echo $arreglo['no- auto']; ?></td>
                            <td><?php echo $arreglo['folio']; ?></td>
                            <td><?php echo $arreglo['nombre_paciente']; ?></td>
                            <td><?php echo $arreglo[45] . " " . $arreglo[46] . " " . $arreglo[47]; ?></td>
                            <td><?php echo $arreglo['municipio']; ?></td>
                            <td><?php echo $arreglo['motivos']; ?></td>
                            <td><?php echo $arreglo[5]; ?></td>

                            <td><?php echo $arreglo['status']; ?></td>
                            <td><?php echo $arreglo['banco']; ?></td>
                            <td><?php echo $arreglo['name']; ?></td>
                            <td><?php echo $arreglo['obs']; ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>

            </table>
        </div>

        <!-- Funciones para imprimir en diferentes formatos -->

        <script>
            $(document).ready(function() {
                $('#example2').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5'
                    ]
                });
            });
        </script>

    <?php
    }

    /* Funcion para añadir consultas negadas */
    public function addnegado($clients, $cuentas, $nombresol, $fechap, $ubica, $obs)
    {
        $db = new con;
        $newCon = $db->sql();
        setlocale(LC_ALL, 'es_MX.UTF-8');
        $time = strftime("%A %B %G %H:%M");
        $query = "INSERT INTO `negadas` (`id`, `cliente`, `cuenta`, `localidad`, `fecha`, `motivo`, `hserv`, `user`) VALUES (NULL, '$clients', '$cuentas', '$ubica', '$fechap', '$obs', '$time', '$nombresol')";
        $sql = mysqli_query($newCon, $query);
    ?>
        <p>Evento registrado</p>

    <?php
    }
    /* Funcion para ver las consultas negadas */
    public function shownegados()
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM `negadas`";
        $sql = mysqli_query($newCon, $query);
    ?>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">



        <div class="table-responsive">
            <table id="examplex" class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Cuenta</th>
                        <th>Localidad</th>
                        <th>Fecha</th>
                        <th>Motivo</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($arreglo = mysqli_fetch_array($sql)) { ?>
                        <tr>
                            <td><?php echo $arreglo[0]; ?></td>
                            <td><?php echo $arreglo[2]; ?></td>
                            <td><?php echo $arreglo[3]; ?></td>
                            <td><?php echo $arreglo[4]; ?></td>
                            <td><?php echo $arreglo[5]; ?></td>
                            <td><?php echo $arreglo[7]; ?></td>

                        </tr>
                    <?php
                    }
                    ?>
                </tbody>

            </table>
        </div>
        <script>
            $(document).ready(function() {
                $('#examplex').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5'
                    ]
                });
            });
        </script>
    <?php
    }
    /* Lista todas las superviciones hospitalarias en el panel de programacion */
    public function sasigsuper()
    {

        $time = new requests;
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion C JOIN alta_de_eventos D ON C.session = D.session JOIN users E ON C.doctor = E.id WHERE doctor != 'null' AND tiposerv= 'Supervisión Hospitalaria' AND status != 'Finalizada' GROUP BY C.id ORDER BY C.id DESC";
        $sql = mysqli_query($newCon, $query);
    ?>
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
        <div class="table-responsive">
            <table class="table  table-striped  table-sm" id="tablesuperv">
                <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th>Tiempo</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($arreglo = mysqli_fetch_array($sql)) {
                    ?>
                        <tr>
                            <!-- Colores dependiendo el status del evento -->
                            <?php
                            if ($arreglo[2] === "Cancelada") {
                                $color = "text-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color = "text-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color = "text-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color = "text-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color = "text-info";
                            }

                            if ($arreglo[2] === "Cancelada") {
                                $color3 = "-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color3 = "-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color3 = "-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color3 = "-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color3 = "-info";
                            }


                            /* Funciones de comparacion de fechas  */
                            $fecha = strftime("%Y-%m-%d %R");
                            $onlyconsonants = str_replace("T", " ", $arreglo[12]);
                            $d1 = new DateTime($fecha);
                            $d2 = new DateTime($onlyconsonants);
                            if ($d1 > $d2) {
                                $compare = "La fecha ya ha pasado <br><h5><i style='color: #ff0833;' class='fas fa-stopwatch'></i> </h5>";
                                $color2 = "bg-primary";
                            } else {
                                $compare = "A tiempo <br><br><h5><i style='color: #029f00;' class='far fa-clock'></i></h5>";
                                $color2 = "bg-success";
                            }
                            ?>
                            <td>
                                <center>

                                    <i class="fas fa-circle <?php echo $color; ?>"></i>
                                </center>
                            </td>
                            <td class="">
                                <center>
                                    <h5><?php echo $arreglo[0]; ?></h5>

                                </center>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <h4><?php echo $arreglo[1]; ?></h4>
                                <br>
                                <p style="color: black;"><strong><i class="fas fa-user-md"></i> </strong><?php echo $arreglo[24]; ?></p>
                                <br>
                                <div class="collapse" id="collapseExample<?php echo $arreglo[0]; ?>">

                                    <div class="container">

                                        <div class="container">
                                            <div class="row">


                                                <div style="margin: 5px;">
                                                    <form id="asignardoc<?php echo $arreglo[0]; ?>" action="../models/programacionTraerDocs.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[0]; ?>">
                                                        <button type="submit" id="btnEnviarAsignDoc<?php echo $arreglo[0]; ?>" class="btn btn-danger" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-user-md"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <div style="margin: 5px;">
                                                    <form id="edit<?php echo $arreglo[0]; ?>" action="../models/programacionedit.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnedit<?php echo $arreglo[0]; ?>" class="btn btn-info" data-toggle="modal" data-target="#docs">
                                                            <i class="far fa-edit"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <div style="margin: 5px;">
                                                    <form id="trash<?php echo $arreglo[0]; ?>" action="../models/programacionTrash.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnTrash<?php echo $arreglo[0]; ?>" class="btn btn-primary">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    <div class="trashRespose"></div>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#trash<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnTrash<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".trashRespose").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                <div style="margin: 5px;">
                                                    <form id="search<?php echo $arreglo[0]; ?>" action="../models/doctorVerDetalles.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnSearch<?php echo $arreglo[0]; ?>" class="btn btn-dark" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#search<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnSearch<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>

                                                <div style="margin: 5px;">
                                                    <form id="note<?php echo $arreglo[0]; ?>" action="../models/programacionNota.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnEnviarNote<?php echo $arreglo[0]; ?>" class="btn btn-success" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-notes-medical"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#note<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnEnviarNote<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                <div style="margin: 5px;">
                                                    <form id="arch<?php echo $arreglo[0]; ?>" action="../models/doctorRecetaEdit.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnEnviarArch<?php echo $arreglo[0]; ?>" class="btn btn-warning" data-toggle="modal" data-target="#docs">
                                                            <i class="far fa-id-badge"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#arch<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnEnviarArch<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                <div style="margin: 5px;">
                                                    <form id="checksuper<?php echo $arreglo[0]; ?>" action="../models/addcheksuper.php" method="post">
                                                        <input style="display: none;" name="ids" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnEnviarchecksuper<?php echo $arreglo[0]; ?>" class="btn btn-success" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-money-check-alt"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#checksuper<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnEnviarchecksuper<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>

                                            </div>

                                        </div>

                                    </div>
                                    <script>
                                        /* Funcion Ajax  */
                                        $(document).ready(function() {
                                            $("#asignardoc<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                var btnEnviar = $("#btnEnviarAsignDoc<?php echo $arreglo[0]; ?>");
                                                $.ajax({
                                                    type: $(this).attr("method"),
                                                    url: $(this).attr("action"),
                                                    data: $(this).serialize(),
                                                    beforeSend: function() {
                                                        btnEnviar.val("Enviando");
                                                        btnEnviar.attr("disabled", "disabled");
                                                    },
                                                    complete: function(data) {
                                                        btnEnviar.val("Iniciar");
                                                        btnEnviar.removeAttr("disabled");
                                                    },
                                                    success: function(data) {
                                                        $(".respuestaDocs").html(data);
                                                    },
                                                    error: function(data) {
                                                        alert("Problemas al tratar de enviar el formulario");
                                                    },
                                                });
                                                return false;
                                            });
                                        });
                                    </script>
                                    <script>
                                        /* Funcion Ajax  */
                                        $(document).ready(function() {
                                            $("#edit<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                var btnEnviar = $("#btnedit<?php echo $arreglo[0]; ?>");
                                                $.ajax({
                                                    type: $(this).attr("method"),
                                                    url: $(this).attr("action"),
                                                    data: $(this).serialize(),
                                                    beforeSend: function() {
                                                        btnEnviar.val("Enviando");
                                                        btnEnviar.attr("disabled", "disabled");
                                                    },
                                                    complete: function(data) {
                                                        btnEnviar.val("Iniciar");
                                                        btnEnviar.removeAttr("disabled");
                                                    },
                                                    success: function(data) {
                                                        $(".respuestaDocs").html(data);
                                                    },
                                                    error: function(data) {
                                                        alert("Problemas al tratar de enviar el formulario");
                                                    },
                                                });
                                                return false;
                                            });
                                        });
                                    </script>


                                    <br>
                                </div>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <center>
                                    <h5 class="text-dark"><?php echo $arreglo[2]; ?></h5>
                                    <br>
                                    <p style="color: black;"><i class="far fa-clock"></i> <?php echo $onlyconsonants; ?></p>
                                </center>
                                <br>
                                <br>

                                <div class="collapse" style="padding-top: 5px;" id="collapseExample<?php echo $arreglo[0]; ?>">

                                    <div class="container">



                                        <center>
                                            <div style="margin: 5px;">
                                                <form action="../models/reception.cart2.php" method="POST" id="ser<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" value="<?php echo $arreglo[5]; ?>" name="session">
                                                    <button type="submit" id="btnServ<?php echo $arreglo[0]; ?>" class="btn btn-warning " data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-briefcase-medical"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#ser<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnServ<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>


                                    </div>



                                    <br>
                                </div>
                            </td>
                            <td class="">
                                <center>
                                    <h5><?php echo  $compare; ?></h5>



                                </center>
                                <br>
                                <br>
                                <br>

                                <div class="collapse" id="collapseExample<?php echo $arreglo[0]; ?>">

                                    <div class="container">



                                        <center>
                                            <div style="margin: 5px;">
                                                <form action="../models/programacionTraerStatus.php" method="POST" id="status<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="sumbit" id="btnEnviarStatus<?php echo $arreglo[0]; ?>" class="btn btn-primary " data-toggle="modal" data-target="#docs"><i class="fas fa-toggle-off"></i></button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#status<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnEnviarStatus<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>


                                    </div>



                                    <br>
                                </div>
                            </td>
                            <td>


                                <button class="btn btn-dark" type="button" data-toggle="collapse" data-target="#collapseExample<?php echo $arreglo[0]; ?>" aria-expanded="false" aria-controls="collapseExample">
                                    <i class="fas fa-bars"></i>
                                </button>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>

                                <div class="collapse" id="collapseExample<?php echo $arreglo[0]; ?>">
                                    <br>
                                    <div class="container">




                                        <center>
                                            <div style="margin: 5px;">
                                                <form action="../models/programacionVerBitacora.php" method="POST" id="Bitacora<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="sumbit" id="btnEnviarBitacora<?php echo $arreglo[0]; ?>" class="btn btn-info " data-toggle="modal" data-target="#docs"><i class="fas fa-book-medical"></i></button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#Bitacora<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnEnviarBitacora<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>

                                    </div>



                                    <br>
                                </div>



                                <!-- onfiguracion de la botonera -->

                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            /* Funcion Ajax  */
            $(document).ready(function() {
                $('#tablesuperv').DataTable({
                    "order": [
                        [1, "desc"]
                    ]
                });
            });
        </script>
    <?php
    }
    public function sasigsuperall()
    {

        $time = new requests;
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM asignacion C JOIN alta_de_eventos D ON C.session = D.session JOIN users E ON C.doctor = E.id WHERE doctor != 'null' AND tiposerv= 'Supervisión Hospitalaria'  GROUP BY C.id ORDER BY C.id DESC";
        $sql = mysqli_query($newCon, $query);
    ?>
        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
        <div class="table-responsive">
            <table class="table  table-striped  table-sm" id="tablesuperv">
                <thead class="thead-dark">
                    <tr>
                        <th></th>
                        <th>No.</th>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th>Tiempo</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($arreglo = mysqli_fetch_array($sql)) {
                    ?>
                        <tr>
                            <!-- Colores dependiendo el status del evento -->
                            <?php
                            if ($arreglo[2] === "Cancelada") {
                                $color = "text-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color = "text-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color = "text-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color = "text-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color = "text-info";
                            }

                            if ($arreglo[2] === "Cancelada") {
                                $color3 = "-primary";
                            } elseif ($arreglo[2] === "En proceso") {
                                $color3 = "-warning";
                            } elseif ($arreglo[2] === "Finalizada") {
                                $color3 = "-success";
                            } elseif ($arreglo[2] === "ABANDONADA") {
                                $color3 = "-dark";
                            } elseif ($arreglo[2] === "Pendiente") {
                                $color3 = "-info";
                            }


                            /* Funciones de comparacion de fechas  */
                            $fecha = strftime("%Y-%m-%d %R");
                            $onlyconsonants = str_replace("T", " ", $arreglo[12]);
                            $d1 = new DateTime($fecha);
                            $d2 = new DateTime($onlyconsonants);
                            if ($d1 > $d2) {
                                $compare = "La fecha ya ha pasado <br><h5><i style='color: #ff0833;' class='fas fa-stopwatch'></i> </h5>";
                                $color2 = "bg-primary";
                            } else {
                                $compare = "A tiempo <br><br><h5><i style='color: #029f00;' class='far fa-clock'></i></h5>";
                                $color2 = "bg-success";
                            }
                            ?>
                            <td>
                                <center>

                                    <i class="fas fa-circle <?php echo $color; ?>"></i>
                                </center>
                            </td>
                            <td class="">
                                <center>
                                    <h5><?php echo $arreglo[0]; ?></h5>

                                </center>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <h4><?php echo $arreglo[1]; ?></h4>
                                <br>
                                <p style="color: black;"><strong><i class="fas fa-user-md"></i> </strong><?php echo $arreglo[24]; ?></p>
                                <br>
                                <div class="collapse" id="collapseExample<?php echo $arreglo[0]; ?>">

                                    <div class="container">

                                        <div class="container">
                                            <div class="row">


                                                <div style="margin: 5px;">
                                                    <form id="asignardoc<?php echo $arreglo[0]; ?>" action="../models/programacionTraerDocs.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[0]; ?>">
                                                        <button type="submit" id="btnEnviarAsignDoc<?php echo $arreglo[0]; ?>" class="btn btn-danger" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-user-md"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <div style="margin: 5px;">
                                                    <form id="edit<?php echo $arreglo[0]; ?>" action="../models/programacionedit.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnedit<?php echo $arreglo[0]; ?>" class="btn btn-info" data-toggle="modal" data-target="#docs">
                                                            <i class="far fa-edit"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <div style="margin: 5px;">
                                                    <form id="trash<?php echo $arreglo[0]; ?>" action="../models/programacionTrash.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnTrash<?php echo $arreglo[0]; ?>" class="btn btn-primary">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    <div class="trashRespose"></div>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#trash<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnTrash<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".trashRespose").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                <div style="margin: 5px;">
                                                    <form id="search<?php echo $arreglo[0]; ?>" action="../models/doctorVerDetalles.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnSearch<?php echo $arreglo[0]; ?>" class="btn btn-dark" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#search<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnSearch<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>

                                                <div style="margin: 5px;">
                                                    <form id="note<?php echo $arreglo[0]; ?>" action="../models/programacionNota.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnEnviarNote<?php echo $arreglo[0]; ?>" class="btn btn-success" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-notes-medical"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#note<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnEnviarNote<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                <div style="margin: 5px;">
                                                    <form id="arch<?php echo $arreglo[0]; ?>" action="../models/doctorRecetaEdit.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnEnviarArch<?php echo $arreglo[0]; ?>" class="btn btn-warning" data-toggle="modal" data-target="#docs">
                                                            <i class="far fa-id-badge"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#arch<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnEnviarArch<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                <div style="margin: 5px;">
                                                    <form id="checksuper<?php echo $arreglo[0]; ?>" action="../models/addcheksuper.php" method="post">
                                                        <input style="display: none;" name="ids" type="text" value="<?php echo $arreglo[5]; ?>">
                                                        <button type="submit" id="btnEnviarchecksuper<?php echo $arreglo[0]; ?>" class="btn btn-success" data-toggle="modal" data-target="#docs">
                                                            <i class="fas fa-money-check-alt"></i>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#checksuper<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnEnviarchecksuper<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocs").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>

                                            </div>

                                        </div>

                                    </div>
                                    <script>
                                        /* Funcion Ajax  */
                                        $(document).ready(function() {
                                            $("#asignardoc<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                var btnEnviar = $("#btnEnviarAsignDoc<?php echo $arreglo[0]; ?>");
                                                $.ajax({
                                                    type: $(this).attr("method"),
                                                    url: $(this).attr("action"),
                                                    data: $(this).serialize(),
                                                    beforeSend: function() {
                                                        btnEnviar.val("Enviando");
                                                        btnEnviar.attr("disabled", "disabled");
                                                    },
                                                    complete: function(data) {
                                                        btnEnviar.val("Iniciar");
                                                        btnEnviar.removeAttr("disabled");
                                                    },
                                                    success: function(data) {
                                                        $(".respuestaDocs").html(data);
                                                    },
                                                    error: function(data) {
                                                        alert("Problemas al tratar de enviar el formulario");
                                                    },
                                                });
                                                return false;
                                            });
                                        });
                                    </script>
                                    <script>
                                        /* Funcion Ajax  */
                                        $(document).ready(function() {
                                            $("#edit<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                var btnEnviar = $("#btnedit<?php echo $arreglo[0]; ?>");
                                                $.ajax({
                                                    type: $(this).attr("method"),
                                                    url: $(this).attr("action"),
                                                    data: $(this).serialize(),
                                                    beforeSend: function() {
                                                        btnEnviar.val("Enviando");
                                                        btnEnviar.attr("disabled", "disabled");
                                                    },
                                                    complete: function(data) {
                                                        btnEnviar.val("Iniciar");
                                                        btnEnviar.removeAttr("disabled");
                                                    },
                                                    success: function(data) {
                                                        $(".respuestaDocs").html(data);
                                                    },
                                                    error: function(data) {
                                                        alert("Problemas al tratar de enviar el formulario");
                                                    },
                                                });
                                                return false;
                                            });
                                        });
                                    </script>


                                    <br>
                                </div>
                            </td>
                            <td class="<?php echo $color3; ?>">
                                <center>
                                    <h5 class="text-dark"><?php echo $arreglo[2]; ?></h5>
                                    <br>
                                    <p style="color: black;"><i class="far fa-clock"></i> <?php echo $onlyconsonants; ?></p>
                                </center>
                                <br>
                                <br>

                                <div class="collapse" style="padding-top: 5px;" id="collapseExample<?php echo $arreglo[0]; ?>">

                                    <div class="container">



                                        <center>
                                            <div style="margin: 5px;">
                                                <form action="../models/reception.cart2.php" method="POST" id="ser<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" value="<?php echo $arreglo[5]; ?>" name="session">
                                                    <button type="submit" id="btnServ<?php echo $arreglo[0]; ?>" class="btn btn-warning " data-toggle="modal" data-target="#docs">
                                                        <i class="fas fa-briefcase-medical"></i>
                                                    </button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#ser<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnServ<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>


                                    </div>



                                    <br>
                                </div>
                            </td>
                            <td class="">
                                <center>
                                    <h5><?php echo  $compare; ?></h5>



                                </center>
                                <br>
                                <br>
                                <br>

                                <div class="collapse" id="collapseExample<?php echo $arreglo[0]; ?>">

                                    <div class="container">



                                        <center>
                                            <div style="margin: 5px;">
                                                <form action="../models/programacionTraerStatus.php" method="POST" id="status<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="sumbit" id="btnEnviarStatus<?php echo $arreglo[0]; ?>" class="btn btn-primary " data-toggle="modal" data-target="#docs"><i class="fas fa-toggle-off"></i></button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#status<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnEnviarStatus<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>


                                    </div>



                                    <br>
                                </div>
                            </td>
                            <td>


                                <button class="btn btn-dark" type="button" data-toggle="collapse" data-target="#collapseExample<?php echo $arreglo[0]; ?>" aria-expanded="false" aria-controls="collapseExample">
                                    <i class="fas fa-bars"></i>
                                </button>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>
                                <br>

                                <div class="collapse" id="collapseExample<?php echo $arreglo[0]; ?>">
                                    <br>
                                    <div class="container">




                                        <center>
                                            <div style="margin: 5px;">
                                                <form action="../models/programacionVerBitacora.php" method="POST" id="Bitacora<?php echo $arreglo[0]; ?>">
                                                    <input style="display: none;" type="text" name="session" value="<?php echo $arreglo[5]; ?>">
                                                    <button type="sumbit" id="btnEnviarBitacora<?php echo $arreglo[0]; ?>" class="btn btn-info " data-toggle="modal" data-target="#docs"><i class="fas fa-book-medical"></i></button>
                                                </form>
                                                <script>
                                                    /* Funcion Ajax  */
                                                    $(document).ready(function() {
                                                        $("#Bitacora<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                            var btnEnviar = $("#btnEnviarBitacora<?php echo $arreglo[0]; ?>");
                                                            $.ajax({
                                                                type: $(this).attr("method"),
                                                                url: $(this).attr("action"),
                                                                data: $(this).serialize(),
                                                                beforeSend: function() {
                                                                    btnEnviar.val("Enviando");
                                                                    btnEnviar.attr("disabled", "disabled");
                                                                },
                                                                complete: function(data) {
                                                                    btnEnviar.val("Iniciar");
                                                                    btnEnviar.removeAttr("disabled");
                                                                },
                                                                success: function(data) {
                                                                    $(".respuestaDocs").html(data);
                                                                },
                                                                error: function(data) {
                                                                    alert("Problemas al tratar de enviar el formulario");
                                                                },
                                                            });
                                                            return false;
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </center>

                                    </div>



                                    <br>
                                </div>



                                <!-- onfiguracion de la botonera -->

                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            /* Funcion Ajax  */
            $(document).ready(function() {
                $('#tablesuperv').DataTable({
                    "order": [
                        [1, "desc"]
                    ]
                });
            });
        </script>
    <?php
    }
    public function addcheckcount($id)
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM `superhosp` WHERE session = '$id'";
        $querySumImport = "SELECT SUM(importe) AS value_sum FROM superhosp WHERE session = '$id'";
        $querySumDes = "SELECT SUM(descuento) AS value_sum FROM superhosp WHERE session = '$id'";
        $queryhosp = "SELECT * FROM `hospital`";

        $sql = mysqli_query($newCon, $query);
        $sqlSumImport = mysqli_query($newCon, $querySumImport);
        $sqlSumDes = mysqli_query($newCon, $querySumDes);
        $rowSumImport = mysqli_fetch_array($sqlSumImport);
        $rowSumSDes = mysqli_fetch_array($sqlSumDes);
        $sqlhosp =  mysqli_query($newCon, $queryhosp);
        $sum = $rowSumImport['value_sum'];
        $sumDes = $rowSumSDes['value_sum'];
        $subtotal = ($sum - $sumDes);
        $iva = ($subtotal * 0.16);
        $total = ($subtotal + $iva);

    ?>
        <div class="jumbotron jumbotron-fluid">
            <div class="container">
                <h3 class="display-3">Estado de cuenta</h3>

                <hr class="my-2">

                <form enctype="multipart/form-data" target="_blank" action="../models/addcheksuperproccess.php" method="post">
                    CSV File: <br> <input required type="file" class="file" name="file" id="file">
                    <br>
                    <br>

                    <div class="form-group">
                        <label for="">Hospital</label>
                        <select class="form-control" name="hosp" id="">
                            <option selected>Selecciona el hospital</option>
                            <?php while ($arreglo = mysqli_fetch_array($sqlhosp)) { ?>
                                <option><?php echo $arreglo[1]; ?></option>
                            <?php } ?>
                        </select>
                        <small>Selecciona el hospital de la cuenta.</small>
                    </div>

                    <input type="text" style="display: none;" id="ids" value="<?php echo $id; ?>" name="ids">
                    <div class="row">
                        <button style="margin-left: 10px;" type="submit" class="btn btn-primary" name="enviar">Guardar <i class="far fa-save"></i></button>
                </form>
                <form id="checksuperrefresh<?php echo $id; ?>" action="../models/addcheksuper.php" method="post">
                    <input type="text" style="display: none;" name="ids" value="<?php echo $id; ?>">
                    <button style="margin-left: 10px;" type="submit" class="btn btn-success" id="btnEnviarchecksuperrefresh<?php echo $id; ?>">Refresh <i class="fas fa-sync"></i></button>
                </form>
                <script>
                    /* Funcion Ajax  */
                    $(document).ready(function() {
                        $("#checksuperrefresh<?php echo $id; ?>").bind("submit", function() {
                            var btnEnviar = $("#btnEnviarchecksuperrefresh<?php echo $id; ?>");
                            $.ajax({
                                type: $(this).attr("method"),
                                url: $(this).attr("action"),
                                data: $(this).serialize(),
                                beforeSend: function() {
                                    btnEnviar.val("Enviando");
                                    btnEnviar.attr("disabled", "disabled");
                                },
                                complete: function(data) {
                                    btnEnviar.val("Iniciar");
                                    btnEnviar.removeAttr("disabled");
                                },
                                success: function(data) {
                                    $(".respuestaDocs").html(data);
                                },
                                error: function(data) {
                                    alert("Problemas al tratar de enviar el formulario");
                                },
                            });
                            return false;
                        });
                    });
                </script>

                <form id="checksuperredelete<?php echo $id; ?>" action="../models/cheksuperdelete.php" method="post">
                    <input type="text" style="display: none;" name="ids" value="<?php echo $id; ?>">
                    <button style="margin-left: 10px;" type="submit" class="btn btn-danger" id="btnEnviarchecksuperdelete<?php echo $id; ?>">Eliminar <i class="fas fa-trash-alt"></i></button>
                </form>
                <script>
                    /* Funcion Ajax  */
                    $(document).ready(function() {
                        $("#checksuperredelete<?php echo $id; ?>").bind("submit", function() {
                            var btnEnviar = $("#btnEnviarchecksuperdelete<?php echo $id; ?>");
                            $.ajax({
                                type: $(this).attr("method"),
                                url: $(this).attr("action"),
                                data: $(this).serialize(),
                                beforeSend: function() {
                                    btnEnviar.val("Enviando");
                                    btnEnviar.attr("disabled", "disabled");
                                },
                                complete: function(data) {
                                    btnEnviar.val("Iniciar");
                                    btnEnviar.removeAttr("disabled");
                                },
                                success: function(data) {
                                    $(".respuestaDocs").html(data);
                                },
                                error: function(data) {
                                    alert("Problemas al tratar de enviar el formulario");
                                },
                            });
                            return false;
                        });
                    });
                </script>
                <form id="checksupeaddhosp<?php echo $id; ?>" action="../models/addhosp.php" method="post">
                    <input type="text" style="display: none;" name="ids" value="<?php echo $id; ?>">
                    <button style="margin-left: 10px;" type="submit" class="btn btn-warning" id="btnEnviarchecksuperaddhosp<?php echo $id; ?>">Agregar hospital <i class="far fa-hospital"></i></button>
                </form>
                <script>
                    /* Funcion Ajax  */
                    $(document).ready(function() {
                        $("#checksupeaddhosp<?php echo $id; ?>").bind("submit", function() {
                            var btnEnviar = $("#btnEnviarchecksuperaddhosp<?php echo $id; ?>");
                            $.ajax({
                                type: $(this).attr("method"),
                                url: $(this).attr("action"),
                                data: $(this).serialize(),
                                beforeSend: function() {
                                    btnEnviar.val("Enviando");
                                    btnEnviar.attr("disabled", "disabled");
                                },
                                complete: function(data) {
                                    btnEnviar.val("Iniciar");
                                    btnEnviar.removeAttr("disabled");
                                },
                                success: function(data) {
                                    $(".respuestaDocs").html(data);
                                },
                                error: function(data) {
                                    alert("Problemas al tratar de enviar el formulario");
                                },
                            });
                            return false;
                        });
                    });
                </script>
            </div>

        </div>
        <hr>
        <div class="table-responsive">
            <table class="table  table-striped  table-sm" id="tablecheck">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Unidad</th>
                        <th>Prestacíon</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Importe</th>
                        <th>Descuento</th>
                        <th>Tipo</th>
                        <th>Hospital</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($arreglo = mysqli_fetch_array($sql)) {  ?>
                        <tr>
                            <td scope="row"><?php echo $arreglo[1]; ?></td>
                            <td><?php echo $arreglo[2]; ?></td>
                            <td><?php echo $arreglo[3]; ?></td>
                            <td><?php echo $arreglo[4]; ?></td>
                            <td><?php echo $arreglo[5]; ?></td>
                            <td>$ <?php echo $arreglo[6]; ?></td>
                            <td>$ <?php echo $arreglo[7]; ?></td>
                            <td>$ <?php echo $arreglo[8]; ?></td>
                            <td><?php echo $arreglo[9]; ?></td>
                            <td><?php echo $arreglo[11]; ?></td>

                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <br>
        <hr>
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">

                    <h3 class="text-primary">
                        Total Importe: <br>
                        <h3 class="text-primary">$ <?php echo round($sum, 2); ?></h3>
                    </h3>

                    <h3 class="text-success">
                        Total Descuento:<br>
                        <h3 class="text-primary">$ <?php echo round($sumDes, 2); ?></h3>
                    </h3>
                    <h3 class="text-danger">
                        IVA:<br>
                        <h3 class="text-primary">$ <?php echo round($iva, 2); ?></h3>
                    </h3>
                    <h3 class="text-warning">
                        Subtotal:<br>
                        <h3 class="text-primary">$ <?php echo round($subtotal, 2); ?></h3>
                    </h3>
                    <h3 class="text-info">
                        Total con IVA:<br>
                        <h3 class="text-primary">$ <?php echo round($total, 2); ?></h3>
                    </h3>


                </div>
                <div class="col-md-6 col-12">

                </div>




            </div>



        </div>

        <script src="https://cdn.datatables.net/plug-ins/1.10.20/api/sum().js"></script>
        <script>
            $(document).ready(function() {
                var tabla = $("#tablecheck").DataTable();
            });
        </script>
        </div>


    <?php

    }
    public function addcheckcountview($id)
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT * FROM `superhosp` WHERE session = '$id'";
        $querySumImport = "SELECT SUM(importe) AS value_sum FROM superhosp WHERE session = '$id'";
        $querySumDes = "SELECT SUM(descuento) AS value_sum FROM superhosp WHERE session = '$id'";
        $queryhosp = "SELECT * FROM `hospital`";

        $sql = mysqli_query($newCon, $query);
        $sqlSumImport = mysqli_query($newCon, $querySumImport);
        $sqlSumDes = mysqli_query($newCon, $querySumDes);
        $rowSumImport = mysqli_fetch_array($sqlSumImport);
        $rowSumSDes = mysqli_fetch_array($sqlSumDes);
        $sqlhosp =  mysqli_query($newCon, $queryhosp);
        $sum = $rowSumImport['value_sum'];
        $sumDes = $rowSumSDes['value_sum'];
        $subtotal = ($sum - $sumDes);
        $iva = ($subtotal * 0.16);
        $total = ($subtotal + $iva);

    ?>
        <div class="jumbotron jumbotron-fluid">
            <div class="container">
                <h3 class="display-3">Estado de cuenta</h3>

                <hr class="my-2">






            </div>

        </div>
        <hr>
        <div class="table-responsive">
            <table class="table  table-striped  table-sm" id="tablecheck">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Unidad</th>
                        <th>Prestacíon</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Importe</th>
                        <th>Descuento</th>
                        <th>Tipo</th>
                        <th>Hospital</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($arreglo = mysqli_fetch_array($sql)) {  ?>
                        <tr>
                            <td scope="row"><?php echo $arreglo[1]; ?></td>
                            <td><?php echo $arreglo[2]; ?></td>
                            <td><?php echo $arreglo[3]; ?></td>
                            <td><?php echo $arreglo[4]; ?></td>
                            <td><?php echo $arreglo[5]; ?></td>
                            <td>$ <?php echo $arreglo[6]; ?></td>
                            <td>$ <?php echo $arreglo[7]; ?></td>
                            <td>$ <?php echo $arreglo[8]; ?></td>
                            <td><?php echo $arreglo[9]; ?></td>
                            <td><?php echo $arreglo[11]; ?></td>

                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <br>
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12">

                    <h3 class="text-primary">
                        Total Importe: <br>
                        <h3 class="text-primary">$ <?php echo round($sum, 2); ?></h3>
                    </h3>

                    <h3 class="text-success">
                        Total Descuento:<br>
                        <h3 class="text-primary">$ <?php echo round($sumDes, 2); ?></h3>
                    </h3>
                    <h3 class="text-danger">
                        IVA:<br>
                        <h3 class="text-primary">$ <?php echo round($iva, 2); ?></h3>
                    </h3>
                    <h3 class="text-warning">
                        Subtotal:<br>
                        <h3 class="text-primary">$ <?php echo round($subtotal, 2); ?></h3>
                    </h3>
                    <h3 class="text-info">
                        Total con IVA:<br>
                        <h3 class="text-primary">$ <?php echo round($total, 2); ?></h3>
                    </h3>


                </div>
                <div class="col-md-6 col-12">

                </div>




            </div>

            <script src="https://cdn.datatables.net/plug-ins/1.10.20/api/sum().js"></script>
            <script>
                $(document).ready(function() {
                    var tabla = $("#tablecheck").DataTable();
                });
            </script>
        </div>


    <?php

    }
    public function deletecounthosp($id)
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "DELETE FROM `superhosp` WHERE `superhosp`.`session` = '$id'";
        $sqlSumDes = mysqli_query($newCon, $query);
        $object = new requests;
        $object->addcheckcount($id);
    }
    public function addhosp($id)
    {
    ?>

        <div class="jumbotron jumbotron-fluid">
            <div class="container">
                <h3>Registrar hospital</h3>

                <hr class="my-2">
                <form id="formaddhosp" action="../models/processaddhosp.php" method="post">
                    <input type="text" name="ids" value="<?php echo $id; ?>" style="display: none;">
                    <div class="form-group">
                        <label for="">Nombre</label>
                        <input type="text" required class="form-control" name="name" id="" aria-describedby="helpId" placeholder="Nombre del hospital">

                    </div>
                    <div class="form-group">
                        <label for="">Dirección</label>
                        <input type="text" required class="form-control" name="adress" id="" aria-describedby="helpId" placeholder="Dirección del hospital">

                    </div>
                    <div class="form-group">
                        <label for="">Teléfono</label>
                        <input type="number" required class="form-control" name="phone" id="" aria-describedby="helpId" placeholder="Teléfono del hospital">

                    </div>
                    <center><button id="btnformaddhosp" type="submit" class="btn btn-success">Registrar <i class="far fa-save"></i></button></center>
                </form>
                <script>
                    /* Funcion Ajax  */
                    $(document).ready(function() {
                        $("#formaddhosp").bind("submit", function() {
                            var btnEnviar = $("#btnformaddhosp");
                            $.ajax({
                                type: $(this).attr("method"),
                                url: $(this).attr("action"),
                                data: $(this).serialize(),
                                beforeSend: function() {
                                    btnEnviar.val("Enviando");
                                    btnEnviar.attr("disabled", "disabled");
                                },
                                complete: function(data) {
                                    btnEnviar.val("Iniciar");
                                    btnEnviar.removeAttr("disabled");
                                },
                                success: function(data) {
                                    $(".respuestaDocs").html(data);
                                },
                                error: function(data) {
                                    alert("Problemas al tratar de enviar el formulario");
                                },
                            });
                            return false;
                        });
                    });
                </script>
                <hr>
                <form id="returnsuper" action="../models/addcheksuper.php" method="POST">
                    <input type="text" name="ids" value="<?php echo $id; ?>" style="display: none;">
                    <button type="submit" class="btn btn-warning" id="btnReturnSuper">Regresar <i class="fas fa-undo-alt"></i></button>
                </form>
                <script>
                    /* Funcion Ajax  */
                    $(document).ready(function() {
                        $("#returnsuper").bind("submit", function() {
                            var btnEnviar = $("#btnReturnSuper");
                            $.ajax({
                                type: $(this).attr("method"),
                                url: $(this).attr("action"),
                                data: $(this).serialize(),
                                beforeSend: function() {
                                    btnEnviar.val("Enviando");
                                    btnEnviar.attr("disabled", "disabled");
                                },
                                complete: function(data) {
                                    btnEnviar.val("Iniciar");
                                    btnEnviar.removeAttr("disabled");
                                },
                                success: function(data) {
                                    $(".respuestaDocs").html(data);
                                },
                                error: function(data) {
                                    alert("Problemas al tratar de enviar el formulario");
                                },
                            });
                            return false;
                        });
                    });
                </script>

            </div>
        </div>

    <?php

    }
    public function processaddhosp($id, $name, $adress, $phone)
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "INSERT INTO `hospital` (`id`, `hosp`, `dire`, `tel`) VALUES (NULL, '$name', '$adress', '$phone');";
        $sqlSumDes = mysqli_query($newCon, $query);
    ?>
        <div class="alert alert-primary" role="alert">
            Registro exitoso
        </div>
        <br>
        <form id="returnsuper" action="../models/addcheksuper.php" method="POST">
            <input type="text" name="ids" value="<?php echo $id; ?>" style="display: none;">
            <button type="submit" class="btn btn-warning" id="btnReturnSuper">Regresar <i class="fas fa-undo-alt"></i></button>
        </form>
        <script>
            /* Funcion Ajax  */
            $(document).ready(function() {
                $("#returnsuper").bind("submit", function() {
                    var btnEnviar = $("#btnReturnSuper");
                    $.ajax({
                        type: $(this).attr("method"),
                        url: $(this).attr("action"),
                        data: $(this).serialize(),
                        beforeSend: function() {
                            btnEnviar.val("Enviando");
                            btnEnviar.attr("disabled", "disabled");
                        },
                        complete: function(data) {
                            btnEnviar.val("Iniciar");
                            btnEnviar.removeAttr("disabled");
                        },
                        success: function(data) {
                            $(".respuestaDocs").html(data);
                        },
                        error: function(data) {
                            alert("Problemas al tratar de enviar el formulario");
                        },
                    });
                    return false;
                });
            });
        </script>
    <?php
    }
    public function showprices()
    {

        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT *, SUM(A.precio) AS Gtb FROM superhosp A JOIN alta_de_eventos B ON A.session = B.session GROUP BY B.session;";
        $query2 = "SELECT * FROM superhosp A JOIN alta_de_eventos B ON A.session = B.session GROUP BY A.descrip";
        $sql = mysqli_query($newCon, $query);
        $sql2 = mysqli_query($newCon, $query2);
    ?>


        <div class="table-responsive">
            <table style="border-radius: 15px;" class="table table-striped table-light table-bordered  table-hover table-sm " id="tablecheck">
                <thead>
                    <tr>
                        <th style="display: none;">Diagnostico</th>
                        <th style="display: none;">Precio</th>
                        <th style="border-radius: 15px;" class="table-primary">Busqueda de precios por diagnostico</th>


                    </tr>
                </thead>
                <tbody>
                    <?php while ($arreglo = mysqli_fetch_array($sql)) { ?>
                        <tr>

                            <td style="display: none;"><?php
                                                        $parts = explode("/", $arreglo['motivos']);
                                                        echo $arreglo[11] . "-" . $parts[2]; ?>
                            </td>
                            <td style="display: none;"><?php echo round($arreglo['Gtb'], 2); ?></td>
                            <td>
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <h5>Diagnostico</h5>

                                            <i class="fas fa-notes-medical"></i> <?php
                                                                                    $parts = explode("/", $arreglo['motivos']);

                                                                                    echo $parts[2]; ?>
                                            <br>
                                            <h5 class="text-info">Hospital</h5>
                                            <h6><i class="far fa-hospital"></i> <?php echo $arreglo[11]; ?></h6>


                                            <br>



                                        </div>
                                        <div class="col-md-6 col-12">
                                            <h5>Precio total</h5>
                                            <h6 class="text-secondary">$ <?php echo round($arreglo['Gtb'], 2); ?></h6>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <form id="arch<?php echo $arreglo[0]; ?>" action="../models/doctorRecetaview.php" method="post">
                                                        <input style="display: none;" name="id" type="text" value="<?php echo $arreglo[10]; ?>">
                                                        <button type="submit" id="btnEnviarArch<?php echo $arreglo[0]; ?>" class="btn btn-info" style="margin-bottom: 10px;" data-toggle="modal" data-target="#form">
                                                            Ver expediente <i class="far fa-eye"></i></span>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#arch<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnEnviarArch<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocss").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <form id="archt<?php echo $arreglo[0]; ?>" action="../models/addcheksuperview.php" method="post">
                                                        <input style="display: none;" name="ids" type="text" value="<?php echo $arreglo[10]; ?>">
                                                        <button type="submit" id="btnEnviarArcht<?php echo $arreglo[0]; ?>" class="btn btn-warning" style="margin-bottom: 10px;" data-toggle="modal" data-target="#form">
                                                            Ver cuenta <i class="fas fa-coins"></i></span>
                                                        </button>
                                                    </form>
                                                    <script>
                                                        /* Funcion Ajax  */
                                                        $(document).ready(function() {
                                                            $("#archt<?php echo $arreglo[0]; ?>").bind("submit", function() {
                                                                var btnEnviar = $("#btnEnviarArcht<?php echo $arreglo[0]; ?>");
                                                                $.ajax({
                                                                    type: $(this).attr("method"),
                                                                    url: $(this).attr("action"),
                                                                    data: $(this).serialize(),
                                                                    beforeSend: function() {
                                                                        btnEnviar.val("Enviando");
                                                                        btnEnviar.attr("disabled", "disabled");
                                                                    },
                                                                    complete: function(data) {
                                                                        btnEnviar.val("Iniciar");
                                                                        btnEnviar.removeAttr("disabled");
                                                                    },
                                                                    success: function(data) {
                                                                        $(".respuestaDocss").html(data);
                                                                    },
                                                                    error: function(data) {
                                                                        alert("Problemas al tratar de enviar el formulario");
                                                                    },
                                                                });
                                                                return false;
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <hr>
            <br>
            <div id="chart2"></div>
            <script>
                let draw = false;

                init();

                /**
                 * FUNCTIONS
                 */

                function init() {
                    // initialize DataTables
                    const table = $("#tablecheck").DataTable();
                    // get table data
                    const tableData = getTableData(table);
                    // create Highcharts
                    createHighcharts(tableData);
                    // table events
                    setTableEvents(table);
                }

                function getTableData(table) {
                    const dataArray = [],
                        countryArray = [],
                        populationArray = [];


                    // loop table rows
                    table.rows({
                        search: "applied"
                    }).every(function() {
                        const data = this.data();
                        countryArray.push(data[0]);
                        populationArray.push(parseInt(data[1].replace(/\,/g, "")));

                    });

                    // store all data in dataArray
                    dataArray.push(countryArray, populationArray);

                    return dataArray;
                }

                function createHighcharts(data) {
                    Highcharts.setOptions({
                        lang: {
                            thousandsSep: ","
                        }
                    });

                    Highcharts.chart("chart2", {
                        title: {
                            text: "Graficas de precios"
                        },
                        subtitle: {
                            text: "Compara los diferentes precios según el hospital"
                        },
                        xAxis: [{
                            categories: data[0],
                            labels: {
                                rotation: -45
                            }
                        }],
                        yAxis: [{
                            // first yaxis
                            title: {
                                text: "Precio ($)"
                            }
                        }],
                        series: [{
                                name: "Precio ($)",
                                color: "#0071A7",
                                type: "column",
                                data: data[1],
                                tooltip: {
                                    valueSuffix: ""
                                }
                            }

                        ],
                        tooltip: {
                            shared: true
                        },
                        legend: {
                            backgroundColor: "#ececec",
                            shadow: true
                        },
                        credits: {
                            enabled: false
                        },
                        noData: {
                            style: {
                                fontSize: "16px"
                            }
                        }
                    });
                }

                function setTableEvents(table) {
                    // listen for page clicks
                    table.on("page", () => {
                        draw = true;
                    });

                    // listen for updates and adjust the chart accordingly
                    table.on("draw", () => {
                        if (draw) {
                            draw = false;
                        } else {
                            const tableData = getTableData(table);
                            createHighcharts(tableData);
                        }
                    });
                }
            </script>
        </div>





    <?php
    }
    public function showpricestwo()
    {

        $db = new con;
        $newCon = $db->sql();
        $query = "SELECT *, SUM(A.precio) AS Gtb FROM superhosp A JOIN alta_de_eventos B ON A.session = B.session GROUP BY B.session;";
        $query2 = "SELECT * FROM superhosp A JOIN alta_de_eventos B ON A.session = B.session GROUP BY A.descrip";
        $sql = mysqli_query($newCon, $query);
        $sql2 = mysqli_query($newCon, $query2);
    ?>



        <div>
            <div class="container">
                <div class="table-responsive">
                    <table id="dt-table" style="border-radius: 15px;" class="table table-striped table-bordered table-light  table-hover table-sm tablecheck">
                        <thead>

                            <tr>
                                <th>Concepto</th>
                                <th>Hospital</th>
                                <th>Precio neto($)</th>
                                <th>Descuento($)</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($arreglo = mysqli_fetch_array($sql2)) { ?>
                                <tr>
                                    <td><?php echo $arreglo['descrip']; ?></td>
                                    <td class="text-info"><?php echo $arreglo['hosp']; ?></td>
                                    <td class="text-warning"><?php echo $arreglo['precio']; ?></td>
                                    <td class="text-danger"><?php echo $arreglo['descuento']; ?></td>

                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <hr>
                <div id="chart"></div>
                <script>
                    let draw2 = false;

                    init2();

                    /**
                     * FUNCTIONS
                     */

                    function init2() {
                        // initialize DataTables
                        const table = $("#dt-table").DataTable();
                        // get table data
                        const tableData = getTableData(table);
                        // create Highcharts
                        createHighcharts(tableData);
                        // table events
                        setTableEvents(table);
                    }

                    function getTableData(table) {
                        const dataArray = [],
                            countryArray = [],
                            populationArray = [],
                            densityArray = [];

                        // loop table rows
                        table.rows({
                            search: "applied"
                        }).every(function() {
                            const data = this.data();
                            countryArray.push(data[0]);
                            populationArray.push(parseInt(data[2].replace(/\,/g, "")));
                            densityArray.push(parseInt(data[3].replace(/\,/g, "")));
                        });

                        // store all data in dataArray
                        dataArray.push(countryArray, populationArray, densityArray);

                        return dataArray;
                    }

                    function createHighcharts(data) {
                        Highcharts.setOptions({
                            lang: {
                                thousandsSep: ","
                            }
                        });

                        Highcharts.chart("chart", {
                            title: {
                                text: "Graficas de precios"
                            },
                            subtitle: {
                                text: "Compara los diferentes precios según el hospital"
                            },
                            xAxis: [{
                                categories: data[0],
                                labels: {
                                    rotation: -45
                                }
                            }],
                            yAxis: [{
                                    // first yaxis
                                    title: {
                                        text: "Precio ($)"
                                    }
                                },
                                {
                                    // secondary yaxis
                                    title: {
                                        text: "Descuento ($)"
                                    },
                                    min: 0,
                                    opposite: true
                                }
                            ],
                            series: [{
                                    name: "Precio ($)",
                                    color: "#0071A7",
                                    type: "column",
                                    data: data[1],
                                    tooltip: {
                                        valueSuffix: " M"
                                    }
                                },
                                {
                                    name: "Descuento ($)",
                                    color: "#FF404E",
                                    type: "spline",
                                    data: data[2],
                                    yAxis: 1
                                }
                            ],
                            tooltip: {
                                shared: true
                            },
                            legend: {
                                backgroundColor: "#ececec",
                                shadow: true
                            },
                            credits: {
                                enabled: false
                            },
                            noData: {
                                style: {
                                    fontSize: "16px"
                                }
                            }
                        });
                    }

                    function setTableEvents(table) {
                        // listen for page clicks
                        table.on("page", () => {
                            draw2 = true;
                        });

                        // listen for updates and adjust the chart accordingly
                        table.on("draw", () => {
                            if (draw2) {
                                draw2 = false;
                            } else {
                                const tableData = getTableData(table);
                                createHighcharts(tableData);
                            }
                        });
                    }
                </script>
            </div>
        </div>



    <?php
    }
    public function clienteInstitucionalespecial($id)

    {
        require('functions.request/clienteInstitucionalespecial.php');
    }
    public function cancelarSolicitud($id, $motivos)
    {
        $db = new con;
        $newCon = $db->sql();
        $query = "UPDATE `asignacion` SET `status` = '$motivos', `doctor` = 'cancel' WHERE `asignacion`.`session` = '$id';";
        $queryTwo = "UPDATE `alta_de_eventos` SET `obs` = '$motivos' WHERE `alta_de_eventos`.`session` = '$id';";
        $sql = mysqli_query($newCon, $query);
        $sql2 = mysqli_query($newCon, $queryTwo);
    ?>
        <div class="alert alert-primary" role="alert">
            Solicitud cancelada
        </div>
<?php
    }
};
?>