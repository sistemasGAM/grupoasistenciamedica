<link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.8.0/sweetalert2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.8.0/sweetalert2.min.js"></script>
<?php

if (!empty($_POST)) {

    $user = (isset($_POST['user']) ? $_POST['user'] : null);
    $email = (isset($_POST['email']) ? $_POST['email'] : null);
    $passOne = (isset($_POST['passOne']) ? $_POST['passOne'] : null);
    $passTwo = (isset($_POST['passTwo']) ? $_POST['passTwo'] : null);
    $nomina = (isset($_POST['nomina']) ? $_POST['nomina'] : null);

    require_once('../../model/validate.php');


    if (isset($user) && isset($email) && isset($passOne) && isset($passTwo) && isset($nomina)) {
        if ($user != "" && $email != "" && $passOne != "" && $passTwo != "" && $nomina != "") {
            $object = new validate;
            $object->validateLogin($data = [
                "user" => $user,
                "email" => $email,
                "passOne" => $passOne,
                "passTwo" => $passTwo,
                "nomina" => $nomina
            ]);
        } else {
?>            
            <script LANGUAGE="javascript">
                    $(document).ready(function() {   
                   swal({
                     title: 'Lo Sentimos!',
                     text: "Variables Vacias!",
                     type: 'error',
                     confirmButtonColor: '#3085d6',
                     confirmButtonText: 'OK!'
                   }).then((result) => {
                     if (result.value) {
                       window.location.href = "";
                     }
                   })   
                 });
            </script>
        <?php
        }
    } else {
?>

        <script LANGUAGE="javascript">
            $(document).ready(function() {   
            swal({
                title: 'Lo Sentimos!',
                text: "Variables Nulas!",
                type: 'error',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK!'
            }).then((result) => {
                if (result.value) {
                window.location.href = "";
                }
            })   
            });
        </script>
<?php
    }
}

