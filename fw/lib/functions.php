<?php
/* 
 * 
 */

function parsear_label($key, $input) {
    $campo = (isset($input['label'])) ? $input['label'] : $key;
    if (strpos($campo, '_')) {
        if (substr($campo, 0, 3) == 'id_') {
            $campo = 'Codigo ' . str_replace("_", " ", substr($campo, 3)); // Adicionando la preposicion Codigo al campo id
        } else if (substr($campo, -3) == '_id') {
            $campo = str_replace("_", " ", substr($campo, 0, -3)); // Eliminando la terminacion _id de de las llaves foraneas
        } else {
            $campo = str_replace("_", " ", $campo); //Eliminando los caracteres _
        }
    }
    return ucfirst($campo);
}