<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Evaluacion</title>
  </head>
  <body>
    <p>ANEXO XVII.</p>
    <br>
    <h3>FORMATO DE EVALUACIÓN AL DESEMPEÑO DE LA ACTIVIDAD COMPLEMENTARIA</h3>
    <p>
      <b>Nombre del estudiante</b>:
      <span>{{$data->nombre . ' ' . $data->apePat . ' ' . $data->apeMat}}</span>
    </p>
    <p>
      <b>Actividad complementaria</b>:
      <span>{{$data->aNombre}}</span>
    </p>
    <p>
      <b>Periodo de realización </b>:
      <span>{{$data->periodo}}</span>
    </p>

    <p style="text-align: right; margin-right: 3rem">
      <b>Nivel de desempeño del criterio.</b>
    </p>
    <table style="width: 100%">
      <tr>
        <th>No.</th>
        <th>Criterios a evaluar</th>
        <th>Insuficiente</th>
        <th>Suficiente</th>
        <th>Bueno</th>
        <th>Notable</th>
        <th>Excelente</th>
      </tr>
      @foreach($criterios as $e)
      <tr>
        <td>{{$e->id_crit_eval}}</td>
        <td class='description'>{{$e->descripcion}}</td>
        @foreach($calificacion as $cal)
          @if($cal->idCrit==$e->id_crit_eval)
            @if($cal->idDes == 1)
              <td>x</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            @elseif($cal->idDes == 2)
              <td></td>
              <td>x</td>
              <td></td>
              <td></td>
              <td></td>
            @elseif($cal->idDes == 3)
              <td></td>
              <td></td>
              <td>x</td>
              <td></td>
              <td></td>
            @elseif($cal->idDes == 4)
              <td></td>
              <td></td>
              <td></td>
              <td>x</td>
              <td></td>
            @elseif($cal->idDes == 5)
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <td>x</td>
            @endif
          @endif
        @endforeach
     </tr>
      @endforeach
    </table>


    <div class="footer">
      <p>
        <b>Observaciones: </b>
        <ins
          >{{$data->observaciones}}
        </ins>
      </p>
      <p>
        <b>Valor numérico de la Actividad complementaria:</b>
        <span>{{$data->calificacion}}</span>
      </p>
      <p>
        <b>Nivel del desempeño alcanzado de la Actividad complementaria: </b
        ><span>{{$data->niv_des}}</span>
      </p>
    </div>
  </body>
  <style>
    table,
    th,
    td {
      border: 1px solid black;
      border-collapse: collapse;
    }

    td {
      text-align: center;
    }

    .description {
      text-align: left;
    }
  </style>
</html>
