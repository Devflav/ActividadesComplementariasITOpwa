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
<button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#horario" id="btn_horario">
        </button>

<div class="modal fade" id="horario" data-backdrop="static" aria-labelledby="horarioLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header" style="background:#1B396A;">
              <h5 class="modal-title text-white" id="horarioLabel"><strong>REIMPRESIÓN DE HORARIO GRUPAL </strong></h5>
              <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button> -->
            </div>
            <div class="modal-body text-center" style="background-color: transparent;">
              <center>LOS HORARIOS DE ESTE GRUPO YA FUERON IMPRESOS, 
              <br> ¿QUIERES VOLVER A IMPRIMIRLOS?</center>
            </div>
            <div class="modal-footer">
                <a href="{{ url('CoordAC/reimprimir_grupo').'/'.$grupo }}" class="btn btn-outline-danger" target="_blank">Imprimir</a>
                <label for="" class="col-1"></label>
                <a href="{{ URL::previous() }}" class="btn btn-outline-primary">Cancelar</a>
            </div>
          </div>
        </div>
      </div>

      <script>
        $(document).ready(function()
        {
          $("#horario").modal("show");
        });
      </script>
@endsection