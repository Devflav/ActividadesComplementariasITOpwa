@extends('layouts.jDeptos')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 15%; padding-right: 15%;">  
        <div class="text-justify">
            <div class="col">
                <p>
                    <h5><em><center>
                        @if($hora < 12)
                            ¡ Buenos días {{ Auth::user()-> nombre }} !
                        @elseif($hora > 11 && $hora < 19)
                            ¡ Buenas tardes {{ Auth::user()-> nombre }} !
                        @else
                            ¡ Buenas noches {{ Auth::user()-> nombre }} !
                        @endif
                    </center></em></h5>
                </p>
<hr>
                
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