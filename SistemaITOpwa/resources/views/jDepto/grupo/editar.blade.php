@extends('layouts.jDeptos')
@section('content')
<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Editar Grupo </div>
        </div>
    </div>
    <div class="card-body">
	@foreach($grupo as $g)
		<form method="POST" action="{{ url('/dpt/editGru').$g->id_grupo }}" class="needs-validation">
	@endforeach
			@csrf
			@foreach($grupo as $g)
            <div class="form-group">
                <div class="col-sm">
				<label for="clave">* Clave:</label>
									<input type="text" class="form-control" name="clave" value="{{ $g->clave }}" required>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
				<label for="periodo">* Periodo:</label>
									<input type="text" class="form-control" value="{{ $periodo->nombre }}" disabled>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
				<label for="actividad">* Actividad:</label>
									<select class="form-control" id="actividad" name="actividad" required> 
									    <option value="{{ $g->id_actividad }}"> {{ $g->actividad }} </option>
										@foreach($actividades as $a)
											<option value="{{ $a->id_actividad }}" require> {{ $a->nombre }} </option>
										@endforeach
									</select>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
				<label for="respon">* Responsable:</label>
									<select class="form-control" id="respon" name="responsable" required> 
									    <option value="{{ $g->id_persona }}"> {{ $g->nomP }} {{ $g->paterno }} {{ $g->materno }}</option>
										@foreach($personas as $p)
											<option value="{{ $p->id_persona }}" required> {{ $p->nombre }} {{ $p->apePat }} {{ $p->apeMat }} </option>
										@endforeach
									</select>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
				<label for="cupo">* Cupo:</label>
									<input type="text" class="form-control" name="cupo" value="{{ $g->cupo }}" require>
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
									<select class="form-control" id="lugar" name="lugar" required> 
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
					@if($hlun == null)
						<input type="time" class="form-control" name="lunes">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="lunesf">
					@else
					@foreach($hlun as $h1)
						<input type="time" class="form-control" name="lunes" value="{{ $h1 -> hora_inicio }}">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="lunesf" value="{{ $h1 -> hora_fin }}">
					@endforeach
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
					@else
						<input type="time" class="form-control" name="martes">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="martesf">
					@endif
						<div class="valid-feedback">Valid.</div>
						<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
				<div class="col-sm">
					<label for="cupo"> Miércoles:</label>
					@if($hmie == null)
						<input type="time" class="form-control" name="miercoles">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="miercolesf">
					@else
					@foreach($hmie as $h3)
						<input type="time" class="form-control" name="miercoles" value="{{ $h3 -> hora_inicio }}">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="miercolesf" value="{{ $h3 -> hora_fin }}">
					@endforeach
					@endif
						<div class="valid-feedback">Valid.</div>
						<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="cupo"> Jueves:</label>
					@if($hjue == null)
						<input type="time" class="form-control" name="jueves">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="juevesf">
					@else
					@foreach($hjue as $h4)
						<input type="time" class="form-control" name="jueves" value="{{ $h4 -> hora_inicio }}">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="juevesf" value="{{ $h4 -> hora_fin }}">
					@endforeach
					@endif
						<div class="valid-feedback">Valid.</div>
						<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
				<div class="col-sm">
					<label for="cupo"> Viernes:</label>
					@if($hvie == null)
						<input type="time" class="form-control" name="viernes">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="viernesf">
					@else
					@foreach($hvie as $h5)
						<input type="time" class="form-control" name="viernes" value="{{ $h5 -> hora_inicio }}">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="viernesf" value="{{ $h5 -> hora_fin }}">
					@endforeach
					@endif
						<div class="valid-feedback">Valid.</div>
						<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="cupo"> Sábado:</label>
					@if($hsab == null)
						<input type="time" class="form-control" name="sabado">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="sabadof">
					@else
					@foreach($hsab as $h6)
						<input type="time" class="form-control" name="sabado" value="{{ $h6 -> hora_inicio }}">
						<center><label for="">- a -</label></center>
						<input type="time" class="form-control" name="sabadof" value="{{ $h6 -> hora_fin }}">
					@endforeach
					@endif
						<div class="valid-feedback">Valid.</div>
						<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
			</div>
            <div class="form-group">
                <div class="col-sm">
                    <label ><strong> * Campos Obligatorios </strong></label>
                </div>
            </div>
            <div class="container">
                <div class="form-group">
                    <div class="col-sm"></div>
                    <div class="col-sm">
                        <button type="submit" class="btn btn-outline-primary"> 
                            Registrar
                        </button>
                    </div>
                    <br>
                    <div class="col-sm">
                        <a href="{{ url('JDepto/grupos/1') }}" class="btn btn-outline-danger"> 
                            Cancelar 
                        </a> 
                    </div>
                    <div class="col-sm"></div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection