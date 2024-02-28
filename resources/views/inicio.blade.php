@extends('layouts.app')


@section('content')

<style>
  .calendar {

    max-width: auto;
    margin: 0, 0;
    padding: 0, 0;
    cursor: pointer;

  }

  .calendar {
    cursor: pointer;
  }

  .has-error {
    border-color: #cc0000;
    background-color: #ffff99;
  }

  .table.dataTable {
    font-family: Verdana, Geneva, Tahoma, sans-serif;

  }

  .alert-message {
    color: red;
  }

  #loading {
    display: none;
    position: absolute;
    top: 50%;
    left: 40%;
    z-index: 1000;
  }


  .mostrar_inputs {
    display: none;
  }

  #loading2 {
    display: none;
    position: absolute;
    top: 50%;
    left: 40%;
    z-index: 1100;
  }

  .listado_citas {
    height: 300px;
    overflow-y: scroll;
    overflow-x: hidden;
  }


  .popover {
    pointer-events: none;
  }

  .tribute-container li {
    padding: 5px 5px;
    cursor: pointer;
    background: #fff;
    font-size: 0.8rem;
  }

  .tribute-container li.highlight {
    background: #fff;
    font-size: 0.8rem;
    font-weight: bold;
  }

  .tribute-container {
    position: absolute;
    top: 0;
    left: 0;
    height: auto;
    max-height: 136px;
    max-width: 500px;
    overflow: auto;
    display: block;
    z-index: 999999;
    padding-top: 10px;
  }


  .modal.modal-fullscreen .modal-dialog {

    width: 100vw;

    height: 100vh;

    margin: 0;

    padding: 0;

    max-width: none;
  }

  .modal.modal-fullscreen .modal-content {

    height: auto;

    height: 100vh;

    border-radius: 0;
    border: none;
  }


  .modal.modal-fullscreen .modal-body {

    overflow-y: auto;
  }
</style>


<br>




<!-- =============================

BUSCADOR DE CLIENTES - SELECT2

================================== -->


<div class="container-fluid">
  <div class="row">
    <!-- Left col -->
    <section class="col-lg-7">
      <!-- Custom tabs (Charts with tabs)-->
      <div class="card card-light">
        <div class="card-header">


          <h3 class="card-title"><span style="color: #28a745;" class="fas fa-search mr-3"></span>Buscador</h3>

          <span class="btn-group float-right" id="btn_listadoClientes">

            <!--
                        <span class="btn-group float-right" id="btn_historialIngresos">
                          <a href="#" class="mr-3" data-toggle="modal" data-target="#modalAgregarListaEspera"><i class="fas fa-plus" style="color: #1566EB;" 
                          title="Agregrar cliente a lista de espera" ></i></a>
                        </span>   
                        -->
            <a href="#" class="btn btn-transparent dropdown-toggle p-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="fa fa-list"></span><span style="color:#212529;"></span>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <a class="dropdown-item" data-target="#modalVerListadoClientes" data-toggle="modal" class="MainNavText" id="MainNavHelp" href="#modalVerListadoClientes">Ver listado de clientes</a>
            </div>
          </span>

          <!--
                     <button type="button" class="btn btn-sm btn-primary mt-3 mr-2" style="position: absolute; top: 0; left: 260px">
                     Ver hospitalizados
                    </button>

                    <button type="button" class="btn btn-sm btn-primary mt-3 mr-2" style="position: absolute; top: 0; left: 400px">
                     Ver en seguimiento
                    </button>

                     -->

          <!--

                    <span class="btn-group float-right" id="btn-group-buscar">
                        <a href="#" class="btn dropdown-toggle p-0" data-toggle="dropdown" aria-haspopup="true"
                           aria-expanded="false">

                       

                        <span class="fa fa-list"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                          <a class="dropdown-item" href="javascript:btn_buscadorMedico();">Buscador de paciente por médico</a>
                          <a class="dropdown-item" href="javascript:btn_buscadorMascota();">Buscador de mascota</a>
                            <a class="dropdown-item" href="javascript:listado_mascotas();">Listado de pacientes</a>
                          <a class="dropdown-item" href="javascript:listadoClientes();">Listado de clientes</a>
                          <a class="dropdown-item" href="javascript:historial_registros();">Historial de registros realizados</a>
                        </div>
                    </span> 
                        
                      -->

        </div>
        <div class="card-body">
          <div class="form-group">
            <!-- <label>Buscar:</label>  -->
            <select class="livesearch form-control" id="livesearch" name="livesearch"></select>
          </div>

          <button class="btn btn-primary" data-toggle="modal" id="agregar_cliente_nuevo" data-target="#modalAgregarCliente" style="text-align:left"><span class="fa fa-user fa-fw" tabindex="3"></span> Agregar cliente nuevo</button>
        </div>
      </div>
      <!-- /.card-body -->





      <!-- ====================================

FORMULARIO RECEPCION DE PACIENTES

=========================================  -->


      <div class="card direct-chat direct-chat-primary card-light">
        <div class="card-header">



          <h3 class="card-title"><span style="color: #28a745;" class="fas fa-list mr-3">

            </span> Pacientes que cumplen años el mes de: &nbsp; </h3>

          <p><b>{{ now()->translatedFormat('F');  }}</b></p>



        </div>

        <div class="card-body">
          
          <div class="form-group">



            <!-- ==================================

DATATABLE CLIENTES QUE CUMPLEN AÑOS

====================================== -->


            <div class="row">
              <div class="col-lg-12">
                <div class="listado_citas">
                  <div class="form-group">
                    <table id="Table_listado_cumpleaños" class="table dt-responsive table-hover" style="width:100%;font-size:12.5px;">
                      <thead>
                        <tr>

                          <th>Paciente</th>
                          <th>Tel / Cel</th>
                          <th>Fecha nacimiento</th>

                          <th></th>
                        </tr>
                      </thead>

                      <tbody>

                      </tbody>


                    </table>




                  </div>
                </div>
              </div>
            </div>
          </div>


        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->

      <!-- /.card -->
    </section>
    <!-- /.Left col -->
    <!-- right col (We are only adding the ID to make the widgets sortable)-->
    <section class="col-lg-5 connectedSortable">





      <!-- =============================

CALENDAR - AGENDAR   MEDICA

================================== -->

      <div class="card" style="height:97%">

        <div class="card card-light">

          <div class="card-header">

            <h3 class="card-title"><span style="color: #28a745;" class="fas fa-calendar mr-3"></span>Agenda de citas</h3>

            <!-- Button trigger modal -->
            <button type="button" class="btn btn-success float-right" data-toggle="modal" data-target="#ModalCalendar">

              <span class="fa fa-calendar fa-fw" tabindex="3"></span> Agregar cita

            </button>

          </div>


         

          <div class="container">

          <div id="calendar"></div>

          <div id="loading" style="display: none;">

            <img id="loading-image" src="../img/loader.gif" alt="Cargando..." />

          </div>

         

         </div>

        </div>
        <!-- /.card-body -->

        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->

    </section>
    <!-- /.content -->
  </div>






  <!-- =======================

   MODAL FULLCALENDAR CITAS 

 =========================== -->

 <div class="modal" tabindex="-1" role="dialog" id="ModalCalendar">

<div class="modal-dialog" role="document">

  <div class="modal-content">

    <div class="modal-header">

      <h5 class="modal-title"><span style="color:#28a745;" class="fas fa-calendar mr-3"></span>Calendario citas</h5>

      <div class="col-6 align-items-center" style="font-size: small;">

        <div id="tituloMes">

          <h6>

            <div class="mx-8 titulo-mes" style="color:brown"></div>

          </h6>

        </div>

      </div>


      <button type="button" class="close" data-dismiss="modal" aria-label="Close">

        <span aria-hidden="true">&times;</span>

      </button>

    </div>

    <div class="modal-body">

      <div class="row">

        <div class="col-md-5">

          <input id="filtrar_calendario" name="filtrar_calendario" type="text" class="form-control form-control" placeholder="Filtrar eventos">

          <!--
            <select id="selector" class="form-control" >
              <option value="" selected="selected" >Filtrar veterinario</option>
              <option value="todos">Mostrar todos</option>
              <option value="Eduardo Correa medina">Eduardo Correa medina</option>
              <option value="Eliana Buitrago Rosales">Eliana Buitrago Rosales</option>
              <option value="David Restrepo">David Restrepo</option>
            </select>

           -->

        </div>

        <div class="col-md-5">


          <select id="selector" class="form-control" placeholder="Filtrar eventos">

            <option value="todos">Mostrar todos</option>

           
            <option value="</option>

           
          </select>



        </div>


        <div id='calendar2'></div>

        <div id="loading2" style="display:none;">

          <img id="loading-image" src="../img/loader.gif" alt="Cargando..." />

        </div>

      </div>



    </div>

    <div class="modal-footer">
      <!-- <button type="button" class="btn btn-primary">Guardar</button>  -->
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

    </div>

  </div>

</div>

</div>

</div>


<!-- ===========================================

 VENTAMA MODAL FULLCALENDAR - GUARDAR 

================================================ -->



<div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">




  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title"><span style="color:#28a745;" class="fas fa-edit mr-3"></span>Agendar citas</h5>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">


        <form method="POST" id="users_form" action="{{ url('fullcalendareventmaster/create') }}">

          <div class="row">

            <div class="col-md-6">

              <div class="form-group">

                <label for="title" class="control-label">Cliente &nbsp;


                  <span class="btn-group float-right" id="btn_historialIngresos">

                    <a href="#" class="mr-3" data-toggle="modal" data-target="#modalAgregarCliente2"><i class="fas fa-user-plus" style="color: #1566EB;" title="Agregar nuevo cliente"></i></a>

                  </span>

                </label>



                <div class="form-group">

                  <select class="livesearch2 form-control" id="livesearch2" name="nombre_cliente" style="width: 100%;"></select>

                </div>


                <input type="hidden" name="cliente" class="form-control text-capitalize" id="cliente">


                <!--
                @if ($errors->has('cliente'))
                    <small class="form-text text-danger"> {{ $errors->first('cliente') }} </small>
                @endif
   
              -->

              </div>

            </div>



            <div class="col-md-6">

              <div class="form-group">

                <label for="Telefono" class="control-label">Tel / Cel</label>

                <input type="tel" name="telefono" class="form-control text-capitalize" id="celular_cliente" required onkeypress="return isNumber(event)">

              </div>

            </div>




            <div class="col-md-6">

              <div class="form-group">

                <label for="CboMedico" class="control-label">profesional</label>


                <select name="medico" class="form-control" id="medico" required placeholder="Seleccione profesional">

                  <option value="" selected="selected" style='color: #cccccc'>Seleccionar médico</option>

                 

                  <option value=""></option>

               

                </select>



              </div>

            </div>

            <div class="col-lg-6">


              <div class="form-group">

                <label for="Servicios" class="control-label">Terapias</label>

                <input type="text" id="servicios2" name="servicios2" class="form-control" required autocomplete="off">


               

              </div>

            </div>





            <div class="col-md-2">

              <div class="form-group">

                <label for="color" class="control-label">Color</label>

                <input type="color" id="color" name="color" list="ListadoColores" value="#1560F6" class="form-control" required autocomplete="off">

              </div>

            </div>



            <div class="col-md-10">

              <div class="form-group">

                <label for="descripcion" class="control-label">Comentario</label>

                <input type="text" name="descripcion" class="form-control" id="descripcion" maxlength=150 autocomplete="off">
              </div>
            </div>

          </div>



          <div class="row">

            <div class="col-md-3">

              <div class="form-group">

                <label for="hora_ini" class="control-label">Hora inicial</label>


                <input type="text" id="start" name="start" class="form-control" required>


              </div>

            </div>



            <div class="col-md-3">

              <div class="form-group">

                <label for="hora_fin" class="control-label">Hora fin</label>

                <input type="text" name="end" class="form-control" id="end" required>

              </div>

            </div>



            <!--

            <div class="col-md-3">

              <div class="form-group">

                <label for="fecha_actual" class=" control-label">Fecha</label>

                <input type="text" name="fecha_actual" class="form-control" id="fecha_actual" readonly>

              </div>
            </div>

          </div>

        -->


            <!--    <div class="form-group">
        <label for="start" class="col-sm-2 control-label">Fecha inicial</label>
  
      -->


            <input type="hidden" id="title" name="title">

            <input type="hidden" id="color">

            <input type="hidden" name="userId" class="form-control" id="userId" value="{{ Auth::user()->id }}" readonly>




          </div>



          <!--     
            
       <div id="enlace_listado">  
                          
            <p><a href="crearClientes.php"><i class="fa fa-user fa-2x"></i>&nbsp; Crear cliente nuevo</a>   </p> 
                          
       </div>
  
   -->

          <div class="modal-footer">

            <button type="submit" id="agregar_cita" name="agregar_cita" class="btn btn-primary">Guardar</button>

            <button type="button" id="salir" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

          </div>

      </div>
    </div>
  </div>

  </form>
</div>

</div>




<!-- ===============================================

VENTANA MODAL EDITAR DATOS DEL CALENDARIO

==================================================== -->




<div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">


  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title"><span style="color:#28a745;" class="fas fa-edit mr-3"></span>Editar citas</h5>



        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">


        <form method="GET" id="editar_calendario" action="{{ url('fullcalendareventmaster/Update_event/id') }}">




          <div class="row">

            <div class="col-lg-8">

              <div class="form-group">

                <label for="title" class="control-label">Cliente</label>


                <input type="text" name="cliente" class="form-control text-capitalize" id="cliente" maxlength="35" autofocus required autocomplete="off">

              </div>

            </div>



            <div class="col-lg-4">

              <div class="form-group">

                <label for="Telefono" class="control-label">Tel / Cel</label>

                <input type="text" name="telefono" class="form-control text-capitalize" id="telefono" required onkeypress="return isNumber(event)">

              </div>

            </div>


            <div class="col-lg-6">

              <div class="form-group">

                <label for="CboMedico" class="control-label">profesional</label>


                <select name="medico" class="form-control" id="medico" required placeholder="Seleccione profesional">
                  <option value="" selected="selected" style='color: #cccccc'>Seleccionar médico</option>
               
                  <option value=""></option>
                
                </select>

              </div>
            </div>




            <div class="col-lg-6">


              <div class="form-group">

                <label for="Servicios" class="control-label">Terapias</label>


                <input type="text" id="servicios" name="servicios" value="#1560F6" class="form-control" required autocomplete="off">


           

              </div>
            </div>
          </div>


          <div class="row">

            <div class="col-lg-2">

              <div class="form-group">

                <label for="color" class="control-label">Color</label>

                <input type="color" id="color2" name="color2" list="ListadoColores" value="#1560F6" class="form-control" required autocomplete="off">


              </div>
            </div>



            <div class="col-lg-10">

              <div class="form-group">

                <label for="descripcion" class="control-label">Comentario</label>

                <input type="text" name="descripcion" class="form-control" id="descripcion" autocomplete="off">
              </div>
            </div>
          </div>


          <div class="row">

            <div class="col-md-3">

              <div class="form-group">

                <label for="hora_ini" class="control-label">Hora ini</label>

                <input type='text' class="form-control" name="hora_ini" id="hora_ini" required>


              </div>
            </div>




            <div class="col-md-3">

              <div class="form-group">

                <label for="hora_fin" class="control-label">Hora fin</label>

                <input type="text" name="hora_fin" class="form-control" id="hora_fin" required>

              </div>
            </div>



            <!--


            <div class="col-md-3">

              <div class="form-group">

                <label for="fecha_actual" class=" control-label">Fecha</label>

                <input type="text" name="fecha_actual" class="form-control" id="fecha_actual" readonly>

              </div>
            </div>

          </div>


          -->

            <!--    <div class="form-group">
<label for="start" class="col-sm-2 control-label">Fecha inicial</label>

-->
            <div class="col-sm-10">
              <input type="hidden" name="start" class="form-control" id="start" readonly>
            </div>


            <input type="hidden" name="titulo" id="titulo">


            <!-- 
<div class="form-group">
<label for="end" class="col-sm-2 control-label">Fecha final</label>

-->
            <div class="col-sm-10">
              <input type="hidden" name="end" class="form-control" id="end" readonly>
            </div>
          </div>


          <input type="hidden" name="id" class="form-control" id="id" readonly>



          <input type="hidden" name="userId" class="form-control" id="userId" value="{{ Auth::user()->id }}" readonly>


          <!--     

<div id="enlace_listado">  
         
<p><a href="crearClientes.php"><i class="fa fa-user fa-2x"></i>&nbsp; Crear cliente nuevo</a>   </p> 
         
</div>

-->

          <div class="modal-footer">


            <button type="button" id="eliminar_evento" name="eliminar_evento" class="btn btn-outline-danger elminar_evento mr-auto">Eliminar cita</button>

            <button type="submit" id="Editar_cita" name="Editar_cita" class="btn btn-primary">Guardar</button>
            <button type="button" id="salir" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

          </div>

      </div>
    </div>
  </div>

  </form>
</div>

</div>





<!--=====================================

    MODAL AGREGAR CLIENTE

======================================-->

<div class="modal fade" id="modalAgregarCliente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">



  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title"><span style="color:#28a745;" class="fas fa-user mr-3"></span>Agregar cliente</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

        @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" id="form_crear_cliente1" action="{{'clientes'}}">

          <input type="hidden" name="_token" value="{{csrf_token()}}">



          <div class="row">

            <div class="col-md-3">

              <div class="form-group">

                <label for="Cedula" class="control-label">Cédula</label>


                <input type="number" name="cedula" class="form-control" id="cedula" autofocus required autocomplete="off">

                <span id="error_cedula"></span>


              </div>

            </div>


            <div class="col-md-5">

              <div class="form-group">

                <label for="Nombre" class="control-label">Nombre</label>

                <div id="fechaNacimiento" class="input-group date enddate">

                  <input type="text" name="nombre" class="form-control text-capitalize" id="nombre_cliente" required autocomplete="off">

                  <div class="alert-message" id="nombreError"></div>

                </div>
              </div>
            </div>


            <div class="col-md-4">
              <div class="form-group">

                <label for="telefono" class="control-label">Tel/Cel.</label>

                <input type="text" name="celular" class="form-control" id="celular_cliente" required autocomplete="off">

                <div class="alert-message" id="celularError"></div>

              </div>
            </div>


            <div class="col-md-3">



              <label for="fechaNacmiento" class="control-label">Fecha nacimiento</label>

              <div class="form-group">

                <input type="date" class="form-control" name="fecha_nacimiento1" id="fecha_nacimiento1" required />



              </div>

            </div>

            <input type="hidden" class="form-control" name="fecha_nacimiento" id="fecha_nacimiento" required />

            <div class="col-md-2">

              <div class="form-group">

                <label for="Edad" class="control-label">Edad</label>

                <input type="text" class="form-control" id="edad" name="edad" required autocomplete="off">


              </div>
            </div>




            <div class="col-md-7">

              <div class="form-group">

                <label for="direccion" class="control-label">Dirección</label>

                <input type="text" name="direccion" class="form-control text-capitalize" id="direccion" required onkeypress="return isNumber(event)">

                <div class="alert-message" id="direccionError"></div>

              </div>

            </div>



            <div class="col-md-6">

              <div class="form-group">

                <label for="barrio" class="control-label">Barrio</label>

                <input type="text" id="barrio" name="barrio" class="form-control text-capitalize" required autocomplete="off">

                <div class="alert-message" id="barrioError"></div>

              </div>
            </div>


            <div class="col-md-6">

              <div class="form-group">

                <label for="municipio" class="control-label">Municipio</label>

                <select class="form-control" name="municipio" id="municipio" onkeypress="return handleEnter(this, event)" required>

                  <option value="" selected="selected">Seleccionar municipio</option>

                  <option value=" Alcala">Alcala</option>
                  <option value=" Andalucía">Andalucía</option>
                  <option value=" Ansermanuevo">Ansermanuevo</option>
                  <option value=" Argelia">Argelia</option>
                  <option value="Bolivar">Bolivar</option>
                  <option value="Buga">Buga</option>
                  <option value="Bugalagrande">Bugalagrande</option>
                  <option value="Caicedonia">Caicedonia</option>

                  <option value="Cali">Cali</option>
                  <option value="Calima">Calima</option>
                  <option value="Candelaria">Candelaria</option>
                  <option value="Cartago">Cartago</option>
                  <option value="Dagua">Dagua</option>

                  <option value="El águila">El águila</option>
                  <option value="El cairo">El cairo</option>
                  <option value="El cerrito">El cerrito</option>
                  <option value="El dóvio">El dóvio</option>
                  <option value="Florida">Florida</option>
                  <option value="Ginebra">Ginebra</option>
                  <option value="Guacarí">Guacarí</option>
                  <option value="Jamundi">Jamundi</option>
                  <option value="La unión">La unión</option>
                  <option value="La paila">La paila</option>
                  <option value="La victoria">La victoria</option>
                  <option value="Obando">Obando</option>
                  <option value="Restrepo">Restrepo</option>
                  <option value="Rio frio">Rio frio</option>
                  <option value="Roldanillo">Roldanillo</option>
                  <option value="San pedro">San pedro</option>
                  <option value="Toro">Toro</option>
                  <option value="Trujillo">Trujillo</option>
                  <option value="Tulúa">Tulúa</option>
                  <option value="Ulloa">Ulloa</option>
                  <option value="Versalles">Versalles</option>
                  <option value="Yotoco">Yotoco</option>
                  <option value="Zarzal">Zarzal</option>


                </select>

              </div>
            </div>




            <div class="col-md-12">

              <div class="form-group">

                <label for="email" class="control-label">Email</label>

                <input type="email" name="email" class="form-control" id="email" autocomplete="off">

                <div class="alert-message" id="emailError"></div>

              </div>
            </div>

            <input type="hidden" name="estado" class="form-control" id="estado" value="1" autocomplete="off">

          </div>




          <!-- 
          <div class="form-group">
          <label for="end" class="col-sm-2 control-label">Fecha final</label>

          -->


          <input type="hidden" name="userId" class="form-control" id="userId" value="{{ Auth::user()->id }}" readonly>



          <!--     

<div id="enlace_listado">  
         
<p><a href="crearClientes.php"><i class="fa fa-user fa-2x"></i>&nbsp; Crear cliente nuevo</a>   </p> 
         
</div>

-->

          <div class="modal-footer">

            <button type="submit" id="agregar_cliente" name="agregar_cliente" class="btn btn-primary loader">Guardar</button>
            <button type="button" id="salir" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

          </div>

      </div>
    </div>
  </div>

  </form>
</div>


</div>




<!--=====================================

    MODAL AGREGAR CLIENTE 2

======================================-->

<div class="modal fade" id="modalAgregarCliente2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-id="1">



  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title"><span style="color:#28a745;" class="fas fa-user mr-3"></span>Agregar cliente</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

        @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" id="form_crear_cliente2" action="{{'clientes'}}">

          <input type="hidden" name="_token" value="{{csrf_token()}}">



          <div class="row">

            <div class="col-md-3">

              <div class="form-group">

                <label for="Cedula" class="control-label">Cédula</label>


                <input type="number" name="cedula2" class="form-control" id="cedula2" autofocus required autocomplete="off">

                <span id="error_cedula2"></span>


              </div>

            </div>


            <div class="col-md-5">

              <div class="form-group">

                <label for="Nombre" class="control-label">Nombre</label>

                <div id="fechaNacimiento" class="input-group date enddate">

                  <input type="text" name="nombre" class="form-control text-capitalize" id="nombre_cliente" required autocomplete="off">

                  <div class="alert-message" id="nombreError"></div>

                </div>
              </div>
            </div>


            <div class="col-md-4">
              <div class="form-group">

                <label for="telefono" class="control-label">Tel/Cel.</label>

                <input type="text" name="celular" class="form-control" id="celular_cliente" required autocomplete="off">

                <div class="alert-message" id="celularError"></div>

              </div>
            </div>


            <div class="col-md-3">



              <label for="fechaNacmiento" class="control-label">Fecha nacimiento</label>

              <div class="form-group">

                <input type="date" class="form-control" name="fecha_nacimiento2" id="fecha_nacimiento2" required />



              </div>

            </div>

            <input type="hidden" class="form-control" name="fecha_nacimientos" id="fecha_nacimientos" required />

            <div class="col-md-2">

              <div class="form-group">

                <label for="Edad" class="control-label">Edad</label>

                <input type="text" class="form-control" id="edad2" name="edad2" required autocomplete="off">


              </div>
            </div>




            <div class="col-md-7">

              <div class="form-group">

                <label for="direccion" class="control-label">Dirección</label>

                <input type="text" name="direccion" class="form-control text-capitalize" id="direccion" required onkeypress="return isNumber(event)">

                <div class="alert-message" id="direccionError"></div>

              </div>

            </div>



            <div class="col-md-6">

              <div class="form-group">

                <label for="barrio" class="control-label">Barrio</label>

                <input type="text" id="barrio" name="barrio" class="form-control text-capitalize" required autocomplete="off">

                <div class="alert-message" id="barrioError"></div>

              </div>
            </div>


            <div class="col-md-6">

              <div class="form-group">

                <label for="municipio" class="control-label">Municipio</label>

                <select class="form-control" name="municipio" id="municipio" onkeypress="return handleEnter(this, event)" required>

                  <option value="" selected="selected">Seleccionar municipio</option>

                  <option value=" Alcala">Alcala</option>
                  <option value=" Andalucía">Andalucía</option>
                  <option value=" Ansermanuevo">Ansermanuevo</option>
                  <option value=" Argelia">Argelia</option>
                  <option value="Bolivar">Bolivar</option>
                  <option value="Buga">Buga</option>
                  <option value="Bugalagrande">Bugalagrande</option>
                  <option value="Caicedonia">Caicedonia</option>

                  <option value="Cali">Cali</option>
                  <option value="Calima">Calima</option>
                  <option value="Candelaria">Candelaria</option>
                  <option value="Cartago">Cartago</option>
                  <option value="Dagua">Dagua</option>

                  <option value="El águila">El águila</option>
                  <option value="El cairo">El cairo</option>
                  <option value="El cerrito">El cerrito</option>
                  <option value="El dóvio">El dóvio</option>
                  <option value="Florida">Florida</option>
                  <option value="Ginebra">Ginebra</option>
                  <option value="Guacarí">Guacarí</option>
                  <option value="Jamundi">Jamundi</option>
                  <option value="La paila">La paila</option>
                  <option value="La unión">La unión</option>
                  <option value="La victoria">La victoria</option>
                  <option value="Obando">Obando</option>
                  <option value="Restrepo">Restrepo</option>
                  <option value="Rio frio">Rio frio</option>
                  <option value="Roldanillo">Roldanillo</option>
                  <option value="San pedro">San pedro</option>
                  <option value="Toro">Toro</option>
                  <option value="Trujillo">Trujillo</option>
                  <option value="Tulúa">Tulúa</option>
                  <option value="Ulloa">Ulloa</option>
                  <option value="Versalles">Versalles</option>
                  <option value="Yotoco">Yotoco</option>
                  <option value="Zarzal">Zarzal</option>


                </select>

              </div>
            </div>




            <div class="col-md-12">

              <div class="form-group">

                <label for="email" class="control-label">Email</label>

                <input type="email" name="email" class="form-control" id="email" autocomplete="off">

                <div class="alert-message" id="emailError"></div>

              </div>
            </div>

            <input type="hidden" name="estado" class="form-control" id="estado" value="1" autocomplete="off">

          </div>




          <!-- 
