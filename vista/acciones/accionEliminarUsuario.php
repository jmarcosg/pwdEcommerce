<?php
include_once "../../configuracion.php";

$datos = data_submitted();

$sesion = new Session();
if (!$sesion->activa()) {
    $message = "No ha iniciado sesion";
    header('Location: ../login/login.php?Message=' . urlencode($message));
}

$abmUsuario = new AbmUsuario();

?>

<div class="container mt-3">
    <?php

    $idUsuarioSesion = $sesion->getIdUsuario();
    if (isset($datos)) {
        if ($datos['idusuario'] == $idUsuarioSesion) {
            $message = "No se puede deshabilitar a si mismo";
            header('Location: ../admin/administrarUsuarios.php?Message=' . urlencode($message));
            exit;
        }
        $exito = $abmUsuario->baja($datos);
        if ($exito) {
            $message = 'Eliminacion exitosa';
            header("Location: ../admin/administrarUsuarios.php?Message=" . urlencode($message));
        } else {
            $message = 'Eliminacion erronea';
            header("Location: ../admin/administrarUsuarios.php?Message=" . urlencode($message));
        }
    }
    ?>
</div>