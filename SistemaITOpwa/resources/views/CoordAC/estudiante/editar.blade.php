@extends('layouts.coordComple')
@section('content')

	<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Actualizar datos del Estudiante </div>
					<div class="card-body">
					@foreach($estudiante as $e)
						<form method="POST" action="{{ url('/update/estudiante').'/'.$e->id_persona }}" class="needs-validation">
					@endforeach
							@csrf
							@foreach($estudiante as $e)
							<div class="row">
								<div class="form-group col">
									<label for="nControl">* Número de Control:</label>
									<input type="text" class="form-control" value="{{ $e->ncontrol }}" pattern="[0-9]{8}|C{1}[0-9]{8}|B{1}[0-9]{8}" name="nControl" required>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="nombre">* Nombre (s):</label>
									<input type="text" class="form-control text-uppercase" value="{{ $e->nombre }}" 
									pattern="{[A-Z][a-z]+}+ *" minlength="4" name="nombre" required>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="apePat">* Apallido Paterno:</label>
									<input type="text" class="form-control text-uppercase" value="{{ $e->apePat }}" 
									pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apePat" required>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="apeMat">* Apellido Materno:</label>
									<input type="text" class="form-control text-uppercase" value="{{ $e->apeMat }}" 
									pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apeMat" required>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="carrera">* Carrera:</label>
									<select class="form-control" id="carrera" name="carrera" required> 
									    <option value="{{ $e->id_carrera }}"> {{ $e->carrera }} </option>
										@foreach($carreras as $c)
											<option value="{{$c->id_carrera}}" require> {{ $c->nombre }} </option>
										@endforeach
									</select>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="semestre">* Semestre:</label>
									<select class="form-control" id="semestre" name="semestre" required> 
                                        <option value="{{ $e->semestre }}"> {{ $e->semestre }} Semestre </option>
                                        @foreach($semestres as $s)
											<option value="{{ $s }}" require> {{ $s }} Semestre</option>
										@endforeach
									</select>
									<div class="valid-feedback">Valido</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="email">* Correo Institucional:</label>
									<input type="email" class="form-control text-lowercase" id="email" value="{{ $e->email }}" pattern="[0-9]{8}@itoaxaca.edu.mx{1}|[C|B]{1}[0-9]{8}@itoaxaca.edu.mx{1}" minlength="24" maxlength="26" name="email" required>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="curp">CURP:</label>
									<input type="text" class="form-control text-uppercase" value="{{ $e->curp }}" pattern="[A-Z]+[0-9]+[A-Z]+[0-9]*" minlength="18" maxlength="18" name="curp">
								</div>
							</div>

							<div class="row">
									<label > <strong>* Campos Obligatorios</strong> </label>
							</div>
							
							<center> <button type="button" data-toggle="modal" data-target="#edit" class="btn btn-outline-primary"> Guardar </button>
                            <a href="{{ URL::previous() }}" class="btn btn-outline-danger"> Cancelar </a> </center>
						@endforeach

						<div class="modal fade" id="edit" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
								<div class="modal-dialog modal-dialog-centered">
									<div class="modal-content">
										<div class="modal-header" style="background-color: #1B396A;">
											<h5 class="modal-title text-white" id="staticBackdropLabel"><strong>EDITAR ESUDIANTE</strong></h5>
											<button class="close text-white" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>  </button>
										</div>
										<div class="modal-body">
											<center>INTENTAS EDITAR UN(A) ESUDIANTE <br> ¿ESTÁS SEGURO DE ESTA ACCIÓN?</center><br>

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