@extends('layouts.coordComple')
@section('content')

<div class="container" style="background-color: transparent; padding-left: 02%; padding-right: 02%; padding-bottom: 05%;">
    @if (session('Catch') != null)
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<h5 class="alert-heading"> 
				<strong> <em> ¡ Error ! </em> </strong>
				<i class="bi bi-x-octagon-fill close float-right" type="button" data-dismiss="alert"></i>
			</h5>
			<hr>
			<p class="text-justify"> {{ session('Catch') }} </p>
		</div>
    @endif
    <div class="input-group mb-3">
        <label for="" class="form-text">LISTA DE ACTIVIDADES OFERTADAS 
                <strong> "{{ $periodo }}" </strong>
        </label>
        <label for="" class="col-1"></label>
        <div class="input-group-append">
        @if($mod)
            <a href="{{ url('CoordAC/nuevaAct') }}" class="btn btn-outline-success btn-sm">
                <i class="fa fa-fw fa-plus"></i> Agregar 
            </a>
        @else
            <a href="{{ url('CoordAC/nuevaAct') }}" class="btn btn-outline-success btn-sm disabled">
                <i class="fa fa-fw fa-plus"></i> Agregar 
            </a>
        @endif
        </div>
    </div>

    <form method="GET" action="{{ url('/searchact') }}">
        <div class="input-group mb-3">
            <input type="text" class="form-control" 
                placeholder="Buscar actividad por: Clave ó Nombre" name="search" required>
            <div class="input-group-append">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="fa fa-fw fa-search"></i> Buscar 
                </button>
            </div>
        </div>
    </form>

    <div id="divTable">
        <table id="carreras" class="table table-hover">
            <thead style="background:#1B396A; width: 100%; height: 50px;">
                <tr class="text-white">
                            <th width="05%">Clave</th>
                            <th width="30%">Nombre</th>
                            <th width="05%">Créditos</th>
                            <th width="40%">Departamento</th>
                            <th width="10%">Tipo</th>
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
                    <td>
                    @if($mod)
                        <center>
                        <a href="{{ url('CoordAC/editarAct').'/'.$a->id_actividad }}" 
                            class="btn btn-outline-primary btn-sm" title="Editar">
                            <i class="fa fa-fw fa-edit"></i>
                        </a>
                        </center>
                    @else
                    <center>
                        <a href="{{ url('CoordAC/editarAct').$a->id_actividad }}" 
                            class="btn btn-outline-primary btn-sm disabled" title="Editar">
                            <i class="fa fa-fw fa-edit"></i>
                        </a>
                    </center>
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

    <button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#mimodal" id="btn_mimodal">
    </button>
    <div class="modal fade" id="mimodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="restablecerLabel" aria-hidden="true">
    </div>
</div>
@endsection