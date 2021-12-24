@extends('layouts.jDeptos')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 25%; padding-right: 25%;">  
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
        <div class="text-justify">
                <div class="col">
                    <p class="text-justify">
                        @if($v == 00)
                            No puedes hacer cambios en los grupos,
                            el proceso de inscripcion ya finalizó. 
                        @elseif($v == 11)
                            No puedes hacer cambios en las actividades,
                            el proceso de inscripcion ya finalizó. 
                        @endif
                    </p>
                </div>
        </div>
    </div>
@endsection