<?php
class AbmProducto
{
    private function cargarObjeto($param)
    {
        $obj = null;
        if (array_key_exists('idproducto', $param) && array_key_exists('proprecio', $param) && array_key_exists('prodescuento', $param) && array_key_exists('pronombre', $param) && array_key_exists('prodetalle', $param) && array_key_exists('provecescomprado', $param) && array_key_exists('procantstock', $param)) {
            $obj = new Producto();
            $obj->setear($param['idproducto'], $param['proprecio'], $param['prodescuento'], $param['pronombre'], $param['prodetalle'], $param['provecescomprado'], $param['procantstock']);
        }
        
        return $obj;
    }

    private function cargarObjetoConClave($param)
    {
        $obj = null;

        if (isset($param['idproducto'])) {
            $obj = new Producto();
            $obj->setear($param['idproducto'], null, null, null, null, null, null);
        }
        return $obj;
    }

    private function seteadosCamposClaves($param)
    {
        $resp = false;
        if (isset($param['idproducto']))
            $resp = true;
        return $resp;
    }

    public function alta($param)
    {
        $resp = false;

        $elObjtProducto = $this->cargarObjeto($param);

        if ($elObjtProducto != null and $elObjtProducto->insertar()) {
            $resp = true;
        }
        return $resp;
    }

    public function baja($param)
    {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {
            $elObjtProducto = $this->cargarObjetoConClave($param);
            if ($elObjtProducto != null and $elObjtProducto->eliminar()) {
                $resp = true;
            }
        }
        return $resp;
    }

    public function modificacion($param)
    {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {
            $elObjtProducto = $this->cargarObjeto($param);
            if ($elObjtProducto != null and $elObjtProducto->modificar()) {
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
                $where .= " and idproducto =" . $param['idproducto'];
            if (isset($param['proprecio']))
                $where .= " and proprecio =" . $param['proprecio'];
            if (isset($param['prodescuento']))
                $where .= " and prodescuento =" . $param['prodescuento'];
            if (isset($param['pronombre']))
                $where .= " and pronombre =" . $param['pronombre'];
            if (isset($param['prodetalle']))
                $where .= " and prodetalle ='" . $param['prodetalle'];
            if (isset($param['provecescomprado']))
                $where .= " and provecescomprado ='" . $param['provecescomprado'];
            if (isset($param['procantstock']))
                $where .= " and procantstock ='" . $param['procantstock'];
        }
        $arreglo = Producto::listar($where);
        return $arreglo;
    }
}