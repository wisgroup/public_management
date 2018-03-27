<?
		



		$ms="hola julian d";
		
		$username='COMRED';
		$password='289217';
		$url='http://www.smartdsoluciones.com/SMSWSSService/SMSWSS?wsdl';
		$name='Juli';
		//$id=soap($url,$username,$password, $name, $ms,$tel);
	
		
		$llamado2=soap2('http://www.smartdsoluciones.com/SMSWSSService/SMSWSS?wsdl','COMRED','289217','hola',"3117993293");
			

		

		function soap($url,$username,$password, $name, $ms,$tel){
			//username = $username;
			//password = $password;
			//url = $url;

			$client = new SoapClient($url, array('login'=>$username, 'password'=>$password));
			$id = $client->__soapCall('AbrirEnvio', array('Nombre'=>$name));
			//$respuesta = $client->__soapCall('EnviarMensaje', array('Mensaje'=>$ms, 'Telefono'=>$tel, 'Envio'=>$id));
			
			return $res=$id->return ;


		}
		function soap2($url,$username,$password,$mensaje,$telefono){
			//username = $username;
			//password = $password;
			//url = $url;
			$client = new SoapClient($url, array('login'=>$username, 'password'=>$password));
			//$client = new SoapClient($url, array('login'=>$username, 'password'=>$password));
			//$id = $client->__soapCall('AbrirEnvio', array('Nombre'=>$name));
			//echo '1'.$ms;
			//echo '2'.$tel;
			$tel = (float) $telefono;
			$ids = $client->__soapCall('AbrirEnvio', array('Nombre'=>'prueba'));	
			$id = $ids->return;
			echo "------------------";

			print_r($id);	
			$respuesta=$client->enviarMensaje(array('Mensaje'=>$mensaje, 'Telefono'=>$tel, 'Envio'=>$id));
			//$respuesta = $client->__soapCall('enviarMensaje', array('Mensaje'=>'hola', 'Telefono'=>3117993293, 'Envio'=>$id));
			print_r($respuesta);
			return $respuesta ;


		}

		function abrir_conexion($name){
			$envio = (array('Nombre'=>$name));

			if (is_soap_fault($client)) 
	    		return trigger_error("SOAP Fault: (faultcode: {$result->faultcode}, faultstring: {$result->faultstring})", E_USER_ERROR);
			else 
			return $envio;
		}
				

		function cerrar_envio($id){
			$respuesta = cerrarEnvio(array('EnvioId'=>$id));
			return $respuesta;
		}

		function enviar_mensaje($ms,$tel,$id){
			
			$respuesta = enviarMensaje(array('Mensaje'=>$ms, 'Telefono'=>$tel, 'Envio'=>$id));
			return $respuesta;
		}

	

?>