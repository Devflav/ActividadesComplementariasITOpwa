<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="author" content="Fernando Altamirano" />
	<title>Jefes Departamento</title>
	<!-- Scripts -->
		<script src="{{ asset('mijs/app.js') }}"></script>
		<script src="{{ asset('mijs/ito_ac.js') }}"></script>
		<script src="{{ asset('mijs/fx_xiuxian.min.js') }}"></script>
		<!-- Fuentes -->

		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

		<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
		
		<!-- Icons -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		
		<!-- Estilos -->
		<link href="{{ asset('micss/app.css') }}" rel="stylesheet">
		<link href="{{ asset('micss/imagenes.css') }}" rel="stylesheet">
		
	<!-- Laravel PWA assets -->
	@laravelPWA
</head>

<body id="bodies" style="background-image: url('/images/ac_ito/escudoNac.png'); background-position: left; background-repeat: no-repeat; background-attachment: fixed; padding-bottom: 10px;">
	<div id="app">
		<div class="" style="padding-top: 15px; padding-bottom: 10px"> <!--  justify-content-center align-content-center -->
			<div imagenes img-responsive>
				<img id="sep" src="{{ asset('images/ac_ito/Sep.png')}}">
				<img id="logo" src="{{ asset('images/ac_ito/logoITO.png')}}">
				<img id="tecMexico" src="{{ asset('images/ac_ito/TecNM.png')}}">
				</div>
		</div>

		<nav class="navbar navbar-expand-md shadow-sm" style="background-color: transparent;"> <!-- navbar-dark bg-info shadow-sm" -->
				<!-- <div  class="collapse navbar-collapse" id="navbarSupportedContent"> -->
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
				<span class="navbar-toggler-icon"><i class="fa fa-fw fa-lg fa-bars text-primary"></i></span>
			</button>

			<div class="collapse navbar-collapse justify-content-center" id="navbarSupportedContent">

			<ul class="navbar-nav">	

            <li class="nav-item">
							<a class="nav-link" href="{{ url('JDepto') }}"><i class="fa fa-fw fa-lg  fa-home"></i>Inicio</a>
						</li>
            <li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-fw fa-lg  fa-bars"></i>Opciones</a>
							<div class="dropdown-menu border-bottom-0 border-left-0 border-right-0" style="background-color: white;">
								<a class="dropdown-item text-primary" href="{{ url('JDepto/actividad/1') }}">Actividades</a>	
								<a class="dropdown-item text-primary" href="{{ url('JDepto/grupos/1') }}">Grupos</a>
								<a class="dropdown-item text-primary" href="{{ url('JDepto/personal/1') }}">Personal</a>
								<a class="dropdown-item text-primary" href="{{ url('JDepto/estudiante/Hist') }}">Historial Est.</a>
							</div>
						</li>
			<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-fw fa-file-text-o"></i>Documentos</a>
							<div class="dropdown-menu border-bottom-0 border-left-0 border-right-0" style="background-color: white;">
								<a class="dropdown-item text-primary" href="{{ url('JDepto/constancia') }}">Constancia y Evaluación</a>
								<a class="dropdown-item text-primary" href="{{ url('JDepto/hmembretada') }}">Hoja membretada</a>
								<a class="dropdown-item text-primary" href="{{ url('JDepto/lista') }}">Descargar listas</a>
							</div>
						</li>            

            @guest
						<li class="nav-item">
							<a class="nav-link" href="{{ url('IniciarSesion') }}"><i class="fa fa-fw fa-lg  fa-user"></i>Iniciar sesión</a>
						</li>
						@else
						<li class="nav-item dropdown">
							<a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
							<i class="fa fa-fw fa-lg  fa-user"></i>{{ Auth::user()-> nombre }} <span class="caret"></span>
							</a>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
								<a class="dropdown-item text-primary" href="{{ url('JDepto/datgen') }}"><i class="fa fa-fw fa-lg  fa-cog"></i>Mi perfil</a>
								
								<a class="dropdown-item text-primary" href="{{ url('logoutJD') }}" onclick="event.preventDefault(); $('#logout-form').submit();">
									<strong><i class="fa fa-fw fa-lg  fa-sign-out"></i> Cerrar sesión </strong>
								</a>

								<form id="logout-form" action="{{ url('logoutJD') }}" method="POST" style="display: none;">
									@csrf
								</form>
							</div>
						</li>
						@endguest
					</ul>
				</div> 
		</nav >

		<main class="py-4">
      		<center> <h5>Actividades Complementarias - Jefe de Departamento </h5> </center>
			  <br>
			@yield('content')
		</main>

		<div style="padding-top: 20px;">
			<footer id="footer" class="bg-secondary text-white">
			Avenida Ing. Víctor Bravo Ahuja No. 125 Esquina Calzada Tecnológico, C.P. 68030 <br>
			Email: tec_oax@itoaxaca.edu.mx <label class="col-1"></label> Tel: (951) 501 50 16
			</footer>
		</div>
	</div>
</body>

</html>
