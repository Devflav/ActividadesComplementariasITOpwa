@extends('layouts.estudiante')
@section('content')

	<div class="container" style="padding-bottom: 55px;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Actualizar mis datos Generales </div>
					<div class="card-body">
					
						<form method="POST" action="{{url('/Enviar/nuevoEst')}}" class="needs-validation">
							@csrf
							@foreach($estudiante as $e)
							<div class="row">
								<div class="form-group col">
									<label for="nControl">Número de Control:</label>
									<input type="text" class="form-control" value="{{ $e->ncontrol }}" name="nControl" disabled>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="nombre">Nombre (s):</label>
									<input type="text" class="form-control" value="{{ $e->nombre }}" name="nombre" disabled>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="apePat">Apallido Paterno:</label>
									<input type="text" class="form-control" value="{{ $e->paterno }}" name="apePat" disabled>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="apeMat">Apellido Materno:</label>
									<input type="text" class="form-control" value="{{ $e->materno }}" name="apeMat" disabled>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="carrera">Carrera:</label>
									<input type="text" class="form-control" value="{{ $e->carrera }}" name="carrera" disabled>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="semestre">Semestre:</label>
									<input type="text" class="form-control" value="{{ $e->semestre }}" name="semestre" disabled>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="curp">CURP:</label>
									<input type="text" class="form-control text-uppercase" value="{{ $e->curp }}" pattern="[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9]{2}" minlength="18" maxlength="18" name="curp">
								</div>
							</div>


							<center> <button type="submit" class="btn btn-outline-primary"> Guardar </button>
                            <a href="{{ url('Est/perfil') }}" class="btn btn-outline-danger"> Cancelar </a> </center>
						@endforeach
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection