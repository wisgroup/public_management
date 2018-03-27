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
                    <p>La solicitud realizada en <a href="<?php echo $liveSites; ?>"><?php echo $siteTitle; ?></a> ha sido realizada correctamente, registrada con el c&oacute;digo de caso: <b><?php  echo $codigo;?></b>.</p>
                    <p>Ante cualquier novedad le estaremos informando oportunamente.</p>
                    <p>Gracias por comunicarse con nosotros.</p>
                    <p></p>
                    <p></p>
                </td>
            </tr>
        </table>
    </body>
</html>