<div class="form-group">
<label for="end" class="col-sm-2 control-label">Fecha final</label>

-->


          <input type="hidden" name="userId" class="form-control" id="userId" value="{{ Auth::user()->id }}" readonly>



          <!--     

<div id="enlace_listado">  
         
<p><a href="crearClientes.php"><i class="fa fa-user fa-2x"></i>&nbsp; Crear cliente nuevo</a>   </p> 
         
</div>

-->

          <div class="modal-footer">

            <button type="submit" id="agregar_cliente2" name="agregar_cliente2" class="btn btn-primary loader">Guardar</button>
            <button type="button" id="salir" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

          </div>

      </div>
    </div>
  </div>

  </form>
</div>


</div>



<datalist id="ListadoColores">
  <option>#831d9f</option>
  <option>#ce3b3b</option>
  <option>#5e66d4</option>
  <option>#33997a</option>
  <option>#f05724</option>
  <option>#939e3d</option>
  <option>#be7b3c</option>
  <option>#e8d15e</option>
  <option>#c26fd3</option>
  <option>#c2bd1e</option>
  <option>#000000</option>
  <option>#013298</option>
  <option>#9602c7</option>
  <option>#4d4d4d</option>
  <option>#21BA45</option>
</datalist>



<!--=====================================

    MODAL AGREGAR CLIENTE A LISTA DE ESPERA

==========================================-->


<div class="modal fade" id="modalAgregarListaEspera" aria-labelledby="exampleModalLabel" aria-hidden="true">

  <div class="modal-dialog">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title"><span style="color:#28a745;" class="fas fa-user mr-3"></span>Agregar a lista de espera</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">


        @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" id="form_lista_espera" action="{{ url('/listado_citas') }}">


          <!-- <input type="hidden" name="_token" value="{{csrf_token()}}"> -->



          <div class="row">

            <div class="col-6">

              <div class="form-group">

                <label for="cliente">Cliente</label>


                <select class="selectBuscarCliente" name="selectBuscarCliente" id="selectBuscarCliente" style="width:100%;" required> </select>



              </div>
            </div>



            <div class="col-6">


              <div class="form-group BuscMascota" style="display:none;">

                <label for="mascota">Mascota</label>

                <select class="form-control" name="buscarMascota" id="buscarMascota" style="width:100%;" required>


                </select>

              </div>

            </div>


            <div class="col-12">
              <div class="form-group motivoConsulta" style="display:none;">
                <label class="control-label">Motivo de consulta</label>
                <input type="text" class="form-control motivo" name="motivo_consulta" required>
              </div>
            </div>




          </div>




      </div>






      <!-- 
              <div class="form-group">
              <label for="end" class="col-sm-2 control-label">Fecha final</label>
              
              -->


      <input type="hidden" name="userId" class="form-control" id="userId" value="{{ Auth::user()->id }}" readonly>



      <input type="text" name="nombreCliente" class="form-control" id="nombreCliente" autocomplete="off">

      <input type="hidden" name="nombre_Mascota" class="form-control" id="nombre_Mascota" autocomplete="off">


      <input type="hidden" name="idMascota" class="form-control" id="idMascota" autocomplete="off">


      <div class="form-group especie" style="display:none;">
        <select class="form-control  buscarEspecie" name="buscarEspecie" id="buscarEspecie" style="width:100%;" required>

        </select>

      </div>



      <div class="modal-footer">

        <button type="submit" id="agregar_lista_espera" name="agregar_lista_espera" class="btn btn-primary">Guardar</button>
        <button type="button" id="salir" class="btn btn-secondary salir" data-dismiss="modal">Cerrar</button>

      </div>

    </div>
  </div>
</div>

</form>
</div>

</div>





<!--  =======================================

MODAL DATATABLE LISTADO DE CLIENTES

============================================-->


<div class="modal fade modal-fullscreen" id="modalVerListadoClientes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">


  <div class="modal-dialog modal-xl role=" document">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title"><span style="color:#28a745;" class="fas fa-users mr-3"></span>Listado de clientes</h5>


        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <div class="row">
          <div class="col-lg-12">


            <table id="Table_listado_clientes" class="table table-hover" width="100%" style="width:100%;font-size:12.5px;">

              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Email</th>
                  <th>Teléfono</th>
                  <th>Dirección</th>
                  <th>Barrio</th>
                  <th>Ciudad</th>
                  <th>Fecha Nac.</th>
                  <th>Fecha reg.</th>
                  <th>Estado</th>
                </tr>
              </thead>

              
              <tbody>

                  <td align="center">

                  </td>

                </tr>
         
              </tbody>






              <!--
                         <th>Nombre</th>  
                         <th>Email</th>              
                         <th>Teléfono</th>
                         <th>Dirección</th>
                         <th>Barrio</th>
                         <th>Ciudad</th>
                         <th>fecha Nac.</th>
                         <th>fecha Reg.</th>
                         
                         <th>Estado</th>

                      -->

              </tr>

              </thead>

              <tbody>

              </tbody>


            </table>



          </div>
        </div>
        <div class="modal-footer">

          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>



  <!-- =======================================

CALCULO DE LA EDAD DEL CLIENTE 1

=========================================== -->


  <script>
    $('#fecha_nacimiento1').blur(function() {
      document.getElementById('edad').value = "";
      var fecha = new Date(document.getElementById('fecha_nacimiento1').value);
      var hoy = new Date();
      var edad = Math.floor((hoy - fecha) / (365.25 * 24 * 60 * 60 * 1000));
      document.getElementById('edad').value = edad + ' ' + 'Años';
      document.getElementById('fecha_nacimiento').value = document.getElementById('fecha_nacimiento1').value;
    });
  </script>




  <!-- =======================================

CALCULO DE LA EDAD DEL CLIENTE 2

=========================================== -->


  <script>
    $('#fecha_nacimiento2').blur(function() {
      document.getElementById('edad2').value = "";
      var fecha = new Date(document.getElementById('fecha_nacimiento2').value);
      var hoy = new Date();
      var edad = Math.floor((hoy - fecha) / (365.25 * 24 * 60 * 60 * 1000));
      document.getElementById('edad2').value = edad + ' ' + 'Años';
      document.getElementById('fecha_nacimientos').value = document.getElementById('fecha_nacimiento2').value;
    });
  </script>




  <!-- =======================================

DESHABILITAR CLICK DERECHO

============================================ -->

  <script>
    $(document).ready(function() {
      $("body").on("contextmenu", function(e) {
        return false;
      });

    });
  </script>




  <script type="text/javascript">
    //============================================

    // DATATIMEPICKER HORA_INI GUARDAR CITA CALENDARIO

    //============================================


    $(document).ready(function() {

      jQuery.datetimepicker.setLocale('es');

      jQuery('#start').datetimepicker({
        format: 'Y-m-d H:i',
        lang: 'es',

      });
    });
  </script>





  <script type="text/javascript">
    //============================================

    // DATATIMEPICKER HORA_INI GUARDAR CITA CALENDARIO

    //============================================


    $(document).ready(function() {

      $('#end').datetimepicker({
        format: 'Y-m-d H:i',


      });
    });
  </script>





  <script type="text/javascript">
    //============================================

    // DATATIMEPICKER HORA_INI EDITAR CALENDARIO

    //============================================


    $(document).ready(function() {


      jQuery('#hora_ini').datetimepicker({

        format: 'Y-m-d H:i',

      });
    });
  </script>





  <script type="text/javascript">
    //============================================

    // DATATIMEPICKER HORA_INI EDITAR CALENDARIO

    //============================================


    $(document).ready(function() {

      $('#hora_fin').datetimepicker({
        format: 'Y-m-d H:i',


      });
    });
  </script>




  <script>
    //============================================

    // LISTA DROPDOWN DE TERAPIAS

    //============================================

    $(document).ready(function() {


      function getlist_calendartipos() {
        let list = ['auriculoterapia', 'biomagnetismo', 'colonterapia', 'colonterapia - lodoterapia', 'control', 'drenaje',
          'lodoterapia', 'masaje', 'terapia con imanes', 'terapia neural'
        ];

        return list
      }



      //============================================

      // LISTADO DE COLORES Y TERAPIAS 

      //============================================


      function calendar_colors(v) {
        if (v == 'auriculoterapia') {
          color = '#c2bd1e';
        } else if (v == 'biomagnetismo') {
          color = '#c26fd3';
        } else if (v == 'colonterapia') {
          color = '#5e66d4';
        } else if (v == 'colonterapia - lodoterapia') {
          color = '#939e3d';
        } else if (v == 'control') {
          color = '#33997a';
        } else if (v == 'drenaje') {
          color = '#be7b3c';
        } else if (v == 'lodoterapia') {
          color = '#5e66d4';
        } else if (v == 'masaje') {
          color = '#e8d15e';
        } else if (v == 'terapia con imanes') {
          color = '#f05724';
        } else if (v == 'terapia neural') {
          color = '#831d9f';

        } else {
          color = '#3d85c6'
        }
        return color
      }




      //==================================================

      // AUTOCOMPLETAR DROPDOWN SERVICIOS CON tribute js

      //==================================================



      let $color = $('#ModalEdit').find('input[name="color2"]');

      // autocomplete titulo
      let list = getlist_calendartipos();
      let tribute = new Tribute({
        autocompleteMode: true,
        noMatchTemplate: function() {
          return "";
        },
        menuItemTemplate: function(item) {
          let color = calendar_colors(item.original.value);
          return item.string + ' <i style="color:' + color + ';" class="fas fa-circle ml-2"></i>';
        },
        selectTemplate: function(item) {
          color = calendar_colors(item.original.value);
          $color.val(color);
          return item.original.value;
        },

        values: $.map(list, function(value, i) {
          return {
            'key': value,
            'value': value
          }
        })
      });

      let $titulo = $('#ModalEdit').find('input[name="servicios"]');


      tribute.detach($titulo);
      tribute.attach($titulo);

      $titulo.off('mousedown').on('mousedown', function() {
        tribute.showMenuForCollection($titulo[0]);
      });
      $titulo.off('focusout').on('focusout', function() {
        $('.tribute-container').hide();
      });




      //==================================================

      // AUTOCOMPLETAR DROPDOWN SERVICIOS 2 CON tribute js

      //==================================================



      let $color2 = $('#ModalAdd').find('input[name="color"]');

      // autocomplete titulo
      let list2 = getlist_calendartipos();
      let tribute2 = new Tribute({
        autocompleteMode: true,
        noMatchTemplate: function() {
          return "";
        },
        menuItemTemplate: function(item) {
          let color = calendar_colors(item.original.value);
          return item.string + ' <i style="color:' + color + ';" class="fas fa-circle ml-2"></i>';
        },
        selectTemplate: function(item) {
          color = calendar_colors(item.original.value);
          $color2.val(color);
          return item.original.value;
        },

        values: $.map(list2, function(value, i) {
          return {
            'key': value,
            'value': value
          }
        })
      });

      let $titulo2 = $('#ModalAdd').find('input[name="servicios2"]');


      tribute2.detach($titulo2);
      tribute2.attach($titulo2);

      $titulo2.off('mousedown').on('mousedown', function() {
        tribute2.showMenuForCollection($titulo2[0]);
      });
      $titulo2.off('focusout').on('focusout', function() {
        $('.tribute-container').hide();
      });




    });
  </script>






  <script>
    //============================================

    // AGREGAR CLIENTE NUEVO

    //============================================

    /*

    $('#form_crear_cliente').off('submit').on('submit', function (event) {

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    */

    /* Configurar botón submit con spinner */

    /*

    let btn = $('#agregar_cliente') 
        let existingHTML =btn.html() //store exiting button HTML
        //Add loading message and spinner
        $(btn).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Procesando...').prop('disabled', true)

        setTimeout(function() {
          $(btn).html(existingHTML).prop('disabled', false) //show original HTML and enable
        },5000) //5 seconds

            $('#agregar_cliente').attr('disabled', true);

            

            event.preventDefault();

            try {

            $.ajax({
                url: 'crear_clientes',
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function() {

                 // table.ajax.reload();

                    $('#agregar_cliente').prop("required", true);
                   // $('#selectBuscarCliente').html("");
                   
                    $('#form_crear_cliente')[0].reset();
                    $('#modalAgregarCliente').modal('hide');
                  
                                       

                    toastr["success"]("Datos guardados correctamente.");
                 }


             });

            } catch(e) {
              toastr["danger"]("Se ha presentado un error.", "Información");
              }

             // window.location.href = 'cliente/' +id;
           
        });


    */
  </script>







  <script>
    //============================================

    // AGREGAR CLIENTE NUEVO

    //============================================


    $('#form_crear_cliente2').off('submit').on('submit', function(event) {

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });


      /* Configurar botón submit con spinner */

      let btn = $('.agregar_cliente')
      let existingHTML = btn.html() //store exiting button HTML
      //Add loading message and spinner
      $(btn).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Procesando...').prop('disabled', true)

      setTimeout(function() {
        $(btn).html(existingHTML).prop('disabled', false) //show original HTML and enable
      }, 15000) //5 seconds

      $('.agregar_cliente').attr('disabled', true);



      event.preventDefault();

      try {

        $.ajax({
          url: 'crear_clientes',
          method: "POST",
          data: $(this).serialize(),
          dataType: "json",
          success: function() {

            // table.ajax.reload();

            $('#agregar_cliente').prop("required", true);
            // $('#selectBuscarCliente').html("");



            $('#form_crear_cliente2')[0].reset();
            $('#modalAgregarCliente2').modal('hide');



            toastr["success"]("Datos guardados correctamente.");
          }


        });

      } catch (e) {
        toastr["danger"]("Se ha presentado un error.", "Información");
      }

      // window.location.href = 'cliente/' +id;

    });
  </script>



  <script>
    $(document).ready(function() {

      $('#modalAgregarCliente').on('hide.bs.modal', function() {
        let valor1 = $('.nombre_cliente').val();
        let valor2 = $('.celular_cliente').val();
        $('#ModalAdd').find('input[id="livesearch2"]').val(valor1);
        $('#ModalAdd').find('input[id="celular_cliente"]').val(valor2);
      });


    });
  </script>






  <!-- =======================================

SELECT2 - BUSQUEDAD DE CLIENTES

============================================ -->

  <script type="text/javascript">
    $('.livesearch').select2({
      placeholder: 'Buscar cliente por nombre...',
      language: "es",
      allowClear: true,
      minimumInputLength: 3,
      delay: 250,
      ajax: {
        // url: '/ajax-autocomplete-search',

        url: '{{ url("/ajax-autocomplete-search") }}',

        dataType: 'json',

        processResults: function(data) {


          return {
            results: $.map(data, function(item) {
              return {
                text: item.nombre + ' ' + ' - ' + 'Cédula' + ' ' + item.cedula + ' ' + ' - ' + 'Teléfono' + ' ' + item.celular,
                id: item.id_cliente
              }

              // location.href = '/clientes/' + id
              // window.location.href =('clientes/id');      

              //  window.location.href =('/clientes'+ item['id']);  
            })

          };

        },


        cache: true,

      }

    });


    //================================================

    // SELECT2 - PASAR VALORES A VIEW BLADE - CLIENTE

    //================================================

    $('#livesearch').off('change').on('change', function() {


      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      let id = $(this).val();

      $.ajax({


        url: '/cliente/' + id,

        method: "GET",
        data: $(this).serialize(),
        dataType: "json",
        success: function(data) {

          $('#celular').val(data['celular_cliente']);




        }

      });

      window.location.href = 'cliente/' + id;

    });
  </script>




  <!-- =========================================

DATATABLE MOSTRAR LISTADO DE CLIENTES

==============================================  -->



  <script type="text/javascript">
    $(document).ready(function() {

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });


      // let id =$('#id_cliente').val();


      let table = $('#Table_listado_clientes').DataTable({

        processing: true,
        serverSide: false,
        rendering: true,
        retrive: true,
        paging: true,
        info: true,
        filter: true,



        "language": {

          /*  "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading..n.</span> ',  */


          "emptyTable": "No hay clientes registrados.",
          "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
          "infoEmpty": "Mostrando 0 to 0 de 0 Entradas",
          "infoFiltered": "(Filtrado de _MAX_ total entradas)",
          "infoPostFix": "",
          "thousands": ",",
          "lengthMenu": "Mostrar _MENU_ Entradas",
          "loadingRecords": "Cargando...",
          "processing": "Procesando...",
          "search": "Buscar:",
          "zeroRecords": "Sin resultados encontrados",
          "paginate": {
            "first": "Primero",
            "last": "Ultimo",
            "next": "Siguiente",
            "previous": "Anterior"
          }

        },

      });
    });
  </script>



  <script>
    // =========================================

    //ACTIVAR / DESACTIVAR CLIENTES - BOOTSTRAP TOGGLE

    // ============================================== 




    $('.switch').change(function() {

      //  $('body').on('change', '.switch', function(e) {


      let estado = $(this).prop('checked') === true ? 1 : 0;
      let id = $(this).data('id');


      $.ajax({

        type: "GET",
        dataType: "json",
        url: 'actualizar_cliente',
        data: {
          'estado': estado,
          'id': id
        },
        success: function(data) {
          console.log(data.success)

          if (estado === 1) {
            toastr["success"]("El cliente se ha habilitado.");

          } else if (estado === 0) {
            toastr["success"]("El cliente se ha deshabilitado temporalmente.");
          }
        }

      })
    });
  </script>








  <!-- =======================================

SELECTBUSCARCLIENTE - CALENDARIO DE CITAS

