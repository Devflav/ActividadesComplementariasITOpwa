@extends('layouts.jDeptos')
@section('content')
<div class="container" style="padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

    <div class="input-group mb-3">
        <label for="" class="form-text">LISTA DE LOS GRUPOS OFERTADOS 
        <strong>"{{ $pnom->nombre }}"</strong></label>
    </div>

    <div id="divTable">

        <table class="table table-hover">
            <thead style="">
                <tr class="text-white" style="background:#1B396A;">
                    <th width="05%">Clave</th>
                    <th width="09%">Periodo</th>
                    <th width="20%">Actividad</th>
                    <th width="24%">Responsable</th>
                    <th width="20%">Lugar</th>
                    <th width="05%">Cupo</th>
                    <th width="09%">Opciones</th>
                </tr>
            </thead>
            <tbody style="">
                @foreach($grupos as $g)
                    <tr>
                        <td>{{ $g->clave }}</td>
                        <td>{{ $g->periodo }}</td>
                        <td>{{ $g->actividad }}</td>
                        <td>{{ $g->nomP }} {{ $g->paterno }} {{ $g->materno }}</td>
                        <td>{{ $g->lugar }}</td>
                        <td>{{ $g->cupo }}</td>
                        <td>
                            @if($origin == 'constancia')
                                <a href="{{ url('JDepto/lista_alumnos').$g->id_grupo.'/'.'constancia' }}"
                                    class="btn btn-outline-primary btn-sm w-100 mb-2">Constancia</a>
                                <a href="{{ url('JDepto/lista_alumnos').$g->id_grupo.'/'.'criterio' }}"
                                    class="btn btn-sm w-100 btn-outline-primary">Evaluación</a>
                            @else
                                <a href="{{ url('JDepto/lista_alumnos').$g->id_grupo.'/'.'ver' }}"
                                    class="btn btn-outline-primary btn-sm w-100 mb-2">Lista</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@endsection
