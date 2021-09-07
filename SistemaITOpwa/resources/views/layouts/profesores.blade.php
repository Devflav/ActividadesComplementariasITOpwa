<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="author" content="Fernando Altamirano" />
	<title>Responsable de Actividad</title>
	<!-- Scripts -->
		<script src="{{ asset('mijs/app.js') }}"></script>
		<script src="{{ asset('mijs/ito_ac.js') }}"></script>
		
	<!-- Fuentes -->

		<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

		<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script> -->
		
	<!-- Icons -->
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		
	<!-- Estilos -->
		<link href="{{ asset('micss/app.css') }}" rel="stylesheet">
		<link href="{{ asset('micss/styles.css') }}" rel="stylesheet">
		
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
							<a class="nav-link" href="{{ url('ProfR') }}"><i class="fa fa-fw fa-fw fa-lg fa-home"></i>Inicio</a>
						</li>
            <li class="nav-item">
							<a class="nav-link" href="{{ url('ProfR/grupos') }}"><i class="fa fa-fw fa-users"></i>Mis Grupos</a>
						</li>
            <li class="nav-item">
							<a class="nav-link" href="{{ url('ProfR'). '/evaluar' }}"><i class="fa fa-fw fa-lg  fa-wpforms"></i> Evaluar</a>
						</li>
			<li class="nav-item">
							<a class="nav-link" href="{{ url('ProfR'). '/constancia' }}"><i class="fa fa-fw fa-lg  fa-file-text-o"></i>Constancias</a>
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
								<a class="dropdown-item text-primary" href="{{ url('ProfR/datosGen') }}"><i class="fa fa-fw fa-lg  fa-cog"></i>Mi perfil</a>
								
								<a class="dropdown-item text-primary" href="{{ url('logoutPR') }}" onclick="event.preventDefault(); $('#logout-form').submit();">
									<strong><i class="fa fa-fw fa-lg  fa-sign-out"></i> Cerrar sesión </strong>
								</a>

								<form id="logout-form" action="{{ url('logoutPR') }}" method="POST" style="display: none;">
									@csrf
								</form>
							</div>
						</li>
						@endguest
					</ul>
				</div> 
		</nav >

		<main class="py-4">
      		<center> <h5>Coordinación de Actividades Complementarias </h5> </center>
			  <br>
			@yield('content')
		</main>

		<div id="general-footer" class="container-fluid gen-footer">
			<hr>
			<div class="group-footer">
				<div class="col-sm">
					<label>
						<i class="bi bi-geo-alt-fill"></i>
						Avenida Ing. Víctor Bravo Ahuja No. 125 Esquina Calzada Tecnológico, C.P. 68030
					</label>
				</div>
			</div>

			<div class="group-footer">
				<div class="col-sm">
					<label>
						<i class="bi bi-envelope-fill"></i>
						Email: tec_oax@itoaxaca.edu.mx
					</label>    
				</div>
				<div class="col-sm">
					<label>
						<i class="bi bi-telephone-fill"></i>
						Tel: (951) 501 50 16
					</label>    
				</div>
			</div>
	    </div>
	</div>
</body>

</html>
