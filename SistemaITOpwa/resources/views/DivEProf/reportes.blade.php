@extends('layouts.divEProf')
@section('content')
<div class="container responsive" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 10%;">

    <center>
    <div class="form-gorup col-10 responsive">
        <script>
            const dataPhp = @json($data);
            const aprobados = dataPhp.filter(e => e.id_desempenio > 1);
            const reprobados = dataPhp.filter(e => e.id_desempenio == 1);

        </script>

        <form class='col' method="GET" action="{{ url('/DivEProf/reportes') }}" role='form'>
                <div class="row">
                    <div class="form-group col">
                        <label for="cars" class="float-left">Selecciona un periodo</label>
                        <select name="periodo" id="periodo" class="form-control">
                            @foreach($periodo as $p)
                                <option value="{{ $p->id_periodo }}">{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-9">
                        <label for="" class="float-left">Selecciona una actividad</label>
                        <select name="actividad" id="actividad" class="form-control">
                            @foreach($actividad as $a)
                                <option value="{{ $a->id_actividad }}">{{ $a->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-2">
                        <label for="" class="invisible"> btn </label>
                        <button type='submit' class='btn btn-primary form-control'>Generar</button>
                    </div>
                </div>
        </form>
        <!-- <center>Reporte de la actividad complementaria {{ $actividad[0] -> nombre}} del periodo {{$periodo[0]->nombre}}</center> -->
        <div>
        <center>
                Reporte de actividad complementaria <br> {{ $data[0] -> actividad ?? 'ACTIVIDAD'}} <br> {{$data[0]->periodo?? 'PERIODO'}}
        </center>

 
            <div id="chart" class="row form-control" style="height: 50%; width: 80%; background-color: transparent"></div>
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
    </center>
</div>
@endsection