<?php

class fxHTML {
    static function filtros_multiples($calendar_inicial, $calendar_final, $filtros, $accion, $param = NULL, $calendar = NULL) {
        global $seccion;
        $hoy = date("Y-m-d");
        $dia_manana = date('d',time()+84600);  
        $mes_manana = date('m',time()+84600);  
        $ano_manana = date('Y',time()+84600);  

        $manana = $ano_manana."-".$mes_manana."-".$dia_manana;  
        $link = $seccion;
        if($accion != '')
            $link.= "&a=".$accion;
        
        $link.= "&content_type=html";

	 if(isset($_SESSION['filtro_estado']))
            $estado = $_SESSION['filtro_estado'];
        else 
            $estado = 0;
    ?>	
        <div id="filtros" class="bg_form">
            <form name="form_filtros" id="form_filtros" action="?opcion=<? echo $link; ?>" method="post" >
            <? if(isset ($filtros['fechas']) && ($filtros['fechas'] === '1')){ ?>
                    <div class ="parametros_fechas"> 
                        <div class="label"> RANGO DE FECHAS:</div>
                        <div class="label_fecha">
                        Desde: 
                        </div>    
                        <div class="campo_fecha">
                            <? $calendar_inicial->writeScript(); ?>
                        </div>    
                        <div class="label_fecha">
                        Hasta: 
                        </div>    
                        <div class="campo_fecha">
                            <? $calendar_final->writeScript(); ?>
                        </div>    
                    </div>
            <? } if(isset ($filtros['dia']) && ($filtros['dia'] === '1')){ ?>
                <div class ="parametros_dia"> 
                    <div class="label"></div>
                    <div class="label_fecha">
                        FECHA: 
                    </div>
                    <div class="campo_fecha">
                    </div>    
                    <div class="campo_fecha">
                        <? $calendar->writeScript(); ?>
                    </div>    
                </div>
            <? } if($filtros['id'] !== '0'){ ?>
                    <div class ="parametros "> 
                        <div class="label"> CONSULTA POR CODIGO:</div>
                        <div class="campo_filtro">
                            <input name="id" id="id"  type="text" class="texto1">
                            <input type="hidden" name="tipo_consulta" id="tipo_consulta" value="1">
                        </div>
                    </div>
            <? }if($filtros['nombre'] !== '0'){ ?>            
                <div class ="parametros "> 
                    <div class="label"> CONSULTA POR NOMBRE:</div>
                    <div class="campo_filtro">
                        <input name="nombre" id="nombre"  type="text" class="texto1">
                        <input type="hidden" name="tipo" id="tipo" value="1">
                    </div>
                </div>
            <?}?>
            <div class ="back">
                <? if(isset ($filtros['back']) && ($filtros['back'] === '1')){ ?>            
                    <div class="boton_filtro botones_filtros">
                        <input name="back" id="back" class="inset boton" type="button" value="ATRAS" onclick="window.location='?opcion=<? echo $seccion; ?>&back=1'">
                    </div> 
                <? } ?> 
                <div class="boton_filtro botones_filtros">
                    <input name="buscar" id="buscar" class="inset boton" type="submit" value="BUSCAR">
                </div>   
            </div>
        </form>
        <? if($filtros[3] === '1'){ ?>            
            <div class ="dato">
                <div class="label"> SALDO DISPONIBLE</div>
                <div align="center">
                    <input name="saldo"  type="text" id="saldo" class="texto1"  value="$<?php echo number_format($saldo->pesos, 2); ?>" onfocus="this.blur();">
                </div>
            </div>
        <? }if($filtros[4] === '1'){ ?>            
        <div class ="exportar">
            <div class="label"> EXPORTAR</div>
            <div align="center">
                <a href="?opcion=<? echo $seccion; ?>&a=exportar&id=<? echo $param; ?>" onfocus="this.blur();">
                    <img src="img/excel_icon.png" height="40" width="40" alt="exportar" title="Exportar en XLS" >
                </a>
            </div>
        </div>
        <div class ="exportar">
            <div class="label"> EXPORTAR</div>
            <div align="center">
                <a href="?opcion=<? echo $seccion; ?>&a=exportar&id=<? echo $param; ?>&type=pdf" onfocus="this.blur();" target="_blank">
                    <img src="img/pdf.png" height="40" width="40" alt="exportar" title="Exportar en PDF" >
                </a>
            </div>
        </div>
        <? }if(isset ($filtros['alerta']) && ($filtros['alerta'] === '1')){ ?>            
        <div class ="warning">
            <div class="warning_msj">
                <p>
                    <img src="img/warning.png" width="30" height="30">
                    <? echo $filtros['alerta_msj']; ?>
                </p>
            </div> 
        </div>
        <? } ?>            
    </div>
    <?
    }
    static function resumen($datos = NULL) {
    ?>
        <div id="resumen" class="bg_form">
            <div class="titulo_formulario titulo_resumen">
                RESUMEN
            </div> 
            <div class="columna_resumen">
                <div class="item_resumen">
                    <b class="nombre_resumen"><? echo $datos[0]->resumen_nombre; ?></b>
                    <div class="valor">
                        $<? echo $datos[0]->dato; ?>
                    </div>
                </div>
            </div>
        </div>       
    <?
    }
    static function resumenUtilidad($datos = NULL) {
    ?>
        <div id="resumen" class="bg_form">
            <div class="titulo_formulario titulo_resumen">
                RESUMEN
            </div> 
            <div class="columna_resumen">
                <div class="item_resumen">
                    <b class="nombre_resumen"><? echo $datos[0]->resumen_nombre; ?></b>
                    <div class="valor">
                        $<? echo $datos[0]->dato; ?>
                    </div>
                </div>
                <div class="item_resumen">
                    <b class="nombre_resumen"><? echo 'TOTAL UTILIDAD'; ?></b>
                    <div class="valor">
                        $<? echo $datos[0]->utilidad; ?>
                    </div>
                </div>
            </div>
        </div>       
    <?
    }
    static function resumenLista($datos) {?>
        <div id="resumen" class="">
            <div class="titulo_formulario titulo_resumen">
                RESUMEN
            </div> 
            <? $t=0;?>
            <div class="columna_resumen">
                <? foreach($datos as $k => $v){?>
                <div class="item_resumen">
                    <?php if ($k=='u' || $k=='w'): ?>
                        <b class="nombre_resumen">Nro Elementos:</b>
                        <div class="valor">
                            <? echo $v ?>
                        </div>
                    <?php else: ?>                                     
                        <b class="nombre_resumen"><? echo "Total ".$k ?></b>
                        <div class="valor">
                           $<? echo number_format($v,2);?>
                        </div> 
                    <?php endif; ?>
                    </div>
                <?$t=$v+$t;?>
                <? }?>
 
                <div class="item_resumen">
                    <b class="nombre_resumen"><? echo "Total "?></b>
                    <div class="valor">
                        $<? echo number_format($t,2);?>
                    </div>
                </div>

            </div>
        </div>     
    <?
    }

    static function titulo($titulo){?>
        <div class="titulo_formulario">
            <? echo strtoupper($titulo); ?>
        </div>  
    <?}
    static function subtitulo($titulo){?>
        <div class="titulo_formulario">
            <? echo strtoupper($titulo); ?>
        </div>  
    <?}
}
?>