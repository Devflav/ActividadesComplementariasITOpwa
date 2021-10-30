<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;
use Codedge\Fpdf\Facades\Fpdf;
use Illuminate\Http\Request;

// se declara modelos (bd)
use App\Models\Mtipo;          use App\Models\Musers;
use App\Models\Mgrado;         use App\Models\Mgrupo;
use App\Models\Mlugar;         use App\Models\Mpuesto;
use App\Models\Mpersona;       use App\Models\Mcarrera;
use App\Models\Mperiodo;       use App\Models\Mactividad;
use App\Models\Mestudiante;    use App\Models\Minscripcion;
use App\Models\Mdepartamento;  use App\Models\Mcriterios_evaluacion;
use DB;

class StudentController extends Controller
{
    public function _construct() {  
        $this->middleware('estudiante');
      }
  /**Envia los tipos de actividades complementarias para la construcción
   * de la barra de navegación en el apartado Actividades
   */
      public function tipos(){
  
         $tipos = Mtipo::select('id_tipo', 'nombre')->get();
  
          foreach($tipos as $t){
              $t->nombre = ucwords(mb_strtolower($t->nombre));
          }
  
          return $tipos;
      }
  /**Verifica si el estudiante está inscrito en alguna actividad complementaria */
     public function inscrito($id_per){
  
      $estudiante = Mestudiante::select('id_estudiante')->where('id_persona', $id_per)->first();

      $inscription = DB::table('inscripcion as i')
         ->leftJoin('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
         ->leftJoin('persona as p', 'e.id_persona', '=', 'p.id_persona')
         ->leftJoin('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
         ->leftJoin('periodo as pr', 'g.id_periodo', '=', 'pr.id_periodo')
         ->select('i.aprobada')
         ->where('i.id_estudiante', $estudiante->id_estudiante)
         ->where('pr.estado', "Actual")
         ->get();
  
        $inscrito = false;
  
        foreach($inscription as $i){
           if($i->aprobada == 0 || $i->aprobada == 1)
              $inscrito = true;
        }
  
        return $inscrito;
     }
  /**Retorna a la vista de inicio con un saludo y un mensaje del proceso que se
   * está llevando a cabo en el sistema, (Inscripción, Evaluación, Generación de constancias)
   */
      public function f_inicio(Request $request) { 
  
         $now = date_create('America/Mexico_City')->format('H');
         $today = date("Y-m-d");
         $data = Mperiodo::where('estado', "Actual")->first();

         $processes = 00;
         $endprocess = 00;

         if($today >= $data->ini_inscripcion && $today <= $data->fin_inscripcion){
               $processes = 01;
               $endprocess = $data->fin_inscripcion;}
         elseif($today >= $data->ini_evaluacion && $today <= $data->fin_evaluacion){
               $processes = 10;
               $endprocess = $data->fin_evaluacion;}
         elseif($today >= $data->ini_gconstancias && $today <= $data->fin_gconstancias){
               $processes = 11;
               $endprocess = $data->fin_gconstancias;}

         $procesos[0] = $processes; $procesos[1] = $endprocess;

         return view('estudiante.inicio')
            ->with('hora', $now)  
            ->with('process', $processes)
            ->with('end', $endprocess)  
            ->with('tipos', $this->tipos());
      }
  /**Retorna a la vista del listado de las diferentes carreras que ofertaron actividades
   * complementarias (Solo las carreras que no restringen sus actividades)
   */
     public function f_actCarreras(Request $request){
  
        $now = date('Y-m-d');
        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
              ->where('estado', "Actual")->first();
  
        if($now >= $roll->ini_inscripcion && $now <= $roll->fin_inscripcion){

         $car = DB::table('actividad as a')
            ->join('departamento as d', 'a.id_depto', '=', 'd.id_depto')
            ->join('carrera as c', 'd.id_depto', '=', 'c.id_depto')
            ->join('grupo as g', 'a.id_actividad', '=', 'g.id_actividad')
            ->join('periodo as p', 'g.id_periodo', '=', 'p.id_periodo')
            ->select('c.id_carrera',
                     'c.nombre')
            ->where('p.estado', "Actual")
            ->where('c.estado', 1)
            ->where('a.restringida', 0)
            ->groupBy('c.id_carrera')
            ->orderBy('c.id_carrera')
            ->paginate(10);
  
           $inscrito = $this->inscrito($request->user()->id_persona);
  
           if(!$inscrito){
              return view('estudiante.actcarreras')
                 ->with('carreras', $car)
                 ->with('tipos', $this->tipos());
           }else{
              return view('estudiante.inscrito')
                 ->with('v', 00)
                 ->with('tipos', $this->tipos());
           }
        }else{
  
           return view('estudiante.inscrito')
                 ->with('v', 11)
                 ->with('tipos', $this->tipos());         
        }
        
     }
  /**Retorna a la vista del listado de actividades con las actividades ofertadas por la 
   * carrera del estudiante
   */
     public function f_micarrera(Request $request){
  
        $now = date('Y-m-d');
        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
              ->where('estado', "Actual")->first();
  
        if($now >= $roll->ini_inscripcion && $now <= $roll->fin_inscripcion){
         //   
           $id_per = $request->user()->id_persona;
  
           $carrera = DB::table('carrera as c')
           ->join('estudiante as e', 'c.id_carrera', '=', 'e.id_carrera')
           ->select('c.nombre')
           ->where('e.id_persona', $id_per)
           ->get();
  
           $dpte = DB::table('departamento as d')
              ->join('carrera as c', 'd.id_depto', '=', 'c.id_depto')
              ->join('estudiante as e', 'c.id_carrera', '=', 'e.id_carrera')
              ->select('d.id_depto')
              ->where('e.id_persona', $id_per)
              ->get();
  
            $actCar = DB::table('grupo as g')
               ->join('periodo as p', 'g.id_periodo', '=', 'p.id_periodo')
               ->join('lugar as l', 'g.id_lugar', '=', 'l.id_lugar')
               ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
               ->join('departamento as d', 'a.id_depto', '=', 'd.id_depto')
               ->join('carrera as c', 'd.id_depto', '=', 'c.id_depto')
               ->join('estudiante as e', 'c.id_carrera', '=', 'e.id_carrera')
               ->select('g.id_grupo',
                        'g.clave',
                        'a.nombre',
                        'a.creditos',
                        'l.nombre AS lugar',
                        'g.cupo_libre')
               ->where('p.estado', "Actual")
               ->where('g.estado', 1)
               ->where('d.id_depto', $dpte[0]->id_depto)
               ->groupBy('g.id_grupo')
               ->orderBy('g.id_grupo')
               ->paginate(10);
  
           $inscrito = $this->inscrito($id_per);
  
           if(!$inscrito){
              return view('estudiante.micarrera')
                 ->with('actividades', $actCar)
                 ->with('car', $carrera)
                 ->with('tipos', $this->tipos());
           }else{
              return view('estudiante.inscrito')
                 ->with('v', 00)
                 ->with('tipos', $this->tipos());    
           }
        }else{
  
           return view('estudiante.inscrito')
                 ->with('v', 11)
                 ->with('tipos', $this->tipos());         
        }
     }
  /**Retorna a la vista del listado de actividades complementarias filtradas po el tipo
   * de actividad seleccionado en el menú
   */
     public function f_actividades($tipo, Request $request){
  
        $now = date('Y-m-d');
        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
              ->where('estado', "Actual")->first();
  
        if($now >= $roll->ini_inscripcion && $now <= $roll->fin_inscripcion){
  
           $tact = Mtipo::select('nombre')
           ->where('id_tipo', $tipo)
           ->get();
  
           $actividad = DB::table('grupo as g')
               ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
               ->join('lugar as l', 'g.id_lugar', '=', 'l.id_lugar')
               ->join('periodo as p', 'g.id_periodo', '=', 'p.id_periodo')
               ->select('g.id_grupo',
                        'g.clave',
                        'a.nombre',
                        'a.creditos',
                        'l.nombre AS lugar',
                        'g.cupo_libre')
               ->where('p.estado', "Actual")
               ->where('a.restringida', 0)
               ->where('g.estado', 1)
               ->where('a.id_tipo', $tipo)
               ->groupBy('g.id_grupo')
               ->orderBy('g.id_grupo')
               ->paginate(10);
  
           $inscrito = $this->inscrito($request->user()->id_persona);
  
           if(!$inscrito){
              return view('estudiante.actividades')
                 ->with('actividades', $actividad)
                 ->with('tnom', $tact)
                 ->with('tipos', $this->tipos());
           }else{
              return view('estudiante.inscrito')
                 ->with('v', 00)
                 ->with('tipos', $this->tipos());
           }
        }else{
  
           return view('estudiante.inscrito')
                 ->with('v', 11)
                 ->with('tipos', $this->tipos());         
        }
  
     }
  /**Retorna a la vista del listado de actividades con las actividades ofertadas por las 
   * diferentes carreras, solo aquellas a las que puede inscribirse este estudiante, las
   * que no están restringidas
   */
     public function f_actividadesCar($id_car, Request $request){
  
         $now = date('Y-m-d');
         $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
               ->where('estado', "Actual")->first();
   
        if($now >= $roll->ini_inscripcion && $now <= $roll->fin_inscripcion){
   
            $cact = Mcarrera::select('nombre')
                  ->where('id_carrera', $id_car)
                  ->get();

               $actCar = DB::table('grupo as g')
                  ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
                  ->join('lugar as l', 'g.id_lugar', '=', 'l.id_lugar')
                  ->join('departamento as d', 'a.id_depto', '=', 'd.id_depto')
                  ->join('carrera as c', 'd.id_depto', '=', 'c.id_depto')
                  ->join('periodo as p', 'g.id_periodo', '=', 'p.id_periodo')
                  ->select('g.id_grupo',
                           'g.clave',
                           'a.nombre',
                           'a.creditos',
                           'l.nombre AS lugar',
                           'g.cupo_libre')
                  ->where('p.estado', "Actual")
                  ->where('g.estado', 1)
                  ->where('c.estado', 1)
                  ->where('c.id_carrera', $id_car)
                  ->orderBy('g.id_grupo')
                  ->paginate(10);
   
            $inscrito = $this->inscrito($request->user()->id_persona);
  
            if(!$inscrito){
                  return view('estudiante.actividades')
                     ->with('actividades', $actCar)
                     ->with('tnom', $cact)
                     ->with('tipos', $this->tipos());
            } else {
                  return view('estudiante.inscrito')
                     ->with('v', 00)
                     ->with('tipos', $this->tipos());
            }
         } else {
   
            return view('estudiante.inscrito')
                  ->with('v', 11)
                  ->with('tipos', $this->tipos());         
         }
   
     }
  /**Retorna a la vista donde se muestran los datos generales de la actividad
   * complementaria, donde el estudiante debe confirmar la solicitud de inscripción
   */
     public function f_inscribir($id_gru){
  
         $actividad = DB::table('grupo as g')
            ->join('lugar as l', 'g.id_lugar', '=', 'l.id_lugar')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('departamento as d', 'a.id_depto', '=', 'd.id_depto')
            ->join('tipo as t', 'a.id_tipo', '=', 't.id_tipo')
            ->join('persona as p', 'g.id_persona', '=', 'p.id_persona')
            ->join('empleado as e', 'p.id_persona', '=', 'e.id_persona')
            ->join('grado as gr', 'e.id_grado', '=', 'gr.id_grado')
            ->select('g.id_grupo',
                     'g.clave',
                     'a.nombre as actividad',
                     'gr.nombre as grado',
                     'p.nombre',
                     'p.apePat',
                     'p.apeMat',
                     'a.creditos',
                     'l.nombre AS lugar',
                     'd.nombre AS depto',
                     't.nombre as tipo')
            ->where('g.id_grupo', $id_gru)
            ->orderBy('g.id_grupo')
            ->paginate(10);

         $horario = DB::table('grupo as g')
            ->join('horario as h', 'g.id_grupo', '=', 'h.id_grupo')
            ->join('dias_semana as ds', 'h.id_dia', '=', 'ds.id_dia')
            ->select('ds.id_dia',
                     'ds.nombre',
                     'h.hora_fin',
                     'h.hora_inicio')
            ->where('g.id_grupo', $id_gru)
            ->get();
      
         return view('estudiante.detInscrip')
            ->with('actividad', $actividad)
            ->with('horario', $horario)
            ->with('v', 00)
            ->with('tipos', $this->tipos());
     }

  /**Se confirma la solitud de inscripción del estudiante */
      public function f_solicitudIns($idgrupo, Request $request){
        
         $student = Mestudiante::where('id_persona', $request->user()->id_persona)
            ->first();

         $cupo = Mgrupo::where('id_grupo', $idgrupo)
            ->first();

         $hoy = date("Y-m-d");
  
         Minscripcion::create([
            'id_estudiante' => $student->id_estudiante,
            'id_grupo' => $idgrupo, 
            'fecha' => $hoy, 
            'aprobada' => 0
         ]);
        
         Mgrupo::where('id_grupo', $idgrupo)
            ->update([
               'cupo_libre' => ($cupo->cupo_libre-1)
         ]);
        
        return redirect()->to('Est');
      }

  /**Retorna a la vista donde se muestra la actividad que está cursando el estudiante */
      public function f_cursando(Request $request){
   
            $id_per = $request->user()->id_persona;
            $student = Mestudiante::where('id_persona', $id_per)
               ->first();

            $actividad = DB::table('inscripcion as i')
               ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
               ->join('lugar as l', 'g.id_lugar', '=', 'l.id_lugar')
               ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
               ->join('departamento as d', 'a.id_depto', '=', 'd.id_depto')
               ->join('tipo as t', 'a.id_tipo', '=', 't.id_tipo')
               ->join('persona as p', 'g.id_persona', '=', 'p.id_persona')
               ->join('empleado as e', 'p.id_persona', '=', 'e.id_persona')
               ->join('grado as gr', 'e.id_grado', '=', 'gr.id_grado')
               ->join('periodo as pr', 'g.id_periodo', '=', 'pr.id_periodo')
               ->select('g.id_grupo',
                        'g.clave',
                        'a.nombre as actividad',
                        'gr.nombre as grado',
                        'p.nombre',
                        'p.apePat',
                        'p.apeMat',
                        'a.creditos',
                        'l.nombre AS lugar',
                        'gr.nombre AS grado',
                        'd.nombre AS depto',
                        't.nombre as tipo',
                        'i.id_inscripcion')
               ->where('pr.estado', "Actual")
               ->where('i.aprobada', 1)
               ->where('i.id_estudiante', $student->id_estudiante)
               ->get();
      
            $inscription = DB::table('inscripcion as i')
               ->join('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
               ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
               ->join('periodo as p', 'g.id_periodo', '=', 'p.id_periodo')
               ->select('i.id_inscripcion')
               ->where('p.estado', "Actual")
               ->where('i.aprobada', 1)
               ->where('e.id_persona', $id_per)
               ->get();
   
         $horario = [];
         
         foreach($inscription as $i){

            $horario = DB::table('inscripcion as i')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('horario as h', 'g.id_grupo', '=', 'h.id_grupo')
            ->join('dias_semana as ds', 'h.id_dia', '=', 'ds.id_dia')
            ->select('ds.id_dia',
                     'ds.nombre',
                     'h.hora_fin',
                     'h.hora_inicio')
            ->where('i.id_inscripcion', $i->id_inscripcion)
            ->get();
         }

         if(count($actividad) == 0){
            return view('estudiante.inscrito')
               ->with('v', 01)
               ->with('tipos', $this->tipos());
         }else{
   
            return view('estudiante.detInscrip')
               ->with('actividad', $actividad)
               ->with('horario', $horario)
               ->with('v', 11)
               ->with('tipos', $this->tipos());
         }
      }
  
  /**Retorna a la vista donde se muestra el historial del estudiante */
     public function f_historial(Request $request){
  
        $id_per = $request->user()->id_persona;

         $historial = DB::table('evaluacion as ev')
            ->join('nivel_desempenio as nd', 'ev.id_desempenio', '=', 'nd.id_desempenio')
            ->join('inscripcion as i', 'ev.id_inscripcion', 'i.id_inscripcion')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('periodo as p', 'g.id_periodo', '=', 'p.id_periodo')
            ->select('p.nombre as periodo',
                     'a.nombre as actividad',
                     'a.creditos',
                     'ev.calificacion',
                     'nd.nombre as evaluacion',
                     'ev.observaciones')
            ->where('e.id_persona', $id_per)
            ->orderBy('p.id_periodo', 'asc')
            ->get();

        if(!count($historial)){
  
           return view('estudiante.inscrito')
              ->with('v', 10)
              ->with('tipos', $this->tipos());
        }else{
  
           return view('estudiante.historial')
           ->with('historial', $historial)
           ->with('tipos', $this->tipos());
        }
     }

  /**Retorna a la vista del perfil de usuario, donde se muestran los datos 
   * generales y las opciones a edición de los mismos como de la contraseña
   */
      public function f_vhorario($id_gru){
      
         $horario = DB::table('horario as h')
            ->join('dias_semana as ds', 'h.id_dia', '=', 'ds.id_dia')
            ->select('ds.nombre as dia', 
                  'h.hora_inicio as hi', 
                  'h.hora_fin as hf')
            ->where('h.id_grupo', $id_gru)
            ->get();
         
         return view('estudiante.vhorario')
         ->with('horario', $horario)
         ->with('tipos', $this->tipos());
      }

      public function f_horario_e($id_ins){

         $periodo_ = Mperiodo::select('nombre', 'inicio', 'cabecera')
            ->where('estado', "Actual")->first();

         $periodo_->cabecera = substr($periodo_->cabecera, 1);

         $fecha_hoy = date('d-m-Y'); 
         $nctrl = mb_strtoupper("número control: ");

         $id_std = Minscripcion::select('id_estudiante')
            ->where('id_inscripcion', $id_ins)->first();

         $schedule = DB::table('inscripcion as i')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('horario as h', 'g.id_grupo', '=', 'h.id_grupo')
            ->join('dias_semana as ds', 'h.id_dia', '=', 'ds.id_dia')
            ->select('ds.nombre',
                     'h.hora_fin',
                     'h.hora_inicio',
                     'h.id_grupo')
            ->where('i.id_estudiante', $id_std->id_estudiante)
            ->where('i.aprobada', 1)
            ->get();

         $student = DB::table('inscripcion as i')
            ->join('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
            ->join('persona AS p', 'e.id_persona', '=', 'p.id_persona')
            ->join('carrera AS c', 'e.id_carrera', '=', 'c.id_carrera')
            ->select('e.num_control',
                     'p.nombre', 
                     'p.apePat', 
                     'p.apeMat', 
                     'c.nombre AS carrera',
                     'e.semestre')
            ->where('i.id_inscripcion', $id_ins)
            ->get();

         $activity = DB::table('inscripcion as i')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('persona AS p', 'g.id_persona', '=', 'p.id_persona')
            ->join('actividad AS a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('lugar AS l', 'g.id_lugar', '=', 'l.id_lugar')
            ->select('p.nombre as nom_res', 
                     'p.apePat',
                     'p.apeMat',
                     'g.clave', 
                     'a.nombre', 
                     'l.nombre AS lugar',
                     'a.creditos')
            ->where('i.id_estudiante', $id_std->id_estudiante)
            ->where('i.aprobada', 1)
            ->get();

         /**Configuración inicial de PDF */
         setlocale(LC_ALL,"es_MX.UTF-8");
         Fpdf::AddPage();
         Fpdf::SetFont('Arial', '', 8);
         Fpdf::SetMargins(30, 5 , 30);
         Fpdf::SetAutoPageBreak(true);

         /**Primer horario datos del estudiante */
         Fpdf::Image($periodo_->cabecera, 20, 10, 165, 31);   

         Fpdf::setXY(10,33);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 25, utf8_decode('FECHA: '), 0); 
         Fpdf::setXY(24,33);
         Fpdf::SetFont('Arial', '', 9);
         Fpdf::Cell(60, 25, utf8_decode($fecha_hoy), 0); 

         Fpdf::setXY(115,33);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 25, utf8_decode('PERIODO ESCOLAR: '), 0);
         Fpdf::setXY(149,33);
         Fpdf::SetFont('Arial', '', 9);
         Fpdf::Cell(60, 25, utf8_decode($periodo_->nombre), 0);

         Fpdf::setXY(10, 39);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: "), 0);
         Fpdf::setXY(44, 39);
         Fpdf::SetFont('Arial', '', 9);
         Fpdf::Cell(60, 25, $student[0]->num_control, 0);

         Fpdf::setXY(10, 45);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 25, 'ALUMNO: ', 0);
         Fpdf::setXY(27, 45);
         Fpdf::SetFont('Arial', '', 9);
         Fpdf::Cell(60, 25, utf8_decode($student[0]->nombre." ".$student[0]->apePat." ".$student[0]->apeMat), 0);

         Fpdf::setXY(115, 39);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 25, 'SEMESTRE: ', 0);
         Fpdf::setXY(135, 39);
         Fpdf::SetFont('Arial', '', 9);
         Fpdf::Cell(60, 25, utf8_decode($student[0]->semestre), 0);

         Fpdf::setXY(10, 51);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 25, 'CARRERA: ', 0);
         Fpdf::setXY(29, 51);
         Fpdf::SetFont('Arial', '', 9);
         Fpdf::Cell(60, 25, utf8_decode($student[0]->carrera), 0);

         /**Primer horario, encabezados y apartado de firmas */
         Fpdf::setXY(10, 71);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(10, 13, 'ACTIVIDAD', 0);

         Fpdf::setXY(47, 71);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(10, 13, 'RESPONSABLE', 0);

         Fpdf::setXY(74, 71);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 13, 'GRUPO ', 0);

         Fpdf::setXY(90, 71);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 13, 'C ', 0);
         
         Fpdf::setXY(94, 71);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 13, 'LUGAR ', 0);

         Fpdf::setXY(126, 71);
         Fpdf::SetFont('Arial', '', 9);
         Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

         Fpdf::SetFont('Arial', '', 9);

         Fpdf::SetFont('Arial', '', 9);
         Fpdf::setXY(10, 115);
         Fpdf::Cell(5, 20, '________________________________________________', 0);

         Fpdf::setXY(10, 120);
         Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES COMPLEMENTARIAS', 0);

         Fpdf::setXY(110, 115);
         Fpdf::Cell(5, 20, '________________________________________________', 0);

         Fpdf::setXY(110, 120);
         Fpdf::Cell(5, 20, utf8_decode($student[0]->nombre." ".$student[0]->apePat." ".$student[0]->apeMat), 0);

         /**Segundo horario, datos del estudiante */
         Fpdf::Image($periodo_->cabecera, 20, 153, 165, 31);   

         Fpdf::setXY(10,176);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 25, 'FECHA: ', 0); 
         Fpdf::setXY(24,176);
         Fpdf::SetFont('Arial', '', 9);
         Fpdf::Cell(60, 25, utf8_decode($fecha_hoy), 0); 

         Fpdf::setXY(115,176);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: ', 0);
         Fpdf::setXY(149,176);
         Fpdf::SetFont('Arial', '', 9);
         Fpdf::Cell(60, 25, utf8_decode($periodo_->nombre), 0);

         Fpdf::setXY(10, 182);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: "), 0);
         Fpdf::setXY(44, 182);
         Fpdf::SetFont('Arial', '', 9);
         Fpdf::Cell(60, 25, utf8_decode($student[0]->num_control), 0);

         Fpdf::setXY(10, 188);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 25, 'ALUMNO: ', 0);
         Fpdf::setXY(27, 188);
         Fpdf::SetFont('Arial', '', 9);
         Fpdf::Cell(60, 25, utf8_decode($student[0]->nombre." ".$student[0]->apePat." ".$student[0]->apeMat), 0);

         Fpdf::setXY(115, 182);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 25, 'SEMESTRE: ', 0);
         Fpdf::setXY(135, 182);
         Fpdf::SetFont('Arial', '', 9);
         Fpdf::Cell(60, 25, utf8_decode($student[0]->semestre), 0);

         Fpdf::setXY(10, 194);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 25, 'CARRERA: ', 0);
         Fpdf::setXY(29, 194);
         Fpdf::SetFont('Arial', '', 9);
         Fpdf::Cell(60, 25, utf8_decode($student[0]->carrera), 0);

         /**Segund horario, encabezados y apartado de firmas */
         Fpdf::setXY(10, 214);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(10, 13, 'ACTIVIDAD', 0);

         Fpdf::setXY(47, 214);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(10, 13, 'RESPONSABLE ', 0);

         Fpdf::setXY(74, 214);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 13, 'GRUPO ', 0);

         Fpdf::setXY(90, 214);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 13, 'C ', 0);

         Fpdf::setXY(94, 214);
         Fpdf::SetFont('Arial', 'B', 9);
         Fpdf::Cell(60, 13, 'LUGAR ', 0);

         Fpdf::setXY(126, 214);
         Fpdf::SetFont('Arial', '', 9);
         Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

         Fpdf::SetFont('Arial', '', 9);

         Fpdf::SetFont('Arial', '', 9);
         Fpdf::setXY(10, 258);
         Fpdf::Cell(5, 20, '________________________________________________', 0);

         Fpdf::setXY(10, 263);
         Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES COMPLEMENTARIAS', 0);

         Fpdf::setXY(110, 258);
         Fpdf::Cell(5, 20, '________________________________________________', 0);

         Fpdf::setXY(110, 263);
         Fpdf::Cell(5, 20, utf8_decode($student[0]->nombre." ".$student[0]->apePat." ".$student[0]->apeMat), 0);

         /**La(s) Actividad(es) a la(s) que está inscrito el estudiante.*/
         for($i = 0; $i < count($activity); $i++){

            /**Fraccionar el tamaño del nombre de la actividad */
            $a = strlen($activity[$i]->nombre);
            $a = $a / 15;
            for($j = 0; $j < $a; $j++){
               $_activity = substr($activity[$i]->nombre, ($j*15), ($j+15));
               Fpdf::setXY(10, 70 + ($j * 3) + ($i * 18));
               Fpdf::SetFont('Arial', '', 9);
               Fpdf::Cell(1, 25, utf8_decode(mb_strtoupper($_activity)), 0);
            }
            
            /**Datos de la(s) Actividad(es) y Responsable(s), primer horario */
            Fpdf::setXY(47, 76 + ($i * 18));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($activity[$i]->nom_res)), 0);
            Fpdf::setXY(47, 79 + ($i * 18));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($activity[$i]->apePat)), 0);
            Fpdf::setXY(47, 82 + ($i * 18));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($activity[$i]->apeMat)), 0);
            
            Fpdf::setXY(74, 77.5 + ($i * 18));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(50, 10, utf8_decode($activity[$i]->clave), 0);

            Fpdf::setXY(90, 70 + ($i * 18));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(60, 25, utf8_decode($activity[$i]->creditos), 0);

            /**Fraccionar el tamaño del nombre del lugar */
            $a = strlen($activity[$i]->lugar);
            $a = $a / 14;
            for($j = 0; $j < $a; $j++){
               $_lugar = substr($activity[$i]->lugar, ($j*14), ($j+14));
               Fpdf::setXY(94, 70 + ($j * 3) + ($i * 18));
               Fpdf::SetFont('Arial', '', 9);
               Fpdf::Cell(1, 25, utf8_decode(mb_strtoupper($_lugar)), 0);
            }

            /**Datos de la(s) Actividad(es) y Responsable(s), segundo horario.*/
            $a = strlen($activity[$i]->nombre);
            $a = $a / 15;

            for($j = 0; $j < $a; $j++){
               $_activity = substr($activity[$i]->nombre, ($j*15), ($j+15));
               Fpdf::setXY(10, 213.5 + ($j*3) + ($i * 19));
               Fpdf::SetFont('Arial', '', 9);
               Fpdf::Cell(60, 25, utf8_decode(mb_strtoupper($_activity)), 0);
            }

            Fpdf::setXY(47, 219.5 + ($i * 19));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($activity[$i]->nom_res)), 0);
            Fpdf::setXY(47, 222.5 + ($i * 19));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($activity[$i]->apePat)), 0);
            Fpdf::setXY(47, 225.5 + ($i * 19));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($activity[$i]->apeMat)), 0);

            Fpdf::setXY(74, 221 + ($i * 19));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(50, 10, utf8_decode($activity[$i]->clave), 0);
            
            Fpdf::setXY(90, 213.5 + ($i * 19));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(60, 25, utf8_decode($activity[$i]->creditos), 0);

            /**Fraccionar el tamaño del nombre del lugar */
            $a = strlen($activity[$i]->lugar);
            $a = $a / 14;
            for($j = 0; $j < $a; $j++){
               $_lugar = substr($activity[$i]->lugar, ($j*14), ($j+14));
               Fpdf::setXY(94, 213.5 + ($j * 3) + ($i * 19));
               Fpdf::SetFont('Arial', '', 9);
               Fpdf::Cell(1, 25, utf8_decode(mb_strtoupper($_lugar)), 0);
            }

         }

         /**El(Los) Horario(s) de la(s) Actividad(es) a la(s) que está inscrito el estudiante.*/
         $contador = 116; $cont2 = 11;
         $gru = $schedule[0]->id_grupo;
         foreach ($schedule as $c)        {
            $contador += 11;
            $c->nombre = substr($c->nombre, 0, 4);
            $c->hora_inicio = substr($c->hora_inicio, 0, 5);
            $c->hora_fin = substr($c->hora_fin, 0, 5);

            if($c->id_grupo == $gru){

               /**Primer horario, primera actividad */
               Fpdf::SetFont('Arial', 'B', 9);
               Fpdf::setXY($contador , 60);
               Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
               
               Fpdf::SetFont('Arial', '', 9);
               Fpdf::setXY($contador, 55);
               Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

               Fpdf::setXY($contador, 53);
               Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

               /**Segundo horario, primera actividad */
               Fpdf::SetFont('Arial', 'B', 9);
               Fpdf::setXY($contador , 203);
               Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);

               Fpdf::SetFont('Arial', '', 9);
               Fpdf::setXY($contador, 198);
               Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

               Fpdf::setXY($contador, 196);
               Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);
            }else{
               $contador = 116;

               /**Primer horario, segunda actividad */
               Fpdf::SetFont('Arial', 'B', 9);
               Fpdf::setXY($contador + $cont2, 82.5);
               Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
               
               Fpdf::SetFont('Arial', '', 9);
               Fpdf::setXY($contador + $cont2, 76.5);
               Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

               Fpdf::setXY($contador + $cont2, 74.5);
               Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

               /**Segundo horario, segunda actividad */
               Fpdf::SetFont('Arial', 'B', 9);
               Fpdf::setXY($contador + $cont2, 227.5);
               Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);

               Fpdf::SetFont('Arial', '', 9);
               Fpdf::setXY($contador + $cont2, 221.5);
               Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

               Fpdf::setXY($contador + $cont2, 219.5);
               Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

               $cont2 += 11;
            }
         } 
         
         $tipo = 'I';
         $headers = ['Content-Type' => 'application/pdf'];
         $doc_name = "Horario-".$student[0]->num_control.".pdf";

         return Response::make(Fpdf::Output($tipo, $doc_name), 200, $headers);
      }

  /**Retorna a la vista donde se muestra la sección del manual de lineamientos del 
   * Tecnológico Nacional de México que habla acerca de las actividades complementarias
   */
      public function f_perfil(Request $request){
            
         $id_per = $request->user()->id_persona;
      
         $estudiante = DB::table('persona as p')
         ->join('estudiante as e', 'p.id_persona', '=', 'e.id_persona')
         ->join('carrera as c', 'e.id_carrera', '=', 'c.id_carrera')
         ->select('p.id_persona as id_persona', 
                  'p.nombre as nombre',
                  'p.apePat as paterno', 
                  'p.apeMat as materno', 
                  'p.curp as curp',
                  'e.num_control as ncontrol', 
                  'e.semestre as semestre',
                  'c.nombre as carrera')
         ->where('p.id_persona', $id_per)
         ->get();
      
         return view('estudiante.perfil')
         ->with('estudiante', $estudiante)
         ->with('tipos', $this->tipos());
      }

  /**Realiza el request de los datos cambiados para actualizar el contenido
  * en la base de datos y retorna a la vista del perfil de usuairo
  */
      public function f_editar(Request $request){
         
         $id_per = $request->user()->id_persona;
      
         $estudiante = DB::table('persona as p')
         ->join('estudiante as e', 'p.id_persona', '=', 'e.id_persona')
         ->join('carrera as c', 'e.id_carrera', '=', 'c.id_carrera')
         ->select('p.id_persona as id_persona', 
                  'p.nombre as nombre',
                  'p.apePat as paterno', 
                  'p.apeMat as materno', 
                  'p.curp as curp',
                  'e.num_control as ncontrol', 
                  'e.semestre as semestre',
                  'c.nombre as carrera')
         ->where('p.id_persona', $id_per)
         ->get();
      
         return view('estudiante.editar')
         ->with('estudiante', $estudiante)
         ->with('tipos', $this->tipos());
      }

  /**Retorna a la vista para cambiar la contraseña */
      public function f_e_passwd() {
         
         return view('estudiante.editpasswd')
         ->with('tipos', $this->tipos());
      }

  /**Realiza el request de la nueva contraseña. Para realizar un cambio de contraseña
  * coteja que:
  * 1.- La nueva contraseña no sea igual a la contraseña actual
  * 2.- Sea de tamaño >= 8 && <= 16
  */
      public function f_editpsswd(Request $request){
      
         $userpwd = $request->user()->password;
         $user = $request->user()->id_persona;
      
         $pswd = $request->pswdactual;
         $newpswd = $request->pswdnueva;
         $conpswd = $request->pswdconfirm;
      
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
                     location.href = "IniciarSesion";
                  </script>
               <?php
      
            }else{
               ?>
                  <script>
                     alert('Las nuevas contraseñas no coinciden, intenta de nuevo.');
                     location.href = "Est/editpasswd";
                  </script>
               <?php
            }
      
         }else{
            ?>
               <script>
                     alert('Contraseña actual incorrecta, intenta de nuevo.');
                     location.href = "Est/editpasswd";
               </script>
            <?php
      
         }
      }

  /**Retorna un modal en el cual se muestra el horario de la actividad complementaria
  * seleccionada
  */
     public function f_lineamiento(){
  
        return view('estudiante.lineamiento')
           ->with('tipos', $this->tipos());
     }

  /**Destruye la sesión del usuario y retorna a la vista de inicio de sesión */
     public function logoutE(Request $request){
  
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect("IniciarSesion");
     }
}
