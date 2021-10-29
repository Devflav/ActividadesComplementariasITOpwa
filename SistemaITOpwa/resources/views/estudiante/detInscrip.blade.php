@extends('layouts.estudiante')
@section('content')
<div class="container form-content col-sm-9">
    <div class="form-group">
		<div class="col-sm">
			<div class="card-header">
                @if($v == 00)
                    Datos de la Actividad
                @else
                    Actividad en Curso
                @endif
            </div>
        </div>
    </div>

    <div class="card-body">
        @foreach($actividad as $a)
        <div class="form-group">
            <div class="col-sm">
                <label for="apePat">Responsable:</label>
                <input type="text" class="form-control" 
                    value="{{ $a->grado }} {{ $a->nombre }} {{ $a->apePat }} {{ $a->apeMat }}" 
                    disabled>
            </div>
        </div>
        
        <div class="form-group">
            <div class="col-sm">
                <label for="nControl">Clave Grupo:</label>
                <input type="text" class="form-control" value="{{ $a->clave }}" disabled>
            </div>

            <div class="col-sm">
                <label for="nombre">Actividad:</label>
                <input type="text" class="form-control" value="{{ $a->actividad }}" disabled>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm">
                <label for="apePat">Departamento:</label>
                <input type="text" class="form-control" value="{{ $a->depto }} " disabled>
            </div>

            <div class="col-sm">
                <label for="apeMat">Lugar:</label>
                <input type="text" class="form-control" value="{{ $a->lugar }}" disabled>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm">
                <label for="apePat">Créditos:</label>
                <input type="text" class="form-control" value="{{ $a->creditos }} " disabled>
            </div>

            <div class="col-sm">
                <label for="apeMat">Tipo:</label>
                <input type="text" class="form-control" value="{{ $a->tipo }}" disabled>
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
                    <label for="lunes">Lunes</label><br>
                    <label for="lunes">{{$h->hora_inicio}} - {{$h->hora_fin}}</label>
                @elseif($h->id_dia == '2')    
                    <label for="martes">Martes</label><br>
                    <label for="martes">{{$h->hora_inicio}} - {{$h->hora_fin}}</label>
                @elseif($h->id_dia == '3')    
                    <label for="miercoles">Miercoles</label><br>
                    <label for="miercoles">{{$h->hora_inicio}} - {{$h->hora_fin}}</label>
                @elseif($h->id_dia == '4')    
                    <label for="jueves">Jueves</label><br>
                    <label for="jueves">{{$h->hora_inicio}} - {{$h->hora_fin}}</label>
                @elseif($h->id_dia == '5')    
                    <label for="viernes">Viernes</label><br>
                    <label for="viernes">{{$h->hora_inicio}} - {{$h->hora_fin}}</label>
                @elseif($h->id_dia == '6')    
                    <label for="sabado">Sabado</label><br>
                    <label for="sabado">{{$h->hora_inicio}} - {{$h->hora_fin}}</label>
                @else
                    
                @endif
                </center>
            </div>
            @endforeach
        </div>
    </div>   

            @if($v == 00)
                <div class="form-group">
                    <div class="col-sm"></div>
                    <div class="col-sm">
                        <button type="button" class="btn btn-outline-primary" data-toggle="modal" 
                            data-target="#staticBackdrop"> 
                            Confirmar Inscripción 
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
            @endif
                <div class="invisible">
                    <input type="text" value="{{ $a->id_grupo}}" name="grupo">
                </div>
                
        @endforeach

        <div class="form-group">
            <div class="col-sm"></div>
            <div class="col-sm"></div>
            <div class="col-sm">
                <a href="{{ url('Est/imprimir/horario').'/'.$actividad[0]->id_inscripcion }}" 
                    class="btn btn-outline-primary" target="_blank">
                    <i class="fa fa-lg fa-file-pdf-o"></i> Imprimir horario
                </a>
            </div>
        </div>

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
                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal">
                        Cancelar
                    </button>
                    <a href="{{ url('/solicitudins').$a->id_grupo }}" class="btn btn-outline-danger">
                        Confirmar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection