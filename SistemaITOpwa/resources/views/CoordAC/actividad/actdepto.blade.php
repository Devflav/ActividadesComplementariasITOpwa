@extends('layouts.coordComple')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

    <label for="carrera"> LISTA DE ACTIVIDADES OFERTADAS POR <strong>"DEPARTAMENTO"</strong>  </label>
        
    <div id="divTable">
        <table id="carreras" class="table table-hover">
            <thead style="background:#1B396A; width: 100%; height: 50px;">
                <tr class="text-white">
                    <th width="70%">Departamentos</th>
                    <th width="30%">Actividades</th>
                </tr>
            </thead>
            <tbody>
            @foreach($deptos as $d)
                <tr>
                    <td>{{ $d -> nombre }}</td>
                    <td>
                    <center>
                        <a href="{{ url('CoordAC/actdep').'/'.$d->id_depto.'/1' }}" 
                            class="btn btn-outline-primary btn-sm">
                            Ver actividades
                        </a>
                    </center>
                    </td>
                </tr>
            @endforeach
            </tbody>  
        </table>
    </div>
    <nav class="navbar navbar-light justify-content-end">
        <ul class="pagination justify-content-end">
            @if($deptos->previousPageUrl() != null)
                <li class="page-item">
                    <a href="{{ $deptos->previousPageUrl() }}" class="page-link m-1">
                        Anterior
                    </a>
                </li>
            @else
                <li class="page-item disabled"> 
                    <a href="" class="page-link m-1"> Anterior </a>
                </li>
            @endif
            @if($deptos->nextPageUrl() != null)
                <li class="page-item">
                    <a href="{{ $deptos->nextPageUrl() }}" class="page-link m-1">
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