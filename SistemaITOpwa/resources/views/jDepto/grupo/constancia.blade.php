@extends('layouts.jDeptos')
@section('content')
<div class="container" style="padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

    <div class="input-group mb-3">
        <label for="" class="form-text">LISTA DE LOS GRUPOS OFERTADOS 
        <strong>"{{ $pnom->nombre }}"</strong></label>
    </div>

    <div id="divTable">

        <table class="table table-hover">
            <thead style="">
                <tr class="text-white" style="background:#1B396A;">
                    <th width="05%">Clave</th>
                    <th width="09%">Periodo</th>
                    <th width="20%">Actividad</th>
                    <th width="24%">Responsable</th>
                    <th width="20%">Lugar</th>
                    <th width="05%">Cupo</th>
                    <th width="09%">Opciones</th>
                </tr>
            </thead>
            <tbody style="">
                @foreach($grupos as $g)
                    <tr>
                        <td>{{ $g->clave }}</td>
                        <td>{{ $g->periodo }}</td>
                        <td>{{ $g->actividad }}</td>
                        <td>{{ $g->nomP }} {{ $g->paterno }} {{ $g->materno }}</td>
                        <td>{{ $g->lugar }}</td>
                        <td>{{ $g->cupo }}</td>
                        <td>
                            @if($origin == 'constancia')
                                <a href="{{ url('JDepto/lista_alumnos').$g->id_grupo.'/'.'constancia' }}"
                                    class="btn btn-outline-primary btn-sm w-100 mb-2">Constancia</a>
                                <a href="{{ url('JDepto/lista_alumnos').$g->id_grupo.'/'.'criterio' }}"
                                    class="btn btn-sm w-100 btn-outline-primary">Evaluación</a>
                            @else
                                <a href="{{ url('JDepto/lista_alumnos').$g->id_grupo.'/'.'ver' }}"
                                    class="btn btn-outline-primary btn-sm w-100 mb-2">Lista</a>
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
                Total: {{ $grupos->total() }}
            </label>
        </div>
        <div class="col">
            <nav class="navbar navbar-light justify-content-end">
                <ul class="pagination justify-content-end">
                    <li class="{{ ($grupos->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $grupos->url(1) }}" class="page-link">
                            1
                        </a>
                    </li>    
                    <li class="{{ ($grupos->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $grupos->previousPageUrl() }}" class="page-link">
                            <i class="bi bi-arrow-left-square"></i>
                        </a>
                    </li>    
                    <li class="{{ ($grupos->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $grupos->nextPageUrl() }}" class="page-link">
                            <i class="bi bi-arrow-right-square"></i>
                        </a>
                    </li> 
                    <li class="{{ ($grupos->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $grupos->url($grupos->lastPage()) }}" class="page-link">
                            {{ $grupos->lastPage() }}
                        </a>
                    </li> 
                </ul>
            </nav>
        </div>
    </div>

</div>
@endsection
