<?php
class validate
{
  private $conn;
  public function __construct()
  {
    $db_host = "localhost";
    $db_user = "u199109938_Desarrollo"; /*USER  u199109938_Desarrollo*/
    $db_pass = "4n##gF:#UH";
    $db_name = "u199109938_GAMCRUD"; /* u199109938_GAMCRUD*/
    $this->conn = $conect = new MySQLi("$db_host", "$db_user", "$db_pass", "$db_name");
  }

  public function bd()
  {
    return $newCon = $this->conn;
  }
  public function validateLogin($data = [])
  {
    $conect = new validate;
    $newCon = $conect->bd();
    extract($data);


    $query = "SELECT * FROM `usuarios` WHERE correo = '$name'";
    $sql = mysqli_query($newCon, $query);
    if ($f = mysqli_fetch_assoc($sql)) {
      if ($pass == $f['contrasena']) {
        session_start();
        $_SESSION['id'] = $f[0];
        $_SESSION['user'] = $f[9];
        $_SESSION['correo'] = $f[4];
        $_SESSION['rol69'] = $f['rol'];
        ?>
        <script>
          function redireccionarSe() {
            window.location.replace("/gamPSys/paxView");
          }

          setTimeout("redireccionarSe()", 1);
        </script>
      <?php
        
      } else {
?>
        <!--<div class="alert alert-danger" role="alert">
                Contraseña incorrecta
                </div>-->

        <script LANGUAGE="javascript">
          $(document).ready(function() {
            swal({
              title: 'Error!',
              text: "Contraseña Incorrecta!",
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
            title: 'Error!',
            text: "Usuario No Existe!",
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
      <?

    }
  }

  public function sessionsLogin($var)
  {
    switch ($var) {
      case '':
        break;
      case 'operador':
      ?>
        <script>
          function redireccionarSe() {
            window.location.replace("/gamPSys/paxView");
          }

          setTimeout("redireccionarSe()", 1);
        </script>
      <?php
        break;

      default:
        # code...
        break;
    }
  }
  public function sessopnsOperator($var)
  {
    switch ($var) {
      case '':
      ?>
        <script>
          function redireccionarSe() {
            window.location.replace("/gamPSys/");
          }

          setTimeout("redireccionarSe()", 1);
        </script>
      <?php
        break;
      case 'operador':
     
        break;

      default:
        # code...
        break;
    }
  }
}
