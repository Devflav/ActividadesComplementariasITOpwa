@extends('layouts.presentacion')
@section('content')
<div class="container form-content col-sm-10">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header">
				Iniciar Sesión
			</div>
			<div class="card-body">
				<div class="justify-content-center">
					<br>
					<img class="rounded mx-auto d-block" src="../images/ac_ito/usuario.png" width="20%">
				</div>
				<br>
				<form method="POST" action="{{ url('/Acceso') }}">
				@csrf
					<div class="form-group">
						<div class="col-sm">
							<div class="input-container">
								<i class="bi bi-person-fill icon"></i>
								<input type="text" class="input-field" 
								name="usuario" value="{{ old('usuario') ?: old('email') }}" 
								placeholder="Usuario" required autofocus>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm">
							<div class="input-container">
								<i class="bi bi-key-fill icon"></i>
								<input type="password" class="input-field" 
								name="password" placeholder="Contraseña"  required autofocus>
							</div>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-6 offset-md-4">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="remember" 
									{{ old('remember') ? 'checked' : '' }}> Recordarme
								</label>
							</div>
						</div>
					</div>
					<div class="container justify-content-center">
						<div class="form-group">
							<div class="col-sm">
								<br>
								<button type="submit" class="btn btn-outline-primary form-control"> 
									Iniciar sesión
								</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="col-sm">
			<div class="card-header">
				Registrate
			</div>
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
				<a href="{{ url('/Registrarse') }}" class="btn btn-outline-primary form-control">
					Registrarme
				</a>
			</div>
		</div>
	</div>
</div>
@endsection