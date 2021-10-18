@extends('layouts.divEProf')
@section('content')
    <div class="container" style="padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

            <div class="input-group mb-3">
                <label for="" class="form-text">LISTA DE ESTUDIANTES REGISTRADOS EN EL SISTEMA</label>
                <label for="" class="col-1"></label>
                <div class="input-group-append">
                    <a href="{{ url('DivEProf/nuevoEst') }}" class="btn btn-outline-success btn-sm"><i class="fa fa-fw fa-plus"></i> Agregar </a>
                </div>
            </div>

            <form method="GET" action="{{ url('/DivEProf/searchest') }}">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar estudiante por: Número de control, Nombre o Carrera" name="search" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
                    </div>
                </div>
            </form>
        

        <div id="divTable">
             
             <table class="table table-hover table-responsive" id="students">
                 <thead style="background:#1B396A;">
                    <tr class="text-white">
                        <th width="10%">Número Control</th>
                        <th width="33%">Estudiante</th>
                        <th width="28%">Carrera</th>
                        <th width="09%">Semestre</th>
                        <th width="20%">CURP</th>
                        <!-- <th width="14%">Opciones</th> -->
                    </tr>
                </thead>

                <tbody style="width: 100%">
                @foreach($estudiantes as $e)
                    @if($e->estado == 0)
                    @else
                    <tr>
                        @if($e->num_control != null)
                            <td>{{$e->num_control}}</td>
                            <td>{{$e->estudiante}}</td>
                            <td>{{$e->carrera}}</td>
                            <td><center>{{$e->semestre}}</center></td>
                            <td>{{$e->curp}}</td>
                            <!-- <td>
                                <a href="{{ url('DivEProf/editEst').$e->id_persona }}" 
                                    class="btn btn-outline-primary btn-sm"><i class="fa fa-lg fa-edit"></i></a>
                            </td> -->
                        @endif
                    </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
                
        </div>
        <nav class="navbar navbar-light justify-content-end">
        <ul class="pagination justify-content-end">
            @if($estudiantes->previousPageUrl() != null)
                <li class="page-item">
                    <a href="{{ $estudiantes->previousPageUrl() }}" class="page-link m-1">
                        Anterior
                    </a>
                </li>
            @else
                <li class="page-item disabled"> 
                    <a href="" class="page-link m-1"> Anterior </a>
                </li>
            @endif
            @if($estudiantes->nextPageUrl() != null)
                <li class="page-item">
                    <a href="{{ $estudiantes->nextPageUrl() }}" class="page-link m-1">
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