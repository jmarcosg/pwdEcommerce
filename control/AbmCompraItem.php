<?php
class AbmCompraItem
{
    private function cargarObjeto($param)
    {
        $obj = null;
        if (array_key_exists('idproducto', $param) && array_key_exists('idcompra', $param)) {
            $objProducto = new Producto();
            $objProducto->setIdProducto($param['idproducto']);
            $objProducto->cargar();

            $objCompra = new Compra();
            $objCompra->setIdCompra($param['idcompra']);
            $objCompra->cargar();

            $obj = new CompraItem();
            $obj->setear($param['idcompraitem'], $objProducto, $objCompra, $param['cicantidad']);
        }

        return $obj;
    }

    private function cargarObjetoConClave($param)
    {
        $obj = null;
        if (isset($param['idcompraitem'])) {
            $obj = new CompraItem();
            $obj->setear($param['idcompraitem'], null, null, null);
        }

        return $obj;
    }

    private function seteadosCamposClaves($param)
    {
        $resp = false;
        if (isset($param['idcompraitem'])) {
            $resp = true;
        }

        return $resp;
    }

    public function alta($param)
    {
        $resp = false;
        // Traigo los carritos que tiene el usuario
        $controlVerificarCarrito = new controlVerificarCarritoCliente();
        $arrayCarritos = $controlVerificarCarrito->verificarCarrito($param['iduser']);
        // Carrito habilitado (que seria el que tiene activo)
        $carrito = $arrayCarritos['carritoHabilitado'];
        // Si el carrito no existe entonces crea un carrito nuevo
        if ($carrito == null) {
            $abmCarrito = new AbmCompra();
            $array = ['idusuario' => $param['iduser']];
            // alta carrito para el usuario actual
            $altaCarrito = $abmCarrito->alta($array);
            if ($altaCarrito) {
                // Aca traigo el carrito activo de vuelta
                $arrayCarritos = $controlVerificarCarrito->verificarCarrito($param['iduser']);
                $carrito = $arrayCarritos['carritoHabilitado'];
            }
        }
        // saco el id del carrito actual
        $idCarrito = $carrito->getIdCompra();
        // establezco los datos de interes, en este caso el idProducto y el idCarrito
        $arrayCargaItem = ['idproducto' => $param['codigoProducto'], 'idcompra' => $idCarrito];
        $cargado = false;
        // listado de items cargados en el carrito
        $arrayItemsCarrito = $this->buscar(['idcompra' => $carrito->getIdCompra()]);
        // verifico que no este cargado ya el item actual en el carrito
        foreach ($arrayItemsCarrito as $itemCarrito) {
            if ($itemCarrito->getIdProducto()->getIdProducto() == $param['codigoProducto']) {
                $cargado = true;
            }
        }
        // sino esta cargado entonces se inserta en la bd 
        if (!$cargado) {
            // inserto item
            $objCompraItem = $this->cargarObjeto($arrayCargaItem);
            if ($objCompraItem != null and $objCompraItem->insertar()) {
                $resp = true;
            }
        }
        return $resp;
    }

    public function baja($param)
    {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {
            $objCompraItem = $this->cargarObjetoConClave($param);
            if ($objCompraItem != null and $objCompraItem->eliminar()) {
                $resp = true;
            }
        }

        return $resp;
    }

    public function modificacion($param)
    {
        //echo "Estoy en modificacion";
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {
            $objCompraItem = $this->cargarObjeto($param);
            if ($objCompraItem != null and $objCompraItem->modificar()) {
                $resp = true;
            }
        }
        return $resp;
    }

    public function sumarItem($param)
    {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {
            $objCompraItem = $this->cargarObjetoConClave($param);
            $objCompraItem = $this->buscar(['idcompraitem' => $param['idcompraitem']]);
            if ($objCompraItem[0] != null) {
                $idProducto = $objCompraItem[0]->getIdProducto()->getIdProducto();
                $abmProducto = new AbmProducto();
                $objProducto = $abmProducto->buscar(['idproducto' => $idProducto]);
                $stockActual = $objProducto[0]->getProCantStock();
                $cantItems = $objCompraItem[0]->getCiCantidad();
                if ($stockActual > $cantItems) {
                    $objCompraItem[0]->setCiCantidad($cantItems + 1);
                    if ($objCompraItem[0]->modificar()) {
                        $resp = true;
                    }
                }
            }
        }
        return $resp;
    }

    public function restarItem($param)
    {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {
            $objCompraItem = $this->cargarObjetoConClave($param);
            $objCompraItem = $this->buscar(['idcompraitem' => $param['idcompraitem']]);
            if ($objCompraItem[0] != null) {
                $cantItems = $objCompraItem[0]->getCiCantidad();
                if ($cantItems > 1) {
                    $objCompraItem[0]->setCiCantidad($cantItems - 1);
                    if ($objCompraItem[0]->modificar()) {
                        $resp = true;
                    }
                }
            }
        }
        return $resp;
    }

    public function buscar($param)
    {
        $where = " true ";
        if ($param <> NULL) {
            if (isset($param['idcompraitem']))
                $where .= " and idcompraitem =" . $param['idcompraitem'];
            if (isset($param['idproducto']))
                $where .= " and idproducto =" . $param['idproducto'];
            if (isset($param['idcompra']))
                $where .= " and idcompra ='" . $param['idcompra'] . "'";
            if (isset($param['cicantidad']))
                $where .= " and cicantidad ='" . $param['cicantidad'] . "'";
        }
        $arreglo = CompraItem::listar($where);
        return $arreglo;
    }
}
