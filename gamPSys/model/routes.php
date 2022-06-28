<?php
class routes
{
    public function __construct()
    {
        require_once("model/templates.php");
        require_once("model/validate.php");
        $uri = $_SERVER['REQUEST_URI']; /* Obtenemos la URL */
        $uriParts = explode('/', $uri); /* la dividimos  */
        $this->rpart = $uriParts[2]; /* Recpcionamos la division 2 */
        
    }
    public function route()
    {
        $sessions = new validate;
        $part = $this->rpart; /* Llamamos la url recepcionada */
        $sessionRolUser = $_SESSION['rol69'];

        switch ($part) {
            case '':
                $sessions->sessionsLogin($sessionRolUser);
                $template = new Template("view/login.html", $data = []);
               
                echo $template;
                break;
            case 'adminHome':
                $view = new Template("view/adminDashboard/home.html", $data = []);
                $template = new Template("view/template/index.html", $data = [

                    "content" => $view
                ]);
                echo $template;
                break;
            case 'prueba':
                $template = new Template("view/pruebas/prueba.html", $data = []);
                echo $template;
                break;
            case 'registroPax':
                $view = new Template("view/pax/formAltaPax.html", $data = []);
                $template = new Template("view/template/index.html", $data = [
                    "content" => $view
                ]);
                echo $template;
                break;
            case 'login2':
                $view = new Template("view/login2/login2.html", $data = []);

                echo $view;
                break;
            case 'registro':

                $template = new Template("view/register.html", $data = []);
                echo $template;
                break;
            case 'userHome':

                $view = new Template("view/pax/home.html", $data = []);
                $template = new Template("view/template/index.html", $data = [
                    "content" => $view
                ]);
                echo $template;
                break;
            case 'loggins':
                $sessions->sessionsLogin($_SESSION['rol']);
                $template = new Template("view/reg.html", $data = []);
                echo $template;
                break;
                /* Admin session */
            case 'regNewDoctor':
                $view = new Template("view/adminDashboard/regNewDoctorForm.html", $data = []);
                $template = new Template("view/template/index.html", $data = [

                    "content" => $view
                ]);
                echo $template;
                break;
                case 'paxView':
                    $sessions->sessopnsOperator($sessionRolUser);
                    $view = new Template("view/adminDashboard/altaNoAutManual.html", $data = []);
                    $template = new Template("view/template/index.html", $data = [
    
                        "content" => $view
                    ]);
                    
                    echo $template;
                    
                    break;
                    case 'directorio':
                        $template = new Template("view/directorio/directorio.html", $data = []);
                        echo $template;
                        break;
            default:

                # code...
                break;
        }
    }
}
