@extends('layouts.jDeptos')
@section('content')

	<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Editar Grupo </div>
					<div class="card-body">
					@foreach($grupo as $g)
						<form method="POST" action="{{ url('/dpt/editGru').$g->id_grupo }}" class="needs-validation">
					@endforeach
							@csrf
                            @foreach($grupo as $g)
							<div class="row">
                            <div class="form-group col">
									<label for="clave">* Clave:</label>
									<input type="text" class="form-control" name="clave" value="{{ $g->clave }}" required>
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
									    <option value="{{ $g->id_actividad }}"> {{ $g->actividad }} </option>
										@foreach($actividades as $a)
											<option value="{{ $a->id_actividad }}" require> {{ $a->nombre }} </option>
										@endforeach
									</select>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="respon">* Responsable:</label>
									<select class="form-control" id="respon" name="responsable" required> 
									    <option value="{{ $g->id_persona }}"> {{ $g->nomP }} {{ $g->paterno }} {{ $g->materno }}</option>
										@foreach($personas as $p)
											<option value="{{ $p->id_persona }}" required> {{ $p->nombre }} {{ $p->apePat }} {{ $p->apeMat }} </option>
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
									    <option value="{{ $g->id_lugar }}"> {{ $g->lugar }} </option>
										@foreach($lugares as $l)
											<option value="{{ $l->id_lugar }}" require> {{ $l->nombre }} </option>
										@endforeach
									</select>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="cupo">* Cupo:</label>
									<input type="text" class="form-control" name="cupo" value="{{ $g->cupo }}" require>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="lugar">* Tipo de oferta de selección:</label>
									<select class="form-control" id="orden" name="orden" required> 
									    <option value="{{ $g->orden }}"> @if($g->oorden = 1)
																			Paralelo
																		@else
																			Secuancial
																		@endif </option>
										<option value="1" require> Paralelo (Se ofertan al mismo tiempo)</option>
										<option value="0" require> Secuencial (Se oferta tras llenarse el grupo anterior)</option>
									</select>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

							</div>

                            @endforeach
							<div class="row">
									<label > * Campos Obligatorios </label>
							</div>
							<center>
                                <button type="submit" class="btn btn-outline-primary"> Guardar </button>
                                <a href="{{ url('JDepto/grupos/1') }}" class="btn btn-outline-danger"> Cancelar </a> 
                            </center>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection