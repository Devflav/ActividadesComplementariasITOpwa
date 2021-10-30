@extends('layouts.divEProf')
@section('content')
<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Registrar Nueva Actvidad </div>
        </div>
    </div>
	<div class="card-body">
		<form method="POST" action="{{url('DivEProf/regAct')}}" class="needs-validation">
			@csrf
			<div class="form-group">
				<div class="col-sm">
					<label for="clave">* Clave:</label>
					<input type="text" class="form-control text-uppercase" 
						placeholder="Clave de la actividad" pattern="[A-Z]{3}[0-9]{2}|[a-z]{3}[0-9]{2}" 
						name="clave" required>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="nombre">* Nombre:</label>
					<input type="text" class="form-control text-uppercase" id="nombre" 
						placeholder="Nombre(s)" pattern="{[A-Z][a-z]+}+ *" name="nombre" required>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="credit">* Número de Créditos:</label>
					<select class="form-control" id="cred" name="creditos" require> 
						<option value=""> Selecciona los créditos </option>
						<option value="1" > Un crédito </option>
						<option value="2" > Dos créditos </option>
					</select>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="depto">* Departamento:</label>
					<select class="form-control" id="depto" name="id_depto" required> 
						<option value=""> Selecciona un Departamento </option>
						@foreach($deptos as $d)
							<option value="{{$d->id_depto}}" require> {{ $d->nombre }} </option>
						@endforeach
					</select>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="tipo">* Tipo de actividad:</label>
					<select class="form-control" id="tipo" name="id_tipo" required> 
						<option value=""> Selecciona un Tipo </option>
						@foreach($tipos as $t)
							<option value="{{$t->id_tipo}}" require> {{ $t->nombre }} </option>
						@endforeach
					</select>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="carrera">* Restringida:</label>
					<select class="form-control" id="restric" name="restringida" required> 
						<option value=""> Selecciona SI/NO </option>
						<option value="1" require> <strong>SI</strong> Restringida </option>
						<option value="0" require> <strong>NO</strong> Restringida </option>
					</select>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="periodo">* Periodo:</label>
					<input type="text" class="form-control" value="{{ $periodo->nombre }}" disabled>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="curp">Descripción:</label>
					<textarea type="" class="form-control" 
						placeholder="Escribe una descripción de la actividad" 
						maxlength="250" name="descripcion"></textarea>		
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label > 
						<strong>
							* Campos Obligatorios
							</strong> 
					</label>
				</div>
			</div>
			<div class="container">
				<div class="form-group">
					<div class="col-sm"></div>
					<div class="col-sm">
						<button type="submit" class="btn btn-outline-primary"> 
							Registrar
						</button>
					</div>
					<br>
					<div class="col-sm">
						<a href="{{ url('/DivEProf/actividades/1') }}" class="btn btn-outline-danger"> 
							Cancelar 
						</a> 
					</div>
					<div class="col-sm"></div>
				</div>
			</div>
		</form>
	</div>

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
@endsection