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
					<input type="date" class="form-control" name="inicio" value="{{ $p->inicio}}" readonly>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="finPeri"> Fin de periodo:</label>
					<input type="date" class="form-control" name="fin" value="{{ $p->fin }}" readonly>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="ini_inscripcion">* Inicio de inscripciones:</label>
					<input type="date" class="form-control" name="ini_inscripcion" value="{{ $p-> ini_inscripcion }}" >
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="fin_inscripcion">* Fin de inscripciones:</label>
					<input type="date" class="form-control" name="fin_inscripcion" value="{{ $p-> fin_inscripcion }}" >
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="ini_evaluacion">* Inicio de evaluaciones:</label>
					<input type="date" class="form-control" name="ini_evaluacion" value="{{ $p-> ini_evaluacion }}" >
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="fin_evaluacion">* Fin de evaluaciones:</label>
					<input type="date" class="form-control" name="fin_evaluacion" value="{{ $p-> fin_evaluacion }}" >
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="ini_gconstancias">* Inicio de G. constancias:</label>
					<input type="date" class="form-control" name="ini_gconstancias" value="{{ $p->ini_gconstancias }}" >
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="fin_gconstancias">* Fin de G. constancias:</label>
					<input type="date" class="form-control" name="fin_gconstancias" value="{{ $p->fin_gconstancias }}" >
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="logoIto"> Cabecera de constancias:</label>
					<br>
					<img id="logoIto" src="{{ $p->logo_ito }}" width="100%" class="img-thumbnail float-right">
					<br><br>
					<div class="custom-file col">
						<input type="file" class="custom-file-input" id="Ito" name="cabecera">
						<label class="custom-file-label" id="logI" for="customFile">Selecciona el archivo</label>
					</div>
				</div>
				<div class="col-sm">
					<label for="logoEnca"> Pie de pagina de constancias:</label>
					<br>
					<img id="logoEnca" src="{{ $p->logo_anio }}" width="100%" class="img-thumbnail">
					<br><br>
					<div class="custom-file col">
						<input type="file" class="custom-file-input" id="Enca" name="pie">
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