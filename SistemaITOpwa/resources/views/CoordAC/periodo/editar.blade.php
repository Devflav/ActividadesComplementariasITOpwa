@extends('layouts.coordComple')
@section('content')
<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Editar Periodo </div>
        </div>
    </div>
    <div class="card-body">
	@foreach($periodo as $p)
		<form method="POST" action="{{ url('/update/periodo').'/'.$p->id_periodo }}" class="needs-validation" enctype="multipart/form-data">
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
					<label for="semestre"> Nombre:</label>
					<input type="text" class="form-control" name="nombre" value="{{ $p->nombre }}" disabled>
					<div class="valid-feedback"></div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="iniPeri"> Inicio de periodo:</label>
					<input type="date" class="form-control" name="iniPeri" value="{{ $p->inicio}}" disabled>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="finPeri"> Fin de periodo:</label>
					<input type="date" class="form-control" name="finPeri" value="{{ $p->fin }}" disabled>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="iniIns">* Inicio de inscripciones:</label>
					<input type="date" class="form-control" name="iniIns" value="{{ $p-> ini_inscripcion }}" >
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="finIns">* Fin de inscripciones:</label>
					<input type="date" class="form-control" name="finIns" value="{{ $p-> fin_inscripcion }}" >
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="iniEval">* Inicio de evaluaciones:</label>
					<input type="date" class="form-control" name="iniEval" value="{{ $p-> ini_evaluacion }}" >
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="finEval">* Fin de evaluaciones:</label>
					<input type="date" class="form-control" name="finEval" value="{{ $p-> fin_evaluacion }}" >
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="iniGcons">* Inicio de G. constancias:</label>
					<input type="date" class="form-control" name="iniGcons" value="{{ $p->ini_gconstancias }}" >
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="finGcons">* Fin de G. constancias:</label>
					<input type="date" class="form-control" name="finGcons" value="{{ $p->fin_gconstancias }}" >
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="logoSep"> Logo Gobierno:</label>
					<br>
					<img id="logoGob" src="{{ $p->logo_gob }}" width="90%" class="img-thumbnail">
					<br><br>
					<div class="custom-file col">
						<input type="file" class="custom-file-input" id="Gob" name="newgobierno">
						<label class="custom-file-label" id="logS" name="logS" for="customFile">Selecciona el archivo</label>
					</div>
				</div>
				<div class="col-sm">
					<label for="logoTecNM"> Logo Tecnológico Nacional de México:</label>
					<br>
					<img id="logoTecNM" src="{{ $p->logo_tecnm }}" width="45%" class="img-thumbnail float-right">
					<div class="custom-file col">
						<input type="file" class="custom-file-input" id="TecNM" name="newtecnmlog">
						<label class="custom-file-label" id="logt" for="customFile">Selecciona el archivo</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="logoIto"> Logo Instituto Tecnológico de Oaxaca:</label>
					<br>
					<img id="logoIto" src="{{ $p->logo_ito }}" width="30%" class="img-thumbnail float-right">
					<br><br>
					<div class="custom-file col">
						<input type="file" class="custom-file-input" id="Ito" name="newitolog">
						<label class="custom-file-label" id="logI" for="customFile">Selecciona el archivo</label>
					</div>
				</div>
				<div class="col-sm">
					<label for="logoEnca"> Logo Año (Encabezado):</label>
					<br>
					<img id="logoEnca" src="{{ $p->logo_anio }}" width="100%" class="img-thumbnail">
					<br><br>
					<div class="custom-file col">
						<input type="file" class="custom-file-input" id="Enca" name="newencabezado">
						<label class="custom-file-label" id="logE" name="logE" for="customFile">Selecciona el archivo</label>
					</div>
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
						<button type="button" data-toggle="modal" data-target="#edit" 
							class="btn btn-outline-primary"> Actualizar 
						</button> 
					</div>
					<div class="col-sm">
						<a href="{{ url('CoordAC/detallePeri').$p->id_periodo }}" 
							class="btn btn-outline-danger"> 
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
								<strong>EDITAR PERIODO</strong>
							</h5>
							<button class="close text-white" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>  </button>
						</div>
						<div class="modal-body text-center">
							INTENTAS EDITAR UN(A) PERIODO <br> ¿ESTÁS SEGURO DE ESTA ACCIÓN?<br>

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
</div>
@endsection