<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

// se declara modelos (bd)
use App\Models\Musers;              use App\Models\Mgrado;
use App\Models\Mgrupo;              use App\Models\Mpuesto;
use App\Models\Mpersona;            use App\Models\Mperiodo;
use App\Models\Mempleado;           use App\Models\Mcarreras;
use App\Models\Meval_valor;         use App\Models\Mestudiante;
use App\Models\Mevaluacion;         use App\Models\Mdepartamento;
use App\Models\Mcriterios_evaluacion;
use App\Models\Mnivel_desempenio; 
use DB;         use Auth;       use mysql_query;

class PResponsableController extends Controller
{
    /**Constructor del controlador */
    public function _construct() {  $this->middleware('profesorr'); }
    
    /**Redirecciona a la pagina de inicio de sesión de este usuario */
    public function f_inicio() { 
        
        $now = date_create('America/Mexico_City')->format('H');

        $today = date("Y-m-d");
        $dates = Mperiodo::where('estado', "Actual")
            ->first();

        $processes = 00;    $endprocess = 00;

        if($today >= $dates->ini_inscripcion && $today <= $dates->fin_inscripcion){
            $processes = 01;
            $endprocess = $dates->fin_inscripcion;
        
        } elseif($today >= $dates->ini_evaluacion && $today <= $dates->fin_evaluacion){
            $processes = 10;
            $endprocess = $dates->fin_evaluacion;
        
        } elseif($today >= $dates->ini_gconstancias && $today <= $dates->fin_gconstancias){
            $processes = 11;
            $endprocess = $dates->fin_gconstancias;
        }

        return view('ProfRes.inicio')  
        ->with('hora', $now)
        ->with('process', $processes)
        ->with('end', $endprocess);
    }

/**Retorna a la vista del listado de grupos asignados a este usuario, grupos 
 * pertenecientes al periodo "Actual"
 */
    public function f_grupos(Request $request) {

        $id_per = $request->user()->id_persona;

        $grupos = DB::table('grupo as g')
        ->join('periodo as pe', 'g.id_periodo', '=', 'pe.id_periodo')
        ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
        ->join('persona as p', 'g.id_persona', '=', 'p.id_persona')
        ->join('lugar as l', 'g.id_lugar', '=', 'l.id_lugar')
        ->select(
            'g.id_grupo as id_grupo',
            'g.clave as clave',
            'a.nombre as actividad',
            'l.nombre as lugar',
            'g.asistencias as asistencias',
            'g.cupo as cupo',
            'a.creditos as creditos'
        )
        ->where('pe.estado', "Actual")
        ->where('g.estado', 1)
        ->where('p.id_persona', $id_per)
        ->get();

        /*'p.nombre as nombreP', 'p.apePat as paterno', 'p.apeMat as materno',*/
        
        return view('ProfRes.grupos')
        ->with('grupos', $grupos)
        ->with('tipo', "Ver");  
    }
/**Retorna a la vista del listado de grupos de ese departamento pero con
 * la busqueda del grupo recibido en el parametro, grupos asignados a
 * este usuario
 */
    public function f_gruposB($search, Request $request) {
 
        $data = ["%".mb_strtoupper($search)."%", $request->user()->id_persona];
        $tipo = "Ver";

        $grupos = DB::table('grupo AS g')
            ->leftJoin('periodo AS p', 'g.id_periodo', '=', 'p.id_periodo')
            ->leftJoin('actividad AS a', 'g.id_actividad', '=', 'a.id_actividad')
            ->leftJoin('persona AS pe', 'g.id_persona', '=', 'pe.id_persona')
            ->leftJoin('lugar AS l', 'g.id_lugar', '=', 'l.id_lugar')
            ->select('g.id_grupo', 
                    'g.cupo', 
                    'g.clave', 
                    'g.asistencias', 
                    'a.nombre AS actividad', 
                    'l.nombre AS lugar',
                    'a.creditos',
                    DB::raw('CONCAT(pe.nombre, " ", pe.apePat, " ", pe.apeMat) AS responsable'))
            ->when($data, function ($query, $data) {
                return $query->where('p.estado', "Actual")
                            ->where('g.estado', 1)
                            ->where('pe.id_persona', $data[1])
                            ->where('g.clave', 'LIKE', $data[0])
                            ->orWhere('a.nombre', 'LIKE', $data[0]);
            })
            ->orderBy('g.id_grupo')
            ->paginate(10);


        return view('ProfRes.grupos')
        ->with('grupos', $grupos)
        ->with('tipo', $tipo);  
    }
/**Realiza el request del buscador de grupos y redirige a la función
 * f_gruposB()
 */
    public function f_searchgru(Request $request) { 
        
        
        $search = $request->search;
        //return $this->f_gruposB($search);
        return redirect()->to('/ProfR/grupos/'.$search);
    }
/**Retorna a un modal donde muestra el horario del grupo seleccionado */    
    public function f_vhorario($id_gru){

        $horario = DB::table('horario as h')
        ->join('dias_semana as ds', 'h.id_dia', '=', 'ds.id_dia')
        ->select('ds.nombre as dia', 
            'h.hora_inicio as hi', 
            'h.hora_fin as hf')
        ->where('h.id_grupo', $id_gru)
        ->get();
        
        return view('ProfRes.vhorario')
        ->with('horario', $horario);
    }
/**Retorna a la vista del listado de grupos asignados a este usuario, donde
 * le permite seleccionar uno para realizar su evaluación
 */ 
    public function f_evaluar(Request $request, $origin) {

        $now = date('Y-m-d');
        $modificar = true;

        if(substr_compare($origin, "evaluar", 0) == 0){

            $roll = Mperiodo::select('ini_evaluacion', 'fin_evaluacion')
                ->where('estado', "Actual")->first();
            if($now < $roll->ini_evaluacion || $now > $roll->fin_evaluacion)
                $modificar = false;
        }

        if(substr_compare($origin, "constancia", 0) == 0){
            
            $roll = Mperiodo::select('ini_gconstancias', 'fin_gconstancias')
                ->where('estado', "Actual")->first();
            if($now < $roll->ini_gconstancias || $now > $roll->fin_gconstancias)
                $modificar = false;
        }

        $id_per = $request->user()->id_persona;

        $grupos = DB::table('grupo as g')
            ->join('periodo as pe', 'g.id_periodo', '=', 'pe.id_periodo')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('persona as p', 'g.id_persona', '=', 'p.id_persona')
            ->join('lugar as l', 'g.id_lugar', '=', 'l.id_lugar')
            ->select(
                'g.id_grupo as id_grupo',
                'g.clave as clave',
                'a.nombre as actividad',
                'l.nombre as lugar',
                'g.asistencias as asistencias',
                'g.cupo as cupo',
                'a.creditos as creditos'
            )
            ->where('g.id_persona', $id_per)
            ->get();
        
        return view('ProfRes.grupos')
        ->with('grupos', $grupos)
        ->with('tipo', $origin)
        ->with('mod', true);  
    }
/**Esta función se encarga de registrar en la base de datos la evaluación realizada */
    public function f_save_eval(Request $request) {

        $now = date_create('America/Mexico_City')->format('H');

        $num_control = $request->input('n_control');
        $grupo = $request->input('id_grupo');

        $student = Mestudiante::where('num_control' ,$num_control)
            ->first();

        $alumno = DB::table('inscripcion as i')
                    ->join('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
                    ->select('i.id_inscripcion as id')
                    ->where('e.num_control', '=', $num_control)
                    ->where('i.id_grupo', $grupo)
                    ->where('i.aprobada', 1)
                    ->get();

        $evaluacion = new Mevaluacion();
        $evaluacion->id_desempenio = $request->input('idDesempenio');
        $evaluacion->id_inscripcion = $alumno[0]->id;
        $evaluacion->asistencias = $request->input('asistencias');
        $evaluacion->calificacion = $request->input('calificacion');
        $evaluacion->observaciones = $request->input('observaciones');
        $evaluacion->constancia = '';
        try {
            $evaluacion->save();

            for ($i = 1; $i <= 7; $i++){
                $evalValor = new Meval_valor();
                $evalValor->id_evaluacion = $evaluacion->id;
                $evalValor->id_crit_eval = $i;
                $evalValor->id_desempenio = $request->input($i)+1;
                $evalValor->save();
            }

            return redirect('ProfR/lista'.$grupo.'/evaluar')
                        ->with('successEval', True);
        } catch (Exception $e) {
            return redirect('ProfR/lista'.$grupo.'/evaluar')
                        ->with('successEval', False);
        }

    }
/**Retorna la lista de estudiantes de un grupo, el cual ya fue evaluado */
    public function f_evaluation_list($id_gru)    {
        
        $grupo = DB::table('grupo as g')
            ->join('persona as p', 'g.id_persona', '=', 'p.id_persona')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->select(
                'g.clave as clave',
                'a.nombre as actividad',
                'p.nombre as nombre',
                'p.apePat as paterno',
                'p.apeMat as materno',
                'g.id_grupo as id_grupo'
            )
            ->where('g.id_grupo', $id_gru)
            ->get();

        $alumnosGrupo = DB::table('persona as p')
            ->join('estudiante as e', 'p.id_persona', '=', 'e.id_persona')
            ->join(
                'inscripcion as i',
                'e.id_estudiante',
                '=',
                'i.id_estudiante'
            )
            ->select(
                'p.nombre as nombre',
                'e.num_control as num_control',
                'p.apePat as apePat',
                'p.apeMat as apeMat',
                'i.id_grupo as id_grupo'
            )
            ->where('i.id_grupo', $id_gru)
            ->get();

        return view('ProfRes.lista')
            ->with('grupo', $grupo)
            ->with('alumnos', $alumnosGrupo)
            ->with('tipo', 'evaluar');
    }
/**Retorna la vista del listado del grupo recibido como parametro */
    public function f_lista($id_gru, $origin)    {
        
        $grupo = DB::table('grupo as g')
            ->join('persona as p', 'g.id_persona', '=', 'p.id_persona')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->select(
                'g.clave as clave',
                'a.nombre as actividad',
                'p.nombre as nombre',
                'p.apePat as paterno',
                'p.apeMat as materno',
                'g.id_grupo as id_grupo',
                'g.asistencias as asistencias'
            )
            ->where('g.id_grupo', $id_gru)
            ->get();

        $alumnosGrupo = DB::table('persona as p')
            ->join('estudiante as e', 'p.id_persona', '=', 'e.id_persona')
            ->join('inscripcion as i', 'e.id_estudiante', '=', 'i.id_estudiante')
            ->leftJoin('evaluacion as ev', 'i.id_inscripcion', '=', 'ev.id_inscripcion')
            ->leftJoin('nivel_desempenio as nd', 'nd.id_desempenio', '=', 'ev.id_desempenio')
            ->select(
                'p.nombre',
                'e.num_control',
                'p.apePat',
                'p.apeMat',
                'i.id_grupo',
                'ev.id_evaluacion as id_eval',
                'nd.nombre as nivel_desempenio'
            )
            ->where('i.id_grupo', $id_gru)
            ->where('i.aprobada', 1)
            ->orderBy('p.apePat', 'asc')
            ->paginate(10);

        return view('ProfRes.lista')
            ->with('grupo', $grupo)
            ->with('alumnos', $alumnosGrupo)
            ->with('tipo', $origin);
    }
/**Esta función genera y descarga la constancia de culminación de actividad
 * complementaria del estudiante que recibe como parametro
 */
    public function downloadConstancia($n_control) {

        $data = DB::table('estudiante as e')
            ->join('persona as p', 'p.id_persona', '=', 'e.id_persona')
            ->join('carrera as c', 'c.id_carrera', '=', 'e.id_carrera')
            ->join('inscripcion as i', 'e.id_estudiante', '=', 'i.id_estudiante')
            ->join('evaluacion as ev', 'i.id_inscripcion', '=', 'ev.id_inscripcion')
            ->join('nivel_desempenio as nd', 'ev.id_desempenio', '=', 'nd.id_desempenio')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('departamento as d', 'd.id_depto', '=', 'a.id_depto')
            ->join('periodo as pr', 'g.id_periodo', '=', 'pr.id_periodo')
            ->select('p.nombre as nombre', 'e.num_control as num_control',
                    'p.apePat as apePat', 'p.apeMat as apeMat',
                    'c.nombre as carrera', 'nd.nombre as niv_des',
                    'ev.calificacion as calificacion',
                    'ev.observaciones as observaciones',
                    'd.id_persona as jefeId',
                    'd.nombre as depto', 'a.creditos as creditos',
                    'pr.nombre as periodo')
            ->where('e.num_control', '=', $n_control)
            ->first();
        
        $jefeDpto = DB::table('persona as p')
                    ->join('departamento as d', 'd.id_persona', '=', 'p.id_persona')
                    ->join('empleado as e', 'e.id_persona', '=', 'p.id_persona')
                    ->join('grado as g', 'g.id_grado', '=', 'e.id_grado')
                    ->where('d.id_persona', '=', $data->jefeId)
                    ->select('p.nombre as nombre', 'p.apePat as apePat',
                             'p.apeMat as apeMat', 'g.nombre as grado')
                    ->first();

        $jefe = DB::table('departamento as dp')
								->join('persona as p', 'p.id_persona', '=', 'dp.id_persona')
                ->join('empleado as e', 'e.id_persona', '=', 'p.id_persona')
                ->join('grado as g', 'g.id_grado', '=', 'e.id_grado')
                ->where('dp.id_depto', '=', 12)
                ->select('p.nombre as nombre',
                         'p.apePat as apePat',
                         'p.apeMat as apeMat', 'g.nombre as grado')
                ->first();

        $profesor = DB::table('persona as p')
                ->join('empleado as e', 'e.id_persona', '=', 'p.id_persona')
                ->join('grado as g', 'g.id_grado', '=', 'e.id_grado')
                ->where('p.id_persona', '=', Auth::user()-> id_persona)
                ->select('p.nombre as nombre',
                         'p.apePat as apePat',
                         'p.apeMat as apeMat', 'g.nombre as grado')
                ->first();

        $images = Mperiodo::select('cabecera', 'pie')
            ->where('estado', "Actual")
            ->first();

        $images->cabecera = substr($images->cabecera, 1);
        $images->pie = substr($images->pie, 1);
        
        $now = date_create('America/Mexico_City');
        
        $monthNumber = $now->format('m'); 
        $months = array (1=>'Enero',2=>'Febrero',
                        3=>'Marzo',4=>'Abril',5=>'Mayo',
                        6=>'Junio',7=>'Julio',8=>'Agosto',
                        9=>'Septiembre',10=>'Octubre',11=>'Noviembre',
                        12=>'Diciembre');

        $month = $months[(int)$monthNumber];
    
        $pdf = App::make('dompdf.wrapper');
        $pdf = app('dompdf.wrapper');
        $content = "<head>
                        <meta charset='UTF-8' />
                        <style>
                            @font-face {
                                font-family: 'Montserrat';
                                font-style: normal;
                                font-weight: normal;
                            }
                        </style>
                    </head>";

        $content .= "<body'
                        <div class='header-images'>
                            <div class='logo-right'>
                                <img src=".$images->cabecera." height='150px'>
                            </div>
                                <br>
                                <br>
                                <h3 class='constancia'>CONSTANCIA DE CUMPLIMIENTO DE ACTIVIDAD COMPLEMENTARIA</h3>
                                <br>
                                <br>
                                <br>
                                <br>
                                <div class='header'>
                                <h4>".$jefeDpto->grado . ' ' . $jefeDpto->nombre . ' ' . $jefeDpto->apePat . ' ' . $jefeDpto->apeMat."</h4>
                                <h4>Jefe del Departamento de Servicios Escolares</h4>
                                <h4>PRESENTE</h4>
                            </div>
                            <div class='content'>
                                El(la) que suscribe
                                <span class='text-content'>". $profesor->grado . ' ' . $profesor->nombre . ' ' . $profesor->apePat . ' ' . $profesor->apeMat ."</span> por este
                                medio se permite hacer de su conocimiento que el(la) estudiante
                                <span
                                    class='text-content'>". $data->nombre . ' ' . $data->apePat . ' ' . $data->apeMat ."
                                </span> con número
                                de control <span class='text-content'>". $data->num_control ."</span> de la carrera de
                                <span class='text-content'>". $data->carrera ."</span> ha cumplido su
                                actividad complementaria con el nivel de desempeño
                                <span class='text-content'>". $data->niv_des ."</span> y un valor numérico de
                                <span class='text-content'>". $data->calificacion ."</span>, durante el periodo escolar
                                ". $data->periodo ." con un valor curricular de
                                <span class='text-content'>". $data->creditos ."</span> créditos.
                            </div>
                            <br />
                            <div class='bottom'>
                                <p>
                                    Se extiende la presente en la Ciudad de Oaxaca a los ".$now->format('d')." dias de ". $month ." de
                                    ".$now->format('Y').".
                                </p>
                                <br>
                                <br>
                                <div class='atentamente'>
                                <br>
                                    <p class='atentamente-title'>ATENTAMENTE</p>
                                    <div class='column'>
                                        <br>    
                                        <br>    
                                        <p class='mb'>.</p>
                                        <p>___________________________________</p>
                                        <p>". $profesor->grado . ' ' . $profesor->nombre . ' ' . $profesor->apePat . ' ' . $profesor->apeMat ."</p>
                                        <p>Nombre y firma del profesor responsable</p>
                                    </div>
                                    <div class='column' style='margin-left: 55%'>
                                        <br>
                                        <br>
                                        <p class='mb'>.</p>
                                        <p>___________________________________</p>
                                        <p>".$jefe->grado . ' ' . $jefe->nombre . ' ' . $jefe->apePat . ' ' . $jefe->apeMat."</p>
                                        <p>Vo. Bo. del Jefe(a) del ".$data->depto."</p>
                                    </div>
                                </div>
                                <p class='copia' style='text-align: left; margin-top: -250px;'>
                                    <br>
                                    <br>
                                    C.c.p. Jefe(a) de Departamento correspondiente.
                                    <br>
                                    C.c.p. Interesado.
                                </p>
                            </div>
                            <footer>
                                <div class='footer-wrapper'>
                                    <img src=".$images->pie." height='130px'>
                                </div>
                            </footer>
                    </body>";

        $content .= "<style>
                        .copia{
                            font-size: 10px;
                        }
                        body {
                            margin: 0 2rem 0 2rem;
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
                            font-size: 16px;
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
                            width: 42%;
                            text-align: center;
                            margin-left: 4%;
                        }

                        .atentamente .column .name{
                            font-size: 12px;
                        }

                        .atentamente .column .mb {
                            margin-bottom: 30px;
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

                    </style>";

        // $pdf = App::make('dompdf.wrapper');
        // $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
        //      ->loadView('ProfRes.constancia', array('data' => $data,
        //                 'profesor' => $profesor,
        //                 'jefe' => $jefeDpto,
        //                 'day' => $now->format('d'),
        //                 'jefeDpto' => $jefe,
        //                 'month' => $month,
        //                 'year' => $now->format('Y'), 
        //                 'images' => $images
        //             ));

        $pdf->loadHTML($content);

        return $pdf->download('Constancia-'.$data->num_control.'.pdf');
    }
/**Esta función genera y descarga el formato de evaluación de actividad
 * complementaria del estudiante que recibe como parametro
 */
    public function criterioPdf($n_control) {
        $data = DB::table('estudiante as e')
            ->join('persona as p', 'p.id_persona', '=', 'e.id_persona')
            ->join('carrera as c', 'c.id_carrera', '=', 'e.id_carrera')
            ->join('departamento as d', 'd.id_depto', '=', 'c.id_depto')
            ->join('inscripcion as i', 'e.id_estudiante', '=', 'i.id_estudiante')
            ->join('evaluacion as ev', 'i.id_inscripcion', '=', 'ev.id_inscripcion')
            ->join('nivel_desempenio as nd', 'ev.id_desempenio', '=', 'nd.id_desempenio')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('periodo as pr', 'g.id_periodo', '=', 'pr.id_periodo')
            ->select('p.nombre as nombre', 'e.num_control as num_control',
                    'p.apePat as apePat', 'p.apeMat as apeMat',
                    'c.nombre as carrera', 'nd.nombre as niv_des',
                    'a.nombre as aNombre',
                    'ev.calificacion as calificacion',
                    'ev.id_evaluacion as id_evaluacion',
                    'ev.observaciones as observaciones',
                    'd.nombre as depto', 'a.creditos as creditos',
                    'pr.nombre as periodo')
            ->where('e.num_control', '=', $n_control)
            ->first();

        $criterios =  DB::table('criterios_evaluacion as ce')
                        ->get();
        
        $calificacionCrit = DB::table('eval_valor  as ev')
                            ->select('ev.id_evaluacion as idEval', 
                                     'ev.id_crit_eval as idCrit',
                                     'ev.id_desempenio as idDes')
                            ->where('ev.id_evaluacion', '=', $data->id_evaluacion)
                            ->get();

        
        $now = date_create('America/Mexico_City');
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('ProfRes.criteriosEvaluacion',
                    array('data' => $data, 'criterios'=>$criterios,
                         'calificacion'=>$calificacionCrit));

        return $pdf->download('criterio.pdf');
    }

    public function update_grupo(Request $request) {
        MGrupo::where('id_grupo', $request->input('id_grupo'))
            ->update(['asistencias' => $request->input('asistencias')]);

        $grupo = DB::table('grupo as g')
            ->join('persona as p', 'g.id_persona', '=', 'p.id_persona')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->select(
                'g.clave as clave',
                'a.nombre as actividad',
                'p.nombre as nombre',
                'p.apePat as paterno',
                'p.apeMat as materno',
                'g.id_grupo as id_grupo',
                'g.asistencias as asistencias'
            )
            ->where('g.id_grupo', $request->input('id_grupo'))
            ->get();

        $alumnosGrupo = DB::table('persona as p')
            ->join('estudiante as e', 'p.id_persona', '=', 'e.id_persona')
            ->join('inscripcion as i', 'e.id_estudiante', '=', 'i.id_estudiante')
            ->leftJoin('evaluacion as ev', 'i.id_inscripcion', '=', 'ev.id_inscripcion')
            ->select(
                'p.nombre as nombre',
                'e.num_control as num_control',
                'p.apePat as apePat',
                'p.apeMat as apeMat',
                'i.id_grupo as id_grupo',
                'ev.id_evaluacion as id_eval'
            )
            ->where('i.id_grupo', $request->input('id_grupo'))
            ->get();

        return redirect('ProfR/lista'.$request->input('id_grupo').'/evaluar');
    }

    public function downloadPdf($gpo, $origin)    {
        $pdf = App::make('dompdf.wrapper');
        $pdf = app('dompdf.wrapper');
        $content = "<h2>LISTADO DE ESTUDIANTES</h2>";

        $alumnosGrupo = DB::table('persona as p')
            ->join('estudiante as e', 'p.id_persona', '=', 'e.id_persona')
            ->join('inscripcion as i', 'e.id_estudiante', '=', 'i.id_estudiante')
            ->select('*')
            ->where('i.id_grupo', $gpo)
            ->where('i.aprobada', 1)
            ->get();

        $grupo = DB::table('grupo as g')
            ->join('persona as p', 'g.id_persona', '=', 'p.id_persona')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('departamento as d', 'a.id_depto', '=', 'd.id_depto')
            ->select(
                'g.clave',
                'a.nombre as actividad',
                'p.nombre',
                'p.apePat as paterno',
                'p.apeMat as materno',
                'g.id_grupo',
                'd.nombre as depto'
            )
            ->where('g.id_grupo', $gpo)
            ->first();

        $horario = DB::table('horario as h')
            ->join('dias_semana as ds', 'h.id_dia', '=', 'ds.id_dia')
            ->select('ds.nombre',
                    'h.hora_fin',
                    'h.hora_inicio')
            ->where('id_grupo', $gpo)
            ->get();

        $content .= "<h5>
                        <label> Grupo: " . $grupo->clave."</label>
                        <label class='col-sm-2'> - </label>
                        <label> Responsable: " .$grupo->nombre ." " .$grupo->paterno ." " .$grupo->materno ." </label>
                    </h5>";

        $content .= "<h5>
                    <label> Actividad: " . $grupo->actividad."</label>
                    <label class='col-sm-2'> - </label>
                    <label> Departamento: " .$grupo->depto ."</label>
                </h5>";

        // $content .= "<div class='row'>";

        foreach($horario as $h){
            $h->hora_inicio = substr($h->hora_inicio, 0, 5);
            $h->hora_fin = substr($h->hora_fin, 0, 5);
            $content .= "<label>| ".$h->nombre.":".$h->hora_inicio."-".$h->hora_fin." | "."</label>";
        }

        $content .= "<br>";

        $content .= "<br> <table style='width: 100%;  border-collapse: collapse;'>
				<tr>
                        <th width='8%'>No. control</th>
                        <th width='35%'>Nombre</th>";
        if($origin == 'print') {
			$content .= "<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>";
        }

	    $content .= "</tr>";

        foreach ($alumnosGrupo as $alumno) {
            $content .=
                "<tr>
										<td>" .
                $alumno->num_control .
                "</td>
										<td>" .
                $alumno->nombre ." ".
                $alumno->apePat ." ".
                $alumno->apeMat .
                "</td>";

                if($origin == 'print') {
                 $content .= "<td></td>".
                 "<td></td>".
                 "<td></td>".
                 "<td></td>".
                 "<td></td>".
                 "<td></td>".
                 "<td></td>".
                 "<td></td>".
                 "<td></td>".
                 "<td></td>".
                 "<td></td>".
                 "<td></td>".
                 "<td></td>".
                 "<td></td>".
                 "<td></td>";
                }
                $content .= "</tr>";
       }

        $content .= "</table>";
        $content .= "<style>
				table, th, td {
                      border: 1px solid black;
                      font-size: 10px;
				}
				h2 {
                      font-size: 23px;
                      text-align:center;
				}
                label{
                    margin-rigth: 15px;
                }
			</style>";

        $pdf->loadHTML($content);

        return $pdf->download('lista.pdf');
    }

    public function f_perfil(Request $request){

        $id_per = $request->user()->id_persona;

        $deptos = Mdepartamento::get();
        $puesto = Mpuesto::get();
        $grados = Mgrado::get();

        $persona = DB::table('persona as p')
        ->join('empleado as e', 'p.id_persona', '=', 'e.id_persona')
        ->join('departamento as d', 'e.id_depto', '=', 'd.id_depto')
        ->join('grado as g', 'e.id_grado', '=', 'g.id_grado')
        ->join('puesto as pu', 'e.id_puesto', '=', 'pu.id_puesto')
        ->select(
            'p.id_persona as id_persona',
            'p.nombre as nombre',
            'p.apePat as paterno',
            'p.apeMat as materno',
            'p.curp as curp',
            'd.nombre as depto',
            'g.nombre as grado',
            'pu.nombre as puesto',
            'e.id_depto as id_depto',
            'e.id_grado as id_grado',
            'e.id_puesto as id_puesto'
        )
        ->where('p.id_persona', $id_per)
                ->get();

        return view('ProfRes.perfil')
            ->with('persona', $persona)
            ->with('departamentos', $deptos)
            ->with('puestos', $puesto)
            ->with('grados', $grados);
    }

    public function f_editperfil(Request $request){

        $id_per = $request->user()->id_persona;

        $deptos = Mdepartamento::get();
        $puesto = Mpuesto::get();
        $grados = Mgrado::get();

        $persona = DB::table('persona as p')
        ->join('empleado as e', 'p.id_persona', '=', 'e.id_persona')
        ->join('departamento as d', 'e.id_depto', '=', 'd.id_depto')
        ->join('grado as g', 'e.id_grado', '=', 'g.id_grado')
        ->join('puesto as pu', 'e.id_puesto', '=', 'pu.id_puesto')
        ->select(
            'p.id_persona as id_persona',
            'p.nombre as nombre',
            'p.apePat as paterno',
            'p.apeMat as materno',
            'p.curp as curp',
            'd.nombre as depto',
            'g.nombre as grado',
            'pu.nombre as puesto',
            'e.id_depto as id_depto',
            'e.id_grado as id_grado',
            'e.id_puesto as id_puesto'
        )
        ->where('p.id_persona', $id_per)
                ->get();

        return view('ProfRes.editperfil')->with('persona', $persona)
        ->with('departamentos', $deptos)
        ->with('puestos', $puesto)
        ->with('grados', $grados);
    }

    public function f_editar(Request $request){

        $usuario = $request->user()->id_persona;
        $grado = $request->grado;
        $nombre = $request->nombre;
        $ape1 = $request->apePat;
        $ape2 = $request->apeMat;
        $curp = $request->curp;

        Mpersona::where('id_persona', $usuario)
            ->update(['nombre' => $nombre,
            'apePat' => $ape1, 'apeMat' => $ape2,
            'curp' => $curp]);

        Mempleado::where('id_persona', $usuario)
            ->update(['id_grado' => $grado]);

        return redirect()->to('/ProfR/datosGen');
    }

    public function formStudentEvaluation($n_control, $id_grupo) {
        
        $now = date_create('America/Mexico_City')->format('H');
        
        $student = Mestudiante::where('num_control' ,$n_control)
            ->first();

        $grupoAsistencias = DB::table('inscripcion as i')
            ->join('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
            ->join('persona as p', 'e.id_persona', '=', 'p.id_persona')
            ->join('grupo as g', 'g.id_grupo', '=', 'i.id_grupo')
            ->select('g.asistencias', 
                    'g.id_grupo',
                    'p.nombre',
                    'p.apeMat', 
                    'p.apePat', 
                    'e.num_control as nControl')
            ->where('i.id_grupo', $id_grupo)
            ->where('i.id_estudiante', $student->id_estudiante)
            ->where('i.aprobada', 1)
            ->get();

        $ct = Mcriterios_evaluacion::where('estado', 1)
            ->get();

        foreach($ct as $t){
            $t->descripcion = ucfirst(mb_strtolower($t->descripcion));
        }

        $ds = Mnivel_desempenio::get();

        return view('ProfRes.evaluationForm')
                ->with('critEval', $ct)
                ->with('nivelD', $ds)
                ->with('asistencias', $grupoAsistencias[0])
                ->with('n_control', $n_control);
    }

    public function f_passwd(){

        return view('ProfRes.editpasswd');
    }

    public function f_edpasswd(Request $request){

        $userpwd = $request->user()->password;
        $user = $request->user()->id_persona;
  
        $pswd = $request->passactual;
        $newpswd = $request->passnueva;
        $conpswd = $request->passconfir;
  
        if (Hash::check($pswd, $userpwd))
        {
           
           if($newpswd == $conpswd){
              $updt = Hash::make($newpswd);
              Musers::where('id_persona', $user)
                 ->update(['password' => $updt]);
  
              ?>
                 <script>
                     alert('Contraseña actualizada satisfactoriamente.');
                 </script>
              <?php
              
              $request->session()->invalidate();
              $request->session()->regenerateToken();
              
              ?>
                 <script>
                    location.href = "/IniciarSesion";
                 </script>
              <?php
  
           }else{
              ?>
                 <script>
                    alert('Las nuevas contraseñas no coinciden, intenta de nuevo.');
                    location.href = "/ProfR/editpasswd";
                 </script>
              <?php
           }
  
        }else{
           ?>
              <script>
                  alert('Contraseña actual incorrecta, intenta de nuevo.');
                  location.href = "/ProfR/editpasswd";
              </script>
          <?php
        }
    }

    public function logoutPR(Request $request){

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect("IniciarSesion");
    }
}
