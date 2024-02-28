@extends('layouts.app')


@section('content')


@livewireStyles

<style>
 

  .has-error {
    border-color: #cc0000;
    background-color: #ffff99;
  }


  .alert-message {
    color: red;
  }


  .popover {
    pointer-events: none;
  }
</style>


<br>



<!-- =============================

REGISTRO DE INGRESOS O EGRSOS 

================================== -->


<div class="container-fluid">


  <!-- ====================================

FORMULARIO RECEPCION DE USUARIOS

=========================================  -->


  <div class="card card-light">

    <div class="card-header">

      <h3 class="card-title"><span style="color: #28a745;" class="fas fa-users mr-3"></span>Listado de usuarios</h3>

      <div class="pull-right">

        <button type="button" class="btn btn-primary  float-right" data-toggle="modal" data-target="#modalCrearUsuario"> <span class="fa fa-list fa-fw"></span>
          Crear usuario
        </button> &nbsp;
      </div>


    </div>


    <div class="card-body">






<!-- ==================================

DATATABLE USUARIOS

====================================== -->


      <div class="row">
        <div class="col-lg-12">

          <table id="table_registros_usuarios" class="table dt-responsive table-hover" style="width:100%;font-size:12.5px;">
          
            <thead>

              <tr>

                <th>Nombre</th>

                <th>Email</th>

                <th>Rol</th>

                <th>Fecha de registro</th>

                <th></th>

              </tr>

            </thead>

          </table>


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



    <!--=====================================

    MODAL AGREGAR USUARIO

======================================-->

    <div class="modal fade" id="modalCrearUsuario" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">



      <div class="modal-dialog modal-mb">

        <div class="modal-content">

          <div class="modal-header">

            <h5 class="modal-title"><span style="color:#28a745;" class="fas fa-user mr-3"></span>Agregar usuario</h5>


            <button type="button" class="close" data-dismiss="modal" aria-label="Close">

              <span aria-hidden="true">&times;</span>

            </button>

          </div>


          <div class="modal-body">
            <form method="POST" id="formCrearUsuario">

              <div class="input-group mb-3">

                <input type="text" class="form-control text-capitalize" id="nombre" name="nombre" placeholder="Nombre completo" required>

                <div class="input-group-append">

                  <div class="input-group-text">

                    <span class="fas fa-user"></span>

                  </div>

                </div>

              </div>


              <div class="input-group mb-3">

                <input type="email" class="form-control" name="email" placeholder="Email" required>

                <div class="input-group-append">

                  <div class="input-group-text">

                    <span class="fas fa-envelope"></span>

                  </div>
                </div>

              </div>



              <div class="input-group mb-3">

              <select id="rol" name="rol" class="form-control" placeholder="roles" required>

                      <option value="">Seleccionar rol</option>

                      <option value="admin">Admin</option>

                      <option value="user">Usuario</option>


               </select>

              <div class="input-group-append">

                <div class="input-group-text">

                  <span class="fas fa-id-card"></span>

                </div>
              </div>

              </div>



              <div class="input-group mb-3">

                <input type="password" data-toggle="password" data-message="Mostrar/Ocultar Contraseña" class="form-control" id="clave" name="clave" placeholder="Contraseña" required>

                <!--
            <div class="input-group-append">

              <div class="input-group-text">

                 <span class="fas fa-lock"></span>

              </div>

            </div>
           
         -->


              </div>


              <div class="input-group mb-3">

                <input type="password" data-toggle="password" data-message="Mostrar/Ocultar Contraseña" data-validation="confirmation" class="form-control" id="repetir_clave" name="repetir_clave" placeholder="Repetir contraseña" required autocomplete="off">

                <div style="margin-top: 7px;" id="CheckPasswordMatch"></div>
                <!--
            <div class="input-group-append">

              <div class="input-group-text">

                 <span class="fas fa-lock"></span>

              </div>

            </div>
           
         -->

              </div>




              <div class="modal-footer">

                <button type="submit" id="agregar_usuario" name="agregar_usuario" class="btn btn-primary loader">Guardar</button>
                <button type="button" id="salir" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

              </div>

          </div>
        </div>
      </div>

      </form>
    </div>


</div>



<!--=====================================

    MODAL EDITAR USUARIO

======================================-->

