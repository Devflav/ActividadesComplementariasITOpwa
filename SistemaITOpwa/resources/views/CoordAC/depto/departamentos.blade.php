@extends('layouts.coordComple')
@section('content')
    <div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

            <div class="input-group mb-3">
                <label for="" class="form-text">LISTA DE LOS DEPARTAMENTOS REGISTRADOS</label>
                <label for="" class="col-1"></label>
                <div class="input-group-append">
                    <a href="{{ url('CoordAC/nuevoDepto') }}" class="btn btn-outline-success btn-sm"><i class="fa fa-fw fa-plus"></i> Agregar </a>
                </div>
            </div>

            <form method="GET" action="{{ url('/searchdpt') }}">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar departamento por Nombre" name="search" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
                    </div>
                </div>
            </form>
            <div id="divTable">

            <table id="" class="table table-hover">   
                
                <thead style="background:#1B396A; width: 100%; height: 50px;">
                    <tr class="text-white">
                        <th width="45%">Departamento</th>
                        <th width="45%">Jefe del Departamento</th>
                        <th width="10%">Opciones</th>
                    </tr>
                </thead>

                <tbody style="width: 100%">
                @foreach($departamentos as $d)
                    <tr>
                        <td>{{$d->depto}}</td>
                        <td>{{$d->grado}} {{$d->nombre}} {{$d->apePat}} {{$d->apeMat}}</td>
                        <td>
                            <center>
                            <a href="{{ url ('CoordAC/editDepto').$d->id_depto }}" class="btn btn-outline-primary btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                            </center>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-end">
                @if($pag == 1)
                    <li class="page-item disabled">
                    <a class="page-link" href="">Página 1</a></li>
                @else
                    @if($vista == 00)
                        <li class="page-item">
                        <a class="page-link" href="{{ url('CoordAC/departamentos').'/'.'1' }}">Primera</a></li>
                        
                            @if(($pa-2) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('CoordAC/departamentos').'/'.($pa-2) }}">{{$pa-2}}</a></li>
                            @endif
                            @if(($pa-1) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('CoordAC/departamentos').'/'.($pa-1) }}">{{$pa-1}}</a></li>
                            @endif
                            <li class="page-item active">
                                <a class="page-link" href="{{ url('CoordAC/departamentos').'/'.($pa) }}">{{$pa}}</a></li>
                            @if(($pa+1) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('CoordAC/departamentos').'/'.($pa+1) }}">{{$pa+1}}</a></li>
                            @endif
                            @if(($pa+2) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('CoordAC/departamentos').'/'.($pa+2) }}">{{$pa+2}}</a></li>
                            @endif
                       
                        <li class="page-item">
                        <a class="page-link" href="{{ url('CoordAC/departamentos').'/'.$pag }}">Última</a></li>
                    @elseif($vista == 01)
                        <li class="page-item">
                        <a class="page-link" href="{{ url('CoordAC/departamentos').'/'.$bus.'/'.'1' }}">Primera</a></li>

                        @if(($pa-2) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('CoordAC/departamentos').'/'.$bus.'/'.($pa-2) }}">{{$pa-2}}</a></li>
                            @endif
                            @if(($pa-1) > 0)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('CoordAC/departamentos').'/'.$bus.'/'.($pa-1) }}">{{$pa-1}}</a></li>
                            @endif
                            <li class="page-item active">
                                <a class="page-link" href="{{ url('CoordAC/departamentos').'/'.$bus.'/'.$pa }}">{{$pa}}</a></li>
                            @if(($pa+1) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('CoordAC/departamentos').'/'.$bus.'/'.($pa+1) }}">{{$pa+1}}</a></li>
                            @endif
                            @if(($pa+2) <= $pag)
                                <li class="page-item">
                                <a class="page-link" href="{{ url('CoordAC/departamentos').'/'.$bus.'/'.($pa+2) }}">{{$pa+2}}</a></li>
                            @endif

                        <li class="page-item">
                        <a class="page-link" href="{{ url('CoordAC/departamentos').'/'.$bus.'/'.$pag }}">Última</a></li>
                    @endif
                @endif
            </ul>
        </nav>

        <button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#mimodal" id="btn_mimodal">
        </button>
        <div class="modal fade" id="mimodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="restablecerLabel" aria-hidden="true">
        </div>
    </div>
@endsection