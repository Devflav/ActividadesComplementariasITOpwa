@extends('layouts.coordComple')
@section('content')
<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Editar Carrera </div>
        </div>
    </div>
	<div class="card-body">
	@foreach($carrera as $c)
		<form  method="POST" action="{{ url('/update/carrera').'/'.$c->id_carrera }}" class="needs-validation">
	@endforeach
		@csrf
		@foreach($carrera as $c)
			<div class="form-group">
				<div class="col-sm">
					<label for="nombre">* Nombre:</label>
					<input type="text" class="form-control text-uppercase" value="{{ $c->nombre }}" 
						pattern="{[A-Z][a-z]+}+ *" name="nombre" required>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>	
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="nombre">* Departamento:</label>
					<input type="text" class="form-control" value="{{ $c->depto }}" name="depto" disabled>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>	
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label > <strong>* Campos Obligatorios</strong> </label>
				</div>

			</div>
		@endforeach
			<div class="container">
				<div class="form-group">
					<div class="col-sm"></div>
					<div class="col-sm">
						<button type="button" data-toggle="modal" data-target="#edit" 
							class="btn btn-outline-primary"> Guardar 
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
			<div class="modal fade" id="edit" data-backdrop="static" data-keyboard="false" 
				tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header" style="background-color: #1B396A;">
							<h5 class="modal-title text-white" id="staticBackdropLabel">
								<strong>EDITAR CARRERA</strong>
							</h5>
							<button class="close text-white" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>  </button>
						</div>
						<div class="modal-body text-center">
							INTENTAS EDITAR UN(A) CARRERA <br> ¿ESTÁS SEGURO DE ESTA ACCIÓN?<br>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cerar</button>
							<button type="submit" class="btn btn-outline-danger">Editar</button>
						</div>
					</div>
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