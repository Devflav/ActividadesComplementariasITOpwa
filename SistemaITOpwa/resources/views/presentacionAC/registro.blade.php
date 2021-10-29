@extends('layouts.presentacion')
@section('content')
<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> 
				<h6>REGISTRO DE ESTUDIANTES</h6>
			</div>
		</div>
    </div>
	<div class="card-body">
		<form method="POST" action="{{url('/Enviar/Registro')}}" class="was-validated">
		@csrf
			<div class="form-group">
				<div class="col-sm">
					<label for="num_control">* Número de Control:</label>
					<input type="text" class="form-control" id="nControl" placeholder="21160000" 
						pattern="[0-9]{8}|[C|B]{1}[0-9]{8}|[0-9]{9}" name="num_control" required>
					<div class="valid-feedback">Formato valido.</div>
					<div class="invalid-feedback">Escribe tu Número de Control</div>
				</div>
				<div class="col-sm">
					<label for="nombre">* Nombre (s):</label>
					<input type="text" class="form-control" id="nombre" placeholder="Augusta" 
						pattern="[a-zA-Z ]*" minlength="4" name="nombre" required>
					<div class="valid-feedback">Formato valido.</div>
					<div class="invalid-feedback">Escribe tu(s) Nombre(s)</div>	
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="apePat">* Apallido Paterno:</label>
					<input type="text" class="form-control" id="apePat" placeholder="Ada" 
						pattern="[a-zA-Z ]*" minlength="4" name="apePat" required>
					<div class="valid-feedback">Formato valido.</div>
					<div class="invalid-feedback">Escribe tu Apellido Paterno</div>
				</div>
				<div class="col-sm">
					<label for="apeMat">* Apellido Materno:</label>
					<input type="text" class="form-control" id="apeMat" placeholder="King" 
						pattern="[a-zA-Z ]*" minlength="4" name="apeMat" required>
					<div class="valid-feedback">Formato valido.</div>
					<div class="invalid-feedback">Escribe tu Apellido Materno</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="carrera">* Carrera:</label>
					<select class="form-control" id="carrera" placeholder="Selecciona tu Carrera" 
						name="id_carrera" required> 
					<option value=""> Selecciona una Carrera </option>
						@foreach($carreras as $c)
							<option value="{{$c->id_carrera}}" require> {{ $c->nombre }} </option>
						@endforeach
					</select>
					<div class="valid-feedback">Formato valido.</div>
					<div class="invalid-feedback">Por favor selecciona una opción</div>
				</div>
				<div class="col-sm">
					<label for="semestre">* Semestre:</label>
					<select class="form-control" id="semestre" placeholder="Selecciona tu Semestre" 
						name="semestre" required> 
					<option value=""> Selecciona un Semestre </option>
						@foreach($semestres as $s)
							<option value="{{ $s }}" require> {{ $s }}° Semestre </option>
						@endforeach
					</select>
					<div class="valid-feedback">Formato valido.</div>
					<div class="invalid-feedback">Por favor selecciona una opción</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="email">* Correo Institucional:</label>
					<input type="email" class="form-control" id="email" 
						placeholder="21160000@itoaxaca.edu.mx" 
						pattern="[0-9]{8}@itoaxaca.edu.mx{1}|[0-9]{9}@itoaxaca.edu.mx{1}|[C|B]{1}[0-9]{8}@itoaxaca.edu.mx{1}"
						minlength="24" maxlength="26" name="email" required>
					<div class="valid-feedback">Formato valido.</div>
					<div class="invalid-feedback">Escribe tu correo INSTITUCIONAL</div>
				</div>
				<div class="col-sm">
					<label for="curp">CURP:</label>
					<input type="text" class="form-control" id="curp" placeholder="AAAA111111BBBBBB22" 
						pattern="[a-zA-Z0-9 ]*" minlength="18" maxlength="18" 
						name="curp">
					<div class="valid-feedback">Formato valido.</div>
					<div class="invalid-feedback">Puedes registrarla en otro momento</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label > <strong> * Campos Obligatorios</strong> </label>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label class="text-justify"> Para iniciar sesión la primera vez en el sistema, ocuparás como
					usuario tu <strong>correo institucional</strong> y como contraseña tu 
					<strong>número de control</strong>. Tras ingresar tendrás que cambiar
					tu contraseña obligatoriamente para poder hacer uso del sistema.  </label>
				</div>
			</div>
			<div class="container">
				<div class="form-group">
					<div class="col-sm"></div>
					<div class="col-sm">
						<button type="submit" class="btn btn-outline-primary"> 
							Registrarme
						</button>
					</div>
					<div class="col-sm"></div>
				</div>
			</div>
		</form>

		@if ($errors->any())
			@foreach ($errors->all() as $error)
				<div class="row">
					<div class="alert alert-danger">
						{{ $error }}
					</div>
				</div>
			@endforeach
		@endif

	</div>
</div>
@endsection

