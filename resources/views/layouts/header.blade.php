<!-- resources/views/layouts/header.blade.php -->

<nav class="navbar navbar-expand-lg navbar-dark bg-brown shadow">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="{{ asset('images/finca_logo.png') }}" alt="Logo" height="50">
            Finca Jiménez
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link text-white"  href="#welcome">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#about">Nosotros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#footer">Contacto</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white"  href="{{ url('admin/login') }}">Login</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    .bg-brown {
        background-color: #5C3D2E;
    }
</style>
