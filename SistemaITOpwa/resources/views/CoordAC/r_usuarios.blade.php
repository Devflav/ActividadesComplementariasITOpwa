@extends('layouts.coordComple')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 10%;">

    <form method="GET" action="{{ url('/searchusu')}}">
        <label for="" class="form-text">LISTA DE LOS USUARIOS REGISTRADOS EN EL SISTEMA</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Buscar usuario por: Nombre, #Control o Curp" name="search" required>
            <div class="input-group-append">
                <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
            </div>
        </div>
    </form>

    <div id="divTable">
        <table class="table table-hover table-responsive">
                 <thead style="background:#1B396A;">
                    <tr class="text-white">
                        <th width="10%">Tipo</th>
                        <th width="20%">Nombre</th>
                        <th width="20%">A. Paterno</th>
                        <th width="20%">A. Materno</th>
                        <th width="20%">Usuario</th>
                        <th width="10%">Restablecer</th>
                    </tr>
                </thead>
                <tbody style="width: 100%">
            @foreach($persona as $p)
                <tr>
                    <td>{{$p->tipo}}</td>
                    <td>{{$p->nombre}}</td>
                    <td>{{$p->apePat}}</td>
                    <td>{{$p->apeMat}}</td>
                    <td>{{$p->usuario}}</td>
                    <td>
                        <center>
                            <a class="btn btn-outline-primary btn-sm text-primary" 
                                onclick="usurest('{{ url('/CoordAC/usuariorestart').$p->id_persona }}')">
                                <i class="fa fa-fw fa-history"></i>
                            </a>
                        </center> 
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div id="divNav" class="row">
        <div class="col">
            <label for="" class="navTotal">
                Total: {{ $persona->total() }}
            </label>
        </div>
        <div class="col">
            <nav class="navbar navbar-light justify-content-end">
                <ul class="pagination justify-content-end">
                    <li class="{{ ($persona->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $persona->url(1) }}" class="page-link">
                            1
                        </a>
                    </li>    
                    <li class="{{ ($persona->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $persona->previousPageUrl() }}" class="page-link">
                            <i class="bi bi-arrow-left-square"></i>
                        </a>
                    </li>    
                    <li class="{{ ($persona->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $persona->nextPageUrl() }}" class="page-link">
                            <i class="bi bi-arrow-right-square"></i>
                        </a>
                    </li> 
                    <li class="{{ ($persona->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $persona->url($persona->lastPage()) }}" class="page-link">
                            {{ $persona->lastPage() }}
                        </a>
                    </li> 
                </ul>
            </nav>
        </div>
    </div>

    <button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#restablecer" 
        id="btn_restablecer">
    </button>
    <div class="modal fade" id="restablecer" data-backdrop="static" data-keyboard="false" 
        tabindex="-1" aria-labelledby="restablecerLabel" aria-hidden="true">
    </div>

</div>

@endsection