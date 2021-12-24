@extends('layouts.jDeptos')
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
			<div class="card-header"> Registrar Nuevo Grupo </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{url('/dpt/regGrupo')}}" class="needs-validation">
            @csrf
            <div class="form-group">
                <div class="col-sm">
					<label for="clave">* Clave:</label>
					<input type="text" class="form-control text-uppercase" name="clave" 
						placeholder="Escribe la clave del grupo" 
						pattern="[G]{1}[A-Z]{3}[0-9]{3}|[g]{1}[a-z]{3}[0-9]{3}" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="periodo"> Periodo:</label>
					<input type="text" class="form-control" value="{{ $periodo->nombre }}" disabled>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>	
                </div>
            </div>
			<div class="form-group">
                <div class="col-sm">
					<label for="deptoper"> Departamento:</label>
					<input type="text" class="form-control" value="{{ $depto->nombre }}" disabled>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
			<div class="form-group">
                <div class="col-sm">
					<label for="actividad">* Actividad:</label>
					<select class="form-control" id="actividades" name="id_actividad" required> 
						<option value=""> Selecciona la actividad </option>
						@foreach($actividades as $a)
							<option value="{{ $a->id_actividad }}"> 
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
						<option value=""> Selecciona el responsable </option>
						@foreach($personas as $p)
							<option value="{{ $p->id_persona }}"> 
								{{ $p->grado }} {{ $p->nombre }} {{ $p->apePat }} {{ $p->apeMat }} 
							</option>
						@endforeach
					</select>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="cupo">* Cupo:</label>
					<input type="text" class="form-control" name="cupo" 
						placeholder="Escribe el cupo para el grupo" pattern="[0-9]{1,4}" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="lugar">* Tipo de oferta de selección:</label>
					<select class="form-control" id="orden" name="orden" required> 
						<option value=""> Selecciona el orden de oferta del grupo </option>
						<option value="1" require> Paralelo (Se ofertan al mismo tiempo)</option>
						<option value="0" require> Secuencial (Se oferta tras llenarse el grupo anterior)</option>
					</select>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="lugar">* Lugar:</label>
					<select class="form-control" id="lugar" name="id_lugar" required> 
						<option value=""> Selecciona el lugar </option>
						@foreach($lugares as $l)
							<option value="{{ $l->id_lugar }}" require> {{ $l->nombre }} </option>
						@endforeach
					</select>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
			<div class="form-group">
                <div class="col-sm">
					<label for="lugar">* Horario semanal:</label>
                </div>
            </div>
			<div class="form-group">
                <div class="col-sm">
					<label for="cupo"> Lunes:</label>
					<input type="time" class="form-control" name="lunes">
					<center><label for="">- a -</label></center>
					<input type="time" class="form-control" name="lunesf">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="cupo"> Martes:</label>
					<input type="time" class="form-control" name="martes">
					<center><label for="">- a -</label></center>
					<input type="time" class="form-control" name="martesf">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
				<div class="col-sm">
					<label for="cupo"> Miércoles:</label>
					<input type="time" class="form-control" name="miercoles">
					<center><label for="">- a -</label></center>
					<input type="time" class="form-control" name="miercolesf">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>	
            </div>
			<br>
			<div class="form-group">
				<div class="col-sm">
					<label for="cupo"> Jueves:</label>
					<input type="time" class="form-control" name="jueves">
					<center><label for="">- a -</label></center>
					<input type="time" class="form-control" name="juevesf">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
				<div class="col-sm">
					<label for="cupo"> Viernes:</label>
					<input type="time" class="form-control" name="viernes">
					<center><label for="">- a -</label></center>
					<input type="time" class="form-control" name="viernesf">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
				<div class="col-sm">
					<label for="cupo"> Sábado:</label>
					<input type="time" class="form-control" name="sabado">
					<center><label for="">- a -</label></center>
					<input type="time" class="form-control" name="sabadof">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
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
                    <div class="col-sm"></div>
                    <div class="col-sm">
                        <button type="submit" class="btn btn-outline-primary"> 
                            Registrar
                        </button>
                    </div>
					<br>
                    <div class="col-sm">
                        <a href="{{ url('/JDepto/grupos/1') }}" class="btn btn-outline-danger"> 
                            Cancelar 
                        </a> 
                    </div>
                    <div class="col-sm"></div>
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