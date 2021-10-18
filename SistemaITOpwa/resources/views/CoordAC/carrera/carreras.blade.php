@extends('layouts.coordComple')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

        <div class="input-group mb-3">
            <label for="" class="form-text">LISTA DE LAS CARRERAS REGISTRADAS</label>
            <label for="" class="col-1"></label>
            <div class="input-group-append">
                <a href="{{ url('CoordAC/nuevaCarr') }}" class="btn btn-outline-success btn-sm"><i class="fa fa-fw fa-plus"></i> Agregar </a>
            </div>
        </div>

        <form method="GET" action="{{ url('/searchcar') }}" class="needs-validation">
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
                    <th with="30%">Nombre</th>
                    <th with="55%">Departamento</th>
                    <th with="10%">Opciones</th>
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
                    <td>
                        <center>
                        <a href="{{ url('CoordAC/editarCarr').$c->id_carrera }}" class="btn btn-outline-primary btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                        </center>
                    </td>
                </tr> 
            <?php $n++; } ?>
            </tbody>
        </table>
    </div>

    <nav class="navbar navbar-light justify-content-end">
        <ul class="pagination justify-content-end">
            @if($carreras->previousPageUrl() != null)
                <li class="page-item">
                    <a href="{{ $carreras->previousPageUrl() }}" class="page-link m-1">
                        Anterior
                    </a>
                </li>
            @else
                <li class="page-item disabled"> 
                    <a href="" class="page-link m-1"> Anterior </a>
                </li>
            @endif
            @if($carreras->nextPageUrl() != null)
                <li class="page-item">
                    <a href="{{ $carreras->nextPageUrl() }}" class="page-link m-1">
                        Siguiente
                    </a>
                </li>
            @else
            <li class="page-item disabled"> 
                    <a href="" class="page-link m-1"> Siguiente </a>
                </li>
            @endif
        </ul>
    </nav>

    <button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#mimodal" id="btn_mimodal">
    </button>
    <div class="modal fade" id="mimodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="restablecerLabel" aria-hidden="true">
    </div>
    
</div>

@endsection