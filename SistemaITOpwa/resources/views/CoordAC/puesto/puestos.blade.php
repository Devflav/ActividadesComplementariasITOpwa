@extends('layouts.coordComple')
@section('content')
    <div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

            <div class="input-group mb-3">
                <label for="" class="form-text">LISTA DE LOS PUESTOS EN EL SISTEMA</label>
                <label for="" class="col-1"></label>
                <div class="input-group-append">
                    <a href="{{ url('CoordAC/nuevoPues') }}" class="btn btn-outline-success btn-sm"><i class="fa fa-fw fa-plus"></i> Agregar </a>
                </div>
            </div>

            <form method="GET" action="{{url('/searchpue')}}">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar puesto por Nombre" name="search" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
                    </div>
                </div>
            </form>
            <div id="divTable">
            <table id="carreras" class="table table-hover">
                <thead style="background:#1B396A; width: 100%; height: 50px;">
                    <tr class="text-white">
                        <th width="05%">#</th>
                        <th width="30%">Puesto</th>
                        <th width="55%">Descripción</th>
                        <th width="10%">Opciones</th>
                    </tr>
                </thead>
                <tbody style="width: 100%">
                @foreach($puestos as $p)
                    <tr>
                        <td>{{$p->id_puesto}}</td>
                        <td>{{$p->nombre}}</td>
                        <td>{{$p->descripcion}}</td>
                        <td>
                        <center>
                            <a href="{{ url('CoordAC/editarPues').$p->id_puesto }}" class="btn btn-outline-primary btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                        </center>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#mimodal" id="btn_mimodal">
        </button>
        <div class="modal fade" id="mimodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="restablecerLabel" aria-hidden="true">
        </div>
        
    </div>
@endsection