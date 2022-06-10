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
        <table class= "table">
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
                        <button type="submit" style="border-radius: 12px;" class="btn btn-dark"><i class="fa-solid fa-bars"></i></button>
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
}
