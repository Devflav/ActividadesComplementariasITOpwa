@extends('layouts.presentacion')
@section('content')
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
    <center>
        <h5><br>
            <em> Ups... lo sentimos, contenido no disponible.</em><br>
            <em> Nuestro personal se encuentra trabajando en ello.</em><br>
            <em> Disculpe las molestias.</em>
        </h5>
    </center>
@endsection