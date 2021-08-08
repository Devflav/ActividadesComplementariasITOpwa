@extends('layouts.servesc')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 40px;">
    <div class="justify-content-center">
		<div class="col-md-12">
        <label for="" ><strong> - HISTORIAL DEL ESTUDIANTE -</strong></label>

            @if($search == 0)
                <form method="GET" action="{{ url('ServEsc/searchest') }}">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Escribe el número de control del estudiante" 
                        name="search" pattern="[0-9]{8}|[B][0-9]{8}|[0-9]{9}" required>
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
                        </div>
                    </div>
                </form>

                @else
                <form method="GET" action="{{ url('ServEsc/searchest') }}">
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
                        No hay historial del Estudiante <br> <br> <br>
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
                                                        <button href="" data-toggle="modal" data-target="#saveproof" class="btn btn-sm btn-outline-success float-right"><i class="fa fa-lg fa-save"> </i>  Constancia  </button>
                                                    @endif
                                                    
                                                @else
                                                    <!--<button data-toggle="modal" data-target="#seepdf" class="btn btn-sm btn-outline-info float-right"><i class="fa fa-lg fa-file-pdf-o"></i> {{ $i -> constancia}}</button>-->
                                                    <button class="btn btn-sm btn-outline-info float-right" onclick="fx_show('/storage/{{ $i->constancia }}', 780)"><i class="fa fa-lg fa-file-pdf-o"></i> Ver Constancia </button>
                                                    
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                                    <div class="modal fade" id="saveproof" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header" style="background:#1B396A;">
                                                                    <h5 class="modal-title text-white" id="staticBackdropLabel">SELECCIONAR CONSTANCIA</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true" class="text-white">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="POST" action="{{ url('ServEsc/saveproof').'/'.$i->id_evaluacion }}" class="needs-validation" enctype="multipart/form-data">
                                                                        @csrf

                                                                        <div class="form-group col">
                                                                            <label for="logoEnca"> Constancia de cumplimiento:</label>
                                                                            <div class="custom-file col">
                                                                                <input type="file" class="custom-file-input" id="" name="constancia" accept="application/pdf" required>
                                                                                <label class="custom-file-label" for="customFile">Selecciona el archivo</label>
                                                                            </div>
                                                                            <div class="valid-feedback">Valid.</div>
                                                                            <div class="invalid-feedback">Por favor rellena el campo.</div>
                                                                        </div>
                                                                        <!-- <div class="row">
                                                                            <div class="col">
                                                                                <div class="custom-file col">
                                                                                    <input type="file" class="custom-file-input" id="customFile" name="proof" accept="application/pdf" required/>
                                                                                    <label class="custom-file-label" for="customFile">Selecciona el archivo</label>
                                                                                </div>
                                                                            </div>
                                                                        </div> -->
                                                                        <!-- <div class="row">
                                                                            <label href="" class="text-primary"><strong>{{ $i -> id_evaluacion }}</strong></label>
                                                                        </div> -->
                                                                        <br><br>
                                                                        <div class="row float-right">
                                                                            <div class="col">
                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                                                <button type="submit" class="btn btn-primary">Guardar</button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
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