@extends('layouts.estudiante')
@section('content')

<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header">
			Actualizar mi Contraseña
            </div>
        </div>
    </div>
	<div class="card-body">
		<form method="POST" action="{{ url('/editpsswd') }}" class="needs-validation">
			@csrf
			<div class="form-group">
				<div class="col-sm">
					<label for="nControl">Contraseña actual:</label>
					<input type="text" class="form-control" minlength="8" name="pswdactual" placeholder="Escribe tu contraseña actual" required>
					<div class="valid-feedback">Valido</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm">
					<label for="apePat">Nueva contraseña:</label>
					<input type="text" class="form-control" minlength="8" name="pswdnueva" placeholder="Escribe tu nueva contraseña" required>
					<div class="valid-feedback">Valido</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm">
					<label for="carrera">Confirmar contraseña:</label>
					<input type="password" class="form-control" name="pswdconfirm" placeholder="Confirma tu nueva contraseña" required>
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
                        <a href="{{ url('Est/perfil') }}" class="btn btn-outline-danger"> 
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