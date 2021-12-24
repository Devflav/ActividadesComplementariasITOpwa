@extends('layouts.divEProf')
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
    <label for="carrera"> LISTA DE ACTIVIDADES OFERTADAS POR <strong>"DEPARTAMENTO"</strong>  </label>
        
    <div id="divTable">
        <table id="carreras" class="table table-hover">
            <thead style="background:#1B396A; width: 100%; height: 50px;">
                <tr class="text-white">
                    <th width="70%">Departamentos</th>
                    <th width="30%">Actividades</th>
                </tr>
            </thead>
            <tbody>
            @foreach($deptos as $d)
                <tr>
                    <td>{{ $d -> nombre }}</td>
                    <td>
                    <center>
                        <a href="{{ url('CoordAC/actdep').'/'.$d->id_depto.'/1' }}" 
                            class="btn btn-outline-primary btn-sm">
                            Ver actividades
                        </a>
                    </center>
                    </td>
                </tr>
            @endforeach
            </tbody>  
        </table>
    </div>
    <div id="divNav" class="row">
        <div class="col">
            <label for="" class="navTotal">
                Total: {{ $deptos->total() }}
            </label>
        </div>
        <div class="col">
            <nav class="navbar navbar-light justify-content-end">
                <ul class="pagination justify-content-end">
                    <li class="{{ ($deptos->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $deptos->url(1) }}" class="page-link">
                            1
                        </a>
                    </li>    
                    <li class="{{ ($deptos->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $deptos->previousPageUrl() }}" class="page-link">
                            <i class="bi bi-arrow-left-square"></i>
                        </a>
                    </li>    
                    <li class="{{ ($deptos->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $deptos->nextPageUrl() }}" class="page-link">
                            <i class="bi bi-arrow-right-square"></i>
                        </a>
                    </li> 
                    <li class="{{ ($deptos->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                        <a href="{{ $deptos->url($deptos->lastPage()) }}" class="page-link">
                            {{ $deptos->lastPage() }}
                        </a>
                    </li> 
                </ul>
            </nav>
        </div>
    </div>
</div>

@endsection