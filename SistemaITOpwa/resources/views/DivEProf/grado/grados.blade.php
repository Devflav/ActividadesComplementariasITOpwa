@extends('layouts.divEProf')
@section('content')
    <div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

            <div class="input-group mb-3">
                <label for="" class="form-text">LISTA DE LOS GRADOS DE PERSONAL </label>
                <label for="" class="col-1"></label>
                <div class="input-group-append">
                    <a href="{{ url('DivEProf/nuevoGrado') }}" class="btn btn-outline-success btn-sm"><i class="fa fa-fw fa-plus"></i> Agregar </a>
                </div>
            </div>

            <form method="GET" action="{{ url('/DivEProf/searchgra') }}">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar grado por Nombre" name="search">
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
                    </div>
                </div>
            </form>
            <div id="divTable">
            
            <table id="carreras" class="table table-hover">
                <thead style="background:#1B396A; width: 100%; height: 50px;">
                    <tr class="text-white">
                            <th width="10%">#</th>
                            <th width="15%">Grado</th>
                            <th width="75%">Descripción</th>
                            <!-- <th width="10%">Opciones</th> -->
                    </tr>
                    </thead>
                <tbody style="width: 100%">
                @php $id = 1; @endphp
                @foreach($grados as $g)
                    <tr>
                        <td>{{$g->id_grado}}</td>
                        <td>{{$g->nombre}}</td>
                        <td>{{$g->significado}}</td>
                        <!-- <td>
                            <a href="{{ url('DivEProf/editGrado').$g->id_grado }}" 
                                class="btn btn-outline-primary btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                        </td> -->
                    </tr>
                    @php $id++; @endphp
                @endforeach
                </tbody>
            </table>
        </div>
        <nav class="navbar navbar-light justify-content-end">
        <ul class="pagination justify-content-end">
            @if($grados->previousPageUrl() != null)
                <li class="page-item">
                    <a href="{{ $grados->previousPageUrl() }}" class="page-link m-1">
                        Anterior
                    </a>
                </li>
            @else
                <li class="page-item disabled"> 
                    <a href="" class="page-link m-1"> Anterior </a>
                </li>
            @endif
            @if($grados->nextPageUrl() != null)
                <li class="page-item">
                    <a href="{{ $grados->nextPageUrl() }}" class="page-link m-1">
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