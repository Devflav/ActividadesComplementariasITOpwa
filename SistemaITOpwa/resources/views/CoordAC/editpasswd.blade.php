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
			<div class="card-header"> Actualizar mi Contraseña </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('/cac/editpasswd') }}" class="needs-validation">
            @csrf
            <div class="form-group">
                <div class="col-sm">
					<label for="nControl">Contraseña actual:</label>
					<input type="text" class="form-control" minlength="8" name="passactual" placeholder="Escribe tu contraseña actual" required>
					<div class="valid-feedback">Valido</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="apePat">Nueva contraseña:</label>
					<input type="text" class="form-control" minlength="8" name="passnueva" placeholder="Escribe tu nueva contraseña" required>
					<div class="valid-feedback">Valido</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="carrera">Confirmar contraseña:</label>
					<input type="password" class="form-control" name="passconfir" placeholder="Confirma tu nueva contraseña" required>
					<div class="valid-feedback">Valido</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
                </div>
            </div>
            <div class="container">
                <div class="form-group">
                    <div class="col-sm"></div>
                    <div class="col-sm">
                        <button type="submit" class="btn btn-outline-primary"> 
							Actualizar
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