@extends('layouts.estudiante')
@section('content')
    <div class="container" style="background-color: transparent; padding-left: 15%; padding-right: 15%;">  
    @if (session('Catch') != null)
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<h5 class="alert-heading"> 
				<strong> <em> ¡ Error ! </em> </strong>
				<i class="bi bi-x-octagon-fill close float-right" type="button" data-dismiss="alert"></i>
			</h5>
			<hr>
			<p class="text-justify"> {{ session('Catch') }} </p>
		</div>
    @endif
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
                <p class="text-justify">
                    Puedes conocer el proceso oficial para el desarrollo de una
                    Actividad Complementaria establecido en el Manual de Lineamientos
                    del Tecnológico Nacional de México, dirigete a la barra de navegación 
                    que se encuentra en la parte superior y selecciona la opción 
                    "<a href="{{ url('Est/lineamiento') }}"><em>Actividades / Lineamiento TecNM</em></a> ", 
                    o haz click en este enlace.
                </p>
<hr>
                
                    @if($process == 00)
                        <p></p>
                    @elseif($process == 01)
                        <p><strong>¡ESTUDIANTE!</strong> recuerda que nos encontramos en la etapa 
                        de <strong>INSCRIPCIONES</strong>. <br> Si planeas cursar una Actividad Complementaria 
                        y no la haz seleccionado aún estas a tiempo. No te confies, pues las 
                        inscripciones estarán abiertas hasta la fecha <strong>{{ $end }}</strong>.</p>
                    @elseif($process == 10)
                        <p><strong>¡ESTUDIANTE!</strong> te informamos que nos encontramos en la etapa de 
                        <strong>EVALUACIÓN</strong>. <br>En estos momentos tu profesor se encuentra
                        evaluando su o sus grupos, si quieres conocer más sobre este proceso te 
                        invitamos a que leas el <strong>Manual de Lineamientos del TecNM</strong>.</p>
                    @elseif($process == 11)
                        <p><strong>¡ESTUDIANTE!</strong> te informamos que nos encontramos en la etapa 
                        de <strong>GENERACIÓN DE CONSTANCIAS</strong>, te pedimos por favor seas paciente 
                        para recibir tu constancia, ya que este proceso termina en la fecha <strong>{{ $end }}</strong>, 
                        y se lleva a cabo bajo lo normado en el <strong>Manual de Lineamientos del TecNM</strong>.</p>
                    @endif
                
            </div>
        </div>
    </div>
@endsection