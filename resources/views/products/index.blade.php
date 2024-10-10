@extends('layout')

@section('html_title', __('Gestión de productos'))

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container">
    <h1>Gestión de Productos</h1>
    @if ($is_authenticated)
        <button class="btn btn-success create-button">Crear Producto</button>
    @else
        <button class="btn btn-success login-button">Iniciar sesión</button>
    @endif 
    <form method="POST" action="{{route('product.list-ajax')}}" id="product-list-ajax-form">
        @csrf
        <input type="hidden" name="page" id="page-input" value="1" />
        <input type="text" id="title" name="title" placeholder="Título" maxlength="255" pattern=".{1,255}">
        <input type="number" id="price" name="price" placeholder="Precio"  min="1" step="any">
        <input type="date" id="created_at" name="created_at" placeholder="Fecha de alta">
        <button type="submit">Filtrar</button>
    </form>
    <div id="productContainer">
        <div id="ajaxLoading"><span class="material-symbols-outlined">sync</span></div>
        <div id="productContainerHtml"></div>
    </div>
    @if ($is_authenticated)
        <button class="btn btn-danger logout-button">Cerrar sesión</button>
    @endif 
</div>

<div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Nuevo Producto</h2>
        <form id="createProductForm">
            @csrf
            <input type="text" id="newTitle" name="title" placeholder="Título" maxlength="255" pattern=".{1,255}" required>
            <input type="number" id="newPrice" name="price" placeholder="Precio" min="1" step="any" required>
            <button type="submit">Crear Producto</button>
        </form>
    </div>
</div>

<div id="editProductModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Editar producto</h2>
        <form id="editProductForm">
            @csrf
            <input type="hidden" id="editProductId" name="product_id">
            <input type="text" id="editTitle" name="title" placeholder="Título" maxlength="255" pattern=".{1,255}" required>
            <input type="number" id="editPrice" name="price" placeholder="Precio" min="1" step="any" required>
            <button type="submit">Editar Producto</button>
        </form>
    </div>
</div>

<div id="deleteProductModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Eliminar producto</h2>
        <form id="deleteProductForm">
            <input type="hidden" id="deleteProductId" name="product_id">
            <p>¿Seguro desea realizar esta acción? </p>
            <button type="submit">Confirmar</button>
        </form>
    </div>
</div>

<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Iniciar sesión</h2>
        <form method="POST" action="{{route('login')}}" id="loginForm">
            @csrf
            <input type="password" placeholder="Ingrese la contraseña de administración" id="password" name="password" required>
            <button type="submit">Iniciar sesión</button>
        </form>
    </div>
</div>

<div id="logoutModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Cerrar sesión</h2>
        <form method="POST" action="{{route('logout')}}" id="logoutForm">
            @csrf
            <p>¿Seguro desea cerran la sesión?</p>
            <button type="submit">Confirmar</button>
        </form>
    </div>
</div>


@endsection
@section('js_files')
    <script src="{{ asset('js/product.js') }}"></script>
@endsection

@section('js_block')

@parent

<script>
    $(function() {

        "use strict";

        $('#product-list-ajax-form').xhrIndex({
            responseContainer: $('#productContainerHtml'),
            pageInput: $('#page-input')
        });

    });
</script>
@endsection