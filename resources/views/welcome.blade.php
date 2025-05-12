@extends('layouts.app')

@section('title', 'Bienvenido a Finca Jiménez')

@section('content')
<div id="welcome"  class="hero-section">
    <div class="overlay"></div>
    <div class="container text-center text-white">
        <h1 class="display-4 fw-bold">Bienvenido a Finca Jiménez</h1>
        <p class="lead">Cacao de calidad, tradición y pasión</p>
        <a href="#about" class="btn btn-warning btn-lg mt-3">Conócenos</a>
    </div>
</div>

<section id="about" class="py-5 bg-light">
    <div class="container text-center">
        <h2 class="mb-4">Sobre Nosotros</h2>
        <p class="lead">Somos una finca dedicada al cultivo y producción de cacao de alta calidad, combinando tradición con innovación.
            Contamos con una extecion de 5 hectarias en el K48 canto El Triunfo-Guayas, donde cultivamos con dedicación y pasión.
        </p>
        <img src="{{ asset('images/welcome/tree.jpg') }}" class="img-fluid rounded shadow" alt="Campo de cacao">
    </div>
</section>

<section class="py-5">
    <div class="container text-center">
        <h2 class="mb-4">Nuestros Productos</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow">
                    <img src="{{ asset('images/welcome/bean.jpg') }}" class="card-img-top" alt="Cacao en granos">
                    <div class="card-body">
                        <h5 class="card-title">Cacao en Grano</h5>
                        <p class="card-text">Seleccionado cuidadosamente para garantizar la mejor calidad.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow">
                    <img src="{{ asset('images/welcome/fruit.jpg') }}" class="card-img-top" alt="mejores practicas">
                    <div class="card-body">
                        <h5 class="card-title">Las mejores practicas</h5>
                        <p class="card-text">Las practicas son importante para mantener la calidad y la sostenibilidad.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow">
                    <img src="{{ asset('images/welcome/cocoa1.png') }}" class="card-img-top" alt="Chocolate artesanal">
                    <div class="card-body">
                        <h5 class="card-title">El mejor aroma</h5>
                        <p class="card-text">El aroma es el reflejo de la calidad y la pasión que nos caracteriza. </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .hero-section {
        background: url("{{ asset('images/welcome/cocaback.jpg') }}") no-repeat center center/cover;
        height: 100vh;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5);
    }
    .hero-section .container {
        position: relative;
        z-index: 2;
    }
</style>
@endsection
