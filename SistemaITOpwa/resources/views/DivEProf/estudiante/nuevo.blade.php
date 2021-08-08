@extends('layouts.divEProf')
@section('content')

	<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Registrar Nuevo Estudiante </div>
					<div class="card-body">
					
						<form method="POST" action="{{url('/DivEProf/regEst')}}" class="needs-validation">
							@csrf
							<div class="row">
								<div class="form-group col">
									<label for="nControl">* Número de Control:</label>
									<input type="text" class="form-control" id="nControl" placeholder="Escribe el número de control" 
									pattern="[0-9]{8}|[C|B]{1}[0-9]{8}" name="nControl" required>
									<div class="valid-feedback"></div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="nombre">* Nombre (s):</label>
									<input type="text" class="form-control text-uppercase" id="nombre" 
									placeholder="Escribe tu(s) Nombre(s)" pattern="{[A-Z][a-z]+}+ *" 
									minlength="4" name="nombre" required>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="apePat">* Apallido Paterno:</label>
									<input type="text" class="form-control text-uppercase" id="apePat" 
									placeholder="Escribe tu Apellido Paterno" pattern="{[A-Z][a-z]+}+ *" 
									minlength="4" name="apePat" required>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="apeMat">* Apellido Materno:</label>
									<input type="text" class="form-control text-uppercase" id="apeMat" 
									placeholder="Escribe tu Apellido Materno" pattern="{[A-Z][a-z]+}+ *" 
									minlength="4" name="apeMat" required>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="carrera">* Carrera:</label>
									<select class="form-control" id="carrera" placeholder="Selecciona tu Carrera" name="carrera" required> 
									<option value=""> Selecciona una Carrera </option>
										@foreach($carreras as $c)
											<option value="{{$c->id_carrera}}" require> {{ $c->nombre }} </option>
										@endforeach
									</select>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="semestre">* Semestre:</label>
									<select class="form-control" id="semestre" placeholder="Selecciona tu Semestre" name="semestre" required> 
									<option value=""> Selecciona un Semestre </option>
										@foreach($semestres as $s)
											<option value="{{ $s }}" require> {{ $s }}° Semestre </option>
										@endforeach
									</select>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="email">* Correo Institucional:</label>
									<input type="email" class="form-control text-lowercase" id="email" placeholder="escribe tu correo institucional" pattern="[0-9]{8}@itoaxaca.edu.mx{1}|[C|B]{1}[0-9]{8}@itoaxaca.edu.mx{1}" minlength="24" maxlength="26" name="email" required>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
								<div class="form-group col">
									<label for="curp">CURP:</label>
									<input type="text" class="form-control text-uppercase" id="curp" 
									placeholder="Puedes registrarla en otro momento" 
									pattern="[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9]{2}" minlength="18" maxlength="18" name="curp">
									<div class="valid-feedback">Valid.</div>
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
							
							<center> <button type="submit" class="btn btn-outline-primary"> Registrar </button>
                            <a href="{{ url('/DivEProf/DivEProf/estudiantes/1') }}" class="btn btn-outline-danger"> Cancelar </a> </center>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection