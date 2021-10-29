@extends('layouts.coordComple')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

    <?php if($type == 13){ ?>

        <form method="GET" action="{{ url('/cac/inscripciones') }}">
            <div class="row">
                <div class="form-group col-8">
                    @if($mod)
                    <select name="dpt" id="" class="form-control" required>
                        <option value="">Selecciona un departamento para ver sus inscripciones</option>
                        <?php foreach($dpts as $d){ ?>
                            <option value="{{ $d -> id_depto }}">{{ $d -> nombre}}</option>
                        <?php } ?>
                    </select>
                    @else
                    <select name="dpt" id="" class="form-control" disabled>
                        <option value="">Selecciona un departamento para ver sus inscripciones</option>
                        <?php foreach($dpts as $d){ ?>
                            <option value="{{ $d -> id_depto }}">{{ $d -> nombre}}</option>
                        <?php } ?>
                    </select>
                    @endif
                </div>

                <div class="form-group col"> 
                @if($mod)
                    <button class="btn btn-outline-primary float-right" type="submit"> Ver inscripciones </button>
                @else
                    <button class="btn btn-outline-primary float-right disabled" type="submit" disabled> Ver inscripciones </button>
                @endif  
                </div>
            </div>
        </form>

    <?php }else{ ?>

        <form method="GET" action="{{ url('/cac/inscripciones') }}">
            <div class="row">
                <div class="form-group col-8">
                    @if($mod)
                    <select name="dpt" id="" class="form-control" required>
                        <option value="">Selecciona un departamento para ver sus inscripciones</option>
                        <?php foreach($dpts as $d){ ?>
                            <option value="{{ $d -> id_depto }}">{{ $d -> nombre}}</option>
                        <?php } ?>
                    </select>
                    @else
                    <select name="dpt" id="" class="form-control" disabled>
                        <option value="">Selecciona un departamento para ver sus inscripciones</option>
                        <?php foreach($dpts as $d){ ?>
                            <option value="{{ $d -> id_depto }}">{{ $d -> nombre}}</option>
                        <?php } ?>
                    </select>
                    @endif
                </div>

                <div class="form-group col">    
                @if($mod)
                    <button class="btn btn-outline-primary float-right" type="submit"> Ver inscripciones </button>
                @else
                    <button class="btn btn-outline-primary float-right disabled" type="submit"> Ver inscripciones </button>
                @endif  
                </div>
            </div>
        </form>

        <label for="" class="form-text">LISTA DE INSCRIPCIONES 
            <strong>  
                    @if($type == 0)
                        "POR APROBAR DE {{ $dptn -> nombre }}"
                    @elseif($type == 1)
                        "APROBADAS DE {{ $dptn -> nombre }}"
                    @elseif($type == 2)
                        "NO APROBADAS DE {{ $dptn -> nombre }}"
                    @elseif($type == 3)
                        "DADAS DE BAJA DE {{ $dptn -> nombre }}"
                    @endif
            </strong>  
        </label>  
        @if($type == 0)
            <form method="GET" action="{{ url('/searchPA').'/'.$dptn->id_depto }}">
        @elseif($type == 1)
            <form method="GET" action="{{ url('/searchA').'/'.$dptn->id_depto }}">
        @elseif($type == 2)
            <form method="GET" action="{{ url('/searchNA').'/'.$dptn->id_depto }}">
        @else
            <form method="GET" action="{{ url('/searchBJ').'/'.$dptn->id_depto }}">
        @endif
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Buscar estudiante por: Número de control" name="search" required>
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
                </div>
            </div>
        </form>

        <div class="">
            <div class="btn-group-sm form-group col"> 
                @if($type == 0)
                <a href="{{ url('CoordAC/inscripPA').'/'.$dptn->id_depto.'/1' }}" class="btn btn-outline-primary active">Por Aprobar</a>
                @else
                <a href="{{ url('CoordAC/inscripPA').'/'.$dptn->id_depto.'/1' }}" class="btn btn-outline-primary">Por Aprobar</a>
                @endif

                @if($type == 1)
                <a href="{{ url('CoordAC/inscripA').'/'.$dptn->id_depto.'/1' }}" class="btn btn-outline-primary active">Aprobadas</a>
                @else
                <a href="{{ url('CoordAC/inscripA').'/'.$dptn->id_depto.'/1' }}" class="btn btn-outline-primary">Aprobadas</a>
                @endif

                @if($type == 2)
                <a href="{{ url('CoordAC/inscripNA').'/'.$dptn->id_depto.'/1' }}" class="btn btn-outline-primary active">No Aprobadas</a>
                @else
                <a href="{{ url('CoordAC/inscripNA').'/'.$dptn->id_depto.'/1' }}" class="btn btn-outline-primary">No Aprobadas</a>
                @endif

                @if($type == 3)
                <a href="{{ url('CoordAC/inscripBJ').'/'.$dptn->id_depto.'/1' }}" class="btn btn-outline-primary active">Dadas de Baja</a>
                @else
                <a href="{{ url('CoordAC/inscripBJ').'/'.$dptn->id_depto.'/1' }}" class="btn btn-outline-primary">Dadas de Baja</a>
                @endif

                @if($type == 4)
                <a href="{{ url('CoordAC/impHorario').'/'.$dptn->id_depto.'/1' }}" class="btn btn-outline-primary active">Imprimir Horarios</a>
                @else
                <a href="{{ url('CoordAC/impHorario').'/'.$dptn->id_depto.'/1' }}" class="btn btn-outline-primary">Imprimir Horarios</a>
                @endif
            </div>
        </div>
        
        <div id="divTable">
            <table id="inscripciones" class="table table-hover">
                <thead style="background:#1B396A; width: 100%; height: 50px;">
                    <tr class="text-white">
                        @if($type == 4)
                        <th width="10%">Clave</th>
                        <th width="40%">Actividad</th>
                        <th width="05%">Cupo</th>
                        <th width="12%">Ins. Aprobadas</th>
                        <th width="12%">Ins. por Aprobar</th>
                        <th width="10%">Imprimir</th>
                        @elseif($type == 1)
                        <th width="09%"># Control</th>
                        <th width="33%">Estudiante</th>
                        <th width="08%">Semestre</th>
                        <th width="34%">Actividad</th>
                        <th width="08%">Opciones</th>
                        <th width="08%"></th>
                        @else
                        <th width="10%"># Control</th>
                        <th width="35%">Estudiante</th>
                        <th width="10%">Semestre</th>
                        <th width="35%">Actividad</th>
                        <th width="10%">Opciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody style="width: 100%">
                @foreach($inscrip as $i)
                    <tr>
                        @if($type == 4)
                            <td>{{$i->clave}}</td>
                            <td>{{$i->nombre}}</td>
                            <td><center>{{$i->cupo}}</center></td>
                            <td><center>{{$i->apro}}</center></td>
                            <td><center>{{$i->noapro}}</center></td>
                            <td>
                            <a href="{{ url('CoordAC/horario').'/'.$i->id_grupo }}" class="btn btn-outline-primary btn-sm" target="_blank">Imprimir</a>
                            </td>
                        @else
                            <td>{{$i->num_control}}</td>
                            <td>{{$i->estudiante}}</td>
                            <td>{{$i->semestre}}</td>
                            <td>{{$i->grupo}} {{$i->actividad}}</td>
                            @if($i->aprobada == '2' || $i->aprobada == '3')
                                <td>
                                    <a href="{{ url('CoordAC/detInscrip').'/'.$dptn->id_depto.'/'.$i->id_inscripcion }}" class="btn btn-outline-primary btn-sm">Detalle</a>
                                </td>
                            @elseif($i->aprobada == '0')
                                <td>
                                    <a href="{{ url('CoordAC/detInscrip').'/'.$dptn->id_depto.'/'.$i->id_inscripcion }}" class="btn btn-outline-primary btn-sm">Aprobar</a>
                                </td>
                            @elseif($i->aprobada == '1')
                                <td>
                                    <a href="{{ url('CoordAC/detInscrip').'/'.$dptn->id_depto.'/'.$i->id_inscripcion }}" class="btn btn-outline-primary btn-sm">Detalle</a>
                                </td>
                                <td>
                                    <a href="{{ url('CoordAC/imprimir').$i->id_inscripcion }}" class="btn btn-outline-primary btn-sm" target="_blank">Imprimir</a>
                                </td>
                            @endif
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    
        <div id="divNav" class="row">
        <div class="col">
            <label for="" class="navTotal">
                Total: {{ $inscrip->total() }}
            </label>
        </div>
        <div class="col">
            <nav class="navbar navbar-light justify-content-end">
                <ul class="pagination justify-content-end">
                    <li class="{{ ($inscrip->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $inscrip->url(1) }}" class="page-link">
                            1
                        </a>
                    </li>    
                    <li class="{{ ($inscrip->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $inscrip->previousPageUrl() }}" class="page-link">
                            <i class="bi bi-arrow-left-square"></i>
                        </a>
                    </li>    
                    <li class="{{ ($inscrip->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $inscrip->nextPageUrl() }}" class="page-link">
                            <i class="bi bi-arrow-right-square"></i>
                        </a>
                    </li> 
                    <li class="{{ ($inscrip->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $inscrip->url($inscrip->lastPage()) }}" class="page-link">
                            {{ $inscrip->lastPage() }}
                        </a>
                    </li> 
                </ul>
            </nav>
        </div>
    </div>
    <?php } ?>

    <button type="button" class="btn btn-primary d-none" data-toggle="modal" 
        data-target="#horario" id="btn_horario">
    </button>
    <div class="modal fade" id="horario"  aria-labelledby="horarioLabel" aria-hidden="true">
    </div>

</div>
@endsection