@extends('layouts.divEProf')
@section('content')

	<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Mis Datos Generales </div>
					<div class="card-body">
					
						<form method="POST" action="{{url('/DivEProf/cac/editperf')}}" class="needs-validation">
							@csrf
                            @foreach($persona as $p)
							<div class="row">
								<div class="form-group col">
									<label for="nControl">* Grado:</label>
									<select class="form-control" id="grado" name="grado" required> 
									    <option value="{{ $p->id_grado}}"> {{ $p->grado }} </option>
										@foreach($grados as $g)
											<option value="{{$g->id_grado}}" require> {{ $g->nombre }} </option>
										@endforeach
									</select>
									<div class="valid-feedback"></div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="nombre">* Nombre (s):</label>
									<input type="text" class="form-control" value="{{ $p->nombre }}" pattern="{[A-Z][a-z]+}+ *" minlength="4" name="nombre" required>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="apePat">* Apallido Paterno:</label>
									<input type="text" class="form-control" value="{{ $p->paterno }}" pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apePat" required>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="apeMat">* Apellido Materno:</label>
									<input type="text" class="form-control" value="{{ $p->materno }}" pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apeMat" required>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="carrera"> Departamento:</label>
									<input type="text" class="form-control" value="{{ $p->depto }}" name="depto" disabled>
								</div>

								<div class="form-group col">
									<label for="semestre"> Puesto:</label>
									<input type="text" class="form-control" value="{{ $p->puesto }}" name="puesto" disabled>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="curp">* CURP:</label>
									<input type="text" class="form-control text-uppercase" value="{{ $p->curp }}" pattern="[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9]{2}" minlength="18" maxlength="18" name="curp" required>
								</div>
							</div>
						@endforeach

							<div class="row">
									<label > * Campos Obligatorios </label>
							</div>
							<center> 
							<button type="submit" class="btn btn-outline-primary"> Actualizar </button>
                            <a href="{{ url('DivEProf/datosGen') }}" class="btn btn-outline-danger"> Cancelar </a> </center>
                        </form>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection