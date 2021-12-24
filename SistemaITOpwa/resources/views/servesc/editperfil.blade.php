@extends('layouts.servesc')
@section('content')
<div class="container form-content col-sm-9">
@if (session('Catch') != null)
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<h5 class="alert-heading"> 
				<strong> <em> ¡ Error ! </em> </strong>
				<i class="bi bi-x-octagon-fill close float-right" type="button" data-dismiss="alert"></i>
			</h5>
			<hr>
			<p class="text-justify"> {{ session('Catch') }} </p>
		</div>
    @endif
	<div class="form-group">
		<div class="col-sm">
					<div class="card-header"> Mis Datos Generales </div>
        </div>
    </div>
    <div class="card-body">
		<form method="POST" action="{{url('/servesc/editperf')}}" class="needs-validation">
			@foreach($persona as $p)
			<div class="form-group">
				<div class="col-sm">
					<label for="nControl">* Grado:</label>
					<select class="form-control" id="grado" name="grado" required> 
						<option value="{{ $p->id_grado}}"> {{ $p->grado }} </option>
						@foreach($grados as $g)
							<option value="{{$g->id_grado}}" require> {{ $g->nombre }} </option>
						@endforeach
					</select>
					<div class="valid-feedback"></div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="nombre">* Nombre (s):</label>
					<input type="text" class="form-control" value="{{ $p->nombre }}" 
						pattern="{[A-Z][a-z]+}+ *" minlength="4" name="nombre" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="apePat">* Apallido Paterno:</label>
					<input type="text" class="form-control" value="{{ $p->paterno }}" 
						pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apePat" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="apeMat">* Apellido Materno:</label>
					<input type="text" class="form-control" value="{{ $p->materno }}" 
						pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apeMat" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="carrera"> Departamento:</label>
					<input type="text" class="form-control" value="{{ $p->depto }}" name="depto" disabled>
				</div>
				<div class="col-sm">
					<label for="semestre"> Puesto:</label>
					<input type="text" class="form-control" value="{{ $p->puesto }}" name="puesto" disabled>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="curp">* CURP:</label>
					<input type="text" class="form-control text-uppercase" value="{{ $p->curp }}" 
						pattern="[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9]{2}" minlength="18" maxlength="18" 
						name="curp" required>
				</div>
				<div class="col-sm">
				</div>
			</div>
			@endforeach
			<div class="form-group">
				<div class="col-sm">
					<label > * Campos Obligatorios </label>
				</div>
			</div>
			<div class="container">
				<div class="form-group">
					<div class="col-sm"></div>
					<br>
					<div class="col-sm">
						<button type="submit" class="btn btn-outline-primary"> 
							Actualizar 
						</button>
					</div>
					<br>
					<div class="col-sm">
						<a href="{{ url('ServEsc/datosGen') }}" class="btn btn-outline-danger"> 
							Cancelar 
						</a>
					</div>
					<div class="col-sm"></div>
				</div>
			</div>
		</form>
    </div>
</div> 
@endsection