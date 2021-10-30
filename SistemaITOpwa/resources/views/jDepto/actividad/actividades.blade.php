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
        
            <div id="divTable" class="table table-hover">
                
                <table class="table">
                    <thead style="background:#1B396A; width: 100%; height: 50px;">
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
                            @else
                                <a href="{{ url('JDepto/editAct').$a->id_actividad }}" class="btn btn-outline-primary btn-sm disabled"><i class="fa fa-fw fa-edit"></i></a>
                            @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
    <div id="divNav" class="row">
        <div class="col">
            <label for="" class="navTotal">
                Total: {{ $actividades->total() }}
            </label>
        </div>
        <div class="col">
            <nav class="navbar navbar-light justify-content-end">
                <ul class="pagination justify-content-end">
                    <li class="{{ ($actividades->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $actividades->url(1) }}" class="page-link">
                            1
                        </a>
                    </li>    
                    <li class="{{ ($actividades->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $actividades->previousPageUrl() }}" class="page-link">
                            <i class="bi bi-arrow-left-square"></i>
                        </a>
                    </li>    
                    <li class="{{ ($actividades->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $actividades->nextPageUrl() }}" class="page-link">
                            <i class="bi bi-arrow-right-square"></i>
                        </a>
                    </li> 
                    <li class="{{ ($actividades->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $actividades->url($actividades->lastPage()) }}" class="page-link">
                            {{ $actividades->lastPage() }}
                        </a>
                    </li> 
                </ul>
            </nav>
        </div>
    </div>
</div>
@endsection