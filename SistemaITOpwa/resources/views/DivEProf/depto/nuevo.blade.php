@extends('layouts.divEProf')
@section('content')

	<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Nuevo Departamento </div>
					<div class="card-body">
					
						<form method="POST" action="{{ url('/DivEProf/regDepto') }}" class="needs-validation">
							@csrf
							<div class="row">
								<div class="form-group col">
									<label for="nombre">* Nombre:</label>
									<input type="text" class="form-control text-uppercase" 
									placeholder="Escribe el nombre del departamento" 
									title="Departamento de gestión económica"
									pattern="{[A-Z][a-z]+}+ *" name="nomDepto" required>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
										<label for="nombre">* Jefe de Departamento:</label>
										<select class="form-control" id="persona" name="persona" required> 
										<option value="" >Selecciona el nuevo Jefe</option>
										@foreach($jefes as $j)
											<option value="{{$j->id_persona }}" required> {{ $j->grado }} {{ $j->nombre }} {{ $j->apePat }} {{ $j->apeMat }}</option>
										@endforeach
									</select>
										<div class="valid-feedback">Valido.</div>
										<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

							<div class="row">
								<label > 
									<strong>
										* Campos Obligatorios
										</strong> 
								</label>
							</div>

							<center> 
                                <button type="submit" class="btn btn-outline-primary"> Registrar </button>
                                <a href="{{ URL::previous() }}" class="btn btn-outline-danger"> Cancelar </a> 
                            </center>
                        
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection