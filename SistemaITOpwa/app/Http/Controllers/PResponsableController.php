<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

// se declara modelos (bd)
use App\Models\Musers;          use App\Models\Mgrado;
use App\Models\Mgrupo;          use App\Models\Mpuesto;
use App\Models\Mpersona;        use App\Models\Mperiodo;
use App\Models\Mempleado;       use App\Models\Mcarreras;
use App\Models\MEvalValor;      use App\Models\Mestudiante;
use App\Models\Mevaluacion;     use App\Models\Mdepartamento;
use DB;         use Auth;       use mysql_query;

class PResponsableController extends Controller
{
    /**Constructor del controlador */
    public function _construct() {  $this->middleware('auth'); }
    
    /**Redirecciona a la pagina de inicio de sesión de este usuario */
    public function f_inicio() { 
        
        $now = date_create('America/Mexico_City')->format('H');

        $today = date("Y-m-d");
        $dates = DB::select('SELECT ini_inscripcion, ini_evaluacion, ini_gconstancias,
                fin_inscripcion, fin_evaluacion, fin_gconstancias
                FROM periodo WHERE estado = "Actual"');
        $processes = 00;
        $endprocess = 00;
        foreach($dates as $d){
            if($today >= $d->ini_inscripcion && $today <= $d->fin_inscripcion){
                $processes = 01;
                $endprocess = $d->fin_inscripcion;}
            elseif($today >= $d->ini_evaluacion && $today <= $d->fin_evaluacion){
                $processes = 10;
                $endprocess = $d->fin_evaluacion;}
            elseif($today >= $d->ini_gconstancias && $today <= $d->fin_gconstancias){
                $processes = 11;
                $endprocess = $d->fin_gconstancias;}
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
 
        $id_per = $request->user()->id_persona;
        $tipo = "Ver";

        $grupos = DB::select('SELECT g.id_grupo as id_grupo,
            g.clave as clave, a.nombre as actividad,
            l.nombre as lugar, g.asistencias as asistencias,
            g.cupo as cupo, a.creditos as creditos
        FROM grupo AS g 
        JOIN periodo as p ON g.id_periodo = p.id_periodo 
        JOIN persona as pe ON g.id_persona = pe.id_persona 
        JOIN actividad as a ON g.id_actividad = a.id_actividad 
        JOIN lugar as l ON g.id_lugar = l.id_lugar 
        WHERE p.estado = "Actual" 
        AND g.estado IN(SELECT estado FROM grupo WHERE estado = 1)
        AND pe.id_persona = '.$id_per.'
        AND g.clave LIKE "%'.$search.'%" 
        OR a.nombre LIKE "%'.$search.'%"');


        return view('ProfRes.grupos')
        ->with('grupos', $grupos)
        ->with('tipo', $tipo);  
    }
/**Realiza el request del buscador de grupos y redirige a la función
 * f_gruposB()
 */
    public function f_searchgru(Request $request) { 
        
        
        $search = mb_strtoupper($request->search);
        //return $this->f_gruposB($search);
        return redirect()->to('ProfR/grupos/'.$search);
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

        /*$crit = DB::select('SELECT id_crit_eval FROM criterios_evaluacion
                WHERE estado = 1');*/

        $now = date_create('America/Mexico_City')->format('H');

        $alumno = DB::table('estudiante as a')
                    ->join('inscripcion as i', 'i.id_estudiante', '=', 'a.id_estudiante')
                    ->select('i.id_inscripcion as id', 'i.id_grupo as id_grupo')
                    ->where('a.num_control', '=', $request->input('n_control'))
                    ->first();
        
        /*$id_desempenio = $request->input('idDesempenio');
        $id_inscripcion = $alumno->id;
        $asistencias = $request->input('asistencias');
        $calificacion = $request->input('calificacion');
        $observaciones = $request->input('observaciones');*/

        $evaluacion = new Mevaluacion();
        $evaluacion->id_desempenio = $request->input('idDesempenio');
        $evaluacion->id_inscripcion = $alumno->id;
        $evaluacion->asistencias = $request->input('asistencias');
        $evaluacion->calificacion = $request->input('calificacion');
        $evaluacion->observaciones = $request->input('observaciones');
        $evaluacion->constancia = '';
        try {
            $evaluacion->save();
            /*Mevaluacion::create(['id_inscripcion' => $id_inscripcion,
            'id_desempenio' => $id_desempenio, 'asistencias' => $asistencias,
            'calificacion' => $calificacion, 'observaciones' => $observaciones]);*/

            for ($i = 1; $i <= 7; $i++){
            //foreach($crit as $c){
                $evalValor = new MEvalValor();
                $evalValor->id_evaluacion = $evaluacion->id;
                $evalValor->id_crit_eval = $i;
                $evalValor->id_desempenio = $request->input($i)+1;
                $evalValor->save();
            }

            return redirect('ProfR/lista'.$alumno->id_grupo.'/evaluar')
                        ->with('successEval', True);
        } catch (Exception $e) {
            return redirect('ProfR/lista'.$alumno->id_grupo.'/evaluar')
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
                'p.nombre as nombre',
                'e.num_control as num_control',
                'p.apePat as apePat',
                'p.apeMat as apeMat',
                'i.id_grupo as id_grupo',
                'ev.id_evaluacion as id_eval',
                'nd.nombre as nivel_desempenio'
            )
            ->where('i.id_grupo', $id_gru)
            ->where('i.aprobada', 1)
            ->get();

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
        
        $now = date_create('America/Mexico_City');
        
        $monthNumber = $now->format('m'); 
        $months = array (1=>'Enero',2=>'Febrero',
                        3=>'Marzo',4=>'Abril',5=>'Mayo',
                        6=>'Junio',7=>'Julio',8=>'Agosto',
                        9=>'Septiembre',10=>'Octubre',11=>'Noviembre',
                        12=>'Diciembre');

        $month = $months[(int)$monthNumber];
    
        $pdf = App::make('dompdf.wrapper');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
             ->loadView('ProfRes.constancia', array('data' => $data,
                        'profesor' => $profesor,
                        'jefe' => $jefeDpto,
                        'day' => $now->format('d'),
                        'jefeDpto' => $jefe,
                        'month' => $month,
                        'year' => $now->format('Y')
                    ));
    
        return $pdf->download('constancia.pdf');
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
        $content = "<h1>Lista de estudiantes</h1>";
        $content .= "<h5> Grupo: " . $gpo . " </h5>";

        $alumnosGrupo = DB::select(
            "SELECT *
							FROM persona as p
							INNER JOIN estudiante as e ON p.id_persona = e.id_persona
							INNER JOIN inscripcion as i ON e.id_estudiante = i.id_estudiante
							WHERE e.id_estudiante IN (SELECT id_estudiante
              FROM inscripcion
              GROUP BY id_grupo) AND i.id_grupo = " .
                $gpo .
                " AND i.aprobada  = 1"
        );

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
            ->where('g.id_grupo', $gpo)
            ->first();

        $content .= "<h5> Actividad: " . $grupo->actividad . " </h5>";
        $content .=
            "<h5> Responsable: " .
            $grupo->nombre .
            " " .
            $grupo->paterno .
            " " .
            $grupo->materno .
            " </h5>";

        $content .= "<table style='width: 100%; border-collapse: collapse;'>
				<tr>
                        <th width='10%'>No de control</th>
                        <th width='15%'>Nombre</th>
						<th width='10%'>Apellido Paterno</th>
						<th width='10%'>Apellido Materno</th>";
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
                $alumno->nombre .
                "</td>
    								<td>" .
                $alumno->apePat .
                "</td>
    								<td>" .
                $alumno->apeMat .
                "</td> ";
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
				h1 {
				    text-align:center;
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

    public function formStudentEvaluation($n_control) {
        
        $now = date_create('America/Mexico_City')->format('H');
        $grupoAsistencias = DB::table('estudiante as a')
                    ->join('persona as p', 'p.id_persona', '=', 'a.id_persona')
                    ->join('inscripcion as i', 'i.id_estudiante', '=', 'a.id_estudiante')
                    ->join('grupo as g', 'g.id_grupo', '=', 'i.id_grupo')
                    ->select('g.asistencias as asistencias', 'p.nombre as nombre',
                             'p.apeMat as apeMat', 'p.apePat as apePat', 'a.num_control as nControl')
                    ->where('a.num_control', '=', $n_control)
                    ->first();

        $ct = DB::table('criterios_evaluacion as t')
            ->select(
                't.id_crit_eval as id_crit_eval',
                't.nombre as nombre',
                't.descripcion as description',
                't.estado as estado'
            )
            ->where('t.estado', 1)
            ->get();

        $ds = DB::table('nivel_desempenio as t')
            ->select(
                't.id_desempenio as id_desempenio',
                't.nombre as nombre',
                't.valor as valor',
            )
            ->get();

        return view('ProfRes.evaluationForm')
                ->with('critEval', $ct)
                ->with('nivelD', $ds)
                ->with('asistencias', $grupoAsistencias)
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
