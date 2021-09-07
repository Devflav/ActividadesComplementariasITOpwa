@extends('layouts.coordComple')
@section('content')
<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Nueva Fecha de Suspensión </div>
        </div>
    </div>
    <div class="card-body">
		<form method="POST" action="{{url('/regFecha')}}" class="needs-validation">
			@csrf
			<div class="form-group">
				<div class="col-sm">
					<label for="nomCritE">* Fecha (Única / Inicio):</label>
					<input type="date" class="form-control" placeholder="Selecciona la fecha" name="fecha" required>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
				<div class="col-sm">
					<label for="nomCritE"> Fecha (Fin):</label>
					<input type="date" class="form-control" placeholder="Selecciona la fecha" name="fechafin">
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>	
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label for="desCritE">* Motivo:</label>
					<textarea type="text" class="form-control text-uppercase" 
					placeholder="Escribe el motivo de la suspención" 
					pattern="{[A-Z][a-z]+}+ *" name="motivo" required></textarea>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>	
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm">
					<label class="text-justify">Para agregar una sola fecha de suspención basta con escoger 
					la fecha en el lado izquierdo (Única / Inicio). Solo si desea agregar un conjunto de
					fechas para la suspención escoja la fecha de inicio de la suspencíon del lado
					izquierdo y la fecha para el término de la suspención del lado derecho.</label>
					<label><strong>* Campos obligatorios</strong></label>
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
						<a href="{{ URL::previous() }}" class="btn btn-outline-danger"> 
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