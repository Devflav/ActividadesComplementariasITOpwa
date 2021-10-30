@extends('layouts.divEProf')
@section('content')
<div class="container form-content col-sm-9">
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Mis Datos Generales </div>
        </div>
    </div>
    <div class="card-body">
		@foreach($persona as $p)
            <div class="form-group">
                <div class="col-sm">
					<label for="nControl"> Grado:</label>
					<input type="text" class="form-control" value="{{ $p->grado }}" 
                        name="id_grado" disabled>
                </div>
                <div class="col-sm">
					<label for="nombre"> Nombre (s):</label>
					<input type="text" class="form-control" value="{{ $p->nombre }}" 
                        name="nombre" disabled>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="apePat"> Apallido Paterno:</label>
					<input type="text" class="form-control" value="{{ $p->paterno }}" 
                        name="apePat" disabled>
                </div>
                <div class="col-sm">
					<label for="apeMat"> Apellido Materno:</label>
					<input type="text" class="form-control" value="{{ $p->materno }}" 
                        name="apeMat" disabled>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="carrera"> Departamento:</label>
					<input type="text" class="form-control" value="{{ $p->depto }}" 
                        name="id_depto" disabled>
                </div>
                <div class="col-sm">
					<label for="semestre"> Puesto:</label>
					<input type="text" class="form-control" value="{{ $p->puesto }}" 
                        name="id_puesto" disabled>
                </div>
            </div>
			<div class="form-group">
                <div class="col-sm">
					<label for="curp"> CURP:</label>
					<input type="text" class="form-control" value="{{ $p->curp }}" 
                        name="curp" disabled>
                </div>
                <div class="col-sm">
                </div>
            </div>
		@endforeach
            <div class="container">
                <div class="form-group">
                    <div class="col-sm">
						<a href="{{ url('DivEProf/editpasswd') }}" class="btn btn-outline-primary"> 
							Cambiar contraseña 
						</a>
					</div>
					<br>
                    <div class="col-sm">
						<a href="{{ url('DivEProf/editperfil') }}" class="btn btn-outline-primary"> 
							Editar 
						</a>
                    </div>
                    <br>
                    <div class="col-sm">
                        <a href="{{ url('DivEProf') }}" class="btn btn-outline-danger"> 
                            Regresar 
                        </a> 
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection