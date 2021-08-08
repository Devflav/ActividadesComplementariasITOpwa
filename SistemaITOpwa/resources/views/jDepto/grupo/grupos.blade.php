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
                        <td>{{$g->nomP}} {{$g->paterno}} {{$g->materno}}</td>
                        <td>{{$g->lugar}}</td>
                        <td>{{$g->cupo}}</td>
                        <td>{{$g->asistencias}}</td>
                        <td>
                        @if($mod)
                            <a href="{{ url('JDepto/editGru').$g->id_grupo }}" class="btn btn-outline-primary btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                            <a href="/eliminarGru/{{ $g->id_grupo }}" class="btn btn-outline-danger btn-sm"><i class="fa fa-fw fa-trash-o"></i></a>
                        @else
                            <a href="{{ url('JDepto/editGru').$g->id_grupo }}" 
                                class="btn btn-outline-primary btn-sm disabled"><i class="fa fa-fw fa-edit"></i></a>
                            <a href="/eliminarGru/{{ $g->id_grupo }}" 
                                class="btn btn-outline-danger btn-sm disabled"><i class="fa fa-fw fa-trash-o"></i></a>
                        @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-end">
                @if($pag == 1)
                    <li class="page-item disabled">
                    <a class="page-link" href="">Página 1</a></li>
                @else
                    @if($vista == 00)
                        <li class="page-item">
                        <a class="page-link" href="{{ url('JDeptogrupos/1') }}">Primera</a></li>
                        
                            @if(($pa-2) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDeptogrupos').'/'.($pa-2) }}">{{$pa-2}}</a></li>
                            @endif
                            @if(($pa-1) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDeptogrupos').'/'.($pa-1) }}">{{$pa-1}}</a></li>
                            @endif
                            <li class="page-item active">
                                <a class="page-link" href="{{ url('JDeptogrupos').'/'.($pa) }}">{{$pa}}</a></li>
                            @if(($pa+1) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDeptogrupos').'/'.($pa+1) }}">{{$pa+1}}</a></li>
                            @endif
                            @if(($pa+2) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDeptogrupos').'/'.($pa+2) }}">{{$pa+2}}</a></li>
                            @endif
                       
                        <li class="page-item">
                        <a class="page-link" href="{{ url('JDeptogrupos').'/'.$pag }}">Última</a></li>
                    @elseif($vista == 01)
                        <li class="page-item">
                        <a class="page-link" href="{{ url('JDeptogrupo').'/'.$bus.'/'.'1' }}">Primera</a></li>

                        @if(($pa-2) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDeptogrupo').'/'.$bus.'/'.($pa-2) }}">{{$pa-2}}</a></li>
                            @endif
                            @if(($pa-1) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDeptogrupo').'/'.$bus.'/'.($pa-1) }}">{{$pa-1}}</a></li>
                            @endif
                            <li class="page-item active">
                                <a class="page-link" href="{{ url('JDeptogrupo').'/'.$bus.'/'.$pa }}">{{$pa}}</a></li>
                            @if(($pa+1) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDeptogrupo').'/'.$bus.'/'.($pa+1) }}">{{$pa+1}}</a></li>
                            @endif
                            @if(($pa+2) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDeptogrupo').'/'.$bus.'/'.($pa+2) }}">{{$pa+2}}</a></li>
                            @endif

                        <li class="page-item">
                        <a class="page-link" href="{{ url('JDeptogrupo').'/'.$bus.'/'.$pag }}">Última</a></li>
                    @endif
                @endif
            </ul>
        </nav>

    </div>
@endsection