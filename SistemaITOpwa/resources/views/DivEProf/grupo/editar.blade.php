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
			<div class="card-header"> Editar Grupo </div>
        </div>
    </div>
    <div class="card-body">
	@foreach($grupo as $g)
		<form method="POST" action="{{ url('DivEProf/update/grupo').'/'.$g->id_grupo }}" class="needs-validation">
	@endforeach
		@csrf
		@foreach($grupo as $g)
            <div class="form-group">
                <div class="col-sm">
					<label for="clave">* Clave:</label>
					<input type="text" class="form-control text-uppercase" name="clave" 
						value="{{ $g->clave }}" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="periodo">* Periodo:</label>
						@foreach($periodos as $p)
							<input type="text" class="form-control" value="{{ $p->nombre }}" disabled>
						@endforeach
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>	
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="lugar"> Departamento:</label>
					<select class="form-control" id="dptedit" name="deptoedit"> 
						<option value=""> (Filtra las actividades y el responsable por departamento) </option>
						@foreach($deptos as $d)
							<option value="{{ $d->id_depto }}" departamento="{{ $d->id_depto }}"> 
								{{ $d->nombre }} 
							</option>
						@endforeach
					</select>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
			<div class="form-group">
                <div class="col-sm">
					<label for="actividad">* Actividad:</label>
					<select class="form-control" id="actividad" name="id_actividad" required> 
						<option value="{{ $g->id_actividad }}"> {{ $g->creditos }}C - {{ $g->aClave }} - {{ $g->actividad }} </option>
						@foreach($actividades as $a)
							<option value="{{ $a->id_actividad }}" require> 
								{{ $a->creditos }}C - {{ $a->clave}} - {{ $a->nombre }}
							</option>
						@endforeach
					</select>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
			<div class="form-group">
                <div class="col-sm">
					<label for="respon">* Responsable:</label>
					<select class="form-control" id="respon" name="id_persona" required> 
						<option value="{{ $g->id_persona }}"> {{ $g->grado }} {{ $g->nomP }} {{ $g->paterno }} {{ $g->materno }}</option>
						@foreach($personas as $p)
							<option value="{{ $p->id_persona }}" required> {{ $p->grado }} {{ $p->nombre }} {{ $p->apePat }} {{ $p->apeMat }} </option>
						@endforeach
					</select>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="cupo">* Cupo:</label>
					<input type="number" class="form-control" name="cupo" 
						min="0" value="{{ $g->cupo }}">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
			<div class="form-group">
                <div class="col-sm">
					<label for="lugar">* Tipo de oferta de selección:</label>
					<select class="form-control" id="orden" name="orden" required> 
						<option value="{{ $g->orden }}"> @if($g->oorden = 1)
															Paralelo
														@else
															Secuancial
														@endif </option>
						<option value="1" require> Paralelo (Se ofertan al mismo tiempo)</option>
						<option value="0" require> Secuencial (Se oferta tras llenarse el grupo anterior)</option>
					</select>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="lugar">* Lugar:</label>
					<select class="form-control" id="lugar" name="id_lugar" required> 
						<option value="{{ $g->id_lugar }}"> {{ $g->lugar }} </option>
						@foreach($lugares as $l)
							<option value="{{ $l->id_lugar }}" require> {{ $l->nombre }} </option>
						@endforeach
					</select>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
		@endforeach
			<div class="form-group">
                <div class="col-sm">
					<label for="lugar">* Horario semanal:</label>
                </div>
            </div>
			<div class="form-group">
                <div class="col-sm">
					<label for="cupo"> Lunes:</label>
					@if($hlun != null)
					@foreach($hlun as $h1)
						<input type="time" class="form-control" name="lunes" value="{{ $h1 -> hora_inicio }}">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="lunesf" value="{{ $h1 -> hora_fin }}">
					@endforeach
					@elseif(!$hlun)
						<input type="time" class="form-control" name="lunes">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="lunesf">
					@endif
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="cupo"> Martes:</label>
					@if($hmar != null)
					@foreach($hmar as $h2)
						<input type="time" class="form-control" name="martes" value="{{ $h2 -> hora_inicio }}">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="martesf" value="{{ $h2 -> hora_fin }}">
					@endforeach
					@elseif(!$hmar)
						<input type="time" class="form-control" name="martes">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="martesf">
					@endif
						<div class="valid-feedback">Valid.</div>
						<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
				<div class="col-sm">
					<label for="cupo"> Miércoles:</label>
					@if($hmie != null)
					@foreach($hmie as $h3)
						<input type="time" class="form-control" name="miercoles" value="{{ $h3 -> hora_inicio }}">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="miercolesf" value="{{ $h3 -> hora_fin }}">
					@endforeach
					@elseif(!$hmie)
						<input type="time" class="form-control" name="miercoles">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="miercolesf">
					@endif
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="cupo"> Jueves:</label>
					@if($hjue != null)
					@foreach($hjue as $h4)
						<input type="time" class="form-control" name="jueves" value="{{ $h4 -> hora_inicio }}">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="juevesf" value="{{ $h4 -> hora_fin }}">
					@endforeach
					@elseif(!$hjue)
						<input type="time" class="form-control" name="jueves">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="juevesf">
					@endif
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="cupo"> Viernes:</label>
					@if($hvie != null)
					@foreach($hvie as $h5)
						<input type="time" class="form-control" name="viernes" value="{{ $h5 -> hora_inicio }}">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="viernesf" value="{{ $h5 -> hora_fin }}">
					@endforeach
					@elseif(!$hvie)
						<input type="time" class="form-control" name="viernes">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="viernesf">
					@endif
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="cupo"> Sábado:</label>
					@if($hsab != null)
					@foreach($hsab as $h6)
						<input type="time" class="form-control" name="sabado" value="{{ $h6 -> hora_inicio }}">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="sabadof" value="{{ $h6 -> hora_fin }}">
					@endforeach
					@elseif(!$hsab)
						<input type="time" class="form-control" name="sabado">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="sabadof">
					@endif
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
				<div class="col-sm">
                </div>
                <div class="col-sm">
                </div>
			</div>
            <div class="form-group">
                <div class="col-sm">
					<label > 
						<strong>
							* Campos Obligatorios
						</strong> 
					</label>
                </div>
            </div>
            <div class="container">
                <div class="form-group">
                    <div class="col-sm">
						<a onclick="eliminar('{{ url('/DivEProf/eliminar/grupo').'/'.$g->id_grupo }}')" 
							class="btn btn-outline-danger"> 
							Eliminar 
						</a> 
					</div>
					<br>
                    <div class="col-sm">
                        <button type="button" data-toggle="modal" data-target="#edit" 
							class="btn btn-outline-primary"> 
                            Guardar
                        </button>
                    </div>
                    <br>
                    <div class="col-sm">
                        <a href="{{ url('/DivEProf/grupos/1') }}" class="btn btn-outline-danger"> 
                            Cancelar 
                        </a> 
                    </div>
                </div>
            </div>
			<div class="modal fade" id="edit" data-backdrop="static" data-keyboard="false" 
				tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header" style="background-color: #1B396A;">
							<h5 class="modal-title text-white" id="staticBackdropLabel">
								<strong>EDITAR GRUPO</strong>
							</h5>
							<button class="close text-white" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>  </button>
						</div>
						<div class="modal-body text-center">
							INTENTAS EDITAR UN(A) GRUPO <br> ¿ESTÁS SEGURO DE ESTA ACCIÓN?<br>

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