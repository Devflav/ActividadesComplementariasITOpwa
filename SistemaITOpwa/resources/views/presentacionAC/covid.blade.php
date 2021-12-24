@extends('layouts.presentacion')
@section('content')

<div class="modal fade" id="alerta" role="dialog">
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
	<div class="modal-dialog modal-lg">
		<div class=" modal-content">
			<div class="modal-header text-light" style="background:#333333">
				<h4 class="modal-title">PROTOCOLO DE PREVENCIÓN ANTE COVID-19 </h4>
			</div>
			<div class="modal-body text-center" style="background:#e5edf3">
				<a>En el TecNM campus Oaxaca estamos conscientes de la importancia de tomar medidas de prevención
					ante el COVID-19, por lo que en atención a ello, decidimos implementar un protocolo
					con tres niveles de prevención: un cuestionario de identificación de riesgos,
					medidas de prevención en nuestras instalaciones y medias de autocuidado y proximidad.</a> <br><br>
				<ul>
					<li>El cuestionario comprende las siguiente preguntas:
						<ul>
							<li>¿Tiene fiebre o la ha tenido en los últimos 14 días?</li>
							<li>¿Ha tenido problema respiratorio (incluyendo tos) en los últimos 14 días?</li>
							<li>¿Usted ha viajado o ha estado en contacto con personas que han viajado
								a países de riesgo en los últimos 14 días?</li>
							<li>¿Ha estado en contacto estrecho con personas que presentaban cuadro
								respiratorio agudo en los últimos 14 días?</li>
							<li>¿Ha estado en contacto con alguna persona con confirmación de coronavirus?</li>
						</ul>
					</li>
					<br>
					<li>Las medidas de Prevención en nuestras instalaciones:
						<ul>
							<li>hay que ponerle algo aqui...</li>
						</ul>
					</li>
					<br>
					<li>Las Medidas de Autocuidado y Proximidad son las siguientes:
						<ul>
							
							<li>primera</li>
							<li>segunda</li>
							<li>tercera ...</li>
						</ul>
					</li>
				</ul>
				<br><br>
				<center> <a>Agradecemos a toda nuestra comunidad tecnológica el seguimiento de este protocolo.</a>
					<br><br>
					<b><a>Atentamente TecNM</a></b>
					<br>
					<b><a>Instituto Tecnológico de Oaxaca	</a></b>
					<br><br><br>
					<a href="{{ url('/Noticias') }}" class="btn btn-outline-dark"> ENTENDIDO </a>
				</center>
			</div>
		</div>

	</div>
</div>
<script>
	$("#alerta").modal("show");
</script>

@endsection
