@extends('layouts.jDeptos')
@section('content')

	<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Mis Datos Generales </div>
					<div class="card-body">
						<form method="POST" action="{{ ('/dpt/editPerfil') }}" class="needs-validation">
							@csrf
                            @foreach($persona as $p)
							<div class="row">
								<div class="form-group col">
									<label for="nControl">* Grado:</label>
									@if($editar == '1') 
										<select class="form-control" id="grado" name="grado" required>
									@else
										<select class="form-control" id="grado" name="grado" disabled>
									@endif
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
									@if($editar == '1')
									<input type="text" class="form-control" value="{{ $p->nombre }}" pattern="{[A-Z][a-z]+}+ *" minlength="4" name="nombre" required>
									@else
									<input type="text" class="form-control" value="{{ $p->nombre }}" pattern="{[A-Z][a-z]+}+ *" minlength="4" name="nombre" disabled>
									@endif
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="apePat">* Apallido Paterno:</label>
									@if($editar == '1')
										<input type="text" class="form-control" value="{{ $p->paterno }}" pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apePat" required>
									@else
										<input type="text" class="form-control" value="{{ $p->paterno }}" pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apePat" disabled>
									@endif
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="apeMat">* Apellido Materno:</label>
									@if($editar == '1')
										<input type="text" class="form-control" value="{{ $p->materno }}" pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apeMat" required>
									@else
										<input type="text" class="form-control" value="{{ $p->materno }}" pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apeMat" disabled>
									@endif
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="carrera">* Departamento:</label>
									<input type="text" class="form-control" value="{{ $depto->nombre }}" disabled>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="semestre">* Puesto:</label>
									<input type="text" class="form-control" value="{{ $p->puesto }}" disabled>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="curp">* CURP:</label>
									@if($editar == '1')
										<input type="text" class="form-control" value="{{ $p->curp }}" pattern="[A-Z]+[0-9]+[A-Z]+[0-9]*" minlength="18" maxlength="18" name="curp" required>
									@else
										<input type="text" class="form-control" value="{{ $p->curp }}" pattern="[A-Z]+[0-9]+[A-Z]+[0-9]*" minlength="18" maxlength="18" name="curp" disabled>
									@endif
								</div>
								<div class="valid-feedback">Valid.</div>
								<div class="invalid-feedback">Por favor rellena el campo.</div>
							</div>
							@php
								$edit = 1;
								$per = 0;
							@endphp

						@endforeach

							<div class="row">
									<label > * Campos Obligatorios </label>
							</div>
							<center>
							
							@if($editar == '1')
								<button type="submit" class="btn btn-outline-primary"> Actualizar </button>
								<a href="{{ url('JDepto/datgen') }}" class="btn btn-outline-danger"> Cancelar </a> 
							@else
								<a href="{{ url('JDepto/cambcontrasenia') }}" class="btn btn-outline-primary"> Cambiar contraseña </a>
								<a href="{{ url('JDepto/editperf') }}" class="btn btn-outline-primary"> Editar </a>
								<a href="{{ url('JDepto') }}" class="btn btn-outline-danger"> Regresar </a> 
							@endif
							</center>
                        </form>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection