@extends('layouts.coordComple')

@section('content')

	<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Editar Actvidad </div>
					<div class="card-body">
					@foreach($actividad as $a)						
							<form method="GET" action="{{ url('/update/actividad').'/'.$a->id_actividad }}" class="needs-validation">
						@endforeach
							@csrf
                            @foreach($actividad as $a)
							<div class="row">
								<div class="form-group col">
									<label for="nControl">* Clave:</label>
									<input type="text" class="form-control text-uppercase" value="{{ $a->clave }}" placeholder="Clave de la actividad" pattern="[A-Z][0-9]*[a-Z]*" name="clave" required>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="nombre">* Nombre:</label>
									<input type="text" class="form-control text-uppercase" value="{{ $a->nombre }}" placeholder="Nombre(s)" pattern="{[A-Z][a-z]+}+ *" name="nombre" required>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="apePat">* Número de Creditos:</label>
									<input type="text" class="form-control" value="{{ $a->creditos }}" placeholder="Número de creditos" pattern="[1-2]" name="creditos" disabled>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="apeMat">* Departamento:</label>
									<select class="form-control" id="carrera" name="depto" required> 
									    <option value="{{ $a->id_depto }}"> {{ $a->depto }} </option>
										@foreach($deptos as $d)
											<option value="{{$d->id_depto}}" require> {{ $d->nombre }} </option>
										@endforeach
									</select>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="carrera">* Tipo de actividad:</label>
									<select class="form-control" id="carrera" name="tipo" required> 
									    <option value="{{ $a->id_tipo }}"> {{ $a->tipo }} </option>
										@foreach($tipos as $t)
											<option value="{{$t->id_tipo}}" require> {{ $t->nombre }} </option>
										@endforeach
									</select>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="curp">Descripción:</label>
									<input type="text-area" class="form-control" value="{{ $a->descripcion }}" placeholder="Escribe una descripción de la actividad" maxlength="250" name="descripcion">
								</div>
							</div>

							<div class="row">
									<label > <strong>* Campos Obligatorios</strong> </label>
							</div>
							<center> 
                                <!-- <a onclick="editar('{{ url('CoordAC/actualizar/actividad').'/'.$a->id_actividad }}')" class="btn btn-outline-primary"> Guardar </a> -->
                                <button type="button" data-toggle="modal" data-target="#edit" class="btn btn-outline-primary"> Guardar </button>
                                <a href="{{ url('/CoordAC/actividades/1') }}" class="btn btn-outline-danger"> Cancelar </a>
                            </center>
                        @endforeach

						<div class="modal fade" id="edit" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
								<div class="modal-dialog modal-dialog-centered">
									<div class="modal-content">
										<div class="modal-header" style="background-color: #1B396A;">
											<h5 class="modal-title text-white" id="staticBackdropLabel"><strong>EDITAR ACTIVIDAD</strong></h5>
											<button class="close text-white" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>  </button>
										</div>
										<div class="modal-body">
											<center>INTENTAS EDITAR UN(A) ACTIVIDAD <br> ¿ESTÁS SEGURO DE ESTA ACCIÓN?</center><br>

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
		<button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#mimodal" id="btn_mimodal">
        </button>
        <div class="modal fade" id="mimodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="restablecerLabel" aria-hidden="true">
        </div>
	</div>

@endsection