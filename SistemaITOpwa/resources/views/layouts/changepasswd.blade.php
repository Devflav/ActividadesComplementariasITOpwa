<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="author" content="Fernando Altamirano" />
	<title>Actividades Complementarias</title>

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

		<main class="py-4">
			<center><h6>TECNOL??GICO NACIONAL DE M??XICO CAMPUS OAXACA - 
						INSTITUTO TECNOL??GICO DE OAXACA <br> 
						ACTIVIDADES COMPLEMENTARIAS</h6></center>
			<br>
				@yield('content')
		</main>

		<div id="general-footer" class="container-fluid gen-footer">
			<hr>
			<div class="group-footer">
				<div class="col-sm">
					<label>
						<i class="bi bi-geo-alt-fill"></i>
						Avenida Ing. V??ctor Bravo Ahuja No. 125 Esquina Calzada Tecnol??gico, C.P. 68030
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