============================================ -->


  <script type="text/javascript">
    $('.livesearch2').select2({


      language: "es",
      placeholder: 'Buscar cliente...',
      // allowClear: true,
      minimumInputLength: 3,
      dropdownParent: $('#ModalAdd'),


      ajax: {

        url: '/ajax-autocomplete-search',

        dataType: 'json',
        delay: 250,


        processResults: function(data) {


          return {
            results: $.map(data, function(item) {
              return {
                text: item.nombre, // + ' '+ ' - '+ 'Cédula' + ' '+ item.cedula,
                id: item.id_cliente,
                celular: item.celular,

              }

            })
          };

        },

        cache: true,

      }



    });



    //==================================================

    // SELECT2 - PASAR VALORES A VIEW BLADE - CLIENTE

    //==================================================

    $('.livesearch2').off('change').on('change', function() {


      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });



      let id = $(this).val();

      $('#cliente').text('');

      let cliente = '';

      cliente = $(".livesearch2").text();

      $('#cliente').val(cliente);




      $.ajax({


        url: 'ajax-autocomplete-search/' + id,

        method: "GET",
        data: $(this).serialize(),
        dataType: "json",
        success: function(data) {


        }

      });



      // window.location.href = 'cliente/' +id;

    });
  </script>


  <!-- =======================================

SELECTBUSCARCLIENTE - LISTA DE ESPERA

============================================ -->

  <script type="text/javascript">
    $('.selectBuscarCliente').select2({
      //  allowClear: true,
      placeholder: 'Buscar cliente por nombre...',
      language: "es",
      minimumInputLength: 3,


      ajax: {

        url: '/ajax-autocomplete-search',

        dataType: 'json',
        delay: 250,


        processResults: function(data) {



          return {
            results: $.map(data, function(item) {
              return {
                text: item.nombre,
                id: item.id_cliente



              }



              // location.href = '/clientes/' + id
              // window.location.href =('clientes/id');      

              //  window.location.href =('/clientes'+ item['id']);  
            })



          };




          // $('.selectBuscarCliente').find(':selected').text();

          // $('#selectBuscarCliente :selected').text();


        },


        cache: true,

      }



    });

    //===================================================

    // SELECT DEPENDIENTES buscarcliente - buscarmascota

    //===================================================

    $('.selectBuscarCliente').off('change').on('change', function() {



      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });



      let id_cliente = this.value;

      $("#buscarMascota").html('');
      $("#buscarEspecie").html('');
      $("#id_mascota").html('');
      $("#nombreMascota").html('');


      $.ajax({
        url: 'buscarmascota',
        type: 'POST',
        dataType: 'json',
        data: {
          id_cliente: id_cliente
        },


        success: function(data) {

          if (data.length > 0) {

            //  $('#buscarMascota').html('<option value="">Seleccinar mascota</option>');
            $('.BuscMascota').css("display", "block");
            $('.motivoConsulta').css("display", "block");
            $('.motivo').focus();



            $.each(data, function(key, value) {
              $('#buscarMascota').append('<option value="' + value.id + '">' + value.nombre + ' - ' + value.especie + ' </option>');

            });




            $.each(data, function(key, value) {
              $('#buscarEspecie').append('<option value="' + value.especie + '">' + value.especie + '</option>');

            });







            $('nombre_Mascota').val('');

            let mascota = '';

            mascota = $("#buscarMascota option:selected").text();

            $('#nombre_Mascota').val(mascota);


            $('#idMascota').val('');

            let id_mascota = '';

            id_mascota = $("#buscarMascota option:selected").val();

            $('#idMascota').val(id_mascota);




            $('#nombreCliente').val('');

            let cliente = '';

            cliente = $(".selectBuscarCliente").text();

            $('#nombreCliente').val(cliente);







          } else {

            toastr["warning"]("El cliente no tiene mascotas registradas.");


          }

        }

      });


    });
  </script>


  <script>
    $("#buscarMascota").change(function() {

      $('#nombre_mascota').val('');


      let mascota = '';

      mascota = $("#buscarMascota option:selected").text();

      $('#nombre_Mascota').val(mascota);

    });
  </script>



  <script>
    $("#buscarMascota").change(function() {

      $('#idMascota').val('');

      let mascota = '';

      mascota = $("#buscarMascota option:selected").val();

      $('#idMascota').val(mascota);

    });
  </script>



  <!-- ================================= 

BORRAR CONTENIDO ESCRITO EN SELECT2: livesearch2

================================= -->


  <script>
    $('.livesearch2').on('select2:opening', function(e) {

      $('.livesearch2').html('');
      $('#celular_cliente').val('');


    });
  </script>



  <!-- =======================================================

PASAR DATOS DE CAMPOS A INPUT TEXT CON SELECT2: livesearch2

============================================================ -->


  <script>
    $('#livesearch2').on('select2:select', function(evt) {

      let celular = evt.params.data.celular;

      var opt = "<option value='" + celular + "' selected ='selected'> </option>";
      $("#celular_cliente").html(opt);
      $("#celular_cliente").val(celular).trigger("change");
    });
  </script>





  <!-- ================================= 

RESET SELECT2: selectBuscarCliente

