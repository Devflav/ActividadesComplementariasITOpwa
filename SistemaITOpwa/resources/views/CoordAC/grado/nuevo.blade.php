@extends('layouts.coordComple')
@section('content')

	<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Nuevo Grado </div>
					<div class="card-body">
					
						<form method="POST" action="{{url('/regGrado')}}" class="needs-validation">
							@csrf
							<div class="row">
								<div class="col-4">
									<div class="form-group col">
										<label for="nomGrado">* Grado:</label>
										<input type="text" class="form-control text-uppercase" 
										placeholder="Escribe el grado" pattern="{[A-Z][a-z]+}+ *" 
										title="ING"
										name="nomGrado" required>
										<div class="valid-feedback">Valido.</div>
										<div class="invalid-feedback">Por favor rellena el campo.</div>							
									</div>
								</div>
								<div class="col-8">
									<div class="form-group col">
										<label for="nomGrado">* Descripción:</label>
										<input type="text" class="form-control text-uppercase" 
										placeholder="Escribe la descripción" pattern="{[A-Z][a-z]+}+ *" 
										title="Ingeniero"
										name="significado" required>
										<div class="valid-feedback">Valido.</div>
										<div class="invalid-feedback">Por favor rellena el campo.</div>							
									</div>
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