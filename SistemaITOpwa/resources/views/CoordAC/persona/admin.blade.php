@extends('layouts.coordComple')
@section('content')

	<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> 
						Registrar Nuevo Administrador 
					</div>
					<div class="card-body">
					
						<form method="POST" action="{{url('/regEmp')}}" class="needs-validation">
							@csrf
							<div class="row">
								<div class="form-group col">
									<label for="nControl">* Grado:</label>
									<select class="form-control" id="grado" name="grado" required> 
									    <option value=""> Selecciona una Grado </option>
										@foreach($grados as $g)
											<option value="{{$g->id_grado}}" require> {{ $g->nombre }} </option>
										@endforeach
									</select>
									<div class="valid-feedback"></div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="nombre">* Nombre (s):</label>
									<input type="text" class="form-control text-uppercase" id="nombre" 
									placeholder="Nombre(s)" pattern="{[A-Z][a-z]+}+ *" 
									minlength="4" name="nombre" required>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="apePat">* Apallido Paterno:</label>
									<input type="text" class="form-control text-uppercase" id="apePat" 
									placeholder="Apellido Paterno" pattern="{[A-Z][a-z]+}+ *" 
									minlength="4" name="apePat" required>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="apeMat">* Apellido Materno:</label>
									<input type="text" class="form-control text-uppercase" id="apeMat" 
									placeholder="Apellido Materno" pattern="{[A-Z][a-z]+}+ *" 
									minlength="4" name="apeMat" required>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="carrera">* Departamento:</label>
									<select class="form-control" id="depto" name="depto" required> 
									    <option value=""> Selecciona un Departamento </option>
										@foreach($departamentos as $d)
											<option value="{{ $d->id_depto }}" require> {{ $d->nombre }} </option>
										@endforeach
									</select>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col-5">
									<label for="curp">* CURP:</label>
									<input type="text" class="form-control text-uppercase" id="curp" placeholder="Escribe la Curp" pattern="[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9]{2}" minlength="18" maxlength="18" name="curp" required>
								</div>

								<div class="form-group col">
									<label for="semestre">* Puesto:</label>
									<input type="text" class="form-control text-uppercase" 
									value="COORDINACIÓN DE ACTIVIDADES COMPLEMENTARIAS" DISABLED>
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
							
							<!--  <button type="submit" class="btn btn-outline-primary"> Registrar </button> -->
                            <center> <button type="button" data-toggle="modal" data-target="#admin" class="btn btn-outline-primary"> Registrar </button>
							<a href="{{ url('/CoordAC/personal/1') }}" class="btn btn-outline-danger"> Cancelar </a> 
							</center>

							<div class="modal fade" id="admin" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
								<div class="modal-dialog modal-dialog-centered">
									<div class="modal-content">
										<div class="modal-header" style="background-color: #1B396A;">
											<h5 class="modal-title text-white" id="staticBackdropLabel"><strong>AGREGAR ADMINISTRADOR</strong></h5>
											<button class="close text-white" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>  </button>
										</div>
										<div class="modal-body">
											<center>AL REGISTRAR UN NUEVO ADMINISTRADOR, TU USUARIO QUEDARÁ
											INHABILITADO, NO PODRÁS ACCEDER AL SISTEMA, HASTA QUE SE TE ASIGNE
											UN NUEVO PUESTO. <br> ¿ESTAS SEGURO DE ESTA ACCIÓN?</center><br>

										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cerar</button>
											<button type="submit" class="btn btn-outline-danger">Registrar</button>
										</div>
									</div>
								</div>
							</div>

						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection