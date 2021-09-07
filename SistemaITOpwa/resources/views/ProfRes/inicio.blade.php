@extends('layouts.profesores')
@section('content')
<div class="container form-content">
	<div class="form-group">
		<div class="col-sm text-center">
            <p class="text-center">
                <h5><em>
                    <!-- <center> -->
                    @if($hora < 12)
                        ¡ Buenos días {{ Auth::user()-> nombre }} !
                    @elseif($hora > 11 && $hora < 19)
                        ¡ Buenas tardes {{ Auth::user()-> nombre }} !
                    @else
                        ¡ Buenas noches {{ Auth::user()-> nombre }} !
                    @endif
                <!-- </center> -->
                </em></h5>
            </p>
        </div>
    </div>
    <hr>
    <div class="form-group">
		<div class="col-sm text-center">
            @if($process == 00)
                <p></p>
            @elseif($process == 01)
                <p>Proceso de Inscripción en curso. Termina el día <strong>{{ $end }}</strong>.</p>
            @elseif($process == 10)
                <p>Proceso de Evaluación en curso. Termina el día <strong>{{ $end }}</strong>.</p>
            @elseif($process == 11)
                <p>Proceso de Generación de Constancias en curso. Termina el día <strong>{{ $end }}</strong>.</p>
            @endif
        </div>
    </div>
</div>
@endsection