@extends('layouts.coordComple')
@section('content')

	<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Detalle del Periodo </div>
					<div class="card-body">
					
						<form method="POST" action="" class="needs-validation">
							@csrf
                            @foreach($periodo as $p)
							<div class="row">
								<div class="form-group col">
									<label for="semestre"> Nombre:</label>
									<input type="text" class="form-control" name="nombre" value="{{ $p->nombre }}" disabled>
									<div class="valid-feedback"></div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

							</div>

							<div class="row">
								<div class="form-group col">
									<label for="iniPeri"> Inicio de periodo:</label>
									<input type="date" class="form-control" name="iniPeri" value="{{ $p->inicio}}" disabled>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="finPeri"> Fin de periodo:</label>
									<input type="date" class="form-control" name="finPeri" value="{{ $p->fin }}" disabled>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="iniIns"> Inicio de inscripciones:</label>
									<input type="date" class="form-control" name="iniIns" value="{{ $p-> ini_inscripcion }}" disabled>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="finIns"> Fin de inscripciones:</label>
									<input type="date" class="form-control" name="finIns" value="{{ $p-> fin_inscripcion }}" disabled>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="iniEval"> Inicio de evaluaciones:</label>
									<input type="date" class="form-control" name="iniEval" value="{{ $p-> ini_evaluacion }}" disabled>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="finEval"> Fin de evaluaciones:</label>
									<input type="date" class="form-control" name="finEval" value="{{ $p-> fin_evaluacion }}" disabled>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div>

                            <div class="row">
								<div class="form-group col">
									<label for="iniGcons"> Inicio de G. constancias:</label>
									<input type="date" class="form-control" name="iniGcons" value="{{ $p->ini_gconstancias }}" disabled>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>

								<div class="form-group col">
									<label for="finGcons"> Fin de G. constancias:</label>
									<input type="date" class="form-control" name="finGcons" value="{{ $p->fin_gconstancias }}" disabled>
									<div class="valid-feedback">Valid.</div>
									<div class="invalid-feedback">Por favor rellena el campo.</div>
								</div>
							</div> 

                            <div class="row">
								<div class="form-group col">
                                    <label for="logoGob"> Logo Gobierno:</label>
									<br>

									<img id="logoGob" src="{{ $p->logo_gob }}" width="90%" class="img-thumbnail">

								</div>

								<div class="form-group col">
                                <label for="logoTecNM"> Logo Tecnológico Nacional de México:</label>
								<br>

									<img id="logoTecNM" src="{{ $p->logo_tecnm }}" width="45%" class="img-thumbnail float-right">

								</div>
							</div>

							<div class="row">
								<div class="form-group col">
                                    <label for="logoEnca"> Logo Año (Encabezado):</label>
									<br>
									
									<img id="logoEnca" src="{{ $p->logo_anio }}" width="100%" class="img-thumbnail">
									
								</div>

								<div class="form-group col">	
                                <label for="logoIto"> Logo Instituto Tecnológico de Oaxaca:</label>
								<br>
									
									<img id="logoIto" src="{{ $p->logo_ito }}" width="30%" class="img-thumbnail float-right">
									
								</div>
							</div>

							<div class="row">
									<!-- <label > * Campos Obligatorios </label> -->
							</div>
							@if($p-> estado == 'Actual')
								<center> 
									<a href="{{ url('CoordAC/periodos/1') }}" class="btn btn-outline-primary"> Regresar </a> 
									<label class="text-primary"> | </label>
									<a href="{{ url('CoordAC/editPeri').'/'.$p->id_periodo }}" class="btn btn-outline-primary"> Editar </a> 
								</center>
							@else
								<center> 
									<a href="{{ URL::previous() }}" class="btn btn-outline-primary"> Regresar </a> 
								</center>
							@endif
						@endforeach
                        </form>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection