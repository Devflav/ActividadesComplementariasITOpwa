@extends('layouts.changepasswd')
@section('content')

<br>
<center>
      <hr class="col-8">
      <center> <strong>¿QUIERES ACCEDER CÓMO PROFESOR O CÓMO JEFE DE DEPARTAMENTO?</strong> <br><br>
        <a href="{{ url('/ProfR') }}" class="btn btn-outline-primary">PROFESOR</a>
        <label class="col-1"></label>
        <a href="{{ url('/JDepto') }}" class="btn btn-outline-primary">JEFE DEPTO</a>
      </center>
      <hr class="col-8">
</center>
        
<!-- <div class="modal fade" id="selusuario"  aria-labelledby="selusuarioLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#1B396A;">
        <h5 class="modal-title text-white" id="selusuarioLabel">Seleccionar Inicio de Sesión</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="background-color: transparent;">
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