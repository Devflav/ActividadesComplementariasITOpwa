@extends('layouts.estudiante')
@section('content')
    <div class="container" style="background-color: transparent; padding-left: 15%; padding-right: 15%;  padding-bottom: 05%;">  
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
                <p>
                    <h5>
                    <strong> Manual de Lineamientos Académico-Administrativos del 
                    Tecnológico Nacional de México. <br> <br>
                    Planes de estudio para la formación y desarrollo de
                    competencias profesionales.</strong> <br><br>
                    </h5>
                    <h6>CAPÍTULO 10. LINEAMIENTO PARA EL CUMPLIMIENTO
                    DE ACTIVIDADES COMPLEMENTARIAS</h6>
                </p>
                <br>
                <center><p>
                    <a href="#fundamento">Fundamentos</a> 
                    <a class="text-primary"> | </a>
                    <a href="#generalidades">Generalidades</a> 
                    <a class="text-primary"> | </a>
                    <a href="#cumplimiento">Cumplimiento</a> 
                    <a class="text-primary"> | </a>
                    <a href="#pResponsable">Prof. Responsable</a> 
                    <a class="text-primary"> | </a>
                    <a href="#estudiante">Estudiante</a> 
                    <a class="text-primary"> | </a>
                    <a href="#resumen">Resumen</a> 
                </p></center>
                <div class="text-justify" style="overflow-y: scroll; height: 350px;">
                    <div class="container">
                    <hr id="fundamento" style="border-style: inset; border-width: 2px;">
                        <p >
                            <strong> Prósito</strong> <br>
                            Establecer la normativa para el cumplimiento de las actividades
                            complementarias para la formación y desarrollo de competencias 
                            profesionales de las Instituciones adscritas al TecNM, con la 
                            finalidad de fortalecer la formación integral de los estudiantes.
                            <br><br>
                            <strong> Alcance</strong> <br>
                            Se aplica a todos los Institutos, Unidades y Centros adscritos al TecNM.
                            <br><br>
                            <strong> Definición y caracterización</strong> <br>
                            Son todas aquellas actividades que realiza el estudiante en beneficio de su
                            formación integral con el objetivo de complementar su formación y desarrollo de
                            competencias profesionales.
                            Las actividades complementarias pueden ser: tutoría, actividades
                            extraescolares, proyecto de investigación, proyecto integrador, participación 
                            en eventos académicos, productividad laboral, emprendedurismo, fomento a la 
                            lectura, construcción de prototipos y desarrollo tecnológico, conservación 
                            al medio ambiente, participación en ediciones, fomento a la cultura y/o 
                            aquellas que la institución considere.
                            <br>
                        </p>
                        <hr id="generalidades" style="border-style: inset; border-width: 2px;">
                        <p >
                        <strong> Generalidades</strong> <br>
                        Cada Instituto oferta las actividades complementarias, a través de los
                        Departamentos correspondientes, de acuerdo con su Programa Institucional
                        de Innovación y Desarrollo.
                        <br>
                        Las actividades complementarias son propuestas por los Departamentos
                        involucrados ante el Comité Académico, quien asigna el número de créditos
                        y lo presenta como recomendación al (a la) Director(a) del Instituto para su
                        dictamen.
                        <br>
                        El valor curricular para el conjunto de las actividades complementarias
                        establecidas en el plan de estudios es de cinco créditos, considerando que
                        por cada crédito equivale a veinte horas efectivas y verificables, su
                        cumplimiento debe ser dentro de los seis primeros semestres.
                        <br>
                        Para cada una de las actividades complementarias autorizadas, no
                        deben de tener más de dos créditos asignados.
                        <br>
                        El Departamento de Desarrollo Académico o su equivalente en los
                        Institutos Tecnológicos Descentralizados difunde en los cursos de inducción
                        las diversas actividades complementarias autorizadas.
                        <br>
                        El (la) Jefe(a) de Departamento designa y da seguimiento al (a la)
                        profesor(a) responsable que dirige la actividad complementaria; quien
                        determina la forma de evaluar, definir la(s) evidencia(s) a satisfacer y de
                        confirmar que el estudiante adquiera las competencias necesarias para la
                        formación integral.
                        <br>
                        La División de Estudios Profesionales o su equivalente en los Institutos
                        Tecnológicos Descentralizados es la responsable de autorizar y registrar la
                        actividad complementaria al estudiante cumpliendo con lo que establece el
                        numeral 5.4.5.4.
                        <br>
                        La División de Estudios Profesionales o su equivalente en los Institutos
                        Tecnológicos Descentralizados a través de los coordinadores de carrera lleva
                        el registro de las actividades complementarias y la difusión de las mismas.
                        <br>
                        El Departamento de Servicios Escolares o su equivalente en los Institutos
                        Tecnológicos Descentralizados es el responsable de llevar el control de las
                        actividades complementarias en el expediente del estudiante, Anexo XVI.
                        <br>
                        La Lengua Extranjera no se autoriza como una actividad
                        complementaria.
                        <br>
                        </p>
                        <hr id="cumplimiento" style="border-style: inset; border-width: 2px;">
                        <p >
                        <strong> Del cumplimiento de las actividades complementarias</strong> <br>
                        Para que se cumplan las competencias de una actividad complementaria
                        es indispensable que el estudiante cubra el 100% de las evidencias y el
                        Departamento responsable de la actividad emita la constancia de
                        competencia de actividad complementaria (Anexo XVI), quien entrega
                        original al Departamento de Servicios Escolares o su equivalente en los
                        Institutos Tecnológicos Descentralizados, y copia al estudiante.
                        <br>
                        Una competencia de actividad complementaria evaluada se registra con
                        los niveles de desempeño: Excelente, Notable, Bueno o Suficiente, esto es,
                        no se asigna calificación numérica (ver Anexo XVII).
                        <br>
                        De no cumplir el estudiante con la competencia de la actividad
                        complementaria correspondiente, debe volver a solicitarla sin afectar su
                        situación académica.
                        <br>
                        Al momento de cubrir los cinco créditos de las actividades
                        complementarias establecidas en el plan de estudios, el Departamento de
                        Servicios Escolares o su equivalente en los Institutos Tecnológicos
                        Descentralizados es el responsable de realizar un promedio final con los
                        valores numéricos reportados de las actividades complementarias en el
                        Anexo XVI, y emitir la constancia de liberación de actividades
                        complementarias con el nivel de desempeño que resulte del promedio, de
                        acuerdo con la Tabla 4 del mismo anexo; dicho nivel de desempeño es el
                        que se asienta en el certificado de estudios del estudiante, esto es,
                        Actividades Complementarias-valor 5 créditos-nivel de desempeño obtenido.
                        <br>
                        </p>
                        <hr id="pResponsable" style="border-style: inset; border-width: 2px;">
                        <p >
                        <strong> Del (de la) Profesor(a) Responsable de la Actividad Complementaria</strong><br>
                        Al inicio de la actividad complementaria realiza las siguientes actividades:
                        <ul style="list-style-type:none;">
                            <li>
                                Desarrolla el proyecto de la actividad complementaria. 
                            </li>
                            <li>
                                Informa al estudiante acerca de la actividad complementaria. 
                            </li>
                            <li>
                                Realiza una evaluación diagnóstica acorde con la actividad
                                complementaria en caso de ser necesaria. 
                            </li>
                            <li>
                                Realiza una rúbrica para la evaluación del nivel de desempeño. 
                            </li>
                        </ul>
                        Durante la actividad complementaria realiza las siguientes tareas:
                        <ul style="list-style-type:none;">
                            <li>
                            Da retroalimentación continua y oportuna del avance de la actividad y de
                            las evidencias del mismo de acuerdo con lo establecido para cada actividad.
                            </li>
                        </ul>
                        Al final de la actividad realiza las siguientes tareas:
                        <ul style="list-style-type:none;">
                            <li>
                            Informa a los estudiantes del cumplimiento o no de la actividad
                            complementaria desarrollada, asignado a su criterio el nivel de desempeño
                            alcanzado de acuerdo con la rúbrica que éste realizó; los niveles de
                            desempeño son: Excelente, Notable, Bueno , Suficiente e Insuficiente.
                            </li>
                            <li>
                            Entrega al Departamento correspondiente la constancia de cumplimiento
                            de la actividad complementaria (Anexo XVI) firmada en original y dos copias,
                            quien a su vez valida la evidencia, firma y sella dicha constancia y la remite
                            al Departamento Servicios Escolares o su equivalente en los Institutos
                            Tecnológicos Descentralizados para su registro. Esto en las fechas
                            estipuladas para la entrega de calificaciones del semestre.
                            </li>
                        </ul>
                        </p>
                        <hr id="estudiante" style="border-style: inset; border-width: 2px;">
                        <p >
                        <strong> Del Estudiante</strong><br>
                        Solicita la autorización y registro para cursar alguna actividad
                        complementaria a la División de Estudios Profesionales o su equivalente en
                        los Institutos Tecnológicos Descentralizados cumpliendo lo establecido en el
                        numeral 5.4.5.4.
                        <br>
                        Puede seleccionar las actividades complementarias desde el primer
                        semestre.
                        <br>
                        Presenta las evidencias para la acreditación de la actividad
                        complementaria al (a la) profesor(a) responsable.
                        <br>
                        Debe presentarse en el lugar, fecha y hora señalada por el (la)
                        profesor(a) responsable, para desarrollar la actividad complementaria que
                        genera la evidencia, de no hacerlo, se le considera actividad complementaria
                        no acreditada.
                        <br>
                        </p>
                        <hr id="resumen" style="border-style: inset; border-width: 2px;">
                        <p >
                        <strong>Resumen</strong><br>
                        <ol>
                            <li>Publicación y difusión por parte de la División de Estudios 
                            Profesionales y demás Departamentos involucrados.</li>
                            <li>Selección de Actividades Complementarias por parte de los
                            Estudiantes en el sistema de Actividades Complementarias.</li>
                            <li>El Estudiante queda a espera de la confirmación de su 
                            inscripción en la Actividad Complementaria que escogio, será 
                            notificado al correo institucional.</li>
                            <li>Desarrollo de la Actividad Complementaria a lo largo del semestre.</li>
                            <li>El <strong>Profesor Responsable</strong> de la Actividad Complementaria será
                            el encargado de <strong>evaluar</strong> a los estudiantes y de 
                            <strong>entregar</strong> al jefe de su
                            departamento las <strong>constancias</strong> de cumplimiento y los 
                            <strong>formatos de evaluación</strong> impresos y firmados.</li>
                            <li>Cada uno de los <strong>departamentos</strong> que ofertó Actividades
                            Complementarias se encargará de sellar las <strong>constancias</strong> 
                            y los <strong>formatos de evaluación</strong> para hacer entrega de los
                            <strong>originales</strong> en <strong>Servicios Escolares</strong> y 
                            <strong>copias</strong> a los <strong>Estudiantes</strong>.</li>
                        </ol>
                        Disposiciones Generales
                        <br>
                        Las situaciones no previstas en el presente Lineamiento serán analizadas por
                        el Comité Académico del Instituto y presentadas como recomendaciones al (a la)
                        Director(a) del Instituto para su dictamen.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection