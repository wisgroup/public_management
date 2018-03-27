<?
	
	class soap_sms{

		private $username;
		private $password;
		private $url;
		private $client;

		function soap_sms($url,$username,$password){
			$this->username = $username;
			$this->password = $password;
			$this->url = $url;
			$this->client = new SoapClient($this->url, array('login'=>$this->username, 'password'=>$this->password));

		}
		
		function abrir_conexion($name){
			$envio = $this->client->AbrirEnvio(array('Nombre'=>$name));
		
			if (is_soap_fault($this->client)) 
	    		return trigger_error("SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring})", E_USER_ERROR);
			else 
				return $envio->return;
		}
				

		function cerrar_envio($id){
			$respuesta = $this->client->cerrarEnvio(array('EnvioId'=>$id));
			return $respuesta;
		}

		function enviar_mensaje($ms,$tel,$id){
			
			$respuesta = $this->client->enviarMensaje(array('Mensaje'=>$ms, 'Telefono'=>$tel, 'Envio'=>$id));
			return $respuesta;
		}

		function estado_mensaje($id){
			$respuesta = $this->client->estadoMensaje(array('IdMensaje' =>$id));
			return $respuesta;
		}

		function soap($url,$username,$password,$mensaje,$telefono){
			$client = new SoapClient($url, array('login'=>$username, 'password'=>$password));
			$tel = (float) $telefono;
			$ids = $client->__soapCall('AbrirEnvio', array('Nombre'=>'prueba'));	
			$id = $ids->return;
			
			$respuesta=$client->enviarMensaje(array('Mensaje'=>$mensaje, 'Telefono'=>$tel, 'Envio'=>$id));
			
			return $respuesta ;


		}

	}

?>
