@extends('layouts.coordComple')
@section('content')

	<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Editar Carrera </div>
					<div class="card-body">
					@foreach($carrera as $c)
						<form  method="POST" action="{{ url('/update/carrera').'/'.$c->id_carrera }}" class="needs-validation">
						@endforeach
							@csrf
                            @foreach($carrera as $c)
							<div class="row">
								<div class="form-group col">
									<label for="nombre">* Nombre:</label>
									<input type="text" class="form-control text-uppercase" value="{{ $c->nombre }}" pattern="{[A-Z][a-z]+}+ *" name="nombre" required>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="nombre">* Departamento:</label>
									<input type="text" class="form-control" value="{{ $c->depto }}" name="depto" disabled>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

							<div class="row">
									<label > <strong>* Campos Obligatorios</strong> </label>
							</div>
							
                            @endforeach
							<center> 
                                <button type="button" data-toggle="modal" data-target="#edit" class="btn btn-outline-primary"> Guardar </button>
                                <a href="{{ URL::previous() }}" class="btn btn-outline-danger"> Cancelar </a> 
							</center>
							
							<div class="modal fade" id="edit" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
								<div class="modal-dialog modal-dialog-centered">
									<div class="modal-content">
										<div class="modal-header" style="background-color: #1B396A;">
											<h5 class="modal-title text-white" id="staticBackdropLabel"><strong>EDITAR CARRERA</strong></h5>
											<button class="close text-white" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>  </button>
										</div>
										<div class="modal-body">
											<center>INTENTAS EDITAR UN(A) CARRERA <br> ¿ESTÁS SEGURO DE ESTA ACCIÓN?</center><br>

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
				</div>
			</div>
		</div>
	</div>


@endsection