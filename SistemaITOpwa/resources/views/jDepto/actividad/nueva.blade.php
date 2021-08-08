@extends('layouts.jDeptos')
@section('content')

	<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Registrar Nueva Actvidad </div>
					<div class="card-body">
					
						<form method="POST" action="{{ url('/dpt/regAct') }}" class="needs-validation">
							@csrf
							<div class="row">
								<div class="form-group col">
									<label for="nControl">* Clave:</label>
									<input type="text" class="form-control" placeholder="Clave de la actividad" pattern="[A-Z][0-9]*[a-Z]*" name="clave" required>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="nombre">* Nombre:</label>
									<input type="text" class="form-control" id="nombre" placeholder="Nombre(s)" pattern="{[A-Z][a-z]+}+ *" name="nombre" required>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="apePat">* Número de Créditos:</label>
									<select class="form-control" id="cred" name="creditos" require> 
										<option value=""> Selecciona los créditos </option>
										<option value="1" > Un crédito </option>
										<option value="2" > Dos créditos </option>
									</select>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="apeMat">* Departamento:</label>
									<input type="text" class="form-control" value="{{ $depto->nombre }}" disabled>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="carrera">* Tipo de actividad:</label>
									<select class="form-control" id="carrera" name="tipo" required> 
									    <option value=""> Selecciona un Tipo </option>
										@foreach($tipos as $t)
											<option value="{{$t->id_tipo}}" require> {{ $t->nombre }} </option>
										@endforeach
									</select>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="periodo">* Periodo:</label>
									<input type="text" class="form-control" value="{{ $periodo->nombre }}" disabled>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="carrera">* Restringida:</label>
									<select class="form-control" id="restric" name="restringida" required> 
										<option value=""> Selecciona SI/NO </option>
										<option value="1" require> <strong>SI</strong> Restringida </option>
										<option value="0" require> <strong>NO</strong> Restringida </option>
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
									<textarea type="textarea" class="form-control" placeholder="Escribe una descripción de la actividad" maxlength="250" name="descripcion"></textarea>
								</div>
							</div>

							<div class="row">
									<label > * Campos Obligatorios </label>
							</div>
							<center> 
                                <button type="submit" class="btn btn-outline-primary"> Registrar </button>
                                <a href="{{ url('JDepto/actividad/1') }}" class="btn btn-outline-danger"> Cancelar </a> 
                            </center>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection