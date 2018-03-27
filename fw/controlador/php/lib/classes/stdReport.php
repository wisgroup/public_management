<?
include 'classes/baseObj.php';
class stdReport extends baseObj{

    var $contentType = "";
    var $report_name = "";
    var $report_table = "transacciones";
    var $report_type = '';
    var $actores = array('4'=>'acreedores', '3'=>'proveedores');
    var $actores_alias = array('4'=>'Cliente', '3'=>'Proveedor');
    

    function getContenido($a) {
        include_once 'classes/funciones.php';
        $fx = New Funciones();
        $codigo = $fx->getDataLoged('param_id');
        switch ($a) {
            case "exportar":
                $this->exportar($fx, $codigo);
                break;
            case "ver_detalle":
                $this->ver_detalle($fx);
                break;
            default:
                $this->home($fx);
        }
    }
    
    function home($fx) {
        $this->contentType = "";
        require_once($this->vista);
       // classHTML::home();
        
        $fx->filtros($this->filtros, "");
        $this->getTabla($fx);
    }
    
    function getTabla($fx, $codigo = 0){
        $parametros = New stdClass();
        $query = New stdClass();
        $link = New stdClass();
        $condicion_complemento = "";
        
        if($codigo == 0){
            $usuario = $fx->getDataLoged('codigo');
        }
        $parametros = $fx->getParametros();
        
        if($parametros->tipo_consulta == 2){
            $condicion = "";
        }
        
        $estado = "'APROBADA' OR  r.Estado = 'A' OR r.Estado = 'ANULADA'";
        
        //$condicion_complemento = $fx->getCondicionFiltro($this->filtros, true);
        $link->href = "codigo";
        $link->accion = 'ver_detalle';
        $link->accion_order = "";
        
        $campos[0]->href="codigo";
        $campos[0]->accion="ver_detalle";
        $campos[0]->type=6;
        $campos[0]->vacio=true;
        
        $query->select = array("t2.id as codigo", "CONCAT(u.nombre, ' ',u.apellido1) Responsable","t4.nombre ".$this->actores_alias[$this->report_type]," t2.fecha", "SUM(t1.cantidad) cantidad", "CONCAT('$',FORMAT(SUM(t1.valor_unitario*t1.cantidad ), 0)) as total");
        $query->from = $this->report_table."_item t1,". $this->report_table." t2 LEFT JOIN usuarios u ON t2.responsable_id = u.id , ". $this->report_table."_tipo t3, ".$this->actores[$this->report_type]." t4 ";
        $query->primary = "";
        $query->where = " t1.transaccion_id = t2.id AND t2.tipo_id = t3.id AND t2.actor_id = t4.id AND tipo_id = '$this->report_type'  ".$condicion_complemento." GROUP BY t2.id";
        $query->group_by = "";
        $query->order_by = " t2.id";
        
        $fx->setDataGrid($query, $link, false, $campos);
        //$fx->getResumen('r.Valor', $query);
    }
    private function exportar($fx, $codigo){
        if($fx->getExport()){
             $this->getTabla($fx, $codigo);
        }
    }
    function ver_detalle($fx){
        $parametros = $fx->getParametros('GET');
        
        $sql = "SELECT t2.id codigo, t3.nombre tipo,t2.fecha,t2.hora, CONCAT(t5.nombre, t5.apellido1) responsable, t4.nombre estado, t2.observacion, t6.nombre as ".$this->actores_alias[$this->report_type] 
                . " FROM transacciones t2 LEFT JOIN ".$this->actores[$this->report_type]. " t6 ON t2.actor_id = t6.id , transacciones_tipo t3, estados t4, usuarios t5 "
                . " WHERE t2.tipo_id = t3.id AND t4.id = t2.estado_id AND t5.id = t2.responsable_id AND t2.id = $parametros->id ";
        
        $result = mysql_query($sql);
        $dato = mysql_fetch_object($result);
            
        $this->contentType = "html";
        require_once($this->vista);
        ClaseHTML::verDetalle($this->report_name, $dato);
        
        $query->select = array("t3.nombre producto","t4.nombre color","(t1.cantidad) cantidad","CONCAT('$',FORMAT((t1.valor_unitario*t1.cantidad ), 0)) as total ");
        $query->from = $this->report_table."_item t1,". $this->report_table." t2, productos t3 LEFT JOIN referencia_color t4 ON t4.id = t3.color_id ";
        $query->primary = "";
        $query->where = " t1.transaccion_id = t2.id AND t1.producto_id = t3.id AND t2.id = $parametros->id ";
        $query->group_by = "";
        $query->order_by = " t2.id";
        
        $fx->setDataGrid($query);
    }
}
?>