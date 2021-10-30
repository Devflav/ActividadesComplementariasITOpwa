@extends('layouts.profesores')
@section('content')

<div class="container" style="background: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">
    <script>
        var controlNumbers = [];
        var shouldBeButtonsBlocked = '{{ $tipo }}'
        var asistencias = '{{ $grupo[0]->asistencias }}'

    </script>

    <div class="row">
        <div class="col">
            <label for="" class="form-text">LISTA DE ESTUDIANTES </label>
            <label for="" class="form-text">GRUPO: </label>
            <label for="" class="form-text">ACTIVIDAD: </label>
            <label for="" class="form-text">RESPONSABLE: </label>
            <label for="" class="form-text">ASISTENCIAS DE ESTE GRUPO: </label>
        </div>

        <div class="col">

            @foreach($grupo as $g)
                <label> </label><br>
                <label> {{ $g -> clave }} </label><br>
                <label> {{ $g -> actividad }} </label><br>
                <label>{{ $g -> nombre }} {{ $g -> paterno }} {{ $g -> materno }}</label><br>
                <label>{{$grupo[0] -> asistencias}}</label>
            @endforeach
        </div>
    </div>
    <br>
    <div class="d-flex flex-row-reverse mb-2">


        @if($tipo == 'ver')
            @foreach($grupo as $g)
                <a href="{{ url('ProfR/download').$g->id_grupo.'/'.'print' }}"
                    class="btn btn-outline-primary btn-sm">Descargar lista</a>
            @endforeach
        @endif
        @if($tipo == 'verJefe')
            @foreach($grupo as $g)
                <a href="{{ url('JDepto/download').$g->id_grupo.'/'.'noPrint' }}"
                    class="btn btn-outline-primary btn-sm">Descargar lista</a>
            @endforeach
        @endif
    </div>

    <div id="divTable">

        <table class="table table-hover">

            @if($tipo == 'ver')
                <tr class="text-white" style="background:#1B396A;">
                    <th width="15%"># Control</th>
                    <th width="25%">Nombre</th>
                    <th width="30%">Ape. paterno</th>
                    <th width="30%">Ape. materno</th>
                </tr>

                @foreach($alumnos as $e)
                    <tr>
                        <th scope="row">{{ $e -> num_control }}</th>
                        <th>{{ $e -> nombre }}</th>
                        <th>{{ $e -> apePat }}</th>
                        <th>{{ $e -> apeMat }}</th>
                    </tr>
                @endforeach
            @else
                <tr class="text-white" style="background:#1B396A;">
                    <th width="10%">No Control</th>
                    <th width="23%">Nombre</th>
                    <th width="20%">Ape. paterno</th>
                    <th width="20%">Ape. materno</th>
                    <th width="17%">Nivel de desempeño</th>
                    @if($tipo=='evaluar')
                        <th width="10%">Evaluar</th>
                    @else
                        <th width="10%">Opción</th>
                    @endif
                </tr>

                @foreach($alumnos as $e)
                    <tr>
                        <th scope="row">{{ $e -> num_control }}</th>
                        <th>{{ $e -> nombre }}</th>
                        <th>{{ $e -> apePat }}</th>
                        <th>{{ $e -> apeMat }}</th>
                        <th>{{ $e -> nivel_desempenio }}</th>
                        <th>
                            <script>
                                controlNumbers.push('btn-{{ $e->num_control }}')

                            </script>
                            @if($tipo=='evaluar')
                                <a href="{{ url('ProfR/formEval').'/'.$e->num_control.'/'.$e->id_grupo }}"
                                    id='btn-{{ $e->num_control }}' shouldBeDisabled='{{ $e->id_eval }}'
                                    class="btn btn-outline-primary">Evaluar</a>
                            @elseif($tipo=='constancia')
                                <a href="{{ url('ProfR/genConst').$e->num_control }}"
                                    id='btn-{{ $e->num_control }}' shouldBeDisabled='{{ $e->nivel_desempenio }}'
                                    class="btn btn-outline-primary">Constancia</a>
                            @elseif($tipo=='criterio')
                                <a href="{{ url('ProfR/criterioPdf').$e->num_control }}"
                                    id='btn-{{ $e->num_control }}' shouldBeDisabled='{{ $e->id_eval }}'
                                    class="btn btn-outline-primary">Evaluación</a>
                            @endif

                        </th>
                    </tr>
                @endforeach
            @endif
        </table>
    </div>

</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
        data-backdrop='static' data-keyboard='false'>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Control de asistencias</h5>
            </div>
            <div class="modal-body text-center">
                <form method="POST" action="{{ url('/ProfR/updateGroup') }}"
                    role='form' id='grupoForm'>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Total de asistencias de la actividad en el semestre.</label>
                        <input class="form-control d-none" id='id_grupo' name='id_grupo' type='number'
                            value='{{ $grupo[0]->id_grupo }}'>
                        <input class="form-control" id='asistencias' name='asistencias' type='number' required>
                        <input class="form-control d-none" id='origin' name='origin' value='{{ $tipo }}'>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar asistencias</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>


    function shouldBeDisabled(param) {
        if (!param)
            return 'disabled'
        return '';
    }


    (() => {
        if (shouldBeButtonsBlocked == 'constancia' || shouldBeButtonsBlocked == 'criterio')
            controlNumbers.forEach(setDisabled);
        else if (shouldBeButtonsBlocked == 'evaluar')
            controlNumbers.forEach(setDisabledEvaluar)
    })();

    function setDisabled(element) {
        const result = document.querySelector('#' + element);
        const shouldBe = shouldBeDisabled(result.getAttribute('shouldBeDisabled'));
        if (shouldBe || result.getAttribute('shouldBeDisabled') == 'Insuficiente')  {
            result.classList.add('disabled')
        }

    }

    function setDisabledEvaluar(element) {
        const result = document.querySelector('#' + element);
        const shouldBe = shouldBeDisabled(result.getAttribute('shouldBeDisabled'));
        if (!shouldBe)
            result.classList.add('disabled')
    }

    if (shouldBeButtonsBlocked == 'evaluar') {
        if (!asistencias || asistencias === '0' || asistencias === 0) {
            $('#exampleModal').modal('toggle')
            const form = document.getElementById('grupoForm');
        }
    }

</script>
@endsection
