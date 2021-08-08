@extends('layouts.coordCar')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 40px;">
    <div class="justify-content-center">
		<div class="col-md-12">
        <label for="" ><strong> - HISTORIAL DEL ESTUDIANTE -</strong></label>

            @if($search == 0)
                <form method="GET" action="{{ url('CoordC/searchest') }}">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Escribe el número de control del estudiante" 
                        name="search" pattern="[0-9]{8}|[B][0-9]{8}|[0-9]{9}" required>
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
                        </div>
                    </div>
                </form>

                @else
                <form method="GET" action="{{ url('CoordC/searchest') }}">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Escribe el número de control del estudiante" 
                        name="search" pattern="[0-9]{8}|[B][0-9]{8}|[0-9]{9}" required>
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
                        </div>
                    </div>
                </form>
                <div class="card border-bottom-0 border-left-0 border-right-0 border-top-0" style="background-color: transparent;">
                    
                    @if($student == null)
                        Estudiante no registrado en el sistema. <br><br>
                    @else
                        <div id="mitexto" class="card-body justify-content-center">
                            <div  class="row">
                                <div  class="form-group col-2 badge" style="background:#1B396A;">
                                    <h6 for="" class="text-white">Número de Control</h6>
                                </div>
                                <div class="form-group col-5 badge" style="background:#1B396A;">
                                    <h6 for="" class="text-white">Estudiante</h6>
                                </div>
                                <div class="form-group col-1 badge" style="background:#1B396A;">
                                    <h6 for="" class="text-white">Semestre</h6>
                                </div>
                                <div class="form-group col-4 badge" style="background:#1B396A;">
                                    <h6 for="" class="text-white">Carrera</h6>
                                </div>
                            </div> 

                            <div class="row">
                                @foreach($student as $s)
                                    <div class="form-group col-2">
                                        <label for="">{{ $s -> num_control }}</label>
                                    </div>
                                    <div class="form-group col-5">
                                        <label for="">{{ $s -> nombre }} {{ $s -> apePat }} {{ $s -> apeMat }}</label>
                                    </div>
                                    <div class="form-group col-1">
                                        <center>{{ $s -> semestre }}</center>
                                    </div>
                                    <div class="form-group col-4">
                                        <label for="">{{ $s -> carrera }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div> 
                    @endif
                    
                    
                    @if($inscripcion == null)
                        No hay historial del Estudiante 
                    @else
                        @php $peri = 0; @endphp
                        @foreach($inscripcion as $i)
                                @if($i -> id_periodo != $peri)
                                <div class="card-header text-white" style="background:#1B396A;"> 
                                    <center>
                                        {{ $i -> periodo }}
                                    </center>
                                </div>
                                    @php $peri = $i -> id_periodo @endphp
                                @else
                                @endif
                                <div id="mitexto" class="card-body justify-content-center">

                                    <div  class="row">
                                        <div  class="form-group col-2 badge badge-info">
                                            <h6 for="" class="text-white">Fecha de Inscripción</h6>
                                        </div>
                                        <div class="form-group col-6 badge badge-info">
                                            <h6 for="" class="text-white">Actividad</h6>
                                        </div>
                                        <div class="form-group col-4 badge badge-info">
                                            <h6 for="" class="text-white">Constancia</h6>
                                        </div>
                                    </div> 

                                    <div class="row">
                                        <div class="form-group col-2">
                                            <label for="">{{ $i -> fecha }}</label>
                                        </div>
                                        <div class="form-group col-6">
                                                <label for="">{{ $i -> clave }} - {{ $i -> actividad }}</label>
                                        </div>
                                        <div class="form-group col-4">
                                            @if($i -> id_evaluacion == null)
                                                Estudiante no evaluado.
                                            @else
                                                @if($i -> constancia == null)
                                                    @if($i -> aprobada == 0)
                                                        Esperando confirmación de inscripción.
                                                    @elseif($i -> aprobada == 1)
                                                        Cursando actividad.
                                                    @elseif($i -> aprobada == 2)
                                                        Inscripción no aprobada.
                                                    @elseif($i -> aprobada == 3)
                                                        Actividad dada de baja.
                                                    @elseif($i -> aprobada == 4)
                                                        Actividad evaluada, sin registro de constancia.
                                                    @endif
                                                @else
                                                    <button class="btn btn-sm btn-outline-info float-right" onclick="fx_show('/storage/{{ $i->constancia }}', 780)"><i class="fa fa-lg fa-file-pdf-o"></i> Ver Constancia </button>
                                                @endif
                                            @endif       
                                        </div>
                                    </div>
                                                                                                                  
                                </div>
                            </div> 
                        @endforeach
                    @endif
                </div>
            @endif
		</div>
	</div>
</div>

@endsection