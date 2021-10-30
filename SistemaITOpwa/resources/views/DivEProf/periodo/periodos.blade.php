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

<form method="GET" action="{{url('DivEProf/searchperi')}}">
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Buscar periodo por Año" name="search" required>
        <div class="input-group-append">
            <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
        </div>
    </div>
</form>
        
<div id="divTable">
    <table id="carreras" class="table table-hover">
        <thead style="background:#1B396A; width: 100%; height: 50px;">
            <tr class="text-white">
                    <th width="30%">Nombre</th>
                    <th width="20%">Inicio Periodo</th>
                    <th width="20%">Fin Periodo</th>
                    <th width="20%">Estado</th>
                    <th width="10%">Opciones</th>
            </tr>
        </thead>
        <tbody >
        @foreach($periodos as $p)
            @if($p->estado == 'Actual')
                <tr>
                    <td><strong> {{ $p->nombre }} </strong></td>
                    <td><strong> {{ $p->inicio }} </strong></td>
                    <td><strong> {{ $p->fin }} </strong></td>
                    <td><strong> {{ $p->estado }} </strong></td>
                    <td>
                        <a href="{{ url('DivEProf/detallePeri').'/'.$p->id_periodo }}" 
                            class="btn btn-outline-primary btn-sm">
                            Detalle
                        </a>
                    </td>
                </tr>
            @else
                <tr>
                    <td> {{ $p->nombre }} </td>
                    <td> {{ $p->inicio }} </td>
                    <td> {{ $p->fin }} </td>
                    <td> {{ $p->estado }} </td>
                    <td>
                        <a href="{{ url('DivEProf/detallePeri').$p->id_periodo }}" 
                            class="btn btn-outline-primary btn-sm">
                            Detalle
                        </a>
                    </td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
</div>

<div id="divNav" class="row">
    <div class="col">
        <label for="" class="navTotal">
            Total: {{ $periodos->total() }}
        </label>
    </div>
    <div class="col">
        <nav class="navbar navbar-light justify-content-end">
            <ul class="pagination justify-content-end">
                <li class="{{ ($periodos->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                    <a href="{{ $periodos->url(1) }}" class="page-link">
                        1
                    </a>
                </li>    
                <li class="{{ ($periodos->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                    <a href="{{ $periodos->previousPageUrl() }}" class="page-link">
                        <i class="bi bi-arrow-left-square"></i>
                    </a>
                </li>    
                <li class="{{ ($periodos->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                    <a href="{{ $periodos->nextPageUrl() }}" class="page-link">
                        <i class="bi bi-arrow-right-square"></i>
                    </a>
                </li> 
                <li class="{{ ($periodos->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                    <a href="{{ $periodos->url($periodos->lastPage()) }}" class="page-link">
                        {{ $periodos->lastPage() }}
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