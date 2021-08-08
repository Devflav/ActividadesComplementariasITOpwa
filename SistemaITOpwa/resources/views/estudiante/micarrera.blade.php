@extends('layouts.estudiante')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

    <label> LISTA DE ACTIVIDADES OFERTADAS POR 
        <strong>"@foreach($car as $c) 
                    {{ $c->nombre }} 
                @endforeach"
        </strong>
    </label>

        <div id="divTable">
            <table class="table table-hover">
                    <tr class="text-white" style="background:#1B396A;">
                        <th width="08%">Clave</th>
                        <th width="28%">Nombre</th>
                        <th width="08%">Créditos</th>
                        <th width="25%">Lugar</th>
                        <th width="15%">Horario</th>
                        <th width="14%">Opciones</th>
                    </tr>
                
                @foreach($actividades as $a)
                    <tr>
                        <td>{{$a->clave}}</td>
                        <td>{{$a->nombre}}</td>
                        <td>{{$a->creditos}}</td>
                        <td>{{$a->lugar}}</td>
                        <td>
                        <a class="btn btn-outline-primary btn-sm text-primary" onclick="horario('{{ url('Est/verh').$a->id_grupo }}')">
                           Horario 
                        </a>
                        </td>
                        <td>
                        <center>
                            @if($a->cupo_libre == 0)
                                <button class="btn btn-outline-primary btn-sm" disabled> Lleno </button>
                            @else
                                <a href="{{ url('Est/inscribir').$a->id_grupo }}" class="btn btn-outline-primary btn-sm">Inscribirme</a>
                            @endif
                        </center>
                        </td>
                    </tr>
                @endforeach
                
            </table>
        </div>

        <button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#alerta" id="btn_dw">
        </button>
        <div class="modal fade" id="alerta"  aria-labelledby="alertaLabel" aria-hidden="true">
        </div>
    </div>

@endsection
