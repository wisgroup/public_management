<?php

class UsuarioCambioClaveHTML {

    static function home($datos_usuario, $interlocutor) {
        ?>
        <div id="" class="wis_bloque"> 
            <div class="titulo_maestro">
                <p>CAMBIO DE CLAVE</p>
            </div>
            <form name="cambio_clave" action="?opcion=usuario_cambio_clave&a=guardar_clave" method="post" id="cambio_clave" >
                <div >
                    <div class="info_centrado">
                        <p><b>Nombre de Usuario</b> : <?php echo $datos_usuario['usuario']; ?></p>
                        <p><b>Código</b> : <?php echo $interlocutor; ?></p>
                    </div>

                    <table width="100%">
                        <tr>
                            <td class="label_maestro"><b>Ingrese Clave Actual</b></td>
                            <td class="input_maestro"><input type="password" name="clave_actual" id="clave_actual" required></td>
                        </tr>
                        <tr>
                            <td class="label_maestro"><b>Ingrese Nueva Clave</b></td>
                            <td class="input_maestro"><input type="password" id="password" name="password"  required></td>
                        </tr>
                        <tr>
                            <td class="label_maestro"><b>Confirme Nueva Clave</b></td>
                            <td class="input_maestro"> <input type="password" name="password2" id="password2"  required></td>
                        </tr>
                    </table>
                    <div id="botones_accion" class="maestro_edit_form">
                        <table>
                            <tr>
                                <td>
                                    <div id="accion_guardar" class="accion_boton">
                                        <button id="boton_formulario_enviar_clave" type="button" class="guardar btn btn-success" onfocus="this.blur();" onclick="validarCambioClave();"> cambiar </button>
                                    </div>
                                </td>
                                <td>
                                    <div id="accion_cancelar" class="accion_boton">
                                        <button  id="cancelar_configuracion" type="reset" class="cancelar btn btn-danger" onfocus="this.blur();" onclick="cerrar_flotante();">cancelar</button>
                                    </div>
                                </td>
                            </tr>
                        </table>  
                    </div>  
                </div>
            </form>
        </div>
        <?php
    }

    static function terminos_condiciones() {
        ?>
        <div id="flotante_cerrar" onclick="cerrar_flotante();" onfocus="this.blur();">X</div>
        <div class="contenedor_condiciones">
            <p class="titulo_condiciones" >Condiciones de Uso</p>
            <ol>
                <li>
                    <p>El envío  de Spam: Entendiéndose como tal, el envío de 16 mensajes con el mismo contenido en el transcurso de 1 (un) minuto por parte de un mismo Usuario Origen, los cuales se detecten técnicamente.</p>
                </li>
                <li>
                    <p>Envío de flooding: Entendiéndose como tal, el envío de más de 100 (cien) Mensajes Cortos a uno o más destinos en el transcurso de un (1) minuto por parte de un mismo Usuario Origen, los cuales se detecten técnicamente.</p>
                </li>
                <li>
                    <p>Que los Proveedores de Contenidos de la Parte Remitente envíen a los Usuarios Destino de la Parte Receptora, Mensajes Cortos que contengan prefijos o códigos que puedan ser interpretados por el Equipo Terminal Móvil del Usuario Destino como imágenes, tonos  o logos. Con el fin de que las Partes puedan tomar las medidas necesarias para llevar a cabo el filtro de este tipo de Mensajes Cortos, a continuación se detallan  los encabezados que actualmente las Partes tienen detectados y no pueden estar contenidos en ninguna parte del mensaje:</p>
                    <ul>
                        <li>//SCKL</li>
                        <li>//SCK</li>
                        <li>IMELODY</li>
                        <li>L35</li>
                        <li>VERSION:1.0+FORMAT:CLASS1.0 </li>
                    </ul>
                </li>
                <li>
                    <p>El envío por una Parte o por la  Fuerza de Ventas de una Parte a los Usuarios Destino de la otra Parte, de cualquier tipo de Mensaje Corto que sugiera la contratación de los servicios de telecomunicaciones provistos por la otra parte, así como la venta de equipos terminales de cualquier naturaleza, sus accesorios o, cualquier otro servicio o bien que comercialice la Parte Receptora.</p> 
                </li>
                <li>
                    <p>Manipular, falsear o insertar información en alguno de los campos de los Mensajes Cortos intercambiados a través del protocolo SMPP.</p> 
                </li>
                <li>
                    <p>Realizar exploraciones en la arquitectura de cualesquiera de los elementos involucrados en el diagrama de Interoperabilidad de la otra Parte, con el fin de buscar y/o explotar fallas en la seguridad.</p>
                </li>
                <li>
                    <p>Enviar cualquier tipo de mensaje que afecte la configuración/programación del Equipo Terminal Móvil del Usuario Destino.</p>
                </li>
                <li>
                    <p>Enviar cualquier tipo de mensaje que pueda afectar, menoscabar o restringir la operación del Equipo Terminal Móvil del Usuario Destino (Código Malicioso).</p>
                </li>
                <li>
                    <p>Enviar por el Enlace mensajes diferentes a los contemplados en el Contrato y sus Anexos para la prestación del Servicio de SMS sin el consentimiento por escrito de la otra Parte.</p>
                </li>
            </ol>
        </div>
        <?php
    }

}
