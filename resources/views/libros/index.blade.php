<!-- resources/views/libros/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Libros</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearLibroModal">
            Nuevo Libro
        </button>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="filtro-titulo" class="form-control" placeholder="Buscar por título...">
            </div>
            <div class="col-md-3">
                <select id="filtro-anio" class="form-control">
                    <option value="">Todos los años</option>
                    @for($i = date('Y'); $i >= 1900; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <select id="filtro-autor" class="form-control">
                    <option value="">Todos los autores</option>
                    <!-- Se cargarán dinámicamente -->
                </select>
            </div>
            <div class="col-md-2">
                <button id="btn-filtrar" class="btn btn-secondary w-100">Filtrar</button>
            </div>
        </div>
        
        <table id="tabla-libros" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>ISBN</th>
                    <th>Año</th>
                    <th>Autores</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Se cargará dinámicamente -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para crear libro -->
<div class="modal fade" id="crearLibroModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Libro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-crear-libro">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Título *</label>
                                <input type="text" name="titulo" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ISBN *</label>
                                <input type="text" name="isbn" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Año Publicación</label>
                                <input type="number" name="anio_publicacion" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Número de Páginas</label>
                                <input type="number" name="numero_paginas" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Stock Disponible *</label>
                                <input type="number" name="stock_disponible" class="form-control" required min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Autores</label>
                        <div id="autores-container">
                            <!-- Se cargarán dinámicamente -->
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="agregarAutor()">
                            + Agregar Autor
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const API_URL = 'http://localhost:8000/api';
    let token = localStorage.getItem('token');
    let autoresDisponibles = [];
    
    $.ajaxSetup({
        headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json'
        }
    });
    
    // Cargar autores para filtros y modal
    function cargarAutores() {
        $.ajax({
            url: `${API_URL}/autores`, // Debes crear este endpoint
            method: 'GET',
            success: function(data) {
                autoresDisponibles = data;
                let options = '<option value="">Todos los autores</option>';
                data.forEach(autor => {
                    options += `<option value="${autor.id}">${autor.nombre} ${autor.apellido}</option>`;
                });
                $('#filtro-autor').html(options);
                
                // Cargar autores en modal
                cargarAutoresEnModal();
            }
        });
    }
    
    function cargarAutoresEnModal() {
        let html = `
        <div class="autor-item mb-2">
            <div class="row">
                <div class="col-md-8">
                    <select name="autores[]" class="form-control autor-select">
                        <option value="">Seleccionar autor</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="orden_autor[]" class="form-control" placeholder="Orden" min="1">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removerAutor(this)">×</button>
                </div>
            </div>
        </div>`;
        
        $('#autores-container').html(html);
        
        // Llenar select de autores
        $('.autor-select').each(function() {
            let selectHtml = '<option value="">Seleccionar autor</option>';
            autoresDisponibles.forEach(autor => {
                selectHtml += `<option value="${autor.id}">${autor.nombre} ${autor.apellido}</option>`;
            });
            $(this).html(selectHtml);
        });
    }
    
    window.agregarAutor = function() {
        let selectHtml = '<option value="">Seleccionar autor</option>';
        autoresDisponibles.forEach(autor => {
            selectHtml += `<option value="${autor.id}">${autor.nombre} ${autor.apellido}</option>`;
        });
        
        let html = `
        <div class="autor-item mb-2">
            <div class="row">
                <div class="col-md-8">
                    <select name="autores[]" class="form-control autor-select">
                        ${selectHtml}
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="orden_autor[]" class="form-control" placeholder="Orden" min="1">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removerAutor(this)">×</button>
                </div>
            </div>
        </div>`;
        
        $('#autores-container').append(html);
    }
    
    window.removerAutor = function(button) {
        if ($('.autor-item').length > 1) {
            $(button).closest('.autor-item').remove();
        }
    }
    
    // Cargar libros
    function cargarLibros(filtros = {}) {
        $.ajax({
            url: `${API_URL}/libros`,
            method: 'GET',
            data: filtros,
            success: function(response) {
                const libros = response.data || response;
                let html = '';
                
                libros.forEach(libro => {
                    const autores = libro.autores ? libro.autores.map(a => 
                        `${a.nombre} ${a.apellido}`).join(', ') : '';
                    
                    html += `
                    <tr>
                        <td>${libro.id}</td>
                        <td>${libro.titulo}</td>
                        <td>${libro.isbn}</td>
                        <td>${libro.anio_publicacion || ''}</td>
                        <td>${autores}</td>
                        <td>
                            <span class="badge ${libro.stock_disponible > 0 ? 'bg-success' : 'bg-danger'}">
                                ${libro.stock_disponible}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="verLibro(${libro.id})">
                                Ver
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="editarLibro(${libro.id})">
                                Editar
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarLibro(${libro.id})">
                                Eliminar
                            </button>
                        </td>
                    </tr>`;
                });
                
                $('#tabla-libros tbody').html(html || '<tr><td colspan="7">No hay libros</td></tr>');
            }
        });
    }
    
    // Filtrar libros
    $('#btn-filtrar').click(function() {
        const filtros = {
            titulo: $('#filtro-titulo').val(),
            autor: $('#filtro-autor').val(),
            anio: $('#filtro-anio').val()
        };
        cargarLibros(filtros);
    });
    
    // Crear libro
    $('#form-crear-libro').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {
            titulo: formData.get('titulo'),
            isbn: formData.get('isbn'),
            anio_publicacion: formData.get('anio_publicacion'),
            numero_paginas: formData.get('numero_paginas'),
            descripcion: formData.get('descripcion'),
            stock_disponible: formData.get('stock_disponible'),
            autores: []
        };
        
        // Recopilar autores
        $('.autor-item').each(function(index) {
            const autorId = $(this).find('.autor-select').val();
            const orden = $(this).find('input[name="orden_autor[]"]').val() || (index + 1);
            if (autorId) {
                data.autores.push(autorId);
            }
        });
        
        $.ajax({
            url: `${API_URL}/libros`,
            method: 'POST',
            data: data,
            success: function(response) {
                alert('Libro creado exitosamente');
                $('#crearLibroModal').modal('hide');
                $('#form-crear-libro')[0].reset();
                cargarLibros();
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors || {};
                let errorMsg = 'Error al crear libro:\n';
                for (const field in errors) {
                    errorMsg += `- ${errors[field].join(', ')}\n`;
                }
                alert(errorMsg);
            }
        });
    });
    
    // Inicializar
    cargarAutores();
    cargarLibros();
    
    // Recargar cada 60 segundos
    setInterval(() => cargarLibros(), 60000);
});

// Funciones globales
window.verLibro = function(id) {
    window.location.href = `/libros/${id}`;
}

window.editarLibro = function(id) {
    // Implementar edición
    alert('Editar libro ' + id);
}

window.eliminarLibro = function(id) {
    if (confirm('¿Está seguro de eliminar este libro?')) {
        $.ajax({
            url: `${API_URL}/libros/${id}`,
            method: 'DELETE',
            success: function() {
                alert('Libro eliminado exitosamente');
                location.reload();
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Error al eliminar libro');
            }
        });
    }
}
</script>
@endsection