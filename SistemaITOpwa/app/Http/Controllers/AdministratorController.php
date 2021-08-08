<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Codedge\Fpdf\Facades\Fpdf;

/**Declaración de los modelos a ocupar dentro del controlador */
use App\Models\Mlogs;           use App\Models\Mgrupo;
use App\Models\Mtipo;           
use App\Models\Mgrado;          use App\Models\Mlugar;
use App\Models\Musers;          use App\Models\Mpuesto;
use App\Models\Mhorario;        use App\Models\Mcarrera;
use App\Models\Mperiodo;        use App\Models\Mpersona;
use App\Models\Mempleado;       use App\Models\Mactividad;
use App\Models\Mestudiante;     use App\Models\Minscripcion;
use App\Models\Mdepartamento;   use App\Models\Mfechas_inhabiles;
use App\Models\Mcriterios_evaluacion;   use App\Models\Mhorarios_impresos;
use App\Models\Minscripcion_outime;
use Mail;       use URL;        use DB;

class AdministratorController extends Controller
{
    //constructor del controlador
    public function _construct() { 
        $this->middleware('auth');
    }
    /**Retorna los tipos de actividades que existen en la bd
    se ocupan para la barra de navegación, de esta manera
    funciona dinamicamente*/
    public function tipos(){

        $tipos = Mtipo::select('id_tipo', 'nombre')->get();

        foreach($tipos as $t){
            $t->nombre = ucwords(mb_strtolower($t->nombre));
        }

        return $tipos;
    }

    public function finalizarPeriodo(){
        
        $today = date("Y-m-d");

        $finish = Mperiodo::select('fin')->where('estado', "Actual")->first();
        
        if($today > $finish->fin){

            // Mperiodo::where('estado', "Anterior")
            // ->update(['estado' => "Finalizado"]);
    
            // Mperiodo::where('estado', "Actual")
            //     ->update(['estado' => "Anterior"]);
    
            // Mperiodo::where('estado', "Siguiente")
            //     ->update(['estado' => "Actual"]);
    
            // DB::delete('DELETE FROM horarios_impresos WHERE id_grupo <> 0');
        }

        return true;
    }

    public function procesoActual(){

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

        $procesos[0] = $processes; $procesos[1] = $endprocess;

        return $procesos;
    }
    /**Reddirecciona a la página de inicio de sesión del usuario */
    public function f_inicio(Request $request) { 

        $now = date_create('America/Mexico_City')->format('H');

        $this->finalizarPeriodo();
        $procesos = $this->procesoActual();

        return view('CoordAC.inicio')
            ->with('hora', $now)
            ->with('process', $procesos[0])
            ->with('end', $procesos[1])
            ->with('tipos', $this->tipos());
    }
    /**Gestiona todas las eliminaciones de elementos que 
     * puede realizar este usuario*/
    public function f_eliminaciones($origin, $objeto){
        
        $url = "/delete/".$origin."/".$objeto;

        return view('CoordAC.mimodal',
            ['nombre' => strtoupper($origin),
            'miurl' => $url,
            'modal' => false,
            'tipos' => $this->tipos()]);
    }
    /**Gestiona todas las ediciones de elementos que 
     * puede realizar este usuario*/
    public function f_ediciones($origin, $objeto){
        
        $url = "/update/".$origin."/".$objeto;

        return view('CoordAC.mimodal',
            ['nombre' => strtoupper($origin),
            'miurl' => $url,
            'modal' => true,
            'tipos' => $this->tipos()]);
    }

/*----------------------------------------------------------------------------------------------------*/

