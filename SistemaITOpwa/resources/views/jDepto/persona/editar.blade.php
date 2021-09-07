@extends('layouts.jDeptos')
@section('content')
<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Editar Personal </div>
        </div>
    </div>
    <div class="card-body">
	@foreach($persona as $p)
		<form method="POST" action="{{ url('/dpt/editpers').$p->id_persona }}" class="needs-validation">
	@endforeach
			@csrf
			@foreach($persona as $p)
            <div class="form-group">
                <div class="col-sm">
					<label for="nControl">* Grado:</label>
					<select class="form-control" id="grado" name="grado" required> 
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
					<input type="text" class="form-control" value="{{ $p->nombre }}" pattern="{[A-Z][a-z]+}+ *" minlength="4" name="nombre" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>	
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="apePat">* Apallido Paterno:</label>
					<input type="text" class="form-control" value="{{ $p->paterno }}" pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apePat" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="apeMat">* Apellido Materno:</label>
					<input type="text" class="form-control" value="{{ $p->materno }}" pattern="{[A-Z][a-z]+}+ *" minlength="4" name="apeMat" required>
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
					<input type="text" class="form-control" value="{{ $puesto->nombre }}" disabled>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="curp">CURP:</label>
					<input type="text" class="form-control" value="{{ $p->curp }}" 
						pattern="[A-Z]+[0-9]+[A-Z]+[0-9]*" minlength="18" maxlength="18" name="curp">
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
                    <div class="col-sm"></div>
                    <div class="col-sm">
                        <button type="submit" class="btn btn-outline-primary"> 
                            Registrar
                        </button>
                    </div>
                    <br>
                    <div class="col-sm">
                        <a href="{{ url('JDepto/personal/1') }}" class="btn btn-outline-danger"> 
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