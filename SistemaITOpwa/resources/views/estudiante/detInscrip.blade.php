@extends('layouts.estudiante')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">
    <div class="row justify-content-center">
		<div class="col-sm-9">
			<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
               
				<div class="card-header">
                    @if($v == 00)
                        Datos de la Actividad
                    @else
                        Actividad en Curso
                    @endif
                </div>

                    <div class="card-body">
                        @foreach($actividad as $a)
						<div class="row">
							<div class="form-group col">
								<label for="apePat">Responsable:</label>
								<input type="text" class="form-control" value="{{ $a->grado }} {{ $a->nombre }} {{ $a->apePat }} {{ $a->apeMat }}" disabled>

							</div>
                        </div>
                        
                        <div class="row">
							<div class="form-group col">
								<label for="nControl">Clave Grupo:</label>
								<input type="text" class="form-control" value="{{ $a->clave }}" disabled>

							</div>

							<div class="form-group col">
								<label for="nombre">Actividad:</label>
								<input type="text" class="form-control" value="{{ $a->actividad }}" disabled>
							
							</div>
						</div>

						<div class="row">
							<div class="form-group col">
								<label for="apePat">Departamento:</label>
								<input type="text" class="form-control" value="{{ $a->depto }} " disabled>

							</div>

							<div class="form-group col">
                                <label for="apeMat">Lugar:</label>
								    <input type="text" class="form-control" value="{{ $a->lugar }}" disabled>
							</div>
                        </div>

                        <div class="row">
							<div class="form-group col">
								<label for="apePat">Créditos:</label>
								<input type="text" class="form-control" value="{{ $a->creditos }} " disabled>

							</div>

							<div class="form-group col">
                                <label for="apeMat">Tipo:</label>
								    <input type="text" class="form-control" value="{{ $a->tipo }}" disabled>
							</div>
                        </div>
                        
                        <div class="row">
							<div class="form-group col">
                                    <label for="">Horario:</label>
							</div>
                        </div>
                        <div class="row">
                            @foreach($horario as $h)
							<div class="form-group col">
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

                @if($v == 00)
                    <center> 
                        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#staticBackdrop"> Confirmar Inscripción </button> 
                        <a href="{{ URL::previous() }}" class="btn btn-outline-danger"> Cancelar </a> 
                    </center>
                @endif
                    <div class="invisible">
                        <input type="text" value="{{ $a->id_grupo}}" name="grupo">
                    </div>
			@endforeach

            <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header" style="background-color: #1B396A;">
                <h5 class="modal-title text-white" id="staticBackdropLabel"><strong>SOLICITUD DE INSCRIPCIÓN</strong></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body text-center">
                Se registrará tú solicitud de inscripción, por favor espera a que sea confirmada
                por la Coordinación de Actividades Complementarias. <br>
                Cuando sea aprobada recibirás un correo electrónico de aprobación, este correo
                electrónico será enviado a tu cuenta de correo instutucional.
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cancelar</button>
                <a href="{{ url('/solicitudins').$a->id_grupo }}" class="btn btn-outline-danger">Confirmar</a>
            </div>
        </div>
        </div>
        </div>
			</div>
		</div>
	</div>
</div>
@endsection