<?php
include("conexion.php");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>&Aacute;reas</title>

    <!-- Required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />


</head>
<body>
        <?php
			if(isset($_GET['aksi']) == 'delete'){
				// escaping, additionally removing everything that could be (html/javascript-) code
				$nik = mysqli_real_escape_string($con,(strip_tags($_GET["nik"],ENT_QUOTES)));
				$cek = mysqli_query($con, "SELECT * FROM areatable WHERE id='$nik'");
				if(mysqli_num_rows($cek) == 0){
				}else{
                    $delete = mysqli_query($con, "DELETE FROM areatable WHERE id='$nik'");
					if($delete){
						echo '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button> Datos eliminados correctamente.</div>';
					}else{
						echo '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button> Error, no se pudo eliminar los datos.</div>';
					}
                }		
			}
			
		?>	
        <?php
        if(!empty($_POST['agregar'])){
            if(isset($_POST['area'])){
                if($_POST["area"]!=""){                    
                    include "conexion.php";
                                      
                    $found=false;
                    $sql1= "SELECT * FROM `areatable`";
                     
                    $query = $con->query($sql1);                    
                    while ($r=$query->fetch_array()) {
                        $found=true;
                        break;                       
                    }                     
                   
                    $sql = "INSERT INTO `areatable` (`id`, `area`) VALUES (NULL, \"$_POST[area]\")";
                    $query = $con->query($sql);
                    
                    if($query!=null){
                        print "<script>alert(\"Se Registro Satisfactoriamente\");window.location='index.php';</script>";
                        
                    }else{                       
                        print "<script>alert(\"Ocurrio un error\")";
                    }
                }                            
            }
        }
    
        ?>			    
<div class="container">
    <div class="jumbotron jumbotron-fluid">
        <center><h3 class="display-3">&Aacute;reas</h3></center>
        
        <div class="container">                     
           
            </script>
            <br><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@mdo">Agregar Nueva &Aacute;rea </button>

            <div>                
                <table  class="table table-responsive-lg table-bordered dt-responsive nowrap ">
                    <thead class="table-dark ">
                        <tr>
                            <th>No</th>
                            <th>Id</th>
                            <th>&Aacute;rea</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody >
                            <?php								
                                $sql = mysqli_query($con, "SELECT * FROM `areatable` ORDER BY `areatable`.`id` ASC");				
                                                
                                if(mysqli_num_rows($sql) == 0){
                                        echo '<tr><td colspan="8">No hay datos.</td></tr>';
                                }else{					
                                    $no = 1;
                                    while($row = mysqli_fetch_assoc($sql)){
                                        echo '
                                        <tr>
                                            <td>'.$no.'</td>
                                            <td>'.$row['id'].'</td>                                           
                                            <td>'.$row['area'].'</td>
                                            <td>
                                            <center>
                                            <div align=center>
                                                <a href="planes.php?aksis=search&nik='.$row['id'].'" title="Buscar Planes"  class="fa-solid fa-bars"></i></a>
                                            </div>
                                            <div align=left>
                                                <a href="index.php?aksi=delete&nik='.$row['id'].'" title="Eliminar" onclick="return confirm(\'Esta seguro de borrar los datos '.$row['area'].'?\')" class="fa fa-eraser" style="font-size:16px;color:red"></i></a>
                                            </div>
                                            </center>
                                            </td>
                                        </tr>
                                        ';
                                        $no++;
                                    }
                                }
                            ?>        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <br>  
</div>  
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Formulario de Estado de Resultados</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
        <div class="modal-body">
            <form class="row g-3" class="form-horizontal" action="" method="post">   
                
                <div class="col-12"><i class="fas fa-pencil-alt prefix"></i>*&Aacute;rea
                    <input type="text" name="area" class="form-control" placeholder="&Aacute;rea" required><br>
                </div>                       
                <div class="col-12">           
                </div>     
                <br><input type="submit"  name="agregar" class="btn btn-outline-success btn-md" value="Guardar datos">    
            </form>      
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button>          
      </div>
    </div>
  </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>


