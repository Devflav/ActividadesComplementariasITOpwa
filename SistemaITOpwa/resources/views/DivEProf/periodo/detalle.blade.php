@extends('layouts.divEProf')
@section('content')
<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Detalle del Periodo </div>
        </div>
    </div>
    <div class="card-body">
	@foreach($periodo as $p)
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
				<input type="date" class="form-control" name="iniIns" value="{{ $p-> ini_inscripcion }}" disabled>
				<div class="valid-feedback">Valid.</div>
				<div class="invalid-feedback">Por favor rellena el campo.</div>
			</div>
			<div class="col-sm">
				<label for="finIns">* Fin de inscripciones:</label>
				<input type="date" class="form-control" name="finIns" value="{{ $p-> fin_inscripcion }}" disabled>
				<div class="valid-feedback">Valid.</div>
				<div class="invalid-feedback">Por favor rellena el campo.</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm">
				<label for="iniEval">* Inicio de evaluaciones:</label>
				<input type="date" class="form-control" name="iniEval" value="{{ $p-> ini_evaluacion }}" disabled>
				<div class="valid-feedback">Valid.</div>
				<div class="invalid-feedback">Por favor rellena el campo.</div>
			</div>
			<div class="col-sm">
				<label for="finEval">* Fin de evaluaciones:</label>
				<input type="date" class="form-control" name="finEval" value="{{ $p-> fin_evaluacion }}" disabled>
				<div class="valid-feedback">Valid.</div>
				<div class="invalid-feedback">Por favor rellena el campo.</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm">
				<label for="iniGcons">* Inicio de G. constancias:</label>
				<input type="date" class="form-control" name="iniGcons" value="{{ $p->ini_gconstancias }}" disabled>
				<div class="valid-feedback">Valid.</div>
				<div class="invalid-feedback">Por favor rellena el campo.</div>
			</div>
			<div class="col-sm">
				<label for="finGcons">* Fin de G. constancias:</label>
				<input type="date" class="form-control" name="finGcons" value="{{ $p->fin_gconstancias }}" disabled>
				<div class="valid-feedback">Valid.</div>
				<div class="invalid-feedback">Por favor rellena el campo.</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm">
				<label for="logoIto"> Cabecera de constancias:</label>
				<br>
				<img id="logoIto" src="{{ $p->cabecera }}" width="100%" class="img-thumbnail float-right">
			</div>
			<div class="col-sm">
				<label for="logoEnca"> Pie de pagina de constancias:</label>
				<br>
				<img id="logoEnca" src="{{ $p->pie }}" width="100%" class="img-thumbnail">
			</div>
		</div>
			<div class="container">
				<div class="form-group">
					<div class="col-sm"></div>
					<div class="col-sm">
						<a href="{{ url('DivEProf/periodos/1') }}" class="btn btn-outline-primary"> 
							Regresar 
						</a>
					</div>
					<div class="col-sm"></div>
				</div>
			</div>
	@endforeach
    </div>
</div>
@endsection