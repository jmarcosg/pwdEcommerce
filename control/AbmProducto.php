<?php
class AbmProducto
{
    private function cargarObjeto($param)
    {
        $obj = null;
        if (array_key_exists('idproducto', $param) && array_key_exists('proprecio', $param) && array_key_exists('prodescuento', $param) && array_key_exists('pronombre', $param) && array_key_exists('prodetalle', $param) && array_key_exists('procantstock', $param)) {
            $obj = new Producto();
            $obj->setear(
                $param['idproducto'],
                '',
                $param['proprecio'],
                $param['prodescuento'],
                $param['pronombre'],
                $param['prodetalle'],
                $param['provecescomprado'],
                $param['procantstock'],
                ''
            );
        }

        return $obj;
    }

    private function cargarObjetoConClave($param)
    {
        $obj = null;

        if (isset($param['idproducto'])) {
            $obj = new Producto();
            $obj->setear($param['idproducto'], null, null, null, null, null, null, null, null);
        }
        return $obj;
    }

    private function seteadosCamposClaves($param)
    {
        $resp = false;
        if (isset($param['idproducto'])) {
            $resp = true;
        }
        return $resp;
    }

    public function alta($param)
    {
        $resp = false;
        $existe = false;
        $datosBusqueda['idproducto'] = $param['idproducto'];
        $listaProductos = $this->buscar($param);
        if (isset($listaProductos[0])) {
            $existe = true;
        }
        if (!$existe) {
            $ObjProducto = $this->cargarObjeto($param);
            if ($ObjProducto != null and $ObjProducto->insertar()) {
                $resp = true;
            }
            $controlCargaImagen = new controlCargaImagenes();
            $controlCargaImagen->cargarImagen($param['files'], $param['idproducto']);
        }
        return $resp;
    }

    public function baja($param)
    {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {
            $ObjProducto = $this->cargarObjetoConClave($param);
            if ($ObjProducto != null and $ObjProducto->eliminar()) {
                $resp = true;
            }
        }
        return $resp;
    }

    public function modificacion($param)
    {
        $resp = false;
        print_r($param);
        if ($this->seteadosCamposClaves($param)) {
            $listadoProductos = $this->buscar(['idproducto'=>$param['idproducto']]);
            if ($listadoProductos != null) {
                $ObjProducto = $this->cargarObjeto($param);
                if($ObjProducto->modificar()){
                    $resp = true;
                    $controlCargaImagen = new controlCargaImagenes();
                    $nombreImagen = $param['files']['imagen']['name'];
                    if($nombreImagen != ""){
                        $controlCargaImagen->eliminarImagen($param['idproducto']);
                        $controlCargaImagen->cargarImagen($param['files'], $param['idproducto']);
                    }
                }
            }
        }
        return $resp;
    }

    public function deshabilitarProd($param)
    {
        $resp = false;
        $objProducto = $this->cargarObjetoConClave($param);
        $listadoProductos = $objProducto->listar("idproducto='" . $param['idproducto'] . "'");
        if (count($listadoProductos) > 0) {
            $estadoProducto = $listadoProductos[0]->getProDeshabilitado();
            if ($estadoProducto == '0000-00-00 00:00:00') {
                if ($objProducto->estado(date("Y-m-d H:i:s"))) {
                    $resp = true;
                }
            } else {
                if ($objProducto->estado()) {
                    $resp = true;
                }
            }
        }
        return $resp;
    }

    public function chequearStock($param)
    {
        $resp = false;
        $idProducto = $param->getIdProducto()->getIdProducto();
        $objProducto = $this->cargarObjetoConClave(["idproducto" => $idProducto]);
        $listadoProductos = $objProducto->listar("idproducto='" . $idProducto . "'");
        if (count($listadoProductos) > 0) {
            $stock = $listadoProductos[0]->getProCantStock();
            if ($stock >= $param->getCiCantidad()) {
                $resp = true;
            }
        }
        return $resp;
    }

    public function buscar($param)
    {
        $where = " true ";
        if ($param <> NULL) {
            if (isset($param['idproducto']))
                $where .= " and idproducto ='" . $param['idproducto'] . "'";
            if (isset($param['proprecio']))
                $where .= " and proprecio =" . $param['proprecio'];
            if (isset($param['prodescuento']))
                $where .= " and prodescuento =" . $param['prodescuento'];
            if (isset($param['pronombre']))
                $where .= " and pronombre ='" . $param['pronombre'] . "'";
            if (isset($param['prodetalle']))
                $where .= " and prodetalle ='" . $param['prodetalle'] . "'";
            if (isset($param['provecescomprado']))
                $where .= " and provecescomprado >=" . $param['provecescomprado'];
            if (isset($param['procantstock']))
                $where .= " and procantstock =" . $param['procantstock'];
        }
        $arreglo = Producto::listar($where);
        return $arreglo;
    }
}
