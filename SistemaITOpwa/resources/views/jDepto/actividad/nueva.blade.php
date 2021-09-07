@extends('layouts.jDeptos')
@section('content')
<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Registrar Nueva Actvidad </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('/dpt/regAct') }}" class="needs-validation">
            @csrf
            <div class="form-group">
                <div class="col-sm">
					<label for="nControl">* Clave:</label>
					<input type="text" class="form-control" placeholder="Clave de la actividad" 
						pattern="[A-Z][0-9]*[a-Z]*" name="clave" required>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="nombre">* Nombre:</label>
					<input type="text" class="form-control" id="nombre" placeholder="Nombre(s)" 
						pattern="{[A-Z][a-z]+}+ *" name="nombre" required>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>	
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="apeMat">* Departamento:</label>
					<input type="text" class="form-control" value="{{ $depto->nombre }}" disabled>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="apePat">* Número de Créditos:</label>
					<select class="form-control" id="cred" name="creditos" require> 
						<option value=""> Selecciona los créditos </option>
						<option value="1" > Un crédito </option>
						<option value="2" > Dos créditos </option>
					</select>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="carrera">* Tipo de actividad:</label>
					<select class="form-control" id="carrera" name="tipo" required> 
						<option value=""> Selecciona un Tipo </option>
						@foreach($tipos as $t)
							<option value="{{$t->id_tipo}}" require> {{ $t->nombre }} </option>
						@endforeach
					</select>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
			<div class="form-group">
                <div class="col-sm">
					<label for="periodo">* Periodo:</label>
					<input type="text" class="form-control" value="{{ $periodo->nombre }}" disabled>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="carrera">* Restringida:</label>
					<select class="form-control" id="restric" name="restringida" required> 
						<option value=""> Selecciona SI/NO </option>
						<option value="1" require> <strong>SI</strong> Restringida </option>
						<option value="0" require> <strong>NO</strong> Restringida </option>
					</select>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="curp">Descripción:</label>
					<textarea type="textarea" class="form-control" 
						placeholder="Escribe una descripción de la actividad" 
						maxlength="250" name="descripcion"></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label ><strong> * Campos Obligatorios </strong></label>
                </div>
                <div class="col-sm">
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
                        <a href="{{ url('JDepto/actividad/1') }}" class="btn btn-outline-danger"> 
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