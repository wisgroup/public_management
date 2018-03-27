<?php 
// require_once '../../config.php';
// global $liveSites;
$liveSites = "http://www.wisgroup.com.co/apps/wis_fw/";
$siteTitle = "MOSS";

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
        <table >
            <tr>
                <td>
                    <a href="<?php echo $liveSites; ?>"><img src="<?php echo $liveSites.'vista/img/login/logo.png' ?>" width="50%" height="50%" alt="<?php echo $siteTitle; ?>" /></a>
                </td>
            </tr>
            <tr>
                <td>
                    <p><?php echo $nombre; ?><br/></p>
                    <p>La clave de su usuario en <a href="<?php echo $liveSites; ?>"><?php echo $siteTitle; ?></a> se ha cambiado recientemente.</p>
                </td>
            </tr>
            <tr>
                <td>
                    USUARIO: <?php echo $usuario; ?>
                </td>
            </tr>
            <tr>
                <td>CLAVE: <?php echo $password; ?></td>
            </tr>
            <tr>
                <td>
                    <p>Fecha: <?php echo $date; ?></p>
                    <p>Para nosotros es un gusto poder contar con usted.</p>
                </td>
            </tr>
        </table>
    </body>
</html>