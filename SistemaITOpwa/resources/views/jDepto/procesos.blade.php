@extends('layouts.jDeptos')
@section('content')
<div class="container" style="background-color: transparent; padding-left: 25%; padding-right: 25%;">  
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