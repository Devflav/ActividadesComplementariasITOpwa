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
<div class="modal fade" id="restablecer" data-backdrop="static"  aria-labelledby="restablecerLabel" 
  data-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background:#1B396A;">
        <h5 class="modal-title text-white" id="restablecerLabel">RESTABLECER USUARIO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center" style="background-color: transparent;">
      <center>INTENTAS REESTABLECER UN USUARIO <br> ¿ESTAS SEGURO DE ESTA ACCIÓN?</center>
      </div>
      <div class="modal-footer">
        <div class="form-group">
          <div class="col-sm"></div>
          <div class="col-sm">
            <a href="{{ url('/restartuser').$usuario }}" class="btn btn-outline-danger">Restablecer</a>
          </div>
          <div class="col-sm">
            <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cerar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- <script>
    $('#restart').on('shown.bs.modal', function () {
      $('#restart').trigger('focus')
    })
  </script> -->

@endsection