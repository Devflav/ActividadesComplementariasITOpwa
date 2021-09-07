@extends('layouts.coordComple')
@section('content')

<div class="modal fade" id="restablecer"  aria-labelledby="restablecerLabel" aria-hidden="true">
  <div class="modal-dialog">
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
        <a href="{{ url('/restartuser').$usuario }}" class="btn btn-outline-danger">Restablecer</a>
        <label for="" class="col-1"></label>
        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cerar</button>
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