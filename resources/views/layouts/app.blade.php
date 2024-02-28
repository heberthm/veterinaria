<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/favicon2.ico') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"
          integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog=="
          crossorigin="anonymous"/>


       


        <!--  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script> -->


        


    <!-- DataTable css -->
    <link href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap4.min.css" rel="stylesheet">



<!-- Font-awesome css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" />



<!-- Datatable Jquery -->
<link href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.dataTables.min.css" rel="stylesheet" />

<link href="{{asset('css/buttons.dataTables2.2.3.min.css')}}" rel="stylesheet" />


<!-- Select2 css -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css" rel="stylesheet" />


<!-- SweetAlert2 -->
<link href="<link rel=" stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css" rel="stylesheet" />


<!-- Fullcalendar 3.10.2 css -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@3.10.2/dist/fullcalendar.min.css' rel='stylesheet' />
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@3.10.2/dist/fullcalendar.print.css' rel='stylesheet' media='print' />


<!-- Toastr css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />



<!-- DateTimePicker css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" />


<!-- x-editable-bs4 css -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/x-editable-bs4@1.5.5/dist/bootstrap4-editable/css/bootstrap-editable.css">

<link href="{{asset('css/bootstrap-editable.css')}}" rel="stylesheet"/> 


<!-- SweetAlert2 -->
<link href="<link rel=" stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css" rel="stylesheet" />


<!-- <link href="{{asset('css/select2.min.css')}}" rel="stylesheet"/> ->

<!-- tribute js-->
<link href="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.min.css" rel="stylesheet">
</link>



<!-- Fullcalendar 3.10.2 css -->
<!-- <link href='https://cdn.jsdelivr.net/npm/fullcalendar@3.10.2/dist/fullcalendar.min.css' rel='stylesheet' /> -->
<!-- <link href='https://cdn.jsdelivr.net/npm/fullcalendar@3.10.2/dist/fullcalendar.print.css' rel='stylesheet' media='print' /> -->


<!-- DataPicker Bootstrap js -->
<link href="{{asset('css/bootstrap-datepicker1.9.0.css')}}" rel="stylesheet" />


<!-- Toastr css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<!-- <link href="{{asset('css/toastr2.1.3.min.css')}}" rel="stylesheet"/> -->




<!-- bootstrap button-toggle 3.6 -->
<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
<!-- <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet"> -->





<!-- =============================

ARCHIVOS JS

================================== -->


<!-- Jquery 3.5 js -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js'></script>

<!-- Jquery JSON 2.6 -->
<script src= 'https://cdnjs.cloudflare.com/ajax/libs/jquery-json/2.6.0/jquery.json.min.js'></script>

<!-- Select2 js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/es.min.js"></script>


<!-- popper 2.11.2 js -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.2/umd/popper.min.js'></script>


<!-- SweetAlert2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>

<!-- Font-awesome css -->
<script src="https://kit.fontawesome.com/d57c775029.js" crossorigin="anonymous"></script>


<!--  tippy js -->
<script src="https://cdn.jsdelivr.net/npm/tippy.js@6.3.7/dist/tippy.cjs.min.js"></script>


<!-- DataTable js -->
<script src='https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js' defer></script>




<script src='https://cdn.datatables.net/fixedheader/3.2.4/js/dataTables.fixedHeader.min.js' defer></script>

<script src='https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js' defer></script>

<script src='https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js' defer></script>




<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.js" defer></script>

<script src="{{asset('js/dataTables.buttons1.2.4.js')}}" defer></script>

<script src="{{asset('js/buttons.print1.2.4.js')}}" defer></script>


<!-- highcharts js -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>


<!-- Tribute js -->
<script src="https://cdn.jsdelivr.net/npm/tributejs@5.1.3/dist/tribute.min.js"></script>

<!-- bootstrap-show-password.min.jst -->
<script src="https://unpkg.com/bootstrap-show-password@1.2.1/dist/bootstrap-show-password.min.js"></script>


<!-- jquery validate -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2/dist/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2/dist/additional-methods.min.js"></script>


<!-- bootstrap buttom-toggle 3.6 -->
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
<!-- <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js" defer></script> -->



