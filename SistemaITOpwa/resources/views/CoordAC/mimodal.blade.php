@extends('layouts.coordComple')
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
      <div class="modal fade" id="mimodal"  aria-labelledby="mimodalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header" style="background:#1B396A;">
            @if($modal)
              <h5 class="modal-title text-white" id="mimodalLabel"><strong>EDITAR {{$nombre}} </strong></h5>
            @else
              <h5 class="modal-title text-white" id="mimodalLabel"><strong>ELIMINAR {{$nombre}} </strong></h5>
            @endif
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body text-center" style="background-color: transparent;">
            @if($modal)
              <center>INTENTAS EDITAR UN(A) {{$nombre}} <br> ¿ESTÁS SEGURO DE ESTA ACCIÓN?</center>
            @else
              <center>INTENTAS ELIMINAR UN(A) {{$nombre}} <br> ¿ESTÁS SEGURO DE ESTA ACCIÓN?</center>
            @endif
            </div>
            <div class="modal-footer">
              <div class="form-group">
                <div class="col-sm">
                  @if($modal)
                    <a href="{{ url('').$miurl }}" class="btn btn-outline-danger">Editar</a>
                  @else
                    <a href="{{ url('').$miurl }}" class="btn btn-outline-danger">Eliminar</a>
                  @endif
                </div>
                <div class="col-sm">
                  <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cerrar</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
@endsection