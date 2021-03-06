@extends('layouts.divEProf')
@section('content')
<div class="container form-content col-sm-9">
@if (session('Catch') != null)
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<h5 class="alert-heading"> 
				<strong> <em> ¡ Error ! </em> </strong>
				<i class="bi bi-x-octagon-fill close float-right" type="button" data-dismiss="alert"></i>
			</h5>
			<hr>
			<p class="text-justify"> {{ session('Catch') }} </p>
		</div>
    @endif
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header">
				<div class="col-sm">
					<label for=""> Registrar Nuevo Personal </label>
				</div> 
			</div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('DivEProf/regEmp') }}" class="needs-validation">
            @csrf
            <div class="form-group">
                <div class="col-sm">
					<label for="nControl">* Grado:</label>
					<select class="form-control" id="grado" name="id_grado" required> 
						<option value=""> Selecciona una Grado </option>
						@foreach($grados as $g)
							<option value="{{$g->id_grado}}" require> {{ $g->nombre }} </option>
						@endforeach
					</select>
					<div class="valid-feedback"></div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="nombre">* Nombre (s):</label>
					<input type="text" class="form-control text-uppercase" id="nombre" 
						placeholder="Nombre(s)" pattern="{[A-Z][a-z]+}+ *" 
						minlength="4" name="nombre" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>	
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="apePat">* Apallido Paterno:</label>
					<input type="text" class="form-control text-uppercase" id="apePat" 
						placeholder="Apellido Paterno" pattern="{[A-Z][a-z]+}+ *" 
						minlength="4" name="apePat" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="apeMat">* Apellido Materno:</label>
					<input type="text" class="form-control text-uppercase" id="apeMat" 
						placeholder="Apellido Materno" pattern="{[A-Z][a-z]+}+ *" 
						minlength="4" name="apeMat" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="carrera">* Departamento:</label>
					<select class="form-control" id="depto" name="id_depto" required> 
						<option value=""> Selecciona un Departamento </option>
						@foreach($departamentos as $d)
							<option value="{{ $d->id_depto }}" require> {{ $d->nombre }} </option>
						@endforeach
					</select>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
			<div class="form-group">
                <div class="col-sm">
					<label for="curp">* CURP:</label>
					<input type="text" class="form-control text-uppercase" id="curp" 
						placeholder="Escribe la Curp" pattern="[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9]{2}|[a-z]{4}[0-9]{6}[a-z]{6}[0-9]{2}" 
						minlength="18" maxlength="18" name="curp" required>
                </div>
                <div class="col-sm">
					<label for="semestre">* Puesto:</label>
					<select class="form-control" id="puesto" name="id_puesto" required> 
						<option value=""> Selecciona el puesto </option>
						@foreach($puestos as $p)
								<option value="{{ $p->id_puesto }}" require> {{ $p->nombre }} </option>
						@endforeach
					</select>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
			<div class="form-group">
                <div class="col-sm">
					<label > 
						<strong>
							* Campos Obligatorios
						</strong> 
					</label>
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
                        <a href="{{ url('/DivEProf/personal/1') }}" class="btn btn-outline-danger"> 
                            Cancelar 
                        </a> 
                    </div>
                    <div class="col-sm"></div>
                </div>
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
        </form>
    </div>
</div>
@endsection