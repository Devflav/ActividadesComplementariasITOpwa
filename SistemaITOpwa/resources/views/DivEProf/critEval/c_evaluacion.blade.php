@extends('layouts.divEProf')
@section('content')
    <div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

            <div class="input-group mb-3">
                <label for="" class="form-text">LISTA DE LOS CRITERIOS DE EVALUACIÓN REGISTRADOS</label>
                <label for="" class="col-1"></label>
                <div class="input-group-append">
                    <a href="{{ url('DivEProf/nuevoCritEval') }}" class="btn btn-outline-success btn-sm"><i class="fa fa-fw fa-plus"></i> Agregar </a>
                </div>
            </div>

            <form method="GET" action="{{ url('/DivEProf/searchcrit') }}">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar cirterio de evaluación por Nombre" name="buscar" id="serachcrit">
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
                    </div>
                </div>
            </form>
            <div id="divTable">

             <table id="criterios" class="table table-hover table-responsive" >
                
                <thead style="background:#1B396A; width: 100%; height: 50px;">
                    <tr class="text-white">
                        <th width="05%">#</th>
                        <th width="25%">Nombre</th>
                        <th width="60%">Descripción</th>
                        <th width="10%">Opciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php $num = 1; ?> 
                @foreach($criterios as $c)
                    <tr>
                        <td>{{$num}}</td>
                        <td>{{$c->nombre}}</td>
                        <td>{{$c->descripcion}}</td>
                        <td>
                            <center>
                            <a href="{{ url('DivEProf/editCritEval').$c->id_crit_eval }}" class="btn btn-outline-primary btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                            </center>
                        </td>
                    </tr>
                    <?php $num++; ?>
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