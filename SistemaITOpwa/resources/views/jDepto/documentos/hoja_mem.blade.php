@extends('layouts.jDeptos')
@section('content')
<div class="container form-content col-sm-9">
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
	<div class="form-group">
		<div class="col-sm">
			<div class="card-header"> Cambiar o Guardar Hoja membretada </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('/dpt/savehmem') }}" class="needs-validation" 
            enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <div class="col-sm">
				@foreach($hoja as $h)
					@if($h->hoja_mem == null)
						<label for=""> No hay una hoja membretada registrada.</label>
					@else
						<label for="nControl">Hoja membretada actual:</label>
						<button type="button" class="btn btn-sm btn-outline-info form-control" 
							onclick="fx_show('/storage/{{ $h->hoja_mem }}', 780)">
							<i class="fa fa-lg fa-file-pdf-o"></i>
							Ver hoja membretada
						</button>
					@endif
				@endforeach
                </div>
                <div class="col-sm">
					<label for="nControl">Nueva hoja membretada:</label>
					<div class="custom-file col">
						<input type="file" class="custom-file-input" id="customFile" name="hojamem" 
							accept="application/pdf" required>
						<label class="custom-file-label" for="customFile">Selecciona el archivo</label>
					</div>
                </div>
            </div>
            <div class="container">
                <div class="form-group">
                    <div class="col-sm"></div>
                    <div class="col-sm">
                        <button type="submit" class="btn btn-outline-primary"> 
                            Registrar
                        </button>
                    </div>
                    <br>
                    <div class="col-sm">
                        <a href="{{ url('JDepto') }}" class="btn btn-outline-danger"> 
                            Cancelar 
                        </a> 
                    </div>
                    <div class="col-sm"></div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

