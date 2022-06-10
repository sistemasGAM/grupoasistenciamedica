<?php
class Template
{
    private $content; /* Declaración de variable en private para protegerla */
    public function __construct($path, $data = []) /* Recepcionamos variables */
    {
        extract($data); /* Extraemos variables del $data = [] */
        ob_start();  /* Activa el almacenamiento en búfer de la salida */
        include($path); /* Incluimos la ruta de la platilla */
        $this->content = ob_get_clean(); /* Agregamos el contenido y cerramos */
    }
    public function __toString() /* Función que corre en segundo plano */
    {
        return $this->content; /* Retorna el contenido protegido */
    }
}