<!--If Print button or buttons:true, this is required for any Buttons or Dom items to appear -->
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.js" defer></script>
<script src="{{asset('js/buttons.print1.2.4.js')}}"></script>


<!--Required for Excel button to appear-->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.js" defer></script>
<script src="{{asset('js/jszip2.5.0.js')}}"></script>


<!--Required for PDF button to appear-->
<script type="text/javascript" src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.js"></script>

<script src="{{asset('js/pdfmake0.1.18.js')}}"></script>



<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script> -->

<script src="{{asset('js/vfs_fonts0.1.36.js')}}"></script>


    
<!-- x-editable-bs4 js -->
<script src="{{asset('js/bootstrap-editable.min.js')}}" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js" ></script>


<!-- Font-awesome js -->
<!-- <script src="https://kit.fontawesome.com/d57c775029.js" crossorigin="anonymous"></script> -->

<script src="{{asset('js/kit.fontawesome-d57c775029.js')}}" defer></script>


<!-- SweetAlert2 -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script> -->

<script src="{{asset('js/sweetalert2.all.min.js')}}" defer></script>




<!-- Fullcalendar 3.10 js -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@3.10.2/dist/fullcalendar.min.js' defer></script>



<!-- Moment 2.24 js -->
<script src='https://cdn.jsdelivr.net/npm/moment@2.24.0/min/moment.min.js'></script>

<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale-all.min.js' defer></script>


<!-- DateTimePicker js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js" defer></script>



<!-- Tidio js -->
<!-- <script src="//code.tidio.co/zffhz4jdjbaro6re8swf82onrcpo6ojy.js" async></script> -->

<!-- Jquery countdowm -->
<script src="//cdn.rawgit.com/hilios/jQuery.countdown/2.2.0/dist/jquery.countdown.min.js"></script>


<!--  Toastr js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>








    @vite('resources/css/app.css') 

 <!-- <link href="/css/app.css" rel="stylesheet">  -->
   
    

</head>


    
<body class="hold-transition sidebar-mini layout-fixed">

<div class="loader-page"></div>


<div class="wrapper">
    <!-- Main Header -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>

            <!-- SELECT DE BUSQUEDAD EN BARRA DE MENU  -->




        </ul>



        <ul class="navbar-nav ml-auto">

            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-danger navbar-badge">15</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
                    <span class="dropdown-item dropdown-header">15 Notifications</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-envelope mr-2"></i> 4 new messages
                        <span class="float-right text-muted text-sm">3 mins</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-users mr-2"></i> 8 friend requests
                        <span class="float-right text-muted text-sm">12 hours</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-file mr-2"></i> 3 new reports
                        <span class="float-right text-muted text-sm">2 days</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                </div>
            </li>

            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">

                    <span class="d-none d-md-inline">hola, {{ Auth::check() ? Auth::user()->name : null }}</span> &nbsp;
                    <img src="../img/avatar4.png" class="user-image img-circle elevation-2" alt="User Image">
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <!-- User image -->
                    <li class="user-header bg-primary">
                        <img src="../img/avatar2.png" class="img-circle elevation-2" alt="User Image">
                        <p>
                            {{ Auth::check() ? Auth::user()->name : null }}
                            <small>Registrado desde: {{ Auth::check() ? Auth::user()->created_at->format('M. Y') : null }}</small>
                        </p>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <a href="#" class="btn btn-default btn-flat">Perfil</a>
                        <a href="#" class="btn btn-default btn-flat float-right" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Salir
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>


    
    <!-- Left side column. contains the logo and sidebar -->
@include('layouts.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content">
            @yield('content')
        </section>
    </div>

    <!-- Main Footer -->
    <footer class="main-footer">
        <div class="float-right d-none d-sm-block">
            <b>Version</b> 1.0
        </div>
        <strong>Copyright &copy; 2020 - <?php echo date("Y") ?> <a href="#">hemTech</a>.</strong> Todos los derechso reservados.
    </footer>
</div>

   


 @vite('resources/js/app.js') 

@stack('third_party_scripts')

@stack('page_scripts')


</body>
</html>
