@extends('layouts.jDeptos')
@section('content')
    <div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

            <div class="input-group mb-3">
                <label for="" class="form-text">LISTA DE LOS GRUPOS OFERTADOS 
                <strong>"{{ $pnom->nombre }}"</strong></label>
                <label for="" class="col-1"></label>
                <div class="input-group-append">
                @if($mod)
                    <a href="{{ url('JDepto/nuevGru') }}" class="btn btn-outline-success btn-sm"> + Agregar </a>
                @else
                    <a href="{{ url('JDepto/nuevGru') }}" class="btn btn-outline-success btn-sm disabled"> + Agregar </a>
                @endif
                </div>
            </div>

            <form method="GET" action="{{url('JDepto/searchGrupo')}}">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar grupo por: Clave o Actividad" name="search">
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit"> Buscar </button>
                    </div>
                </div>
            </form>
            <div id="divTable">
             
             <table class="table table-hover">
                <thead style=""> 
                <tr class="text-white" style="background:#1B396A;">
                            <th width="06%">Clave</th>
                            <th width="23%">Actividad</th>
                            <th width="27%">Responsable</th>
                            <th width="22%">Lugar</th>
                            <th width="05%">Cupo</th>
                            <th width="8%">Asistencias</th>
                            <th width="09%">Opciones</th>
                    </tr>
                </thead>
                <tbody style="">
                @foreach($grupos as $g)
                    <tr>
                        <td>{{$g->clave}}</td>
                        <td>{{$g->actividad}}</td>
                        <td>{{$g->responsable}}</td>
                        <td>{{$g->lugar}}</td>
                        <td>{{$g->cupo}}</td>
                        <td>{{$g->asistencias}}</td>
                        <td>
                        @if($mod)
                            <a href="{{ url('JDepto/editGru').$g->id_grupo }}" class="btn btn-outline-primary btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                        @else
                            <a href="{{ url('JDepto/editGru').$g->id_grupo }}" 
                                class="btn btn-outline-primary btn-sm disabled"><i class="fa fa-fw fa-edit"></i></a>
                        @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <nav class="navbar navbar-light justify-content-end">
        <ul class="pagination justify-content-end">
            @if($grupos->previousPageUrl() != null)
                <li class="page-item">
                    <a href="{{ $grupos->previousPageUrl() }}" class="page-link m-1">
                        Anterior
                    </a>
                </li>
            @else
                <li class="page-item disabled"> 
                    <a href="" class="page-link m-1"> Anterior </a>
                </li>
            @endif
            @if($grupos->nextPageUrl() != null)
                <li class="page-item">
                    <a href="{{ $grupos->nextPageUrl() }}" class="page-link m-1">
                        Siguiente
                    </a>
                </li>
            @else
            <li class="page-item disabled"> 
                    <a href="" class="page-link m-1"> Siguiente </a>
                </li>
            @endif
        </ul>
    </nav>

    </div>
@endsection