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
					<select name="mesi" id="mesi" class="form-control">
						<option value="">Mes de inicio</option>
						@foreach($mes as $m)
							<option value="{{ $m }}"> {{ $m }} </option>
						@endforeach
					</select>
				</div>
				<div class="col-sm">
					<label for="semestre" class="invisible"><strong>*</strong> Inicio de Semestre:</label>
					<select name="anioi" id="anioi" class="form-control">
						<option value="">Año de inicio</option>
						@foreach($año as $a)
							<option value="{{ $a }}"> {{ $a }} </option>
						@endforeach
					</select>
				</div>
				<div class="col-sm">
					<label for="semestre"><strong>*</strong> Fin de Semestre:</label><br>
					<select name="mesf" id="mesf" class="form-control">
						<option value="">Mes de fin</option>
						@foreach($mes as $m)
							<option value="{{ $m }}"> {{ $m }} </option>
						@endforeach
					</select>
				</div>
				<div class="col-sm">
					<label for="semestre" class="invisible"><strong>*</strong> Fin de Semestre:</label><br>
					<select name="aniof" id="aniof" class="form-control">
						<option value="">Año de fin</option>
						@foreach($año as $a)
							<option value="{{ $a }}"> {{ $a }} </option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="iniPeri"><strong>*</strong> Inicio de periodo:</label>
					<input type="date" class="form-control" title="Selecciona el inicio del periodo." 
						id="iniPeri" name="iniPeri" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="finPeri"><strong>*</strong> Fin de periodo:</label>
					<input type="date" class="form-control" id="finPeri" name="finPeri" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="iniIns"> Inicio de inscripciones:</label>
					<input type="date" class="form-control" id="iniIns" name="iniIns">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="finIns"> Fin de inscripciones:</label>
					<input type="date" class="form-control" id="finIns" name="finIns">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="iniEval"> Inicio de evaluaciones:</label>
					<input type="date" class="form-control" id="iniEval" name="iniEval">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="finEval"> Fin de evaluaciones:</label>
					<input type="date" class="form-control" id="finEval" name="finEval">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="iniGcons"> Inicio de G. constancias:</label>
					<input type="date" class="form-control" id="iniGcons" name="iniGcons">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="finGcons"> Fin de G. constancias:</label>
					<input type="date" class="form-control" id="finGcons" name="finGcons">
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="logoSep"> Logo Gobierno:</label>
					<div class="custom-file col">
						<input type="file" class="custom-file-input" id="Gob" name="gobierno">
						<label class="custom-file-label" id="logS" name="logS" for="customFile">
							Selecciona el archivo
						</label>
					</div>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="logoTecNM"> Logo Tecnológico Nacional de México:</label>
					<div class="custom-file col">
						<input type="file" class="custom-file-input" id="TecNM" name="tecnmlog">
						<label class="custom-file-label" id="logt" for="customFile">Selecciona el archivo</label>
					</div>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="logoIto"> Logo Instituto Tecnológico de Oaxaca:</label>
					<div class="custom-file col">
						<input type="file" class="custom-file-input" id="Ito" name="itolog">
						<label class="custom-file-label" id="logI" for="customFile">Selecciona el archivo</label>
					</div>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="logoEnca"> Logo Año (Encabezado):</label>
					<div class="custom-file col">
						<input type="file" class="custom-file-input" id="Enca" name="encabezado">
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
    </div>
</div>
@endsection