@extends('layouts.jDeptos')
@section('content')
<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
					<div class="card-header"> Mis Datos Generales </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('/dpt/editPerfil') }}" class="needs-validation">
            @csrf
			@foreach($persona as $p)
            <div class="form-group">
                <div class="col-sm">
					<label for="nControl">* Grado:</label>
					@if($editar == '1') 
						<select class="form-control" id="grado" name="grado" required>
					@else
						<select class="form-control" id="grado" name="grado" disabled>
					@endif
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
					@if($editar == '1')
						<input type="text" class="form-control" value="{{ $p->nombre }}" 
							pattern="{[A-Z][a-z]+}+ *" minlength="4" name="nombre" required>
					@else
						<input type="text" class="form-control" value="{{ $p->nombre }}" 
							pattern="{[A-Z][a-z]+}+ *" minlength="4" name="nombre" disabled>
					@endif
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>			
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="apePat">* Apallido Paterno:</label>
					@if($editar == '1')
						<input type="text" class="form-control" value="{{ $p->paterno }}" 
							pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apePat" required>
					@else
						<input type="text" class="form-control" value="{{ $p->paterno }}" 
							pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apePat" disabled>
					@endif
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="apeMat">* Apellido Materno:</label>
					@if($editar == '1')
						<input type="text" class="form-control" value="{{ $p->materno }}" 
							pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apeMat" required>
					@else
						<input type="text" class="form-control" value="{{ $p->materno }}" 
							pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apeMat" disabled>
					@endif
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="carrera">* Departamento:</label>
					<input type="text" class="form-control" value="{{ $depto->nombre }}" disabled>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="semestre">* Puesto:</label>
					<input type="text" class="form-control" value="{{ $p->puesto }}" disabled>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="curp">* CURP:</label>
					@if($editar == '1')
						<input type="text" class="form-control" value="{{ $p->curp }}" 
							pattern="[A-Z]+[0-9]+[A-Z]+[0-9]*" minlength="18" maxlength="18" 
							name="curp" required>
					@else
						<input type="text" class="form-control" value="{{ $p->curp }}" 
							pattern="[A-Z]+[0-9]+[A-Z]+[0-9]*" minlength="18" maxlength="18" 
							name="curp" disabled>
					@endif
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
                </div>
            </div>
			@endforeach
            <div class="form-group">
                <div class="col-sm">
                    <label ><strong> * Campos Obligatorios </strong></label>
                </div>
            </div>

            <div class="container">
                <div class="form-group">
				@if($editar == '1')
					<div class="col-sm"></div>
					<div class="col-sm">
						<button type="submit" class="btn btn-outline-primary"> 
							Actualizar
						</button>
					</div>
					<br>
					<div class="col-sm">
						<a href="{{ url('JDepto/datgen') }}" class="btn btn-outline-danger"> 
							Cancelar 
						</a> 
					</div>
					<div class="col-sm"></div>
				@else
					<div class="col-sm">
						<a href="{{ url('JDepto/cambcontrasenia') }}" class="btn btn-outline-primary"> 
							Cambiar contraseña 
						</a>
					</div>
					<div class="col-sm">
						<a href="{{ url('JDepto/editperf') }}" class="btn btn-outline-primary"> 
							Editar 
						</a>
					</div>
					<br>
					<div class="col-sm">
						<a href="{{ url('JDepto') }}" class="btn btn-outline-danger"> 
							Regresar 
						</a> 
					</div>
				@endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection