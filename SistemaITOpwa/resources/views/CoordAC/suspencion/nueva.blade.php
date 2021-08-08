@extends('layouts.coordComple')
@section('content')

	<div class="container" style="padding-bottom: 55px;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0 border-top-0" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
					<div class="card-header text-white" style="background:#1B396A;"> Nueva Fecha de Suspensión </div>
					<div class="card-body">
					
						<form method="POST" action="{{url('/regFecha')}}" class="needs-validation">
							@csrf
							<div class="row">
								<div class="form-group col">
									<label for="nomCritE">* Fecha (Única / Inicio):</label>
									<input type="date" class="form-control" placeholder="Selecciona la fecha" name="fecha" required>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>

								<div class="form-group col">
									<label for="nomCritE"> Fecha (Fin):</label>
									<input type="date" class="form-control" placeholder="Selecciona la fecha" name="fechafin">
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

                            <div class="row">
								<div class="form-group col">
									<label for="desCritE">* Motivo:</label>
									<textarea type="text" class="form-control text-uppercase" 
									placeholder="Escribe el motivo de la suspención" 
									pattern="{[A-Z][a-z]+}+ *" name="motivo" required></textarea>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label class="text-justify">Para agregar una sola fecha de suspención basta con escoger 
									la fecha en el lado izquierdo (Única / Inicio). Solo si desea agregar un conjunto de
									fechas para la suspención escoja la fecha de inicio de la suspencíon del lado
									izquierdo y la fecha para el término de la suspención del lado derecho.</label>
									<label><strong>* Campos obligatorios</strong></label>
								</div>
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