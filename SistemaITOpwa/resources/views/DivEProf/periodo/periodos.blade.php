@extends('layouts.divEProf')
@section('content')
    <div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

            <div class="input-group mb-3">
                <label for="" class="form-text">HISTORIAL DE PERIODOS ESCOLARES REGISTRADOS</label>   
                <label for="" class="col-1"></label>
                <div class="input-group-append">
                    <a href="{{ url('DivEProf/nuevoPeri') }}" class="btn btn-outline-success btn-sm"><i class="fa fa-fw fa-plus"></i> Agregar </a>
                </div>
            </div>

            <form method="GET" action="{{url('/DivEProf/searchperi')}}">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar periodo por Año" name="search">
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
                    </div>
                </div>
            </form>
            
            <div id="divTable" class="input-group">

                <table id="carreras" class="table table-hover">
                <thead style="background:#1B396A; width: 100%; height: 50px;">
                    <tr class="text-white">
                            <th width="28%">Nombre</th>
                            <th width="19%">Inicio Periodo</th>
                            <th width="19%">Fin Periodo</th>
                            <th width="19%">Estado</th>
                            <th width="15%">Opciones</th>
                    </tr>
                </thead>
                <tbody style="">
                @foreach($periodos as $p)
                    @if($p->estado == 'Actual')
                        <tr>
                            <td><strong> {{ $p->nombre }} </strong></td>
                            <td><strong> {{ $p->inicio }} </strong></td>
                            <td><strong> {{ $p->fin }} </strong></td>
                            <td><strong> {{ $p->estado }} </strong></td>
                            <td>
                                <a href="{{ url('DivEProf/detallePeri').$p->id_periodo }}" class="btn btn-outline-primary btn-sm">Detalle</a>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td>{{ $p->nombre }}</td>
                            <td>{{ $p->inicio }}</td>
                            <td>{{ $p->fin }}</td>
                            <td>{{ $p->estado }}</td>
                            <td>
                                <a href="{{ url('DivEProf/detallePeri').$p->id_periodo }}" class="btn btn-outline-primary btn-sm">Detalle</a>
                            </td>
                        </tr>
                    @endif
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
                        <a class="page-link" href="{{ url('DivEProf/periodos').'/'.'1' }}">Primera</a></li>
                        
                            @if(($pa-2) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/periodos').'/'.($pa-2) }}">{{$pa-2}}</a></li>
                            @endif
                            @if(($pa-1) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/periodos').'/'.($pa-1) }}">{{$pa-1}}</a></li>
                            @endif
                            <li class="page-item active">
                                <a class="page-link" href="{{ url('DivEProf/periodos').'/'.($pa) }}">{{$pa}}</a></li>
                            @if(($pa+1) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/periodos').'/'.($pa+1) }}">{{$pa+1}}</a></li>
                            @endif
                            @if(($pa+2) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/periodos').'/'.($pa+2) }}">{{$pa+2}}</a></li>
                            @endif
                       
                        <li class="page-item">
                        <a class="page-link" href="{{ url('DivEProf/periodos').'/'.$pag }}">Última</a></li>
                    @elseif($vista == 01)
                        <li class="page-item">
                        <a class="page-link" href="{{ url('DivEProf/periodos').'/'.$bus.'/'.'1' }}">Primera</a></li>

                        @if(($pa-2) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/periodos').'/'.$bus.'/'.($pa-2) }}">{{$pa-2}}</a></li>
                            @endif
                            @if(($pa-1) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/periodos').'/'.$bus.'/'.($pa-1) }}">{{$pa-1}}</a></li>
                            @endif
                            <li class="page-item active">
                                <a class="page-link" href="{{ url('DivEProf/periodos').'/'.$bus.'/'.$pa }}">{{$pa}}</a></li>
                            @if(($pa+1) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/periodos').'/'.$bus.'/'.($pa+1) }}">{{$pa+1}}</a></li>
                            @endif
                            @if(($pa+2) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('DivEProf/periodos').'/'.$bus.'/'.($pa+2) }}">{{$pa+2}}</a></li>
                            @endif

                        <li class="page-item">
                        <a class="page-link" href="{{ url('DivEProf/periodos').'/'.$bus.'/'.$pag }}">Última</a></li>
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