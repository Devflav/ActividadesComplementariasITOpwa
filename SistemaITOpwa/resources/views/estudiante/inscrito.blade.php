@extends('layouts.estudiante')
@section('content')
    <div class="container" style="background-color: transparent; padding-left: 15%; padding-right: 15%;">  
        <div class="text-justify">
                <div class="col">
                    <p class="text-justify">
                        @if($v == 00)
                            Solo puedes inscribirte a una actividad por semestre,
                            y ya has seleccionado una. <br>
                            Dirigete a la barra de navegación que se encuentra en 
                            la parte superior y selecciona la opción 
                            "<a href="{{ url('Est/cursando') }}"><em>En curso</em></a>",
                            o haz click en este enlace.
                            Ahí encontrarás los datos de la actividad a la cual estas 
                            inscrito. <br>
                            Puedes conocer el proceso oficial para el desarrollo de una
                            Actividad Complementaria establecido en el Manual de Lineamientos
                            del Tecnológico Nacional de México, dirigete a la barra de navegación 
                            que se encuentra en la parte superior y selecciona la opción 
                            "Actividades/<a href="">Lineamiento TecNM</a>", o haz click en este enlace.
                        @elseif($v == 01)
                            Por el momento no estas cursando ninguna Actividad Complementaria. <br>
                            Puedes ver todas las Actividades si vas a la barra de navegación en la
                            parte superior y despliegas la opción 
                            "<a href="{{ url('Est/micarrera') }}"><em>Actividades</em></a>". <br>
                            Ahí podrás escoger la Actividad de tu agrado y solicitar tu inscripción. <br>
                            Si ya te inscribiste a una actividad espera a que tu solicitud sea aprobada
                            para que puedas visualizar el detalle de la actividad en la opción 
                            "<a href="{{ url('Est/cursando') }}"><em>En curso</em></a>".
                        @elseif($v == 10)
                            No cuentas con un historial, ya que no has cursado ninguna actividad
                            complementaria, o si estas cursando una espera a que concluya el
                            periodo de evaluación para visualizar tu historial. <br>
                            Puedes ver todas las Actividades si vas a la barra de navegación en la
                            parte superior y despliegas la opción 
                            "<a href="{{ url('Est/micarrera') }}"><em>Actividades</em></a>". <br>
                            Ahí podrás escoger la Actividad de tu agrado y solicitar tu inscripción.
                        @elseif($v == 11)
                            Ya no puedes visualizar las actividades, el periodo de inscripciones 
                            ha finalizado.
                        @endif
                    </p>
                </div>
        </div>
    </div>
@endsection