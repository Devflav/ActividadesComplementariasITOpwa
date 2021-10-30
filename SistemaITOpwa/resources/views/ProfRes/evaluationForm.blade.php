@extends('layouts.profesores')
@section('content')
<script>const formElements = {{count($critEval)}}</script>
<script>const asistenciasGrupo = {{$asistencias->asistencias}}</script>
<div class='container form-content col-sm-9'>
    <div class='row d-flex justify-content-center'>
        <label>
            <strong> Nombre del estudiante: </strong>
            {{ $asistencias->nombre . ' ' . $asistencias->apePat . ' ' . $asistencias->apeMat }}
            <br>
            <strong> Número de control: </strong> {{ $asistencias->nControl }}
        </label>
    </div>
    <!-- <div class='container form-content col-sm-10'> -->
        <!-- <div class='form-group'> -->
            <form method="POST" action="{{ url('/ProfR/saveEval') }}" role='form'
                id='evaluacionForm'>
                {{ csrf_field() }}
                @foreach($critEval as $c)
                    <div class="form-group">
                        <div class="col-sm">
                            <label>{{ $c -> descripcion }}</label>
                            <select class="form-control" id='select-{{ $c ->  id_crit_eval }}'
                                name='{{ $c -> id_crit_eval }}'>
                                @foreach($nivelD as $d)
                                <option value='{{ $d -> valor }}'>{{ $d -> nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endforeach
                <div class="form-group">
                    <div class="col-sm">

                        <label>Observaciones</label>
                        <textarea class="form-control" id='observaciones' name='observaciones' rows="3"
                        required
                        placeholder='Observacion que se desee agregar sobre la evaluacion realizada'></textarea>
                    </div>
                </div>
                <div class="form-group">
                        <div class="col-sm">

                            <label>Asistencias</label>
                            <input class="form-control" name='asistencias' id='asistencias' rows="3" placeholder='0'
                            type='number' readonly>
                        </div>
                </div>
                <div class='form-group'>
                    <div class="col-sm">
                        <label>Valor numerico de la activida complementaria</label>
                        <input readonly type="text" class="form-control" name='calificacion' placeholder="0"
                            id="resultadoNumerico">
                    </div>
                    <div class="col-sm">
                        <label>Nivel de Desempenio de la actividad complementaria</label>
                        <input readonly type="text" class="form-control" id="resultado" placeholder="Insuficiente">
                    </div>
                    <input readonly type="hidden" class="form-control" name='idDesempenio' id="idDesempenio"
                        placeholder='0'>
                    <input readonly type="hidden" class="form-control" name='n_control' value='{{ $n_control }}'>
                    <input readonly type="hidden" class="form-control" name='id_grupo' value='{{ $asistencias->id_grupo }}'>
                </div>

                <div class="container">
                    <div class="form-group">
                        <div class="col-sm"> </div>
                        <br>
                        <div class="col-sm">
                            <button type="submit" class="btn btn-primary">Guardar evaluacion</button>
                        </div>
                        <br>
                        <div class="col-sm">
                            <a href="{{ URL::previous() }}" class="btn btn-danger">Cancelar</a> 
                        </div>
                        <div class="col-sm"> </div>
                    </div>
                </div>
            </form>
        <!-- </div> -->
    <!-- </div> -->
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
        data-backdrop='static' data-keyboard='false'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Control de asistencias</h5>
            </div>
            <div class="modal-body text-center">
                <form role='form' id='grupoForm'>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Ingrese el total de asistencias del alumno.</label>
                    </div>
                    <div class="form-group">
                    <label>Total de asistencias del grupo: {{$asistencias->asistencias}}</label>
                    </div>
                    <div class="form-group">
                        <div class="col-sm"></div>
                        <div class="col-sm">
                            <input class="form-control" id='asistenciasModal' type='number' required>
                        </div>
                        <div class="col-sm"></div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm">
                            <button type="submit" class="btn btn-primary" id='saveAsistencias'>Guardar asistencias</button>
                        </div>
                        <div class="col-sm">
                            <a href="{{ URL::previous() }}" class="btn btn-danger">Cancelar</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('mijs/form.js') }}"></script>
@endsection
