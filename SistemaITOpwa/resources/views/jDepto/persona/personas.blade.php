@extends('layouts.jDeptos')
@section('content')

    <div class="container" style="padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

            <div class="input-group mb-3">
                <label for="" class="form-text">LISTA DEL PERSONAL REGISTRADO
                <strong>"{{ $pnom->nombre }}"</strong></label>
                <label for="" class="col-1"></label>
                <div class="input-group-append">
                    <a href="{{ url('JDepto/nuevper') }}" class="btn btn-outline-success btn-sm"> + Agregar </a>
                </div>
            </div>

            <form method="GET" action="{{url('JDepto/searchPersonal')}}">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar persona por: Nombre" name="search">
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit"> Buscar </button>
                    </div>
                </div>
            </form>
        

        <div id="divTable">
             
             <table class="table table-hover">
                 
                <tr class="text-white" style="background:#1B396A;">
                    <th width="06%">Grado</th>
                    <th width="40%">Nombre</th>
                    <th width="24%">Puesto</th>
                    <th width="20%">Curp</th>
                    <th width="10%">Opciones</th>
                </tr>

            @foreach($personas as $p)
                <tr>
                    <td>{{ $p-> grado }}</td>
                    <td>{{ $p-> empleado }}</td>
                    <td>{{ $p-> puesto }}</td>
                    <td>{{ $p-> curp }}</td>
                    <td>
                        <a href="{{ url('JDepto/editpers').$p->id_persona }}" 
                            class="btn btn-outline-primary btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                    </td>
                </tr>
            @endforeach
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
    </div>

@endsection