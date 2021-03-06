@extends('layouts.coordComple')
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
			<div class="card-header"> Registrar Nuevo Estudiante </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{url('/regEst')}}" class="needs-validation">
            @csrf
            <div class="form-group">
                <div class="col-sm">
					<label for="nControl">* Número de Control:</label>
					<input type="text" class="form-control" id="nControl" placeholder="Escribe el número de control" 
					pattern="[0-9]{8}|[C|B]{1}[0-9]{8}" name="num_control" required>
					<div class="valid-feedback"></div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="nombre">* Nombre (s):</label>
					<input type="text" class="form-control text-uppercase" id="nombre" 
					placeholder="Escribe tu(s) Nombre(s)" pattern="{[A-Z][a-z]+}+ *" 
					minlength="4" name="nombre" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="apePat">* Apallido Paterno:</label>
					<input type="text" class="form-control text-uppercase" id="apePat" 
					placeholder="Escribe tu Apellido Paterno" pattern="{[A-Z][a-z]+}+ *" 
					minlength="4" name="apePat" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="apeMat">* Apellido Materno:</label>
					<input type="text" class="form-control text-uppercase" id="apeMat" 
					placeholder="Escribe tu Apellido Materno" pattern="{[A-Z][a-z]+}+ *" 
					minlength="4" name="apeMat" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="carrera">* Carrera:</label>
					<select class="form-control" id="carrera" placeholder="Selecciona tu Carrera" 
						name="id_carrera" required> 
					<option value=""> Selecciona una Carrera </option>
						@foreach($carreras as $c)
							<option value="{{$c->id_carrera}}" require> {{ $c->nombre }} </option>
						@endforeach
					</select>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="semestre">* Semestre:</label>
					<select class="form-control" id="semestre" placeholder="Selecciona tu Semestre" 
						name="semestre" required> 
					<option value=""> Selecciona un Semestre </option>
						@foreach($semestres as $s)
							<option value="{{ $s }}" require> {{ $s }}° Semestre </option>
						@endforeach
					</select>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
			<div class="form-group">
                <div class="col-sm">
					<label for="email">* Correo Institucional:</label>
					<input type="email" class="form-control text-lowercase" id="email" 
						placeholder="Escribe tu correo institucional" 
						pattern="[0-9]{8}@itoaxaca.edu.mx{1}|[0-9]{9}@itoaxaca.edu.mx{1}|[C|B]{1}[0-9]{8}@itoaxaca.edu.mx{1}" 
						minlength="24" maxlength="26" name="email" required>
					<div class="valid-feedback">Valid.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
                <div class="col-sm">
					<label for="curp">CURP:</label>
					<input type="text" class="form-control text-uppercase" id="curp" 
						placeholder="Puedes registrarla en otro momento" 
						pattern="[A-Z]{4}[0-9]{6}[A-Z]{6}[0-9]{2}" minlength="18" maxlength="18" name="curp">
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
                        <a href="{{ url('/CoordAC/estudiantes/1') }}" class="btn btn-outline-danger"> 
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