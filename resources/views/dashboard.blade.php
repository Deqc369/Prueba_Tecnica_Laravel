<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">Total Libros</h5>
                <h2 id="total-libros" class="card-text">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Préstamos Activos</h5>
                <h2 id="prestamos-activos" class="card-text">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title">Préstamos Vencidos</h5>
                <h2 id="prestamos-vencidos" class="card-text">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card bg-danger text-white">
            <div class="card-body">
                <h5 class="card-title">Libros sin Stock</h5>
                <h2 id="libros-sin-stock" class="card-text">0</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Libros Disponibles</h5>
            </div>
            <div class="card-body">
                <div class="search-box">
                    <input type="text" id="search-libros" class="form-control" placeholder="Buscar libros...">
                </div>
                <div id="libros-container">
                    <!-- Libros cargados dinámicamente -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Nuevo Préstamo</h5>
            </div>
            <div class="card-body">
                <form id="form-prestamo">
                    @csrf
                    <div class="mb-3">
                        <label for="usuario_id" class="form-label">Usuario</label>
                        <select id="usuario_id" class="form-control" required>
                            <option value="">Seleccionar usuario</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="libro_id" class="form-label">Libro</label>
                        <select id="libro_id" class="form-control" required>
                            <option value="">Seleccionar libro</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Crear Préstamo</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const API_URL = 'http://localhost:8000/api';
    let token = localStorage.getItem('token');
    
    // Configurar headers para todas las peticiones
    $.ajaxSetup({
        headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        }
    });
    
    // Cargar estadísticas
    function cargarEstadisticas() {
        $.ajax({
            url: `${API_URL}/libros`,
            method: 'GET',
            success: function(data) {
                const totalLibros = data.total || data.data.length;
                $('#total-libros').text(totalLibros);
                
                // Contar libros sin stock
                const sinStock = data.data.filter(libro => libro.stock_disponible === 0).length;
                $('#libros-sin-stock').text(sinStock);
            }
        });
        
        $.ajax({
            url: `${API_URL}/prestamos`,
            method: 'GET',
            success: function(data) {
                const prestamosActivos = data.data.filter(p => p.estado === 'activo').length;
                const prestamosVencidos = data.data.filter(p => p.estado === 'vencido').length;
                
                $('#prestamos-activos').text(prestamosActivos);
                $('#prestamos-vencidos').text(prestamosVencidos);
            }
        });
    }
    
    // Cargar libros
    function cargarLibros(search = '') {
        $.ajax({
            url: `${API_URL}/libros`,
            method: 'GET',
            data: { titulo: search },
            success: function(data) {
                let html = '';
                const libros = data.data || data;
                
                libros.forEach(libro => {
                    if (libro.stock_disponible > 0) {
                        html += `
                        <div class="card mb-2">
                            <div class="card-body">
                                <h6 class="card-title">${libro.titulo}</h6>
                                <p class="card-text">
                                    <small>ISBN: ${libro.isbn}</small><br>
                                    <small>Stock: ${libro.stock_disponible}</small>
                                </p>
                            </div>
                        </div>`;
                    }
                });
                
                $('#libros-container').html(html || '<p>No hay libros disponibles</p>');
            }
        });
    }
    
    // Cargar usuarios para el select
    function cargarUsuarios() {
        $.ajax({
            url: `${API_URL}/usuarios`, // Debes crear este endpoint
            method: 'GET',
            success: function(data) {
                let options = '<option value="">Seleccionar usuario</option>';
                data.forEach(usuario => {
                    if (usuario.estado === 'activo') {
                        options += `<option value="${usuario.id}">${usuario.nombre} (${usuario.email})</option>`;
                    }
                });
                $('#usuario_id').html(options);
            }
        });
    }
    
    // Cargar libros para el select
    function cargarLibrosSelect() {
        $.ajax({
            url: `${API_URL}/libros`,
            method: 'GET',
            success: function(data) {
                let options = '<option value="">Seleccionar libro</option>';
                const libros = data.data || data;
                
                libros.forEach(libro => {
                    if (libro.stock_disponible > 0) {
                        options += `<option value="${libro.id}">${libro.titulo} (Stock: ${libro.stock_disponible})</option>`;
                    }
                });
                $('#libro_id').html(options);
            }
        });
    }
    
    // Enviar préstamo
    $('#form-prestamo').submit(function(e) {
        e.preventDefault();
        
        const prestamoData = {
            usuario_id: $('#usuario_id').val(),
            libro_id: $('#libro_id').val()
        };
        
        if (!prestamoData.usuario_id || !prestamoData.libro_id) {
            alert('Por favor complete todos los campos');
            return;
        }
        
        $.ajax({
            url: `${API_URL}/prestamos`,
            method: 'POST',
            data: prestamoData,
            success: function(response) {
                alert('Préstamo creado exitosamente');
                $('#form-prestamo')[0].reset();
                cargarEstadisticas();
                cargarLibros();
                cargarLibrosSelect();
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'Error al crear préstamo';
                alert(error);
            }
        });
    });
    
    // Búsqueda en tiempo real
    $('#search-libros').on('input', function() {
        cargarLibros($(this).val());
    });
    
    // Inicializar
    cargarEstadisticas();
    cargarLibros();
    cargarUsuarios();
    cargarLibrosSelect();
    
    // Actualizar cada 30 segundos
    setInterval(cargarEstadisticas, 30000);
});
</script>
@endsection