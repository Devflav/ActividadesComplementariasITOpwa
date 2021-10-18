@extends('layouts.coordComple')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 10%;">

    <form method="GET" action="{{ url('/searchusu')}}">
        <label for="" class="form-text">LISTA DE LOS USUARIOS REGISTRADOS EN EL SISTEMA</label>
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Buscar usuario por: Nombre, #Control o Curp" name="search" required>
            <div class="input-group-append">
                <button class="btn btn-outline-primary" type="submit"><i class="fa fa-fw fa-search"></i> Buscar </button>
            </div>
        </div>
    </form>

    <div id="divTable">
        <table class="table table-hover table-responsive">
                 <thead style="background:#1B396A;">
                    <tr class="text-white">
                        <th width="10%">Tipo</th>
                        <th width="20%">Nombre</th>
                        <th width="20%">A. Paterno</th>
                        <th width="20%">A. Materno</th>
                        <th width="20%">Usuario</th>
                        <th width="10%">Restablecer</th>
                    </tr>
                </thead>
                <tbody style="width: 100%">
            @foreach($persona as $p)
                <tr>
                    <td>{{$p->tipo}}</td>
                    <td>{{$p->nombre}}</td>
                    <td>{{$p->apePat}}</td>
                    <td>{{$p->apeMat}}</td>
                    <td>{{$p->usuario}}</td>
                    <td>
                        <center>
                            <a class="btn btn-outline-primary btn-sm text-primary" onclick="usurest('{{ url('CoordAC/usuariorestart').$p->id_persona }}')"><i class="fa fa-fw fa-history"></i></a>
                        </center> 
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <nav class="navbar navbar-light justify-content-end">
        <ul class="pagination justify-content-end">
            @if($persona->previousPageUrl() != null)
                <li class="page-item">
                    <a href="{{ $persona->previousPageUrl() }}" class="page-link m-1">
                        Anterior
                    </a>
                </li>
            @else
                <li class="page-item disabled"> 
                    <a href="" class="page-link m-1"> Anterior </a>
                </li>
            @endif
            @if($persona->nextPageUrl() != null)
                <li class="page-item">
                    <a href="{{ $persona->nextPageUrl() }}" class="page-link m-1">
                        Siguiente
                    </a>
                </li>
            @else
            <li class="page-item disabled"> 
                    <a href="" class="page-link m-1"> Siguiente </a>
                </li>
            @endif
        </ul>
    </nav>
</div>

<script>
    const usurest = (_url) =>{
        $.ajax(_url) .done(function(respuesta) {
            let _html = $(respuesta).find("#restablecer").html();
            $("#restablecer").html(_html);
            $("#btn_restablecer").click()
        }).fail(function(error) {
            console.log('Error', error);
        });
    }
</script>

@endsection