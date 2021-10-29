@extends('layouts.coordComple')
@section('content')
<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Registrar un Nuevo Periodo </div>
        </div>
    </div>
    <div class="card-body">
		<form method="POST" action="{{url('/regPeriE')}}" class="needs-validation" enctype="multipart/form-data">
			@csrf
			<div class="form-group">
				<div class="col-sm">
					<em>
						<li>Recuerda que el periodo mínimo para registrar un semestre es de 16 semanas (4 meses).</li> 
						<li>Los procesos de Inscripción, Evaluación y Generación de Constancias requieren de un lapso 
						mínimo de 3 días entre el inicio y el final del proceso.</li>
						<hr>
					</em>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="semestre"><strong>*</strong> Inicio de Semestre:</label>
					<select name="mes_ini" id="mes_ini" class="form-control">
						<option value="">Mes de inicio</option>
						@foreach($mes as $m)
							<option value="{{ $m }}"> {{ $m }} </option>
						@endforeach
					</select>
				</div>
				<div class="col-sm">
					<label for="semestre" class="invisible"><strong>*</strong> Inicio de Semestre:</label>
					<select name="anio_ini" id="anio_ini" class="form-control">
						<option value="">Año de inicio</option>
						@foreach($año as $a)
							<option value="{{ $a }}"> {{ $a }} </option>
						@endforeach
					</select>
				</div>
				<div class="col-sm">
					<label for="semestre"><strong>*</strong> Fin de Semestre:</label><br>
					<select name="mes_fin" id="mes_fin" class="form-control">
						<option value="">Mes de fin</option>
						@foreach($mes as $m)
							<option value="{{ $m }}"> {{ $m }} </option>
						@endforeach
					</select>
				</div>
				<div class="col-sm">
					<label for="semestre" class="invisible"><strong>*</strong> Fin de Semestre:</label><br>
					<select name="anio_fin" id="anio_fin" class="form-control">
						<option value="">Año de fin</option>
						@foreach($año as $a)
							<option value="{{ $a }}"> {{ $a }} </option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="inicio"><strong>*</strong> Inicio de periodo:</label>
					<input type="date" class="form-control" title="Selecciona el inicio del periodo." 
						id="inicio" name="inicio" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="fin"><strong>*</strong> Fin de periodo:</label>
					<input type="date" class="form-control" id="fin" name="fin" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="ini_inscripcion"> Inicio de inscripciones:</label>
					<input type="date" class="form-control" id="ini_inscripcion" name="ini_inscripcion">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="fin_inscripcion"> Fin de inscripciones:</label>
					<input type="date" class="form-control" id="fin_inscripcion" name="fin_inscripcion">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="ini_evaluacion"> Inicio de evaluaciones:</label>
					<input type="date" class="form-control" id="ini_evaluacion" name="ini_evaluacion">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="fin_evaluacion"> Fin de evaluaciones:</label>
					<input type="date" class="form-control" id="fin_evaluacion" name="fin_evaluacion">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="ini_gconstancias"> Inicio de G. constancias:</label>
					<input type="date" class="form-control" id="ini_gconstancias" name="ini_gconstancias">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="fin_gconstancias"> Fin de G. constancias:</label>
					<input type="date" class="form-control" id="fin_gconstancias" name="fin_gconstancias">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="logoIto"> Cabecera de constancias:</label>
					<div class="custom-file col">
						<input type="file" class="custom-file-input" id="Ito" name="cabecera">
						<label class="custom-file-label" id="logI" for="customFile">Selecciona el archivo</label>
					</div>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="logoEnca"> Pie de pagina de constancias:</label>
					<div class="custom-file col">
						<input type="file" class="custom-file-input" id="Enca" name="pie">
						<label class="custom-file-label" id="logE" name="logE" for="customFile">Selecciona el archivo</label>
					</div>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label > <strong> * Campos Obligatorios </strong> </label>
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
					<div class="col-sm">
						<a href="{{ url('CoordAC/periodos/1') }}" class="btn btn-outline-danger"> 
							Cancelar 
						</a> 
					</div>
					<div class="col-sm"></div>
				</div>
			</div>
		</form>
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
</div>
@endsection