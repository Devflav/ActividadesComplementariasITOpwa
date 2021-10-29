@extends('layouts.coordComple')
@section('content')
<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Editar Grado </div>
        </div>
    </div>
    <div class="card-body">
	@foreach($grado as $g)
        <form method="POST" action="{{ url('/update/grado').'/'.$g->id_grado }}" class="needs-validation">
            @csrf
            <div class="form-group">
                <div class="col-sm">
					<label for="nombre">* Grado:</label>
					<input type="text" class="form-control text-uppercase" 
					value="{{ $g->nombre }}" 
					pattern="{[A-Z][a-z]+}+ *" name="nombre" 
					required>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>	
                </div>
                <div class="col-sm">
					<label for="nombre">* Descripción:</label>
					<input type="text" class="form-control text-uppercase" 
					value="{{ $g->significado }}" pattern="{[A-Z][a-z]+}+ *" 
					name="significado" required>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
		@endforeach
            <div class="form-group">
                <div class="col-sm">
					<label > <strong>* Campos Obligatorios</strong> </label>
                </div>
            </div>
            <div class="container">
                <div class="form-group">
                    <div class="col-sm"></div>
                    <div class="col-sm">
                        <button type="button" data-toggle="modal" data-target="#edit" 
							class="btn btn-outline-primary"> 
                            Guardar
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
								<strong>EDITAR GRADO</strong>
							</h5>
							<button class="close text-white" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>  </button>
						</div>
						<div class="modal-body text-center">
							INTENTAS EDITAR UN(A) GRADO <br> ¿ESTÁS SEGURO DE ESTA ACCIÓN?<br>

						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-outline-primary" data-dismiss="modal">
								Cerar
							</button>
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