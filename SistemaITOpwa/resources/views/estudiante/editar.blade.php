@extends('layouts.estudiante')
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
		<form method="POST" action="{{url('/Enviar/nuevoEst')}}" class="needs-validation">
			@csrf
			@foreach($estudiante as $e)
			<div class="form-group">
				<div class="col-sm">
					<label for="nControl">Número de Control:</label>
					<input type="text" class="form-control" value="{{ $e->ncontrol }}" name="nControl" disabled>
					<div class="valid-feedback">Valido</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>

				<div class="col-sm">
					<label for="nombre">Nombre (s):</label>
					<input type="text" class="form-control" value="{{ $e->nombre }}" name="nombre" disabled>
					<div class="valid-feedback">Valido</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>							
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm">
					<label for="apePat">Apallido Paterno:</label>
					<input type="text" class="form-control" value="{{ $e->paterno }}" name="apePat" disabled>
					<div class="valid-feedback">Valido</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>

				<div class="col-sm">
					<label for="apeMat">Apellido Materno:</label>
					<input type="text" class="form-control" value="{{ $e->materno }}" name="apeMat" disabled>
					<div class="valid-feedback">Valido</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm">
					<label for="carrera">Carrera:</label>
					<input type="text" class="form-control" value="{{ $e->carrera }}" name="carrera" disabled>
					<div class="valid-feedback">Valido</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>

				<div class="col-sm">
					<label for="semestre">Semestre:</label>
					<input type="text" class="form-control" value="{{ $e->semestre }}" name="semestre" disabled>
					<div class="valid-feedback">Valido</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm">
					<label for="curp">CURP:</label>
					<input type="text" class="form-control text-uppercase" value="{{ $e->curp }}" pattern="[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9]{2}" minlength="18" maxlength="18" name="curp">
				</div>
			</div>
			@endforeach
			<div class="container">
                <div class="form-group">
                    <div class="col-sm"></div>
                    <div class="col-sm">
                        <button type="submit" class="btn btn-outline-primary"> 
                            Guardar
                        </button>
                    </div>
                    <br>
                    <div class="col-sm">
                        <a href="{{ url('Est/perfil') }}" class="btn btn-outline-danger"> 
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