@extends('layouts.estudiante')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 10px; padding-right: 10px; padding-bottom: 55px;">
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
	<div class="justify-content-center">
		<div class="col-sm-12">
			<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
				@foreach($historial as $h)
					<div class="card-header"> 
						<center>
							{{ $h -> periodo }}
						</center> </div>
						<div id="mitexto" class="card-body justify-content-center">
							<div  class="row">
								<div  class="form-group col-4 badge badge-info">
									<h6 for="" class="text-white">Actividad</h6>
								</div>
								<div class="form-group col-1 badge badge-info">
									<h6 for="" class="text-white">Créditos</h6>
								</div>
								<div class="form-group col-2 badge badge-info">
									<h6 for="" class="text-white">Calificación</h6>
								</div>
								<div class="form-group col-2 badge badge-info">
									<h6 for="" class="text-white">Evaluación</h6>
								</div>
								<div class="form-group col-3 badge badge-info">
									<h6 for="" class="text-white">Observaciones</h6>
								</div>
							</div> 

							<div class="row">
								<div class="form-group col-4">
									<label for="">{{ $h -> actividad }}</label>
								</div>
								<div class="form-group col-1">
									<center>
										<label for="">{{ $h -> creditos }}</label>
									</center>
								</div>
								<div class="form-group col-2">
									<center>
										<label for="">{{ $h -> calificacion }}</label>
									</center>
								</div>
								<div class="form-group col-2">
									<center>
										<label for="">{{ $h -> evaluacion }}</label>
									</center>
								</div>
								<div class="form-group col-3">
									<label for="">@if( $h->calificacion < 1)
											No aprobada
										@else
											Aprobada
										@endif	</label>
								</div>
							</div>                               
						</div>
					</div> 
				@endforeach 
			</div>
		</div>
	</div>
</div>
@endsection