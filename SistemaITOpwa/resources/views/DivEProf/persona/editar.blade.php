@extends('layouts.divEProf')
@section('content')

	<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Editar Personal </div>
					<div class="card-body">
					@foreach($persona as $p)
					
						<form method="POST" action="{{ url('/DivEProf/update/personal').'/'.$p->id_persona}}" class="needs-validation">
					@endforeach
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
									<input type="text" class="form-control text-uppercase" 
									value="{{ $p->nombre }}" pattern="{[A-Z][a-z]+}+ *" 
									minlength="4" name="nombre" required>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>							
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="apePat">* Apallido Paterno:</label>
									<input type="text" class="form-control text-uppercase" 
									value="{{ $p->paterno }}" pattern="{[A-Z][a-z]+}+ *" 
									minlength="4" name="apePat" required>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="apeMat">* Apellido Materno:</label>
									<input type="text" class="form-control text-uppercase" 
									value="{{ $p->materno }}" pattern="{[A-Z][a-z]+}+ *" 
									minlength="4" name="apeMat" required>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="carrera">* Departamento:</label>
									<select class="form-control" id="depto" name="depto" required> 
									    <option value="{{ $p->id_depto }}"> {{ $p->depto }} </option>
										@foreach($departamentos as $d)
											<option value="{{ $d->id_depto }}" require> {{ $d->nombre }} </option>
										@endforeach
									</select>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="semestre">* Puesto:</label>
									<select class="form-control" id="puesto" name="puesto" required> 
                                        <option value="{{ $p->id_puesto }}"> {{ $p->puesto }} </option>
                                        @foreach($puestos as $pu)
											<option value="{{ $pu->id_puesto }}" require> {{ $pu->nombre }} </option>
										@endforeach
									</select>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="curp">* CURP:</label>
									<input type="text" class="form-control text-uppercase" 
									value="{{ $p->curp }}" pattern="[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9]{2}" 
									minlength="18" maxlength="18" name="curp" required>
								</div>
							</div>
						@endforeach

							<div class="row">
									<label ><strong> * Campos Obligatorios</strong> </label>
							</div>
							<center> <button type="button" data-toggle="modal" data-target="#edit" class="btn btn-outline-primary"> Guardar </button>
                            <a href="{{ URL::previous() }}" class="btn btn-outline-danger"> Cancelar </a> 
							</center>

							<div class="modal fade" id="edit" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
								<div class="modal-dialog modal-dialog-centered">
									<div class="modal-content">
										<div class="modal-header" style="background-color: #1B396A;">
											<h5 class="modal-title text-white" id="staticBackdropLabel"><strong>EDITAR PERSONAL</strong></h5>
											<button class="close text-white" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>  </button>
										</div>
										<div class="modal-body">
											<center>INTENTAS EDITAR UN(A) PERSONAL <br> ¿ESTAS SEGURO DE ESTA ACCIÓN?</center><br>

										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cerar</button>
											<button type="submit" class="btn btn-outline-danger">Editar</button>
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