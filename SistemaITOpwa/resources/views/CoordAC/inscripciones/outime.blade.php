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
			Para realizar el proceso de inscripción adecuadamente, favor de seguir los siguientes pasos:
			<ol>
				<li>Escribir el número de estudiantes (Enter).</li>
				<li>Seleccionar el oficio de solicitud de inscripción.</li>
				<li>Seleccionar el grupo al que se van a inscribir los estudiantes 
					(Haciendo uso del filrador de grupos por departamento).</li>
				<li>Escribir los números de control de los estudiantes a inscribir.</li>
				<li>Click en el botón Inscribir.</li>
			</ol>
        </div>
    </div>	
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Inscripciones Fuera de Tiempo </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('CoordAC/inscribir_outime').'/'.$ns }}" 
			class="needs-validation" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <div class="col-sm">
					<label for="nun_students"> Número de estudiantes:</label>
					<input type="number" class="form-control" 
					placeholder="1234" 
					pattern="[0-9]{4}" 
					name="num_students" id="num_stds">						
					<!-- <select name="estudiantes" id="nStudents" class="form-control">
						@for($j=0; $j<=2000; $j++)
							<option value="{{ $j }}" numStudents="{{ $j }}"> {{ $j }} </option>
						@endfor
					</select> -->
                </div>
                <div class="col-sm">
					<label for="oficio">* Oficio de solicitud de inscripción</label>
					<div class="custom-file col">
						<input type="file" class="custom-file-input" id="ofi" name="oficio" 
							accept="application/pdf" required>
						<label class="custom-file-label" for="customFile">Selecciona el archivo</label>
					</div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="deptoper"> Departamento:</label>
					<select class="form-control" id="deptos" name="deptoper"> 
						<option value=""> Filtrar las actividades por departamento </option>
						@foreach($dpts as $d)
							<option value="{{ $d->id_depto }}" departamento="{{ $d->id_depto }}"> {{ $d->nombre }} </option>
						@endforeach
					</select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm">
					<label for="actividad">* Actividad:</label>
					<select class="form-control" id="groups" name="group" required> 
						<option value=""> Selecciona la actividad </option>
						@foreach($groups as $g)
							<option value="{{ $g->id_grupo }}" require> {{ $g->creditos }}C - {{ $g->clave}} - {{ $g->nombre }} </option>
						@endforeach
					</select>
                </div>
            </div>
			@php $rows = $ns / 4; $count = 0; $name = "ncontrol"; @endphp
			@for($i = 0; $i < $rows; $i++)
				<div class="form-group">
					@for($j=0; $j<=3; $j++)
						@if($count < $ns)
							@php $name = "stds".$count; @endphp
								<div class="col-sm">
									<label for="nombreCarr">* Número de ctrl. E{{$count+1}}:</label>
									<input type="text" class="form-control" placeholder="20161234" 
									pattern="[0-9]{8}|[C|B]{1}[0-9]{8}|[0-9]{9}" 
									name="{{ $name }}" id="ctrl"
									required>	
								</div>
							@php $count++; @endphp
						@endif
					@endfor
				</div>
			@endfor
			<div class="form-group">
                <div class="col-sm">
					<label> 
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
                            Inscribir
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
</div>
@endsection