<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        $this->middleware('auth');
      }
  /**Envia los tipos de actividades complementarias para la construcción
   * de la barra de navegación en el apartado Actividades
   */
     public function tipos(){
  
        $tipos = DB::select('SELECT id_tipo, nombre
              FROM tipo');
  
          foreach($tipos as $t){
              $t->nombre = ucwords(mb_strtolower($t->nombre));
          }
  
          return $tipos;
     }
  /**Verifica si el estudiante está inscrito en alguna actividad complementaria */
     public function inscrito($id_per){
  
        //$id_per = $request->user()->id_persona;
        
        $inscription = DB::select('SELECT i.aprobada
        FROM inscripcion AS i
        LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
        LEFT JOIN persona AS p ON e.id_persona = p.id_persona
        LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
        LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
        WHERE i.id_estudiante  IN(SELECT es.id_estudiante
                                   FROM estudiante AS es
                                   WHERE es.id_persona = '.$id_per.')
        AND pr.estado = "Actual"
        ');
  
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
        $dates = DB::select('SELECT ini_inscripcion, ini_evaluacion, ini_gconstancias,
              fin_inscripcion, fin_evaluacion, fin_gconstancias
              FROM periodo WHERE estado = "Actual"');
        $processes;
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
           else
              $processes = 00;
        }
  
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
  
        //if($now >= $roll->ini_inscripcion && $now <= $roll->fin_inscripcion){
  
           $car = DB::select('SELECT c.id_carrera, c.nombre
           FROM actividad AS a 
           LEFT JOIN departamento AS d ON a.id_depto = d.id_depto
           LEFT JOIN carrera AS c ON d.id_depto = c.id_depto
           LEFT JOIN grupo AS g ON a.id_actividad = g.id_actividad
           LEFT JOIN periodo AS p ON g.id_periodo = p.id_periodo
           WHERE a.restringida = 0
           AND p.estado = "Actual"
           AND c.estado = 1
           GROUP BY c.id_carrera, c.nombre');
  
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
        // }else{
  
        //    return view('estudiante.inscrito')
        //          ->with('v', 11)
        //          ->with('tipos', $this->tipos());         
        // }
        
     }
  /**Retorna a la vista del listado de actividades con las actividades ofertadas por la 
   * carrera del estudiante
   */
     public function f_micarrera(Request $request){
  
        $now = date('Y-m-d');
        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
              ->where('estado', "Actual")->first();
  
        //if($now >= $roll->ini_inscripcion && $now <= $roll->fin_inscripcion){
           
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
  
           $actCar = DB::select('SELECT g.id_grupo,g.clave, a.nombre, 
                    a.creditos, l.nombre as lugar, g.cupo_libre
              FROM grupo AS g
                 JOIN periodo AS p ON g.id_periodo = p.id_periodo
                 JOIN lugar AS l ON g.id_lugar = l.id_lugar
                 JOIN actividad AS a ON g.id_actividad = a.id_actividad
                 JOIN departamento AS d ON a.id_depto = d.id_depto
                 JOIN carrera AS c on d.id_depto = c.id_depto
                 JOIN estudiante AS e on c.id_carrera = e.id_carrera
                    WHERE p.estado = "Actual"
                    AND g.estado = 1
                    AND d.id_depto = '.$dpte[0]->id_depto.'
                    GROUP BY g.id_grupo,g.clave, a.nombre, 
                    a.creditos, l.nombre, g.cupo_libre
                    ORDER BY g.id_grupo');
  
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
        // }else{
  
        //    return view('estudiante.inscrito')
        //          ->with('v', 11)
        //          ->with('tipos', $this->tipos());         
        // }
     }
  /**Retorna a la vista del listado de actividades complementarias filtradas po el tipo
   * de actividad seleccionado en el menú
   */
     public function f_actividades($tipo, Request $request){
  
        $now = date('Y-m-d');
        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
              ->where('estado', "Actual")->first();
  
        //if($now >= $roll->ini_inscripcion && $now <= $roll->fin_inscripcion){
  
           $tact = Mtipo::select('nombre')
           ->where('id_tipo', $tipo)
           ->get();
  
           $actividad = DB::select('SELECT g.id_grupo, g.clave, a.nombre,
                  a.creditos, l.nombre AS lugar, g.cupo_libre
           FROM grupo AS g
           LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
           LEFT JOIN lugar AS l ON g.id_lugar = l.id_lugar
           LEFT JOIN periodo AS p ON g.id_periodo = p.id_periodo
           WHERE p.estado = "Actual"
           AND a.restringida = 0
           AND g.estado = 1
           AND a.id_tipo ='.$tipo.'
           ORDER BY g.id_grupo');
  
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
        // }else{
  
        //    return view('estudiante.inscrito')
        //          ->with('v', 11)
        //          ->with('tipos', $this->tipos());         
        // }
  
     }
  /**Retorna a la vista del listado de actividades con las actividades ofertadas por las 
   * diferentes carreras, solo aquellas a las que puede inscribirse este estudiante, las
   * que no están restringidas
   */
     public function f_actividadesCar($id_car, Request $request){
  
        $now = date('Y-m-d');
        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
              ->where('estado', "Actual")->first();
  
        //if($now >= $roll->ini_inscripcion && $now <= $roll->fin_inscripcion){
  
           $cact = Mcarrera::select('nombre')
                 ->where('id_carrera', $id_car)
                 ->get();
  
           $actCar = DB::select('SELECT `g`.`id_grupo`, `g`.`clave`, 
              `a`.`nombre`, `a`.`creditos`, `l`.`nombre` AS lugar, `g`.`cupo_libre`
              FROM `grupo` AS `g`
              LEFT JOIN `actividad` AS `a` ON `g`.`id_actividad` = `a`.`id_actividad` 
              LEFT JOIN `lugar` AS `l` ON `g`.`id_lugar` = `l`.`id_lugar` 
              LEFT JOIN `departamento` AS `d` ON `a`.`id_depto` = `d`.`id_depto` 
              LEFT JOIN `carrera` AS `c` ON `d`.`id_depto` = `c`.`id_depto` 
              LEFT JOIN `periodo` AS `p` ON `g`.`id_periodo` = `p`.`id_periodo` 
              WHERE  `p`.`estado` = "Actual"
              AND  `g`.`estado` = 1
              AND  `c`.`estado` = 1
              AND `c`.`id_carrera` = '.$id_car);
  
           $inscrito = $this->inscrito($request->user()->id_persona);
  
           if(!$inscrito){
              return view('estudiante.actividades')
           ->with('actividades', $actCar)
           ->with('tnom', $cact)
           ->with('tipos', $this->tipos());
           }else{
              return view('estudiante.inscrito')
                 ->with('v', 00)
                 ->with('tipos', $this->tipos());
           }
        // }else{
  
        //    return view('estudiante.inscrito')
        //          ->with('v', 11)
        //          ->with('tipos', $this->tipos());         
        // }
  
     }
  /**Retorna a la vista donde se muestran los datos generales de la actividad
   * complementaria, donde el estudiante debe confirmar la solicitud de inscripción
   */
     public function f_inscribir($id_gru){
  
        $actividad = DB::select('SELECT g.clave, a.nombre AS actividad, 
        gr.nombre AS grado, p.nombre, p.apePat, p.apeMat, 
        l.nombre AS lugar, a.creditos, t.nombre AS tipo, g.id_grupo,
        d.nombre AS depto
        FROM grupo AS g 
        LEFT JOIN lugar AS l ON g.id_lugar = l.id_lugar
        LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
        LEFT JOIN departamento AS d ON a.id_depto = d.id_depto
        LEFT JOIN tipo AS t ON a.id_tipo = t.id_tipo
        LEFT JOIN persona AS p ON g.id_persona = p.id_persona
        LEFT JOIN empleado AS e ON p.id_persona = e.id_persona
        LEFT JOIN grado AS gr ON e.id_grado = gr.id_grado
        WHERE g.id_grupo = '.$id_gru);
  
        $horario = DB::select('SELECT ds.id_dia, ds.nombre, h.hora_inicio, h.hora_fin
        FROM grupo AS g
           LEFT JOIN horario AS h ON g.id_grupo = h.id_grupo
           LEFT JOIN dias_semana AS ds ON h.id_dia = ds.id_dia
        WHERE g.id_grupo = '.$id_gru);
  
        return view('estudiante.detInscrip')
        ->with('actividad', $actividad)
        ->with('horario', $horario)
        ->with('v', 00)
        ->with('tipos', $this->tipos());
     }
  /**Se confirma la solitud de inscripción del estudiante */
     public function f_solicitudIns($idgrupo, Request $request){
        
        $student = DB::select('SELECT id_estudiante
                    FROM estudiante
                    WHERE id_persona = '.$request->user()->id_persona);
  
        $hoy = date("Y-m-d");
  
        foreach($student as $s){
           $ins = Minscripcion::create(['id_estudiante' => $s->id_estudiante,
           'id_grupo' => $idgrupo, 'fecha' => $hoy, 
           'aprobada' => 0]);
        }  
        
        $cupo = DB::select('SELECT cupo_libre
              FROM grupo
              WHERE id_grupo = '.$idgrupo);
  
        foreach($cupo as $c){
           Mgrupo::where('id_grupo', $idgrupo)
           ->update(['cupo_libre' => $c->cupo_libre-1]);
        }
        
        return redirect()->to('Est');
     }
  /**Retorna a la vista donde se muestra la actividad que está cursando el estudiante */
     public function f_cursando(Request $request){
  
        $id_per = $request->user()->id_persona;
        
        $actividad = DB::select('SELECT g.clave, a.nombre AS actividad, 
        d.nombre AS depto, p.nombre, p.apePat, p.apeMat,
        gr.nombre AS grado, l.nombre AS lugar, a.creditos,
        t.nombre AS tipo, i.id_grupo
        FROM inscripcion AS i
           LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
           LEFT JOIN lugar AS l ON g.id_lugar = l.id_lugar
           LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
           LEFT JOIN departamento AS d ON a.id_depto = d.id_depto
           LEFT JOIN tipo AS t ON a.id_tipo = t.id_tipo
           LEFT JOIN persona AS p ON g.id_persona = p.id_persona
           LEFT JOIN empleado AS e ON p.id_persona = e.id_persona
           LEFT JOIN grado AS gr ON e.id_grado = gr.id_grado
           LEFT JOIN periodo AS pr on g.id_periodo = pr.id_periodo
        WHERE pr.estado = "Actual"
        AND i.aprobada = 1
        AND i.id_estudiante IN(SELECT es.id_estudiante
                             FROM estudiante AS es
                             WHERE es.id_persona = '.$id_per.')');
  
        $inscription = DB::select('SELECT i.id_inscripcion
        FROM inscripcion AS i
        LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
        LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
        LEFT JOIN periodo AS p ON g.id_periodo = p.id_periodo
        WHERE p.estado = "Actual"
        AND i.aprobada = 1
        AND e.id_persona = '.$id_per);
  
        $horario;
        
        foreach($inscription as $i){
              $horario = DB::select('SELECT ds.id_dia, ds.nombre, h.hora_inicio, h.hora_fin
           FROM inscripcion AS i 
           LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
           LEFT JOIN horario AS h ON g.id_grupo = h.id_grupo
           LEFT JOIN dias_semana AS ds ON h.id_dia = ds.id_dia
           WHERE i.id_inscripcion = '.$i->id_inscripcion);
        }
  
        if($actividad == null){
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
  
        $historial = DB::select('SELECT pr.nombre AS periodo, a.nombre AS actividad,
        a.creditos, ev.calificacion, nd.nombre AS evaluacion,
        ev.observaciones
        FROM evaluacion AS ev
           LEFT JOIN nivel_desempenio AS nd ON ev.id_desempenio = nd.id_desempenio
           LEFT JOIN inscripcion AS i ON ev.id_inscripcion = i.id_inscripcion
           LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
           LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
           LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
           LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
           WHERE e.id_persona = '.$id_per.'
        ORDER BY pr.id_periodo ASC');
  
        if($historial == null){
  
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
