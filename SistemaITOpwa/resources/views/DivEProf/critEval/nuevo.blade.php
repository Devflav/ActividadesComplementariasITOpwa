@extends('layouts.divEProf')
@section('content')

	<div class="container" style="padding-bottom: 55px;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
					<div class="card-header text-white" style="background:#1B396A;"> Nuevo Criterio de Evaluación </div>
					<div class="card-body">
					
						<form method="POST" action="{{url('/DivEProf/regCritE')}}" class="needs-validation">
							@csrf
							<div class="row">
								<div class="form-group col">
									<label for="nomCritE">* Nombre:</label>
									<input type="text" class="form-control text-uppercase" placeholder="Escribe el nombre del criterio de evaluación" 
									pattern="{[A-Z][a-z]+}+ *" name="nomCritE" id="nomCritE"
									data-toggle="tooltip" title="Responsabilidad" required>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

                            <div class="row">
								<div class="form-group col">
									<label for="desCritE">* Descripción:</label>
									<textarea type="text" class="form-control text-uppercase" 
									placeholder="Escribe la descripción del criterio de evaluación" 
									title="Muestra responsabilidad en las actividades realizadas"
									pattern="{[A-Z][a-z]+}+ *" name="desCritE" required></textarea>
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