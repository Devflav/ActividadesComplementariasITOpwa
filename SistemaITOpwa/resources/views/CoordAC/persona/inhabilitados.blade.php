@extends('layouts.coordComple')
@section('content')

    <div class="container" style="padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

        <div class="input-group mb-3">
            <label for="" class="form-text">LISTA DEL PERSONAL INHABILITADO</label>

        </div>

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
                        <td width="30%">{{ $p-> nombre }} {{ $p-> paterno }} {{ $p-> materno }}</td>
                        <td width="25%">{{ $p-> depto }}</td>
                        <td width="10%">{{ $p-> puesto }}</td>
                        <td width="20%">{{ $p-> curp }}</td>
                        <td width="10%">
                            <a href="{{ url('CoordAC/editPer').$p->id_persona }}" class="btn btn-outline-primary btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
            </table>
   
        </div>

        <div class="row">
            <div class="form-group col float-right">
                <a href="{{ url('CoordAC/personal/1') }}" class="btn btn-sm btn-outline-primary float-right">Personal</a>
            </div>
        </div>

        <button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#mimodal" id="btn_mimodal">
        </button>
        <div class="modal fade" id="mimodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="restablecerLabel" aria-hidden="true">
        </div>

    </div>

@endsection