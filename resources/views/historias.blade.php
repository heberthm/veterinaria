@extends('layouts.app')

@section('titulo', 'Historias Clínicas - VetFlow')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-notes-medical mr-2"></i>Historias Clínicas
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Historias Clínicas</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-2"></i>Listado de Historias Clínicas
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('mascotas') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-paw"></i> Ver Mascotas
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($mascotas) && $mascotas->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="tabla-historias">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Mascota</th>
                                        <th>Propietario</th>
                                        <th>Especie</th>
                                        <th>Última Consulta</th>
                                        <th>Total Consultas</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mascotas as $mascota)
                                    <tr>
                                        <td>{{ $mascota->id }}</td>
                                        <td>
                                            <strong>{{ $mascota->nombre }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $mascota->raza ?? 'Sin raza' }}</small>
                                        </td>
                                        <td>
                                            @if($mascota->cliente)
                                                {{ $mascota->cliente->nombreCompleto() }}
                                            @else
                                                <span class="text-danger">Sin asignar</span>
                                            @endif
                                        </td>
                                        <td>{{ ucfirst($mascota->especie) }}</td>
                                        <td>
                                            @if($mascota->historiasClinicas && $mascota->historiasClinicas->isNotEmpty())
                                                {{ $mascota->historiasClinicas->first()->fecha_consulta->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted">Sin consultas</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info">
                                                {{ $mascota->historiasClinicas ? $mascota->historiasClinicas->count() : 0 }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('historias.show', $mascota->id) }}" class="btn btn-primary btn-accion" title="Ver Historia Clínica">
                                                    <i class="fas fa-notes-medical"></i>
                                                </a>
                                                <a href="{{ route('historias.crear', $mascota->id) }}" class="btn btn-success btn-accion" title="Nueva Consulta">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No hay mascotas registradas.
                            <a href="{{ route('mascotas.store') }}" class="alert-link">Registrar una mascota</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    @if(isset($mascotas) && $mascotas->isNotEmpty())
    $('#tabla-historias').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        responsive: true,
        pageLength: 10,
        order: [[0, 'desc']]
    });
    @endif
});
</script>
@stop