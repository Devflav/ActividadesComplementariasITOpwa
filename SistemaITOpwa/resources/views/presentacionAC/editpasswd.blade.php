@extends('layouts.changepasswd')
@section('content')

	<div class="container" style="padding-bottom: 55px;">
		<div class="row justify-content-center">
			<div class="col-sm-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header"> ACTUALIZAR CONTRASEÑA PARA PODER ACCEDER AL SISTEMA </div>
					<div class="card-body">
					
						<form method="POST" action="{{ url('/change/passwd') }}" class="was-validated">
							@csrf
							<div class="form-group">
								<div class="col-sm">
									<label for="passwd">Nueva contraseña:</label>
									<input type="text" class="form-control" minlength="8" name="passwd" placeholder="Escribe tu nueva contraseña" minlength="8" maxlength="18" required>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Mínimo 8 y máximo 18 caracteres.</div>
								</div>
								<div class="col-sm">
									<label for="carrera">Confirmar contraseña:</label>
									<input type="password" class="form-control" name="pswdconfirm" placeholder="Confirma tu nueva contraseña" minlength="8" maxlength="18" required>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Mínimo 8 y máximo 18 caracteres.</div>
								</div>
							</div>

							<center> <button type="submit" class="btn btn-outline-primary"> Actualizar </button>

						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection