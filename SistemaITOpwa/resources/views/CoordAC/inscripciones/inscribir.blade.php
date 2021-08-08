@extends('layouts.coordComple')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">
    <div class="row justify-content-center">
		<div class="col-md-10">
			<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
				<div class="card-header text-white" style="background:#1B396A;"> Datos del Estudiante </div>
					<div class="card-body">
						@foreach($student as $e)
						<div class="row" >
							<div class="form-group col-2">
								<label for="nControl">Número de Control</label>
								<input type="text" class="form-control" value="{{ $e->num_control }}" disabled>

							</div>

							<div class="form-group col">
								<label for="nombre">Nombre</label>
								<input type="text" class="form-control" value="{{ $e->nombre }} {{ $e->apePat }} {{ $e->apeMat }}" disabled>
							
							</div>

                            <div class="form-group col-1">
								<label for="semestre">Semestre</label>
								<input type="text" class="form-control" value="{{ $e->semestre }}" disabled>
							</div>

                            <div class="form-group col">
								<label for="carrera">Carrera</label>
								<input type="text" class="form-control" value="{{ $e->carrera }}" disabled>

							</div>
						</div>
					    @endforeach
                </div>
                
				<div class="form-group">
                    <select name="depto" id="deptoactividad" class="form-control">
                        <option value="">Filtrar actividades por departamento</option>
                        <?php foreach($deptos as $d){ ?>
                            <option value="{{ $d -> id_depto }}"> {{ $d -> nombre}} </option>
                        <?php } ?>
                    </select>
                </div>

                <div id="divTable">
                    <table id="carreras" class="table table-hover">
                        <thead style="background:#1B396A; width: 100%; height: 50px;">
                            <tr class="text-white">
                                    <th width="10%">Clave</th>
                                    <th width="30%">Actividad</th>
                                    <th width="10%">Créditos</th>
                                    <th width="30%">Departamento</th>
                                    <th width="10%">Restringida</th>
                                    <th width="10%">Inscribir</th>
                            </tr>
                            </thead>
                        <tbody style="width: 100%">
                            <?php foreach($grupos as $g){ ?>
                                <tr>
                                <?php if($g->id_depto == $dpt) {?>
                                    <td>{{$g->clave}}</td>
                                    <td>{{$g->nombre}}</td>
                                    <td>{{$g->creditos}}</td>
                                    <td>{{$g->depto}}</td>
                                    <?php if($g->restringida == 1) {?>    
                                        <td>SI</td>
                                    <?php }else{ ?>
                                        <td>NO</td>
                                    <?php } ?>
                                    <td>
                                    <center>
                                        @if($g->cupo_libre == 0)
                                            <button class="btn btn-outline-primary" disabled>Lleno</button>
                                        @else
                                            <a href="{{ url ('CoordAC/register').'/'.$std.'/'.$g->id_grupo }}"
                                                    class="btn btn-outline-primary">Inscribir</a>
                                        @endif
                                    </center>
                                    </td>
                                <?php }else{ ?>
                                        <?php if($g->restringida == 1) {?>

                                        <?php }else{ ?>
                                            <td>{{$g->clave}}</td>
                                            <td>{{$g->nombre}}</td>
                                            <td>{{$g->creditos}}</td>
                                            <td>{{$g->depto}}</td>
                                            <?php if($g->restringida == 1) {?>    
                                                <td>SI</td>
                                            <?php }else{ ?>
                                                <td>NO</td>
                                            <?php } ?>
                                            <td>
                                            <center>
                                                @if($g->cupo_libre == 0)
                                                    <button class="btn btn-outline-primary" disabled>Lleno</button>
                                                @else
                                                    <a href="{{ url ('CoordAC/register').'/'.$std.'/'.$g->id_grupo }}"
                                                            class="btn btn-outline-primary">Inscribir</a>
                                                @endif
                                            </center>
                                            </td>
                                        <?php } ?>
                                <?php } ?>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

		</div>
	</div>
</div>
@endsection