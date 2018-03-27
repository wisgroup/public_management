<?php

class Mailer {

    public static function send_mail($cuerpo, $from, $from_name, $to, $to_name, $subject) {
        require_once 'phpmailer.php';
        $mailTemp = new PHPMailer();
        $mailTemp->IsMail();
        $mailTemp->SetLanguage("es", "php/language/");
        $mailTemp->IsHTML(true);
        $mailTemp->From = $from;
        $mailTemp->FromName = $from_name;
        $mailTemp->Subject = $subject;
        $mailTemp->AddAddress($to, $to_name);
        $mailTemp->Body = $cuerpo;
        $envio = $mailTemp->Send();

        if ($envio) {
            return true;
        } else {
            return $mailTemp->ErrorInfo;
        }
    }

    public static function send_mail_smtp($cuerpo, $from, $from_name, $to, $to_name, $subject) {
        require_once "Mail.php";
        require_once "Mail/mime.php";

        //Nombre con que aprece el remitente mÃ¡s no la direcciÃ³n de correo
        //$from = "Soporte<soporte@comred.com.co>";
        //Destinatario
        //$to = "hatmlive@hotmail.com"; 
        //$subject = "Hi!";
        //Si el visor de correo no soporta html o estÃ¡ apagado este soporte
        //$body_txt = "Hi, \n\n How are you) \n\n Are you there Chelsea?"; 
        //correo en html para agregar imÃ¡genes y contenido multimedia
        //$body_html = "<html><body><p>Hi,\n\nHow are you?<p><br/><h1> Are there Chelsea?<h1></body></html>"; 

        $host = "ssl://smtp.gmail.com";
        $port = "465";
        //Cuenta para autenticar el correo, tambiÃ©n quedarÃ¡ registrado como remitente, recomiendo usar una cuenta ComRed  vÃ¡lida
        $username = "mail@comred.com.co";
        $password = "MailComr3d";
        $crlf = "\n";

        //Contruye la cabecera del mensaje
        $headers = array(
            'From' => $from_name . "<" . $from . ">",
            'To' => $to,
            'Subject' => $subject
        );

        //Crea un correo multi-part (texto + html)
        $mime = new Mail_mime($crlf);

        //Construye el cuerpo en modo texto
        $mime->setTXTBody($cuerpo);
        //Construye el cuerp en modo HTML
        $mime->setHTMLBody($cuerpo);

        //Carga el cuerpo en multi-part
        $body = $mime->get();
        //Convierte las cabeceras a multi-part
        $headers = $mime->headers($headers);

        //Construye el mensaje utilizando el servidor declarado al inicio
        $smtp = Mail::factory('smtp', array('host' => $host,
                    'port' => $port,
                    'auth' => true,
                    'username' => $username,
                    'password' => $password));

        //Envia el correo
        $mail = $smtp->send($to, $headers, $body);

        //Control de Exito o error
        if (PEAR::isError($mail)) {
            return $mail->getMessage();
        } else {
            return true;
        }
    }

    public static function send_mail_smtp2($cuerpo, $from, $from_name, $to, $to_name, $subject) {
        require_once 'phpmailer.php';
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587;
        $mail->Username = "herman.torres@comred.com.co";
        $mail->Password = "htorres10";

        $mail->From = $from;
        $mail->FromName = $from_name;
        $mail->Subject = $subject;
        $mail->AddAddress($to, $to_name);

        $mail->IsHTML(true);
        $mail->AltBody = $cuerpo;
        $mail->Body = $cuerpo;
        //$mail->MsgHTML($cuerpo);
        //$mail->AddAttachment("files/files.zip");
        //$mail->AddAttachment("files/img03.jpg");
        //$mailTemp->SetLanguage("es", "php/language/");

        $envio = $mail->Send();

        if ($envio) {
            return true;
        } else {
            return $mail->ErrorInfo;
        }
    }

    public static function enviarEmailRemoto($cuerpo, $from, $from_name, $to, $to_name, $subject, $host_remoto) {
        $fp = fsockopen($host_remoto, 80, $errno, $errstr, 30);
        if (!$fp) {
            echo "resultado=error&msj=Error inesperado&t=$errstr ($errno)\n";
        } else {
            $key = "k=" . base64_encode(("cuerpo=" . base64_encode($cuerpo) . "&from=" . $from . "&from_name=" . $from_name . "&to=" . $to . "&to_name=" . $to_name . "&subject=" . $subject));

            $http = "POST /mailing_remoto/response.php HTTP/1.1\r\n";
            $http .= "Host: " . $this->host_remoto . "\r\n";
            $http .= "User-Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\r\n";
            $http .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $http .= "Content-length: " . strlen($key) . "\r\n";
            $http .= "Connection: close\r\n\r\n";
            $http .= $key . "\r\n\r\n";
            fwrite($fp, $http);

            $data = "";
            while (!feof($fp)) {
                $data.=fgets($fp, 64000);
            }
            fclose($fp);
            $data = substr($data, strpos($data, "}") + 1);
            return $data;
        }
    }

    public static function mailingGeneral($mail, $datos_mail, $subject, $tokens = "") {
        // $subject = "subject" . ucfirst($mail);
        //global $$subject;

        // $subjectContrasena = "La clave de tu nuevo usuario Multiproducto";
        // $subjectSoporte = "Gracias por comunicarse con nosotros";
        require_once 'controlador/php/lib/app_config.class.php';
        $Conf = NEW App_Config();
        $nombreFrom = $Conf->_get('nombre_from');
        $emailFrom = $Conf->_get('email_from');
        $siteTitle = $Conf->_get('site_tittle');
        
        $liveSite = $Conf->_get('live_site');
        $liveSite1 = $Conf->_get('live_site2');

        $key = "";
        $nombreTo = $datos_mail["nombreTo"];
        $emailTo = $datos_mail["mailTo"];
        $ruta = "controlador/php/mailing/" . $mail . "/" . $mail . ".html";

        if (count($tokens) > 0) {
            foreach ($tokens as $k => $v) {
                $key .= $k . "=" . $v . "&";
            }
            $link_alternativo = $liveSite1 . "controlador/php/mailing/" . $mail . "/" . $mail . ".php?key=" . base64_encode(substr($key, 0, -1));
        } else {
            $link_alternativo = $liveSite1 . "controlador/php/mailing/" . $mail . "/" . $mail . ".php";
        }

        $mensaje = file_get_contents($ruta);
        $mensaje = str_replace("{siteTitle}", $siteTitle, $mensaje);
        $mensaje = str_replace("{link_alternativo}", $link_alternativo, $mensaje);
        $mensaje = str_replace("{link}", $liveSite, $mensaje);
        $mensaje = str_replace('src="imagenes', 'src="' . $liveSite1 . "controlador/php/mailing/" . $mail . "/img", $mensaje);

        foreach ($tokens as $k => $v) {
            $mensaje = str_replace("{" . $k . "}", $v, $mensaje);
        }

        $temp = Mailer::send_mail_smtp($mensaje, $emailFrom, $nombreFrom, $emailTo, $nombreTo, $subject);
        if ($temp === true) {
            return true;
        } else {
            return $temp;
        }
    }
}