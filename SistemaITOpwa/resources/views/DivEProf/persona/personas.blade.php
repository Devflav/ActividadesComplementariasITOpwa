@extends('layouts.divEProf')
@section('content')

    <div class="container" style="padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

            <div class="input-group mb-3">
                <label for="" class="form-text">LISTA DEL PERSONAL REGISTRADO</label>
                <label for="" class="col-1"></label>
                <div class="input-group-append">
                    <a href="{{ url('DivEProf/nuevaPer') }}" class="btn btn-outline-success btn-sm"><i class="fa fa-fw fa-plus"></i> Agregar </a>
                </div>
            </div>

            <form method="GET" action="{{url('/DivEProf/searchpers')}}">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar persona por: Nombre ó Departamento" name="search">
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
                    </div>
                </div>
            </form>
        

        <div id="divTable">
            <table id="carreras" class="table table-hover">
                <thead style="background:#1B396A; width: 100%; height: 50px;">
                    <tr class="text-white">
                        <th width="05%">Grado</th>
                        <th width="30%">Nombre</th>
                        <th width="25%">Departamento</th>
                        <th width="10%">Puesto</th>
                        <th width="20%">CURP</th>
                        <th width="10%">Opciones</th>
                    </tr>
                </thead>
                <tbody style="width: 100%">
            @foreach($personas as $p)
                @if($p->estado == 0)
                @else
                    <tr>
                        <td width="05%">{{ $p-> grado }}</td>
                        <td width="30%">{{ $p-> empleado }}</td>
                        <td width="25%">{{ $p-> depto }}</td>
                        <td width="10%">{{ $p-> puesto }}</td>
                        <td width="20%">{{ $p-> curp }}</td>
                        <td width="10%">
                            <a href="{{ url('DivEProf/editPer').$p->id_persona }}" class="btn btn-outline-primary btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
            </table>
        </div>
        <nav class="navbar navbar-light justify-content-end">
        <ul class="pagination justify-content-end">
            @if($personas->previousPageUrl() != null)
                <li class="page-item">
                    <a href="{{ $personas->previousPageUrl() }}" class="page-link m-1">
                        Anterior
                    </a>
                </li>
            @else
                <li class="page-item disabled"> 
                    <a href="" class="page-link m-1"> Anterior </a>
                </li>
            @endif
            @if($personas->nextPageUrl() != null)
                <li class="page-item">
                    <a href="{{ $personas->nextPageUrl() }}" class="page-link m-1">
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