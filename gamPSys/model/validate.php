<link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.8.0/sweetalert2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.8.0/sweetalert2.min.js"></script>
    <?php
class validate{
    private $conn;
    public function __construct()
    {
        $db_host = "localhost";
        $db_user = "u199109938_Desarrollo"; /*USER  u199109938_Desarrollo*/
        $db_pass = "4n##gF:#UH";
        $db_name = "u199109938_GAMCRUD"; /* u199109938_GAMCRUD*/
        $this->conn = $conect = new MySQLi("$db_host", "$db_user", "$db_pass", "$db_name");
        
    }
    
    public function bd(){
        return $newCon = $this->conn;
    }
    public function validateLogin($data = []){
        $conect = new validate;
        $newCon = $conect->bd();
        extract($data);
        
        
        $query = "SELECT * FROM pruebaUser WHERE email = '$name'";
        $sql = mysqli_query($newCon, $query);
        if ($f = mysqli_fetch_assoc($sql)) {
                if($pass == $f['password']){
                    echo "Valido";
                }else{
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
        }
        else{
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
    #Prueba

    /* 
    session_start();
    switch($POST['datos'])
  {
        case 'login':                    
              include('./x/login.html');
               break; 

        case 'logout':                    
              include('./x/logout.php'); 
              break;   
        case '':
            
              break;
            
            default:
                
                break;
        }
    */
}
