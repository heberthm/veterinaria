<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name') }}</title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"
          integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog=="
          crossorigin="anonymous"/>

          @vite(['resources/css/app.css'])

        
</head>
<body class="hold-transition login-page">

<div class="login-box">

    <div class="login-logo">
        
        <a href="{{ url('/inicio') }}"><b>{{ config('app.name') }}</b></a>
    </div>
    <!-- /.login-logo -->

    <!-- /.login-box-body -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg"></p>

            <form method="post" action="{{ url('/login') }}" id="form-login">
                @csrf

                <div class="input-group mb-3">
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="Email"
                           class="form-control @error('email') is-invalid @enderror">
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                    @error('email')
                    <span class="error invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-group mb-3">
                    <input type="password"
                           name="password"
                           placeholder="clave"
                           class="form-control @error('password') is-invalid @enderror">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @error('password')
                    <span class="error invalid-feedback">{{ $message }}</span>
                    @enderror

                </div>

                <div class="row">
                    <div class="col-8">
                        



                    </div>

                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Entrar</button>
                    </div>

                </div>
            </form>

            <p class="mb-1">
                <a href="{{ route('password.request') }}">He olvidado la clave</a>
            </p>
          
        </div>
        <!-- /.login-card-body -->
    </div>

</div>
<!-- /.login-box -->

@vite(['resources/js/app.js'])


<script>




  </script>

</body>
</html>
