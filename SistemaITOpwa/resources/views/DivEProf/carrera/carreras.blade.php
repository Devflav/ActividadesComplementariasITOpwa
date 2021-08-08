@extends('layouts.divEProf')
@section('content')
    <div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

            <div class="input-group mb-3">
                <label for="" class="form-text">LISTA DE LAS CARRERAS REGISTRADAS</label>
                <label for="" class="col-1"></label>
                <div class="input-group-append">
                    <a href="{{ url('DivEProf/nuevaCarr') }}" class="btn btn-outline-success btn-sm"><i class="fa fa-fw fa-plus"></i> Agregar </a>
                </div>
            </div>

            <form method="GET" action="{{ url('/DivEProf/searchcar') }}" class="needs-validation">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar carrera por Nombre" 
                    name="buscar" id="searchcar" title="Debes introducir un valor para la búsqueda"
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
                <?php $num = 1; ?>
                <?php foreach($carreras as $c) { 
                     ?>
                    <tr>
                        <td>{{ $c->id_carrera }}</td>
                        <td>{{ $c->nombre }}</td>
                        <td>{{ $c->depto }}</td>
                        <td>
                            <center>
                            <a href="{{ url('DivEProf/editarCarr').$c->id_carrera }}" class="btn btn-outline-primary btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                            </center>
                        </td>
                    </tr> 
                <?php $num++; } ?>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#mimodal" id="btn_mimodal">
        </button>
        <div class="modal fade" id="mimodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="restablecerLabel" aria-hidden="true">
        </div>
        
    </div>

@endsection