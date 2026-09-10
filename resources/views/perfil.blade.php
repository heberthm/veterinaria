@extends('adminlte::page')

@section('title', 'Mi Perfil')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                <i class="fas fa-user mr-2"></i>Mi Perfil
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Perfil</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-3">
        <!-- Widget de perfil -->
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    @if(isset($user->avatar) && $user->avatar)
                        <img class="profile-user-img img-fluid img-circle"
                             src="{{ asset('storage/' . $user->avatar) }}"
                             alt="{{ $user->name }}">
                    @else
                        <img class="profile-user-img img-fluid img-circle"
                             src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4F46E5&color=fff&size=128"
                             alt="{{ $user->name }}">
                    @endif
                </div>

                <h3 class="profile-username text-center">{{ $user->name }}</h3>

                <p class="text-muted text-center">{{ $user->rol ?? 'Usuario' }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <b>Email</b>
                        <span>{{ $user->email }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <b>Rol</b>
                        <span class="badge badge-primary">{{ $user->rol ?? 'Usuario' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <b>Miembro desde</b>
                        <span>{{ $user->created_at->format('d/m/Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <b>Estado</b>
                        <span class="badge badge-{{ $user->is_active ? 'success' : 'danger' }}">
                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </li>
                </ul>

                <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-block">
                    <i class="fas fa-edit"></i> Editar Perfil
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle mr-1"></i> Información del Perfil
                </h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Aquí puedes ver y editar tu información personal.
                </div>

                <dl class="row">
                    <dt class="col-sm-3">Nombre completo</dt>
                    <dd class="col-sm-9">{{ $user->name }}</dd>

                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9">{{ $user->email }}</dd>

                    <dt class="col-sm-3">Rol</dt>
                    <dd class="col-sm-9">
                        <span class="badge badge-primary">{{ $user->rol ?? 'Usuario' }}</span>
                    </dd>

                    <dt class="col-sm-3">Estado</dt>
                    <dd class="col-sm-9">
                        <span class="badge badge-{{ $user->is_active ? 'success' : 'danger' }}">
                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </dd>

                    <dt class="col-sm-3">Registrado</dt>
                    <dd class="col-sm-9">{{ $user->created_at->format('d/m/Y H:i') }}</dd>

                    <dt class="col-sm-3">Último acceso</dt>
                    <dd class="col-sm-9">{{ $user->updated_at->format('d/m/Y H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@stop