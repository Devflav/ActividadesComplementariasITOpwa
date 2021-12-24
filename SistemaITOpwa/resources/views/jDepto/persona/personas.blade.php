@extends('layouts.jDeptos')
@section('content')

    <div class="container" style="padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">
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
                <label for="" class="form-text">LISTA DEL PERSONAL REGISTRADO
                <strong>"{{ $pnom->nombre }}"</strong></label>
                <label for="" class="col-1"></label>
                <div class="input-group-append">
                    <a href="{{ url('JDepto/nuevper') }}" class="btn btn-outline-success btn-sm"> + Agregar </a>
                </div>
            </div>

            <form method="GET" action="{{url('JDepto/searchPersonal')}}">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Buscar persona por: Nombre" name="search">
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary" type="submit"> Buscar </button>
                    </div>
                </div>
            </form>
        

        <div id="divTable">
             
             <table class="table table-hover">
                 
                <tr class="text-white" style="background:#1B396A;">
                    <th width="06%">Grado</th>
                    <th width="40%">Nombre</th>
                    <th width="24%">Puesto</th>
                    <th width="20%">Curp</th>
                    <th width="10%">Opciones</th>
                </tr>

            @foreach($personas as $p)
                <tr>
                    <td>{{ $p-> grado }}</td>
                    <td>{{ $p-> empleado }}</td>
                    <td>{{ $p-> puesto }}</td>
                    <td>{{ $p-> curp }}</td>
                    <td>
                        <a href="{{ url('JDepto/editpers').$p->id_persona }}" 
                            class="btn btn-outline-primary btn-sm"><i class="fa fa-fw fa-edit"></i></a>
                    </td>
                </tr>
            @endforeach
            </table>
        </div>

        <div id="divNav" class="row">
        <div class="col">
            <label for="" class="navTotal">
                Total: {{ $personas->total() }}
            </label>
        </div>
        <div class="col">
            <nav class="navbar navbar-light justify-content-end">
                <ul class="pagination justify-content-end">
                    <li class="{{ ($personas->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $personas->url(1) }}" class="page-link">
                            1
                        </a>
                    </li>    
                    <li class="{{ ($personas->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $personas->previousPageUrl() }}" class="page-link">
                            <i class="bi bi-arrow-left-square"></i>
                        </a>
                    </li>    
                    <li class="{{ ($personas->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $personas->nextPageUrl() }}" class="page-link">
                            <i class="bi bi-arrow-right-square"></i>
                        </a>
                    </li> 
                    <li class="{{ ($personas->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $personas->url($personas->lastPage()) }}" class="page-link">
                            {{ $personas->lastPage() }}
                        </a>
                    </li> 
                </ul>
            </nav>
        </div>
    </div>

</div>

@endsection