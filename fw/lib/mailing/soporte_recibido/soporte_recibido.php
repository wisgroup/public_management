<?php 
// require_once '../../config.php';
// global $liveSites;
// global $siteTitle;

$liveSites = "http://multiproducto.comred.co/";
$siteTitle = "Multiproducto";

$datos = explode("&", base64_decode($_GET['key']));

foreach ($datos as $d) {
    $temp = explode("=", $d);
    $$temp[0] = $temp[1];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>.:<?php echo $siteTitle; ?>:.</title>
    </head>
    <body bgcolor="#fff">
        <table>
            <tr>
                <td>
                    <a href="<?php echo $liveSites; ?>"><img src="<?php echo $liveSites.'vista/img/login/logo.png' ?>" width="50%" height="50%" alt="<?php echo $siteTitle; ?>" /></a>
                </td>
            </tr>
            <tr>
                <td>
                    <p><?php echo $nombre; ?></p>
                    <p>Se ha recibido una solicitud de soporte realizada por el Usuario: <b><?php echo $cod_usuario.' '.$usuario ?></b>. </p>
                    <p>Registrada con el c&oacute;digo de caso: <b><?php  echo $codigo;?></b></p>
                    <p>Fecha de solicitud: <b><?php echo $date; ?></b></p>
                </td>
            </tr>
        </table>
    </body>
</html>