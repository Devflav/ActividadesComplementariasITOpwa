@extends('layouts.divEProf')
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
			<div class="card-header"> Nuevo Criterio de Evaluación </div>
        </div>
    </div>
	<div class="card-body">
		<form method="POST" action="{{url('DivEProf/regCritE')}}" class="needs-validation">
			@csrf
			<div class="form-group">
				<div class="col-sm">
					<label for="nomCritE">* Nombre:</label>
					<input type="text" class="form-control text-uppercase" 
						placeholder="Escribe el nombre del criterio de evaluación" 
						pattern="{[A-Z][a-z]+}+ *" name="nombre" id="nomCritE"
						data-toggle="tooltip" title="Responsabilidad" required>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>	
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="desCritE">* Descripción:</label>
					<textarea type="text" class="form-control text-uppercase" 
						placeholder="Escribe la descripción del criterio de evaluación" 
						title="Muestra responsabilidad en las actividades realizadas"
						pattern="{[A-Z][a-z]+}+ *" name="descripcion" required></textarea>
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