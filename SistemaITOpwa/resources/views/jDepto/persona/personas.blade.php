@extends('layouts.jDeptos')
@section('content')

    <div class="container" style="padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

            <div class="input-group mb-3">
                <label for="" class="form-text">LISTA DEL PERSONAL REGISTRADO
                <strong>"{{ $pnom->nombre }}"</strong></label>
                <label for="" class="col-1"></label>
                <div class="input-group-append">
                    <a href="{{ url('JDepto/nuevper') }}" class="btn btn-outline-success btn-sm"> + Agregar </a>
                </div>
            </div>

            <form method="GET" action="{{url('JDepto/searchPersonal')}}">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar persona por: Nombre" name="search">
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit"> Buscar </button>
                    </div>
                </div>
            </form>
        

        <div id="divTable">
             
             <table class="table table-hover">
                 
                <tr class="text-white" style="background:#1B396A;">
                    <th width="06%">Grado</th>
                    <th width="40%">Nombre</th>
                    <th width="24%">Puesto</th>
                    <th width="20%">Curp</th>
                    <th width="10%">Opciones</th>
                </tr>

            @foreach($personas as $p)
                <tr>
                    <td>{{ $p-> grado }}</td>
                    <td>{{ $p-> nombre }} {{ $p-> paterno }} {{ $p-> materno }}</td>
                    <td>{{ $p-> puesto }}</td>
                    <td>{{ $p-> curp }}</td>
                    <td>
                        <a href="{{ url('JDepto/editpers').$p->id_persona }}" 
                            class="btn btn-outline-primary btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                    </td>
                </tr>
            @endforeach
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
                        <a class="page-link" href="{{ url('JDepto/personal/1') }}">Primera</a></li>
                        
                            @if(($pa-2) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDepto/personal').'/'.($pa-2) }}">{{$pa-2}}</a></li>
                            @endif
                            @if(($pa-1) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDepto/personal').'/'.($pa-1) }}">{{$pa-1}}</a></li>
                            @endif
                            <li class="page-item active">
                                <a class="page-link" href="{{ url('JDepto/personal').'/'.($pa) }}">{{$pa}}</a></li>
                            @if(($pa+1) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDepto/personal').'/'.($pa+1) }}">{{$pa+1}}</a></li>
                            @endif
                            @if(($pa+2) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDepto/personal').'/'.($pa+2) }}">{{$pa+2}}</a></li>
                            @endif
                       
                        <li class="page-item">
                        <a class="page-link" href="{{ url('JDepto/personal').'/'.$pag }}">Última</a></li>
                    @elseif($vista == 01)
                        <li class="page-item">
                        <a class="page-link" href="{{ url('JDepto/personal').'/'.$bus.'/'.'1' }}">Primera</a></li>

                        @if(($pa-2) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDepto/personal').'/'.$bus.'/'.($pa-2) }}">{{$pa-2}}</a></li>
                            @endif
                            @if(($pa-1) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDepto/personal').'/'.$bus.'/'.($pa-1) }}">{{$pa-1}}</a></li>
                            @endif
                            <li class="page-item active">
                                <a class="page-link" href="{{ url('JDepto/personal').'/'.$bus.'/'.$pa }}">{{$pa}}</a></li>
                            @if(($pa+1) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDepto/personal').'/'.$bus.'/'.($pa+1) }}">{{$pa+1}}</a></li>
                            @endif
                            @if(($pa+2) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('JDepto/personal').'/'.$bus.'/'.($pa+2) }}">{{$pa+2}}</a></li>
                            @endif

                        <li class="page-item">
                        <a class="page-link" href="{{ url('JDepto/personal').'/'.$bus.'/'.$pag }}">Última</a></li>
                    @endif
                @endif
            </ul>
        </nav>

    </div>

@endsection