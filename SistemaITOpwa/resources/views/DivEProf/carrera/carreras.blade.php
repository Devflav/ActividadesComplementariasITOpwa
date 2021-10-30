@extends('layouts.divEProf')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

<div class="input-group mb-3">
    <label for="" class="form-text">LISTA DE LAS CARRERAS REGISTRADAS</label>
    <label for="" class="col-1"></label>
    <div class="input-group-append">
        <a href="{{ url('DivEProf/nuevaCarr') }}" class="btn btn-outline-success btn-sm"><i class="fa fa-fw fa-plus"></i> Agregar </a>
    </div>
</div>

<form method="GET" action="{{ url('DivEProf/searchcar') }}" class="needs-validation">
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Buscar carrera por Nombre" 
        name="search" id="searchcar" title="Debes introducir un valor para la búsqueda"
        required>
        <div class="input-group-append">
            <button type="submit" class="btn btn-outline-primary"><i class="fa fa-fw fa-search"></i> Buscar </button>
        </div>
    </div>
</form>

<div id="divTable">

<table id="carreras" class="table table-hover">
    <thead style="background:#1B396A; width: 100%; height: 50px;">
        <tr class="text-white">
            <th with="05%">#</th>
            <th with="35%">Nombre</th>
            <th with="60%">Departamento</th>
        </tr>
    </thead>
    
    <tbody style="width: 100%">
    <?php $n = 1; 
    foreach($carreras as $c) { 
            ?>
        <tr>
            <td>{{ $n }}</td>
            <td>{{ $c->nombre }}</td>
            <td>{{ $c->depto }}</td>
        </tr> 
    <?php $n++; } ?>
    </tbody>
</table>
</div>

<div id="divNav" class="row">
<div class="col">
    <label for="" class="navTotal">
        Total: {{ $carreras->total() }}
    </label>
</div>
<div class="col">
    <nav class="navbar navbar-light justify-content-end">
        <ul class="pagination justify-content-end">
            <li class="{{ ($carreras->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                <a href="{{ $carreras->url(1) }}" class="page-link">
                    1
                </a>
            </li>    
            <li class="{{ ($carreras->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                <a href="{{ $carreras->previousPageUrl() }}" class="page-link">
                    <i class="bi bi-arrow-left-square"></i>
                </a>
            </li>    
            <li class="{{ ($carreras->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                <a href="{{ $carreras->nextPageUrl() }}" class="page-link">
                    <i class="bi bi-arrow-right-square"></i>
                </a>
            </li> 
            <li class="{{ ($carreras->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                <a href="{{ $carreras->url($carreras->lastPage()) }}" class="page-link">
                    {{ $carreras->lastPage() }}
                </a>
            </li> 
        </ul>
    </nav>
</div>
</div>

<button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#mimodal" id="btn_mimodal">
</button>
<div class="modal fade" id="mimodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="restablecerLabel" aria-hidden="true">
</div>

</div>

@endsection