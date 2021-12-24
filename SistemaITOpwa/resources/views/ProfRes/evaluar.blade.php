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
            <div class="input-group mb-3">
                <h5>LISTA DE GRUPOS A MI CARGO</h5>
            </div>

            <form method="POST" action="{{url('/buscarGru')}}">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar grupo por: clave, periodo, responsable" name="buscarG">
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
                            <!-- <th width="20%">Periodo</th> -->
                            <th width="30%">Actividad</th>
                            <th width="10%">Opciones</th>
                    </tr>
                </thead>
                <tbody style="">
                @foreach($grupos as $g)
                    <tr>
                        <td>{{$g->clave}}</td>

                        <td>{{$g->actividad}}</td>
                        @if($origin== 'evaluar')
                        <td>
                            <a href="{{ url('ProfR/lista').$g->id_grupo }}" class="btn btn-outline-primary btn-sm">Evaluar</a>
                        </td>
                        @elseif($origin =='constancia')
                        <td>
                            <a href="{{ url('ProfR/lista').$g->id_grupo }}" class="btn btn-outline-primary btn-sm">Constancias</a>
                        </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
@endsection
