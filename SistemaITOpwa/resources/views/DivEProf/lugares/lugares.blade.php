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
<div class="input-group mb-3">
    <label for="" class="form-text">LISTA DE LOS LUGARES REGISTRADOS</label>
    <label for="" class="col-1"></label>
    <div class="input-group-append">
        <a href="{{ url('DivEProf/nuevoLugar') }}" class="btn btn-outline-success btn-sm"><i class="fa fa-fw fa-plus"></i> Agregar </a>
    </div>
</div>

<form method="GET" action="{{url('DivEProf/searchlug')}}">
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Buscar lugar por Nombre" name="search" required>
        <div class="input-group-append">
            <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
        </div>
    </div>
</form>

<div id="divTable">
    <table id="carreras" class="table table-hover">
        <thead style="background:#1B396A; width: 100%; height: 50px;">
            <tr class="text-white">
                    <th width="10%">#</th>
                    <th width="90%">Lugar</th>
            </tr>
        </thead>
        <tbody style="width: 100%">
            <?php $num = (($lugares->currentPage() -1 )*10) + 1; ?> 
        @foreach($lugares as $l)
            <tr>
                <td width="10%">{{$num}}</td>
                <td width="70%">{{$l->nombre}}</td>
            </tr>
            <?php $num++; ?>
        @endforeach
        </tbody>
    </table>
</div>

<div id="divNav" class="row">
    <div class="col">
        <label for="" class="navTotal">
            Total: {{ $lugares->total() }}
        </label>
    </div>
    <div class="col">
        <nav class="navbar navbar-light justify-content-end">
            <ul class="pagination justify-content-end">
                <li class="{{ ($lugares->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                    <a href="{{ $lugares->url(1) }}" class="page-link">
                        1
                    </a>
                </li>    
                <li class="{{ ($lugares->previousPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                    <a href="{{ $lugares->previousPageUrl() }}" class="page-link">
                        <i class="bi bi-arrow-left-square"></i>
                    </a>
                </li>    
                <li class="{{ ($lugares->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                    <a href="{{ $lugares->nextPageUrl() }}" class="page-link">
                        <i class="bi bi-arrow-right-square"></i>
                    </a>
                </li> 
                <li class="{{ ($lugares->nextPageUrl() == null) ? 'page-item disabled' : 'page-item' }}">
                    <a href="{{ $lugares->url($lugares->lastPage()) }}" class="page-link">
                        {{ $lugares->lastPage() }}
                    </a>
                </li> 
            </ul>
        </nav>
    </div>
</div>

<button type="button" class="btn btn-primary d-none" data-toggle="modal" 
    data-target="#mimodal" id="btn_mimodal">
</button>
<div class="modal fade" id="mimodal" data-backdrop="static" data-keyboard="false" 
    tabindex="-1" aria-labelledby="restablecerLabel" aria-hidden="true">
</div>

</div>
@endsection