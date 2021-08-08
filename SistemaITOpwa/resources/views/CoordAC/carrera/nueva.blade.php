@extends('layouts.coordComple')
@section('content')

	<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Nueva Carrera </div>
					<div class="card-body">
					
						<form method="POST" action="{{url('/regCar')}}" class="needs-validation">
							@csrf
							<div class="row">
								
							<div class="form-group col-4">
									<label for="tipo">* Tipo</label>
									<select name="tipo" id="" class="form-control" required>
										<option value="" readonly>Selecciona un tipo</option>
										<option value="1">INGENIERÍA</option>
										<option value="2">LICENCIATURA</option>
									</select>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>

								<div class="form-group col-8">
									<label for="nombreCarr" data-toggle="tooltip" title="¡Solo se admiten letras!">* Nombre:</label>
									<input type="text" class="form-control text-uppercase" placeholder="Escribe el nombre de la carrera" 
									pattern="[A-Z]+ [A-Z]* [A-Z]*|[a-z]+ [a-z]* [a-z]*" 
									name="nombreCarr" id="carrera"
									data-toggle="tooltip" title="Ing/Lic Contabilidad Fianciera"
									required>
									<div class="valid-feedback">Valido.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="apeMat">* Departamento:</label>
									<select class="form-control" id="depto" name="depto" required> 
									    <option value=""> Selecciona un Departamento </option>
										@foreach($deptos as $d)
											<option value="{{$d->id_depto}}" require> {{ $d->nombre }} </option>
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

<script> 
	//var input = document.getElementById('carrera');
	input.oninvalid = function(event) {
    event.target.setCustomValidity('El nombre de la carrera solo puede contener letras.');
}
</script>
@endsection