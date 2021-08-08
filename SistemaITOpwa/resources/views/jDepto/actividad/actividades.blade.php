@extends('layouts.jDeptos')
@section('content')
    <div class="container" style="background-color: transparent; padding-left: 02%; padding-right: 02%; padding-bottom: 05%;">

            <div class="input-group mb-3">
                <label for="" class="form-text">LISTA DE ACTIVIDADES OFERTADAS 
                        <strong> "{{ $pnom->nombre }}" </strong>
                </label>
                <label for="" class="col-1"></label>
                <div class="input-group-append">
                @if($mod)
                    <a href="{{ url('JDepto/nuevAct') }}" class="btn btn-outline-success btn-sm"> + Agregar </a>
                @else
                    <a href="{{ url('JDepto/nuevAct') }}" class="btn btn-outline-success btn-sm disabled"> + Agregar </a>
                @endif
                </div>
            </div>

            <form method="GET" action="{{url('JDepto/searchActividad')}}">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar actividad por: Clave o Nombre" name="search">
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit"> Buscar </button>
                    </div>
                </div>
            </form>
        
            <div id="divTable" class="table-hover table-responsive">
                
                <table class="table">
                    <thead id="divTable">
                        <tr class="text-white" style="background:#1B396A;">
                                <th width="05%">Clave</th>
                                <th width="20%">Nombre</th>
                                <th width="05%">Créditos</th>
                                <th width="25%">Departamento</th>
                                <th width="10%">Tipo</th>
                                <th width="20%">Descripción</th>
                                <th width="10%">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
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
                                <a href="{{ url('JDepto/editAct').$a->id_actividad }}" class="btn btn-outline-primary btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                                <a href="/eliminarEst/{{ $a->id_actividad }}" class="btn btn-outline-danger btn-sm"><i class="fa fa-fw fa-trash-o"></i></a>
                            @else
                                <a href="{{ url('JDepto/editAct').$a->id_actividad }}" class="btn btn-outline-primary btn-sm disabled"><i class="fa fa-fw fa-edit"></i></a>
                                <a href="{{ url('/eliminarEst').'/'.$a->id_actividad }}" class="btn btn-outline-danger btn-sm disabled"><i class="fa fa-fw fa-trash-o"></i></a>
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
                        <a class="page-link" href="{{ url('JDepto/actividad/1') }}">Primera</a></li>
                        
                            @if(($pa-2) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDepto/actividad').'/'.($pa-2) }}">{{$pa-2}}</a></li>
                            @endif
                            @if(($pa-1) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDepto/actividad').'/'.($pa-1) }}">{{$pa-1}}</a></li>
                            @endif
                            <li class="page-item active">
                                <a class="page-link" href="{{ url('JDepto/actividad').'/'.($pa) }}">{{$pa}}</a></li>
                            @if(($pa+1) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDepto/actividad').'/'.($pa+1) }}">{{$pa+1}}</a></li>
                            @endif
                            @if(($pa+2) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDepto/actividad').'/'.($pa+2) }}">{{$pa+2}}</a></li>
                            @endif
                       
                        <li class="page-item">
                        <a class="page-link" href="{{ url('JDepto/actividad').'/'.$pag }}">Última</a></li>
                    @elseif($vista == 01)
                        <li class="page-item">
                        <a class="page-link" href="{{ url('JDepto/actividad').'/'.$bus.'/'.'1' }}">Primera</a></li>

                        @if(($pa-2) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDepto/actividad').'/'.$bus.'/'.($pa-2) }}">{{$pa-2}}</a></li>
                            @endif
                            @if(($pa-1) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDepto/actividad').'/'.$bus.'/'.($pa-1) }}">{{$pa-1}}</a></li>
                            @endif
                            <li class="page-item active">
                                <a class="page-link" href="{{ url('JDepto/actividad').'/'.$bus.'/'.$pa }}">{{$pa}}</a></li>
                            @if(($pa+1) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDepto/actividad').'/'.$bus.'/'.($pa+1) }}">{{$pa+1}}</a></li>
                            @endif
                            @if(($pa+2) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDepto/actividad').'/'.$bus.'/'.($pa+2) }}">{{$pa+2}}</a></li>
                            @endif

                        <li class="page-item">
                        <a class="page-link" href="{{ url('JDepto/actividad').'/'.$bus.'/'.$pag }}">Última</a></li>
                    @endif
                @endif
            </ul>
        </nav>
    </div>
@endsection