@extends('layouts.estudiante')
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
    <label> LISTA DE ACTIVIDADES OFERTADAS POR 
        <strong>"@foreach($car as $c) 
                    {{ $c->nombre }} 
                @endforeach"
        </strong>
    </label>

        <div id="divTable">
            <table class="table table-hover">
                    <tr class="text-white" style="background:#1B396A;">
                        <th width="08%">Clave</th>
                        <th width="28%">Nombre</th>
                        <th width="08%">Créditos</th>
                        <th width="25%">Lugar</th>
                        <th width="15%">Horario</th>
                        <th width="14%">Opciones</th>
                    </tr>
                
                @foreach($actividades as $a)
                    <tr>
                        <td>{{$a->clave}}</td>
                        <td>{{$a->nombre}}</td>
                        <td>{{$a->creditos}}</td>
                        <td>{{$a->lugar}}</td>
                        <td>
                        <a class="btn btn-outline-primary btn-sm text-primary" onclick="horario('{{ url('Est/verh').$a->id_grupo }}')">
                           Horario 
                        </a>
                        </td>
                        <td>
                        <center>
                            @if($a->cupo_libre == 0)
                                <button class="btn btn-outline-primary btn-sm" disabled> Lleno </button>
                            @else
                                <a href="{{ url('Est/inscribir').$a->id_grupo }}" class="btn btn-outline-primary btn-sm">Inscribirme</a>
                            @endif
                        </center>
                        </td>
                    </tr>
                @endforeach
                
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
    
    <button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#alerta" id="btn_dw">
    </button>
    <div class="modal fade" id="alerta"  aria-labelledby="alertaLabel" aria-hidden="true">
    </div>
    </div>

@endsection
