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
			<div class="card-header"> Nuevo Grado </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{url('DivEProf/regGrado')}}" class="needs-validation">
            @csrf
            <div class="form-group">
                <div class="col-sm-3">
					<label for="nombre">* Grado:</label>
					<input type="text" class="form-control text-uppercase" 
					placeholder="Escribe el grado" pattern="{[A-Z][a-z]+}+ *" 
					title="ING"
					name="nombre" required>
					<div class="valid-feedback">Valido.</div>
					<div class="invalid-feedback">Por favor rellena el campo.</div>	
                </div>
                <div class="col-sm">
					<label for="significado">* Descripción:</label>
					<input type="text" class="form-control text-uppercase" 
					placeholder="Escribe la descripción" pattern="{[A-Z][a-z]+}+ *" 
					title="Ingeniero"
					name="significado" required>
					<div class="valid-feedback">Valido.</div>
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
                        <a href="{{ URL::previous() }}" class="btn btn-outline-danger"> 
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