<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <style>
        @font-face {
            font-family: 'Montserrat';
            font-style: normal;
            font-weight: normal;
            src: url({{ url('/fonts/Montserrat-Regular.ttf') }}) format('truetype');
        }
    </style>
</head>

<body>
    <div class="header-images">
        <div class="logo-right">
            <img src="{{ url('/images/tec_nm.jpeg') }}"
            height='60px'
            >
        </div>
        <h3 class='constancia'>CONSTANCIA DE CUMPLIMIENTO DE ACTIVIDAD COMPLEMENTARIA</h3>
        <h5 class='tecDep'>Instituto Tecnológico de Oaxaca <br> {{ $data->depto }}</h5>
        <div class="header">
            <h4>{{$jefeDpto->grado . ' ' . $jefeDpto->nombre . ' ' . $jefeDpto->apePat . ' ' . $jefeDpto->apeMat}}</h4>
            <h4>Jefe del Departamento de Servicios Escolares</h4>
            <h4>PRESENTE</h4>
        </div>
        <div class="content">
            El(la) que suscribe
            <span class="text-content">{{ $profesor->grado . ' ' . $profesor->nombre . ' ' . $profesor->apePat . ' ' . $profesor->apeMat }}</span> por este
            medio se permite hacer de su conocimiento que el(la) estudiante
            <span
                class="text-content">{{ $data->nombre . ' ' . $data->apePat . ' ' . $data->apeMat }}
            </span> con número
            de control <span class="text-content">{{ $data->num_control }}</span> de la carrera de
            <span class="text-content">{{ $data->carrera }}</span> ha cumplido su
            actividad complementaria con el nivel de desempeño
            <span class="text-content">{{ $data->niv_des }}</span> y un valor numérico de
            <span class="text-content">{{ $data->calificacion }}</span>, durante el periodo escolar
            {{ $data->periodo }} con un valor curricular de
            <span class="text-content">{{ $data->creditos }}</span> créditos.
        </div>
        <br />
        <div class="bottom">
            <p>
                Se extiende la presente en la Ciudad de Oaxaca a los {{ $day }} dias de {{ $month }} de
                {{ $year }}.
            </p>
            <br>
            <br>
            <br>
            <div class="atentamente">
                <p class='atentamente-title'>ATENTAMENTE</p>
                <div class="column">
                    <p class='mb'>.</p>
                    <p>_________________________________</p>
                    <p>{{ $profesor->grado . ' ' . $profesor->nombre . ' ' . $profesor->apePat . ' ' . $profesor->apeMat }}</p>
                    <p>Nombre y firma del profesor responsable</p>
                </div>
                <div class="column" style='margin-left: 52%'>
                    <p class='mb'>.</p>
                    <p>_________________________________</p>
                    <p>{{$jefe->grado . ' ' . $jefe->nombre . ' ' . $jefe->apePat . ' ' . $jefe->apeMat}}</p>
                    <p>Vo. Bo. del Jefe(a) del {{ $data->depto }}</p>
                </div>
            </div>
            <p class="copia" style='text-align: left; margin-top: -250px;'>
                C.c.p. Jefe(a) de Departamento correspondiente.
                <br>
                C.c.p. Coordinador(a) de Actividades Complementarias.
                <br>
                C.c.p. Interesado.
            </p>
        </div>
        <footer>
            <div class="footer-wrapper">
                <div class="logo-left">
                    <img 
                    src="{{ url('/images/ito.jpeg') }}"
                        alt="logo tec" height="50px" style="margin: 0px 30px 0px 30px;" />
                </div>
                <div class="footer-content">
                    <p>Avenida Ing. Víctor Bravo Ahuja No. 125 Esquina Calzada</p>
                    <p>Tecnológico, C.P. 68030</p>
                    <p>Oaxaca, Oax. Tel. (951) 501 50 16</p>
                    <p>e-mail: tec_oax@itoaxaca.edu.mx</p>
                    <p class="bold">tecnm.mx | oaxaca.tecnm.mx</p>
                </div>
                <div class="logo-right">
                    <img src="{{ url('/images/logo_mexico.jpeg') }}" height='120px'>
                </div>
            </div>
        </footer>
</body>


<style>
    .copia{
        font-size: 10px;
    }
    body {
        margin: 0 1rem 0 1rem;
    }

    .tecDep {
        font-size: 10px;
        text-transform: uppercase;
        text-align: right;
    }
    .constancia{
        margin-bottom: 20px;
        font-weight: bold;
        font-size: 17px;
        display: flex;
        justify-content: center;
    }

    .header {
        margin-bottom: 20px;
        font-weight: bold;
        font-size: 17px;
    }

    .content {
        text-align: justify;
    }

    .content, .bottom {
        font-size: 15px;
    }

    .text-content {
        font-weight: bold;
    }

    body {
        font-family: 'Montserrat' !important;
    }

    h4 {
        padding: 0px;
        margin: 0px;
    }

    footer {
        position: fixed;
        bottom: -100px;
        left: 0px;
        right: 0px;
        height: 200px;
    }

    .atentamente {
        display: flex;
        justify-content: center;
    }

    .atentamente .column {
        width: 40%;
        text-align: center;
        margin-left: 15%;
    }

    .atentamente .column .name{
        font-size: 12px;
    }

    .atentamente .column .mb {
        margin-bottom: 60px;
        color: white;
    }

    .footer-wrapper {
        color: gray;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    footer .bold {
        font-weight: bold;
    }

    .atentamente-title {
        text-align: center;
    }

    footer .logo-left {
        margin-right: 600px;
    }

    footer .logo-right {
        margin-left: 600px;
    }

</style>

</html>
