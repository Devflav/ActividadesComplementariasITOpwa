@extends('layouts.divEProf')
@section('content')
<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Nueva Carrera </div>
        </div>
    </div>
	<div class="card-body">
		<form method="POST" action="{{url('DivEProf/regCar')}}" class="needs-validation">
			@csrf
			<div class="form-group">
				<div class="col-sm">
					<label for="tipo">* Tipo</label>
					<select name="tipo" id="" class="form-control" required>
						<option value="" readonly>Selecciona un tipo</option>
						<option value="0">INGENIERÍA</option>
						<option value="1">LICENCIATURA</option>
					</select>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>	
				</div>
				<div class="col-sm">
					<label for="nombre" data-toggle="tooltip" title="¡Solo se admiten letras!">* Nombre:</label>
					<input type="text" class="form-control text-uppercase" 
						placeholder="Escribe el nombre de la carrera" 
						name="nombre" id="carrera" data-toggle="tooltip" 
						title="Ing/Lic Contabilidad Fianciera" required>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>	
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="apeMat">* Departamento:</label>
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
					<label > <strong> * Campos Obligatorios </strong> </label>
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
						<a href="{{ URL::previous() }}" class="btn btn-outline-danger"> 
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