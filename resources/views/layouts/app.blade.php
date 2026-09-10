@extends('adminlte::page')

{{-- Extend and customize the browser title --}}

@section('title')
    {{ config('adminlte.title') }}
    @hasSection('subtitle')
        | @yield('subtitle')
    @endif
@stop


{{-- Extend and customize the page content header --}}

@section('content_header')
    @hasSection('content_header_title')
        <h1 class="text-muted">
            @yield('content_header_title')

            @hasSection('content_header_subtitle')
                <small class="text-dark">
                    <i class="fas fa-xs fa-angle-right text-muted"></i>
                    @yield('content_header_subtitle')
                </small>
            @endif
        </h1>
    @endif
@stop

 
{{-- Rename section content to content_body --}}

@section('content')
    @yield('content_body')
    @stack('js')
@stop

{{-- Create a common footer --}}

@section('footer')
    <div class="float-right">
        Version: {{ config('app.version', '1.0.0') }}
    </div>

    <strong>
        Copyright &copy; 2023 - <?php echo date('Y'); ?> <a href="#">hemTech</a>. Todos los derechos reservados.

    </strong>
@stop

{{-- Add common Javascript/Jquery code --}}




@push('js')

 @include('adminlte::plugins', ['type' => 'js'])
    @yield('js')

 <script src="{{ asset('js/vetcloud.js') }}"></script>
    <script>
        

    </script>
@endpush

{{-- Add common CSS customizations --}}

@push('css')

<!-- CSS propio de VetCloud (pixel-match con el diseño) -->
    <link rel="stylesheet" href="{{ asset('css/vetcloud.css') }}">

  @include('adminlte::plugins', ['type' => 'css'])
  @yield('css')


    <style type="text/css">
        {{-- You can add AdminLTE customizations here --}}      

               
        /*
                .card-header {
                    border-bottom: none;
                }
                .card-title {
                    font-weight: 600;
                }
                */
    </style>
@endpush

