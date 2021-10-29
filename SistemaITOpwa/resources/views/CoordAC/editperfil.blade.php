@extends('layouts.coordComple')
@section('content')
<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Mis Datos Generales </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('/cac/editperf') }}" class="needs-validation">
			@csrf
			@foreach($persona as $p)
            <div class="form-group">
                <div class="col-sm">
					<label for="nControl">* Grado:</label>
					<select class="form-control" id="grado" name="id_grado" required> 
						<option value="{{ $p->id_grado}}"> {{ $p->grado }} </option>
						@foreach($grados as $g)
							<option value="{{$g->id_grado}}" require> {{ $g->nombre }} </option>
						@endforeach
					</select>
					<div class="valid-feedback"></div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="nombre">* Nombre (s):</label>
					<input type="text" class="form-control" value="{{ $p->nombre }}" 
                        pattern="{[A-Z][a-z]+}+ *" minlength="4" name="nombre" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="apePat">* Apallido Paterno:</label>
					<input type="text" class="form-control" value="{{ $p->paterno }}" 
                        pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apePat" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="apeMat">* Apellido Materno:</label>
					<input type="text" class="form-control" value="{{ $p->materno }}" 
                        pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apeMat" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="carrera"> Departamento:</label>
					<input type="text" class="form-control" value="{{ $p->depto }}" name="id_depto" disabled>
                </div>
                <div class="col-sm">
					<label for="semestre"> Puesto:</label>
					<input type="text" class="form-control" value="{{ $p->puesto }}" name="id_puesto" disabled>
                </div>
            </div>
			<div class="form-group">
                <div class="col-sm">
					<label for="curp">* CURP:</label>
					<input type="text" class="form-control text-uppercase" value="{{ $p->curp }}" 
						pattern="[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9]{2}" minlength="18" maxlength="18" 
						name="curp" required>
                </div>
                <div class="col-sm">
                </div>
            </div>
			@endforeach
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
                    <br>
                    <div class="col-sm">
                        <a href="{{ url('CoordAC/datosGen') }}" class="btn btn-outline-danger"> 
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