
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Planes</title>

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


    <!-- Datatables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs4-4.1.1/dt-1.10.18/datatables.min.css">
    <script src="https://cdn.datatables.net/v/bs4-4.1.1/dt-1.10.18/datatables.min.js"></script>

    <!-- Buttons -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.53/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.53/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.flash.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
</head>
<?php
include("conexion.php");
$data = $_GET['nik'];
?>


<div class="jumbotron jumbotron-fluid">
    <div class="container">
   <a href="index.php" title="Regresar"  class="btn btn-warning">Regresar</a>

        <h1 class="display-3">ESTADO DE RESULTADOS</h1>
        <hr class="my-2">
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home"
                    type="button" role="tab" aria-controls="pills-home" aria-selected="true">Formulario</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile"
                    type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Resultados</button>
            </li>
        </ul>     
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <div class="container">
                
                <?php
                if(isset($_GET['aksi']) == 'delete'){
                    // escaping, additionally removing everything that could be (html/javascript-) code
                    $nik = mysqli_real_escape_string($con,(strip_tags($_GET["nik"],ENT_QUOTES)));
                    $cek = mysqli_query($con, "SELECT * FROM estadoresultados WHERE id='$nik'");
                    if(mysqli_num_rows($cek) == 0){
                    }else{
                        $delete = mysqli_query($con, "DELETE FROM estadoresultados WHERE id='$nik'");
                        if($delete){
                            echo '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button> Datos eliminados correctamente.</div>';
                        }else{
                            echo '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button> Error, no se pudo eliminar los datos.</div>';
                        }
                    }		
                }
			
		        ?>	
                
                <?php

                    if(!empty($_POST)){
                        if(isset($_POST['metaInicial'])&&
                        isset($_POST['objetivo'])&&
                        isset($_POST['acciones'])&&
                        isset($_POST['responsable'])&&
                        isset($_POST['fechaInicio'])&&
                        isset($_POST['fechaFin'])&&
                        isset($_POST['desviacionTime'])&&
                        isset($_POST['recursos'])&&
                        isset($_POST['incidencias'])){
                            if($_POST["metaInicial"]!=""&& 
                            $_POST["objetivo"]!=""&& 
                            $_POST["acciones"]!=""&& 
                            $_POST["responsable"]!=""&& 
                            $_POST["fechaInicio"]!=""&& 
                            $_POST["fechaFin"]!=""&& 
                            $_POST["desviacionTime"]!=""&& 
                            $_POST["recursos"]!=""&& 
                            $_POST["incidencias"]){

                                        
                                include "conexion.php";
                                
                                $found=false;
                                $sql1= "SELECT * FROM `estadoresultados` ORDER BY `estadoresultados`.`codigo` ASC";
                                $query = $con->query($sql1);
                                while ($r=$query->fetch_array()) {
                                    $found=true;
                                    break;
                                }
                                if($found){
                                    print "<script>alert(\"Formulario Enviado Correctamente.\");window.location='detalles.php?nik=".$data."';</script>";
                                }
                                $sql = "INSERT INTO `estadoresultados` (`codigo`, `metaInicial`, `objetivo`, `acciones`, `responsable`, `fechaInicio`, `fechaFin`, `desviacionTime`, `recursos`, `incidencias`, `idPlan`) 
                                            VALUES (NULL, \"$_POST[metaInicial]\",  \"$_POST[objetivo]\", \"$_POST[acciones]\", \"$_POST[responsable]\", \"$_POST[fechaInicio]\", \"$_POST[fechaFin]\", \"$_POST[desviacionTime]\", \"$_POST[recursos]\", \"$_POST[incidencias]\", \"$_POST[idPlan]\");";
                                $query = $con->query($sql);
                                if($query!=null){
                                    print "<script>alert(\"Se Registro Satisfactoriamente\");window.location='detalles.php?nik=".$data."';</script>";
                                }else{
                                    print "<script>alert(\"Ocurrio un error\")";
                                }
                            }                            
                        }
                    }

                    ?>
                    
                    <div class="jumbotron jumbotron-fluid">
                        <div class="container">                                               
                          <form class="row g-3" class="form-horizontal" action="" method="post">   
                           
                            
                              <input type="text"
                                class="form-control" name="idPlan" id="" value="<?=$data?>" aria-describedby="helpId" placeholder="">
                             
                         
                                <div class="col-12">
                                  <div class="md-form amber-textarea active-amber-textarea">
                                      <br><i class="fas fa-pencil-alt prefix"></i></i>Descripción de la actividad* 
                                          <textarea id="" name="metaInicial" class="md-textarea form-control" rows="4" required ></textarea><br>
                                      <label for=""></label>
                                  </div>
                              </div>                               
                                <div class="col-6">
                                    <div class="md-form amber-textarea active-amber-textarea">
                                      <i class="fas fa-pencil-alt prefix"></i>*OBJETIVO A ALCANZAR 
                                             <textarea id="" name="objetivo" class="md-textarea form-control" required ></textarea>
                                        <label for=""></label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="md-form amber-textarea active-amber-textarea">
                                        <i class="fas fa-pencil-alt prefix"></i>*ACCIONES 
                                            <textarea id="" name="acciones" class="md-textarea form-control" required></textarea><br>
                                        <label for=""></label>
                                    </div>
                                </div><br>
                                <div class="col-12"><i class="fas fa-pencil-alt prefix"></i>*RESPONSABLE
                                  <input type="text" name="responsable" class="form-control" placeholder="RESPONSABLE" aria-label="" required><br>
                                </div>       
                                <div class="col-6"><i class="fas fa-pencil-alt prefix"></i>*FECHA INICIO 
                                    <input type="date" name="fechaInicio" class="form-control" required >
                                </div>
                                <div class="col-6"> <i class="fas fa-pencil-alt prefix"></i>*FECHA FIN 
                                     <input type="date" name="fechaFin" class="form-control" required><br>
                                </div>    
                                <div class="col-6">
                                    <div class="md-form amber-textarea active-amber-textarea">
                                      <i class="fas fa-pencil-alt prefix"></i>*DESVIACI&Oacute;N EN EL TIEMPO 
                                            <textarea id="" name="desviacionTime" class="md-textarea form-control" required></textarea>
                                        <label for=""></label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="md-form amber-textarea active-amber-textarea">
                                      <i class="fas fa-pencil-alt prefix"></i>*RECURSOS 
                                            <textarea id="" name="recursos" class="md-textarea form-control" required ></textarea><br>
                                        <label for=""></label>
                                    </div>
                                </div>            
                                <div class="col-12">
                                  <div class="md-form amber-textarea active-amber-textarea">
                                      <br><i class="fas fa-pencil-alt prefix"></i>INCIDENCIAS* 
                                          <textarea id="" name="incidencias" class="md-textarea form-control" rows="4" required></textarea>
                                      <label for=""></label>
                                  </div>
                              </div>   
                            <div class="col-12">   
                                <center>
                                <br><input type="submit"  name="agregar" class="btn btn-outline-success" value="Guardar datos">   
                                <button type="reset" class="btn btn-outline-dark" style="margin-right: 20px;"> Limpiar</button>     
                                </center> 
                            </div>        
                          </form>      
                        </div> 
                    </div>
                    </div>
            </div>

            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                    <div class="table-responsive" id="example-container">
                        <table class="records_list table table-striped table-bordered table-hover" id="example">
                            <thead class="table-dark ">
                                <tr>
                                    <th>No.</th>
                                    <th>Codigo</th>
                                    <th>Meta Inicial del Plan de tranajo</th>
                                    <th>Objetivo a alcanzar</th>
                                    <th>Acciones</th>
                                    <th>Rsponsable</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Desviación en el tiempo</th>
                                    <th>Recursos</th>
                                    <th>Incidencias</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody >
                               
                                <?php                               
                                    $sql = mysqli_query($con, "SELECT * FROM estadoresultados WHERE idPlan = '$data'  ORDER BY codigo ");                                                        
                                if(mysqli_num_rows($sql) == 0){
                                    echo '<tr><td colspan="8">No hay datos.</td></tr>';
                                }else{
                                    $no = 1;
                                    while($row = mysqli_fetch_assoc($sql)){
                                        echo '
                                        <tr>
                                            <td>'.$no.'</td>
                                            <td>'.$row['codigo'].'</td>
                                            <td>'.$row['metaInicial'].'</td>
                                            <td>'.$row['objetivo'].'</td>
                                            <td>'.$row['acciones'].'</td>
                                            <td>'.$row['responsable'].'</td>
                                            <td>'.$row['fechaInicio'].'</td>
                                            <td>'.$row['fechaFin'].'</td>
                                            <td>'.$row['desviacionTime'].'</td>
                                            <td>'.$row['recursos'].'</td>
                                            <td>'.$row['incidencias'].'</td>
                                            <td>
                                            <center>
                                            <div align=center>
                                                <a href="planes.php?aksis=search&nik='.$row['id'].'" title="Buscar Planes"  class="fa-solid fa-bars"></i></a>
                                            </div>
                                            <div align=left>
                                                <a href="index.php?aksi=delete&nik='.$row['codigo'].'" title="Eliminar" onclick="return confirm(\'Esta seguro de borrar los datos '.$row['metaInicial'].'?\')" class="fa fa-eraser" style="font-size:16px;color:red"></i></a>
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
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#example tfoot th').each(function () {
                var title = $(this).text();
                $(this).html('<input type="text" placeholder="Filtrar.." />');
            });

            var table = $('#example').DataTable({
                "dom": 'B<"float-left"i><"float-right"f>t<"float-left"l><"float-right"p><"clearfix">',
                "responsive": false,
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json"
                },
                "order": [
                    [0, "desc"]
                ],
                "initComplete": function () {
                    this.api().columns().every(function () {
                        var that = this;

                        $('input', this.footer()).on('keyup change', function () {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    })
                },
                "buttons": ['copy','csv', 'excel', 'pdf', 'print']
            });
        });
    </script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>