@extends('layouts.divEProf')
@section('content')
<div class="container form-content col-sm-9">
@if (session('Catch') != null)
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<h5 class="alert-heading"> 
				<strong> <em> ¡ Error ! </em> </strong>
				<i class="bi bi-x-octagon-fill close float-right" type="button" data-dismiss="alert"></i>
			</h5>
			<hr>
			<p class="text-justify"> {{ session('Catch') }} </p>
		</div>
    @endif
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Editar Personal </div>
        </div>
    </div>
    <div class="card-body">
	@foreach($persona as $p)
			<form method="POST" action="{{ url('DivEProf/update/personal').'/'.$p->id_persona}}" class="needs-validation">
	@endforeach
			@csrf
			@foreach($persona as $p)
				@if($p->id_puesto != 9)
					<div class="form-group">
						<div class="col-sm">
							<label for="nControl">* Grado:</label>
							<select class="form-control" id="grado" name="id_grado" required> 
								<option value="{{ $p->id_grado}}"> {{ $p->grado }} </option>
								@foreach($grados as $g)
									<option value="{{$g->id_grado}}" require> {{ $g->nombre }} </option>
								@endforeach
							</select>
							<div class="valid-feedback"></div>
							<div class="invalid-feedback">Por favor rellena el campo.</div>
						</div>
						<div class="col-sm">
							<label for="nombre">* Nombre (s):</label>
							<input type="text" class="form-control text-uppercase" 
								value="{{ $p->nombre }}" pattern="{[A-Z][a-z]+}+ *" 
								minlength="4" name="nombre" required>
							<div class="valid-feedback">Valid.</div>
							<div class="invalid-feedback">Por favor rellena el campo.</div>	
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm">
							<label for="apePat">* Apallido Paterno:</label>
							<input type="text" class="form-control text-uppercase" 
								value="{{ $p->paterno }}" pattern="{[A-Z][a-z]+}+ *" 
								minlength="4" name="apePat" required>
							<div class="valid-feedback">Valid.</div>
							<div class="invalid-feedback">Por favor rellena el campo.</div>
						</div>
						<div class="col-sm">
							<label for="apeMat">* Apellido Materno:</label>
							<input type="text" class="form-control text-uppercase" 
								value="{{ $p->materno }}" pattern="{[A-Z][a-z]+}+ *" 
								minlength="4" name="apeMat" required>
							<div class="valid-feedback">Valid.</div>
							<div class="invalid-feedback">Por favor rellena el campo.</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm">
							<label for="carrera">* Departamento:</label>
							<select class="form-control" id="depto" name="id_depto" required> 
								<option value="{{ $p->id_depto }}"> {{ $p->depto }} </option>
								@foreach($departamentos as $d)
									<option value="{{ $d->id_depto }}" require> {{ $d->nombre }} </option>
								@endforeach
							</select>
							<div class="valid-feedback">Valid.</div>
							<div class="invalid-feedback">Por favor rellena el campo.</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm">
							<label for="curp">* CURP:</label>
							<input type="text" class="form-control text-uppercase" 
								value="{{ $p->curp }}" pattern="[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9]{2}" 
								minlength="18" maxlength="18" name="curp" required>
						</div>
						<div class="col-sm">
							<label for="semestre">* Puesto:</label>
							<select class="form-control" id="puesto" name="id_puesto" required> 
								<option value="{{ $p->id_puesto }}"> {{ $p->puesto }} </option>
								@foreach($puestos as $pu)
									<option value="{{ $pu->id_puesto }}" require> {{ $pu->nombre }} </option>
								@endforeach
							</select>
							<div class="valid-feedback">Valid.</div>
							<div class="invalid-feedback">Por favor rellena el campo.</div>
               			</div>
					</div>
				@else
					<div class="form-group">
						<div class="col-sm">
							<label for="nControl">* Grado:</label>
							<select class="form-control" id="grado" name="id_grado" readonly> 
								<option value="{{ $p->id_grado}}"> {{ $p->grado }} </option>
								@foreach($grados as $g)
									<option value="{{$g->id_grado}}" readonly> {{ $g->nombre }} </option>
								@endforeach
							</select>
							<div class="valid-feedback"></div>
							<div class="invalid-feedback">Por favor rellena el campo.</div>
						</div>
						<div class="col-sm">
							<label for="nombre">* Nombre (s):</label>
							<input type="text" class="form-control text-uppercase" 
								value="{{ $p->nombre }}" pattern="{[A-Z][a-z]+}+ *" 
								minlength="4" name="nombre" readonly>
							<div class="valid-feedback">Valid.</div>
							<div class="invalid-feedback">Por favor rellena el campo.</div>	
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm">
							<label for="apePat">* Apallido Paterno:</label>
							<input type="text" class="form-control text-uppercase" 
								value="{{ $p->paterno }}" pattern="{[A-Z][a-z]+}+ *" 
								minlength="4" name="apePat" readonly>
							<div class="valid-feedback">Valid.</div>
							<div class="invalid-feedback">Por favor rellena el campo.</div>
						</div>
						<div class="col-sm">
							<label for="apeMat">* Apellido Materno:</label>
							<input type="text" class="form-control text-uppercase" 
								value="{{ $p->materno }}" pattern="{[A-Z][a-z]+}+ *" 
								minlength="4" name="apeMat" readonly>
							<div class="valid-feedback">Valid.</div>
							<div class="invalid-feedback">Por favor rellena el campo.</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm">
							<label for="carrera">* Departamento:</label>
							<select class="form-control" id="depto" name="id_depto" readonly> 
								<option value="{{ $p->id_depto }}"> {{ $p->depto }} </option>
								@foreach($departamentos as $d)
									<option value="{{ $d->id_depto }}"readonly> {{ $d->nombre }} </option>
								@endforeach
							</select>
							<div class="valid-feedback">Valid.</div>
							<div class="invalid-feedback">Por favor rellena el campo.</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm">
							<label for="curp">* CURP:</label>
							<input type="text" class="form-control text-uppercase" 
								value="{{ $p->curp }}" pattern="[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9]{2}" 
								minlength="18" maxlength="18" name="curp" readonly>
						</div>
						<div class="col-sm">
							<label for="semestre">* Puesto:</label>
							<select class="form-control" id="puesto" name="id_puesto" required> 
								<option value="{{ $p->id_puesto }}"> {{ $p->puesto }} </option>
								@foreach($puestos as $pu)
									<option value="{{ $pu->id_puesto }}"> {{ $pu->nombre }} </option>
								@endforeach
							</select>
							<div class="valid-feedback">Valid.</div>
							<div class="invalid-feedback">Por favor rellena el campo.</div>
               			</div>
					</div>
				@endif
			@endforeach
			<div class="form-group">
                <div class="col-sm">
					<label ><strong> * Campos Obligatorios</strong> </label>
                </div>
            </div>
            <div class="container">
                <div class="form-group">
                    <div class="col-sm"></div>
                    <div class="col-sm">
                        <button type="button" data-toggle="modal" data-target="#edit" 
							class="btn btn-outline-primary"> 
                            Guardar
                        </button>
                    </div>
                    <br>
                    <div class="col-sm">
                        <a href="{{ URL::previous() }}" class="btn btn-outline-danger"> 
                            Cancelar 
                        </a> 
                    </div>
                    <div class="col-sm"></div>
                </div>
            </div>
			<div class="modal fade" id="edit" data-backdrop="static" data-keyboard="false" 
				tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header" style="background-color: #1B396A;">
							<h5 class="modal-title text-white" id="staticBackdropLabel">
								<strong>EDITAR PERSONAL</strong>
							</h5>
							<button class="close text-white" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>  </button>
						</div>
						<div class="modal-body text-center">
							INTENTAS EDITAR UN(A) PERSONAL <br> ¿ESTÁS SEGURO DE ESTA ACCIÓN?<br>
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
	@if ($errors->any())
		@foreach ($errors->all() as $error)
			<div class="row">
				<div class="alert alert-danger">
					{{ $error }}
				</div>
			</div>
		@endforeach
	@endif
</div>
@endsection