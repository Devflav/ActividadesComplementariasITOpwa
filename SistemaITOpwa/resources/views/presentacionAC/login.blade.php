@extends('layouts.presentacion')
@section('content')

<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 10%;">
	<div class="row justify-content-center">
		<div class="col-md-6">
			<div class="card  border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
				<div class="card-header text-white" style="background:#1B396A;">Iniciar Sesión</div>
				<div class="card-body text-justify">
					<form method="POST" action="{{ url('Acceso') }}">
						@csrf
						<br>
						<center><img src="../images/ac_ito/usuario.png" width="20%"> </center>
						<br><br><br>	
						<center>
						<div class="form-group row">
							<div class="col-md-8 offset-md-2">
								<input type="text" class="form-control col" name="usuario" value="{{ old('usuario') ?: old('email') }}" placeholder="Usuario" required autofocus>
							</div>
						</div>

						 <div class="form-group row">
							<div class="col-md-8 offset-md-2">
								<input type="password" class="form-control" id="password" name="password" placeholder="Contraseña"  required autofocus>
							</div>
						</div>
						</center>
						<div class="form-group row">
							<div class="col-md-6 offset-md-4">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>Recordarme
									</label>
								</div>
							</div>
						</div>
						<div class="form-group row mb-0">
							<div class="col-md-8 offset-md-4">
								<button type="submit" class="btn btn-outline-primary">
									Iniciar sesión
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
				<div class="card-header text-white" style="background:#1B396A;">Registrate</div>
				<div class="card-body text-justify" style="background-color: transparent;">
					<a>Para poder acceder al sistema, es necesario tener una cuenta, tómate un
						minuto para crear una. Sigue estos sencillos pasos.</a> <br><br><br>
					<ul>
						
							<li> Rellena el Formato de Registro con tus datos.</li>
							<li> Inicia sesión en el sistema.</li>
							<li> Al ser la primera vez que accedes al sistema tandrás que cambiar tu contraseña.</li>
							<li> Tu registro será confirmado y podrás acceder al sistema con tu nueva contraseña.</li>
							<li> A partir de ese momento sólo necesitarás utilizar tu nombre de usuario y tu
								contraseña en la página inicial para ingresar al sistema. </li><br>
						
					</ul>
					<center> <a href="{{ url('/Registrarse') }}" class="btn btn-outline-primary">Registrarme</a> </center>
				</div>
			</div>
		</div>
	</div>
@endsection