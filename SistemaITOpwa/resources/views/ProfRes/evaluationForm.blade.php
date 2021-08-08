@extends('layouts.profesores')
@section('content')
<script>const formElements = {{count($critEval)}}</script>
<script>const asistenciasGrupo = {{$asistencias->asistencias}}</script>
<div class='container mb-5'>
    <div class='row d-flex justify-content-center'>
        <label>
            Nombre del estudiante:
            {{ $asistencias->nombre . ' ' . $asistencias->apePat . ' ' . $asistencias->apeMat }}
            <br>
            Numero de control: {{ $asistencias->nControl }}
        </label>
    </div>
    <div class='d-flex justify-content-center'>
        <div class='row'>
            <form class='col-12' method="POST" action="{{ url('/ProfR/saveEval') }}" role='form'
                id='evaluacionForm'>
                {{ csrf_field() }}
                @foreach($critEval as $c)
                    <div class="form-group">
                        <label>{{ $c -> description }}</label>
                        <select class="form-control" id='select-{{ $c ->  id_crit_eval }}'
                            name='{{ $c -> id_crit_eval }}'>
                            @foreach($nivelD as $d)
                                <option value='{{ $d -> valor }}'>{{ $d -> nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach
                <div class="form-group">
                    <label>Observaciones</label>
                    <textarea class="form-control" id='observaciones' name='observaciones' rows="3"
                        required
                        placeholder='observacion que se desee agregar sobre la evaluacion realizada'></textarea>
                </div>
                <div class="form-group">
                    <label>Asistencias</label>
                    <input class="form-control" name='asistencias' id='asistencias' rows="3" placeholder='0'
                        type='number' readonly>
                </div>
                <div class='row'>
                    <div class="form-group col-6">
                        <label>Valor numerico de la activida complementaria</label>
                        <input readonly type="text" class="form-control" name='calificacion' placeholder="0"
                            id="resultadoNumerico">
                    </div>
                    <div class="form-group col-6">
                        <label>Nivel de Desempenio de la actividad complementaria</label>
                        <input readonly type="text" class="form-control" id="resultado" placeholder="Insuficiente">
                    </div>
                    <input readonly type="hidden" class="form-control" name='idDesempenio' id="idDesempenio"
                        placeholder='0'>
                    <input readonly type="hidden" class="form-control" name='n_control' value='{{ $n_control }}'>
                </div>
                <button type="submit" class="btn btn-primary">Guardar evaluacion</button>
                <a href="{{ URL::previous() }}" class="btn btn-danger">Cancelar</a>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
        data-backdrop='static' data-keyboard='false'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Control de asistencias</h5>
            </div>
            <div class="modal-body">
                <form role='form' id='grupoForm'>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Ingrese el total de asistencias del alumno.</label>
                        <label>Total de asistencias del grupo: {{$asistencias->asistencias}}</label>
                        <input class="form-control" id='asistenciasModal' type='number' required>
                    </div>
                    <button type="submit" class="btn btn-primary" id='saveAsistencias'>Guardar asistencias</button>
                    <a href="{{ URL::previous() }}" class="btn btn-danger">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('mijs/form.js') }}"></script>
@endsection
