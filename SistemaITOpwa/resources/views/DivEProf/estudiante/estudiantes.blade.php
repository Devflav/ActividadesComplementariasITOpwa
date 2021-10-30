@extends('layouts.divEProf')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 10%;">

<div class="input-group mb-3">
    <label for="" class="form-text">LISTA DE ESTUDIANTES REGISTRADOS EN EL SISTEMA</label>
    <label for="" class="col-1"></label>
    <div class="input-group-append">
        <a href="{{ url('DivEProf/nuevoEst') }}" class="btn btn-outline-success btn-sm"><i class="fa fa-fw fa-plus"></i> Agregar </a>
    </div>
</div>

<form method="GET" action="{{ url('DivEProf/searchest') }}">
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Buscar estudiante por: Número de control, Nombre o Carrera" name="search" required>
        <div class="input-group-append">
            <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
        </div>
    </div>
</form>

<div id="divTable">
        <table class="table table-hover table-responsive" id="students">
            <thead style="background:#1B396A;">
            <tr class="text-white">
                <th width="14%">Número Control</th>
                <th width="35%">Estudiante</th>
                <th width="28%">Carrera</th>
                <th width="09%">Semestre</th>
            </tr>
        </thead>

        <tbody style="width: 100%">
        @foreach($estudiantes as $e)
            @if($e->estado == 0)
            @else
            <tr>
                @if($e->num_control != null)
                    <td>{{$e->num_control}}</td>
                    <td>{{$e->estudiante}}</td>
                    <td>{{$e->carrera}}</td>
                    <td><center>{{$e->semestre}}</center></td>
                @endif
            </tr>
            @endif
        @endforeach
        </tbody>
    </table> 
</div>

<div id="divNav" class="row">
    <div class="col">
        <label for="" class="navTotal">
            Total: {{ $estudiantes->total() }}
        </label>
    </div>
    <div class="col">
        <nav class="navbar navbar-light justify-content-end">
            <ul class="pagination justify-content-end">
                <li class="{{ ($estudiantes->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                    <a href="{{ $estudiantes->url(1) }}" class="page-link">
                        1
                    </a>
                </li>    
                <li class="{{ ($estudiantes->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                    <a href="{{ $estudiantes->previousPageUrl() }}" class="page-link">
                        <i class="bi bi-arrow-left-square"></i>
                    </a>
                </li>    
                <li class="{{ ($estudiantes->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                    <a href="{{ $estudiantes->nextPageUrl() }}" class="page-link">
                        <i class="bi bi-arrow-right-square"></i>
                    </a>
                </li> 
                <li class="{{ ($estudiantes->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                    <a href="{{ $estudiantes->url($estudiantes->lastPage()) }}" class="page-link">
                        {{ $estudiantes->lastPage() }}
                    </a>
                </li> 
            </ul>
        </nav>
    </div>
</div>

<button type="button" class="btn btn-primary d-none" data-toggle="modal" 
    data-target="#mimodal" id="btn_mimodal">
</button>
<div class="modal fade" id="mimodal" data-backdrop="static" data-keyboard="false" 
    tabindex="-1" aria-labelledby="restablecerLabel" aria-hidden="true">
</div>
</div>
@endsection