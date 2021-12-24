@extends('layouts.coordComple')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">
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
    <div class="row justify-content-center">
		<div class="col-sm-9">
			<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
				<div class="card-header"> Datos del Estudiante </div>
					<div class="card-body">
						@foreach($estudiante as $e)
						<div class="form-group">
							<div class="col-sm">
								<label for="nControl">Número de Control:</label>
								<input type="text" class="form-control" value="{{ $e->num_control }}" disabled>

							</div>

							<div class="col-sm">
								<label for="nombre">Nombre (s):</label>
								<input type="text" class="form-control" value="{{ $e->nombre }}" disabled>
							
							</div>
						</div>

						<div class="form-group">
							<div class="col-sm">
								<label for="apePat">Apallido Paterno:</label>
								<input type="text" class="form-control" value="{{ $e->apePat }}" disabled>

							</div>

							<div class="col-sm">
								<label for="apeMat">Apellido Materno:</label>
								<input type="text" class="form-control" value="{{ $e->apeMat }}" disabled>

							</div>
						</div>

						<div class="form-group">
							<div class="col-sm">
								<label for="carrera">Carrera:</label>
								<input type="text" class="form-control" value="{{ $e->carrera }}" disabled>

							</div>

							<div class="col-sm">
								<label for="semestre">Semestre:</label>
								<input type="text" class="form-control" value="{{ $e->semestre }}" disabled>
							</div>
                        </div>
					    @endforeach
                </div>
                
				<div class="card-header"> Datos de la Actividad </div>
                    <div class="card-body">
						@foreach($actividad as $a)
						<div class="form-group">
							<div class="col-sm">
								<label for="nControl">Clave Grupo:</label>
								<input type="text" class="form-control" value="{{ $a->grupo }}" disabled>

							</div>

							<div class="col-sm">
								<label for="nombre">Actividad:</label>
								<input type="text" class="form-control" value="{{ $a->actividad }}" disabled>
							
							</div>
						</div>

						<div class="form-group">
							<div class="col-sm">
								<label for="apePat">Departamento:</label>
								<input type="text" class="form-control" value="{{ $a->depto }}" disabled>

							</div>

							<div class="col-sm">
                                <label for="apeMat">Restringida:</label>
                                @if($a->restringida == '1')
								    <input type="text" class="form-control" value="Restringida" disabled>
                                @else
                                    <input type="text" class="form-control" value="NO Restringida" disabled>
                                @endif
							</div>
                        </div>
                        
                        <div class="form-group">
							<div class="col-sm">
                                    <label for="">Horario:</label>
							</div>
                        </div>
                        <div class="form-group">
                            @foreach($horario as $h)
							<div class="col-sm">
                                <center>
                                @if($h->id_dia == '1')    
                                    <label for="apePat">Lunes</label><br>
                                    <label for="apePat">{{$h->hora_inicio}} - {{$h->hora_fin}}</label>
                                @elseif($h->id_dia == '2')    
                                    <label for="apeMat">Martes</label><br>
                                    <label for="apePat">{{$h->hora_inicio}} - {{$h->hora_fin}}</label>
                                @elseif($h->id_dia == '3')    
                                    <label for="apePat">Miercoles</label><br>
                                    <label for="apePat">{{$h->hora_inicio}} - {{$h->hora_fin}}</label>
                                @elseif($h->id_dia == '4')    
                                    <label for="apeMat">Jueves</label><br>
                                    <label for="apePat">{{$h->hora_inicio}} - {{$h->hora_fin}}</label>
                                @elseif($h->id_dia == '5')    
                                    <label for="apeMat">Viernes</label><br>
                                    <label for="apePat">{{$h->hora_inicio}} - {{$h->hora_fin}}</label>
                                @elseif($h->id_dia == '6')    
                                    <label for="apeMat">Sabado</label><br>
                                    <label for="apePat">{{$h->hora_inicio}} - {{$h->hora_fin}}</label>
                                @else
                                    
                                @endif
                                </center>
							</div>
                            @endforeach
                        
                    </div>
                </div>
                @if($a->aprobada == '1')    
                    <div class="container">
                        <div class="form-group">
                            <div class="col-sm"></div>
                            <div class="col-sm">
                                <a href="{{ URL::previous() }}" class="btn btn-outline-primary">
                                    Regresar 
                                </a> 
                            </div>
                            <div class="col-sm">
                                <button class="btn btn-outline-danger" data-toggle="modal" data-target="#baja"> 
                                    Dar de Baja 
                                </button>
                            </div>
                            <div class="col-sm"></div>
                        </div>
                    </div>
                @elseif($a->aprobada == '0')
                    <div class="container">
                        <div class="form-group">
                            <div class="col-sm"></div>
                            <div class="col-sm">
                                <button class="btn btn-outline-primary" data-toggle="modal" data-target="#aprobar"> 
                                    Aprobar
                                </button> 
                            </div>
                            <div class="col-sm">
                                <button class="btn btn-outline-danger" data-toggle="modal" data-target="#noaprobar"> 
                                    No Aprobar 
                                </button> 
                            </div>
                            <div class="col-sm"></div>
                        </div>
                    </div>
				@elseif($a->aprobada == '2' || $a->aprobada == '3')
                    <div class="container">
                        <div class="form-group">
                            <div class="col-sm"></div>
                            <div class="col-sm">
                                <a href="{{ URL::previous() }}" class="btn btn-outline-primary"> 
                                    Regresar 
                                </a> 
                            </div>
                            <div class="col-sm"></div>
                        </div>
                    </div>
                @endif
			@endforeach
			</div>

			<div class="modal fade" id="aprobar" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #1B396A;">
                        <h5 class="modal-title text-white" id="staticBackdropLabel"><strong>APROBAR INSCRIPCIÓN</strong></h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>  </button>
                    </div>
                    <div class="modal-body text-center">
                        <center>APROBARÁS LA INSCRIPCIÓN DE ESTE ESTUDIANTE <br> ¿ESTAS SEGURO DE ESTA ACCIÓN?</center>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cerar</button>
                        <a href="{{ url('/aprobar').'/'.$a->id_inscripcion.'/'.$dpt }}" class="btn btn-outline-danger">Aprobar</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="noaprobar" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #1B396A;">
                        <h5 class="modal-title text-white" id="staticBackdropLabel"><strong>NO APROBAR INSCRIPCIÓN</strong></h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>  </button>
                    </div>
                    <div class="modal-body text-center">
                        <center>NO APROBARÁS LA INSCRIPCIÓN DE ESTE ESTUDIANTE A LA ACTIVIDAD COMPLEMENTARIA <br> ¿ESTAS SEGURO DE ESTA ACCIÓN?</center>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cerar</button>
                        <a href="{{ url('/noaprob').'/'.$a->id_inscripcion.'/'.$dpt }}" class="btn btn-outline-danger">No aprobar</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="baja" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #1B396A;">
                        <h5 class="modal-title text-white" id="staticBackdropLabel"><strong>DAR DE BAJA A ESTUDIANTE</strong></h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>  </button>
                    </div>
                    <div class="modal-body text-center">
                        <center>DARÁS DE BAJA A ESTE ESTUDIANTE DE SU ACTIVIDAD COMPLEMENTARIA <br> ¿ESTAS SEGURO DE ESTA ACCIÓN?</center>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cerar</button>
                        <a  href="{{ url('/bajainscrip').'/'.$a->id_inscripcion.'/'.$dpt }}" class="btn btn-outline-danger">Dar de Baja</a>
                    </div>
                </div>
            </div>
        </div>

		</div>
	</div>
</div>
@endsection