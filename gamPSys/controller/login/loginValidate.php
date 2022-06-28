<link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.8.0/sweetalert2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.8.0/sweetalert2.min.js"></script>
<?php



    $name = (isset($_POST['name']) ? $_POST['name'] : null);
    $pass = (isset($_POST['pass']) ? $_POST['pass'] : null);

    require_once('../../model/validate.php');



        if ($name != null && $pass != null) {
            $object = new validate;
            $object->validateLogin($data = [
                "name" => $name,
                "pass" => $pass
            ]);
        } else {
?>
            <!--<div class="alert alert-danger" role="alert">
                Variables Vacias
            </div>-->
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
    





/* ------------------------------------------------------- */
