<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<?php
class request
{
    private $conn;
    public function __construct()
    {
        require_once('validate.php');
        $object = new validate;
        $newCon = $object->bd();
        $this->conn = $newCon;
    }
    public function encriptador($key)
    {
        $hash = password_hash($key, PASSWORD_DEFAULT);
        return $hash;
    }
    public function sqlEject($sqlT)
    {
        $newCon = $this->conn;
        return $sql = mysqli_query($newCon, $sqlT);
    }
    public function regNewUser($data = [])
    {

        extract($data);

        if ($passOne == $passTwo) {

            $requestClass = new request;
            $sql = "INSERT INTO `usuarios` (`codigo`, `nombre`, `apellidoP`, `apellidiM`, `correo`, `contrasena`, `direccion`, `telefono`, `usuario`) VALUES ('$nomina', 'Pedro', 'Eamos', 'Gonzales', '$email', '123456', 'penumbras 2 av. del negro', '55856598', '$user');";
            $requestClass->sqlEject($sql);
?>
            <div class="alert alert-primary" role="alert">
                <strong><?= $user ?></strong>
            </div>
        <?php
        }
    }
    public function newDoctorReg($data = [])
    {
        extract($data);

        $requestClass = new request;
        $sql = "SELECT * FROM `doctores` WHERE folio = '$folio'";
        $query = $requestClass->sqlEject($sql);
        if ($f = mysqli_fetch_assoc($query)) {
        ?>
            <div class="alert alert-danger" role="alert">
                <strong>Ya existe un registro con el mismo folio</strong>
            </div>
            <?php
        } else {

            if ($passOne == $passTwo) {
                $hash = $requestClass->encriptador($passOne);
                $sql = "INSERT INTO `usuarios` (`codigo`, `nombre`, `apellidoP`, `apellidiM`, `correo`, `contrasena`, `direccion`, `telefono`, `usuario`) VALUES ('$nomina', 'Pedro', 'Eamos', 'Gonzales', '$email', '123456', 'penumbras 2 av. del negro', '55856598', '$user');";
                $requestClass->sqlEject($sql);
            ?>
                <div class="alert alert-success" role="alert">
                    <strong>Registro exitoso</strong>
                </div>
        <?php
            }
        }
    }
    public function searchPax($id)
    {
        $sql = "SELECT * FROM `pax` WHERE nomina LIKE '%$id%';";
        $requestClass = new request;
        $query = $requestClass->sqlEject($sql);

        ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nomina</th>
                        <th>Nombre</th>
                        <th>Herramientas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($arreglo = mysqli_fetch_array($query)) {
                    ?>
                        <tr>
                            <td scope="row"><?= $arreglo[1] ?></td>
                            <td><?= $arreglo[3] ?> <?= $arreglo[4] ?> <?= $arreglo[5] ?></td>
                            <td>

                                <button type="button" style="border-radius: 12px;" class="btn btn-dark" onclick="openModalTools('<?= $arreglo[1] ?>')"><i class="fa-solid fa-bars"></i></button>
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
    public function buttonsPax($id)
    {
    ?>
        <style>
            .btnmargin {
                margin-top: 10px;
            }
        </style>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <center>
                        <h3>Nomina: <?= $id ?></h3>
                    </center>
                </div>
                <div class="col-12 col-md-4">
                    <center>
                        <div class="d-grid gap-2">

                            <button type="button" onclick="NewAutReg('<?= $id ?>')" class="btn btn-primary btn-block btnmargin"><i class="fab fa-sourcetree"></i> Nuevo numero de Aut</button>
                        </div>
                    </center>
                </div>

                <div class="col-12 col-md-4">
                    <center>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-info btn-block btnmargin"><i class="fas fa-user-tag"></i> Nuevo numero de Aut. Especial</button>
                        </div>
                    </center>
                </div>
                <div class="col-12 col-md-4">
                    <center>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-warning btn-block btnmargin"><i class="fas fa-history"></i> Historico</button>
                        </div>
                    </center>
                </div>
            </div>
        </div>
    <?php
    }
    public function newAutRegCon($id)
    {
    ?>


        <div class="jumbotron" style="margin-left: 30px; margin-right: 30px;">
            <center>
                <br>
                <h1 class="">Nuevo numero de Autorización</h1>

            </center>
            <hr class="my-4" />
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8 col-12">

                    <form class="row" method="post" action="controller/adminController/newAutRegProcess.php" id="formAddNoAuto">
                        <input type="hidden" value="<?=$id?>" name="nom">
                        <div class="col-md-6 col-12">
                            <label class="form-label font-weight-bold">N&uacute;mero Autorizaci&oacute;n</label>
                            <input type="text" class="form-control" name="numAut" placeholder="Digite el N&uacute;mero Autorizaci&oacute;n" required />
                        </div>
                        <div class="col-md-6 col-12">
                            <label class="form-label font-weight-bold">N&uacute;mero de Proveedor</label>
                            <input type="text" class="form-control" name="numProv" placeholder="Digite el N&uacute;mero de Proveedor" required />
                        </div>

                        <div class="col-12">
                            <br />
                        </div>

                        <div class="col-12">
                            <br />
                        </div>


                        <center>
                            <div class="col-md-12">
                                <br />
                                <button type="submit" class="btn btn-primary btn-lg">Enviar</button>
                            </div>
                            <br>
                        </center>
                    </form>
                    <script>
                        $(document).ready(function() {
                            $("#formAddNoAuto").bind("submit", function() {

                                $.ajax({
                                    type: $(this).attr("method"),
                                    url: $(this).attr("action"),
                                    data: $(this).serialize(),
                                    beforeSend: function() {

                                    },
                                    complete: function(data) {

                                    },
                                    success: function(data) {
                                        $(".buttonsPax").html(data);
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
                <div class="col-md-2"></div>
            </div>
        </div>
        </div>
        </div>
        <?php
    }
    public function addNewAutProcess($numAut,$numProv,$nom)
    {
        $requestClass = new request;
        $sql = "SELECT * FROM `autGeneral` WHERE id = '$numAut'";
        $query = $requestClass->sqlEject($sql);
        if ($f = mysqli_fetch_assoc($query)) {
        ?>
            <div class="alert alert-danger" role="alert">
                <strong>Ya existe un registro con el mismo número de Aut.</strong>
            </div>
        <?php
        } else {
            $sql = "INSERT INTO `autGeneral` (`id`, `provedor`, `nomina`, `fecha`) VALUES ('$numAut', '$numProv', '$nom', current_timestamp());";
            $query = $requestClass->sqlEject($sql);
        ?>
            <div class="alert alert-success" role="alert">
                <strong>Registro Actualizado.</strong>
            </div>
<?php
        }
    }
}