================================= -->


  <script>
    $('.selectBuscarCliente').on('select2:opening', function(e) {

      $('.selectBuscarCliente').html('');

    });
  </script>




  <script>
    //==================================================

    // SELECT2 - PASAR VALORES A VIEW BLADE - CLIENTE

    //================================================

    $('#livesearch').off('change').on('change', function() {

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      let id = $(this).val();


      $.ajax({


        url: '/cliente/' + id,

        method: "GET",
        data: $(this).serialize(),
        dataType: "json",
        success: function(data) {


        }

      });

      window.location.href = 'cliente/' + id;

    });
  </script>






  <!-- ================================= 

  FullCalendar  

 ================================= -->


  <script>
    $(document).ready(function() {
      let SITEURL = "{{url('/')}}";

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      let calendar = $('#calendar').fullCalendar({

        loading: function(isLoading, view) {

          if (isLoading) {

            $('#loading').show();

          } else {

            $('#loading').hide();

          }

        },

        locale: 'es',

        timezone: 'local',

        editable: true,

        defaultView: 'listDay',

        events: SITEURL + "inicio",

        height: 500,

        //displayEventTime: true,
        // themeSystem: 'bootstrap4',

        timeFormat: "H:mm a",

        slotLabelFormat: [

          'H: (mm)a',

        ],


        header: {

          left: 'title',

        },


        events: SITEURL + "/fullcalendareventmaster",

        editable: true,

        selectable: true,

        selectHelper: true,

        eventRender: function(Event) {


          $('.popover').remove();

          if (Event.allDay === 'true') {

            Event.allDay = true;

          } else {

            Event.allDay = false;
          }


        },


        eventAfterRender: function(event, element) {

          $('.popover').remove();

          element.popover({

            placement: 'left',

            trigger: 'hover',

            container: 'body',

            html: true,

            title: event.cliente,

            content: '<p>' + 'Teléfono: ' +

              event.telefono + '<p>' + 'Asignado a: ' + event.medico + '<p>' + 'Descripción: ' + event.descripcion +

              '<p>' + '<h6>' + 'Haga clic para cambiar datos. ',
          });

        },



        select: function(start, end, allDay) {

          calendar.fullCalendar('unselect');
        },





        //   events: SITEURL + "/fullcalendareventmaster",

        eventClick: function(event) {



          $('#ModalEdit #id').val(event.id);


          $('#ModalEdit #cliente').val(event.cliente);

          $('#ModalEdit #telefono').val(event.telefono);

          // $('#ModalEdit #email').html(event.email);

          $('#ModalEdit #medico').val(event.medico);

          $('#ModalEdit #descripcion').val(event.descripcion);

          $('#ModalEdit #servicios').val(event.title);


          $('#ModalEdit #color2').val(event.color);

          $('#ModalEdit #hora_ini').val(moment(event.start).format('YYYY-MM-DD HH:mm:ss'));

          $('#ModalEdit #hora_fin').val(moment(event.end).format('YYYY-MM-DD HH:mm:ss'));

          $('#ModalEdit #fecha_actual').val(moment(event.start).format('DD-MM-YYYY'));


          $('#ModalEdit').modal('show');



        },

      });

    });



    function displayMessage(message) {

      toastr.success(message);

    }
  </script>


  <!-- ==========================

 Calendar2  

 ========================== -->


  <script>
    $(document).ready(function() {

      let SITEURL = "{{url('/')}}";

      $.ajaxSetup({

        headers: {

          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

        }

      });

      let calendar = $('#calendar2').fullCalendar({


        loading: function(isLoading, view) {

          if (isLoading) {

            $('#loading2').show();

          } else {

            $('#loading2').hide();

          }

        },


        locale: "es",

        timezone: 'local',

        editable: true,

        defaultView: "agendaDay",

        events: SITEURL + "inicio",

        displayEventTime: true,

        eventLimit: true, // allow "more" link when too many events

        minTime: "07:00",

        maxTime: "20:00",

        nowIndicator: true,

        timeFormat: "H:mm a",

        slotDuration: '00:30:00',


        slotLabelFormat: [

          'H: (mm)a',

        ],

        header: {

          left: 'prev,next today',

          center: 'print',

          right: 'agendaDay, agendaThreeDay, agendaWeek, month'

        },

        views: {

          agendaThreeDay: {

            type: 'agenda',

            duration: {

              days: 3

            },

            buttonText: 'Tres días'
          },

          agendaDay: {

            type: 'agenda',

            duration: {

              days: 1

            },



            buttonText: 'Día'
          },
          defaultView: 'agendaThreeDay'
        },


        // navLinks: true, 


        viewRender: function(view) {

          var title = view.title;

          $(".titulo-mes").html(title);

        },


        events: SITEURL + "/fullcalendareventmaster",

        eventRender: function(event, element, view) {



          $('.popover').remove();

          if (event.allDay === 'true') {

            event.allDay = true;

          } else {

            event.allDay = false;

          }


          element.popover({

            placement: 'left',

            trigger: 'hover',

            container: 'body',

            html: true,

            title: event.cliente,

            content: '<p>' + 'Teléfono: ' +

              event.telefono + '<p>' + 'Asignado a: ' + event.medico + '<p>' + 'Descripción: ' + event.descripcion,



          });

          // return $('#selector2').val() === 'todos' || event.title.indexOf($('#selector2').val()) >= 0;
          // return $('#selector').val() === 'todos' || event.medico.indexOf($('#selector').val()) >= 0;

          return ['todos', event.medico].indexOf($('#selector').val()) >= 0 && ['', event.title].indexOf($('#filtrar_calendario').val()) >= 0;


        },



        selectable: true,
        selectHelper: true,


        select: function(start, end, allDay) {


          $('#ModalAdd #start').val(moment(start).format('YYYY-MM-DD HH:mm'));

          $('#ModalAdd #end').val(moment(end).format('YYYY-MM-DD HH:mm'));

          $('#ModalAdd #hora_ini').val(moment(start).format('hh:mm a'));

          $('#ModalAdd #hora_fin').val(moment(end).format('hh:mm a'));

          $('#ModalAdd #fecha_actual').val(moment(start).format('DD-MM-YYYY'));

          $('#ModalAdd').modal('show');





          //  $('#users_form').off('submit').on('submit', function(event) {

          /*

                  $('#users_form').submit(function (event) {
                 
                 

                

                    $.ajax({
                        url: SITEURL + "/fullcalendareventmaster/create",
                        method: "POST",
                        data: $(this).serialize(),
                        dataType: "json",
                        success: function(data) {

                            $('#agregar').attr('disabled', true);
                            $('#agregar')[0].reset();
                            $('#ModalAdd').modal('hide');
                            //$('#agenda_modal').modal('hide');
                          
                           

                            $('#calendar2').fullCalendar('refetchEvents');

                            $('#calendar2').fullCalendar('render');
                            $('#calendar').fullCalendar('refetchEvents');

                            toastr["success"]("los datos se han guardado correctamente", "Información");

                        }
                      
                    });

                    event.preventDefault();
                });
       
                */



          calendar.fullCalendar('unselect');

        },

        editable: true,



        eventDrop: function(event, delta) {

          $('.popover.fade.top').remove();

          let start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");

          let end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");

          $.ajax({

            url: SITEURL + '/fullcalendareventmaster/update',

            data: 'title=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id,

            type: "POST",

            success: function(response) {

              displayMessage("Evento actualizado correctamente.");

              $('#calendar').fullCalendar('refetchEvents');

            }

          });

        },

        eventResize: function(event) {

          $('.popover.fade.top').remove();

          var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");

          var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");

          $.ajax({

            url: SITEURL + '/fullcalendareventmaster/update',

            data: 'title=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id + 'color=' + event.color,

            type: "POST",

            success: function(response) {

              displayMessage("Evento actualizado correctamente.");

              $('#calendar').fullCalendar('refetchEvents');


            }
          });

        },


        eventClick: function(event) {


          $('#ModalEdit #id').val(event.id);

          $('#ModalEdit #cliente').val(event.cliente);

          $('#ModalEdit #telefono').val(event.telefono);

          $('#ModalEdit #medico').val(event.medico);

          $('#ModalEdit #descripcion').val(event.descripcion);

          $('#ModalEdit #servicios').val(event.title);

          $('#ModalEdit #titulo').val(event.title);

          $('#ModalEdit #color2').val(event.color);

          $('#ModalEdit #hora_ini').val(moment(event.start).format('YYYY-MM-DD HH:mm:ss'));

          $('#ModalEdit #hora_fin').val(moment(event.end).format('YYYY-MM-DD HH:mm:ss'));

          $('#ModalEdit #fecha_actual').val(moment(event.start).format('DD-MM-YYYY'));

          $('#ModalEdit').modal('show');

        }



      });




      // ========================================   

      // SELECCIONAR EVENTO DESDE SELECT2 EVENTOS

      // ========================================

      $('#selector').on('change', function() {

        $('#calendar2').fullCalendar('rerenderEvents');

      });




      // ========================================   

      // SELECCIONAR EVENTO DESDE SELECT2 EVENTOS

      // ========================================


      /*
      $('#filtrar_calendario').on('keyup', function() {

        var value = $(this).val();
       
          $('#calendar2').fullCalendar('rerenderEvents');
          
          // return $(this).text().search(new RegExp(value, "i")) < 0;

           return  $(this).toLowerCase().indexOf(search.toLowerCase()) !== -1;
           
        });
      */



      $("#filtrar_calendario").on("keyup", function() {

        var value = $(this).val().toLowerCase();

        $("#filtrar_calendario").filter(function() {

          $(this).text().toLowerCase().indexOf(value) > -1

        });


        $('#calendar2').fullCalendar('rerenderEvents');

      });


      $('#ModalCalendar').on('shown.bs.modal', function() {

        $("#calendar2").fullCalendar('render');

        $("#calendar2").fullCalendar('refetchEvents');



      });


    });

    function displayMessage(message) {

      toastr.success(message);

    }
  </script>



  <!-- =========================================

INSERTAR DATOS A FULLCALENDAR

==============================================  -->


  <script type="text/javascript">
    $(document).ready(function() {


      $('#users_form').on('submit', function(event) {

        event.preventDefault();


        /* Configurar botón submit con spinner */

        let btn = $('#agregar_cita')

        let existingHTML = btn.html() //store exiting button HTML
        //Add loading message and spinner

        $(btn).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Procesando...').prop('disabled', true)

        setTimeout(function() {

          $(btn).html(existingHTML).prop('disabled', false) //show original HTML and enable

        }, 5000) //5 seconds



        $.ajax({
          url: "/fullcalendareventmaster/create",

          method: "POST",

          data: $(this).serialize(),

          dataType: "json",

          success: function(data) {

            // $('#agregar').attr('disabled', true);
            $('#users_form')[0].reset();

            $('#ModalAdd').modal('hide');
            //$('#agenda_modal').modal('hide');


            $('#calendar2').fullCalendar('refetchEvents');

            $('#calendar').fullCalendar('refetchEvents');

            //  $("#calendar").fullCalendar("unselect");

            toastr["success"]("Evento guardado correctamente.");

            //document.location.reload(); 


          }

        });

      });

    });
  </script>



  <!-- =========================================

EDITAR DATOS DE FULLCALENDAR

==============================================  -->




  <script type="text/javascript">
    $(document).ready(function() {

      $('#editar_calendario').on('submit', function(e, event) {

        e.preventDefault();


        /* Configurar botón submit con spinner */

        let btn = $('#Editar_cita')

        let existingHTML = btn.html() //store exiting button HTML
        //Add loading message and spinner

        $(btn).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Procesando...').prop('disabled', true)

        setTimeout(function() {

          $(btn).html(existingHTML).prop('disabled', false) //show original HTML and enable

        }, 5000) //5 seconds



        let id = $(this).data('id');

        $.ajax({
          url: "/fullcalendareventmaster/update_event",
          method: "GET",
          data: $(this).serialize(),

          dataType: "json",
          success: function(data) {

            $('#id').val(data.id);

            $('#cliente').val(data.cliente);

            $('#medico').val(data.medico);

            $('#especie').html(data.especie);

            $('#telefono').val(data.telefono);

            $('#descripcion').val(data.descripcion);

            $('#servicios').val(data.title);

            $('#titulo').val(data.title);

            $('#ModalEdit #color2').val(data.color);

            $('#calendar').fullCalendar('refetchEvents');

            $('#calendar2').fullCalendar('refetchEvents');


            // $('#agregar').attr('disabled', true);
            $('#editar_calendario')[0].reset();

            $('#ModalEdit').modal('hide');

            $('#agenda_modal').modal('hide');

            toastr["success"]("los datos se han actulizado correctamente");

            // $("#fullCalModal").fullCalendar('addEventSource', JSON, true); 

            // document.location.reload(); 

          }

        });

      });

    });
  </script>





  <!-- =========================================

VINCULAR SELECT COLOR CON SERVICIOS - EDITAR

============================================ -->

  <!--

<script type="text/javascript">
 
 $("#servicios2").change(function(){
   

				let select = document.getElementById('servicios2');
				let option = select.options[select.selectedIndex];

				document.getElementById('color').value = option.value;
		   	document.getElementById('titulo').value = option.text;
			

    });   
	
</script>

  -->


  <!-- =========================================

VINCULAR SELECT COLOR CON SERVICIOS - AGREGAR

============================================ -->


  <script type="text/javascript">
    $("#servicio").change(function() {


      let select = document.getElementById('servicio');

      let option = select.options[select.selectedIndex];

      document.getElementById('color').value = option.value;

      document.getElementById('title').value = option.text;


    });
  </script>






  <!-- ==============================

// VERIFICAR SI EXISTE CLIENTE

===================================  -->

  <script>
    $(document).ready(function() {

      $('#cedula').blur(function() {

        var error_cedula = '';

        var cedula = $('#cedula').val();

        var _token = $('input[name="_token"]').val();

        var filter = /([0-9])/;

        if (!filter.test(cedula)) {

          $('#error_cedula').html('<label class="text-danger">Debe escribir número de cédula.</label>');

          $('#cedula').addClass('has-error');

          $('#agregar_cliente').attr('disabled', 'disabled');

        } else {

          $.ajax({

            url: 'verificar_cliente',

            method: "POST",

            data: {

              cedula: cedula

            },

            success: function(result) {

              if (result == 'unique') {

                $('#error_cedula').html('<label class="text-danger">El cliente ya existe.</label>');

                $('#cedula').addClass('has-error');

                $('#agregar_cliente').attr('disabled', 'disabled');

              } else {

                $('#error_cedula').html('<label class="text-success">Disponible.</label>');

                $('#cedula').removeClass('has-error');

                $('#agregar_cliente').attr('disabled', false);


              }

            }

          })

        }

      });

    });
  </script>




  <!-- ==============================

// VERIFICAR SI EXISTE CLIENTE 2

===================================  -->

  <script>
    $(document).ready(function() {

      $('#cedula2').blur(function() {

        var error_cedula = '';

        var cedula2 = $('#cedula2').val();

        var _token = $('input[name="_token"]').val();

        var filter = /([0-9])/;

        if (!filter.test(cedula2)) {

          $('#error_cedula2').html('<label class="text-danger">Debe escribir número de cédula.</label>');

          $('#cedula2').addClass('has-error');

          $('#agregar_cliente2').attr('disabled', 'disabled');

        } else {

          $.ajax({

            url: 'verificar_cliente',

            method: "POST",

            data: {

              cedula: cedula2

            },

            success: function(result) {

              if (result == 'unique') {

                $('#error_cedula2').html('<label class="text-danger">El cliente ya existe.</label>');

                $('#cedula2').addClass('has-error');

                $('#agregar_cliente2').attr('disabled', 'disabled');

              } else {

                $('#error_cedula2').html('<label class="text-success">Disponible.</label>');

                $('#cedula2').removeClass('has-error');

                $('#agregar_cliente2').attr('disabled', false);


              }

            }

          })

        }

      });

    });
  </script>




  <!-- =========================================

 FUNCIÓN FOCUS PARA PRIMER INPUT ModalAdd

 ============================================ -->


  <script>
    $(document).ready(function() {

      $('#ModalAdd').on('shown.bs.modal', function() {

        $('#cliente').focus()

      });

    });
  </script>




  <!-- =========================================

 FUNCIÓN FOCUS PARA PRIMER INPUT ModalAdd

 ============================================ -->


  <script>
    $(document).ready(function() {

      $('#ModalAgregarCliente').on('shown.bs.modal', function() {

        $('#cliente').focus();

      });

    });
  </script>



  <script>
    $(document).ready(function() {

      $('#buscarMascota').off('change').on('change', function() {


        let id_cliente = this.value;

        if (data.length > 0) {

          $.ajax({
            url: 'buscarmascota',

            type: 'POST',

            dataType: 'json',

            data: {

              id_cliente: id_cliente
            },

            success: function(data) {


              $.each(data, function(key, value) {

                $('#buscarEspecie').append('<option value="' + value.especie + '">' + value.especie + '</option>');

              });

            }

          });

        }

      });

    });
  </script>






  <!-- =====================================================

 FUNCiÓN PARA OCULTAR ELEMENTOS DE MODAL modalAgregarListaEspera

 ======================================================== -->


  <script>
    $(document).ready(function() {

      $('#modalAgregarListaEspera').on('hidden.bs.modal', function() {

        $('#selectBuscarCliente').html('');

        $('#buscarMascota').html('');

        $('#buscarEspecie').html('');

        $('#motivoConsulta').html('');

        //  $('.BuscMascota').css("display", "none");
        //  $('.motivoConsulta').hide();
        // $('.buscarEspecie').css("display", "none");
        $('#nombreMascota').html('');

        $('#form_lista_espera')[0].reset();

        $('#modalAgregarListaEspera').modal('hide');

        $("#nombreCliente").html('');
        // $("#nombreCliente").hide();


      });

    });
  </script>



  <!-- ================================================

 FUNCIÓN FOCUS PARA PRIMER INPUT modalAgregarCliente

 ================================================= -->


  <script>
    $(document).ready(function() {

      $('#modalAgregarCliente2').on('shown.bs.modal', function() {

        $('#cedula').focus();

        // $('#agregar_cliente').attr('enabled','enabled');

      });

    });
  </script>



  <!-- ===================================================

CARGAR DATATABLE JQUERY LISTA DE ESPERA Y  GUARDAR DATOS 

======================================================= --->

  <script type="text/javascript">
    $(document).ready(function() {


      $.ajaxSetup({

        headers: {

          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }

      });


      let table = $('#Table_listado_cumpleaños').DataTable({

        processing: true,

        serverSide: true,

        rendering: true,

        retrive: true,

        paging: false,

        info: false,

        filter: false,

        responsive: true,

        type: "GET",

        ajax: "{{ url('/listado_cliente') }}",


        columns: [


          {
            data: 'nombre',

            name: 'nombre'
          },
          {
            data: 'celular',

            name: 'celular'
          },
          {
            data: 'fecha_nacimiento',

            name: 'fecha_nacimiento'
          },

        ],

        order: [
          [0, 'desc']
        ],


        "language": {

          /*  processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading..n.</span> ',   */
          "emptyTable": "No hay eventos para mostrar."

        },



      });


      //============================================

      // AGREGAR CLIENTE A LISTA DE ESPERA

      //============================================


      $('#form_lista_espera').off('submit').on('submit', function(event) {

        $.ajaxSetup({

          headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

          }

        });


        /* Configurar botón submit con spinner */

        let btn = $('#agregar_lista_espera')

        let existingHTML = btn.html() //store exiting button HTML
        //Add loading message and spinner

        $(btn).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Procesando...').prop('disabled', true)

        setTimeout(function() {
          $(btn).html(existingHTML).prop('disabled', false) //show original HTML and enable
        }, 5000) //5 seconds

        $('#agregar').attr('disabled', true);

        event.preventDefault();

        try {

          $.ajax({

            url: "/listado_citas",

            method: "POST",

            data: $(this).serialize(),

            dataType: "json",

            success: function(data) {

              table.ajax.reload();

              $('#agregar').prop("required", true);

              $('#buscarMascotas').empty();

              $('.BuscMascota').css("display", "block");

              $('.motivoConsulta').css("display", "block");

              $('#nombreMascota').html("");

              $('#form_lista_espera')[0].reset();

              $('#modalAgregarListaEspera').modal('hide');

              $("#nombreCliente").html('');

              $('.selectBuscarCliente').val('').trigger('change');


              toastr["success"]("Cita registrada correctamente.");





            }

          });

        } catch (e) {
          toastr["danger"]("Se ha presentado un error.", "Información");
        }

      });





      // ======================================= 

      //  ELIMINAR CITA DE LISTA DE ESPERAS  

      // ========================================= 

      //event

      $('body').on('click', '.deletePost', function(e) {


        let id = $(this).data("id");

        e.preventDefault();

        $.ajax({
          type: 'delete',

          url: '/eliminar_cita/' + id,

          success: function(data) {

            table.ajax.reload();

            toastr["success"]("Cita eliminada correctamente.");

          }
        });



      });


    });
  </script>



  <script>
    // ======================================= 

    //  ELIMINAR EVENTO DE CALENDARIO

    // ========================================= 




    $('#eliminar_evento').off('click').on('click', function(event, e) {

      event.preventDefault();

      let id = $('#id').val();

      swal({
        title: "Esta seguro de eliminar?",

        text: "La acción es permanente!",

        type: "warning",

        showCancelButton: !0,

        confirmButtonText: "Si, Eliminar",

        cancelButtonText: "No, cancelar",

        reverseButtons: !0


      }).then(function(e) {

        if (e.value === true) {

          let CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

          $.ajax({

            type: 'delete',

            url: '/fullcalendareventmaster/delete/' + id,

            dataType: 'JSON',

            success: function(data) {

              //   if (data.success === true) {

              swal("Cita eliminada correctamente!", data.message, "success");

              $('#ModalEdit').modal('hide');

              $('#calendar').fullCalendar('refetchEvents');

              $('#calendar2').fullCalendar('refetchEvents');



              //  } else {
              //       swal("Error!", data.message, "error");
              //   }
            }

          });

        } else {

          e.dismiss;

        }

      }, function(dismiss) {

        return false;

      })

    });
  </script>





  @endsection