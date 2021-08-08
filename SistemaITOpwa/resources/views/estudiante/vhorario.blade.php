@extends('layouts.estudiante')
@section('content')

<!-- Modal -->
<div class="modal fade" id="alerta"  aria-labelledby="alertaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="alertaLabel">Horario de grupo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="background-color: transparent;">
        <table class="table table-hover">

            <tr class="text-white" style="background:#1B396A;">
                <th width="15%"> Día </th>
                <th width="15%"> Inicio </th>
                <th width="15%"> Fin </th>
            </tr>

            @foreach($horario as $h)
            <tr>
                <td>{{$h -> dia }}</td>
                <td>{{$h -> hi }}</td>
                <td>{{$h -> hf }}</td>
            </tr>
            @endforeach

        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

  <script>
    $('#alerta').on('shown.bs.modal', function () {
      $('#alerta').trigger('focus')
    })
  </script>

@endsection
