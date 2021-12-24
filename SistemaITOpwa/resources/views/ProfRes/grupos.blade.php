@extends('layouts.profesores')
@section('content')
    <div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">
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
                <h5>LISTA DE GRUPOS A MI CARGO</h5>
            <br>

            <form method="GET" action="{{url('/pr/searchgru')}}">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar grupo por: Clave o Actividad" name="search" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit"> Buscar </button>
                    </div>
                </div>
            </form>
            <div id="divTable">
             
                <table class="table table-hover">
                    <thead style=""> 
                    <tr class="text-white" style="background:#1B396A;">
                        

                                <th width="10%">Clave</th>
                                <th width="22%">Actividad</th>
                                <th width="20%">Lugar</th>
                                <th width="10%">Asistencias</th>
                                <th width="05%">Cupo</th>
                                <th width="10%">Créditos</th>
                                <th width="13%">Opciones</th>                      
                        </tr>
                    </thead>
                    <tbody style="">
                            @foreach($grupos as $g)
                                <tr>
                                    <td>{{ $g-> clave }}</td>
                                    <td>{{ $g-> actividad }}</td>
                                    <td>{{ $g-> lugar }}</td>
                                    <td><center>{{ $g-> asistencias }}</center></td>
                                    <td><center>{{ $g-> cupo }}</center></td>
                                    <td><center>{{ $g-> creditos }}</center></td>
                                    <td>
                                        @if($tipo == 'Ver')
                                            <a class="btn btn-outline-primary btn-sm text-primary" onclick="horario('{{ url('ProfR/verh').$g->id_grupo }}')">
                                            Horario
                                            </a>
                                            <a href="{{ url('ProfR/lista').$g->id_grupo.'/ver' }}" class="btn btn-outline-primary btn-sm">Ver Lista</a>
                                        @elseif($tipo == 'evaluar')
                                        <center>
                                            @if($mod)
                                                <a href="{{ url('ProfR/lista').$g->id_grupo.'/evaluar'}}" 
                                                class="btn btn-outline-primary btn-sm">Evaluar</a>
                                            @else
                                                <a href="{{ url('ProfR/lista').$g->id_grupo.'/evaluar'}}" 
                                                class="btn btn-outline-primary btn-sm disabled">Evaluar</a>
                                            @endif
                                        </center>
                                        @elseif($tipo == 'constancia')
                                            @if($mod)
                                                <a href="{{ url('ProfR/lista').$g->id_grupo.'/constancia'}}" 
                                                    class="btn btn-outline-primary btn-sm w-100">Constancia</a>
                                                <a href="{{ url('ProfR/lista').$g->id_grupo.'/criterio'}}" 
                                                    class="btn btn-outline-primary btn-sm mt-1 w-100">Formato Evaluación</a>
                                            @else
                                            <a href="{{ url('ProfR/lista').$g->id_grupo.'/constancia'}}" 
                                                class="btn btn-outline-primary btn-sm w-100 disabled">Constancia</a>
                                            <a href="{{ url('ProfR/lista').$g->id_grupo.'/criterio'}}" 
                                                class="btn btn-outline-primary btn-sm mt-1 w-100 disabled">Formato Evaluación</a>
                                            @endif
                                        @endif
                                    
                                    </td>
                                </tr>
                            @endforeach
                    </tbody>
                </table>
        </div>
    </div>
    <button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#alerta" id="btn_dw">
        </button>
        <div class="modal fade" id="alerta"  aria-labelledby="alertaLabel" aria-hidden="true">
        </div>
    </div>
@endsection
