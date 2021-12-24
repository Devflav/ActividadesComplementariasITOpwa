@extends('layouts.changepasswd')
@section('content')

<br>
<div class="container form-content col-sm-9">
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
	<div class="form-group">
    <div class="card-body">
      <hr>
      <div class="form-group">
          <div class="col-sm">
            <p class="text-center">
              <strong>¿QUIERES ACCEDER CÓMO PROFESOR O CÓMO JEFE DE DEPARTAMENTO?</strong>
            </p>
          </div>
      </div>
      <div class="container">
          <div class="form-group">
            <div class="col-sm"></div>
            <div class="col-sm">
                <a href="{{ url('/ProfR') }}" class="btn btn-outline-primary"> 
                    PROFESOR
                </a>
            </div>
            <br>
            <div class="col-sm">
                <a href="{{  url('/JDepto') }}" class="btn btn-outline-primary"> 
                    JEFE DEPTO. 
                </a> 
            </div>
            <div class="col-sm"></div>
          </div>
      </div>
      <hr>
    </div>
  </div>
</div> 
        
<!-- <div class="modal fade" id="selusuario"  aria-labelledby="selusuarioLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#1B396A;">
        <h5 class="modal-title text-white" id="selusuarioLabel">Seleccionar Inicio de Sesión</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center" style="background-color: transparent;">
      <center>¿QUIERES ACCEDER CÓMO PROFESOR O CÓMO JEFE DE DEPARTAMENTO?</center>
      </div>
      <div class="modal-footer">
        <a href="{{ url('/ProfR') }}" class="btn btn-outline-primary">Profesor</a>
        <a href="{{ url('/JDepto') }}" class="btn btn-outline-primary">Jefe Depto.</a>
      </div>
    </div>
  </div>
</div> -->

<script>
    $('#selusuario').on('shown.bs.modal', function () {
      $('#selusuario').trigger('focus')
    })
  </script>

@endsection