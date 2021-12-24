@extends('layouts.divEProf')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">
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
    <label for="" class="form-text">LISTA DE GRUPOS OFERTADOS 
        <strong> "{{ $periodo }}" </strong>
    </label>
    <label for="" class="col-1"></label>
    <div class="input-group-append">
        @if($mod)    
            <a href="{{ url('DivEProf/nuevoGrupo/1') }}" 
                class="btn btn-outline-success btn-sm"><i class="fa fa-fw fa-plus">
                </i> Agregar 
            </a>
        @else
            <a href="{{ url('DivEProf/nuevoGrupo/1') }}" 
                class="btn btn-outline-success btn-sm disabled"><i class="fa fa-fw fa-plus">
                </i> Agregar 
            </a>
        @endif
    </div>
</div>

<form method="GET" action="{{ url('DivEProf/searchgru') }}">
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Buscar grupo por: Clave, Actividad o Responsable" name="search" required>
        <div class="input-group-append">
            <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
        </div>
    </div>
</form>

<div id="divTable">
    <table class="table table-hover table-responsive">
        <thead style="background:#1B396A;">
                <tr class="text-white">
                    <th width="06%">Clave</th>
                    <th width="27%">Actividad</th>
                    <th width="24%">Responsable</th>
                    <th width="16%">Lugar</th>
                    <th width="10%">Cupo Disp.</th>
                    <th width="08%">Asistencias</th>
                    <th width="09%">Opciones</th>
            </tr>
            </thead>

            <tbody style="width: 100%">
        @foreach($grupos as $g)
            <tr>
                <td>{{$g->clave}}</td>
                <td>{{$g->actividad}}</td>
                <td>{{$g->responsable}}</td>
                <td>{{$g->lugar}}</td>
                <td><center>
                        {{$g->cupo_libre}}</td>
                    </center>
                <td><center>
                        {{$g->asistencias}}</td>
                    </center>
                <td>
                    @if($mod)
                    <center>
                    <a href="{{ url('DivEProf/editarGru').'/'.$g->id_grupo.'/'.$g->id_depto }}" 
                        class="btn btn-outline-primary btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                    </center>
                    @else
                    <center>
                    <a href="{{ url('DivEProf/editarGru').'/'.$g->id_grupo.'/'.$g->id_depto }}" 
                        class="btn btn-outline-primary btn-sm disabled"><i class="fa fa-fw fa-edit"></i></a>
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

<button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#mimodal" id="btn_mimodal">
</button>
<div class="modal fade" id="mimodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="restablecerLabel" aria-hidden="true">
</div>

</div>
@endsection