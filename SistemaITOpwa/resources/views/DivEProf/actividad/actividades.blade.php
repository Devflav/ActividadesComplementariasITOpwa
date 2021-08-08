@extends('layouts.divEProf')
@section('content')

    <div class="container" style="background-color: transparent; padding-left: 02%; padding-right: 02%; padding-bottom: 05%;">

            <div class="input-group mb-3">
                <label for="" class="form-text">LISTA DE ACTIVIDADES OFERTADAS 
                    @foreach($pnom as $p) 
                        <strong> "{{ $p->nombre }}" </strong>
                    @endforeach </label>
                <label for="" class="col-1"></label>
                <div class="input-group-append">
                @if($mod)
                    <a href="{{ url('DivEProf/nuevaAct') }}" class="btn btn-outline-success btn-sm"><i class="fa fa-fw fa-plus"></i> Agregar </a>
                @else
                    <a href="{{ url('DivEProf/nuevaAct') }}" class="btn btn-outline-success btn-sm disabled"><i class="fa fa-fw fa-plus"></i> Agregar </a>
                @endif
                </div>
            </div>

            <form method="GET" action="{{ url('/DivEProf/searchact') }}">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar actividad por: Clave ó Nombre" name="search" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
                    </div>
                </div>
            </form>

            <div id="divTable">
            
            <table id="carreras" class="table table-hover">
                <thead style="background:#1B396A; width: 100%; height: 50px;">
                    <tr class="text-white">
                                <th width="05%">Clave</th>
                                <th width="20%">Nombre</th>
                                <th width="05%">Créditos</th>
                                <th width="25%">Departamento</th>
                                <th width="10%">Tipo</th>
                                <th width="20%">Descripción</th>
                                <th width="10%">Opciones</th>
                        </tr>
                    </thead>
                <tbody style="width: 100%">
                @foreach($actividades as $a)
                    <tr>
                        <td>{{$a->clave}}</td>
                        <td>{{$a->nombre}}</td>
                        <td><center>{{$a->creditos}}</center></td>
                        <td>{{$a->depto}}</td>
                        <td>{{$a->tipo}}</td>
                        <td>{{$a->descripcion}}</td>
                        <td>
                        @if($mod)
                            <a href="{{ url('DivEProf/editarAct').$a->id_actividad }}" 
                                class="btn btn-outline-primary btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                        @else
                            <a href="{{ url('DivEProf/editarAct').$a->id_actividad }}" 
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
                        <a class="page-link" href="{{ url('DivEProf/actividades').'/'.'1' }}">Primera</a></li>
                        
                            @if(($pa-2) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/actividades').'/'.($pa-2) }}">{{$pa-2}}</a></li>
                            @endif
                            @if(($pa-1) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/actividades').'/'.($pa-1) }}">{{$pa-1}}</a></li>
                            @endif
                            <li class="page-item active">
                                <a class="page-link" href="{{ url('DivEProf/actividades').'/'.($pa) }}">{{$pa}}</a></li>
                            @if(($pa+1) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/actividades').'/'.($pa+1) }}">{{$pa+1}}</a></li>
                            @endif
                            @if(($pa+2) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/actividades').'/'.($pa+2) }}">{{$pa+2}}</a></li>
                            @endif
                       
                        <li class="page-item">
                        <a class="page-link" href="{{ url('DivEProf/actividades').'/'.$pag }}">Última</a></li>
                    @elseif($vista == 01)
                        <li class="page-item">
                        <a class="page-link" href="{{ url('DivEProf/actividad').'/'.$bus.'/'.'1' }}">Primera</a></li>

                        @if(($pa-2) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/actividad').'/'.$bus.'/'.($pa-2) }}">{{$pa-2}}</a></li>
                            @endif
                            @if(($pa-1) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/actividad').'/'.$bus.'/'.($pa-1) }}">{{$pa-1}}</a></li>
                            @endif
                            <li class="page-item active">
                                <a class="page-link" href="{{ url('DivEProf/actividad').'/'.$bus.'/'.$pa }}">{{$pa}}</a></li>
                            @if(($pa+1) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/actividad').'/'.$bus.'/'.($pa+1) }}">{{$pa+1}}</a></li>
                            @endif
                            @if(($pa+2) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/actividad').'/'.$bus.'/'.($pa+2) }}">{{$pa+2}}</a></li>
                            @endif

                        <li class="page-item">
                        <a class="page-link" href="{{ url('DivEProf/actividad').'/'.$bus.'/'.$pag }}">Última</a></li>
                    @elseif($vista == 10)
                        <li class="page-item">
                        @foreach($pnom as $d)<a class="page-link" href="{{ url('DivEProf/actdep').'/'.$d->id_depto/1 }}">Primera</a>@endforeach
                        </li>

                            @if(($pa-2) > 0)
                                <li class="page-item">
                                @foreach($pnom as $d)<a class="page-link" href="{{ url('DivEProf/actdep').'/'.$d->id_depto.'/'.($pa-2) }}">{{$pa-2}}</a>@endforeach</li>
                            @endif
                            @if(($pa-1) > 0)
                                <li class="page-item">
                                @foreach($pnom as $d)<a class="page-link" href="{{ url('DivEProf/actdep').'/'.$d->id_depto.'/'.($pa-1) }}">{{$pa-1}}</a>@endforeach</li>
                            @endif
                            <li class="page-item active">
                                @foreach($pnom as $d)<a class="page-link" href="{{ url('DivEProf/actdep').'/'.$d->id_depto.'/'.$pa }}">{{$pa}}</a>@endforeach</li>
                            @if(($pa+1) <= $pag)
                                <li class="page-item">
                                @foreach($pnom as $d)<a class="page-link" href="{{ url('DivEProf/actdep').'/'.$d->id_depto.'/'.($pa+1) }}">{{$pa+1}}</a>@endforeach</li>
                            @endif
                            @if(($pa+2) <= $pag)
                                <li class="page-item">
                                @foreach($pnom as $d)<a class="page-link" href="{{ url('DivEProf/actdep').'/'.$d->id_depto.'/'.($pa+2) }}">{{$pa+2}}</a>@endforeach</li>
                            @endif

                        <li class="page-item">
                        @foreach($pnom as $d)<a class="page-link" href="{{ url('DivEProf/actdep').'/'.$d->id_depto.'/'.$pag }}">Última</a>@endforeach</li>
                    @elseif($vista == 11)
                        <li class="page-item">
                        @foreach($pnom as $t)<a class="page-link" href="{{ url('DivEProf/actip').'/'.$t->id_tipo/1 }}">Primera</a>@endforeach
                        </li>

                            @if(($pa-2) > 0)
                                <li class="page-item">
                                @foreach($pnom as $t)<a class="page-link" href="{{ url('DivEProf/actip').'/'.$t->id_tipo.'/'.($pa-2) }}">{{$pa-2}}</a>@endforeach</li>
                            @endif
                            @if(($pa-1) > 0)
                                <li class="page-item">
                                @foreach($pnom as $t)<a class="page-link" href="{{ url('DivEProf/actip').'/'.$t->id_tipo.'/'.($pa-1) }}">{{$pa-1}}</a>@endforeach</li>
                            @endif
                            <li class="page-item active">
                                @foreach($pnom as $t)<a class="page-link" href="{{ url('DivEProf/actip').'/'.$t->id_tipo.'/'.$pa }}">{{$pa}}</a>@endforeach</li>
                            @if(($pa+1) <= $pag)
                                <li class="page-item">
                                @foreach($pnom as $t)<a class="page-link" href="{{ url('DivEProf/actip').'/'.$t->id_tipo.'/'.($pa+1) }}">{{$pa+1}}</a>@endforeach</li>
                            @endif
                            @if(($pa+2) <= $pag)
                                <li class="page-item">
                                @foreach($pnom as $t)<a class="page-link" href="{{ url('DivEProf/actip').'/'.$t->id_tipo.'/'.($pa+2) }}">{{$pa+2}}</a>@endforeach</li>
                            @endif

                        <li class="page-item">
                        @foreach($pnom as $t)<a class="page-link" href="{{ url('DivEProf/actip').'/'.$t->id_tipo.'/'.$pag }}">Última</a>@endforeach</li>
                    @endif
                @endif
            </ul>
        </nav>

        <button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#mimodal" id="btn_mimodal">
        </button>
        <div class="modal fade" id="mimodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="restablecerLabel" aria-hidden="true">
        </div>
        
            @if(URL::previous() == 'http://127.0.0.1:8000/DivEProf/actdeptos')
                <center> <a href="{{ URL::previous() }}" class="btn btn-outline-primary"> Regresar </a>  </center>
            @endif
    </div>
@endsection