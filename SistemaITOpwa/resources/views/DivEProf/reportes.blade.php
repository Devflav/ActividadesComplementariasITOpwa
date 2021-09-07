@extends('layouts.divEProf')
@section('content')
<div class="container form-content">
    <div class="form-title">
        <h5>Gráficas de Estudiantes aprobados-Reprobados por Actividad Complementaria</h5>
    </div>
    <br>
	<div class="form-group">
		<div class="col-sm">
            <script>
                const dataPhp = @json($data);
                const aprobados = dataPhp.filter(e => e.id_desempenio > 1);
                const reprobados = dataPhp.filter(e => e.id_desempenio == 1);
            </script>   
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ url('/DivEProf/reportes') }}" role='form'>
            @csrf
            <div class="form-group">
                <div class="col-sm">
                    <label for="cars" class="float-left">Selecciona un periodo</label>
                    <select name="periodo" id="periodo" class="form-control">
                        @foreach($periodo as $p)
                            <option value="{{ $p->id_periodo }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-9">
                    <label for="" class="float-left">Selecciona una actividad</label>
                    <select name="actividad" id="actividad" class="form-control">
                        @foreach($actividad as $a)
                            <option value="{{ $a->id_actividad }}">{{ $a->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm">
                    <label for="" class="invisible"> btn </label>
                    <button type='submit' class='btn btn-primary form-control'>Generar</button>
                </div>
            </div>
        </form>
        <div class="form-group">
            <div class="col-sm text-center">
                Reporte de actividad complementaria <br> 
                {{ $data[0] -> actividad ?? 'ACTIVIDAD'}} <br> 
                {{$data[0]->periodo?? 'PERIODO'}}
            </div>
        </div>
        <div class="form-group">
            <div id="chart" class="col-sm text-center chart">
                <script>
                    const data = {
                        chart: {
                            labels: ["{{$data[0]->periodo??'PERIODO'}}"]
                        },
                        datasets: [{
                                name: 'Aprobados',
                                values: [aprobados.length]
                            },
                            {
                                name: 'Reprobados',
                                values: [reprobados.length]
                            },
                        ],
                    }
                    const chart = new Chartisan({
                        el: '#chart',
                        data,
                        hooks: new ChartisanHooks()
                            .beginAtZero()
                            .colors(),
                    })
                </script>
            </div>
        </div>
    </div>
</div>
@endsection