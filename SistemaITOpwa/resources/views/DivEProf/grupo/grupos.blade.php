@extends('layouts.divEProf')
@section('content')
    <div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

            <div class="input-group mb-3">
            <label for="" class="form-text">LISTA DE GRUPOS OFERTADOS 
                    @foreach($pnom as $p) 
                        <strong> "{{ $p->nombre }}" </strong>
                    @endforeach </label>
                <label for="" class="col-1"></label>
                <div class="input-group-append">
                @php
				    $dpt = $dept;
			    @endphp
                @if($mod)    
                    <a href="{{ url('DivEProf/nuevoGrupo').'/'.$dpt }}" class="btn btn-outline-success btn-sm"><i class="fa fa-fw fa-plus"></i> Agregar </a>
                @else
                    <a href="{{ url('DivEProf/nuevoGrupo').'/'.$dpt }}" class="btn btn-outline-success btn-sm disabled"><i class="fa fa-fw fa-plus"></i> Agregar </a>
                @endif
                </div>
            </div>

            <form method="GET" action="{{ url('/DivEProf/searchgru') }}">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar grupo por: Clave, Actividad o Responsable" name="search">
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
                    </div>
                </div>
            </form>
            <div id="divTable">
             
            <table class="table table-hover table-responsive">
                <thead style="background:#1B396A;">
                        <tr class="text-white">
                            <th width="08%">Clave</th>
                            <!--<th width="11%">Periodo</th>-->
                            <th width="30%">Actividad</th>
                            <th width="24%">Responsable</th>
                            <th width="16%">Lugar</th>
                            <th width="05%">Cupo</th>
                            <th width="8%">Total Asistencias</th>
                            <th width="09%">Opciones</th>
                    </tr>
                    </thead>

                    <tbody style="width: 100%">
                @foreach($grupos as $g)
                    <tr>
                        <td>{{$g->clave}}</td>
                        <!--<td>{{$g->periodo}}</td>-->
                        <td>{{$g->actividad}}</td>
                        <td>{{$g->nomP}} {{$g->paterno}} {{$g->materno}}</td>
                        <td>{{$g->lugar}}</td>
                        <td>{{$g->cupo}}</td>
                        <td>{{$g->asistencias}}</td>
                        <td>
                            @if($mod)
                            <a href="{{ url('DivEProf/editarGru').'/'.$g->id_grupo.'/'.$g->id_depto }}" 
                                class="btn btn-outline-primary btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                            @else
                            <a href="{{ url('DivEProf/editarGru').'/'.$g->id_grupo.'/'.$g->id_depto }}" 
                                class="btn btn-outline-primary btn-sm disabled"><i class="fa fa-fw fa-edit"></i></a>
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
                        <a class="page-link" href="{{ url('DivEProf/grupos').'/'.'1' }}">Primera</a></li>
                        
                            @if(($pa-2) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/grupos').'/'.($pa-2) }}">{{$pa-2}}</a></li>
                            @endif
                            @if(($pa-1) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/grupos').'/'.($pa-1) }}">{{$pa-1}}</a></li>
                            @endif
                            <li class="page-item active">
                                <a class="page-link" href="{{ url('DivEProf/grupos').'/'.($pa) }}">{{$pa}}</a></li>
                            @if(($pa+1) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/grupos').'/'.($pa+1) }}">{{$pa+1}}</a></li>
                            @endif
                            @if(($pa+2) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/grupos').'/'.($pa+2) }}">{{$pa+2}}</a></li>
                            @endif
                       
                        <li class="page-item">
                        <a class="page-link" href="{{ url('DivEProf/grupos').'/'.$pag }}">Última</a></li>
                    @elseif($vista == 01)
                        <li class="page-item">
                        <a class="page-link" href="{{ url('DivEProf/grupos').'/'.$bus.'/'.'1' }}">Primera</a></li>

                        @if(($pa-2) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/grupos').'/'.$bus.'/'.($pa-2) }}">{{$pa-2}}</a></li>
                            @endif
                            @if(($pa-1) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/grupos').'/'.$bus.'/'.($pa-1) }}">{{$pa-1}}</a></li>
                            @endif
                            <li class="page-item active">
                                <a class="page-link" href="{{ url('DivEProf/grupos').'/'.$bus.'/'.$pa }}">{{$pa}}</a></li>
                            @if(($pa+1) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/grupos').'/'.$bus.'/'.($pa+1) }}">{{$pa+1}}</a></li>
                            @endif
                            @if(($pa+2) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/grupos').'/'.$bus.'/'.($pa+2) }}">{{$pa+2}}</a></li>
                            @endif

                        <li class="page-item">
                        <a class="page-link" href="{{ url('DivEProf/grupos').'/'.$bus.'/'.$pag }}">Última</a></li>
                    @endif
                @endif
            </ul>
        </nav>

        <button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#mimodal" id="btn_mimodal">
        </button>
        <div class="modal fade" id="mimodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="restablecerLabel" aria-hidden="true">
        </div>

    </div>
@endsection