@extends('layouts.presentacion')
@section('content')

<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 10%;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> <h6>REGISTRO DE ESTUDIANTES</h6>
					<!-- <img class="mx-auto d-block float-right" src="../images/ac_ito/agregar-usuario.png" width="5%">  -->
					</div>
					<center></center>
					<div class="card-body">
					
						<form method="POST" action="{{url('/Enviar/Registro')}}" class="was-validated">
							@csrf
							<div class="row">
								<div class="form-group col">
									<label for="nControl">* Número de Control:</label>
									<input type="text" class="form-control" id="nControl" placeholder="21160000" pattern="[0-9]{8}|[C|B]{1}[0-9]{8}|[0-9]{9}" name="nControl" required>
									<div class="valid-feedback"></div>
									<div class="invalid-feedback">Escribe tu Número de Control</div>
								</div>

								<div class="form-group col">
									<label for="nombre">* Nombre (s):</label>
									<input type="text" class="form-control" id="nombre" placeholder="Augusta" pattern="{[A-Z][a-z]+}+ *" minlength="4" name="nombre" required>
									<div class="valid-feedback">Formato valido.</div>
									<div class="invalid-feedback">Escribe tu(s) Nombre(s)</div>							
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="apePat">* Apallido Paterno:</label>
									<input type="text" class="form-control" id="apePat" placeholder="Ada" pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apePat" required>
									<div class="valid-feedback">Formato valido.</div>
									<div class="invalid-feedback">Escribe tu Apellido Paterno</div>
								</div>

								<div class="form-group col">
									<label for="apeMat">* Apellido Materno:</label>
									<input type="text" class="form-control" id="apeMat" placeholder="King" pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apeMat" required>
									<div class="valid-feedback">Formato valido.</div>
									<div class="invalid-feedback">Escribe tu Apellido Materno</div>
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
									<div class="valid-feedback">Formato valido.</div>
									<div class="invalid-feedback">Por favor selecciona una opción</div>
								</div>

								<div class="form-group col">
									<label for="semestre">* Semestre:</label>
									<select class="form-control" id="semestre" placeholder="Selecciona tu Semestre" name="semestre" required> 
									<option value=""> Selecciona un Semestre </option>
										@foreach($semestres as $s)
											<option value="{{ $s }}" require> {{ $s }}° Semestre </option>
										@endforeach
									</select>
									<div class="valid-feedback">Formato valido.</div>
									<div class="invalid-feedback">Por favor selecciona una opción</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="email">* Correo Institucional:</label>
									<input type="email" class="form-control" id="email" placeholder="21160000@itoaxaca.edu.mx" pattern="[0-9]{8}@itoaxaca.edu.mx{1}|[C|B]{1}[0-9]{8}@itoaxaca.edu.mx{1}" minlength="24" maxlength="26" name="email" required>
									<div class="valid-feedback">Formato valido.</div>
									<div class="invalid-feedback">Escribe tu correo INSTITUCIONAL</div>
								</div>
								<div class="form-group col">
									<label for="curp">CURP:</label>
									<input type="text" class="form-control" id="curp" placeholder="AAAA111111BBBBBB22" pattern="[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9]{2}" minlength="18" maxlength="18" name="curp">
									<div class="valid-feedback">Formato valido.</div>
									<div class="invalid-feedback">Puedes registrarla en otro momento</div>
								</div>
							</div>

							<div class="row">
									<label > <strong> * Campos Obligatorios</strong> </label>
							</div>
							<div class="row">
									<div class="form-group col">
									<label class="text-justify"> Para iniciar sesión la primera vez en el sistema, ocuparás como
									usuario tu <strong>correo institucional</strong> y como contraseña tu 
									<strong>número de control</strong>. Tras ingresar tendrás que cambiar
									tu contraseña obligatoriamente para poder hacer uso del sistema.  </label>
									</div>
							</div>
							<center> <button type="submit" class="btn btn-outline-primary" style="padding-bottom: 10px;"> Registrarme </button> </center>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection

