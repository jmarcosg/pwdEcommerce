<?php
class AbmCompra
{
    private function cargarObjeto($param)
    {
        $obj = null;
        if (
            array_key_exists('idusuario', $param)
        ) {

            // Creo objeto usuario
            $objUsuario = new Usuario();
            $objUsuario->setIdUsuario($param['idusuario']);
            $objUsuario->cargar();

            // Agregarle los otros objetos
            $obj = new Compra();
            $obj->setear('', '', $objUsuario);
        }
        return $obj;
    }

    private function cargarObjetoConClave($param)
    {
        $obj = null;
        if (isset($param['idcompra'])) {
            $obj = new Compra();
            $obj->setear($param['idcompra'], null, null);
        }
        return $obj;
    }

    private function seteadosCamposClaves($param)
    {
        $resp = false;
        if (isset($param['idcompra']))
            $resp = true;
        return $resp;
    }

    public function alta($param)
    {
        $resp = false;
        $objCompra = $this->cargarObjeto($param);
        print_r($objCompra);
        if ($objCompra != null and $objCompra->insertar()) {
            $resp = true;
        }
        return $resp;
    }

    public function modificacion($param)
    {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {
            $objCompra = $this->cargarObjeto($param);
            if ($objCompra != null and $objCompra->modificar()) {
                $resp = true;
            }
        }
        return $resp;
    }

    public function buscar($param)
    {
        $where = " true ";
        if ($param <> NULL) {
            if (isset($param['idcompra']))
                $where .= " and idcompra =" . $param['idcompra'];
            if (isset($param['cofecha']))
                $where .= " and cofecha =" . $param['cofecha'];
            if (isset($param['idusuario']))
                $where .= " and idusuario ='" . $param['idusuario'] . "'";
        }
        $arreglo = Compra::listar($where);
        return $arreglo;
    }
}
