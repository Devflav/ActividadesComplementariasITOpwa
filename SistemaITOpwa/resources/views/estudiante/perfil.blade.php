@extends('layouts.estudiante')
@section('content')

	<div class="container" style="padding-bottom: 55px;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Mis Datos Generales </div>
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
									<input type="text" class="form-control" value="{{ $e->carrera }}"   name="carrera" disabled>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="semestre">Semestre:</label>
									<input type="text" class="form-control" value="{{ $e->semestre }}"   name="semestre" disabled>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="curp">CURP:</label>
									<input type="text" class="form-control text-uppercase" value="{{ $e->curp }}" name="curp" disabled>
								</div>
							</div>


							<center> 
							<a href="{{ url('Est/editpasswd') }}" class="btn btn-outline-primary"> Cambiar contraseña </a>
							<a href="{{ url('Est/editar') }}" class="btn btn-outline-primary"> Editar </a>
                            <a href="{{ url('Est') }}" class="btn btn-outline-danger"> Regresar </a> </center>
						@endforeach
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection