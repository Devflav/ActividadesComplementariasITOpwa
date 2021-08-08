@extends('layouts.profesores')
@section('content')

	<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
		<div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Mis Datos Generales </div>
					<div class="card-body">
							@csrf
                            @foreach($persona as $p)
							<div class="row">
								<div class="form-group col">
									<label for="nControl"> Grado:</label>
									<input type="text" class="form-control" value="{{ $p->grado }}" name="grado" disabled>
								</div>

								<div class="form-group col">
									<label for="nombre"> Nombre (s):</label>
									<input type="text" class="form-control" value="{{ $p->nombre }}" name="nombre" disabled>						
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="apePat"> Apallido Paterno:</label>
									<input type="text" class="form-control" value="{{ $p->paterno }}" name="apePat" disabled>
								</div>

								<div class="form-group col">
									<label for="apeMat"> Apellido Materno:</label>
									<input type="text" class="form-control" value="{{ $p->materno }}" name="apeMat" disabled>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="carrera"> Departamento:</label>
									<input type="text" class="form-control" value="{{ $p->depto }}" name="depto" disabled>
								</div>

								<div class="form-group col">
									<label for="semestre"> Puesto:</label>
									<input type="text" class="form-control" value="{{ $p->puesto }}" name="puesto" disabled>
								</div>
							</div>

							<div class="row">
								<div class="form-group col">
									<label for="curp"> CURP:</label>
									<input type="text" class="form-control" value="{{ $p->curp }}" name="curp" disabled>
								</div>
							</div>
						@endforeach

							<center> 
							<a href="{{ url('ProfR/editpasswd') }}" class="btn btn-outline-primary"> Cambiar contraseña </a>
							<a href="{{ url('ProfR/editper') }}" class="btn btn-outline-primary"> Editar </a>
                            <a href="{{ url('ProfR') }}" class="btn btn-outline-danger"> Regresar </a> </center>
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection