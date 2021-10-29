@extends('layouts.estudiante')
@section('content')
<div class="container justify-content-center" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">

    <label for="carrera" style="padding-left: 10%;"> LISTA DE ACTIVIDADES OFERTADAS POR <strong>"CARRERAS"</strong>  </label>

    <div id="divTable">
            <table id="carreras" class="table table-hover">
                <thead style="background:#1B396A; width: 100%; height: 50px;">
                    <tr class="text-white">
                            <th width="80%">Carreras</th>
                            <th width="20%">Actividades</th>
                        </tr>
            </thead>
            <tbody>
                    @foreach($carreras as $c)
                        @if($c->id_carrera== null)

                        @else
                        <tr>
                            <td>{{$c->nombre}}</td>
                            <td>
                                <a href="{{ url('Est/actcar').$c->id_carrera }}" class="btn btn-outline-primary btn-sm">Ver actividades</a>
                            </td>
                        </tr>
                        @endif
                    @endforeach
            </tbody>        
                    
                </table>
            </div>

            <div id="divNav" class="row">
        <div class="col">
            <label for="" class="navTotal">
                Total: {{ $carreras->total() }}
            </label>
        </div>
        <div class="col">
            <nav class="navbar navbar-light justify-content-end">
                <ul class="pagination justify-content-end">
                    <li class="{{ ($carreras->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $carreras->url(1) }}" class="page-link">
                            1
                        </a>
                    </li>    
                    <li class="{{ ($carreras->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $carreras->previousPageUrl() }}" class="page-link">
                            <i class="bi bi-arrow-left-square"></i>
                        </a>
                    </li>    
                    <li class="{{ ($carreras->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $carreras->nextPageUrl() }}" class="page-link">
                            <i class="bi bi-arrow-right-square"></i>
                        </a>
                    </li> 
                    <li class="{{ ($carreras->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $carreras->url($carreras->lastPage()) }}" class="page-link">
                            {{ $carreras->lastPage() }}
                        </a>
                    </li> 
                </ul>
            </nav>
        </div>
    </div>
</div>

@endsection