@extends('layouts.divEProf')
@section('content')
<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Editar Actvidad </div>
        </div>
    </div>
	<div class="card-body">
	@foreach($actividad as $a)						
		<form method="GET" action="{{ url('/DivEProf/update/actividad').'/'.$a->id_actividad }}" 
			class="needs-validation">
	@endforeach
		@csrf
		@foreach($actividad as $a)
			<div class="form-group">
				<div class="col-sm">
					<label for="nControl">* Clave:</label>
					<input type="text" class="form-control text-uppercase" value="{{ $a->clave }}" 
						placeholder="Clave de la actividad" pattern="[A-Z][0-9]*[a-Z]*" 
						name="clave" required>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="nombre">* Nombre:</label>
					<input type="text" class="form-control text-uppercase" value="{{ $a->nombre }}" 
						placeholder="Nombre(s)" pattern="{[A-Z][a-z]+}+ *" 
						name="nombre" required>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>	
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="apePat">* Número de Creditos:</label>
					<input type="text" class="form-control" value="{{ $a->creditos }}" 
						placeholder="Número de creditos" pattern="[1-2]" name="creditos" disabled>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="apeMat">* Departamento:</label>
					<select class="form-control" id="carrera" name="depto" required> 
						<option value="{{ $a->id_depto }}"> {{ $a->depto }} </option>
						@foreach($deptos as $d)
							<option value="{{$d->id_depto}}" require> {{ $d->nombre }} </option>
						@endforeach
					</select>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="carrera">* Tipo de actividad:</label>
					<select class="form-control" id="carrera" name="tipo" required> 
						<option value="{{ $a->id_tipo }}"> {{ $a->tipo }} </option>
						@foreach($tipos as $t)
							<option value="{{$t->id_tipo}}" require> {{ $t->nombre }} </option>
						@endforeach
					</select>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="curp">Descripción:</label>
					<textarea type="text-area" class="form-control" value="{{ $a->descripcion }}" }
						maxlength="250" name="descripcion"></textarea>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label > <strong>* Campos Obligatorios</strong> </label>
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
						<a href="{{ url('/DivEProf/actividades/1') }}" class="btn btn-outline-danger"> 
							Cancelar 
						</a> 
					</div>
					<div class="col-sm"></div>
				</div>
			</div>
		@endforeach

			<div class="modal fade" id="edit" data-backdrop="static" data-keyboard="false" 
				tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header" style="background-color: #1B396A;">
							<h5 class="modal-title text-white" id="staticBackdropLabel">
								<strong>EDITAR ACTIVIDAD</strong>
							</h5>
							<button class="close text-white" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>  </button>
						</div>
						<div class="modal-body text-center">
							INTENTAS EDITAR UN(A) ACTIVIDAD <br> ¿ESTÁS SEGURO DE ESTA ACCIÓN? <br>

						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-outline-primary" data-dismiss="modal">
								Cerar
							</button>
							<button type="submit" class="btn btn-outline-danger">Editar</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<button type="button" class="btn btn-primary d-none" data-toggle="modal" 
		data-target="#mimodal" id="btn_mimodal">
	</button>
	<div class="modal fade" id="mimodal" data-backdrop="static" data-keyboard="false" 
		tabindex="-1" aria-labelledby="restablecerLabel" aria-hidden="true">
	</div>
</div>
@endsection