<?php
    include 'conn.php';
    if($_COOKIE['noEmpleado'] == '' || $_COOKIE['noEmpleado'] == null){
        //echo '<script>window.location.assign("../loginMaster")</script>';
    }
?>
<style>        
    .text-bg-orange {
        --bs-bg-opacity: 1;
        background-color: #ff7300ff !important;
        color: #ffffffff !important;
    }
    .btn-logistica{
        --bs-bg-opacity: 1;
        background-color: #bf00ffff !important;
        color: #ffffffff !important;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
<!-- Sidebar - Brand -->
<a class="sidebar-brand d-flex align-items-center justify-content-center" href="index">
    <div class="sidebar-brand-icon rotate-n-1">
        <img class="sidebar-card-illustration mb-2" href="" src="img/MESS_07_CuboMess_2.png" width="40" alt="Logo">
    </div>
</a>
<!-- Heading -->
<div class="sidebar-heading">
    <span class="badge text-xl-white">Opciones</span>
</div>
<!-- Divider -->
<hr class="sidebar-divider my-2 alert-light">

<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseActivos">
        <i class="fas fa-fw fa-list  text-gray-400"></i>
        <span>Registro Activos</span>
    </a>
    <div id="collapseActivos" class="collapse" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="nuevoActivo">Regisrar Activo</a>
            <a class="collapse-item" href="verActivos">Ver Activos</a>
        </div>
    </div>
</li>
<hr class="sidebar-divider my-2 alert-light">
<li class="nav-item" id="navRentas" name="navRentas">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRentas">
        <i class="fas fa-fw fa-retweet  text-gray-400"></i>
        <span>Renta/Prest Activos</span>
    </a>
    <div id="collapseRentas" class="collapse" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="rentaPrestamo">Nueva movimiento</a>
            <a class="collapse-item" href="administrar_prestRenta">Administrar Prest/Renta</a>
        </div>
    </div>
</li>

<hr class="sidebar-divider my-0 alert-light">
<li class = "nav-item">
    <a class = "nav-link" href = "#" data-toggle = "modal" data-target = "#logoutModalN">
        <i class = "fas fa-sign-out-alt"></i>
        Salir
    </a>
</li>

<hr class="sidebar-divider my-1 alert-light">

<div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button> 
</div>
</ul>

<script src="../loginMaster/funcionesGlobales.js"></script>
<script>
    async function verificarAccesoRentas() {
        // 1. Agregamos await para esperar la respuesta
        const respuesta = await validaOpciones('activos', 'rentas');
        
        // 2. Accedemos a la estructura correcta según tu PHP: respuesta.data[0].cuantos
        const cuantos = (respuesta && respuesta.status === 'success') 
                        ? parseInt(respuesta.data[0].cuantos) 
                        : 0;
alert(cuantos);
        if (cuantos <= 0) {            
            $('#navRentas').hide(); // Oculta el elemento si no tiene acceso            
        }else {
            $('#navRentas').show(); // Muestra el elemento si tiene acceso  

        }
    }

    $(document).ready(function() {        
        verificarAccesoRentas();        
    });
</script>