@extends('layouts.jDeptos')
@section('content')

	<div class="container" style="background-color: transparent; padding-left: 05%; padding-right: 05%; padding-bottom: 05%;">
		
        <div class="row justify-content-center">
           
        </div>
        
        <div class="row justify-content-center">
			<div class="col-md-9">
				<div class="card border-bottom-0 border-left-0 border-right-0" style="background-color: transparent;">
					<div class="card-header text-white" style="background:#1B396A;"> Cambiar o Guardar Hoja membretada </div>
					<div class="card-body">
					
						<form method="POST" action="{{url('/dpt/savehmem') }}" class="needs-validation" enctype="multipart/form-data">
							@csrf
                            <div class="row">
								<div class="form-group col">
									@foreach($hoja as $h)
										@if($h->hoja_mem == null)
											<label for=""> No hay una hoja membretada registrada.</label>
										@else
											<label for="nControl">Hoja membretada actual:</label>
											<button type="button" class="btn btn-sm btn-outline-info form-control" onclick="fx_show('/storage/{{ $h -> hoja_mem }}', 780)">
												<i class="fa fa-lg fa-file-pdf-o"></i>
												Ver hoja membretada
											</button>
										@endif
									@endforeach
								</div>

								<div class="form-group col">
									<label for="nControl">Nueva hoja membretada:</label>
									<div class="custom-file col">
										<input type="file" class="custom-file-input" id="customFile" name="hojamem" accept="application/pdf" required>
										<label class="custom-file-label" for="customFile">Selecciona el archivo</label>
									</div>
								</div>
                            </div>
							<br>
							<center>
								<button type="submit" class="btn btn-outline-primary"> Registrar </button>
                            	<a href="{{ url('JDepto') }}" class="btn btn-outline-danger"> Cancelar </a> 
							</center>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	
@endsection

