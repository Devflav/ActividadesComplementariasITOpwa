@extends('layouts.coordComple')
@section('content')

<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
    <div class="row justify-content-center">
        <div class="col-md-9">
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
		<div class="col-md-9">
            <div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
                <div class="card-header text-white" style="background:#1B396A;"> Inscripciones Fuera de Tiempo </div>
				<div class="card-body">
					<form method="POST" action="{{url('CoordAC/inscribir_outime').'/'.$ns}}" class="needs-validation" enctype="multipart/form-data">
						@csrf
						<div class="row">	
							<div class="form-group col">
								<label for="nun_students"> Número de estudiantes:</label>
								<input type="number" class="form-control" 
								placeholder="1234" 
								pattern="[0-9]{4}" 
								name="num_students" id="num_stds">						
								<!-- <select name="estudiantes" id="students" class="form-control">
									@for($j=0; $j<=2000; $j++)
										<option value="{{ $j }}"> {{ $j }} </option>
									@endfor
								</select> -->
							</div>
							<div class="form-group col-8">
								<label for="oficio">* Oficio de solicitud de inscripción</label>
								<div class="custom-file col">
									<input type="file" class="custom-file-input" id="ofi" name="oficio" accept="application/pdf" required>
									<label class="custom-file-label" for="customFile">Selecciona el archivo</label>
								</div>						
							</div>
						</div>

						<div class="form-group">	
							<label for="deptoper"> Departamento:</label>
							<select class="form-control" id="deptos" name="deptoper"> 
								<option value=""> Filtrar las actividades por departamento </option>
								@foreach($dpts as $d)
									<option value="{{ $d->id_depto }}" departamento="{{ $d->id_depto }}"> {{ $d->nombre }} </option>
								@endforeach
							</select>
						</div>

						<div class="form-group">	
							<label for="actividad">* Actividad:</label>
							<select class="form-control" id="groups" name="group" required> 
								<option value=""> Selecciona la actividad </option>
								@foreach($groups as $g)
									<option value="{{ $g->id_grupo }}" require> {{ $g->creditos }}C - {{ $g->clave}} - {{ $g->nombre }} </option>
								@endforeach
							</select>
						</div>

						@php $rows = $ns / 4; $count = 0; $name = "ncontrol"; @endphp
						@for($i = 0; $i < $rows; $i++)
							<div class="row">
								@for($j=0; $j<=3; $j++)
									@if($count < $ns)
										@php $name = "stds".$count; @endphp
										<div class="form-group col">
											<label for="nombreCarr">* Número de control E{{$count+1}}:</label>
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

						<div class="row">
							<label> 
								<strong>
									* Campos Obligatorios
									</strong> 
							</label>
						</div>

						<center> 
							<button type="submit" class="btn btn-outline-primary"> Inscribir </button>
							<a href="{{ URL::previous() }}" class="btn btn-outline-danger"> Cancelar </a> 
						</center>
					
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection