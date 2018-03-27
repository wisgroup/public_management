<?php 
// require_once '../../config.php';
// global $liveSites;
// $liveSites = "https://www.comred.com.co/sms/";
$liveSites ="http://www.wisgroup.com.co/apps/wis_fw/";
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
                    <p>Bienvenido<br/></p>
                    <!-- <p>Para nosotros es un gusto poder contar con usted.</p> -->
                    <p>Estos son los datos de acceso para  su nueva cuenta de usuario en <a href="<?php echo $liveSites; ?>"><?php echo $siteTitle; ?></a></p>
                    <!-- <p>En su primer ingreso debe cambiar la clave <b>por seguridad</b>.</p> -->
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
        </table>
    </body>
</html>