    /**Redirecciona a la vista donde se enlistan todas las
     * actividades vigentes en el sistema */
    public function f_actividades($pagina) { 

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('inicio', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->inicio || $now > $roll->fin_inscripcion)
            $modificar = false;
    
        $peri = Mperiodo::select('id_periodo', 'nombre')
        ->where('estado', "Actual")
            ->get();

        $actividad = DB::select('SELECT a.id_actividad, a.clave, 
                a.nombre, a.creditos, d.nombre AS depto, 
                t.nombre AS tipo, a.descripcion 
        FROM actividad AS a
        LEFT JOIN  departamento AS  d ON  a.id_depto =  d.id_depto
        LEFT JOIN  tipo AS  t ON  a.id_tipo =  t.id_tipo
        WHERE a.estado IN(SELECT estado FROM actividad WHERE estado = 1)
        ORDER BY a.id_actividad');

        $actividadP = DB::select('SELECT a.id_actividad, a.clave, 
                a.nombre, a.creditos, d.nombre AS depto, 
                t.nombre AS tipo, a.descripcion 
            FROM actividad AS a
            LEFT JOIN  departamento AS  d ON  a.id_depto =  d.id_depto
            LEFT JOIN  tipo AS  t ON  a.id_tipo =  t.id_tipo
            WHERE a.estado IN(SELECT estado FROM actividad WHERE estado = 1)
            LIMIT '.(($pagina-1)*10).', 10');

        $pag = 0;

            foreach($actividad as $a){
                $pag = $pag + 1;
            }

            $pag = ceil($pag / 10);

        return view('CoordAC.actividad.actividades')
        ->with('actividades', $actividadP)
        ->with('pnom', $peri)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 00)
        ->with('mod', true)
        ->with('tipos', $this->tipos()); 
    }

    public function f_actividad($search, $pagina) { 

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('inicio', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->inicio || $now > $roll->fin_inscripcion)
            $modificar = false;
  
        $peri = Mperiodo::select('id_periodo', 'nombre')
        ->where('estado', "Actual")
               ->get();

        $actividadt = DB::select('SELECT a.id_actividad, a.clave, 
                a.nombre, a.creditos, d.nombre AS depto, 
                t.nombre AS tipo, a.descripcion 
        FROM actividad AS a
        LEFT JOIN  departamento AS  d ON  a.id_depto =  d.id_depto
        LEFT JOIN  tipo AS  t ON  a.id_tipo =  t.id_tipo
        WHERE a.clave LIKE "%'.$search.'%" OR a.nombre LIKE "%'.$search.'%"
        AND a.estado IN(SELECT estado FROM actividad WHERE estado = 1)
        ORDER BY a.id_actividad ASC');

        $actividad = DB::select('SELECT a.id_actividad, a.clave, 
                a.nombre, a.creditos, d.nombre AS depto, 
                t.nombre AS tipo, a.descripcion 
        FROM actividad AS a
        LEFT JOIN  departamento AS  d ON  a.id_depto =  d.id_depto
        LEFT JOIN  tipo AS  t ON  a.id_tipo =  t.id_tipo
        WHERE a.clave LIKE "%'.$search.'%" OR a.nombre LIKE "%'.$search.'%"
        AND a.estado IN(SELECT estado FROM actividad WHERE estado = 1)
        LIMIT '.(($pagina-1)*10).', 10');

        $pag = 0;

        foreach($actividadt as $a){
            $pag = $pag + 1;
        }

        $pag = ceil($pag / 10);

        return view('CoordAC.actividad.actividades')
        ->with('actividades', $actividad)
        ->with('pnom', $peri)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 01)
        ->with('bus', $search)
        ->with('mod', true)
        ->with('tipos', $this->tipos()); 
    }

    public function f_searchact(Request $request) { 

        $search = mb_strtoupper($request->search);
        //return redirect()->to('CoordAC/actividad/'.$search.'/1');
        return $this->f_actividad($search, 1);   
    }

    public function f_depto() { 

        $depto = DB::select('SELECT d.id_depto, d.nombre
            FROM departamento AS d
            LEFT JOIN actividad AS a ON d.id_depto = a.id_depto
            WHERE a.estado IN(SELECT estado FROM actividad WHERE estado = 1)
            AND d.estado IN(SELECT estado FROM departamento WHERE estado = 1)
            GROUP BY d.id_depto, d.nombre');    

        $pag = 0;

        foreach($depto as $d){
            $pag = $pag + 1;
        }

        $pag = ceil($pag / 10);

        return view('CoordAC.actividad.actdepto')
        ->with('deptos', $depto)
        ->with('pag', $pag)
        ->with('pa', 1)
        ->with('tipos', $this->tipos()); 
    }

    public function f_actdepto($id_dep, $pagina) { 

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('inicio', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->inicio || $now > $roll->fin_inscripcion)
            $modificar = false;

        $depto = Mdepartamento::select('nombre', 'id_depto')
               ->where('id_depto', $id_dep)
               ->get();
        
        $actividad = DB::select('SELECT a.id_actividad, a.clave, 
                a.nombre, a.creditos, d.nombre AS depto, 
                t.nombre AS tipo, a.descripcion
        FROM actividad AS a 
        LEFT JOIN departamento AS d ON a.id_depto = d.id_depto
        LEFT JOIN tipo AS t on a.id_tipo = t.id_tipo
        WHERE a.estado IN(SELECT estado FROM actividad WHERE estado = 1)
        AND d.id_depto = '.$id_dep);    

        $actividadD = DB::select('SELECT a.id_actividad, a.clave, 
                a.nombre, a.creditos, d.nombre AS depto, 
                t.nombre AS tipo, a.descripcion, d.id_depto
        FROM actividad AS a 
        LEFT JOIN departamento AS d ON a.id_depto = d.id_depto
        LEFT JOIN tipo AS t on a.id_tipo = t.id_tipo
        WHERE a.estado IN(SELECT estado FROM actividad WHERE estado = 1)
        AND d.id_depto = '.$id_dep.' LIMIT '.(($pagina-1)*10).', 10');

        $pag = 0;

        foreach($actividad as $d){
            $pag = $pag + 1;
        }

        $pag = ceil($pag / 10);

        return view('CoordAC.actividad.actividades')
        ->with('actividades', $actividadD)
        ->with('pnom', $depto)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 10)
        ->with('mod', true)
        ->with('tipos', $this->tipos());  
    }

    public function f_actipo($id_tip, $pagina) { 

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('inicio', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->inicio || $now > $roll->fin_inscripcion)
            $modificar = false;

        $tipo = Mtipo::select('nombre', 'id_tipo')
               ->where('id_tipo', $id_tip)
               ->get();
        
        
        $actividad = DB::select('SELECT a.id_actividad, a.clave,
               a.nombre, a.creditos, d.nombre AS depto, 
               t.nombre AS tipo, a.descripcion
        FROM actividad AS a 
        LEFT JOIN departamento AS d ON a.id_depto = d.id_depto
        LEFT JOIN tipo AS t ON a.id_tipo = t.id_tipo
        WHERE a.estado IN(SELECT estado FROM actividad WHERE estado = 1)
        AND t.id_tipo = '.$id_tip);

        $actividadT = DB::select('SELECT a.id_actividad, a.clave,
                a.nombre, a.creditos, d.nombre AS depto, 
                t.nombre AS tipo, a.descripcion, t.id_tipo
        FROM actividad AS a 
        LEFT JOIN departamento AS d ON a.id_depto = d.id_depto
        LEFT JOIN tipo AS t ON a.id_tipo = t.id_tipo
        WHERE a.estado IN(SELECT estado FROM actividad WHERE estado = 1)
        AND t.id_tipo = '.$id_tip.' LIMIT '.(($pagina-1)*10).', 10');

        $pag = 0;

        foreach($actividad as $d){
            $pag = $pag + 1;
        }

        $pag = ceil($pag / 10);

        return view('CoordAC.actividad.actividades')
        ->with('actividades', $actividadT)
        ->with('pnom', $tipo)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 11)
        ->with('mod', true)
        ->with('tipos', $this->tipos());  
    }

    public function f_n_actividad() { 

        $depto = Mdepartamento::get();
        $tipos = Mtipo::get();
        $periodo = Mperiodo::select('nombre')
        ->where('estado', "Actual")
        ->first();

        return view('CoordAC.actividad.nueva')
        ->with('deptos', $depto)
        ->with('tipos', $tipos)
        ->with('periodo', $periodo)
        ->with('tipos', $this->tipos());   
    }

    public function f_regAct(Request $request){

        $clave = mb_strtoupper($request->clave);
        $nombre = mb_strtoupper($request->nombre);
        $creditos = $request->creditos;
        $depto = $request->depto;
        $tipo = $request->tipo;
        $descrip = mb_strtoupper($request->descripcion);
        $restringida = $request->restringida;

        $peri = Mperiodo::select('id_periodo')
            ->where('estado', "Actual")
                ->first();

        Mactividad::create(['id_depto' => $depto, 'id_tipo' => $tipo,
        'id_periodo' => $peri->id_periodo, 'clave' => $clave, 'nombre' => $nombre,
        'creditos' => $creditos, 'descripcion' => $descrip, 
        'restringida' => $restringida, 'estado' => 1]);

        return redirect()->to('CoordAC/actividades/1');
    }

    public function f_e_actividad($id_act) { 

        $depto = Mdepartamento::get();
        $tipos = Mtipo::get();

        $actividad = DB::select('SELECT a.id_actividad, a.clave,
                a.nombre, a.creditos, a.id_tipo,
                d.nombre AS depto, t.nombre AS tipo,
                a.descripcion, a.id_depto
        FROM actividad AS a 
        LEFT JOIN departamento AS d ON a.id_depto = d.id_depto
        LEFT JOIN tipo AS t ON a.id_tipo = t.id_tipo
        WHERE a.id_actividad = '.$id_act);

        return view('CoordAC.actividad.editar')
        ->with('actividad', $actividad)
        ->with('deptos', $depto)
        ->with('tipos', $tipos)
        ->with('tipos', $this->tipos());
    }

    public function f_editAct($id_act, Request $request){

        $clave = mb_strtoupper($request->clave);
        $nombre = mb_strtoupper($request->nombre);
        $creditos = $request->creditos;
        $depto = $request->depto;
        $tipo = $request->tipo;
        $descrip = mb_strtoupper($request->descripcion);

        Mactividad::where('id_actividad', $id_act)
        ->update(['id_depto' => $depto, 'id_tipo' => $tipo,
        'clave' => $clave, 'nombre' => $nombre,
        'creditos' => $creditos, 'descripcion' => $descrip, 
        'estado' => 1]);

        return redirect()->to('CoordAC/actividades/1');
    }

    public function f_deleteact($id_delete){

        Mactividad::where('id_actividad', $id_delete)
            ->update(['estado' => 0]);

        return $this->f_actividades(1);
    }

/*----------------------------------------------------------------------------------------------------*/

    public function f_grupos($pagina) {
        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('inicio', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->inicio || $now > $roll->fin_inscripcion)
            $modificar = false;

            $gruposP = DB::select('SELECT g.id_grupo, g.cupo_libre, g.clave,
                g.asistencias, p.nombre AS periodo,
                a.nombre AS actividad, pe.nombre AS nomP, 
                pe.apePat AS paterno, pe.apeMat AS materno, 
                l.nombre AS lugar, d.id_depto
            FROM grupo AS g 
            LEFT JOIN periodo AS p ON g.id_periodo = p.id_periodo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN persona AS pe ON g.id_persona = pe.id_persona
            LEFT JOIN lugar AS l ON g.id_lugar = l.id_lugar
            JOIN departamento AS d ON a.id_depto = d.id_depto
            WHERE p.estado = "Actual"
            AND g.estado IN(SELECT estado FROM grupo WHERE estado = 1)
            LIMIT '.(($pagina-1)*10).', 10');

            $grupos = DB::select('SELECT g.id_grupo, g.cupo_libre, g.clave,
                g.asistencias, p.nombre AS periodo,
                a.nombre AS actividad, pe.nombre AS nomP, 
                pe.apePat AS paterno, pe.apeMat AS materno, 
                l.nombre AS lugar
            FROM grupo AS g 
            LEFT JOIN periodo AS p ON g.id_periodo = p.id_periodo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN persona AS pe ON g.id_persona = pe.id_persona
            LEFT JOIN lugar AS l ON g.id_lugar = l.id_lugar
            JOIN departamento AS d ON a.id_depto = d.id_depto
            WHERE p.estado = "Actual"
            AND g.estado IN(SELECT estado FROM grupo WHERE estado = 1)');


        $peri = Mperiodo::select('id_periodo', 'nombre')
        ->where('estado', "Actual")
               ->get();
        
       $dept = 1;

       $pag = 0;
        foreach($grupos as $g){
            $pag = $pag + 1;
        }
        $pag = ceil($pag / 10);
       /** */

        return view('CoordAC.grupo.grupos')
        ->with('grupos', $gruposP)
        ->with('pnom', $peri)
        ->with('dept', $dept)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 00)
        ->with('mod', true)
        ->with('tipos', $this->tipos());   
    }

    public function f_gruposB($search, $pagina) {

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('inicio', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->inicio || $now > $roll->fin_inscripcion)
            $modificar = false;

        $gruposP = DB::select('SELECT g.id_grupo, g.cupo_libre, g.clave,
            g.asistencias, p.nombre AS periodo,
            a.nombre AS actividad, pe.nombre AS nomP, 
            pe.apePat AS paterno, pe.apeMat AS materno, 
            l.nombre AS lugar, d.id_depto
        FROM grupo AS g 
        LEFT JOIN periodo AS p ON g.id_periodo = p.id_periodo
        LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
        LEFT JOIN persona AS pe ON g.id_persona = pe.id_persona
        LEFT JOIN lugar AS l ON g.id_lugar = l.id_lugar
        JOIN departamento AS d ON a.id_depto = d.id_depto
        WHERE p.estado = "Actual"
        AND g.estado IN(SELECT estado FROM grupo WHERE estado = 1)
        AND g.clave LIKE "'.$search.'%" 
        OR a.nombre LIKE "%'.$search.'%"
        OR pe.nombre LIKE "%'.$search.'%"
        OR pe.apePat LIKE "%'.$search.'%"
        AND g.estado IN(SELECT estado FROM grupo WHERE estado = 1)');

            $grupos = DB::select('SELECT g.id_grupo, g.cupo_libre, g.clave,
                g.asistencias, p.nombre AS periodo,
                a.nombre AS actividad, pe.nombre AS nomP, 
                pe.apePat AS paterno, pe.apeMat AS materno, 
                l.nombre AS lugar, d.id_depto
            FROM grupo AS g 
            LEFT JOIN periodo AS p ON g.id_periodo = p.id_periodo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN persona AS pe ON g.id_persona = pe.id_persona
            LEFT JOIN lugar AS l ON g.id_lugar = l.id_lugar
            JOIN departamento AS d ON a.id_depto = d.id_depto
            WHERE p.estado = "Actual"
            AND g.estado IN(SELECT estado FROM grupo WHERE estado = 1)
            AND g.clave LIKE "'.$search.'%" 
            OR a.nombre LIKE "%'.$search.'%"
            OR pe.nombre LIKE "%'.$search.'%"
            OR pe.apePat LIKE "%'.$search.'%"
            LIMIT '.(($pagina-1)*10).', 10');
        

        $peri = Mperiodo::select('id_periodo', 'nombre')
        ->where('estado', "Actual")
               ->get();
        
       $dept = 1;

       $pag = 0;
        foreach($gruposP as $g){
            $pag = $pag + 1;
        }
        $pag = ceil($pag / 10);
       /** */

        return view('CoordAC.grupo.grupos')
        ->with('grupos', $grupos)
        ->with('pnom', $peri)
        ->with('dept', $dept)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 01)
        ->with('bus', $search)
        ->with('mod', true)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchgru(Request $request) { 

        $search = mb_strtoupper($request->search);
        //return redirect()->to('CoordAC/grupos/'.$search.'/1');
        return $this->f_gruposB($search, 1);  
    }

    public function f_n_grupo(Request $request, $id_dep){

        $periodo = Mperiodo::select('id_periodo', 'nombre')
        ->where('estado', "Actual")
        ->get();

        $actividad = DB::select('SELECT a.id_actividad, a.clave, 
                a.nombre, a.creditos
        FROM actividad AS a
        LEFT JOIN  departamento AS  d ON  a.id_depto =  d.id_depto
        WHERE a.estado IN(SELECT estado FROM actividad WHERE estado = 1)
        AND a.id_depto = '.$id_dep);
        
        /*id_periodo 
            IN (SELECT id_periodo
                FROM periodo
                WHERE estado = "Actual")
            AND a.id_depto = '.$id_dep);*/

        $persona = DB::table('persona AS p')
        ->join('empleado AS e', 'p.id_persona', '=', 'e.id_persona')
        ->join('grado AS g', 'e.id_grado', '=', 'g.id_grado')
        ->select('p.id_persona', 'g.nombre AS grado', 'p.nombre', 'p.apePat', 'p.apeMat')
        ->where('e.id_depto', $id_dep)
        ->get();

        // Solucion
        /*if($request->ajax()){
            return $persona->pluck("nombre", "id_persona");
        }*/

        $lugar = Mlugar::get();

        $deptos = Mdepartamento::get();

        return view('CoordAC.grupo.nuevo')
        ->with('periodos', $periodo)
        ->with('actividades', $actividad)
        ->with('personas', $persona)
        ->with('lugares', $lugar)
        ->with('deptos', $deptos)
        ->with('tipos', $this->tipos());
    }

    public function f_n_g_d($id_dep){

        $periodo = Mperiodo::select('id_periodo', 'nombre')
        ->where('estado', "Actual")
        ->get();

        $actividad = DB::select('SELECT a.id_actividad, a.clave, 
                a.nombre, a.creditos, d.nombre AS depto,  
                t.nombre AS tipo, a.descripcion 
        FROM actividad AS a
        LEFT JOIN  departamento AS  d ON  a.id_depto =  d.id_depto
        LEFT JOIN  tipo AS  t ON  a.id_tipo =  t.id_tipo
        WHERE a.estado IN(SELECT estado FROM actividad WHERE estado = 1) '); 
                /*IN (SELECT id_periodo
                FROM periodo
                WHERE estado = "Actual")');*/

        $persona = DB::table('persona AS p')
        ->join('empleado AS e', 'p.id_persona', '=', 'e.id_persona')
        ->select('p.id_persona', 'p.nombre', 'p.apePat', 'p.apeMat')
        ->where('e.id_depto', $id_dep)
        ->where('p.estado', 1)
        ->get();

        $lugar = Mlugar::get();

        $deptos = Mdepartamento::get();

        return view('CoordAC.grupo.nuevo')
        ->with('periodos', $periodo)
        ->with('actividades', $actividad)
        ->with('personas', $persona)
        ->with('lugares', $lugar)
        ->with('deptos', $deptos)
        ->with('tipos', $this->tipos());
    }

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

        $peri = Mperiodo::select('id_periodo')
                                ->where('estado', "Actual")->first();

        $grupo = Mgrupo::create(['id_periodo' => $peri->id_periodo, 
            'id_actividad' => $actividad, 'id_persona' => $responsable, 
            'id_lugar' => $lugar, 'clave' => $clave, 'cupo' => $cupo, 
            'cupo_libre' => $cupo, 'orden' => $orden, 'estado' => 1]);

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

        return redirect()->to('CoordAC/grupos/1');
    }
    
    public function f_e_grupo($id_gru, $dpt){

        $periodo = Mperiodo::select('id_periodo', 'nombre')
        ->where('estado', "Actual")
        ->get();

        $actividad = DB::select('SELECT a.id_actividad, a.clave, 
                a.nombre, a.creditos, a.descripcion,
                d.nombre AS depto, t.nombre AS tipo
        FROM actividad AS a
        LEFT JOIN departamento AS d ON a.id_depto = d.id_depto
        LEFT JOIN tipo AS t ON a.id_tipo = t.id_tipo
        WHERE a.estado IN(SELECT estado FROM actividad WHERE estado = 1)
        AND d.id_depto = '.$dpt);
        /*IN (SELECT id_periodo
        FROM periodo
        WHERE estado = "Actual")*/

        $persona = DB::select('SELECT p.id_persona, p.nombre,
                p.apePat, p.apeMat, g.nombre AS grado
        FROM persona AS p
        JOIN empleado AS e ON p.id_persona = e.id_persona
        JOIN grado AS g ON e.id_grado = g.id_grado
        JOIN departamento AS d ON e.id_depto = d.id_depto
        WHERE p.estado IN(SELECT estado FROM persona WHERE estado = 1)
        AND d.id_depto = '.$dpt);

        $lugar = Mlugar::get();

        $grupo = DB::select('SELECT g.id_grupo, g.cupo, g.clave, g.asistencias, 
                p.nombre as periodo, g.id_periodo, g.cupo_libre,
                g.id_actividad, g.id_lugar, g.id_persona, 
                a.nombre as actividad, pe.nombre as nomP, 
                pe.apePat as paterno, pe.apeMat as materno, 
                l.nombre as lugar, g.orden, gr.nombre AS grado
        FROM grupo AS g 
        LEFT JOIN periodo AS p ON g.id_periodo = p.id_periodo 
        LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad 
        LEFT JOIN persona AS pe ON g.id_persona = pe.id_persona 
        LEFT JOIN lugar AS l ON g.id_lugar = l.id_lugar
        JOIN empleado AS e ON pe.id_persona = e.id_persona
        JOIN grado AS gr ON e.id_grado = gr.id_grado
        WHERE g.id_grupo = '.$id_gru);

        $deptos = Mdepartamento::get();

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

        return view('CoordAC.grupo.editar')->with('grupo', $grupo)
                ->with('periodos', $periodo)
                ->with('actividades', $actividad)
                ->with('personas', $persona)
                ->with('lugares', $lugar)
                ->with('deptos', $deptos)
                ->with('hlun', $h1)->with('hmar', $h2)->with('hmie', $h3)
                ->with('hjue', $h4)->with('hvie', $h5)->with('hsab', $h6)
                ->with('tipos', $this->tipos());
    }

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

        $oldcupo = Mgrupo::select('cupo', 'cupo_libre')->where('id_grupo', $id_gru)->get();

        foreach($oldcupo as $c){
            
            if($c->cupo == $c->cupo_libre){
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
        

        return redirect()->to('CoordAC/grupos/1');
    }

    public function f_deletegru($id_delete){

        Mgrupo::where('id_grupo', $id_delete)
            ->update(['estado' => 0]);

        return redirect()->to('CoordAC/grupos/1');
    
    }
        
        
/*----------------------------------------------------------------------------------------------------*/

    public function f_estudiantes($pagina) { 
        
        $now = date('Y-m-d');
        $modificar = true;
        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion', 'ini_evaluacion')
        ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now >= $roll->ini_evaluacion)
            $modificar = false;
        
        $outime = 2;
        $roll->fin_inscripcion < $now ? $outime = true : $outime = false;

            $estudiantesP = DB::select('SELECT e.id_estudiante, e.num_control AS ncontrol,
                p.nombre, p.apePat, p.apeMat, c.nombre AS carrera, 
                e.semestre, p.curp, e.id_persona, p.estado, d.id_depto
            FROM persona AS p
            JOIN estudiante AS e ON p.id_persona = e.id_persona
            JOIN carrera AS c ON e.id_carrera = c.id_carrera
            JOIN departamento AS d ON c.id_depto = d.id_depto
            WHERE p.estado IN(SELECT estado FROM persona WHERE estado = 1)
            AND p.tipo = "Estudiante"');

            $estudiantes = DB::select('SELECT e.id_estudiante, e.num_control AS ncontrol,
                p.nombre, p.apePat, p.apeMat, c.nombre AS carrera, 
                e.semestre, p.curp, e.id_persona, p.estado, d.id_depto
            FROM persona AS p
            JOIN estudiante AS e ON p.id_persona = e.id_persona
            JOIN carrera AS c ON e.id_carrera = c.id_carrera
            JOIN departamento AS d ON c.id_depto = d.id_depto
            WHERE p.tipo = "Estudiante"
            AND p.estado IN(SELECT estado FROM persona WHERE estado = 1)
            LIMIT '.(($pagina-1)*10).', 10');

            $pag = 0;
            foreach($estudiantesP as $g){
                $pag = $pag + 1;
            }
            $pag = ceil($pag / 10);

        return view('CoordAC.estudiante.estudiantes')
        ->with('estudiantes', $estudiantes)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 00)
        ->with('mod', true)
        ->with('outime', $outime)
        ->with('tipos', $this->tipos()); 
    }

    public function f_estudiantesB($search, $pagina) { 
        
        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

            $estudiantesP = DB::select('SELECT e.id_estudiante, e.num_control AS ncontrol,
                p.nombre, p.apePat, p.apeMat, c.nombre AS carrera, 
                e.semestre, p.curp, e.id_persona, p.estado, d.id_depto
            FROM persona AS p
            JOIN estudiante AS e ON p.id_persona = e.id_persona
            JOIN carrera AS c ON e.id_carrera = c.id_carrera
            JOIN departamento AS d ON c.id_depto = d.id_depto
            WHERE p.tipo = "Estudiante"
            AND p.estado IN(SELECT estado FROM persona WHERE estado = 1)
            AND e.num_control LIKE "%'.$search.'%" 
           
            OR c.nombre LIKE "%'.$search.'%"');
            // OR p.nombre LIKE "%'.$search.'%"
            // OR p.apePat LIKE "%'.$search.'%"
            $estudiantes = DB::select('SELECT e.id_estudiante, e.num_control AS ncontrol,
                p.nombre, p.apePat, p.apeMat, c.nombre AS carrera, 
                e.semestre, p.curp, e.id_persona, p.estado, d.id_depto
            FROM persona AS p
            JOIN estudiante AS e ON p.id_persona = e.id_persona
            JOIN carrera AS c ON e.id_carrera = c.id_carrera
            JOIN departamento AS d ON c.id_depto = d.id_depto
            WHERE p.tipo = "Estudiante"
            AND p.estado IN(SELECT estado FROM persona WHERE estado = 1)
            AND e.num_control LIKE "%'.$search.'%" 

            OR c.nombre LIKE "%'.$search.'%"
            LIMIT '.(($pagina-1)*10).', 10');

            $pag = 0;
            foreach($estudiantesP as $g){
                $pag = $pag + 1;
            }
            $pag = ceil($pag / 10);

        return view('CoordAC.estudiante.estudiantes')
        ->with('estudiantes', $estudiantes)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 01)
        ->with('bus', $search)
        ->with('mod', true)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchest(Request $request) { 

        $search = mb_strtoupper($request->search);
        //return redirect()->to('CoordAC/estudiantes/'.$search.'/1');
        return $this->f_estudiantesB($search, 1);
    }
            
    public function f_n_estudiante() { 

        $carreras = Mcarrera::select('id_carrera', 'nombre')
            ->where('estado', 1)->get();

        $semestres = ['1', '2', '3', '4', '5', '6', '7', '8', 
        '9', '10','11', '12'];

            return view('CoordAC.estudiante.nuevo')
            ->with('carreras', $carreras)
            ->with('semestres', $semestres)
            ->with('tipos', $this->tipos());   
    }

    public function f_regEst(Request $request){

        $nControl = $request->nControl;
        $contraseña = bcrypt($request->nControl);
        $nombre = mb_strtoupper($request->nombre);
        $apePat = mb_strtoupper($request->apePat);
        $apeMat = mb_strtoupper($request->apeMat);
        $nomUser = mb_strtoupper($request->nombre.' '.$request->apePat.' '.$request->apeMat);
        $carrera = $request->carrera;
        $semestre = $request->semestre;
        $email = $request->email;
        $curp = mb_strtoupper($request->curp);
        $hoy = date("Y-m-d");

        $existe = false;
        $estudiantes = Mestudiante::select('num_control', 'email')->get();
        foreach($estudiantes as $e){
            if($e->num_control == $nControl || $e->email == $email)
                $existe = true;
        }

        if($existe){
            ?><script>
                alert("Número de control ya registrado, por favor verifica los datos.");
                location.href = "/CoordAC/nuevoEst";
            </script><?php
        }else{
            $persona = Mpersona::create(['nombre' => $nombre, 'apePat' => $apePat,
            'apeMat' => $apeMat, 'curp' => $curp, 'tipo' => "Estudiante", 'estado' => 1]);

            Mestudiante::create(['id_persona' => $persona->id, 'id_carrera' => $carrera, 
            'num_control' => $nControl, 'email' => $email, 'semestre' => $semestre]);

            Musers::create(['id_persona' => $persona->id, 'id_puesto' => 6,
            'nombre' => $nomUser, 'usuario' => $email, 'password' => $contraseña,
            'fecha_registro' => $hoy, 'edo_sesion' => 0, 'estado' => 1]);

            return redirect()->to('/CoordAC/estudiantes/1');
        }
    }

    public function f_e_estudiante($id_est)  {
           
        $estudiante = DB::table('persona AS p')
        ->join('estudiante AS e', 'p.id_persona', '=', 'e.id_persona')
        ->join('carrera AS c', 'e.id_carrera', '=', 'c.id_carrera')
        ->select('e.id_estudiante', 'e.num_control AS ncontrol',
        'p.nombre', 'p.apePat', 'p.apeMat', 'e.email',
        'c.nombre AS carrera', 'e.semestre', 'p.curp',
        'e.id_persona', 'e.id_carrera')
        ->where('e.id_persona', $id_est)
        ->get();

        $semestres = ['1', '2', '3', '4', '5', '6', '7', '8', 
        '9', '10', '11', '12'];
        $carreras = Mcarrera::get();

            return view('CoordAC.estudiante.editar')
            ->with('estudiante',$estudiante)
                ->with('semestres', $semestres)
                ->with('carreras', $carreras)
                ->with('tipos', $this->tipos());
    }
    
    public function f_editEst($id_est, Request $request){

        $nControl = $request->nControl;
        $nombre = mb_strtoupper($request->nombre);
        $apePat = mb_strtoupper($request->apePat);
        $apeMat = mb_strtoupper($request->apeMat);
        $carrera = $request->carrera;
        $semestre = $request->semestre;
        $curp = mb_strtoupper($request->curp);
        $email = mb_strtolower($request->email);
        $nomUser = mb_strtoupper($request->nombre.' '.$request->apePat.' '.$request->apeMat);

        $persona = Mpersona::where('id_persona', $id_est)
        ->update(['nombre' => $nombre, 'apePat' => $apePat,
        'apeMat' => $apeMat, 'curp' => $curp]);

        Mestudiante::where('id_persona', $id_est)
        ->update(['id_carrera' => $carrera, 
        'num_control' => $nControl, 'email' => $email, 'semestre' => $semestre]);

        Musers::where('id_persona', $id_est)
        ->update(['nombre' => $nomUser, 'usuario' => $nControl]);


        return redirect()->to('CoordAC/estudiantes/1');
    }

    public function f_deleteest($id_delete){

        Mpersona::where('id_persona', $id_delete)
            ->update(['estado' => 0]);

        return redirect()->to('CoordAC/estudiantes/1');
    }
/*----------------------------------------------------------------------------------------------------*/
    
    public function f_reportes(Request $request) { 

        $periodoI = $request->input('periodo');
        $actividadI = $request->input('actividad');

        $periodo = DB::select('SELECT id_periodo, nombre
            FROM periodo
            WHERE estado = "Actual"
            OR estado = "Anterior"
            OR estado = "Finalizado"');

        $actividad = DB::table('actividad AS a')
            ->select('a.nombre as nombre', 'a.id_actividad as id_actividad')
            ->get();
        
        if(isset($periodoI) == False) {
            $periodoI = $periodo[0]->id_periodo;
            $actividadI = $actividad[0]->id_actividad;
        }

        $res = DB::table('periodo as p')
                ->join('actividad as a','a.id_periodo', '=', 'p.id_periodo')
                ->join('grupo as g', 'g.id_actividad', '=', 'a.id_actividad')
                ->join('inscripcion as i', 'i.id_grupo', '=', 'g.id_grupo')
                ->join('evaluacion as e', 'e.id_inscripcion', '=', 'i.id_inscripcion')
                ->select('e.id_desempenio as id_desempenio', 'p.nombre as periodo', 'a.nombre as actividad')
                ->where('p.id_periodo', '=', $periodoI)
                ->where('a.id_actividad', '=', $actividadI)
                ->get();
        
        
        return view('CoordAC.reportes')
            ->with('tipos', $this->tipos())
            ->with('periodo', $periodo)
            ->with('data', $res)
            ->with('p', $periodoI)
            ->with('a', $actividadI)
            ->with('actividad', $actividad);
  
    }

    
/*----------------------------------------------------------------------------------------------------*/

    public function f_carreras($search) { 

        if($search == "0"){
            $carreras = DB::select('SELECT c.id_carrera, 
                        c.nombre, d.nombre AS depto
            FROM carrera AS c
            LEFT JOIN departamento AS d ON c.id_depto = d.id_depto
            WHERE c.estado = 1');
        }else{
            $carreras = DB::select('SELECT c.id_carrera, 
                    c.nombre, d.nombre AS depto
            FROM carrera AS c
            LEFT JOIN departamento AS d ON c.id_depto = d.id_depto
            WHERE c.estado = 1
            AND c.nombre LIKE "%'.$search.'%"');
        }

        return view('CoordAC.carrera.carreras')
            ->with('carreras', $carreras)
            ->with('tipos', $this->tipos());   
    }

    public function f_searchcar(Request $request) { 

        $search = mb_strtoupper($request->buscar);
        //return redirect()->to('CoordAC/carreras/'.$search);
        return $this->f_carreras($search);
    }

    public function f_n_carrera() { 

        $deptos = Mdepartamento::where('estado', 1)->get();

        return view('CoordAC.carrera.nueva')
        ->with('deptos', $deptos)
        ->with('tipos', $this->tipos());   
    }

    public function f_regCar(Request $request){

        $carrera = mb_strtoupper($request->nombreCarr);
        $depto = $request->depto;
        $tipo = $request->tipo;
        if($tipo == 1)
            $tipo = 'INGENIERÍA ';
        else 
            $tipo = 'LICENCIATURA ';
        
        $carrera = $tipo.$carrera;

        //return $carrera;

        Mcarrera::create(['id_depto' => $depto, 'nombre' => $carrera, 
        'estado' => 1]);

        return redirect()->to('CoordAC/carreras/0');
    }

    public function f_e_carrera($id_car) { 

        $carrera = DB::table('carrera AS c')
        ->join('departamento AS d', 'd.id_depto', '=', 'c.id_depto')
        ->select('c.id_carrera', 'c.nombre', 'd.nombre AS depto')
        ->where('c.id_carrera', $id_car)
        ->get();

        return view('CoordAC.carrera.editar')
        ->with('carrera', $carrera)
        ->with('tipos', $this->tipos());   
    }

    public function f_editCar($id_car, Request $request){

        $nombre = mb_strtoupper($request->nombre);
        //$depto = $request->depto;

        Mcarrera::where('id_carrera', $id_car)
        ->update(['nombre' => $nombre]);

        return redirect()->to('CoordAC/carreras/0');
    }

    public function f_deletecar($carrera){

        Mcarrera::where('id_carrera', $carrera)
            ->update(['estado' => 0]);

        return redirect()->to('CoordAC/carreras/0');
    }

/*----------------------------------------------------------------------------------------------------*/

    public function f_critEva($search) {
        
        if($search == "0"){
            $critEval = DB::select('SELECT id_crit_eval,
                nombre, descripcion
                FROM criterios_evaluacion
                WHERE estado = 1');
        }
        else{
            $critEval = DB::select('SELECT id_crit_eval,
                nombre, descripcion
                FROM criterios_evaluacion
                WHERE estado = 1
                AND nombre LIKE "%'.$search.'%"');
        }
        
        return view('CoordAC.critEval.c_evaluacion')
        ->with('criterios', $critEval)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchcrit(Request $request) { 

        $search = mb_strtoupper($request->buscar);
        //return redirect()->to('CoordAC/critEvaluacion/'.$search);
        return $this->f_critEva($search);
    }

    public function f_n_critEva() { 
        return view('CoordAC.critEval.nuevo')
        ->with('tipos', $this->tipos());   
    }

    public function f_regCritE(Request $request){

        $nombre = mb_strtoupper($request->nomCritE);
        $descrip = mb_strtoupper($request->desCritE);

        Mcriterios_evaluacion::create(['nombre' => $nombre,
        'descripcion' => $descrip, 'estado' => 1]);

        return redirect()->to('CoordAC/critEvaluacion/0');
    }

    public function f_e_critEva($id_crit) { 
        $critEval = Mcriterios_evaluacion::where('id_crit_eval', $id_crit)->get();
        return view('CoordAC.critEval.editar')
        ->with('criterio', $critEval)
        ->with('tipos', $this->tipos());  
    }

    public function f_editCritE($id_critE, Request $request){

        $nombre = mb_strtoupper($request->nombre);
        $descrip = mb_strtoupper($request->descripcion);

        Mcriterios_evaluacion::where('id_crit_eval', $id_critE)
        ->update(['nombre' => $nombre,
        'descripcion' => $descrip]);

        return redirect()->to('CoordAC/critEvaluacion/0');
    }

    public function f_deletecrit($criterio){

        Mcriterios_evaluacion::where('id_crit_eval', $criterio)
            ->update(['estado' => 0]);

        return redirect()->to('CoordAC/critEvaluacion/0');
    }

/*----------------------------------------------------------------------------------------------------*/

    public function f_departamentos($pagina){

 
            $departamentos = DB::select('SELECT d.id_depto, d.nombre AS depto, 
            g.nombre AS grado, p.nombre, p.apePat, p.apeMat
            FROM departamento AS d 
            LEFT JOIN persona AS p ON d.id_persona = p.id_persona
            LEFT JOIN empleado AS e ON e.id_persona = p.id_persona
            LEFT JOIN grado AS g ON e.id_grado = g.id_grado
            WHERE d.estado = 1
            LIMIT '.(($pagina-1)*10).', 10');

            $departamentosT = DB::select('SELECT d.id_depto, d.nombre AS depto, 
            g.nombre AS grado, p.nombre, p.apePat, p.apeMat
            FROM departamento AS d 
            LEFT JOIN persona AS p ON d.id_persona = p.id_persona
            LEFT JOIN empleado AS e ON e.id_persona = p.id_persona
            LEFT JOIN grado AS g ON e.id_grado = g.id_grado
            WHERE d.estado = 1');
        
        $pag = 0;
        foreach($departamentosT as $g){
            $pag = $pag + 1;
        }
        $pag = ceil($pag / 10);

        return view('CoordAC.depto.departamentos')
        ->with('departamentos', $departamentos)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 00)
        ->with('tipos', $this->tipos());   
    }

    public function f_departamento($search, $pagina){

        
            $departamentosT = DB::select('SELECT d.id_depto, d.nombre AS depto, 
            g.nombre AS grado, p.nombre, p.apePat, p.apeMat
            FROM departamento AS d 
            LEFT JOIN persona AS p ON d.id_persona = p.id_persona
            LEFT JOIN empleado AS e ON e.id_persona = p.id_persona
            LEFT JOIN grado AS g ON e.id_grado = g.id_grado
            WHERE d.estado = 1
            AND d.nombre LIKE "%'.$search.'%"');
        
            $departamentos = DB::select('SELECT d.id_depto, d.nombre AS depto, 
            g.nombre AS grado, p.nombre, p.apePat, p.apeMat
            FROM departamento AS d 
            LEFT JOIN persona AS p ON d.id_persona = p.id_persona
            LEFT JOIN empleado AS e ON e.id_persona = p.id_persona
            LEFT JOIN grado AS g ON e.id_grado = g.id_grado
            WHERE d.estado = 1
            AND d.nombre LIKE "%'.$search.'%" 
            LIMIT '.(($pagina-1)*10).', 10');
        
        $pag = 0;
        foreach($departamentosT as $g){
            $pag = $pag + 1;
        }
        $pag = ceil($pag / 10);

        return view('CoordAC.depto.departamentos')
        ->with('departamentos', $departamentos)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 01)
        ->with('bus', $search)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchdpt(Request $request) { 

        $search = mb_strtoupper($request->search);
        //return redirect()->to('CoordAC/departamentos/'.$search.'/1');
        return $this->f_departamento($search, 1);
    }

    public function f_n_depto() { 

        $jefes = DB::select('SELECT p.id_persona, g.nombre AS grado, 
            p.nombre, p.apePat, p.apeMat
            FROM persona AS p
            LEFT JOIN empleado AS e ON p.id_persona = e.id_persona
            LEFT JOIN grado AS g ON e.id_grado = g.id_grado
            WHERE e.id_puesto = 2
            AND p.estado = 1');
        
        return view('CoordAC.depto.nuevo')
        ->with('jefes', $jefes)
        ->with('tipos', $this->tipos());   
    }

    public function f_regDepto(Request $request){

        $nombre = mb_strtoupper($request->nomDepto);
        $jefe = $request->persona;

        Mdepartamento::create(['id_persona' => $jefe, 'nombre' => $nombre, 'estado' => 1]);

        return redirect()->to('CoordAC/departamentos/1');
    }
    
    public function f_e_depto($id_dep) { 

        $depto = DB::select('SELECT d.id_depto, d.nombre AS depto, 
        g.nombre AS grado, p.nombre, p.apePat, p.apeMat
        FROM departamento AS d 
        LEFT JOIN persona AS p ON d.id_persona = p.id_persona
        LEFT JOIN empleado AS e ON e.id_persona = p.id_persona
        LEFT JOIN grado AS g ON e.id_grado = g.id_grado
        WHERE d.estado = 1
        AND d.id_depto = '.$id_dep);

        $jefes = DB::select('SELECT p.id_persona, g.nombre AS grado, 
            p.nombre, p.apePat, p.apeMat
            FROM persona AS p
            LEFT JOIN empleado AS e ON p.id_persona = e.id_persona
            LEFT JOIN grado AS g ON e.id_grado = g.id_grado
            WHERE e.id_puesto <> 7
            AND e.id_puesto <> 5
            AND p.estado = 1');

        return view('CoordAC.depto.editar')
        ->with('depto', $depto)
        ->with('jefes', $jefes)
        ->with('tipos', $this->tipos());   
    }

    public function f_editDepto($id_dep, Request $request){

        $nombre = mb_strtoupper($request->nombre);
        $newjefe = $request->newjefe;
        $url = 'CoordAC/editDepto'.$id_dep;

        $asignado = Mdepartamento::where('id_persona', $newjefe)->first();

        if($asignado == null){

            if($newjefe == null){
                Mdepartamento::where('id_depto', $id_dep)
                    ->update(['nombre' => $nombre]);
            }else{
                $puesto = Mempleado::select('id_puesto')
                ->where('id_persona', $newjefe)->first();
                $anterior = Mdepartamento::select('id_persona')
                    ->where('id_depto', $id_dep)->first();
                $puestoa = Mempleado::select('id_puesto')
                    ->where('id_persona', $anterior->id_persona)
                    ->first();

                if($puesto->id_puesto == 1){
                    if($id_dep == 11){
                        Mdepartamento::where('id_depto', $id_dep)
                            ->update(['nombre' => $nombre,
                                'id_persona' => $newjefe]);

                        if($puestoa->id_puesto == 2){
                            Mempleado::where('id_persona', $anterior->id_persona)
                                ->update(['id_puesto' => 9]);
                        }elseif($puestoa->id_puesto == 8){
                            Mempleado::where('id_persona', $anterior->id_persona)
                                ->update(['id_puesto' => 3]);
                        }
                                
                        return redirect()->to('/CoordAC/departamentos/1');
                    }else{
                        Mempleado::where('id_persona', $newjefe)
                            ->update(['id_puesto' => 1]);

                        Mdepartamento::where('id_depto', $id_dep)
                            ->update(['nombre' => $nombre,
                                'id_persona' => $newjefe]);

                        if($puestoa->id_puesto == 2){
                            Mempleado::where('id_persona', $anterior->id_persona)
                                ->update(['id_puesto' => 9]);
                        }elseif($puestoa->id_puesto == 8){
                            Mempleado::where('id_persona', $anterior->id_persona)
                                ->update(['id_puesto' => 3]);
                        }

                        return redirect()->to('/CoordAC/departamentos/1');
                    }
                }elseif($puesto->id_puesto == 3){
                    if($id_dep == 11){
                        ?><script>
                            alert('Empleado no válido para este departamento.');
                            location.href = "/CoordAC/departamentos/1";
                        </script><?php
                    }else{
                        Mempleado::where('id_persona', $newjefe)
                        ->update(['id_puesto' => 8]);

                        Mdepartamento::where('id_depto', $id_dep)
                            ->update(['nombre' => $nombre,
                                'id_persona' => $newjefe]);

                        if($puestoa->id_puesto == 2){
                            Mempleado::where('id_persona', $anterior->id_persona)
                                ->update(['id_puesto' => 9]);
                        }elseif($puestoa->id_puesto == 8){
                            Mempleado::where('id_persona', $anterior->id_persona)
                                ->update(['id_puesto' => 3]);
                        }

                        return redirect()->to('/CoordAC/departamentos/1');
                    }
                }elseif($puesto->id_puesto == 4){
                    if($id_dep == 11){
                        ?><script>
                            alert('Empleado no válido para este departamento.');
                            location.href = "/CoordAC/departamentos/1";
                        </script><?php
                    }else{
                        Mempleado::where('id_persona', $newjefe)
                            ->update(['id_puesto' => 2]);
                        
                        Mdepartamento::where('id_depto', $id_dep)
                            ->update(['nombre' => $nombre,
                                'id_persona' => $newjefe]);

                        if($puestoa->id_puesto == 2){
                            Mempleado::where('id_persona', $anterior->id_persona)
                                ->update(['id_puesto' => 9]);
                        }elseif($puestoa->id_puesto == 8){
                            Mempleado::where('id_persona', $anterior->id_persona)
                                ->update(['id_puesto' => 3]);
                        }

                        return redirect()->to('/CoordAC/departamentos/1');
                    }
                }
            }
        } else{
            ?><script>
                alert('Esta persona ya es jefe de otro departamento.');
                location.href = "/CoordAC/departamentos/1";
            </script><?php
        }
    }

    public function f_deletedpt($dpto){

        Mdepartamento::where('id_depto', $dpto)
            ->update(['estado' => 0]);

        return redirect()->to('CoordAC/departamentos/1');
    }

/*----------------------------------------------------------------------------------------------------*/

    public function f_grados($pagina) {


        $grados = DB::select('SELECT id_grado, nombre, significado
            FROM grado
            WHERE estado = 1
            LIMIT '.(($pagina-1)*10).', 10');

        $gradosT = DB::select('SELECT id_grado, nombre, significado
            FROM grado
            WHERE estado = 1');

        $pag = 0;
        foreach($gradosT as $g){
            $pag = $pag + 1;
        }
        $pag = ceil($pag / 10);

        return view('CoordAC.grado.grados')
        ->with('grados', $grados)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 00)
        ->with('tipos', $this->tipos());   
    }

    public function f_grado($search, $pagina) {


        $gradosT = DB::select('SELECT id_grado, nombre, significado
            FROM grado
            WHERE estado = 1
            AND nombre LIKE "%'.$search.'%"');

        $grados = DB::select('SELECT id_grado, nombre, significado
            FROM grado
            WHERE estado = 1
            AND nombre LIKE "%'.$search.'%" 
            LIMIT '.(($pagina-1)*10).', 10');

        $pag = 0;
        foreach($gradosT as $g){
            $pag = $pag + 1;
        }
        $pag = ceil($pag / 10);

        return view('CoordAC.grado.grados')
        ->with('grados', $grados)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 01)
        ->with('bus', $search)
        ->with('tipos', $this->tipos());   
}

    public function f_searchgra(Request $request) { 

        $search = mb_strtoupper($request->search);
        //return redirect()->to('CoordAC/grados/'.$search.'/1');
        return $this->f_grado($search, 1); 
    }

    public function f_n_grado() { 

        return view('CoordAC.grado.nuevo')
        ->with('tipos', $this->tipos());   
    }

    public function f_regGrado(Request $request){

        $nombre = mb_strtoupper($request->nomGrado);
        $sig = mb_strtoupper($request->significado);

        Mgrado::create(['nombre' => $nombre, 
        'significado' => $sig, 'estado' => 1]);

        return redirect()->to('CoordAC/grados/1');
    }
        
    public function f_e_grado($id_gra) { 

        $grado = Mgrado::where('id_grado', $id_gra)->get();

        return view('CoordAC.grado.editar')
        ->with('grado', $grado)
        ->with('tipos', $this->tipos());   
    }

    public function f_editGrado($id, Request $request){

        $nombre = mb_strtoupper($request->nombre);
        $sig = mb_strtoupper($request->significado);

        Mgrado::where('id_grado', $id)
            ->update(['nombre' => $nombre, 
        'significado' => $sig, 'estado' => 1]);

        return redirect()->to('CoordAC/grados/1');
    }

    public function f_deletegra($id_delete){

        Mgrado::where('id_grado', $id_delete)
            ->update(['estado' => 0]);

        return redirect()->to('CoordAC/grados/1');
    }

/*----------------------------------------------------------------------------------------------------*/

    public function f_periodos($pagina) {

        $periodosT = DB::select('SELECT id_periodo, 
                nombre, inicio, fin, estado
                FROM periodo
                WHERE condicion = 1
                ORDER BY id_periodo');

        $periodos = DB::select('SELECT id_periodo, 
        nombre, inicio, fin, estado
        FROM periodo
        WHERE condicion = 1 
        ORDER BY id_periodo
        LIMIT '.(($pagina-1)*10).', 10');

        
        $pag = 0;
        foreach($periodosT as $g){
            $pag = $pag + 1;
        }
        $pag = ceil($pag / 10);

        return view('CoordAC.periodo.periodos')
            ->with('periodos', $periodos)
            ->with('pag', $pag)
            ->with('pa', $pagina)
            ->with('vista', 00)
            ->with('tipos', $this->tipos());   
    }

    public function f_periodo($search, $pagina) {

        $periodosT = DB::select('SELECT id_periodo, 
            nombre, inicio, fin, estado
            FROM periodo
            WHERE condicion = 1
            AND nombre LIKE "%'.$search.'%" 
            ORDER BY id_periodo');

        $periodos = DB::select('SELECT id_periodo, 
            nombre, inicio, fin, estado
            FROM periodo
            WHERE condicion = 1
            AND nombre LIKE "%'.$search.'%" 
            ORDER BY id_periodo
            LIMIT '.(($pagina-1)*10).', 10');

        

        $pag = 0;
        foreach($periodosT as $g){
            $pag = $pag + 1;
        }
        $pag = ceil($pag / 10);

        return view('CoordAC.periodo.periodos')
            ->with('periodos', $periodos)
            ->with('pag', $pag)
            ->with('pa', $pagina)
            ->with('vista', 01)
            ->with('bus', $search)
            ->with('tipos', $this->tipos());   
    }
/**Realiza la busqueda de periodos según el parametro de entrada.
 * Busca coincidencias por año y nombre.
 */
    public function f_searchperi(Request $request) { 

        $search = mb_strtoupper($request->search);
        //return $this->f_periodo($search, 1); 
        return redirect()->to('CoordAC/periodos/'.$search.'/1');
    }
/**Redirecciona a la vista para agregar un nuevo periodo */
    public function f_n_periodo(){

        $mes = array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO",
                    "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");

        $año = [date("Y"), date("Y")+1, date("Y")+2];

        return view('CoordAC.periodo.nuevo')
            ->with('mes', $mes)
            ->with('año', $año)
            ->with('tipos', $this->tipos());
    }
/**Esta función de encarga de hacer el request de los datos agregados en la vista
 * para el registro de un nuevo periodo, así como de las validaciones de los datos,
 * 1.- Lapzo del periodo >= 4 && <= 6 meses.
 * 2.- Lapzo entre pocesos del periodo (Inscripción, Evaluación, Generación de contancias),
 *      >= 3 && <= 15 días
 */
    public function f_regPeriodo(Request $request){
        
        $ruta = "images/ac_ito/";
        $nombre = $request->mesi.' '.$request->anioi.' - '.$request->mesf.' '.$request->aniof;
        $inicio = $request->iniPeri;
        $fin = $request->finPeri;
        $iniIns = $request->iniIns;
        $finIns = $request->finIns;
        $iniEval = $request->iniEval;
        $finEval = $request->finEval;
        $iniCons = $request->iniGcons;
        $finCons = $request->finGcons;
        $inscrip = true; $eval = true; $const = true; 
        $mi = true; $me = true; $mc = true;
        $gob; $tecnm; $ito; $encabezado;

        if($request->hasFile('gobierno')){
            $g_new = $request->file('gobierno')->getClientOriginalName();
            $gob =  $request->file('gobierno');
            $gob->move($ruta, $g_new);
            $gob = '/'.$ruta.$g_new;
        }

        if($request->hasFile('tecnmlog')){
            $t_new = $request->file('tecnmlog')->getClientOriginalName();
            $tecnm = $request->file('tecnmlog');
            $tecnm->move($ruta, $t_new);
            $tecnm = '/'.$ruta.$t_new;
        }

        if($request->hasFile('itolog')){
            $i_new = $request->file('itolog')->getClientOriginalName();
            $ito = $request->file('itolog');
            $ito->move($ruta, $i_new);
            $ito = '/'.$ruta.$i_new;
        }

        if($request->hasFile('encabezado')){
            $e_new = $request->file('encabezado')->getClientOriginalName();
            $encabezado = $request->file('encabezado'); 
            $encabezado->move($ruta, $e_new);
            $encabezado = '/'.$ruta.$e_new;
        }

        ($iniIns != '' && $finIns != '') 
            ? (($finIns < date('Y-m-d', strtotime('+2 days', strtotime($iniIns)))
                || $finIns > date('Y-m-d', strtotime('+14 days', strtotime($iniIns))))
                ? $mi = true : $mi = false) 
            : $inscrip = false;

        ($iniEval != '' && $finEval != '') 
            ? (($finEval < date('Y-m-d', strtotime('+2 days', strtotime($iniEval)))
                || $finEval > date('Y-m-d', strtotime('+14 days', strtotime($iniEval))))
                ? $me = true : $me = false) 
            : $eval = false;

        ($iniCons != '' && $finCons != '') 
            ? (($finCons < date('Y-m-d', strtotime('+2 days', strtotime($iniCons)))
                || $finCons > date('Y-m-d', strtotime('+14 days', strtotime($iniCons))))
                ? $mc = true : $mc = false) 
            : $const = false;
        
        if($inicio < $fin){

            if($fin < date('Y-m-d', strtotime('+4 month', strtotime($inicio)))
                || $fin > date('Y-m-d', strtotime('+5 month', strtotime($inicio)))){
                ?><script>
                   alert('Periodo fuera de rango, deben ser 4 meses como mínimo y 5 meses como máximo.');
                   location.href = "CoordAC/nuevoPeri";
                </script><?php
            }else{
                if(!$inscrip && !$eval && !$const){

                    $sig = Mperiodo::select('id_periodo')
                        ->where('estado', "Siguiente")->first();
                    
                    $sig != null 
                        ? 
                            Mperiodo::where('id_periodo', $sig->id_periodo)
                                ->update(['estado' => "Espera"])
                        :   "";

                    Mperiodo::create(['nombre' => $nombre, 'inicio' => $inicio, 'fin' => $fin, 
                    'logo_gob' => $gob, 'logo_tecnm' => $tecnm, 
                    'logo_ito' => $ito, 'logo_anio' => $encabezado, 
                    'estado' => "Siguiente"]);

                    return redirect()->to('CoordAC/periodos/1');
                }else{
                    if(!$mi && !$me && !$mc){
                        if($inicio < $iniIns && $finIns < $iniEval && $finEval < $iniCons && $finCons < $fin){
                            
                            $sig = Mperiodo::select('id_periodo')
                                ->where('estado', "Siguiente")->first();
                            
                            $sig != null 
                                ? 
                                    Mperiodo::where('id_periodo', $sig->id_periodo)
                                        ->update(['estado' => "Espera"])
                                :   "";

                            Mperiodo::create(['nombre' => $nombre, 'inicio' => $inicio, 'fin' => $fin, 
                            'ini_inscripcion' => $iniIns, 'fin_inscripcion' => $finIns,
                            'ini_evaluacion' => $iniEval, 'fin_evaluacion' => $finEval,
                            'ini_gconstancias' => $iniCons, 'fin_gconstancias' => $finCons,
                            'logo_gob' => $gob, 'logo_tecnm' => $tecnm, 
                            'logo_ito' => $ito, 'logo_anio' => $encabezado, 
                            'estado' => "Siguiente"]);
                            
                            return redirect()->to('CoordAC/periodos/1');

                        }else{

                            ?> <script>
                                alert('Las fechas de los procesos de Inscripción, Evaluación y G. Constancias no pueden traslaparse.');
                                location.href = "CoordAC/nuevoPeri";
                            </script> <?php
                        }
                    }elseif(!$mi && $inicio < $iniIns){
                        if(!$me && $finIns < $iniEval && $finEval < $fin){
                            
                            $sig = Mperiodo::select('id_periodo')
                                ->where('estado', "Siguiente")->first();
                            
                            $sig != null 
                                ? 
                                    Mperiodo::where('id_periodo', $sig->id_periodo)
                                        ->update(['estado' => "Espera"])
                                :   "";

                            Mperiodo::create(['nombre' => $nombre, 'inicio' => $inicio, 'fin' => $fin, 
                            'ini_inscripcion' => $iniIns, 'fin_inscripcion' => $finIns,
                            'ini_evaluacion' => $iniEval, 'fin_evaluacion' => $finEval,
                            'logo_gob' => $gob, 'logo_tecnm' => $tecnm, 
                            'logo_ito' => $ito, 'logo_anio' => $encabezado, 
                            'estado' => "Siguiente"]);

                            return redirect()->to('CoordAC/periodos/1');

                        }elseif(!$mc && $finIns < $iniCons && $finCons < $fin){
                            
                            $sig = Mperiodo::select('id_periodo')
                                ->where('estado', "Siguiente")->first();
                            
                            $sig != null 
                                ? 
                                    Mperiodo::where('id_periodo', $sig->id_periodo)
                                        ->update(['estado' => "Espera"])
                                :   "";

                            Mperiodo::create(['nombre' => $nombre, 'inicio' => $inicio, 'fin' => $fin, 
                            'ini_inscripcion' => $iniIns, 'fin_inscripcion' => $finIns,
                            'ini_gconstancias' => $iniCons, 'fin_gconstancias' => $finCons,
                            'logo_gob' => $gob, 'logo_tecnm' => $tecnm, 
                            'logo_ito' => $ito, 'logo_anio' => $encabezado, 
                            'estado' => "Siguiente"]);

                            return redirect()->to('CoordAC/periodos/1');

                        }else{

                            $sig = Mperiodo::select('id_periodo')
                                ->where('estado', "Siguiente")->first();
                            
                            $sig != null 
                                ? 
                                    Mperiodo::where('id_periodo', $sig->id_periodo)
                                        ->update(['estado' => "Espera"])
                                :   "";

                            Mperiodo::create(['nombre' => $nombre, 'inicio' => $inicio, 'fin' => $fin, 
                            'ini_inscripcion' => $iniIns, 'fin_inscripcion' => $finIns,
                            'logo_gob' => $gob, 'logo_tecnm' => $tecnm, 
                            'logo_ito' => $ito, 'logo_anio' => $encabezado, 
                            'estado' => "Siguiente"]);

                            return redirect()->to('CoordAC/periodos/1');

                        }
                    }elseif(!$me && $inicio < $iniEval){
                        if(!$mc && $finEval < $iniCons && $finCons < $fin){
                            
                            $sig = Mperiodo::select('id_periodo')
                                ->where('estado', "Siguiente")->first();
                            
                            $sig != null 
                                ? 
                                    Mperiodo::where('id_periodo', $sig->id_periodo)
                                        ->update(['estado' => "Espera"])
                                :   "";

                            Mperiodo::create(['nombre' => $nombre, 'inicio' => $inicio, 'fin' => $fin, 
                            'ini_evaluacion' => $iniEval, 'fin_evaluacion' => $finEval,
                            'ini_gconstancias' => $iniCons, 'fin_gconstancias' => $finCons,
                            'logo_gob' => $gob, 'logo_tecnm' => $tecnm, 
                            'logo_ito' => $ito, 'logo_anio' => $encabezado, 
                            'estado' => "Siguiente"]);

                            return redirect()->to('CoordAC/periodos/1');

                        }else{
                            
                            $sig = Mperiodo::select('id_periodo')
                                ->where('estado', "Siguiente")->first();
                            
                            $sig != null 
                                ? 
                                    Mperiodo::where('id_periodo', $sig->id_periodo)
                                        ->update(['estado' => "Espera"])
                                :   "";

                            Mperiodo::create(['nombre' => $nombre, 'inicio' => $inicio, 'fin' => $fin, 
                            'ini_evaluacion' => $iniEval, 'fin_evaluacion' => $finEval,
                            'logo_gob' => $gob, 'logo_tecnm' => $tecnm, 
                            'logo_ito' => $ito, 'logo_anio' => $encabezado, 
                            'estado' => "Siguiente"]);

                            return redirect()->to('CoordAC/periodos/1');

                        }
                    }elseif(!$mc && $inicio < $iniCons && $finCons < $fin){
                        
                        $sig = Mperiodo::select('id_periodo')
                                ->where('estado', "Siguiente")->first();
                            
                            $sig != null 
                                ? 
                                    Mperiodo::where('id_periodo', $sig->id_periodo)
                                        ->update(['estado' => "Espera"])
                                :   "";

                        Mperiodo::create(['nombre' => $nombre, 'inicio' => $inicio, 'fin' => $fin, 
                        'ini_gconstancias' => $iniCons, 'fin_gconstancias' => $finCons,
                        'logo_gob' => $gob, 'logo_tecnm' => $tecnm, 
                        'logo_ito' => $ito, 'logo_anio' => $encabezado, 
                        'estado' => "Siguiente"]);

                        return redirect()->to('CoordAC/periodos/1');

                    }else{

                        ?> <script>
                            alert('Los procesos de Inscripción, Evaluación y G. Constancias deben ser mínimo de 3 días y máximo 2 semanas.');
                            location.href = "CoordAC/nuevoPeri";
                        </script> <?php
                    } 
                }
            }
        }else{
            ?> <script>
                   alert('El término del semestre no puede ser anterior al inicio.');
                   location.href = "CoordAC/nuevoPeri";
                </script> <?php
        }

    }
/**Retorna la vista donde se muestra la información del periodo que recibe como parametro */
    public function f_det_periodo($id_peri){

        $periodo = Mperiodo::where('id_periodo', $id_peri)->get();

        return view('CoordAC.periodo.detalle')
        ->with('periodo', $periodo)
        ->with('tipos', $this->tipos());
    }
/**Retorna la vista donde se muestra la información del periodo que recibe como parametro,
 * esta vista permite cambiar algunas de las propiedades del periodo (esta vista solo está 
 * disponible para el periodo "Actual")
 */
    public function f_e_peri($id_peri){

        $periodo = Mperiodo::where('id_periodo', $id_peri)->get();

        return view('CoordAC.periodo.editar')
        ->with('periodo', $periodo)
        ->with('tipos', $this->tipos());
    }
/**Realiza el request de la vista de edición de un periodo, esta edición solo es permitida
 * para el periodo "Actual".
 * Se realizan las mismas validaciones que en la función de registro del periodo
 */
    public function f_editPeriodo($id_peri, Request $request){
        
        $ruta = "images/ac_ito/";
        $iniIns = $request->iniIns;
        $finIns = $request->finIns;
        $iniEval = $request->iniEval;
        $finEval = $request->finEval;
        $iniCons = $request->iniGcons;
        $finCons = $request->finGcons;

        if($request->hasFile('newgobierno')){
            $g_new = $request->file('newgobierno')->getClientOriginalName();
            $s_old = Mperiodo::where('id_periodo', $id_peri)->first();
            if(strcmp(substr($s_old->logo_gob, 15), $g_new)){

                $gob =  $request->file('newgobierno');
                $gob->move($ruta, $g_new);
                $gob = '/'.$ruta.$g_new;
                Mperiodo::where('id_periodo', $id_peri)
                ->update(['logo_gob' => $gob]);
            }
        }

        if($request->hasFile('newtecnmlog')){
            $t_new = $request->file('newtecnmlog')->getClientOriginalName();
            $t_old = Mperiodo::where('id_periodo', $id_peri)->first();
            if(strcmp(substr($t_old->logo_tecnm, 15), $t_new)){

                $tecnm = $request->file('newtecnmlog');
                $tecnm->move($ruta, $t_new);
                $tecnm = '/'.$ruta.$t_new;
                Mperiodo::where('id_periodo', $id_peri)
                ->update(['logo_tecnm' => $tecnm]);
            }
        }

        if($request->hasFile('newitolog')){
            $i_new = $request->file('newitolog')->getClientOriginalName();
            $i_old = Mperiodo::where('id_periodo', $id_peri)->first();
            if(strcmp(substr($i_old->logo_ito, 15), $i_new)){

                $ito = $request->file('newitolog');
                $ito->move($ruta, $i_new);
                $ito = '/'.$ruta.$i_new;
                Mperiodo::where('id_periodo', $id_peri)
                    ->update(['logo_ito' => $ito]);
            }
        }

        if($request->hasFile('newencabezado')){
            $e_new = $request->file('newencabezado')->getClientOriginalName();
            $e_old = Mperiodo::where('id_periodo', $id_peri)->first();
            if(strcmp(substr($i_old->logo_anio, 15), $e_new)){

                $encabezado = $request->file('newencabezado'); 
                $encabezado->move($ruta, $e_new);
                $encabezado = '/'.$ruta.$e_new;
                Mperiodo::where('id_periodo', $id_peri)
                    ->update(['logo_anio' => $encabezado]);
            }
        }

        Mperiodo::where('id_periodo', $id_peri)
        ->update(['ini_inscripcion' => $iniIns, 'fin_inscripcion' => $finIns,
        'ini_evaluacion' => $iniEval, 'fin_evaluacion' => $finEval,
        'ini_gconstancias' => $iniCons, 'fin_gconstancias' => $finCons]);

        return redirect()->to('CoordAC/periodos/1');
    }

    public function f_deleteperi($id_delete){

        Mperiodo::where('id_periodo', $id_delete)
            ->update(['estado' => "Eliminado", 'condicion' => 0]);

        return redirect()->to('CoordAC/periodos/1');
    }

/*----------------------------------------------------------------------------------------------------*/

    public function f_personal($pagina) {

        $personasT = DB::select('SELECT p.id_persona, p.nombre, p.apePat AS paterno, 
                p.apeMat AS materno, p.curp AS curp, 
                d.nombre AS depto, g.nombre AS grado,
                pu.nombre AS puesto, p.estado
        FROM persona AS p 
        LEFT JOIN empleado AS e ON p.id_persona = e.id_persona
        LEFT JOIN departamento AS d ON e.id_depto = d.id_depto
        LEFT JOIN grado AS g ON e.id_grado = g.id_grado
        LEFT JOIN puesto AS pu ON e.id_puesto = pu.id_puesto
        WHERE p.estado IN(SELECT estado FROM persona WHERE estado = 1)
        AND p.tipo = "Empleado" ');

        $personas = DB::select('SELECT p.id_persona, p.nombre, p.apePat AS paterno, 
                p.apeMat AS materno, p.curp AS curp, 
                d.nombre AS depto, g.nombre AS grado,
                pu.nombre AS puesto, p.estado
        FROM persona AS p 
        LEFT JOIN empleado AS e ON p.id_persona = e.id_persona
        LEFT JOIN departamento AS d ON e.id_depto = d.id_depto
        LEFT JOIN grado AS g ON e.id_grado = g.id_grado
        LEFT JOIN puesto AS pu ON e.id_puesto = pu.id_puesto
        WHERE p.estado IN(SELECT estado FROM persona WHERE estado = 1)
        AND p.tipo = "Empleado" 
        LIMIT '.(($pagina-1)*10).', 10');
        
        $pag = 0;
            foreach($personasT as $g){
                $pag = $pag + 1;
            }
            $pag = ceil($pag / 10);


        return view('CoordAC.persona.personas')
        ->with('personas', $personas)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 00)
        ->with('tipos', $this->tipos());   
    }

    public function f_personalB($search, $pagina) {

        $personasT = DB::select('SELECT p.id_persona, p.nombre, p.apePat AS paterno, 
                p.apeMat AS materno, p.curp AS curp, 
                d.nombre AS depto, g.nombre AS grado,
                pu.nombre AS puesto, p.estado
        FROM persona AS p 
        LEFT JOIN empleado AS e ON p.id_persona = e.id_persona
        LEFT JOIN departamento AS d ON e.id_depto = d.id_depto
        LEFT JOIN grado AS g ON e.id_grado = g.id_grado
        LEFT JOIN puesto AS pu ON e.id_puesto = pu.id_puesto
        WHERE p.estado IN(SELECT estado FROM persona WHERE estado = 1)
        AND p.tipo IN(SELECT tipo FROM persona WHERE tipo = "Empleado")
        AND p.nombre LIKE "%'.$search.'%" 
        OR p.nombre LIKE "%'.$search.'%"
        OR p.apePat LIKE "%'.$search.'%"
        OR d.nombre LIKE "%'.$search.'%"
        ' );


        $personas = DB::select('SELECT p.id_persona, p.nombre, p.apePat AS paterno, 
                p.apeMat AS materno, p.curp AS curp, 
                d.nombre AS depto, g.nombre AS grado,
                pu.nombre AS puesto, p.estado
        FROM persona AS p 
        LEFT JOIN empleado AS e ON p.id_persona = e.id_persona
        LEFT JOIN departamento AS d ON e.id_depto = d.id_depto
        LEFT JOIN grado AS g ON e.id_grado = g.id_grado
        LEFT JOIN puesto AS pu ON e.id_puesto = pu.id_puesto
        WHERE p.estado IN(SELECT estado FROM persona WHERE estado = 1)
        AND p.tipo IN(SELECT tipo FROM persona WHERE tipo = "Empleado")
        AND p.nombre LIKE "%'.$search.'%"
        OR p.nombre LIKE "%'.$search.'%"
        OR p.apePat LIKE "%'.$search.'%"
        OR d.nombre LIKE "%'.$search.'%" 
        LIMIT '.(($pagina-1)*10).', 10');
        
        $pag = 0;
        foreach($personasT as $g){
            $pag = $pag + 1;
        }
        $pag = ceil($pag / 10);

        return view('CoordAC.persona.personas')
        ->with('personas', $personas)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 01)
        ->with('bus', $search)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchpers(Request $request) { 

        $search = mb_strtoupper($request->search);
        //return redirect()->to('CoordAC/personal/'.$search.'/1');
        return $this->f_personalB($search, 1); 
    }

    public function f_n_persona(){

        $deptos = Mdepartamento::where('estado', 1)->get();
        $puesto = DB::select('SELECT id_puesto, nombre
            FROM puesto
                WHERE estado = 1
                AND id_puesto <> 6
                AND id_puesto <> 7');
        $grados = Mgrado::where('estado', 1)->get();

        return view('CoordAC.persona.nueva')
        ->with('departamentos', $deptos)
        ->with('puestos', $puesto)
        ->with('grados', $grados)
        ->with('tipos', $this->tipos());
    }

    public function f_n_admin(){

        $deptos = Mdepartamento::where('estado', 1)->get();
        $grados = Mgrado::where('estado', 1)->get();

        return view('CoordAC.persona.admin')
        ->with('departamentos', $deptos)
        ->with('grados', $grados)
        ->with('tipos', $this->tipos());
    }

    public function f_regEmp(Request $request){

        $curp = mb_strtoupper($request->curp);
        $contraseña = bcrypt($request->curp);
        $nombre = mb_strtoupper($request->nombre);
        $apePat = mb_strtoupper($request->apePat);
        $apeMat = mb_strtoupper($request->apeMat);
        $nomUser = mb_strtoupper($request->nombre.' '.$request->apePat.' '.$request->apeMat);
        $depto = $request->depto;
        $puesto = $request->puesto;
        $grado = $request->grado;
        $hoy = date("Y-m-d");
        $exist = false;
        $empleados = Mpersona::select('curp')->get();

        foreach($empleados as $e){
            if($e->curp == $curp)
                $exist = true;
        }

        if($exist){
            ?><script>
                alert("Empleado ya registrado, por favor verifca los datos.");
                location.href = "/CoordAC/nuevaPer";
            </script><?php
        }else{
            $persona = Mpersona::create(['nombre' => $nombre, 'apePat' => $apePat,
            'apeMat' => $apeMat, 'curp' => $curp, 'tipo' => "Empleado", 'estado' => 1]);

            Mempleado::create(['id_persona' => $persona->id, 'id_depto' => $depto, 
            'id_grado' => $grado, 'id_puesto' => $puesto]);

            Musers::create(['id_persona' => $persona->id, 'id_puesto' => $puesto,
            'nombre' => $nomUser, 'usuario' => $curp, 'password' => $contraseña,
            'fecha_registro' => $hoy, 'edo_sesion' => 0, 'estado' => 1]);

            return redirect()->to('CoordAC/personal/1');
        }
    }

    public function f_regAdmin(Request $request){

        $curp = mb_strtoupper($request->curp);
        $contraseña = bcrypt($request->curp);
        $nombre = mb_strtoupper($request->nombre);
        $apePat = mb_strtoupper($request->apePat);
        $apeMat = mb_strtoupper($request->apeMat);
        $nomUser = mb_strtoupper($request->nombre.' '.$request->apePat.' '.$request->apeMat);
        $depto = $request->depto;
        $grado = $request->grado;
        $hoy = date("Y-m-d");
        $exist = false;
        $empleados = Mpersona::select('curp')->get();

        foreach($empleados as $e){
            if($e->curp == $curp)
                $exist = true;
        }

        if($exist){
            ?><script>
                alert("Empleado ya registrado, por favor verifca los datos.");
                location.href = "/CoordAC/nuevaPer";
            </script><?php
        }else{
            $persona = Mpersona::create(['nombre' => $nombre, 'apePat' => $apePat,
            'apeMat' => $apeMat, 'curp' => $curp, 'tipo' => "Empleado", 'estado' => 1]);

            Mempleado::create(['id_persona' => $persona->id, 'id_depto' => $depto, 
            'id_grado' => $grado, 'id_puesto' => 7]);

            Musers::create(['id_persona' => $persona->id, 'id_puesto' => $puesto,
            'nombre' => $nomUser, 'usuario' => $curp, 'password' => $contraseña,
            'fecha_registro' => $hoy, 'edo_sesion' => 0, 'estado' => 1]);

            Mempleado::where('id_persona', $request->user()->id_persona)
                ->update(['id_puesto' => 9]);
                
            return redirect()->to('CoordAC/personal/1');
        }
    }

    public function f_e_persona($id_per){

        $deptos = Mdepartamento::get();
        $puesto = DB::select('SELECT id_puesto, nombre
            FROM puesto
                WHERE estado = 1
                AND id_puesto <> 6
                AND id_puesto <> 7');
        $grados = Mgrado::get();

        $persona = DB::table('persona AS p')
        ->join('empleado AS e', 'p.id_persona', '=', 'e.id_persona')
        ->join('departamento AS d', 'e.id_depto', '=', 'd.id_depto')
        ->join('grado AS g', 'e.id_grado', '=', 'g.id_grado')
        ->join('puesto AS pu', 'e.id_puesto', '=', 'pu.id_puesto')
        ->select('p.id_persona', 'p.nombre', 
                'p.apePat AS paterno', 'p.apeMat AS materno',
                'p.curp', 'd.nombre AS depto', 'g.nombre AS grado',
                'pu.nombre AS puesto', 'e.id_depto',
                'e.id_grado', 'e.id_puesto')
        ->where('p.id_persona', $id_per)
                ->get();

        return view('CoordAC.persona.editar')
        ->with('persona', $persona)
        ->with('departamentos', $deptos)
        ->with('puestos', $puesto)
        ->with('grados', $grados)
        ->with('tipos', $this->tipos());
    }

    public function f_editEmp($id_emp, Request $request){

        
        //$person = $request->persona->id_persona;

        $grado = $request->grado;
        $nombre = mb_strtoupper($request->nombre);
        $apePat = mb_strtoupper($request->apePat);
        $apeMat = mb_strtoupper($request->apeMat);
        $depto = $request->depto;
        $puesto = $request->puesto;
        $curp = mb_strtoupper($request->curp);
        $nomUser = mb_strtoupper($request->nombre.' '.$request->apePat.' '.$request->apeMat);

        Mpersona::where('id_persona', $id_emp)
        ->update(['nombre' => $nombre, 'apePat' => $apePat,
        'apeMat' => $apeMat, 'curp' => $curp]);

        Mempleado::where('id_persona', $id_emp)
        ->update(['id_depto' => $depto, 
        'id_grado' => $grado, 'id_puesto' => $puesto]);

        Musers::where('id_persona', $id_emp)
        ->update(['id_puesto' => $puesto, 'nombre' => $nomUser, 
        'usuario' => $curp]);


        return redirect()->to('CoordAC/personal/1');
    }

    public function f_inhabilitados() {

        $personas = DB::select('SELECT p.id_persona, p.nombre, p.apePat AS paterno, 
                p.apeMat AS materno, p.curp AS curp, 
                d.nombre AS depto, g.nombre AS grado,
                pu.nombre AS puesto, p.estado
        FROM persona AS p 
        LEFT JOIN empleado AS e ON p.id_persona = e.id_persona
        LEFT JOIN departamento AS d ON e.id_depto = d.id_depto
        LEFT JOIN grado AS g ON e.id_grado = g.id_grado
        LEFT JOIN puesto AS pu ON e.id_puesto = pu.id_puesto
        WHERE p.estado IN(SELECT estado FROM persona WHERE estado = 1)
        AND p.tipo = "Empleado" 
        AND e.id_puesto = 9');

        return view('CoordAC.persona.inhabilitados')
        ->with('personas', $personas)
        ->with('tipos', $this->tipos());   
    }

    public function f_habilitar($id_emp, Request $request){

        $puesto = $request->puesto;

        Mempleado::where('id_persona', $id_emp)
            ->update(['id_puesto' => $puesto]);

        return redirect()->to('CoordAC/personal/1');
    }

    public function f_deleteper($id_delete){

        // Mpersona::where('id_persona', $id_delete)
        //     ->update(['estado' => 0]);

        Mempleado::where('id_persona', $id_delete)
            ->update(['id_puesto' => 9]);

        return redirect()->to('CoordAC/personal/1');
    }

/*----------------------------------------------------------------------------------------------------*/

    public function f_puestos($search) {

        if($search == "0"){
            $puestos = DB::select('SELECT id_puesto,
                nombre, descripcion
                FROM puesto
                WHERE estado = 1'); 
        }else{
            $puestos = DB::select('SELECT id_puesto,
                nombre, descripcion
                FROM puesto
                WHERE estado = 1
                AND nombre LIKE "%'.$search.'%" '); 
        }

        return view('CoordAC.puesto.puestos')
        ->with('puestos', $puestos)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchpue(Request $request) { 

        $search = mb_strtoupper($request->search);
        //return redirect()->to('CoordAC/puestos/'.$search);
        return $this->f_puestos($search); 
    }

    public function f_n_puesto(){

        return view('CoordAC.puesto.nuevo')
        ->with('tipos', $this->tipos());
    }

    public function f_regPuesto(Request $request){

        $nombre = mb_strtoupper($request->nomPuesto);
        $descrip = $request->descrip;

        Mpuesto::create(['nombre' => $nombre,
        'descripcion' => $descrip, 'estado' => 1]);

        return redirect()->to('CoordAC/puestos/0');
    }

    public function f_e_puesto($id_pue){

        $puesto = Mpuesto::where('id_puesto', $id_pue)->get();

        return view('CoordAC.puesto.editar')
        ->with('puesto', $puesto)
        ->with('tipos', $this->tipos()); 
    }

    public function f_editpuesto($id_pue, Request $request){

        $nom = mb_strtoupper($request->nombre);
        $descrip = $request->descrip;

        $puesto = Mpuesto::where('id_puesto', $id_pue)
            ->update(['nombre' => $nom,
            'descripcion' => $descrip]);

        return redirect()->to('CoordAC/puestos/0');
    }

    public function f_deletepue($id_delete){

        Mpuesto::where('id_puesto', $id_delete)
            ->update(['estado' => 0]);

        return redirect()->to('CoordAC/puestos/0');
    }

/*----------------------------------------------------------------------------------------------------*/

    public function f_r_usuarios($pagina) { 


        $personaP = DB::select('SELECT p.nombre, 
        p.apePat, p.apeMat, p.tipo, u.usuario, p.id_persona
        FROM persona AS p
        LEFT JOIN users AS u ON p.id_persona = u.id_persona
        WHERE p.estado = 1');

        $persona = DB::select('SELECT p.nombre, 
        p.apePat, p.apeMat, p.tipo, u.usuario, p.id_persona
        FROM persona AS p
        LEFT JOIN users AS u ON p.id_persona = u.id_persona
        WHERE p.estado = 1
        LIMIT '.(($pagina-1)*10).', 10');
        
        $pag = 0;
        foreach($personaP as $g){
            $pag = $pag + 1;
        }
        $pag = ceil($pag / 10);

        return view('CoordAC.r_usuarios')
        ->with('persona', $persona)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 00)
        ->with('tipos', $this->tipos());   
    }

    public function f_r_usuariosB($search, $pagina) { 


        $personaP = DB::select('SELECT p.nombre, 
        p.apePat, p.apeMat, p.tipo, u.usuario, p.id_persona
        FROM persona AS p
        LEFT JOIN users AS u ON p.id_persona = u.id_persona
        WHERE p.estado = 1
        AND p.nombre LIKE "%'.$search.'%" OR u.usuario LIKE "%'.$search.'%"');

        $persona = DB::select('SELECT p.nombre, 
        p.apePat, p.apeMat, p.tipo, u.usuario, p.id_persona
        FROM persona AS p
        LEFT JOIN users AS u ON p.id_persona = u.id_persona
        WHERE p.estado = 1
        AND p.nombre LIKE "%'.$search.'%" OR u.usuario LIKE "%'.$search.'%"
        LIMIT '.(($pagina-1)*10).', 10');

        $pag = 0;
        foreach($personaP as $g){
            $pag = $pag + 1;
        }
        $pag = ceil($pag / 10);

        return view('CoordAC.r_usuarios')
        ->with('persona', $persona)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('bus', $search)
        ->with('vista', 01)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchusu(Request $request) { 

        $search = mb_strtoupper($request->search);
        //return redirect()->to('CoordAC/restUsuario/'.$search.'/1'); 
        return $this->f_r_usuariosB($search, 1);
    }

    public function f_viewrestart($usuario){
       $cab = "";
        return view('CoordAC.restart')
            ->with('usuario', $usuario)
            ->with('tipos', $this->tipos());
    }

    public function f_restartuser($iduser) { 

        $type = Mpersona::select('tipo')
            ->where('id_persona', $iduser)->first();

        if(strcmp($type->tipo, "Estudiante")){
            $passwd = Mestudiante::select('num_control')->where('id_persona', $iduser)->first();
            
            $newpw = Hash::make($passwd->num_control);

            Musers::where('id_persona', $iduser)
                    ->update(['password' => $newpw]);

            return redirect()->to('CoordAC/restUsuario/1'); 
        }
        else{
            $passwd = Mpersona::select('curp')
                ->where('id_persona', $iduser)->first();
            
            $newpw = Hash::make($passwd->curp);

            Musers::where('id_persona', $iduser)
                    ->update(['password' => $newpw]);

            return redirect()->to('CoordAC/restUsuario/1'); 
        }
    }


/*----------------------------------------------------------------------------------------------------*/

    public function f_s_labores($pagina) { 

        $fechasT = DB::select('SELECT id_fecha, fecha, motivo
                FROM fechas_inhabiles
                WHERE estado = 1');

        $fechas = DB::select('SELECT id_fecha, fecha, motivo
                FROM fechas_inhabiles
                WHERE estado = 1
                LIMIT '.(($pagina-1)*10).', 10');
                //AND p.nombre LIKE "%'.$search.'%" OR u.usuario LIKE "%'.$search.'%"

        $pag = 0;
        foreach($fechasT as $g){
            $pag = $pag + 1;
        }
        $pag = ceil($pag / 10);
        
        return view('CoordAC.suspencion.sus_labores')
        ->with('fechas', $fechas)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 00)
        ->with('tipos', $this->tipos()); 
    }

    public function f_s_labor($search, $pagina) { 

        $fechasT = DB::select('SELECT id_fecha, fecha, motivo
                FROM fechas_inhabiles
                WHERE estado IN(SELECT estado FROM fechas_inhabiles WHERE estado = 1)
                AND fecha LIKE "%'.$search.'" 
                OR motivo LIKE "%'.$search.'%" ');
                //AND p.nombre LIKE "%'.$search.'%" OR u.usuario LIKE "%'.$search.'%"

        $fechas = DB::select('SELECT id_fecha, fecha, motivo
                FROM fechas_inhabiles
                WHERE estado IN(SELECT estado FROM fechas_inhabiles WHERE estado = 1)
                AND fecha LIKE "%'.$search.'" 
                OR motivo LIKE "%'.$search.'%" 
                LIMIT '.(($pagina-1)*10).', 10');


        

        $pag = 0;
        foreach($fechasT as $g){
            $pag = $pag + 1;
        }
        $pag = ceil($pag / 10);
        
        return view('CoordAC.suspencion.sus_labores')
        ->with('fechas', $fechas)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('bus', $search)
        ->with('vista', 01)
        ->with('tipos', $this->tipos()); 
    }

    public function f_searchslab(Request $request) { 

        $search = mb_strtoupper($request->search);
        //return redirect()->to('CoordAC/suspLabores/'.$search.'/1');
        return $this->f_s_labor($search, 1); 
    }

    public function f_n_fecha(){

        return view('CoordAC.suspencion.nueva')
        ->with('tipos', $this->tipos());
    }

    public function f_regFecha(Request $request){

        $fecha = $request->fecha;
        $fechfin = $request->fechafin;
        $motivo = mb_strtoupper($request->motivo);
        $end = false;


        //return date('Y-m-d', strtotime('tomorrow', strtotime($fecha)));       
  
        if($fechfin == '' || $fecha == $fechfin)  {

            Mfechas_inhabiles::create(['fecha' => $fecha,
            'motivo' => $motivo, 'estado' => 1]);
        
            return redirect()->to('CoordAC/suspLabores/1');
        }
        elseif($fecha > $fechfin) {
            ?>
                <script>
                    alert("La fecha de término no puede ser menor que la fecha de inicio.");
                    location.href = "CoordAC/nuevaFecha";
                </script>
            <?php
        }
        else{
            $fnew = $fecha;
            while($end != true){

                Mfechas_inhabiles::create(['fecha' => $fnew,
                'motivo' => $motivo, 'estado' => 1]);

                $fnew = date('Y-m-d', strtotime('tomorrow', strtotime($fnew)));

                if($fnew == $fechfin){ 

                    Mfechas_inhabiles::create(['fecha' => $fnew,
                    'motivo' => $motivo, 'estado' => 1]);
                    $end = true;
                    return redirect()->to('CoordAC/suspLabores/1');
                }
            }
        }

    }

    public function f_deletefech($id_delete){

        Mfechas_inhabiles::where('id_fecha', $id_delete)
            ->update(['estado' => 0]);

        return redirect()->to('CoordAC/suspLabores/1');
    }

/*----------------------------------------------------------------------------------------------------*/

    public function f_lugar($search, $pagina) {

        $lugares = DB::select('SELECT id_lugar, nombre
            FROM lugar
            WHERE estado = 1
            AND nombre LIKE "%%'.$search.'%" 
            LIMIT '.(($pagina-1)*10).', 10');

        $lugaresT = DB::select('SELECT id_lugar, nombre
            FROM lugar
            WHERE estado = 1
            AND nombre LIKE "%%'.$search.'%" ');

        $pag = 0;
            foreach($lugaresT as $g){
                $pag = $pag + 1;
            }
        $pag = ceil($pag / 10);

        return view('CoordAC.lugares.lugares')
        ->with('lugares', $lugares)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 01)
        ->with('bus', $search)
        ->with('tipos', $this->tipos());   
    }

    public function f_lugares($pagina) {

        $lugares = DB::select('SELECT id_lugar, nombre
            FROM lugar
            WHERE estado = 1
            LIMIT '.(($pagina-1)*10).', 10');
        
        $lugaresT = DB::select('SELECT id_lugar, nombre
            FROM lugar
            WHERE estado = 1');

        $pag = 0;
            foreach($lugaresT as $g){
                $pag = $pag + 1;
            }
        $pag = ceil($pag / 10);

        return view('CoordAC.lugares.lugares')
        ->with('lugares', $lugares)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 00)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchlug(Request $request) { 

        $search = mb_strtoupper($request->search);
        //return redirect()->to('CoordAC/lugares/'.$search.'/1');
        return $this->f_lugar($search, 1);   
    }

    public function f_n_lugar(){

        return view('CoordAC.lugares.nuevo')
        ->with('tipos', $this->tipos());
    }

    public function f_regLugar(Request $request){

        $nombre = mb_strtoupper($request->nomLugar);

        Mlugar::create(['nombre' => $nombre, 'estado' => 1]);

        return redirect()->to('CoordAC/lugares/1');
    }

    public function f_e_lugar($id_lug){

        $lugar = Mlugar::where('id_lugar', $id_lug)->get();

        return view('CoordAC.lugares.editar')
        ->with('lugar', $lugar)
        ->with('tipos', $this->tipos()); 
    }

    public function f_editlugar(Request $request){

        $nombre = mb_strtoupper($request->nombre);
        $id = $request->id_lugar;

        $lugar = Mlugar::where('id_lugar', $id)
            ->update(['nombre' => $nombre]);

        return redirect('CoordAC/lugares/1'); 
    }
    

    public function f_deletelug($id_delete){

        Mlugar::where('id_lugar', $id_delete)
            ->update(['estado' => 0]);

        return redirect()->to('CoordAC/lugares/1');
    }

/************************************************************************************************** */
    public function f_inscripciones(){

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'ini_evaluacion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();

        return view('CoordAC.inscripciones.inscrip')
            ->with('inscrip', 000)
            ->with('dpts', $dpts)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_inscrip(Request $request){

        $dpt = $request->dpt;

        return redirect()->to('CoordAC/inscripPA/'.$dpt.'/1'); 
    }

    public function f_inscripPA($dpt, $pagina){

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $inscrip = DB::select('SELECT e.num_control, e.semestre, 
        p.nombre, p.apePat, p.apeMat,
        g.clave AS grupo, a.nombre AS actividad, 
        i.aprobada, i.id_inscripcion
        FROM inscripcion AS i
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
        WHERE i.aprobada = 0
        AND pr.estado = "Actual"
        AND a.id_depto = '.$dpt.'
        LIMIT '.(($pagina-1)*10).', 10');

        $inscript = DB::select('SELECT e.num_control, e.semestre, 
        p.nombre, p.apePat, p.apeMat,
        g.clave AS grupo, a.nombre AS actividad, 
        i.aprobada, i.id_inscripcion
        FROM inscripcion AS i
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
        WHERE i.aprobada = 0
        AND pr.estado = "Actual" 
        AND a.id_depto = '.$dpt);

        $pag = 0;
            foreach($inscript as $g){
                $pag = $pag + 1;
            }
        $pag = ceil($pag / 10);

        $dptn = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_depto', $dpt)->first();
        
        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();

        return view('CoordAC.inscripciones.inscrip')
            ->with('inscrip', $inscrip)
            ->with('type', 0)
            ->with('dpts', $dpts)
            ->with('dptn', $dptn)
            ->with('pag', $pag)
            ->with('pa', $pagina)
            ->with('vista', 00)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_inscripPAB($dpt, $pagina, $search){

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $inscript = DB::select('SELECT e.num_control, e.semestre, 
        p.nombre, p.apePat, p.apeMat,
        g.clave AS grupo, a.nombre AS actividad, 
        i.aprobada, i.id_inscripcion
        FROM inscripcion AS i
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
        WHERE i.aprobada = 0
        AND pr.estado = "Actual"
        AND e.num_control LIKE "%'.$search.'%"
        AND a.id_depto = '.$dpt);

        $inscrip = DB::select('SELECT e.num_control, e.semestre, 
        p.nombre, p.apePat, p.apeMat,
        g.clave AS grupo, a.nombre AS actividad, 
        i.aprobada, i.id_inscripcion
        FROM inscripcion AS i
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
        WHERE i.aprobada = 0
        AND pr.estado = "Actual"
        AND e.num_control LIKE "%'.$search.'%" 
        AND a.id_depto = '.$dpt.'
        LIMIT '.(($pagina-1)*10).', 10');


        $dptn = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_depto', $dpt)->first();
        
        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();

        return view('CoordAC.inscripciones.inscrip')
            ->with('inscrip', $inscrip)
            ->with('type', 0)
            ->with('dpts', $dpts)
            ->with('dptn', $dptn)
            ->with('pag', $pag)
            ->with('pa', $pagina)
            ->with('vista', 01)
            ->with('bus', $search)
            ->with('mod', true)
            ->with('tipos', $this->tipos());
    }

    public function f_searchPA($dpt, Request $request) { 

        $search = mb_strtoupper($request->search);
        //return $this->f_inscripPA($dpt, 1, $search);   
        return redirect()->to('CoordAC/inscripPA/'.$dpt.'/1/'.$search);
    }

    public function f_inscripA($dpt, $pagina){

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $inscript = DB::select('SELECT e.num_control, e.semestre, 
        p.nombre, p.apePat, p.apeMat,
        g.clave AS grupo, a.nombre AS actividad, 
        i.aprobada, i.id_inscripcion
        FROM inscripcion AS i
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
        WHERE i.aprobada = 1
        AND pr.estado = "Actual"
        AND a.id_depto = '.$dpt);

        $inscrip = DB::select('SELECT e.num_control, e.semestre, 
        p.nombre, p.apePat, p.apeMat,
        g.clave AS grupo, a.nombre AS actividad, 
        i.aprobada, i.id_inscripcion
        FROM inscripcion AS i
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
        WHERE i.aprobada = 1
        AND pr.estado = "Actual"
        AND a.id_depto = '.$dpt.'
        LIMIT '.(($pagina-1)*10).', 10');

        $pag = 0;
            foreach($inscript as $g){
                $pag = $pag + 1;
            }
        $pag = ceil($pag / 10);

        $dptn = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_depto', $dpt)->first();
        
        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();


        return view('CoordAC.inscripciones.inscrip')
            ->with('inscrip', $inscrip)
            ->with('type', 1)
            ->with('dpts', $dpts)
            ->with('dptn', $dptn)
            ->with('pag', $pag)
            ->with('pa', $pagina)
            ->with('vista', 00)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_inscripAB($dpt, $pagina, $search){

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $inscript = DB::select('SELECT e.num_control, e.semestre, 
        p.nombre, p.apePat, p.apeMat,
        g.clave AS grupo, a.nombre AS actividad, 
        i.aprobada, i.id_inscripcion
        FROM inscripcion AS i
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
        WHERE i.aprobada = 1
        AND pr.estado = "Actual"
        AND a.id_depto = '.$dpt.'
        AND e.num_control LIKE "%'.$search.'%"');

        $inscrip = DB::select('SELECT e.num_control, e.semestre, 
        p.nombre, p.apePat, p.apeMat,
        g.clave AS grupo, a.nombre AS actividad, 
        i.aprobada, i.id_inscripcion
        FROM inscripcion AS i
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
        WHERE i.aprobada = 1
        AND pr.estado = "Actual"
        AND a.id_depto = '.$dpt.'
        AND e.num_control LIKE "%'.$search.'%" 
        LIMIT '.(($pagina-1)*10).', 10');


        $pag = 0;
            foreach($inscript as $g){
                $pag = $pag + 1;
            }
        $pag = ceil($pag / 10);

        $dptn = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_depto', $dpt)->first();
        
        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();


        return view('CoordAC.inscripciones.inscrip')
            ->with('inscrip', $inscrip)
            ->with('type', 1)
            ->with('dpts', $dpts)
            ->with('dptn', $dptn)
            ->with('pag', $pag)
            ->with('pa', $pagina)
            ->with('vista', 01)
            ->with('bus', $search)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_searchA($dpt, Request $request) { 

        $search = mb_strtoupper($request->search);
        //return $this->f_inscripAB($dpt, 1, $search);   
        return redirect()->to('CoordAC/inscripA/'.$dpt.'/1/'.$search);
        
    }

    public function f_inscripNA($dpt, $pagina){

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $inscript = DB::select('SELECT e.num_control, e.semestre, 
        p.nombre, p.apePat, p.apeMat,
        g.clave AS grupo, a.nombre AS actividad, 
        i.aprobada, i.id_inscripcion
        FROM inscripcion AS i
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
        WHERE i.aprobada = 2
        AND pr.estado = "Actual"
        AND a.id_depto = '.$dpt);

        $inscrip = DB::select('SELECT e.num_control, e.semestre, 
        p.nombre, p.apePat, p.apeMat,
        g.clave AS grupo, a.nombre AS actividad, 
        i.aprobada, i.id_inscripcion
        FROM inscripcion AS i
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
        WHERE i.aprobada = 2
        AND pr.estado = "Actual"
        AND a.id_depto = '.$dpt.'
        LIMIT '.(($pagina-1)*10).', 10');

        $pag = 0;
            foreach($inscript as $g){
                $pag = $pag + 1;
            }
        $pag = ceil($pag / 10);

        $dptn = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_depto', $dpt)->first();
        
        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();


        return view('CoordAC.inscripciones.inscrip')
            ->with('inscrip', $inscrip)
            ->with('type', 2)
            ->with('dpts', $dpts)
            ->with('dptn', $dptn)
            ->with('pag', $pag)
            ->with('pa', $pagina)
            ->with('vista', 00)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_inscripNAB($dpt, $pagina, $search){

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $inscript = DB::select('SELECT e.num_control, e.semestre, 
        p.nombre, p.apePat, p.apeMat,
        g.clave AS grupo, a.nombre AS actividad, 
        i.aprobada, i.id_inscripcion
        FROM inscripcion AS i
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
        WHERE i.aprobada = 2
        AND pr.estado = "Actual"
        AND a.id_depto = '.$dpt.'
        AND e.num_control LIKE "%'.$search.'%"');

        $inscrip = DB::select('SELECT e.num_control, e.semestre, 
        p.nombre, p.apePat, p.apeMat,
        g.clave AS grupo, a.nombre AS actividad, 
        i.aprobada, i.id_inscripcion
        FROM inscripcion AS i
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
        WHERE i.aprobada = 2
        AND pr.estado = "Actual"
        AND a.id_depto = '.$dpt.'
        AND e.num_control LIKE "%'.$search.'%" 
        LIMIT '.(($pagina-1)*10).', 10');

        $pag = 0;
            foreach($inscript as $g){
                $pag = $pag + 1;
            }
        $pag = ceil($pag / 10);

        $dptn = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_depto', $dpt)->first();
        
        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();


        return view('CoordAC.inscripciones.inscrip')
            ->with('inscrip', $inscrip)
            ->with('type', 2)
            ->with('dpts', $dpts)
            ->with('dptn', $dptn)
            ->with('pag', $pag)
            ->with('pa', $pagina)
            ->with('vista', 01)
            ->with('bus', $search)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_searchNA($dpt, Request $request) { 

        $search = mb_strtoupper($request->search);
        //return $this->f_inscripNAB($dpt, 1, $search);   
        return redirect()->to('CoordAC/inscripNA/'.$dpt.'/1/'.$search);
    }

    public function f_inscripBJ($dpt, $pagina){

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

            $inscript = DB::select('SELECT e.num_control, e.semestre, 
            p.nombre, p.apePat, p.apeMat,
            g.clave AS grupo, a.nombre AS actividad, 
            i.aprobada, i.id_inscripcion
            FROM inscripcion AS i
                LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
                LEFT JOIN persona AS p ON e.id_persona = p.id_persona
                LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
                LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
                LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
            WHERE i.aprobada = 3
            AND pr.estado = "Actual"
            AND a.id_depto = '.$dpt);

            $inscrip = DB::select('SELECT e.num_control, e.semestre, 
            p.nombre, p.apePat, p.apeMat,
            g.clave AS grupo, a.nombre AS actividad, 
            i.aprobada, i.id_inscripcion
            FROM inscripcion AS i
                LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
                LEFT JOIN persona AS p ON e.id_persona = p.id_persona
                LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
                LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
                LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
            WHERE i.aprobada = 3
            AND pr.estado = "Actual"
            AND a.id_depto = '.$dpt.'
            LIMIT '.(($pagina-1)*10).', 10');

        $pag = 0;
            foreach($inscript as $g){
                $pag = $pag + 1;
            }
        $pag = ceil($pag / 10);

        $dptn = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_depto', $dpt)->first();
        
        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();

        return view('CoordAC.inscripciones.inscrip')
            ->with('inscrip', $inscrip)
            ->with('type', 3)
            ->with('dpts', $dpts)
            ->with('dptn', $dptn)
            ->with('pag', $pag)
            ->with('pa', $pagina)
            ->with('vista', 00)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_inscripBJB($dpt, $pagina, $search){

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $inscript = DB::select('SELECT e.num_control, e.semestre, 
        p.nombre, p.apePat, p.apeMat,
        g.clave AS grupo, a.nombre AS actividad, 
        i.aprobada, i.id_inscripcion
        FROM inscripcion AS i
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
        WHERE i.aprobada = 3
        AND pr.estado = "Actual"
        AND a.id_depto = '.$dpt.'
        AND e.num_control LIKE "%'.$search.'%"');

        $inscrip = DB::select('SELECT e.num_control, e.semestre, 
        p.nombre, p.apePat, p.apeMat,
        g.clave AS grupo, a.nombre AS actividad, 
        i.aprobada, i.id_inscripcion
        FROM inscripcion AS i
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
        WHERE i.aprobada = 3
        AND pr.estado = "Actual"
        AND a.id_depto = '.$dpt.'
        AND e.num_control LIKE "%'.$search.'%" 
        LIMIT '.(($pagina-1)*10).', 10');

        $pag = 0;
            foreach($inscript as $g){
                $pag = $pag + 1;
            }
        $pag = ceil($pag / 10);

        $dptn = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_depto', $dpt)->first();
        
        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();


        return view('CoordAC.inscripciones.inscrip')
            ->with('inscrip', $inscrip)
            ->with('type', 3)
            ->with('dpts', $dpts)
            ->with('dptn', $dptn)
            ->with('pag', $pag)
            ->with('pa', $pagina)
            ->with('vista', 01)
            ->with('bus', $search)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_searchBJ($dpt, Request $request) { 

        $search = mb_strtoupper($request->search);
        //return $this->f_inscripBJB($dpt, 1, $search);   
        return redirect()->to('CoordAC/inscripBJ/'.$dpt.'/1/'.$search);
    }

    public function f_detInscrip($dpto, $id_ins){

        $est = DB::select('SELECT e.num_control, e.semestre, 
            p.nombre, p.apePat, 
            p.apeMat, c.nombre AS carrera
            FROM inscripcion AS i
                LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
                LEFT JOIN persona AS p ON e.id_persona = p.id_persona
                LEFT JOIN carrera AS c ON e.id_carrera = c.id_carrera
            WHERE i.id_inscripcion = '.$id_ins);

        $acti = DB::select('SELECT g.clave AS grupo, a.nombre AS actividad, 
        d.nombre AS depto, i.aprobada, i.id_inscripcion, a.restringida
        FROM inscripcion AS i
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN departamento AS d ON a.id_depto = d.id_depto
        WHERE i.id_inscripcion = '.$id_ins);

        $horario = DB::select('SELECT ds.id_dia, ds.nombre, h.hora_inicio, h.hora_fin
        FROM inscripcion AS i
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN horario AS h ON g.id_grupo = h.id_grupo
            LEFT JOIN dias_semana AS ds ON h.id_dia = ds.id_dia
        WHERE i.id_inscripcion = '.$id_ins);

        return view('CoordAC.inscripciones.detalle')
        ->with('estudiante', $est)
        ->with('actividad', $acti)
        ->with('horario', $horario)
        ->with('dpt', $dpto)
        ->with('tipos', $this->tipos()); 

    }
/**Esta función se encarga de la aprobación de las inscripciones,
 * actualiza el registro de la inscripción aprobando la solicitud
 * de inscripción que realiza cada estudiante. También envia los
 * correos electrónicos de notificación de inscripción aprobada.
 */
    public function f_aprobar($id_ins, $dpt){

        Minscripcion::where('id_inscripcion', $id_ins)
        ->update(['aprobada' => 1]);

        $est = DB::select('SELECT g.clave , a.nombre AS actividad, 
                            p.nombre, p.apePat, p.apeMat, e.email
        FROM inscripcion AS i
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
        WHERE i.id_inscripcion = '.$id_ins);

        $message = 
        'Hola, '.$est[0]->nombre.' '.$est[0]->apePat.' '.$est[0]->apeMat.' 

        Te escribimos para notificarte que ha sido aprobada tu inscripción a la actividad 

                "'.$est[0]->clave.' - '.$est[0]->actividad.'"

        si tienes algúna duda envíanos un correo electrónico a la 
        siguiente dirección: altamirano.flv@gmail.com 
            
                                Attentamente 
            
                Coordinación de Actividades Complementarias 
                    Tecnológco Nacional de MéxicoITO
                    Instituto Tecnológico de Oaxaca';


        $est = "".$est[0]->email;
        $message = wordwrap($message, 80);

        Mail::raw($message, function ($message) use ($est) {
            
            $message->to($est)
                ->subject('Inscripción Aprobada');
         });

        return redirect()->to('CoordAC/inscripPA/'.$dpt.'/1');
    }
/**Esta función se encarga de la NO aprobación de las inscripciones,
* actualiza el registro de la inscripción denegando la solicitud
* de inscripción que realiza cada estudiante. También envia los
* correos electrónicos de notificación de inscripción NO aprobada.
*/
    public function f_noaprobar($id_ins, $dpt){

        Minscripcion::where('id_inscripcion', $id_ins)
        ->update(['aprobada' => 2]);
        
        $group = Minscripcion::where('id_inscripcion', $id_ins)->first();
        $cupo = Mgrupo::select('cupo_libre')->where('id_grupo', $group->id_grupo)->first();
        $cupo = $cupo->cupo_libre + 1;

        Mgrupo::where('id_grupo', $group->id_grupo)->update([
            'cupo_libre' => $cupo
        ]);
        
        $est = DB::select('SELECT g.clave , a.nombre AS actividad, 
                            p.nombre, p.apePat, p.apeMat, e.email
        FROM inscripcion AS i
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
        WHERE i.id_inscripcion = '.$id_ins);

            $message = 
        'Hola, '.$est[0]->nombre.' '.$est[0]->apePat.' '.$est[0]->apeMat.' 

        Te escribimos para notificarte que no ha sido aprobada tu inscripción a la actividad 

                "'.$est[0]->clave.' - '.$est[0]->actividad.'"

        si tienes algúna duda envíanos un correo electrónico a la 
        siguiente dirección: altamirano.flv@gmail.com 
            
                                Attentamente 
            
                Coordinación de Actividades Complementarias 
                    Tecnológco Nacional de MéxicoITO
                    Instituto Tecnológico de Oaxaca';

        
        $est = "".$est[0]->email;
        $message = wordwrap($message, 80);

        Mail::raw($message, function ($message) use ($est) {
            
            $message->to($est)
                ->subject('Inscripción No Aprobada');
        });

        return redirect()->to('CoordAC/inscripPA/'.$dpt.'/1');
    }
/**Esta función se encarga de las bajas de actividades complementarias
 * solitadas por los estudiantes, actualiza eñ registro de la inscripción
 * a un estado de baja de actividad, y envia el correo electrónico de 
 * notificación por baja de actividad
 */
    public function f_bajaInscrip($id_ins, $dpt){

        Minscripcion::where('id_inscripcion', $id_ins)
        ->update(['aprobada' => 3]);

        $group = Minscripcion::where('id_inscripcion', $id_ins)->first();
        $cupo = Mgrupo::select('cupo_libre')->where('id_grupo', $group->id_grupo)->first();
        $cupo = $cupo->cupo_libre + 1;
        
        Mgrupo::where('id_grupo', $group->id_grupo)->update([
            'cupo_libre' => $cupo
        ]);
        
        $est = DB::select('SELECT g.clave , a.nombre AS actividad, 
                            p.nombre, p.apePat, p.apeMat, e.email
        FROM inscripcion AS i
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
        WHERE i.id_inscripcion = '.$id_ins);


            $message = 
        'Hola, '.$est[0]->nombre.' '.$est[0]->apePat.' '.$est[0]->apeMat.' 
        Te escribimos para notificarte que haz sido dado de baja de la actividad 

                "'.$est[0]->clave.' - '.$est[0]->actividad.'"

        si tienes algúna duda envíanos un correo electrónico a la 
        siguiente dirección: altamirano.flv@gmail.com 
                    
                                Attentamente 
                    
                Coordinación de Actividades Complementarias 
                    Tecnológco Nacional de MéxicoITO
                    Instituto Tecnológico de Oaxaca';
        

        $est = "".$est[0]->email;
        $message = wordwrap($message, 80);

        Mail::raw($message, function ($message) use ($est) {
            
            $message->to($est)
                ->subject('Baja Actividad Complementaria');
        });

        return redirect()->to('CoordAC/inscripBJ/'.$dpt.'/1');
    }

/**Esta función se encarga de las inscripciones realizadas por la 
 * coordinación de actividades complementarias, es decir, cuando 
 * un estudiante ya se inscribió a una actividad y solicita una
 * segunda actividad.
 */
    public function f_inscribir($stdnt, $dpt){

        $student = DB::select('SELECT e.num_control, p.nombre, p.apePat, 
                p.apeMat, e.semestre, c.nombre AS carrera, c.id_depto,
                    e.id_estudiante
            FROM estudiante AS e 
            JOIN persona AS p ON e.id_persona = p.id_persona 
            JOIN carrera AS c ON e.id_carrera = c.id_carrera 
            WHERE e.id_persona = '.$stdnt);

        $deptos = Mdepartamento::where('estado', 1)->get();

        $grupos = DB::select('SELECT g.clave, a.nombre, a.creditos, 
                d.nombre AS depto, a.restringida, g.id_grupo,
                d.id_depto, g.cupo_libre
            FROM grupo AS g
            JOIN actividad AS a ON g.id_actividad = a.id_actividad
            JOIN departamento AS d ON a.id_depto = d.id_depto
            JOIN periodo AS p ON g.id_periodo = p.id_periodo
            WHERE a.id_depto = '.$dpt.'
            AND p.estado = "Actual"
            AND g.estado = 1');

            $std = 0; $dpt = 0;
            foreach($student as $s){
    
                $std = $s->id_estudiante;
                $dpt = $s->id_depto;
            }

        return view('CoordAC.inscripciones.inscribir',
            ['student' => $student,
             'deptos' => $deptos,
             'grupos' => $grupos,
             'std' => $std,
             'dpt' => $dpt,
             'tipos' => $this->tipos()]);
    }

    // public function f_outime(Request $request, $dpt){
    public function f_outime($ns, $dpt){
        // $n_stds = $request->num_students;

        $dpts = Mdepartamento::select('id_depto', 'nombre')->get();

        $grupos = DB::select('SELECT g.id_grupo, g.clave, a.nombre, a.creditos
        FROM grupo AS g
        JOIN actividad AS a ON g.id_actividad = a.id_actividad
        JOIN departamento AS  d ON  a.id_depto =  d.id_depto
        JOIN periodo AS p ON g.id_periodo = p.id_periodo
        WHERE a.estado IN(SELECT estado FROM actividad WHERE estado = 1)
        AND p.estado = "Actual"
        AND a.id_depto = '.$dpt);

        return view('CoordAC.inscripciones.outime',
            ['tipos' => $this->tipos(),
            'ns' => $ns,
            'dpts' => $dpts,
            'groups' => $grupos]);
    }

    public function f_inscrip_outime(Request $request, $ns){
        
        $oficio;
        $_stds;
        $group = $request->group;
        $date = date('Y-m-d');
        
        for($i = 0; $i < $ns; $i++){
            $req = 'stds'.$i;
            $_std = $request->input($req);
            $_std = Mestudiante::select('id_estudiante')->where('num_control', $_std)->first();
            $_std = $_std->id_estudiante;
            $_stds[$i] = $_std;
        }

        $cupo = Mgrupo::where('id_grupo', $group)->first();

        if( $cupo->cupo_libre < $ns ){
   
            ?>
                <script>
                    alert("Cupo insuficiente");
                    location.href = "/CoordAC/inscrip_fuera_tiempo/1/1";
                </script>
            <?php

        }else{

            if($request->hasFile('oficio')){
                $oficio = $request->file('oficio')->store('public/inscripciones');
                $oficio = substr($oficio, 7); 
            }

            for($i = 0; $i < count($_stds); $i++){
                
                $ins = Minscripcion::create(['id_estudiante' => $_stds[$i], 'id_grupo' => $group,
                'fecha' => $date, 'aprobada' => 1]);
                
                Minscripcion_outime::create(['id_inscripcion' => $ins->id, 'oficio' => $oficio]);
            }

            $cupo = $cupo->cupo_libre - $ns;
            Mgrupo::where('id_grupo', $group)->update(['cupo_libre' => $cupo]);

            ?>
                <script>
                    // alert("Cupo insuficiente, solo hay '.$cupo->cupo_libre.' lugares disponibles.")
                    location.href = "/CoordAC/estudiantes/1";
                </script>
            <?php
        }
        
        // return redirect()->to('/CoordAC/estudiantes/1');
    }

    public function f_register($student, $group){

        $peri = Mperiodo::select('id_periodo')->where('estado', "Actual")->first();
        $cupo = Mgrupo::select('cupo_libre')->where('id_grupo', $group)->first();
        $cupo = $cupo->cupo_libre - 1;

        $inscrito = DB::select('SELECT COUNT(id_estudiante) as n_inscrip
            FROM inscripcion as i
            JOIN grupo as g ON i.id_grupo = g.id_grupo
            WHERE i.id_estudiante = '.$student.'
            AND g.id_periodo = '.$peri->id_periodo.'
            AND aprobada <> 4
            AND aprobada <> 3
            AND aprobada <> 2');

        foreach($inscrito as $i){

            if($i->n_inscrip >= 0 && $i->n_inscrip < 2){

                Minscripcion::create(['id_estudiante' => $student,
                    'id_grupo' => $group,
                    'fecha' => date('Y-m-d'),
                    'aprobada' => 1]);

                Mgrupo::where('id_grupo')->update([
                        'cupo_libre' => $cupo
                    ]);

                return redirect()->to('CoordAC/estudiantes/1');
            }else{

                ?><script>
                    alert('No se puede inscribir en más de dos actividades por semestre.');
                   location.href = "/CoordAC/estudiantes/1";
                </script><?php
            }

        }
    }

    public function logoutCAC(Request $request){

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect("IniciarSesion");
    }
/**función completamente lista
 * agregar la depuración de la tabla horarios_impresos en la función inicio
 * copiar la función en la funcion re_imprimir_horarios
 */
    public function fpdf_imprimir($id_g){

        $usuario_actual = auth()->user();
        $id_e = $usuario_actual->id;
        $periodo_ = Mperiodo::select('id_periodo', 'nombre', 'inicio')
            ->where('estado', "Actual")->first();
        $fecha_hoy = date('d - m - Y');

        $impresos = DB::select('SELECT id_grupo FROM horarios_impresos GROUP BY id_grupo');
        // foreach($impresos as $i){
            
        //     if($id_g == $i->id_grupo){
        //         return view('CoordAC.imp_horario',
        //             ['grupo' => $id_g,
        //              'tipos' => $this->tipos()]);
        //     }
        // }

        $impresos = DB::select('SELECT id_estudiante FROM horarios_impresos');
     
        $group_students = DB::select('SELECT p.nombre, p.apePat, p.apeMat, 
            e.num_control, e.semestre, c.nombre as carrera, e.id_estudiante
        FROM inscripcion AS i
        JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
        JOIN persona AS p ON e.id_persona = p.id_persona
        JOIN carrera AS c ON e.id_carrera = c.id_carrera
        JOIN grupo AS g ON i.id_grupo = g.id_grupo
            WHERE g.id_periodo = '.$periodo_->id_periodo.' 
            AND i.aprobada = 1
            AND i.id_grupo = '.$id_g);

        $other_students = [];
        for($i = 0; $i < count($group_students); $i++){

            $_student = DB::select('SELECT p.nombre, p.apePat, p.apeMat, 
                e.num_control, e.semestre, c.nombre as carrera, e.id_estudiante,
                g.id_grupo
            FROM inscripcion AS i
            JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            JOIN persona AS p ON e.id_persona = p.id_persona
            JOIN carrera AS c ON e.id_carrera = c.id_carrera
            JOIN grupo AS g ON i.id_grupo = g.id_grupo
                WHERE g.id_periodo = '.$periodo_->id_periodo.' 
                AND i.aprobada = 1
                AND i.id_grupo <> '.$id_g.'
                AND i.id_estudiante = '.$group_students[$i]->id_estudiante);
            
            if($_student != null)
                $other_students += $_student;
        }

        $estudiantes = []; $count = 0;
        for($n = 0; $n < count($group_students); $n++){

            for($m = 0; $m < count($other_students); $m++){
                if($other_students[$m]->id_estudiante != $group_students[$n]->id_estudiante){

                    $estudiantes[$count] = $group_students[$n];
                    $count++;
                }
            }
        }

        $grupo = DB::select('SELECT g.clave, a.nombre AS actividad, a.creditos, 
                l.nombre AS lugar, p.nombre, p.apePat, p.apeMat
                FROM grupo AS g
            JOIN persona AS p ON g.id_persona = p.id_persona
            JOIN actividad AS a ON g.id_actividad = a.id_actividad
            JOIN lugar AS l ON g.id_lugar = l.id_lugar
                WHERE g.id_grupo = '.$id_g);

        $horario = DB::select('SELECT ds.nombre, h.hora_inicio, h.hora_fin,
            h.id_grupo
            FROM grupo AS g
            LEFT JOIN horario AS h ON g.id_grupo = h.id_grupo
            LEFT JOIN dias_semana AS ds ON h.id_dia = ds.id_dia
                WHERE g.id_grupo = '.$id_g);

        for($i = 0; $i < count($estudiantes); $i++){
            if($impresos != null){
                for($j = 0; $j < count($impresos); $j++){
                    if($impresos[$j]->id_estudiante != $estudiantes[$i]->id_estudiante){

                        setlocale(LC_ALL,"es_MX.UTF-8");

                        if(($i % 2) == 0){
                            Fpdf::AddPage();
                            Fpdf::SetFont('Arial', '', 8);
                            Fpdf::SetMargins(30, 5 , 30);
                            Fpdf::SetAutoPageBreak(true);
                            Fpdf::Image("img/tec_nm.jpeg", 33, 17, 140, 17);   

                            Fpdf::setXY(10,33);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 

                            Fpdf::setXY(115,33);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

                            Fpdf::setXY(10, 39);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").$estudiantes[$i]->num_control, 0);

                            Fpdf::setXY(10, 45);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat." ".$estudiantes[$i]->apeMat), 0);

                            Fpdf::setXY(115, 39);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($estudiantes[$i]->semestre), 0);

                            Fpdf::setXY(10, 51);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($estudiantes[$i]->carrera), 0);

                            Fpdf::setXY(10, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(10, 13, 'ACTIV', 0);

                            Fpdf::setXY(37, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(10, 13, 'RESPON', 0);

                            Fpdf::setXY(64, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'GRUPO ', 0);

                            Fpdf::setXY(80, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'C ', 0);

                            Fpdf::setXY(84, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'LUGAR ', 0);

                            Fpdf::setXY(115, 71);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

                            Fpdf::SetFont('Arial', '', 9);

                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::setXY(20, 115);
                            Fpdf::Cell(5, 20, '___________________________________', 0);

                            Fpdf::setXY(23, 120);
                            Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                            Fpdf::setXY(23, 125);
                            Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

                            Fpdf::setXY(130, 115);
                            Fpdf::Cell(5, 20, '___________________________________', 0);

                            Fpdf::setXY(133, 120);
                            Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat), 0);

                            Fpdf::setXY(133, 125);
                            Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->apeMat), 0);

                            $a = strlen($grupo[0]->actividad);
                            $a = $a / 12;

                            for($j = 0; $j < $a; $j++){
                                $_activity = substr($grupo[0]->actividad, ($j*12), ($j+12));
                                Fpdf::setXY(10, 71 + ($j * 3));
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::Cell(1, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                            }

                            Fpdf::setXY(37, 77);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->nombre)), 0);
                            Fpdf::setXY(37, 80);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apePat)), 0);
                            Fpdf::setXY(37, 83);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apeMat)), 0);
                            
                            Fpdf::setXY(64, 78);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);

                            Fpdf::setXY(80, 70.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode($grupo[0]->creditos), 0);

                            Fpdf::setXY(84, 76.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($grupo[0]->lugar)), 0);
                        

                            $contador = 106;
                            foreach ($horario as $c)        {
                                
                                Fpdf::SetFont('Arial', 'B', 9);
                                Fpdf::setXY($contador , 60);
                                Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                                
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::setXY($contador, 55);
                                Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);
                                
                                Fpdf::setXY($contador, 53);
                                Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);
                                
                                $contador += 16;
                            }
                        
                        } else{
                            
                            Fpdf::Image("img/tec_nm.jpeg", 33, 150, 140, 17);   

                            Fpdf::setXY(10,166);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 

                            Fpdf::setXY(115,166);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

                            Fpdf::setXY(10, 172);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").utf8_decode($estudiantes[$i]->num_control), 0);

                            Fpdf::setXY(10, 178);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat." ".$estudiantes[$i]->apeMat), 0);

                            Fpdf::setXY(115, 172);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($estudiantes[$i]->semestre), 0);

                            Fpdf::setXY(10, 184);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($estudiantes[$i]->carrera), 0);

                            Fpdf::setXY(10, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(10, 13, 'ACTIV', 0);

                            Fpdf::setXY(37, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(10, 13, 'RESPON: ', 0);

                            Fpdf::setXY(64, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'GRUPO: ', 0);

                            Fpdf::setXY(80, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'C ', 0);

                            Fpdf::setXY(84, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'LUGAR: ', 0);

                            Fpdf::setXY(110, 204);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

                            Fpdf::SetFont('Arial', '', 9);

                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::setXY(20, 248);
                            Fpdf::Cell(5, 20, '___________________________________', 0);

                            Fpdf::setXY(23, 253);
                            Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                            Fpdf::setXY(23, 258);
                            Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

                            Fpdf::setXY(130, 248);
                            Fpdf::Cell(5, 20, '___________________________________', 0);

                            Fpdf::setXY(133, 253);
                            Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat), 0);

                            Fpdf::setXY(133, 258);
                            Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->apeMat), 0);


                            $a = strlen($grupo[0]->actividad);
                            $a = $a / 12;

                            for($j = 0; $j < $a; $j++){
                                $_activity = substr($grupo[0]->actividad, ($j*12), ($j+12));
                                Fpdf::setXY(10, 205 + ($j * 3));
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::Cell(60, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                            }

                            Fpdf::setXY(37, 211);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->nombre)), 0);
                            Fpdf::setXY(37, 214);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apePat)), 0);
                            Fpdf::setXY(37, 217);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apeMat)), 0);

                            Fpdf::setXY(64, 212);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);
                            
                            Fpdf::setXY(80, 204.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode($grupo[0]->creditos), 0);

                            Fpdf::setXY(84, 210.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($grupo[0]->lugar)), 0);

                            $contador = 106;
                            foreach ($horario as $c)        {
                                
                                Fpdf::SetFont('Arial', 'B', 9);
                                Fpdf::setXY($contador , 193);
                                Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                                
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::setXY($contador, 189);
                                Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);
                                
                                Fpdf::setXY($contador, 187);
                                Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);
                                
                                $contador += 16;
                            }
                        }
                    } //Si es horario del estudiante ya fue impreso no se genera ese horario
                }    //Cierre del ciclo for para el recorrido de los horarios impresos
            //construcción de horarios si la tabla horarios_impresos está vacia
            }else{

                setlocale(LC_ALL,"es_MX.UTF-8");

                if(($i % 2) == 0){
                    Fpdf::AddPage();
                    Fpdf::SetFont('Arial', '', 8);
                    Fpdf::SetMargins(30, 5 , 30);
                    Fpdf::SetAutoPageBreak(true);
                    Fpdf::Image("img/tec_nm.jpeg", 33, 17, 140, 17);   

                    Fpdf::setXY(10,33);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 

                    Fpdf::setXY(115,33);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

                    Fpdf::setXY(10, 39);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").$estudiantes[$i]->num_control, 0);

                    Fpdf::setXY(10, 45);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat." ".$estudiantes[$i]->apeMat), 0);

                    Fpdf::setXY(115, 39);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($estudiantes[$i]->semestre), 0);

                    Fpdf::setXY(10, 51);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($estudiantes[$i]->carrera), 0);

                    Fpdf::setXY(10, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(10, 13, 'ACTIV', 0);

                    Fpdf::setXY(37, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(10, 13, 'RESPON', 0);

                    Fpdf::setXY(64, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'GRUPO ', 0);

                    Fpdf::setXY(80, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'C ', 0);

                    Fpdf::setXY(84, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'LUGAR ', 0);

                    Fpdf::setXY(115, 71);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

                    Fpdf::SetFont('Arial', '', 9);

                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::setXY(20, 115);
                    Fpdf::Cell(5, 20, '___________________________________', 0);

                    Fpdf::setXY(23, 120);
                    Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                    Fpdf::setXY(23, 125);
                    Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

                    Fpdf::setXY(130, 115);
                    Fpdf::Cell(5, 20, '___________________________________', 0);

                    Fpdf::setXY(133, 120);
                    Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat), 0);

                    Fpdf::setXY(133, 125);
                    Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->apeMat), 0);


                    $a = strlen($grupo[0]->actividad);
                    $a = $a / 12;

                    for($j = 0; $j < $a; $j++){
                        $_activity = substr($grupo[0]->actividad, ($j*12), ($j+12));
                        Fpdf::setXY(10, 71 + ($j * 3));
                        Fpdf::SetFont('Arial', '', 9);
                        Fpdf::Cell(1, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                    }
                    
                    Fpdf::setXY(37, 77);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->nombre)), 0);
                    Fpdf::setXY(37, 80);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apePat)), 0);
                    Fpdf::setXY(37, 83);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apeMat)), 0);
                    
                    Fpdf::setXY(64, 78);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);

                    Fpdf::setXY(80, 70.5);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, utf8_decode($grupo[0]->creditos), 0);

                    Fpdf::setXY(84, 76.5);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($grupo[0]->lugar)), 0);
                

                    $contador = 106;
                    foreach ($horario as $c)        {
                        
                        Fpdf::SetFont('Arial', 'B', 9);
                        Fpdf::setXY($contador , 60);
                        Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                        
                        Fpdf::SetFont('Arial', '', 9);
                        Fpdf::setXY($contador, 55);
                        Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);
                        
                        Fpdf::setXY($contador, 53);
                        Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);
                        
                        $contador += 16;
                    }
                
                } else{
                    
                    Fpdf::Image("img/tec_nm.jpeg", 33, 150, 140, 17);   

                    Fpdf::setXY(10,166);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 

                    Fpdf::setXY(115,166);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

                    Fpdf::setXY(10, 172);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").utf8_decode($estudiantes[$i]->num_control), 0);

                    Fpdf::setXY(10, 178);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat." ".$estudiantes[$i]->apeMat), 0);

                    Fpdf::setXY(115, 172);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($estudiantes[$i]->semestre), 0);

                    Fpdf::setXY(10, 184);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($estudiantes[$i]->carrera), 0);

                    Fpdf::setXY(10, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(10, 13, 'ACTIV', 0);

                    Fpdf::setXY(37, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(10, 13, 'RESPON: ', 0);

                    Fpdf::setXY(64, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'GRUPO: ', 0);

                    Fpdf::setXY(80, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'C ', 0);

                    Fpdf::setXY(84, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'LUGAR: ', 0);

                    Fpdf::setXY(110, 204);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

                    Fpdf::SetFont('Arial', '', 9);

                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::setXY(20, 248);
                    Fpdf::Cell(5, 20, '___________________________________', 0);

                    Fpdf::setXY(23, 253);
                    Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                    Fpdf::setXY(23, 258);
                    Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

                    Fpdf::setXY(130, 248);
                    Fpdf::Cell(5, 20, '___________________________________', 0);

                    Fpdf::setXY(133, 253);
                    Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat), 0);

                    Fpdf::setXY(133, 258);
                    Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->apeMat), 0);

                    $a = strlen($grupo[0]->actividad);
                    $a = $a / 12;

                    for($j = 0; $j < $a; $j++){
                        $_activity = substr($grupo[0]->actividad, ($j*12), ($j+12));
                        Fpdf::setXY(10, 205 + ($j * 3));
                        Fpdf::SetFont('Arial', '', 9);
                        Fpdf::Cell(60, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                    }

                    Fpdf::setXY(37, 211);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->nombre)), 0);
                    Fpdf::setXY(37, 214);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apePat)), 0);
                    Fpdf::setXY(37, 217);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apeMat)), 0);

                    Fpdf::setXY(64, 212);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);
                    
                    Fpdf::setXY(80, 204.5);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, utf8_decode($grupo[0]->creditos), 0);

                    Fpdf::setXY(84, 210.5);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($grupo[0]->lugar)), 0);

                    $contador = 106;
                    foreach ($horario as $c)        {
                        
                        Fpdf::SetFont('Arial', 'B', 9);
                        Fpdf::setXY($contador , 193);
                        Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                        
                        Fpdf::SetFont('Arial', '', 9);
                        Fpdf::setXY($contador, 189);
                        Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);
                        
                        Fpdf::setXY($contador, 187);
                        Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);
                        
                        $contador += 16;
                    }
                    
                }
            }
        }

        for($i = 0; $i < count($other_students); $i++){
            $par = $i + count($estudiantes);

            $other_grupo = DB::select('SELECT g.clave, a.nombre AS actividad, a.creditos, 
                l.nombre AS lugar, p.nombre, p.apePat, p.apeMat, g.id_grupo
                FROM grupo AS g
            JOIN persona AS p ON g.id_persona = p.id_persona
            JOIN actividad AS a ON g.id_actividad = a.id_actividad
            JOIN lugar AS l ON g.id_lugar = l.id_lugar
                WHERE g.id_grupo = '.$other_students[$i]->id_grupo);

            // Mhorarios_impresos::create(['id_grupo' => $other_grupo[0]->id_grupo, 
            //     'id_estudiante' => $other_students[$i]->id_estudiante]);

            $other_horario = DB::select('SELECT ds.nombre, h.hora_inicio, h.hora_fin,
                h.id_grupo
                FROM grupo AS g
                LEFT JOIN horario AS h ON g.id_grupo = h.id_grupo
                LEFT JOIN dias_semana AS ds ON h.id_dia = ds.id_dia
                    WHERE g.id_grupo = '.$other_students[$i]->id_grupo);

            if($impresos != null){
                for($j = 0; $j < count($impresos); $j++){
                    if($impresos[$j]->id_estudiante != $other_students[$i]->id_estudiante){

                        setlocale(LC_ALL,"es_MX.UTF-8");

                        if(($par % 2) == 0){
                            Fpdf::AddPage();
                            Fpdf::SetFont('Arial', '', 8);
                            Fpdf::SetMargins(30, 5 , 30);
                            Fpdf::SetAutoPageBreak(true);
                            Fpdf::Image("img/tec_nm.jpeg", 33, 17, 140, 17);   

                            Fpdf::setXY(10,33);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 

                            Fpdf::setXY(115,33);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

                            Fpdf::setXY(10, 39);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").$other_students[$i]->num_control, 0);

                            Fpdf::setXY(10, 45);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($other_students[$i]->nombre." ".$other_students[$i]->apePat." ".$other_students[$i]->apeMat), 0);

                            Fpdf::setXY(115, 39);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($other_students[$i]->semestre), 0);

                            Fpdf::setXY(10, 51);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($other_students[$i]->carrera), 0);

                            Fpdf::setXY(10, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(10, 13, 'ACTIV', 0);

                            Fpdf::setXY(37, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(10, 13, 'RESPON', 0);

                            Fpdf::setXY(64, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'GRUPO ', 0);

                            Fpdf::setXY(80, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'C ', 0);

                            Fpdf::setXY(84, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'LUGAR ', 0);

                            Fpdf::setXY(115, 71);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

                            Fpdf::SetFont('Arial', '', 9);

                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::setXY(20, 115);
                            Fpdf::Cell(5, 20, '___________________________________', 0);

                            Fpdf::setXY(23, 120);
                            Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                            Fpdf::setXY(23, 125);
                            Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

                            Fpdf::setXY(130, 115);
                            Fpdf::Cell(5, 20, '___________________________________', 0);

                            Fpdf::setXY(133, 120);
                            Fpdf::Cell(5, 20, utf8_decode($other_students[$i]->nombre." ".$other_students[$i]->apePat), 0);

                            Fpdf::setXY(133, 125);
                            Fpdf::Cell(5, 20, utf8_decode($other_students[$i]->apeMat), 0);

                            //Primera actividad
                            $a = strlen($grupo[0]->actividad);
                            $a = $a / 12;

                            for($j = 0; $j < $a; $j++){
                                $_activity = substr($grupo[0]->actividad, ($j*12), ($j+12));
                                Fpdf::setXY(10, 70 + ($j * 3));
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::Cell(1, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                            }
                            
                            Fpdf::setXY(37, 76);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->nombre)), 0);
                            Fpdf::setXY(37, 79);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apePat)), 0);
                            Fpdf::setXY(37, 82);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apeMat)), 0);
                            
                            Fpdf::setXY(64, 77);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);

                            Fpdf::setXY(80, 69.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode($grupo[0]->creditos), 0);

                            Fpdf::setXY(84, 75.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($grupo[0]->lugar)), 0);

                            //Segunda actividad
                            $a = strlen($other_grupo[$i]->actividad);
                            $a = $a / 12;

                            for($j = 0; $j < $a; $j++){
                                $_activity = substr($other_grupo[$i]->actividad, ($j*12), ($j+12));
                                Fpdf::setXY(10, 85 + ($j * 3));
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::Cell(60, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                            }

                            Fpdf::setXY(37, 91);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($other_grupo[$i]->nombre)), 0);
                            Fpdf::setXY(37, 94);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($other_grupo[$i]->apePat)), 0);
                            Fpdf::setXY(37, 97);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($other_grupo[$i]->apeMat)), 0);

                            Fpdf::setXY(64, 92);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(50, 10, utf8_decode($other_grupo[$i]->clave), 0);
                            
                            Fpdf::setXY(80, 84.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode($other_grupo[$i]->creditos), 0);

                            Fpdf::setXY(84, 90.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($other_grupo[$i]->lugar)), 0);

                            $contador = 90; $cont2 = 16;
                            $gru = $horario[0]->id_grupo;
                            foreach ($horario as $c)        {
                                $contador += 16;
                                
                                // if($c->id_grupo == $gru){

                                    Fpdf::SetFont('Arial', 'B', 9);
                                    Fpdf::setXY($contador , 60);
                                    Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                                    
                                    Fpdf::SetFont('Arial', '', 9);
                                    Fpdf::setXY($contador, 54);
                                    Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                                    Fpdf::setXY($contador, 52);
                                    Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

                                // }else{
                                //     $contador = 90;

                                //     Fpdf::SetFont('Arial', 'B', 9);
                                //     Fpdf::setXY($contador + $cont2, 79);
                                //     Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                                    
                                //     Fpdf::SetFont('Arial', '', 9);
                                //     Fpdf::setXY($contador + $cont2, 73);
                                //     Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                                //     Fpdf::setXY($contador + $cont2, 71);
                                //     Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

                                //     $cont2 += 16;
                                // }
                            }

                            $contador = 90; $cont2 = 16;
                            $gru = $other_horario[0]->id_grupo;
                            foreach ($other_horario as $c)        {
                                $contador += 16;
                                
                                // if($c->id_grupo == $gru){

                                    Fpdf::SetFont('Arial', 'B', 9);
                                    Fpdf::setXY($contador , 79);
                                    Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                                    
                                    Fpdf::SetFont('Arial', '', 9);
                                    Fpdf::setXY($contador, 73);
                                    Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                                    Fpdf::setXY($contador, 71);
                                    Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

                                // }else{
                                //     $contador = 90;

                                //     Fpdf::SetFont('Arial', 'B', 9);
                                //     Fpdf::setXY($contador + $cont2, 79);
                                //     Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                                    
                                //     Fpdf::SetFont('Arial', '', 9);
                                //     Fpdf::setXY($contador + $cont2, 73);
                                //     Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                                //     Fpdf::setXY($contador + $cont2, 71);
                                //     Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

                                //     $cont2 += 16;
                                // }
                            }
                        
                        } else{
                            
                            Fpdf::Image("img/tec_nm.jpeg", 33, 150, 140, 17);   

                            Fpdf::setXY(10,166);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 

                            Fpdf::setXY(115,166);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

                            Fpdf::setXY(10, 172);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").utf8_decode($other_students[$i]->num_control), 0);

                            Fpdf::setXY(10, 178);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($other_students[$i]->nombre." ".$other_students[$i]->apePat." ".$other_students[$i]->apeMat), 0);

                            Fpdf::setXY(115, 172);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($other_students[$i]->semestre), 0);

                            Fpdf::setXY(10, 184);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($other_students[$i]->carrera), 0);

                            Fpdf::setXY(10, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(10, 13, 'ACTIV', 0);

                            Fpdf::setXY(37, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(10, 13, 'RESPON: ', 0);

                            Fpdf::setXY(64, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'GRUPO: ', 0);

                            Fpdf::setXY(80, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'C ', 0);

                            Fpdf::setXY(84, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'LUGAR: ', 0);

                            Fpdf::setXY(110, 204);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

                            Fpdf::SetFont('Arial', '', 9);

                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::setXY(20, 248);
                            Fpdf::Cell(5, 20, '___________________________________', 0);

                            Fpdf::setXY(23, 253);
                            Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                            Fpdf::setXY(23, 258);
                            Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

                            Fpdf::setXY(130, 248);
                            Fpdf::Cell(5, 20, '___________________________________', 0);

                            Fpdf::setXY(133, 253);
                            Fpdf::Cell(5, 20, utf8_decode($other_students[$i]->nombre." ".$other_students[$i]->apePat), 0);

                            Fpdf::setXY(133, 258);
                            Fpdf::Cell(5, 20, utf8_decode($other_students[$i]->apeMat), 0);

                            //Primer actividad
                            $a = strlen($grupo[0]->actividad);
                            $a = $a / 12;

                            for($j = 0; $j < $a; $j++){
                                $_activity = substr($grupo[0]->actividad, ($j*12), ($j+12));
                                Fpdf::setXY(10, 205 + ($j * 3));
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::Cell(60, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                            }

                            Fpdf::setXY(37, 211);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->nombre)), 0);
                            Fpdf::setXY(37, 214);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apePat)), 0);
                            Fpdf::setXY(37, 217);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apeMat)), 0);

                            Fpdf::setXY(64, 212);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);
                            
                            Fpdf::setXY(80, 204.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode($grupo[0]->creditos), 0);

                            Fpdf::setXY(84, 210.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($grupo[0]->lugar)), 0);

                            //Segunda actividad
                            $a = strlen($other_grupo[$i]->actividad);
                            $a = $a / 12;

                            for($j = 0; $j < $a; $j++){
                                $_activity = substr($other_grupo[$i]->actividad, ($j*12), ($j+12));
                                Fpdf::setXY(10, 222 + ($j * 3));
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::Cell(60, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                            }

                            Fpdf::setXY(37, 228);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($other_grupo[$i]->nombre)), 0);
                            Fpdf::setXY(37, 231);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($other_grupo[$i]->apePat)), 0);
                            Fpdf::setXY(37, 234);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($other_grupo[$i]->apeMat)), 0);

                            Fpdf::setXY(64, 229);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(50, 10, utf8_decode($other_grupo[$i]->clave), 0);
                            
                            Fpdf::setXY(80, 221.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode($other_grupo[$i]->creditos), 0);

                            Fpdf::setXY(84, 227.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($other_grupo[$i]->lugar)), 0);

                            $contador = 106;
                            foreach ($horario as $c)        {
                                
                                Fpdf::SetFont('Arial', 'B', 9);
                                Fpdf::setXY($contador , 193);
                                Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                                
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::setXY($contador, 188);
                                Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);
                                
                                Fpdf::setXY($contador, 186);
                                Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);
                                
                                $contador += 16;
                            }

                            $contador = 106;
                            foreach ($other_horario as $c)        {
                                
                                Fpdf::SetFont('Arial', 'B', 9);
                                Fpdf::setXY($contador , 212);
                                Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                                
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::setXY($contador, 207);
                                Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);
                                
                                Fpdf::setXY($contador, 205);
                                Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);
                                
                                $contador += 16;
                            }
                        }
                    } //Si es horario del estudiante ya fue impreso no se genera ese horario
                }    //Cierre del ciclo for para el recorrido de los horarios impresos
            //construcción de horarios si la tabla horarios_impresos está vacia
            }else{

                setlocale(LC_ALL,"es_MX.UTF-8");

                if(($i % 2) == 0){
                    Fpdf::AddPage();
                    Fpdf::SetFont('Arial', '', 8);
                    Fpdf::SetMargins(30, 5 , 30);
                    Fpdf::SetAutoPageBreak(true);
                    Fpdf::Image("img/tec_nm.jpeg", 33, 17, 140, 17);   

                    Fpdf::setXY(10,33);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0);

                    Fpdf::setXY(115,33);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

                    Fpdf::setXY(10, 39);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").$other_students[$i]->num_control, 0);

                    Fpdf::setXY(10, 45);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($other_students[$i]->nombre." ".$other_students[$i]->apePat." ".$other_students[$i]->apeMat), 0);

                    Fpdf::setXY(115, 39);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($other_students[$i]->semestre), 0);

                    Fpdf::setXY(10, 51);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($other_students[$i]->carrera), 0);

                    Fpdf::setXY(10, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(10, 13, 'ACTIV', 0);

                    Fpdf::setXY(37, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(10, 13, 'RESPON', 0);

                    Fpdf::setXY(64, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'GRUPO ', 0);

                    Fpdf::setXY(80, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'C ', 0);

                    Fpdf::setXY(84, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'LUGAR ', 0);

                    Fpdf::setXY(115, 71);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

                    Fpdf::SetFont('Arial', '', 9);

                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::setXY(20, 115);
                    Fpdf::Cell(5, 20, '___________________________________', 0);

                    Fpdf::setXY(23, 120);
                    Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                    Fpdf::setXY(23, 125);
                    Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

                    Fpdf::setXY(130, 115);
                    Fpdf::Cell(5, 20, '___________________________________', 0);

                    Fpdf::setXY(133, 120);
                    Fpdf::Cell(5, 20, utf8_decode($other_students[$i]->nombre." ".$other_students[$i]->apePat), 0);

                    Fpdf::setXY(133, 125);
                    Fpdf::Cell(5, 20, utf8_decode($other_students[$i]->apeMat), 0);

                    $a = strlen($grupo[0]->actividad);
                    $a = $a / 12;

                    for($j = 0; $j < $a; $j++){
                        $_activity = substr($grupo[0]->actividad, ($j*12), ($j+12));
                        Fpdf::setXY(10, 70 + ($j * 3));
                        Fpdf::SetFont('Arial', '', 9);
                        Fpdf::Cell(1, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                    }
                    
                    Fpdf::setXY(37, 76);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->nombre)), 0);
                    Fpdf::setXY(37, 79);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apePat)), 0);
                    Fpdf::setXY(37, 82);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apeMat)), 0);
                    
                    Fpdf::setXY(64, 77);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);

                    Fpdf::setXY(80, 69.5);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, utf8_decode($grupo[0]->creditos), 0);

                    Fpdf::setXY(84, 75.5);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($grupo[0]->lugar)), 0);
                    

                    $contador = 90; $cont2 = 16;
                    $gru = $horario[0]->id_grupo;
                    foreach ($horario as $c)        {
                        $contador += 16;
                        
                        if($c->id_grupo == $gru){

                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::setXY($contador , 60);
                            Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                            
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::setXY($contador, 56);
                            Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                            Fpdf::setXY($contador, 54);
                            Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

                        }else{
                            $contador = 90;

                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::setXY($contador + $cont2, 79);
                            Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                            
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::setXY($contador + $cont2, 73);
                            Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                            Fpdf::setXY($contador + $cont2, 71);
                            Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

                            $cont2 += 16;
                        }
                    }
                
                } else{
                    
                    Fpdf::Image("img/tec_nm.jpeg", 33, 150, 140, 17);   

                    Fpdf::setXY(10,166);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 

                    Fpdf::setXY(115,166);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

                    Fpdf::setXY(10, 172);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").utf8_decode($other_students[$i]->num_control), 0);

                    Fpdf::setXY(10, 178);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($other_students[$i]->nombre." ".$other_students[$i]->apePat." ".$other_students[$i]->apeMat), 0);

                    Fpdf::setXY(115, 172);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($other_students[$i]->semestre), 0);

                    Fpdf::setXY(10, 184);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($other_students[$i]->carrera), 0);

                    Fpdf::setXY(10, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(10, 13, 'ACTIV', 0);

                    Fpdf::setXY(37, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(10, 13, 'RESPON: ', 0);

                    Fpdf::setXY(64, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'GRUPO: ', 0);

                    Fpdf::setXY(80, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'C ', 0);

                    Fpdf::setXY(84, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'LUGAR: ', 0);

                    Fpdf::setXY(110, 204);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

                    Fpdf::SetFont('Arial', '', 9);

                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::setXY(20, 248);
                    Fpdf::Cell(5, 20, '___________________________________', 0);

                    Fpdf::setXY(23, 253);
                    Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                    Fpdf::setXY(23, 258);
                    Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

                    Fpdf::setXY(130, 248);
                    Fpdf::Cell(5, 20, '___________________________________', 0);

                    Fpdf::setXY(133, 253);
                    Fpdf::Cell(5, 20, utf8_decode($other_students[$i]->nombre." ".$other_students[$i]->apePat), 0);

                    Fpdf::setXY(133, 258);
                    Fpdf::Cell(5, 20, utf8_decode($other_students[$i]->apeMat), 0);

                    $a = strlen($grupo[0]->actividad);
                    $a = $a / 12;

                    for($j = 0; $j < $a; $j++){
                        $_activity = substr($grupo[0]->actividad, ($j*12), ($j+12));
                        Fpdf::setXY(10, 205 + ($j * 3));
                        Fpdf::SetFont('Arial', '', 9);
                        Fpdf::Cell(60, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                    }

                    Fpdf::setXY(37, 211);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->nombre)), 0);
                    Fpdf::setXY(37, 214);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apePat)), 0);
                    Fpdf::setXY(37, 217);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apeMat)), 0);

                    Fpdf::setXY(64, 212);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);
                    
                    Fpdf::setXY(80, 205);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, utf8_decode($grupo[0]->creditos), 0);

                    Fpdf::setXY(84, 211);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($grupo[0]->lugar)), 0);
                    

                    $contador = 90; $cont2 = 16;
                    $gru = $horario[0]->id_grupo;
                    foreach ($horario as $c)        {
                        $contador += 16;
                        
                        if($c->id_grupo == $gru){

                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::setXY($contador , 193);
                            Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);

                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::setXY($contador, 191);
                            Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                            Fpdf::setXY($contador, 189);
                            Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);
                            
                        }else{
                            $contador = 90;

                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::setXY($contador + $cont2, 215);
                            Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);

                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::setXY($contador + $cont2, 209);
                            Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                            Fpdf::setXY($contador + $cont2, 207);
                            Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

                            $cont2 += 16;
                        }
                    }
                }
            }
        }
        
        // foreach($estudiantes as $e){
        //     Mhorarios_impresos::create(['id_grupo' => $id_g, 
        //         'id_estudiante' => $e->id_estudiante]);
        // }

        
        $tipo = 'I';
        $nombre_archivo = 'horario-'.$grupo[0]->nombre."-".$fecha_hoy.'.pdf';
    
        $headers = ['Content-Type' => 'application/pdf'];

        return response()->file(Fpdf::Output($tipo, $nombre_archivo));
    }

    public function re_imprimir_grupo($id_g)    {

        $usuario_actual = auth()->user();
        $id_e = $usuario_actual->id;
        $periodo_ = Mperiodo::select('id_periodo', 'nombre', 'inicio')
            ->where('estado', "Actual")->first();
        $fecha_hoy = date('d - m - Y');

        $impresos = DB::select('SELECT id_estudiante FROM horarios_impresos');
     
        $group_students = DB::select('SELECT p.nombre, p.apePat, p.apeMat, 
            e.num_control, e.semestre, c.nombre as carrera, e.id_estudiante
        FROM inscripcion AS i
        JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
        JOIN persona AS p ON e.id_persona = p.id_persona
        JOIN carrera AS c ON e.id_carrera = c.id_carrera
        JOIN grupo AS g ON i.id_grupo = g.id_grupo
            WHERE g.id_periodo = '.$periodo_->id_periodo.' 
            AND i.aprobada = 1
            AND i.id_grupo = '.$id_g);

        $other_students = [];
        for($i = 0; $i < count($group_students); $i++){

            $_student = DB::select('SELECT p.nombre, p.apePat, p.apeMat, 
                e.num_control, e.semestre, c.nombre as carrera, e.id_estudiante,
                g.id_grupo
            FROM inscripcion AS i
            JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            JOIN persona AS p ON e.id_persona = p.id_persona
            JOIN carrera AS c ON e.id_carrera = c.id_carrera
            JOIN grupo AS g ON i.id_grupo = g.id_grupo
                WHERE g.id_periodo = '.$periodo_->id_periodo.' 
                AND i.aprobada = 1
                AND i.id_grupo <> '.$id_g.'
                AND i.id_estudiante = '.$group_students[$i]->id_estudiante);
            
            if($_student != null)
                $other_students += $_student;
        }

        $estudiantes = []; $count = 0;
        for($n = 0; $n < count($group_students); $n++){

            for($m = 0; $m < count($other_students); $m++){
                if($other_students[$m]->id_estudiante != $group_students[$n]->id_estudiante){

                    $estudiantes[$count] = $group_students[$n];
                    $count++;
                }
            }
        }

        $grupo = DB::select('SELECT g.clave, a.nombre AS actividad, a.creditos, 
                l.nombre AS lugar, p.nombre, p.apePat, p.apeMat
                FROM grupo AS g
            JOIN persona AS p ON g.id_persona = p.id_persona
            JOIN actividad AS a ON g.id_actividad = a.id_actividad
            JOIN lugar AS l ON g.id_lugar = l.id_lugar
                WHERE g.id_grupo = '.$id_g);

        $horario = DB::select('SELECT ds.nombre, h.hora_inicio, h.hora_fin,
            h.id_grupo
            FROM grupo AS g
            LEFT JOIN horario AS h ON g.id_grupo = h.id_grupo
            LEFT JOIN dias_semana AS ds ON h.id_dia = ds.id_dia
                WHERE g.id_grupo = '.$id_g);

        for($i = 0; $i < count($estudiantes); $i++){
            if($impresos != null){
                for($j = 0; $j < count($impresos); $j++){
                    if($impresos[$j]->id_estudiante != $estudiantes[$i]->id_estudiante){

                        setlocale(LC_ALL,"es_MX.UTF-8");

                        if(($i % 2) == 0){
                            Fpdf::AddPage();
                            Fpdf::SetFont('Arial', '', 8);
                            Fpdf::SetMargins(30, 5 , 30);
                            Fpdf::SetAutoPageBreak(true);
                            Fpdf::Image("img/tec_nm.jpeg", 33, 17, 140, 17);   

                            Fpdf::setXY(10,33);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 

                            Fpdf::setXY(115,33);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

                            Fpdf::setXY(10, 39);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").$estudiantes[$i]->num_control, 0);

                            Fpdf::setXY(10, 45);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat." ".$estudiantes[$i]->apeMat), 0);

                            Fpdf::setXY(115, 39);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($estudiantes[$i]->semestre), 0);

                            Fpdf::setXY(10, 51);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($estudiantes[$i]->carrera), 0);

                            Fpdf::setXY(10, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(10, 13, 'ACTIV', 0);

                            Fpdf::setXY(37, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(10, 13, 'RESPON', 0);

                            Fpdf::setXY(64, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'GRUPO ', 0);

                            Fpdf::setXY(80, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'C ', 0);

                            Fpdf::setXY(84, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'LUGAR ', 0);

                            Fpdf::setXY(115, 71);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

                            Fpdf::SetFont('Arial', '', 9);

                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::setXY(20, 115);
                            Fpdf::Cell(5, 20, '___________________________________', 0);

                            Fpdf::setXY(23, 120);
                            Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                            Fpdf::setXY(23, 125);
                            Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

                            Fpdf::setXY(130, 115);
                            Fpdf::Cell(5, 20, '___________________________________', 0);

                            Fpdf::setXY(133, 120);
                            Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat), 0);

                            Fpdf::setXY(133, 125);
                            Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->apeMat), 0);

                            $a = strlen($grupo[0]->actividad);
                            $a = $a / 12;

                            for($j = 0; $j < $a; $j++){
                                $_activity = substr($grupo[0]->actividad, ($j*12), ($j+12));
                                Fpdf::setXY(10, 71 + ($j * 3));
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::Cell(1, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                            }

                            Fpdf::setXY(37, 77);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->nombre)), 0);
                            Fpdf::setXY(37, 80);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apePat)), 0);
                            Fpdf::setXY(37, 83);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apeMat)), 0);
                            
                            Fpdf::setXY(64, 78);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);

                            Fpdf::setXY(80, 70.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode($grupo[0]->creditos), 0);

                            Fpdf::setXY(84, 76.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($grupo[0]->lugar)), 0);
                        

                            $contador = 106;
                            foreach ($horario as $c)        {
                                
                                Fpdf::SetFont('Arial', 'B', 9);
                                Fpdf::setXY($contador , 60);
                                Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                                
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::setXY($contador, 55);
                                Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);
                                
                                Fpdf::setXY($contador, 53);
                                Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);
                                
                                $contador += 16;
                            }
                        
                        } else{
                            
                            Fpdf::Image("img/tec_nm.jpeg", 33, 150, 140, 17);   

                            Fpdf::setXY(10,166);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 

                            Fpdf::setXY(115,166);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

                            Fpdf::setXY(10, 172);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").utf8_decode($estudiantes[$i]->num_control), 0);

                            Fpdf::setXY(10, 178);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat." ".$estudiantes[$i]->apeMat), 0);

                            Fpdf::setXY(115, 172);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($estudiantes[$i]->semestre), 0);

                            Fpdf::setXY(10, 184);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($estudiantes[$i]->carrera), 0);

                            Fpdf::setXY(10, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(10, 13, 'ACTIV', 0);

                            Fpdf::setXY(37, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(10, 13, 'RESPON: ', 0);

                            Fpdf::setXY(64, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'GRUPO: ', 0);

                            Fpdf::setXY(80, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'C ', 0);

                            Fpdf::setXY(84, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'LUGAR: ', 0);

                            Fpdf::setXY(110, 204);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

                            Fpdf::SetFont('Arial', '', 9);

                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::setXY(20, 248);
                            Fpdf::Cell(5, 20, '___________________________________', 0);

                            Fpdf::setXY(23, 253);
                            Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                            Fpdf::setXY(23, 258);
                            Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

                            Fpdf::setXY(130, 248);
                            Fpdf::Cell(5, 20, '___________________________________', 0);

                            Fpdf::setXY(133, 253);
                            Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat), 0);

                            Fpdf::setXY(133, 258);
                            Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->apeMat), 0);


                            $a = strlen($grupo[0]->actividad);
                            $a = $a / 12;

                            for($j = 0; $j < $a; $j++){
                                $_activity = substr($grupo[0]->actividad, ($j*12), ($j+12));
                                Fpdf::setXY(10, 205 + ($j * 3));
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::Cell(60, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                            }

                            Fpdf::setXY(37, 211);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->nombre)), 0);
                            Fpdf::setXY(37, 214);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apePat)), 0);
                            Fpdf::setXY(37, 217);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apeMat)), 0);

                            Fpdf::setXY(64, 212);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);
                            
                            Fpdf::setXY(80, 204.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode($grupo[0]->creditos), 0);

                            Fpdf::setXY(84, 210.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($grupo[0]->lugar)), 0);

                            $contador = 106;
                            foreach ($horario as $c)        {
                                
                                Fpdf::SetFont('Arial', 'B', 9);
                                Fpdf::setXY($contador , 193);
                                Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                                
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::setXY($contador, 189);
                                Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);
                                
                                Fpdf::setXY($contador, 187);
                                Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);
                                
                                $contador += 16;
                            }
                        }
                    } //Si es horario del estudiante ya fue impreso no se genera ese horario
                }    //Cierre del ciclo for para el recorrido de los horarios impresos
            //construcción de horarios si la tabla horarios_impresos está vacia
            }else{

                setlocale(LC_ALL,"es_MX.UTF-8");

                if(($i % 2) == 0){
                    Fpdf::AddPage();
                    Fpdf::SetFont('Arial', '', 8);
                    Fpdf::SetMargins(30, 5 , 30);
                    Fpdf::SetAutoPageBreak(true);
                    Fpdf::Image("img/tec_nm.jpeg", 33, 17, 140, 17);   

                    Fpdf::setXY(10,33);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 

                    Fpdf::setXY(115,33);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

                    Fpdf::setXY(10, 39);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").$estudiantes[$i]->num_control, 0);

                    Fpdf::setXY(10, 45);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat." ".$estudiantes[$i]->apeMat), 0);

                    Fpdf::setXY(115, 39);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($estudiantes[$i]->semestre), 0);

                    Fpdf::setXY(10, 51);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($estudiantes[$i]->carrera), 0);

                    Fpdf::setXY(10, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(10, 13, 'ACTIV', 0);

                    Fpdf::setXY(37, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(10, 13, 'RESPON', 0);

                    Fpdf::setXY(64, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'GRUPO ', 0);

                    Fpdf::setXY(80, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'C ', 0);

                    Fpdf::setXY(84, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'LUGAR ', 0);

                    Fpdf::setXY(115, 71);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

                    Fpdf::SetFont('Arial', '', 9);

                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::setXY(20, 115);
                    Fpdf::Cell(5, 20, '___________________________________', 0);

                    Fpdf::setXY(23, 120);
                    Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                    Fpdf::setXY(23, 125);
                    Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

                    Fpdf::setXY(130, 115);
                    Fpdf::Cell(5, 20, '___________________________________', 0);

                    Fpdf::setXY(133, 120);
                    Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat), 0);

                    Fpdf::setXY(133, 125);
                    Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->apeMat), 0);


                    $a = strlen($grupo[0]->actividad);
                    $a = $a / 12;

                    for($j = 0; $j < $a; $j++){
                        $_activity = substr($grupo[0]->actividad, ($j*12), ($j+12));
                        Fpdf::setXY(10, 71 + ($j * 3));
                        Fpdf::SetFont('Arial', '', 9);
                        Fpdf::Cell(1, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                    }
                    
                    Fpdf::setXY(37, 77);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->nombre)), 0);
                    Fpdf::setXY(37, 80);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apePat)), 0);
                    Fpdf::setXY(37, 83);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apeMat)), 0);
                    
                    Fpdf::setXY(64, 78);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);

                    Fpdf::setXY(80, 70.5);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, utf8_decode($grupo[0]->creditos), 0);

                    Fpdf::setXY(84, 76.5);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($grupo[0]->lugar)), 0);
                

                    $contador = 106;
                    foreach ($horario as $c)        {
                        
                        Fpdf::SetFont('Arial', 'B', 9);
                        Fpdf::setXY($contador , 60);
                        Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                        
                        Fpdf::SetFont('Arial', '', 9);
                        Fpdf::setXY($contador, 55);
                        Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);
                        
                        Fpdf::setXY($contador, 53);
                        Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);
                        
                        $contador += 16;
                    }
                
                } else{
                    
                    Fpdf::Image("img/tec_nm.jpeg", 33, 150, 140, 17);   

                    Fpdf::setXY(10,166);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 

                    Fpdf::setXY(115,166);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

                    Fpdf::setXY(10, 172);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").utf8_decode($estudiantes[$i]->num_control), 0);

                    Fpdf::setXY(10, 178);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat." ".$estudiantes[$i]->apeMat), 0);

                    Fpdf::setXY(115, 172);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($estudiantes[$i]->semestre), 0);

                    Fpdf::setXY(10, 184);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($estudiantes[$i]->carrera), 0);

                    Fpdf::setXY(10, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(10, 13, 'ACTIV', 0);

                    Fpdf::setXY(37, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(10, 13, 'RESPON: ', 0);

                    Fpdf::setXY(64, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'GRUPO: ', 0);

                    Fpdf::setXY(80, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'C ', 0);

                    Fpdf::setXY(84, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'LUGAR: ', 0);

                    Fpdf::setXY(110, 204);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

                    Fpdf::SetFont('Arial', '', 9);

                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::setXY(20, 248);
                    Fpdf::Cell(5, 20, '___________________________________', 0);

                    Fpdf::setXY(23, 253);
                    Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                    Fpdf::setXY(23, 258);
                    Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

                    Fpdf::setXY(130, 248);
                    Fpdf::Cell(5, 20, '___________________________________', 0);

                    Fpdf::setXY(133, 253);
                    Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat), 0);

                    Fpdf::setXY(133, 258);
                    Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->apeMat), 0);

                    $a = strlen($grupo[0]->actividad);
                    $a = $a / 12;

                    for($j = 0; $j < $a; $j++){
                        $_activity = substr($grupo[0]->actividad, ($j*12), ($j+12));
                        Fpdf::setXY(10, 205 + ($j * 3));
                        Fpdf::SetFont('Arial', '', 9);
                        Fpdf::Cell(60, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                    }

                    Fpdf::setXY(37, 211);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->nombre)), 0);
                    Fpdf::setXY(37, 214);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apePat)), 0);
                    Fpdf::setXY(37, 217);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apeMat)), 0);

                    Fpdf::setXY(64, 212);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);
                    
                    Fpdf::setXY(80, 204.5);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, utf8_decode($grupo[0]->creditos), 0);

                    Fpdf::setXY(84, 210.5);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($grupo[0]->lugar)), 0);

                    $contador = 106;
                    foreach ($horario as $c)        {
                        
                        Fpdf::SetFont('Arial', 'B', 9);
                        Fpdf::setXY($contador , 193);
                        Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                        
                        Fpdf::SetFont('Arial', '', 9);
                        Fpdf::setXY($contador, 189);
                        Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);
                        
                        Fpdf::setXY($contador, 187);
                        Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);
                        
                        $contador += 16;
                    }
                    
                }
            }
        }

        for($i = 0; $i < count($other_students); $i++){
            $par = $i + count($estudiantes);

            $other_grupo = DB::select('SELECT g.clave, a.nombre AS actividad, a.creditos, 
                l.nombre AS lugar, p.nombre, p.apePat, p.apeMat, g.id_grupo
                FROM grupo AS g
            JOIN persona AS p ON g.id_persona = p.id_persona
            JOIN actividad AS a ON g.id_actividad = a.id_actividad
            JOIN lugar AS l ON g.id_lugar = l.id_lugar
                WHERE g.id_grupo = '.$other_students[$i]->id_grupo);

            $other_horario = DB::select('SELECT ds.nombre, h.hora_inicio, h.hora_fin,
                h.id_grupo
                FROM grupo AS g
                LEFT JOIN horario AS h ON g.id_grupo = h.id_grupo
                LEFT JOIN dias_semana AS ds ON h.id_dia = ds.id_dia
                    WHERE g.id_grupo = '.$other_students[$i]->id_grupo);

            if($impresos != null){
                for($j = 0; $j < count($impresos); $j++){
                    if($impresos[$j]->id_estudiante != $other_students[$i]->id_estudiante){

                        setlocale(LC_ALL,"es_MX.UTF-8");

                        if(($par % 2) == 0){
                            Fpdf::AddPage();
                            Fpdf::SetFont('Arial', '', 8);
                            Fpdf::SetMargins(30, 5 , 30);
                            Fpdf::SetAutoPageBreak(true);
                            Fpdf::Image("img/tec_nm.jpeg", 33, 17, 140, 17);   

                            Fpdf::setXY(10,33);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 

                            Fpdf::setXY(115,33);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

                            Fpdf::setXY(10, 39);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").$other_students[$i]->num_control, 0);

                            Fpdf::setXY(10, 45);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($other_students[$i]->nombre." ".$other_students[$i]->apePat." ".$other_students[$i]->apeMat), 0);

                            Fpdf::setXY(115, 39);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($other_students[$i]->semestre), 0);

                            Fpdf::setXY(10, 51);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($other_students[$i]->carrera), 0);

                            Fpdf::setXY(10, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(10, 13, 'ACTIV', 0);

                            Fpdf::setXY(37, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(10, 13, 'RESPON', 0);

                            Fpdf::setXY(64, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'GRUPO ', 0);

                            Fpdf::setXY(80, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'C ', 0);

                            Fpdf::setXY(84, 71);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'LUGAR ', 0);

                            Fpdf::setXY(115, 71);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

                            Fpdf::SetFont('Arial', '', 9);

                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::setXY(20, 115);
                            Fpdf::Cell(5, 20, '___________________________________', 0);

                            Fpdf::setXY(23, 120);
                            Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                            Fpdf::setXY(23, 125);
                            Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

                            Fpdf::setXY(130, 115);
                            Fpdf::Cell(5, 20, '___________________________________', 0);

                            Fpdf::setXY(133, 120);
                            Fpdf::Cell(5, 20, utf8_decode($other_students[$i]->nombre." ".$other_students[$i]->apePat), 0);

                            Fpdf::setXY(133, 125);
                            Fpdf::Cell(5, 20, utf8_decode($other_students[$i]->apeMat), 0);

                            //Primera actividad
                            $a = strlen($grupo[0]->actividad);
                            $a = $a / 12;

                            for($j = 0; $j < $a; $j++){
                                $_activity = substr($grupo[0]->actividad, ($j*12), ($j+12));
                                Fpdf::setXY(10, 70 + ($j * 3));
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::Cell(1, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                            }
                            
                            Fpdf::setXY(37, 76);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->nombre)), 0);
                            Fpdf::setXY(37, 79);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apePat)), 0);
                            Fpdf::setXY(37, 82);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apeMat)), 0);
                            
                            Fpdf::setXY(64, 77);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);

                            Fpdf::setXY(80, 69.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode($grupo[0]->creditos), 0);

                            Fpdf::setXY(84, 75.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($grupo[0]->lugar)), 0);

                            //Segunda actividad
                            $a = strlen($other_grupo[$i]->actividad);
                            $a = $a / 12;

                            for($j = 0; $j < $a; $j++){
                                $_activity = substr($other_grupo[$i]->actividad, ($j*12), ($j+12));
                                Fpdf::setXY(10, 85 + ($j * 3));
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::Cell(60, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                            }

                            Fpdf::setXY(37, 91);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($other_grupo[$i]->nombre)), 0);
                            Fpdf::setXY(37, 94);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($other_grupo[$i]->apePat)), 0);
                            Fpdf::setXY(37, 97);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($other_grupo[$i]->apeMat)), 0);

                            Fpdf::setXY(64, 92);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(50, 10, utf8_decode($other_grupo[$i]->clave), 0);
                            
                            Fpdf::setXY(80, 84.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode($other_grupo[$i]->creditos), 0);

                            Fpdf::setXY(84, 90.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($other_grupo[$i]->lugar)), 0);

                            $contador = 90; $cont2 = 16;
                            $gru = $horario[0]->id_grupo;
                            foreach ($horario as $c)        {
                                $contador += 16;
                                
                                // if($c->id_grupo == $gru){

                                    Fpdf::SetFont('Arial', 'B', 9);
                                    Fpdf::setXY($contador , 60);
                                    Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                                    
                                    Fpdf::SetFont('Arial', '', 9);
                                    Fpdf::setXY($contador, 54);
                                    Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                                    Fpdf::setXY($contador, 52);
                                    Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

                                // }else{
                                //     $contador = 90;

                                //     Fpdf::SetFont('Arial', 'B', 9);
                                //     Fpdf::setXY($contador + $cont2, 79);
                                //     Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                                    
                                //     Fpdf::SetFont('Arial', '', 9);
                                //     Fpdf::setXY($contador + $cont2, 73);
                                //     Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                                //     Fpdf::setXY($contador + $cont2, 71);
                                //     Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

                                //     $cont2 += 16;
                                // }
                            }

                            $contador = 90; $cont2 = 16;
                            $gru = $other_horario[0]->id_grupo;
                            foreach ($other_horario as $c)        {
                                $contador += 16;
                                
                                // if($c->id_grupo == $gru){

                                    Fpdf::SetFont('Arial', 'B', 9);
                                    Fpdf::setXY($contador , 79);
                                    Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                                    
                                    Fpdf::SetFont('Arial', '', 9);
                                    Fpdf::setXY($contador, 73);
                                    Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                                    Fpdf::setXY($contador, 71);
                                    Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

                                // }else{
                                //     $contador = 90;

                                //     Fpdf::SetFont('Arial', 'B', 9);
                                //     Fpdf::setXY($contador + $cont2, 79);
                                //     Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                                    
                                //     Fpdf::SetFont('Arial', '', 9);
                                //     Fpdf::setXY($contador + $cont2, 73);
                                //     Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                                //     Fpdf::setXY($contador + $cont2, 71);
                                //     Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

                                //     $cont2 += 16;
                                // }
                            }
                        
                        } else{
                            
                            Fpdf::Image("img/tec_nm.jpeg", 33, 150, 140, 17);   

                            Fpdf::setXY(10,166);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 

                            Fpdf::setXY(115,166);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

                            Fpdf::setXY(10, 172);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").utf8_decode($other_students[$i]->num_control), 0);

                            Fpdf::setXY(10, 178);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($other_students[$i]->nombre." ".$other_students[$i]->apePat." ".$other_students[$i]->apeMat), 0);

                            Fpdf::setXY(115, 172);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($other_students[$i]->semestre), 0);

                            Fpdf::setXY(10, 184);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($other_students[$i]->carrera), 0);

                            Fpdf::setXY(10, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(10, 13, 'ACTIV', 0);

                            Fpdf::setXY(37, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(10, 13, 'RESPON: ', 0);

                            Fpdf::setXY(64, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'GRUPO: ', 0);

                            Fpdf::setXY(80, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'C ', 0);

                            Fpdf::setXY(84, 204);
                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::Cell(60, 13, 'LUGAR: ', 0);

                            Fpdf::setXY(110, 204);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

                            Fpdf::SetFont('Arial', '', 9);

                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::setXY(20, 248);
                            Fpdf::Cell(5, 20, '___________________________________', 0);

                            Fpdf::setXY(23, 253);
                            Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                            Fpdf::setXY(23, 258);
                            Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

                            Fpdf::setXY(130, 248);
                            Fpdf::Cell(5, 20, '___________________________________', 0);

                            Fpdf::setXY(133, 253);
                            Fpdf::Cell(5, 20, utf8_decode($other_students[$i]->nombre." ".$other_students[$i]->apePat), 0);

                            Fpdf::setXY(133, 258);
                            Fpdf::Cell(5, 20, utf8_decode($other_students[$i]->apeMat), 0);

                            //Primer actividad
                            $a = strlen($grupo[0]->actividad);
                            $a = $a / 12;

                            for($j = 0; $j < $a; $j++){
                                $_activity = substr($grupo[0]->actividad, ($j*12), ($j+12));
                                Fpdf::setXY(10, 205 + ($j * 3));
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::Cell(60, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                            }

                            Fpdf::setXY(37, 211);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->nombre)), 0);
                            Fpdf::setXY(37, 214);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apePat)), 0);
                            Fpdf::setXY(37, 217);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apeMat)), 0);

                            Fpdf::setXY(64, 212);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);
                            
                            Fpdf::setXY(80, 204.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode($grupo[0]->creditos), 0);

                            Fpdf::setXY(84, 210.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($grupo[0]->lugar)), 0);

                            //Segunda actividad
                            $a = strlen($other_grupo[$i]->actividad);
                            $a = $a / 12;

                            for($j = 0; $j < $a; $j++){
                                $_activity = substr($other_grupo[$i]->actividad, ($j*12), ($j+12));
                                Fpdf::setXY(10, 222 + ($j * 3));
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::Cell(60, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                            }

                            Fpdf::setXY(37, 228);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($other_grupo[$i]->nombre)), 0);
                            Fpdf::setXY(37, 231);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($other_grupo[$i]->apePat)), 0);
                            Fpdf::setXY(37, 234);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($other_grupo[$i]->apeMat)), 0);

                            Fpdf::setXY(64, 229);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(50, 10, utf8_decode($other_grupo[$i]->clave), 0);
                            
                            Fpdf::setXY(80, 221.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 25, utf8_decode($other_grupo[$i]->creditos), 0);

                            Fpdf::setXY(84, 227.5);
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($other_grupo[$i]->lugar)), 0);

                            $contador = 106;
                            foreach ($horario as $c)        {
                                
                                Fpdf::SetFont('Arial', 'B', 9);
                                Fpdf::setXY($contador , 193);
                                Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                                
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::setXY($contador, 188);
                                Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);
                                
                                Fpdf::setXY($contador, 186);
                                Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);
                                
                                $contador += 16;
                            }

                            $contador = 106;
                            foreach ($other_horario as $c)        {
                                
                                Fpdf::SetFont('Arial', 'B', 9);
                                Fpdf::setXY($contador , 212);
                                Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                                
                                Fpdf::SetFont('Arial', '', 9);
                                Fpdf::setXY($contador, 207);
                                Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);
                                
                                Fpdf::setXY($contador, 205);
                                Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);
                                
                                $contador += 16;
                            }
                        }
                    } //Si es horario del estudiante ya fue impreso no se genera ese horario
                }    //Cierre del ciclo for para el recorrido de los horarios impresos
            //construcción de horarios si la tabla horarios_impresos está vacia
            }else{

                setlocale(LC_ALL,"es_MX.UTF-8");

                if(($i % 2) == 0){
                    Fpdf::AddPage();
                    Fpdf::SetFont('Arial', '', 8);
                    Fpdf::SetMargins(30, 5 , 30);
                    Fpdf::SetAutoPageBreak(true);
                    Fpdf::Image("img/tec_nm.jpeg", 33, 17, 140, 17);   

                    Fpdf::setXY(10,33);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0);

                    Fpdf::setXY(115,33);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

                    Fpdf::setXY(10, 39);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").$other_students[$i]->num_control, 0);

                    Fpdf::setXY(10, 45);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($other_students[$i]->nombre." ".$other_students[$i]->apePat." ".$other_students[$i]->apeMat), 0);

                    Fpdf::setXY(115, 39);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($other_students[$i]->semestre), 0);

                    Fpdf::setXY(10, 51);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($other_students[$i]->carrera), 0);

                    Fpdf::setXY(10, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(10, 13, 'ACTIV', 0);

                    Fpdf::setXY(37, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(10, 13, 'RESPON', 0);

                    Fpdf::setXY(64, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'GRUPO ', 0);

                    Fpdf::setXY(80, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'C ', 0);

                    Fpdf::setXY(84, 71);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'LUGAR ', 0);

                    Fpdf::setXY(115, 71);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

                    Fpdf::SetFont('Arial', '', 9);

                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::setXY(20, 115);
                    Fpdf::Cell(5, 20, '___________________________________', 0);

                    Fpdf::setXY(23, 120);
                    Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                    Fpdf::setXY(23, 125);
                    Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

                    Fpdf::setXY(130, 115);
                    Fpdf::Cell(5, 20, '___________________________________', 0);

                    Fpdf::setXY(133, 120);
                    Fpdf::Cell(5, 20, utf8_decode($other_students[$i]->nombre." ".$other_students[$i]->apePat), 0);

                    Fpdf::setXY(133, 125);
                    Fpdf::Cell(5, 20, utf8_decode($other_students[$i]->apeMat), 0);

                    $a = strlen($grupo[0]->actividad);
                    $a = $a / 12;

                    for($j = 0; $j < $a; $j++){
                        $_activity = substr($grupo[0]->actividad, ($j*12), ($j+12));
                        Fpdf::setXY(10, 70 + ($j * 3));
                        Fpdf::SetFont('Arial', '', 9);
                        Fpdf::Cell(1, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                    }
                    
                    Fpdf::setXY(37, 76);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->nombre)), 0);
                    Fpdf::setXY(37, 79);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apePat)), 0);
                    Fpdf::setXY(37, 82);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apeMat)), 0);
                    
                    Fpdf::setXY(64, 77);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);

                    Fpdf::setXY(80, 69.5);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, utf8_decode($grupo[0]->creditos), 0);

                    Fpdf::setXY(84, 75.5);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($grupo[0]->lugar)), 0);
                    

                    $contador = 90; $cont2 = 16;
                    $gru = $horario[0]->id_grupo;
                    foreach ($horario as $c)        {
                        $contador += 16;
                        
                        if($c->id_grupo == $gru){

                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::setXY($contador , 60);
                            Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                            
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::setXY($contador, 56);
                            Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                            Fpdf::setXY($contador, 54);
                            Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

                        }else{
                            $contador = 90;

                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::setXY($contador + $cont2, 79);
                            Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                            
                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::setXY($contador + $cont2, 73);
                            Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                            Fpdf::setXY($contador + $cont2, 71);
                            Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

                            $cont2 += 16;
                        }
                    }
                
                } else{
                    
                    Fpdf::Image("img/tec_nm.jpeg", 33, 150, 140, 17);   

                    Fpdf::setXY(10,166);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 

                    Fpdf::setXY(115,166);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

                    Fpdf::setXY(10, 172);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").utf8_decode($other_students[$i]->num_control), 0);

                    Fpdf::setXY(10, 178);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($other_students[$i]->nombre." ".$other_students[$i]->apePat." ".$other_students[$i]->apeMat), 0);

                    Fpdf::setXY(115, 172);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($other_students[$i]->semestre), 0);

                    Fpdf::setXY(10, 184);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($other_students[$i]->carrera), 0);

                    Fpdf::setXY(10, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(10, 13, 'ACTIV', 0);

                    Fpdf::setXY(37, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(10, 13, 'RESPON: ', 0);

                    Fpdf::setXY(64, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'GRUPO: ', 0);

                    Fpdf::setXY(80, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'C ', 0);

                    Fpdf::setXY(84, 204);
                    Fpdf::SetFont('Arial', 'B', 9);
                    Fpdf::Cell(60, 13, 'LUGAR: ', 0);

                    Fpdf::setXY(110, 204);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

                    Fpdf::SetFont('Arial', '', 9);

                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::setXY(20, 248);
                    Fpdf::Cell(5, 20, '___________________________________', 0);

                    Fpdf::setXY(23, 253);
                    Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                    Fpdf::setXY(23, 258);
                    Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

                    Fpdf::setXY(130, 248);
                    Fpdf::Cell(5, 20, '___________________________________', 0);

                    Fpdf::setXY(133, 253);
                    Fpdf::Cell(5, 20, utf8_decode($other_students[$i]->nombre." ".$other_students[$i]->apePat), 0);

                    Fpdf::setXY(133, 258);
                    Fpdf::Cell(5, 20, utf8_decode($other_students[$i]->apeMat), 0);

                    $a = strlen($grupo[0]->actividad);
                    $a = $a / 12;

                    for($j = 0; $j < $a; $j++){
                        $_activity = substr($grupo[0]->actividad, ($j*12), ($j+12));
                        Fpdf::setXY(10, 205 + ($j * 3));
                        Fpdf::SetFont('Arial', '', 9);
                        Fpdf::Cell(60, 25, utf8_decode(mb_strtoupper($_activity)), 0);
                    }

                    Fpdf::setXY(37, 211);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->nombre)), 0);
                    Fpdf::setXY(37, 214);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apePat)), 0);
                    Fpdf::setXY(37, 217);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($grupo[0]->apeMat)), 0);

                    Fpdf::setXY(64, 212);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);
                    
                    Fpdf::setXY(80, 205);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 25, utf8_decode($grupo[0]->creditos), 0);

                    Fpdf::setXY(84, 211);
                    Fpdf::SetFont('Arial', '', 9);
                    Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($grupo[0]->lugar)), 0);
                    

                    $contador = 90; $cont2 = 16;
                    $gru = $horario[0]->id_grupo;
                    foreach ($horario as $c)        {
                        $contador += 16;
                        
                        if($c->id_grupo == $gru){

                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::setXY($contador , 193);
                            Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);

                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::setXY($contador, 191);
                            Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                            Fpdf::setXY($contador, 189);
                            Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);
                            
                        }else{
                            $contador = 90;

                            Fpdf::SetFont('Arial', 'B', 9);
                            Fpdf::setXY($contador + $cont2, 215);
                            Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);

                            Fpdf::SetFont('Arial', '', 9);
                            Fpdf::setXY($contador + $cont2, 209);
                            Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                            Fpdf::setXY($contador + $cont2, 207);
                            Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

                            $cont2 += 16;
                        }
                    }
                }
            }
        }

        $tipo = 'I';
        $nombre_archivo = 'horario-'.$grupo[0]->nombre."-".$fecha_hoy.'.pdf';
    
        $headers = ['Content-Type' => 'application/pdf'];

        return response()->file(Fpdf::Output($tipo, $nombre_archivo));
    }

    public function f_horarioGrupos($dpt, $pagina){

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $grupost = DB::select('SELECT g.clave, a.nombre, g.id_grupo
        FROM inscripcion AS i
        JOIN grupo AS g ON i.id_grupo = g.id_grupo
        JOIN actividad AS a ON g.id_actividad = a.id_actividad
        JOIN departamento AS d ON a.id_depto = d.id_depto
        WHERE d.id_depto = '.$dpt.'
        AND i.fecha >= '.$roll->ini_inscripcion.'
        AND i.aprobada = 1
        GROUP BY g.id_grupo, g.clave, a.nombre');

        $grupos = DB::select('SELECT g.clave, a.nombre, g.id_grupo,
            COUNT(i.aprobada) as apro, COUNT(i.aprobada) as noapro, g.cupo
        FROM inscripcion AS i
        JOIN grupo AS g ON i.id_grupo = g.id_grupo
        JOIN actividad AS a ON g.id_actividad = a.id_actividad
        JOIN departamento AS d ON a.id_depto = d.id_depto
        WHERE d.id_depto = '.$dpt.'
        AND i.fecha >= '.$roll->ini_inscripcion.'
        AND i.aprobada = 1
        GROUP BY g.id_grupo, g.clave, a.nombre, g.cupo
        LIMIT '.(($pagina-1)*10).', 10');

        $inscripA = DB::select('SELECT g.id_grupo,
            COUNT(i.aprobada) as noapro
        FROM inscripcion AS i
        JOIN grupo AS g ON i.id_grupo = g.id_grupo
        JOIN actividad AS a ON g.id_actividad = a.id_actividad
        JOIN departamento AS d ON a.id_depto = d.id_depto
        WHERE d.id_depto = '.$dpt.'
        AND i.fecha >= '.$roll->ini_inscripcion.'
        AND i.aprobada = 0
        GROUP BY g.id_grupo');
        
        if($inscripA == null){
            foreach($grupos as $ti)
            $ti->noapro = 0;
        }else{
            for($i = 0; $i < count($grupos); $i++){

                for($j = 0; $j < count($inscripA); $j++){
                    if($grupos[$i]->id_grupo == $inscripA[$j]->id_grupo)
                        $grupos[$i]->noapro = $inscripA[$j]->noapro;
                    else
                        $grupos[$i]->noapro = 0;
                }
            }
        }

        $pag = 0;
            foreach($grupost as $g){
                $pag = $pag + 1;
            }
        $pag = ceil($pag / 10);

        $dptn = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_depto', $dpt)->first();
        
        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();

        return view('CoordAC.inscripciones.inscrip')
            ->with('mod', true)
            ->with('inscrip', $grupos)
            ->with('type', 4)
            ->with('dpts', $dpts)
            ->with('dptn', $dptn)
            ->with('pag', $pag)
            ->with('pa', $pagina)
            ->with('vista', 00)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_horario($id_ins){

        $periodo_ = Mperiodo::select('nombre', 'inicio')->where('estado', "Actual")->first();
        $id_std = Minscripcion::select('id_estudiante')->where('id_inscripcion', $id_ins)->first();

        $schedule = DB::select('SELECT ds.nombre, h.hora_inicio, 
            h.hora_fin, i.id_grupo
        FROM inscripcion AS i
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN horario AS h ON g.id_grupo = h.id_grupo
            LEFT JOIN dias_semana AS ds ON h.id_dia = ds.id_dia
        WHERE i.id_estudiante = '.$id_std->id_estudiante.'
        AND i.aprobada = 1');

        $student = DB::select('SELECT p.nombre, p.apePat, p.apeMat,
                e.semestre, e.num_control, c.nombre AS carrera
        FROM inscripcion AS i
        LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
        LEFT JOIN persona AS p ON e.id_persona = p.id_persona
        LEFT JOIN carrera AS c ON e.id_carrera = c.id_carrera
        WHERE i.id_inscripcion = '.$id_ins);

        $activity = DB::select('SELECT g.clave, a.nombre, a.creditos,
            p.nombre as nom_res, p.apePat, p.apeMat, l.nombre as lugar
        FROM inscripcion AS i
        JOIN grupo AS g ON i.id_grupo = g.id_grupo
        JOIN persona AS p ON g.id_persona = p.id_persona
        JOIN actividad AS a ON g.id_actividad = a.id_actividad
        JOIN lugar AS l ON g.id_lugar = l.id_lugar
        WHERE i.id_estudiante = '.$id_std->id_estudiante.'
        AND i.aprobada = 1');

        $all = DB::select('SELECT ds.nombre AS dia, h.hora_inicio, h.hora_fin,
        p.nombre, p.apePat, p.apeMat, e.semestre, c.nombre AS carrera,
        g.clave, a.nombre AS actividad, a.creditos
        FROM inscripcion AS i
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN horario AS h ON g.id_grupo = h.id_grupo
            LEFT JOIN dias_semana AS ds ON h.id_dia = ds.id_dia
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
            LEFT JOIN carrera AS c ON e.id_carrera = c.id_carrera
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
        WHERE i.id_inscripcion = '.$id_ins);

        // $usuario_actual = auth()->user();
        // $id_e = $usuario_actual->id;
        $periodo_ = Mperiodo::select('nombre', 'inicio')->where('estado', "Actual")->first();
        $fecha_hoy = date('d - m - Y'); $nctrl = mb_strtoupper("número control: ");

        setlocale(LC_ALL,"es_MX.UTF-8");

        Fpdf::AddPage();
        Fpdf::SetFont('Arial', '', 8);
        Fpdf::SetMargins(30, 5 , 30);
        Fpdf::SetAutoPageBreak(true);
        Fpdf::Image("img/tec_nm.jpeg", 33, 17, 140, 17);   

        Fpdf::setXY(10,33);
        Fpdf::SetFont('Arial', '', 9);
        Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 

        Fpdf::setXY(115,33);
        Fpdf::SetFont('Arial', '', 9);
        Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

        Fpdf::setXY(10, 39);
        Fpdf::SetFont('Arial', '', 9);
        Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").$student[0]->num_control, 0);

        Fpdf::setXY(10, 45);
        Fpdf::SetFont('Arial', '', 9);
        Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($student[0]->nombre." ".$student[0]->apePat." ".$student[0]->apeMat), 0);

        Fpdf::setXY(115, 39);
        Fpdf::SetFont('Arial', '', 9);
        Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($student[0]->semestre), 0);

        Fpdf::setXY(10, 51);
        Fpdf::SetFont('Arial', '', 9);
        Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($student[0]->carrera), 0);

        Fpdf::setXY(10, 71);
        Fpdf::SetFont('Arial', 'B', 9);
        Fpdf::Cell(10, 13, 'ACTIV', 0);

        Fpdf::setXY(37, 71);
        Fpdf::SetFont('Arial', 'B', 9);
        Fpdf::Cell(10, 13, 'RESPON', 0);

        Fpdf::setXY(64, 71);
        Fpdf::SetFont('Arial', 'B', 9);
        Fpdf::Cell(60, 13, 'GRUPO ', 0);

        Fpdf::setXY(80, 71);
        Fpdf::SetFont('Arial', 'B', 9);
        Fpdf::Cell(60, 13, 'C ', 0);
        
        Fpdf::setXY(84, 71);
        Fpdf::SetFont('Arial', 'B', 9);
        Fpdf::Cell(60, 13, 'LUGAR ', 0);

        Fpdf::setXY(115, 71);
        Fpdf::SetFont('Arial', '', 9);
        Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

        Fpdf::SetFont('Arial', '', 9);

        Fpdf::SetFont('Arial', '', 9);
        Fpdf::setXY(20, 115);
        Fpdf::Cell(5, 20, '___________________________________', 0);

        Fpdf::setXY(23, 120);
        Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

        Fpdf::setXY(23, 125);
        Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

        Fpdf::setXY(130, 115);
        Fpdf::Cell(5, 20, '___________________________________', 0);

        Fpdf::setXY(133, 120);
        Fpdf::Cell(5, 20, utf8_decode($student[0]->nombre." ".$student[0]->apePat), 0);

        Fpdf::setXY(133, 125);
        Fpdf::Cell(5, 20, utf8_decode($student[0]->apeMat), 0);

        //segunda parteeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee

        Fpdf::Image("img/tec_nm.jpeg", 33, 150, 140, 17);   

        Fpdf::setXY(10,166);
        Fpdf::SetFont('Arial', '', 9);
        Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 

        Fpdf::setXY(115,166);
        Fpdf::SetFont('Arial', '', 9);
        Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);

        Fpdf::setXY(10, 172);
        Fpdf::SetFont('Arial', '', 9);
        Fpdf::Cell(60, 25, utf8_decode("NÚMERO CONTROL: ").utf8_decode($student[0]->num_control), 0);

        Fpdf::setXY(10, 178);
        Fpdf::SetFont('Arial', '', 9);
        Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($student[0]->nombre." ".$student[0]->apePat." ".$student[0]->apeMat), 0);

        Fpdf::setXY(115, 172);
        Fpdf::SetFont('Arial', '', 9);
        Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($student[0]->semestre), 0);

        Fpdf::setXY(10, 184);
        Fpdf::SetFont('Arial', '', 9);
        Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($student[0]->carrera), 0);

        Fpdf::setXY(10, 204);
        Fpdf::SetFont('Arial', 'B', 9);
        Fpdf::Cell(10, 13, 'ACTIV', 0);

        Fpdf::setXY(37, 204);
        Fpdf::SetFont('Arial', 'B', 9);
        Fpdf::Cell(10, 13, 'RESPON: ', 0);

        Fpdf::setXY(64, 204);
        Fpdf::SetFont('Arial', 'B', 9);
        Fpdf::Cell(60, 13, 'GRUPO: ', 0);

        Fpdf::setXY(80, 204);
        Fpdf::SetFont('Arial', 'B', 9);
        Fpdf::Cell(60, 13, 'C ', 0);

        Fpdf::setXY(84, 204);
        Fpdf::SetFont('Arial', 'B', 9);
        Fpdf::Cell(60, 13, 'LUGAR: ', 0);

        Fpdf::setXY(110, 204);
        Fpdf::SetFont('Arial', '', 9);
        Fpdf::Cell(1, 2, 'Horario de Actividad', 0);

        Fpdf::SetFont('Arial', '', 9);

        Fpdf::SetFont('Arial', '', 9);
        Fpdf::setXY(20, 248);
        Fpdf::Cell(5, 20, '___________________________________', 0);

        Fpdf::setXY(23, 253);
        Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

        Fpdf::setXY(23, 258);
        Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

        Fpdf::setXY(130, 248);
        Fpdf::Cell(5, 20, '___________________________________', 0);

        Fpdf::setXY(133, 253);
        Fpdf::Cell(5, 20, utf8_decode($student[0]->nombre." ".$student[0]->apePat), 0);

        Fpdf::setXY(133, 258);
        Fpdf::Cell(5, 20, utf8_decode($student[0]->apeMat), 0);

        for($i = 0; $i < count($activity); $i++){

            $a = strlen($activity[$i]->nombre);
            $a = $a / 12;

            for($j = 0; $j < $a; $j++){
                $_activity = substr($activity[$i]->nombre, ($j*12), ($j+12));
                Fpdf::setXY(10, 70 + ($j * 3) + ($i * 15));
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(1, 25, utf8_decode(mb_strtoupper($_activity)), 0);
            }
                
            Fpdf::setXY(37, 76 + ($i * 15));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($activity[$i]->nom_res)), 0);
            Fpdf::setXY(37, 79 + ($i * 15));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($activity[$i]->apePat)), 0);
            Fpdf::setXY(37, 82 + ($i * 15));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($activity[$i]->apeMat)), 0);
            
            Fpdf::setXY(64, 77 + ($i * 15));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(50, 10, utf8_decode($activity[$i]->clave), 0);

            Fpdf::setXY(80, 69 + ($i * 15));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(60, 25, utf8_decode($activity[$i]->creditos), 0);

            Fpdf::setXY(84, 76 + ($i * 15));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($activity[$i]->lugar)), 0);


            $a = strlen($activity[$i]->nombre);
            $a = $a / 12;

            for($j = 0; $j < $a; $j++){
                $_activity = substr($activity[$i]->nombre, ($j*12), ($j+12));
                Fpdf::setXY(10, 205 + ($j*3) + ($i * 16));
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 25, utf8_decode(mb_strtoupper($_activity)), 0);
            }

            Fpdf::setXY(37, 211 + ($i * 16));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($activity[$i]->nom_res)), 0);
            Fpdf::setXY(37, 214 + ($i * 16));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($activity[$i]->apePat)), 0);
            Fpdf::setXY(37, 217 + ($i * 16));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(10, 13, utf8_decode(mb_strtoupper($activity[$i]->apeMat)), 0);

            Fpdf::setXY(64, 212 + ($i * 16));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(50, 10, utf8_decode($activity[$i]->clave), 0);
            
            Fpdf::setXY(80, 205 + ($i * 16));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(60, 25, utf8_decode($activity[$i]->creditos), 0);

            Fpdf::setXY(84, 211 + ($i * 16));
            Fpdf::SetFont('Arial', '', 9);
            Fpdf::Cell(60, 13, utf8_decode(mb_strtoupper($activity[$i]->lugar)), 0);
        }


        $contador = 90; $cont2 = 16;
        $gru = $schedule[0]->id_grupo;
        foreach ($schedule as $c)        {
            $contador += 16;
            
            if($c->id_grupo == $gru){

                Fpdf::SetFont('Arial', 'B', 9);
                Fpdf::setXY($contador , 60);
                Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::setXY($contador, 55);
                Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                Fpdf::setXY($contador, 53);
                Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);



                Fpdf::SetFont('Arial', 'B', 9);
                Fpdf::setXY($contador , 193);
                Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);

                Fpdf::SetFont('Arial', '', 9);
                Fpdf::setXY($contador, 188);
                Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                Fpdf::setXY($contador, 186);
                Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);
            }else{
                $contador = 90;

                Fpdf::SetFont('Arial', 'B', 9);
                Fpdf::setXY($contador + $cont2, 79);
                Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);
                
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::setXY($contador + $cont2, 73);
                Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                Fpdf::setXY($contador + $cont2, 71);
                Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

                Fpdf::SetFont('Arial', 'B', 9);
                Fpdf::setXY($contador + $cont2, 215);
                Fpdf::Cell(1, 35, utf8_decode(mb_strtoupper($c->nombre)), 0);

                Fpdf::SetFont('Arial', '', 9);
                Fpdf::setXY($contador + $cont2, 209);
                Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);

                Fpdf::setXY($contador + $cont2, 207);
                Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

                $cont2 += 16;
            }
        } 
        
        $tipo = 'I';
        $headers = ['Content-Type' => 'application/pdf'];

        return Response::make(Fpdf::Output($tipo), 200, $headers);
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

        return view('CoordAC.perfil')
            ->with('persona', $persona)
            ->with('departamentos', $deptos)
            ->with('puestos', $puesto)
            ->with('grados', $grados)
            ->with('tipos', $this->tipos());
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

        return view('CoordAC.editperfil')
            ->with('persona', $persona)
            ->with('departamentos', $deptos)
            ->with('puestos', $puesto)
            ->with('grados', $grados)
            ->with('tipos', $this->tipos());
    }

    public function f_editar(Request $request){

        $usuario = $request->user()->id_persona;
        $grado = $request->grado;
        $nombre = mb_strtoupper($request->nombre);
        $ape1 = mb_strtoupper($request->apePat);
        $ape2 = mb_strtoupper($request->apeMat);
        $curp = mb_strtoupper($request->curp);

        Mpersona::where('id_persona', $usuario)
            ->update(['nombre' => $nombre,
            'apePat' => $ape1, 'apeMat' => $ape2,
            'curp' => $curp]);

        Mempleado::where('id_persona', $usuario)
            ->update(['id_grado' => $grado]);

        return redirect()->to('/CoordAC/datosGen');
    }

    public function f_passwd(){

        return view('CoordAC.editpasswd')
            ->with('tipos', $this->tipos());
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
                    location.href = "/CoordAC/editpasswd";
                 </script>
              <?php
           }
  
        }else{
           ?>
              <script>
                  alert('Contraseña actual incorrecta, intenta de nuevo.');
                  location.href = "/CoordAC/editpasswd";
              </script>
          <?php
        }
    }
}
