@extends('layouts.coordComple')
@section('content')
<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Nuevo Departamento </div>
        </div>
    </div>
    <div class="card-body">
		<form method="POST" action="{{ url('/regDepto') }}" class="needs-validation">
			@csrf
			<div class="form-group">
				<div class="col-sm">
					<label for="nombre">* Nombre:</label>
					<input type="text" class="form-control text-uppercase" 
					placeholder="Escribe el nombre del departamento" 
					title="Departamento de gestión económica"
					pattern="{[A-Z][a-z]+}+ *" name="nomDepto" required>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="nombre">* Jefe de Departamento:</label>
					<select class="form-control" id="persona" name="persona" required> 
						<option value="" >Selecciona el nuevo Jefe</option>
						@foreach($jefes as $j)
							<option value="{{$j->id_persona }}" required> 
								{{ $j->grado }} {{ $j->nombre }} {{ $j->apePat }} {{ $j->apeMat }}
							</option>
						@endforeach
					</select>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>		
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
						<a href="{{ URL::previous() }}" class="btn btn-outline-danger"> 
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