<div class="modal fade" id="modalEditarUsuario" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">


  <div class="modal-dialog modal-lg">

    <div class="modal-content">

      <div class="modal-header">

        <h5 class="modal-title"><span style="color:#28a745;" class="fas fa-user mr-3"></span>Editar datos del usuario</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">

          <span aria-hidden="true">&times;</span>

        </button>

      </div>

      <div class="modal-body">

        <form method="POST" id="formEditarUsuario">

          <div class="input-group mb-3">

            <input type="text" class="form-control text-capitalize" id="nombre" name="nombre" placeholder="Nombre completo" required>

            <div class="input-group-append">

              <div class="input-group-text">

                <span class="fas fa-user"></span>

              </div>

            </div>

          </div>


          <div class="input-group mb-3">

            <input type="email" class="form-control" name="email" placeholder="Email" required>

            <div class="input-group-append">

              <div class="input-group-text">

                <span class="fas fa-envelope"></span>

              </div>
            </div>

          </div>




          <div class="input-group mb-3">

          <select id="rol" name="rol" class="form-control" placeholder="roles">

                     

                      <option value="admin">Admin</option>

                      <option value="user">Usuario</option>


               </select>

          <div class="input-group-append">

            <div class="input-group-text">

              <span class="fas fa-id-card"></span>

            </div>
          </div>

          </div>

          

          <div class="input-group mb-3">

            <input type="password" data-toggle="password" data-message="Mostrar/Ocultar Contraseña" class="form-control" id="clave2" name="clave" placeholder="Contraseña" required>

            <!--
            <div class="input-group-append">

              <div class="input-group-text">

                 <span class="fas fa-lock"></span>

              </div>

            </div>
           
         -->


          </div>


          <div class="input-group mb-3">

            <input type="password" data-toggle="password" data-message="Mostrar/Ocultar Contraseña" data-validation="confirmation" class="form-control" id="repetir_clave2" name="repetir_clave" placeholder="Repetir contraseña" required autocomplete="off">

            <div style="margin-top: 7px;" id="CheckPasswordMatch2"></div>
            <!--
            <div class="input-group-append">

              <div class="input-group-text">

                 <span class="fas fa-lock"></span>

              </div>

            </div>
           
         -->

            <input type="hidden" name="id_usuario" id="id_usuario">

          </div>






          <div class="modal-footer">

            <button type="submit" id="editar_usuario" name="editar_usuario" class="btn btn-primary loader">Guardar</button>
            <button type="button" id="salir" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

          </div>

      </div>
    </div>
  </div>

  </form>
</div>


</div>





<!-- ========================================

MOSTRAR SPINNER AL CARGAR PAGINA

========================================== -->


<script type="text/javascript">

  $(window).on('load', function() {

    setTimeout(function() {

      $(".loader-page").css({

        visibility: "hidden",

        opacity: "0"
      })

    }, 1000);

  });

</script>





<!-- ================================================

 FUNCIÓN FOCUS PARA PRIMER INPUT modalEditarCliente

 ================================================= -->


<script>

  $(document).ready(function() {

    $('#modalCrearUsuario').on('shown.bs.modal', function() {

      $('#nombre').focus();


    });
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




<!-- =======================================

MOSTRAR / OCULTAR CLAVE

============================================ -->

<script>

  $(function() {

    $('#clave').password()

  })

</script>


<!-- =======================================

CONFIRMAR CLAVE - GUARDAR

============================================ -->


<script type="text/javascript">

  $(document).ready(function() {

    $("#repetir_clave").on('keyup', function() {


      var user_pass = $("#clave").val();

      var user_pass2 = $("#repetir_clave").val();

      if (user_pass.length == 0) {

        $("#CheckPasswordMatch").html("El campo es oligatorio.").css("color", "red");

        $("#agregar_usuario").attr('disabled', true);

      } else if (user_pass == user_pass2) {

        $("#agregar_usuario").attr('disabled', false);

        $("#CheckPasswordMatch").html("Clave correcta.").css("color", "green");

      } else {

        $("#agregar_usuario").attr('disabled', true);

        $("#CheckPasswordMatch").html("La clave no coincide !").css("color", "red");
      }

    });

  });

</script>



<!-- =======================================

CONFIRMAR CLAVE -EDITAR

============================================ -->


<script type="text/javascript">

  $(document).ready(function() {

    $("#repetir_clave2").on('keyup', function() {


      var user_pass = $("#clave2").val();

      var user_pass2 = $("#repetir_clave2").val();

      if (user_pass.length == 0) {

        $("#CheckPasswordMatch2").html("El campo es oligatorio.").css("color", "red");

        $("#editar_usuario").attr('disabled', true);

      } else if (user_pass == user_pass2) {

        $("#editar_usuario").attr('disabled', false);

        $("#CheckPasswordMatch2").html("Clave correcta.").css("color", "green");

      } else {

        $("#editar_usuario").attr('disabled', true);

        $("#CheckPasswordMatch2").html("La clave no coincide !").css("color", "red");
      }

    });

  });

</script>





<!-- =======================================

MOSTRAR / OCULTAR CLAVE

============================================ -->

<script>

  $(function() {
    
    $('#repetir_clave').password()

  })

</script>



<!-- ===================================================

 DATATABLE USUARIOS

======================================================= --->

<script type="text/javascript">

  $(document).ready(function() {


    $.ajaxSetup({

      headers: {

        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        
      }
    });


    let table = $('#table_registros_usuarios').DataTable({

      processing: true,
      serverSide: true,

      type: "GET",
      ajax: 'listado_usuarios',




      columns: [


        {
          data: 'name',

          name: 'name'

        },

        {
          data: 'email',

          name: 'email'
        },

        {
          data: 'rol',

          name: 'rol'
        },
        
        {
          data: 'created_at',

          name: 'created_at',

          orderable: true
        },

        {
          data: 'action',

          name: 'action',

          orderable: false,
          
          searchable: false
        },


      ],

      order: [2, 'asc'],

          
      "language": {


        "emptyTable": "No hay usuarios registrados.",
        "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
        "infoEmpty": "Mostrando 0 a 0 de 0 Entradas",
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




    // =========================================

    /// GUARDAR REGISTROS DE USUARIO

    // =========================================

    $('#formCrearUsuario').off('submit').on('submit', function(event) {

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      /* Configurar botón submit con spinner */
      let btn = $('#agregar_usuario')

      let existingHTML = btn.html() //store exiting button HTML
      //Add loading message and spinner
     
      $(btn).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Procesando...').prop('disabled', true)
    
      setTimeout(function() {
    
        $(btn).html(existingHTML).prop('disabled', false) //show original HTML and enable
    
      }, 5000) //5 seconds
   
      $('#agregar_usuario').attr('disabled', true);

      event.preventDefault();

      try {

        $.ajax({

          url: "crear_usuario",

          method: "POST",

          data: $(this).serialize(),

          dataType: "json",

          success: function(data) {

            table.ajax.reload();

            $('#agregar_usuario').prop("required", true);
            // $('#selectBuscarCliente').html("");

            $('#formCrearUsuario')[0].reset();

            $('#modalCrearUsuario').modal('hide');

            //   table.ajax.reload();
            //   location.reload(true);
            toastr["success"]("Usuario creado correctamente.");

          }
        });
      } catch (e) {

        toastr["danger"]("Se ha presentado un error.", "Información");

      }

    });




    // =========================================

    /// EDITAR REGISTROS DE USUARIO

    // =========================================

    $('body').on('click', '.editarUsuario', function(e) {

      e.preventDefault();

      $('#formEditarUsuario')[0].reset();

      let id = $(this).data('id');

      $('#CheckPasswordMatch2').html('');

      $.ajax({

        url: 'editar_usuario/' + id,

        method: 'GET',

        data: {

          id: id

        },


        success: function(data) {

          $('#modalEditarUsuario').modal('show');

          $('#modalEditarUsuario input[name="id_usuario"]').val(data.id);

         $('#modalEditarUsuario input[name="nombre"]').val(data.name);

          $('#modalEditarUsuario input[name="email"]').val(data.email);

          $('#modalEditarUsuario select[name="rol"]').val(data.rol);

          $('#modalEditarUsuario input[name="clave"]').val(data.password);


        }

      });

    });




    // =========================================

    // ACTUALIZAR DATOS DE ABONO

    // =========================================


    $('#formEditarUsuario').off('submit').on('submit', function(event) {

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      /* Configurar botón submit con spinner */
      let btn = $('#editar_usuario')

      let existingHTML = btn.html() //store exiting button HTML
      //Add loading message and spinner

      $(btn).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Procesando...').prop('disabled', true)
     
      setTimeout(function() {

        $(btn).html(existingHTML).prop('disabled', false) //show original HTML and enable
    
      }, 5000) //5 seconds

      $('#editar_usuario').attr('disabled', true);

      event.preventDefault();

      try {

        let id = $(this).data('id');

        $.ajax({

          url: 'actualizar_usuario/' + id,

          method: "POST",

          data: $(this).serialize(),

          dataType: "json",

          success: function(data) {

            $('#editar_usuario').prop("required", true);
            // $('#selectBuscarCliente').html("");

            $('#formEditarUsuario')[0].reset();

            $('#modalEditarUsuario').modal('hide');

            table.ajax.reload();
            //   location.reload(true);
            toastr["success"]("datos actualizados correctamente.");

          }
        });
      } catch (e) {
        toastr["danger"]("Se ha presentado un error.", "Información");
      }
    });





    // =========================================

    /// ELIMINAR USUARIO

    // =========================================   



    $(document).on('click', '.eliminarUsuario', function(event) {

      event.preventDefault();

      let id = $(this).data('id');

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

            url: '/eliminar_usuario/' + id,

            data: {

              id: id

            },
            dataType: 'JSON',
            success: function(data) {

              //   if (data.success === true) {

              swal("Usuario eliminado correctamente!", data.message, "success");

              table.ajax.reload();
              //  $('#table_mascotas').html(data);


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



  });

</script>


@livewireScripts


@endsection