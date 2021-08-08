@extends('layouts.divEProf')
@section('content')

	<div class="container" style="padding-bottom: 55px;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Actualizar mi Contraseña </div>
					<div class="card-body">
					
						<form method="POST" action="{{url('/DivEProf/cac/editpasswd')}}" class="needs-validation">
							@csrf
							<div class="row">
								<div class="form-group col">
									<label for="nControl">Contraseña actual:</label>
									<input type="text" class="form-control" minlength="8" name="passactual" placeholder="Escribe tu contraseña actual" required>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="apePat">Nueva contraseña:</label>
									<input type="text" class="form-control" minlength="8" name="passnueva" placeholder="Escribe tu nueva contraseña" required>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="carrera">Confirmar contraseña:</label>
									<input type="password" class="form-control" name="passconfir" placeholder="Confirma tu nueva contraseña" required>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>
							<center> <button type="submit" class="btn btn-outline-primary"> Actualizar </button>
                            <a href="{{ url('DivEProf/datosGen') }}" class="btn btn-outline-danger"> Cancelar </a> </center>

						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection