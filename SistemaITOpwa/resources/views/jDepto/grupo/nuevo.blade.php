@extends('layouts.jDeptos')
@section('content')

	<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Registrar Nuevo Grupo </div>
					<div class="card-body">
					
						<form method="POST" action="{{url('/dpt/regGru')}}" class="needs-validation">
							@csrf
							<div class="row">
                            <div class="form-group col">
									<label for="clave">* Clave:</label>
									<input type="text" class="form-control" name="clave" placeholder="Escribe la clave del grupo" pattern="[G]{1}[A-Z]{3}[0-9]{3}" required>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="periodo">* Periodo:</label>
									<input type="text" class="form-control" value="{{ $periodo->nombre }}" disabled>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="actividad">* Actividad:</label>
									<select class="form-control" id="actividad" name="actividad" required> 
									    <option value=""> Selecciona la actividad </option>
										@foreach($actividades as $a)
											<option value="{{ $a->id_actividad }}" require> {{ $a->clave}} - {{ $a->nombre }} </option>
										@endforeach
									</select>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="respon">* Responsable:</label>
									<select class="form-control" id="respon" name="responsable" required> 
									    <option value=""> Selecciona el responsable </option>
										@foreach($personas as $p)
											<option value="{{ $p->id_persona }}" require>{{ $p->grado }} {{ $p->nombre }} {{ $p->apePat }} {{ $p->apeMat }} </option>
										@endforeach
									</select>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="lugar">* Lugar:</label>
									<select class="form-control" id="lugar" name="lugar" required> 
									    <option value=""> Selecciona el lugar </option>
										@foreach($lugares as $l)
											<option value="{{ $l->id_lugar }}" require> {{ $l->nombre }} </option>
										@endforeach
									</select>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="cupo">* Cupo:</label>
									<input type="text" class="form-control" name="cupo" placeholder="Escribe el cupo para el grupo" pattern="[0-9]{2}|[0-9]{1}" required>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="lugar">* Tipo de oferta de selección:</label>
									<select class="form-control" id="orden" name="orden" required> 
									    <option value=""> Selecciona el orden de oferta del grupo </option>
										<option value="1" require> Paralelo (Se ofertan al mismo tiempo)</option>
										<option value="0" require> Secuencial (Se oferta tras llenarse el grupo anterior)</option>
									</select>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

							</div>

							<div class="row">
									<label > * Campos Obligatorios </label>
							</div>
							<center>
                                <button type="submit" class="btn btn-outline-primary"> Registrar </button>
                                <a href="{{ url('JDepto/grupos/1') }}" class="btn btn-outline-danger"> Cancelar </a> 
                            </center>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection