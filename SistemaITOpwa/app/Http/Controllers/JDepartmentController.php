<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

use DB;
use Auth;
use App\Models\Mtipo;
use App\Models\Mlugar;
use App\Models\Mgrado;
use App\Models\Mgrupo;
use App\Models\Musers;
use App\Models\Mpuesto;
use App\Models\Mhorario;
use App\Models\Mpersona;
use App\Models\Mperiodo;
use App\Models\Mempleado;
use App\Models\Mactividad;
use App\Models\Mdepartamento;

class JDepartmentController extends Controller
{
    public function _construct() { $this->middleware('jefedepto');  }

    public function f_inicio(Request $request) {

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
        
        return view('jDepto.inicio')
        ->with('hora', $now)
        ->with('process', $processes)
        ->with('end', $endprocess);  

    }

/******************ACTIVIDADES****************************************************************** */
    public function f_deptoAct($pagina, Request $request) { 

        $now = date('Y-m-d');
        $roll = Mperiodo::select('ini_inscripcion')
            ->where('estado', "Actual")->first();
        $modificar = true;

        if($now > $roll->ini_inscripcion)
            $modificar = false;

        $id_per = $request->user()->id_persona;

        $depto = Mdepartamento::select('nombre')
            ->where('id_persona', $request->user()->id_persona)
            ->first();

        $actividades = DB::table('actividad as a')
            ->leftJoin('departamento as d', 'a.id_depto', '=', 'd.id_depto')
            ->leftJoin('tipo as t', 'a.id_tipo', '=', 't.id_tipo')
            ->leftJoin('persona as p', 'd.id_persona', '=', 'p.id_persona')
            ->select('a.id_actividad', 
                    'a.clave', 
                    'a.nombre', 
                    'a.creditos', 
                    'd.nombre AS depto', 
                    't.nombre AS tipo', 
                    'a.descripcion')
            ->when($id_per, function ($query, $id_per) {
                return $query->where('p.id_persona', $id_per)
                            ->where('a.estado', 1);
            })
            ->orderBy('a.id_actividad')
            ->paginate(10);

        return view('jDepto.actividad.actividades')
        ->with('actividades', $actividades)
        ->with('pnom', $depto)
        ->with('mod', $modificar);  
    }

    public function f_deptoA($search, $pagina, $user) { 

        $data = [mb_strtoupper("%".$search."%"), $user];

        $depto = Mdepartamento::select('nombre')
            ->where('id_persona', $request->user()->id_persona)
            ->first();

        $actividades = DB::table('actividad as a')
            ->leftJoin('departamento as d', 'a.id_depto', '=', 'd.id_depto')
            ->leftJoin('tipo as t', 'a.id_tipo', '=', 't.id_tipo')
            ->leftJoin('persona as p', 'd.id_persona', '=', 'p.id_persona')
            ->select('a.id_actividad', 
                    'a.clave', 
                    'a.nombre', 
                    'a.creditos', 
                    'd.nombre AS depto', 
                    't.nombre AS tipo', 
                    'a.descripcion')
            ->when($data, function ($query, $data) {
                return $query->where('p.id_persona', $data[1])
                            ->orWhere('a.nombre', 'LIKE', $data[0])
                            ->orWhere('a.clave', 'LIKE', $data[0])
                            ->where('a.estado', 1);
            })
            ->orderBy('a.id_actividad')
            ->paginate(10);

        return view('jDepto.actividad.actividades')
        ->with('actividades', $actividades)
        ->with('pnom', $depto);  
    }
/**Realiza el request del buscador de actividades y redirige a la función
 * f_deptoA()
 */
    public function f_searchact(Request $request) { 

        $search = $request->search;
        $per = $request->user()->id_persona;
        //return redirect()->to('JDepto/actividad/'.$search.'/1');  
        return $this->f_deptoA($search, 1, $per); 
    }
/**Retorna a la vista que contiene el formulario para el registro de una
 * nueva actividad complentaria
 */
    public function f_n_actividad(Request $request) { 

        $now = date('Y-m-d');
        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();

        if($now >= $roll->ini_inscripcion && $now <= $roll->fin_inscripcion){

            $id_per = $request->user()->id_persona;

            $depto = Mdepartamento::select('nombre')
            ->where('id_persona', $request->user()->id_persona)
            ->first();

            $periodo = Mperiodo::select('nombre')
            ->where('estado', "Actual")
            ->first();

            $tipos = Mtipo::get();

            return view('jDepto.actividad.nueva')
            ->with('depto', $depto)
            ->with('tipos', $tipos)
            ->with('periodo', $periodo);
        }else{
            return view('jDepto.procesos')
               ->with('v', 11);
        }

    }
/**Realiza el request del formulario para el registro de una nueva actividad
 * complementaria, valida los datos (tipo, estructura, longitud) y crea el
 * registro correspondiente
 */
    public function f_regAct(Request $request){

        $id_per = $request->user()->id_persona;

        $depto = Mdepartamento::select('id_depto')
        ->where('id_persona', $request->user()->id_persona)
        ->first();

        $periodo = Mperiodo::select('id_periodo')
        ->where('estado', "Actual")
        ->first();

        $clave = mb_strtoupper($request->clave);
        $nombre = mb_strtoupper($request->nombre);
        $creditos = $request->creditos;
        $tipo = $request->tipo;
        $descrip = mb_strtoupper($request->descripcion);
        $restrin = $request->restringida;

        Mactividad::create(['id_depto' => $depto->id_depto, 'id_tipo' => $tipo,
        'id_periodo' => $periodo->id_periodo, 'clave' => $clave, 'nombre' => $nombre,
        'creditos' => $creditos, 'descripcion' => $descrip, 
        'restringida' => $restrin, 'estado' => 1]);

        return redirect()->to('JDepto/actividad/1');
    }
/**Retorna a la vista de edición de la actividad complementaria recibida como parametro */
    public function f_e_actividad($id_act, Request $request) { 

        $now = date('Y-m-d');
        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
        ->where('estado', "Actual")->first();

        if($now >= $roll->ini_inscripcion && $now <= $roll->fin_inscripcion){
        
            $id_per = $request->user()->id_persona;

            $depto = Mdepartamento::select('nombre')
                ->where('id_persona', $request->user()->id_persona)
                ->first();

            $periodo = Mperiodo::select('nombre')
                ->where('estado', "Actual")
                ->first();

            $tipos = Mtipo::get();

            $actividad = DB::table('actividad as a')
            ->join('tipo as t', 'a.id_tipo', '=', 't.id_tipo')
            ->select('a.id_actividad', 'a.clave',
            'a.nombre', 'a.creditos',
            'a.id_tipo',
            't.nombre as tipo',
            'a.descripcion', 'a.restringida')
            ->where('id_actividad', $id_act)
            ->get();

            return view('jDepto.actividad.editar')
            ->with('actividad', $actividad)
            ->with('depto', $depto)
            ->with('tipos', $tipos)
            ->with('periodo', $periodo);
        }else{
            return view('jDepto.procesos')
            ->with('v', 11);
        }
            
        
    }
/**Realiza el request del formulario de edición de actividad complentaria y
 * realiza las actualizaciones correspondientes
 */
    public function f_editAct($id_act, Request $request){

        $clave = mb_strtoupper($request->clave);
        $nombre = mb_strtoupper($request->nombre);
        $creditos = $request->creditos;
        $tipo = $request->tipo;
        $descrip = mb_strtoupper($request->descripcion);
        $restrin = $request->restringida;

        Mactividad::where('id_actividad', $id_act)
        ->update(['id_tipo' => $tipo,
        'clave' => $clave, 'nombre' => $nombre,
        'creditos' => $creditos, 
        'restringida' => $restrin, 'descripcion' => $descrip]);

        return redirect()->to('JDepto/actividad/1');
    }

   /****************GRUPOS******************************************************** */
/**Retorna a la vista del listado de grupos de ese departamento, grupos 
 * pertenecientes al periodo "Actual"
 */
    public function f_grupos($pagina, Request $request) {

        $now = date('Y-m-d');
        $roll = Mperiodo::select('ini_inscripcion')
        ->where('estado', "Actual")->first();
        $modificar = true;
        if($now > $roll->ini_inscripcion)
            $modificar = false;
            
        $id_per = $request->user()->id_persona;

        $depto = Mdepartamento::select('nombre')
            ->where('id_persona', $request->user()->id_persona)
            ->first();

        $grupos = DB::table('grupo AS g')
            ->leftJoin('periodo AS p', 'g.id_periodo', '=', 'p.id_periodo')
            ->leftJoin('actividad AS a', 'g.id_actividad', '=', 'a.id_actividad')
            ->leftJoin('persona AS pe', 'g.id_persona', '=', 'pe.id_persona')
            ->leftJoin('lugar AS l', 'g.id_lugar', '=', 'l.id_lugar')
            ->join('departamento as d', 'a.id_depto', '=', 'd.id_depto')
            ->select('g.id_grupo', 
                    'g.cupo', 
                    'g.clave', 
                    'g.asistencias', 
                    'a.nombre AS actividad', 
                    'l.nombre AS lugar',
                    'd.id_depto',
                    DB::raw('CONCAT(pe.nombre, " ", pe.apePat, " ", pe.apeMat) AS responsable'))
            ->when($id_per, function ($query, $id_per) {
                return $query->where('p.estado', "Actual")
                            ->where('g.estado', 1)
                            ->where('d.id_persona', $id_per);
            })
            ->orderBy('g.id_grupo')
            ->paginate(10);

        return view('jDepto.grupo.grupos')
            ->with('grupos', $grupos)
            ->with('pnom', $depto)
            ->with('mod', true);   
    }
/**Retorna a la vista del listado de grupos de ese departamento pero con
 * la busqueda del grupo recibido en el parametro, grupos pertenecientes
 * al periodo "Actual"
 */
    public function f_grupo($search, $pagina, Request $request) {

        $now = date('Y-m-d');
        $roll = Mperiodo::select('ini_inscripcion')
            ->where('estado', "Actual")->first();
        $modificar = true;

        if($now > $roll->ini_inscripcion)
            $modificar = false;
            
        $data = [mb_strtoupper("%".$search."%"), $request->user()->id_persona];

        $depto = Mdepartamento::select('nombre')
            ->where('id_persona', $data[1])
            ->first();

        $grupos = DB::table('grupo AS g')
            ->leftJoin('periodo AS p', 'g.id_periodo', '=', 'p.id_periodo')
            ->leftJoin('actividad AS a', 'g.id_actividad', '=', 'a.id_actividad')
            ->leftJoin('persona AS pe', 'g.id_persona', '=', 'pe.id_persona')
            ->leftJoin('lugar AS l', 'g.id_lugar', '=', 'l.id_lugar')
            ->join('departamento as d', 'a.id_depto', '=', 'd.id_depto')
            ->select('g.id_grupo', 
                    'g.cupo', 
                    'g.clave', 
                    'g.asistencias', 
                    'a.nombre AS actividad', 
                    'l.nombre AS lugar',
                    'd.id_depto',
                    DB::raw('CONCAT(pe.nombre, " ", pe.apePat, " ", pe.apeMat) AS responsable'))
            ->when($data, function ($query, $data) {
                return $query->where('p.estado', "Actual")
                            ->where('g.estado', 1)
                            ->where('d.id_persona', $data[1])
                            ->where('g.clave', 'LIKE', $data[0])
                            ->orWhere('a.nombre', 'LIKE', $data[0]);
            })
            ->orderBy('g.id_grupo')
            ->paginate(10);

        return view('jDepto.grupo.grupos')
            ->with('grupos', $grupos)
            ->with('pnom', $depto)
            ->with('mod', true);
    }
/**Realiza el request del buscador de grupos y redirige a la función
 * f_grupo
 */
    public function f_searchgrupo(Request $request) { 

        $search = $request->search;
        return $this->f_grupo($search, 1);
        //return redirect()->to('JDepto/grupo/'.$search.'/1');   
    }
/**Retorna a la vista del formulario para el registro de un nuevo grupo */
    public function f_n_grupo(Request $request){

        $now = date('Y-m-d');
        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
        ->where('estado', "Actual")->first();

        if($now >= $roll->ini_inscripcion && $now <= $roll->fin_inscripcion){
        // 
            $id_per = $request->user()->id_persona;

            $periodo = Mperiodo::select('nombre')
                ->where('estado', "Actual")
                ->first();

            $actividad = DB::table('actividad as a')
                ->join('empleado as e', 'a.id_depto', '=', 'e.id_depto')
                ->join('persona as per', 'e.id_persona', '=', 'per.id_persona')
                ->join('departamento as d', 'e.id_depto', '=', 'd.id_depto')
                ->join('tipo as t', 'a.id_tipo', '=', 't.id_tipo')
                ->join('periodo as p', 'a.id_periodo', '=', 'p.id_periodo')
                ->select('a.id_actividad', 'a.clave',
                    'a.nombre', 'a.creditos',
                    'd.nombre as depto', 't.nombre as tipo',
                    'a.descripcion')
                ->where('per.id_persona', $id_per)
                ->where('p.estado', "Actual")
                ->groupby('clave', 'id_actividad', 'nombre', 'creditos', 'depto', 
                    't.nombre', 'descripcion')
                ->get();

            $persona = DB::select(
                'SELECT p.id_persona, g.nombre AS grado, p.nombre, p.apePat, p.apeMat
                FROM persona AS p
                LEFT JOIN empleado AS e ON p.id_persona = e.id_persona
                LEFT JOIN grado AS g ON e.id_grado = g.id_grado
                WHERE e.id_depto IN (SELECT id_depto
                                FROM empleado
                                WHERE id_persona = '.$id_per.')');
            
            $persona = DB::table('persona as p')
                ->join('empleado as e', 'p.id_persona', '=', 'e.id_persona')
                ->join('grado as g', 'e.id_grado', '=', 'g.id_grado')
                ->select('p.id_persona',
                        'g.nombre AS grado',
                        'p.nombre',
                        'p.apePat',
                        'p.apeMat')
                ->where('e.id_depto', DB::raw('SELECT id_depto
                                FROM empleado WHERE id_persona = '.$id_per))
                ->get();

            $lugar = Mlugar::get();

            return view('jDepto.grupo.nuevo')
            ->with('periodo', $periodo)
            ->with('actividades', $actividad)
            ->with('personas', $persona)
            ->with('lugares', $lugar);
        }else{
            return view('jDepto.procesos')
            ->with('v', 00);
        }
        
    }  
/**Realiza el request del formulario de registro de nuevo grupo, valida
 * los datos y realiza los registros correspondientes
 */
    public function f_regGrupo(Request $request){   

        $clave = mb_strtoupper($request->clave);
        $actividad = $request->actividad;
        $responsable = $request->responsable;
        $lugar = $request->lugar;
        $cupo = $request->cupo;
        $orden = $request->orden;

        $lun = $request->lunes;
        $lunf = $request->lunesf;
        $mar = $request->martes;
        $marf = $request->martesf;
        $mie = $request->miercoles;
        $mief = $request->miercolesf;
        $jue = $request->jueves;
        $juef = $request->juevesf;
        $vie = $request->viernes;
        $vief = $request->viernesf;
        $sab = $request->sabado;
        $sabf = $request->sabadof;

        $periodo = Mperiodo::select('id_periodo')
                ->where('estado', "Actual")
                ->first();

        $grupo = Mgrupo::create(['id_periodo' => $periodo->id_periodo, 'id_actividad' => $actividad,
        'id_persona' => $responsable, 'id_lugar' => $lugar,
        'clave' => $clave, 'cupo' => $cupo, 'orden' => $orden, 
        'estado' => 1]);

        if($lun != null){
            Mhorario::create(['id_grupo' => $grupo->id, 
                'id_dia' => 1, 'hora_inicio' => $lun,
                'hora_fin' => $lunf]);
        }

        if($mar != null){
            Mhorario::create(['id_grupo' => $grupo->id, 
                'id_dia' => 2, 'hora_inicio' => $mar,
                'hora_fin' => $marf]);
        }

        if($mie != null){
            Mhorario::create(['id_grupo' => $grupo->id, 
                'id_dia' => 3, 'hora_inicio' => $mie,
                'hora_fin' => $mief]);
        }

        if($jue != null){
            Mhorario::create(['id_grupo' => $grupo->id, 
                'id_dia' => 4, 'hora_inicio' => $jue,
                'hora_fin' => $juef]);
        }

        if($vie != null){
            Mhorario::create(['id_grupo' => $grupo->id, 
                'id_dia' => 5, 'hora_inicio' => $vie,
                'hora_fin' => $vief]);
        }

        if($sab != null){
            Mhorario::create(['id_grupo' => $grupo->id, 
                'id_dia' => 6, 'hora_inicio' => $sab,
                'hora_fin' => $sabf]);
        }

        return redirect()->to('JDepto/grupos/1');
    }
/**Retorna a la vista de edición del grupo con los datos generales del grupo
 * que recibe como parametro
 */
    public function f_e_grupo($id_gru, Request $request){

        $now = date('Y-m-d');
        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
        ->where('estado', "Actual")->first();

        if($now >= $roll->ini_inscripcion && $now <= $roll->fin_inscripcion){
        
            $id_per = $request->user()->id_persona;

            $periodo = Mperiodo::select('nombre')
                ->where('estado', "Actual")
                ->first();

            $actividad = DB::table('actividad as a')
            ->join('empleado as e', 'a.id_depto', '=', 'e.id_depto')
            ->join('persona as per', 'e.id_persona', '=', 'per.id_persona')
            ->join('departamento as d', 'e.id_depto', '=', 'd.id_depto')
            ->join('tipo as t', 'a.id_tipo', '=', 't.id_tipo')
            ->join('periodo as p', 'a.id_periodo', '=', 'p.id_periodo')
            ->select('a.id_actividad as id_actividad', 'a.clave as clave',
            'a.nombre as nombre', 'a.creditos as creditos',
            'd.nombre as depto', 't.nombre as tipo',
            'a.descripcion as descripcion')
            ->where('per.id_persona', $id_per)
            ->where('p.estado', "Actual")
            ->groupby('clave', 'id_actividad', 'nombre', 'creditos', 'depto', 't.nombre', 'descripcion')
            ->get();    

            $persona = DB::table('persona as p')
            ->join('empleado as e', 'p.id_persona', '=', 'e.id_persona')
            ->select('p.id_persona', 'p.nombre', 'p.apePat', 'p.apeMat')
            ->get();

            $lugar = Mlugar::get();

            $grupo = DB::table('grupo as g')
            ->join('periodo as p', 'g.id_periodo', '=', 'p.id_periodo')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('persona as pe', 'g.id_persona', '=', 'pe.id_persona')
            ->join('lugar as l', 'g.id_lugar', '=', 'l.id_lugar')
            ->select('g.id_grupo as id_grupo', 'g.cupo as cupo', 'g.clave as clave',
            'g.asistencias as asistencias', 'p.nombre as periodo',
            'g.id_periodo as id_periodo', 'g.id_actividad as id_actividad',
            'g.id_persona as id_persona', 'g.id_lugar as id_lugar',
            'a.nombre as actividad', 'pe.nombre as nomP', 'pe.apePat as paterno',
            'pe.apeMat as materno', 'l.nombre as lugar', 'g.orden as orden')
            ->where('id_grupo', $id_gru)
            ->get();

            $h1 = DB::select('SELECT h.hora_inicio, h.hora_fin
            FROM grupo AS g
                LEFT JOIN horario AS h ON g.id_grupo = h.id_grupo
                LEFT JOIN dias_semana AS ds ON h.id_dia = ds.id_dia
            WHERE ds.id_dia = 1
            AND h.estado = 1
            AND g.id_grupo = '.$id_gru);

            $h2 = DB::select('SELECT h.hora_inicio, h.hora_fin
            FROM grupo AS g
                LEFT JOIN horario AS h ON g.id_grupo = h.id_grupo
                LEFT JOIN dias_semana AS ds ON h.id_dia = ds.id_dia
            WHERE ds.id_dia = 2
            AND h.estado = 1
            AND g.id_grupo = '.$id_gru);

            $h3 = DB::select('SELECT h.hora_inicio, h.hora_fin
            FROM grupo AS g
                LEFT JOIN horario AS h ON g.id_grupo = h.id_grupo
                LEFT JOIN dias_semana AS ds ON h.id_dia = ds.id_dia
            WHERE ds.id_dia = 3
            AND h.estado = 1
            AND g.id_grupo = '.$id_gru);

            $h4 = DB::select('SELECT h.hora_inicio, h.hora_fin
            FROM grupo AS g
                LEFT JOIN horario AS h ON g.id_grupo = h.id_grupo
                LEFT JOIN dias_semana AS ds ON h.id_dia = ds.id_dia
            WHERE ds.id_dia = 4
            AND h.estado = 1
            AND g.id_grupo = '.$id_gru);

            $h5 = DB::select('SELECT h.hora_inicio, h.hora_fin
            FROM grupo AS g
                LEFT JOIN horario AS h ON g.id_grupo = h.id_grupo
                LEFT JOIN dias_semana AS ds ON h.id_dia = ds.id_dia
            WHERE ds.id_dia = 5
            AND h.estado = 1
            AND g.id_grupo = '.$id_gru);

            $h6 = DB::select('SELECT h.hora_inicio, h.hora_fin
            FROM grupo AS g
                LEFT JOIN horario AS h ON g.id_grupo = h.id_grupo
                LEFT JOIN dias_semana AS ds ON h.id_dia = ds.id_dia
            WHERE ds.id_dia = 6
            AND h.estado = 1
            AND g.id_grupo = '.$id_gru);

            return view('jDepto.grupo.editar')->with('grupo', $grupo)
                    ->with('periodo', $periodo)
                    ->with('actividades', $actividad)
                    ->with('personas', $persona)
                    ->with('lugares', $lugar)
                    ->with('hlun', $h1)->with('hmar', $h2)->with('hmie', $h3)
                    ->with('hjue', $h4)->with('hvie', $h5)->with('hsab', $h6);
        }else{
            return view('jDepto.procesos')
            ->with('v', 00);
        }
        
    }
/**Realiza el request de la edición del grupo, valida los datos y registra
 * las actualizaciones correspondientes
 */
    public function f_editGrupo($id_gru, Request $request){
        
        $clave = mb_strtoupper($request->clave);
        $actividad = $request->actividad;
        $responsable = $request->responsable;
        $lugar = $request->lugar;
        $cupo = $request->cupo;
        $orden = $request->orden;

        $lun = $request->lunes;         $lunf = $request->lunesf;
        $mar = $request->martes;        $marf = $request->martesf;
        $mie = $request->miercoles;     $mief = $request->miercolesf;
        $jue = $request->jueves;        $juef = $request->juevesf;
        $vie = $request->viernes;       $vief = $request->viernesf;
        $sab = $request->sabado;        $sabf = $request->sabadof;

        $oldcupo = Mgrupo::select('cupo', 'cupo_libre')->where('id_grupo', $id_gru)->first();

        if($oldcupo->cupo == $oldcupo->cupo_libre){
            Mgrupo::where('id_grupo', $id_gru)
            ->update(['id_actividad' => $actividad,
            'id_persona' => $responsable, 'id_lugar' => $lugar,
            'clave' => $clave, 'cupo' => $cupo, 
            'cupo_libre' => $cupo, 'orden' => $orden]);
        }else{
            Mgrupo::where('id_grupo', $id_gru)
            ->update(['id_actividad' => $actividad,
            'id_persona' => $responsable, 'id_lugar' => $lugar,
            'clave' => $clave, 'cupo' => $cupo, 'orden' => $orden]);
        }

        $haylun = 0; $haymar = 0; $haymie = 0; $hayjue = 0; $hayvie = 0; $haysab = 0;

        $horario = Mhorario::where('id_grupo', $id_gru)->where('estado', 1)->get();

            foreach($horario as $h){

                if($h->id_dia == 1){
                    if($lun != null){
                        Mhorario::where('id_grupo', $id_gru)
                        ->where('id_dia', 1)
                        ->update(['hora_inicio' => $lun,
                        'hora_fin' => $lunf]);
                    }else{
                        Mhorario::where('id_grupo', $id_gru)
                        ->where('id_dia', 1)
                        ->update(['estado' => 0]);
                    }
                    $haylun = 1;

                }elseif($h->id_dia == 2){
                    if($mar != null){
                        Mhorario::where('id_grupo', $id_gru)
                        ->where('id_dia', 2)
                        ->update(['hora_inicio' => $mar,
                            'hora_fin' => $marf]);
                    }else{
                        Mhorario::where('id_grupo', $id_gru)
                        ->where('id_dia', 2)
                        ->update(['estado' => 0]);
                    }
                    $haymar = 1;

                }elseif($h->id_dia == 3){
                    if($mie != null){
                        Mhorario::where('id_grupo', $id_gru)
                        ->where('id_dia', 3)
                        ->update(['hora_inicio' => $mie,
                            'hora_fin' => $mief]);
                    }else{
                        Mhorario::where('id_grupo', $id_gru)
                        ->where('id_dia', 3)
                        ->update(['estado' => 0]);
                    }
                    $haymie = 1;

                }elseif($h->id_dia == 4){
                    if($jue != null){
                        Mhorario::where('id_grupo', $id_gru)
                        ->where('id_dia', 4)
                        ->update(['hora_inicio' => $jue,
                            'hora_fin' => $juef]);
                    }else{
                        Mhorario::where('id_grupo', $id_gru)
                        ->where('id_dia', 4)
                        ->update(['estado' => 0]);
                    }
                    $hayjue = 1;

                }elseif($h->id_dia == 5){
                    if($vie != null){
                        Mhorario::where('id_grupo', $id_gru)
                        ->where('id_dia', 5)
                        ->update(['hora_inicio' => $vie,
                            'hora_fin' => $vief]);
                    }else{
                        Mhorario::where('id_grupo', $id_gru)
                        ->where('id_dia', 5)
                        ->update(['estado' => 0]);
                    }
                    $hayvie = 1;

                }elseif($h->id_dia == 6){
                    if($sab != null){
                        Mhorario::where('id_grupo', $id_gru)
                        ->where('id_dia', 6)
                        ->update(['hora_inicio' => $sab,
                            'hora_fin' => $sabf]);
                    }else{
                        Mhorario::where('id_grupo', $id_gru)
                        ->where('id_dia', 6)
                        ->update(['estado' => 0]);
                    }
                    $haysab = 1;
                }
            }

        
            if($haylun == 0){
                if($lun != null){
                    Mhorario::create(['id_grupo' => $id_gru, 
                        'id_dia' => 1, 'hora_inicio' => $lun,
                        'hora_fin' => $lunf]);
                }

            }

            if($haymar == 0){
                if($mar != null){
                    Mhorario::create(['id_grupo' => $id_gru, 
                        'id_dia' => 2, 'hora_inicio' => $mar,
                        'hora_fin' => $marf]);
                }
            }
            
            if($haymie == 0){
                if($mie != null){
                    Mhorario::create(['id_grupo' => $id_gru, 
                        'id_dia' => 3, 'hora_inicio' => $mie,
                        'hora_fin' => $mief]);
                }
            }
            
            if($hayjue == 0){
                if($jue != null){
                    Mhorario::create(['id_grupo' => $id_gru, 
                        'id_dia' => 4, 'hora_inicio' => $jue,
                        'hora_fin' => $juef]);
                }
            }
            
            if($hayvie == 0){
                if($vie != null){
                    Mhorario::create(['id_grupo' => $id_gru, 
                        'id_dia' => 5, 'hora_inicio' => $vie,
                        'hora_fin' => $vief]);
                }
            }
            
            if($haysab == 0){
                if($sab != null){
                    Mhorario::create(['id_grupo' => $id_gru, 
                        'id_dia' => 6, 'hora_inicio' => $sab,
                        'hora_fin' => $sabf]);
                }
            }

        return redirect()->to('JDepto/grupos/1');
    }

    /****************DATOS GENERALES ********************************************************* */
    public function f_perfil(Request $request){

        $id_per = $request->user()->id_persona;

        $depto = Mdepartamento::select('nombre')
            ->where('id_persona', $request->user()->id_persona)
            ->first();

        $puesto = Mpuesto::get();
        $grados = Mgrado::get();

        $persona = DB::table('persona as p')
        ->join('empleado as e', 'p.id_persona', '=', 'e.id_persona')
        ->join('departamento as d', 'e.id_depto', '=', 'd.id_depto')
        ->join('grado as g', 'e.id_grado', '=', 'g.id_grado')
        ->join('puesto as pu', 'e.id_puesto', '=', 'pu.id_puesto')
        ->select('p.id_persona', 'p.nombre', 
                'p.apePat as paterno', 'p.apeMat as materno',
                'p.curp', 'd.nombre as depto', 'g.nombre as grado',
                'pu.nombre as puesto',
                'e.id_grado', 'pu.nombre as puesto')
        ->where('p.id_persona', $id_per)
                ->get();

        return view('jDepto.perfil')->with('persona', $persona)
            ->with('depto', $depto)
            ->with('puestos', $puesto)
            ->with('grados', $grados)
            ->with('editar', 0);
    }

    public function f_editperfil(Request $request){

        $id_per = $request->user()->id_persona;

        $depto = Mdepartamento::select('nombre')
            ->where('id_persona', $request->user()->id_persona)
            ->first();
            
        $puesto = Mpuesto::get();
        $grados = Mgrado::get();

        $persona = DB::table('persona as p')
            ->join('empleado as e', 'p.id_persona', '=', 'e.id_persona')
            ->join('departamento as d', 'e.id_depto', '=', 'd.id_depto')
            ->join('grado as g', 'e.id_grado', '=', 'g.id_grado')
            ->join('puesto as pu', 'e.id_puesto', '=', 'pu.id_puesto')
            ->select('p.id_persona', 'p.nombre', 
                    'p.apePat as paterno', 'p.apeMat as materno',
                    'p.curp', 'd.nombre as depto', 'g.nombre as grado',
                    'pu.nombre as puesto',
                    'e.id_grado', 'pu.nombre as puesto')
            ->where('p.id_persona', $id_per)
                    ->get();

        return view('jDepto.perfil')->with('persona', $persona)
            ->with('depto', $depto)
            ->with('puestos', $puesto)
            ->with('grados', $grados)
            ->with('editar', 1);
    }

    public function f_updatePerfil(Request $request){

        $grado = $request->grado;
        $nombre = mb_strtoupper($request->nombre);
        $apePat = mb_strtoupper($request->apePat);
        $apeMat = mb_strtoupper($request->apeMat);
        $curp = mb_strtoupper($request->curp);
        $user = $nombre." ".$apePat." ".$apeMat;
        
        Mpersona::where('id_persona', $request->user()->id_persona)
            ->update(['nombre' => $nombre,
                'apePat' => $apePat,
                'apeMat' => $apeMat,
                'curp' => $curp]);
        
        Mempleado::where('id_persona', $request->user()->id_persona)
            ->update(['id_grado' => $grado]);

        Musers::where('id_persona', $request->user()->id_persona)
            ->update(['nombre' => $user,
                'usuario' => $curp]);

        return redirect()->to('JDepto/datgen');
    }

    public function f_passwd(Request $request) {
        
        $id_per = $request->user()->id_persona;
        
        return view('jDepto.editpasswd');  
    }

    public function f_editpasswd(Request $request){

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
                    location.href = "/JDepto/cambcontrasenia";
                 </script>
              <?php
           }
  
        }else{
           ?>
              <script>
                  alert('Contraseña actual incorrecta, intenta de nuevo.');
                  location.href = "/JDepto/cambcontrasenia";
              </script>
          <?php
        }
     }

    /*************************PERSONAL***************************************************************** */

    public function f_personal($pagina, Request $request) {

        $id_per = $request->user()->id_persona;

        $depto = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_persona', $request->user()->id_persona)
            ->first();

        $empleados = DB::table('persona as p')
            ->leftJoin('empleado as e', 'p.id_persona', '=', 'e.id_persona')
            ->leftJoin('departamento as d', 'e.id_depto', '=', 'd.id_depto')
            ->leftJoin('grado as g', 'e.id_grado', '=', 'g.id_grado')
            ->leftJoin('puesto as pu', 'e.id_puesto', '=', 'pu.id_puesto')
            ->select('p.id_persona',
                    'p.estado', 
                    'p.curp', 
                    'd.nombre as depto', 
                    'g.nombre as grado', 
                    'pu.nombre as puesto', 
                    DB::raw('CONCAT(p.nombre, " ", p.apePat, " ", p.apeMat) AS empleado'))
            ->when($depto, function ($query,$depto) {
                return $query->where('e.id_depto', $depto->id_depto)
                            ->where('p.estado', 1);
            })
            ->orderBy('p.id_persona')
            ->paginate(10);

        return view('jDepto.persona.personas')
            ->with('personas', $empleados)
            ->with('pnom', $depto);   
    }

    public function f_personalB($search, $pagina, $user) {

        $id_per = $user;

        $depto = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_persona', $user)
            ->first();

        $data = [mb_strtoupper("%".$search."%"), $depto->id_depto];
        
        $empleados = DB::table('persona as p')
            ->leftJoin('empleado as e', 'p.id_persona', '=', 'e.id_persona')
            ->leftJoin('departamento as d', 'e.id_depto', '=', 'd.id_depto')
            ->leftJoin('grado as g', 'e.id_grado', '=', 'g.id_grado')
            ->leftJoin('puesto as pu', 'e.id_puesto', '=', 'pu.id_puesto')
            ->select('p.id_persona',
                    'p.estado', 
                    'p.curp', 
                    'd.nombre as depto', 
                    'g.nombre as grado', 
                    'pu.nombre as puesto', 
                    DB::raw('CONCAT(p.nombre, " ", p.apePat, " ", p.apeMat) AS empleado'))
            ->when($data, function ($query,$data) {
                return $query->where('e.id_depto', $data[1])
                            ->where('p.estado', 1)
                            ->where('p.nombre', 'LIKE', $data[0])
                            ->orWhere('p.apePat', 'LIKE', $data[0])
                            ->orWhere('p.apeMat', 'LIKE', $search[0]);
            })
            ->orderBy('p.id_persona')
            ->paginate(10);

        return view('jDepto.persona.personas')
            ->with('personas', $empleados)
            ->with('pnom', $depto);   
    }

    public function f_searchper(Request $request) { 

        $search = $request->search;
        return $this->f_personalB($search, 1, $request->user()->id_persona);
        //return redirect()->to('JDepto/personal/'.$search.'/1');   
    }

    public function f_n_persona(Request $request){

        $id_per = $request->user()->id_persona;

        $depto = Mdepartamento::select('nombre')
            ->where('id_persona', $request->user()->id_persona)
            ->first();

        $puesto = Mpuesto::select('nombre')
            ->where('id_puesto', 3)
            ->first();

        $grados = Mgrado::get();

        return view('jDepto.persona.nueva')
        ->with('depto', $depto)
        ->with('puesto', $puesto)
        ->with('grados', $grados);
    }

    public function f_regEmp(Request $request){

        $id_per = $request->user()->id_persona;
        $depto = Mdepartamento::select('id_depto')
            ->where('id_persona', $request->user()->id_persona)
            ->first();
        $curp = mb_strtoupper($request->curp);
        $contraseña = bcrypt($request->curp);
        $nombre = mb_strtoupper($request->nombre);
        $apePat = mb_strtoupper($request->apePat);
        $apeMat = mb_strtoupper($request->apeMat);
        $nomUser = mb_strtoupper($request->nombre.' '.$request->apePat.' '.$request->apeMat);
        $tipo = 'Empleado';
        $grado = $request->grado;
        $hoy = date("Y-m-d");

        $persona = Mpersona::create(['nombre' => $nombre, 'apePat' => $apePat,
        'apeMat' => $apeMat, 'curp' => $curp, 'tipo' => $tipo, 'estado' => 1]);

        Mempleado::create(['id_persona' => $persona->id, 'id_depto' => $depto->id_depto, 
        'id_grado' => $grado, 'id_puesto' => 3]);

        Musers::create(['id_persona' => $persona->id, 'id_puesto' => 3,
        'nombre' => $nomUser, 'usuario' => $curp, 'password' => $contraseña,
        'fecha_registro' => $hoy, 'edo_sesion' => 0, 'estado' => 1]);


        return redirect()->to('JDepto/personal/1');
    }

    public function f_e_persona($id_per, Request $request){

        $depto = Mdepartamento::select('nombre')
            ->where('id_persona', $request->user()->id_persona)
            ->first();

        $puesto = Mpuesto::select('nombre')
            ->where('id_puesto', 3)
            ->first();

        $grados = Mgrado::get();

        $persona = DB::table('persona as p')
            ->join('empleado as e', 'p.id_persona', '=', 'e.id_persona')
            ->join('grado as g', 'e.id_grado', '=', 'g.id_grado')
            ->join('puesto as pu', 'e.id_puesto', '=', 'pu.id_puesto')
            ->select('p.id_persona', 'p.nombre', 
                    'p.apePat as paterno', 'p.apeMat as materno',
                    'p.curp', 'g.nombre as grado',
                    'pu.nombre as puesto',
                    'e.id_grado')
            ->where('p.id_persona', $id_per)
            ->get();

        return view('jDepto.persona.editar')
            ->with('persona', $persona)
            ->with('depto', $depto)
            ->with('puesto', $puesto)
            ->with('grados', $grados);
    }

    public function f_editEmp($id_emp, Request $request){

        $grado = $request->grado;
        $nombre = mb_strtoupper($request->nombre);
        $apePat = mb_strtoupper($request->apePat);
        $apeMat = mb_strtoupper($request->apeMat);
        $curp = mb_strtoupper($request->curp);
        $nomUser = $nombre.' '.$apePat.' '.$apeMat;

        Mpersona::where('id_persona', $id_emp)
            ->update(['nombre' => $nombre, 'apePat' => $apePat,
            'apeMat' => $apeMat, 'curp' => $curp]);

        Mempleado::where('id_persona', $id_emp)
            ->update(['id_grado' => $grado]);

        Musers::where('id_persona', $id_emp)
            ->update(['nombre' => $nomUser, 
                'usuario' => $curp]);

        return redirect()->to('JDepto/personal/1');
    }

    public function f_h_mem(Request $request){

        $id_per = $request->user()->id_persona;

        $hoja = DB::select('SELECT d.hoja_mem
            FROM departamento AS d
            LEFT JOIN empleado AS e ON d.id_depto = e.id_depto
            WHERE e.id_persona = '.$id_per);

        return view('jDepto.documentos.hoja_mem')
            ->with('hoja', $hoja);
    }

    public function f_savehmem(Request $request){

        if($request->hasFile('hojamem')){
            $proof = $request->file('hojamem')->store('membretadas');

            $dptP = Mempleado::select('id_depto')
                ->where('id_persona', $request->user()->id_persona)
                ->first();

                Mdepartamento::where('id_depto', $dptP->id_depto)
                ->update(['hoja_mem' => $proof]);
        }

        return redirect('JDepto/hmembretada');
    }

    public function f_grupos_constancias(Request $request, $origin) {
        
        $id_per = $request->user()->id_persona;
        $pnom = Mperiodo::select('nombre')->where('estado', "Actual")->first();

        $grupos = DB::table('grupo as g')
            ->join('periodo as p', 'g.id_periodo', '=', 'p.id_periodo')
            ->join('persona as pe', 'g.id_persona', '=', 'pe.id_persona')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('empleado as e', 'a.id_depto', '=', 'e.id_depto')
            ->join('lugar as l', 'g.id_lugar', '=', 'l.id_lugar')
            ->select('g.id_grupo as id_grupo', 
                'g.cupo as cupo', 
                'g.clave as clave',
                'g.asistencias as asistencias', 
                'p.nombre as periodo',
                'a.nombre as actividad', 
                'pe.nombre as nomP', 
                'pe.apePat as paterno',
                'pe.apeMat as materno', 
                'l.nombre as lugar')
            ->where('e.id_persona', $id_per)
            ->where('p.estado', "Actual")
            ->get();

        return view('jDepto.grupo.constancia')
        ->with('grupos', $grupos)
        ->with('origin', $origin)
        ->with('pnom', $pnom);   
    }

	public function f_lista_alumnos($id_gru, $origin){

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

        return view('jDepto.grupo.lista')
            ->with('grupo', $grupo)
            ->with('alumnos', $alumnosGrupo)
            ->with('tipo', $origin);
	}

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

    public function logoutJD(Request $request){

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect("IniciarSesion");
     }

     public function downloadConstancia($n_control) {

        $d = DB::table('estudiante as e')
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

        //foreach($data as $d){
            $jefeDpto = DB::table('persona as p')
                    ->join('departamento as d', 'd.id_persona', '=', 'p.id_persona')
                    ->join('empleado as e', 'e.id_persona', '=', 'p.id_persona')
                    ->join('grado as g', 'g.id_grado', '=', 'e.id_grado')
                    ->where('d.id_persona', '=', $d->jefeId)
                    ->select('p.nombre as nombre', 'p.apePat as apePat',
                             'p.apeMat as apeMat', 'g.nombre as grado')
                    ->first(); 
                //}

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
             ->loadView('ProfRes.constancia', array('data' => $d,
                        'profesor' => $profesor,
                        'jefe' => $jefeDpto,
                        'day' => $now->format('d'),
                        'jefeDpto' => $jefe,
                        'month' => $month,
                        'year' => $now->format('Y')
                    ));
    
        return $pdf->download('constancia.pdf');
    }

    public function f_estudianteH(){

        return view('jDepto.histestudiante')
            ->with('search', 0);
    }

    public function f_estudianteHist(Request $request){

        $control = "".$request->search."";
        
        $inscription = DB::table('inscripcion as i')
            ->leftJoin('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
            ->leftJoin('evaluacion as ev', 'i.id_inscripcion', '=', 'ev.id_inscripcion')
            ->leftJoin('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->leftJoin('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->leftJoin('periodo as pr', 'g.id_periodo', '=', 'pr.id_periodo')
            ->select('pr.nombre AS periodo', 
                    'i.fecha', 
                    'a.nombre AS actividad', 
                    'ev.constancia', 
                    'ev.id_evaluacion', 
                    'g.clave', 
                    'i.aprobada', 
                    'pr.id_periodo')
            ->where('e.num_control', $control)->get();
            // ->paginate(10);

        $student = DB::table('estudiante as e')
            ->leftJoin('persona as p', 'e.id_persona', '=', 'p.id_persona')
            ->leftJoin('carrera as c', 'e.id_carrera', '=', 'c.id_carrera')
            ->select('p.nombre', 
                    'p.apePat', 
                    'p.apeMat', 
                    'e.num_control', 
                    'e.semestre', 
                    'c.nombre AS carrera')
            ->where('e.num_control', $control)->get();

        return view('jDepto.histestudiante')
            ->with('search', 1)
            ->with('student', $student)
            ->with('inscripcion', $inscription);
    }
}
