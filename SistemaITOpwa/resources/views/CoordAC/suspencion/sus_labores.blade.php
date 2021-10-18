@extends('layouts.coordComple')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

    <div class="input-group mb-3">
        <label for="" class="form-text">LISTA DE FECHAS DE SUSPENSIÓN DE LABORES</label>
        <label for="" class="col-1"></label>
        <div class="input-group-append">
            <a href="{{ url('CoordAC/nuevaFecha') }}" class="btn btn-outline-success btn-sm"><i class="fa fa-fw fa-plus"></i> Agregar </a>
        </div>
    </div>

    <form method="GET" action="{{ url('/searchslab') }}">
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Buscar fecha por: Motivo o Día" name="search" required>
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
                        <th width="20%">Fecha</th>
                        <th width="70%">Motivo</th>
                </tr>
            <tbody style="width: 100%">
            <?php $num = (($fechas->currentPage() -1 )*10) + 1; ?> 
            @foreach($fechas as $f)
                <tr>
                    <td>{{$num}}</td>
                    <td>{{$f->fecha}}</td>
                    <td>{{$f->motivo}}</td>
                    </td>
                </tr>
                <?php $num++; ?>
            @endforeach
            </tbody>
        </table>
    </div>

    <nav class="navbar navbar-light justify-content-end">
        <ul class="pagination justify-content-end">
            @if($fechas->previousPageUrl() != null)
                <li class="page-item">
                    <a href="{{ $fechas->previousPageUrl() }}" class="page-link m-1">
                        Anterior
                    </a>
                </li>
            @else
                <li class="page-item disabled"> 
                    <a href="" class="page-link m-1"> Anterior </a>
                </li>
            @endif
            @if($fechas->nextPageUrl() != null)
                <li class="page-item">
                    <a href="{{ $fechas->nextPageUrl() }}" class="page-link m-1">
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

    <button type="button" class="btn btn-primary d-none" data-toggle="modal" 
        data-target="#mimodal" id="btn_mimodal">
    </button>
    <div class="modal fade" id="mimodal" data-backdrop="static" data-keyboard="false" 
        tabindex="-1" aria-labelledby="restablecerLabel" aria-hidden="true">
    </div>

</div>
@endsection