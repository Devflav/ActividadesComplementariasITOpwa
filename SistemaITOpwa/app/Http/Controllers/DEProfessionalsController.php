<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\Mtipo;
use App\Models\Mgrupo;
use App\Models\Mgrado;
use App\Models\Mlugar;
use App\Models\Musers;
use App\Models\Mpuesto;
use App\Models\Mhorario;
use App\Models\Mcarrera;
use App\Models\Mperiodo;
use App\Models\Mpersona;
use App\Models\Mempleado;
use App\Models\Mactividad;
use App\Models\Mestudiante;
use App\Models\Minscripcion;
use App\Models\Mdepartamento;
use App\Models\Mfechas_inhabiles;
use App\Models\Mcriterios_evaluacion;
use DB;     use Mail;       use URL;

class DEProfessionalsController extends Controller
{
    public function _construct() { 
        $this->middleware('auth');
      }

    public function tipos(){

        $tipos = DB::select('SELECT id_tipo, nombre
            FROM tipo');

        foreach($tipos as $t){
            $t->nombre = ucwords(mb_strtolower($t->nombre));
        }
        
        return $tipos;
     }

    public function f_inicio(Request $request) { 

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

        return view('DivEProf.inicio')
        ->with('hora', $now)
        ->with('process', $processes)
        ->with('end', $endprocess)
        ->with('tipos', $this->tipos());
    }

    public function f_eliminaciones($origin, $objeto){
        
        $url = "/delete/".$origin."/".$objeto;

        return view('DivEProf.mimodal',
        ['nombre' => strtoupper($origin),
         'miurl' => $url,
         'modal' => false,
         'tipos' => $this->tipos()]);
    }

    public function f_ediciones($origin, $objeto){
        
        $url = "/update/".$origin."/".$objeto;

        return view('DivEProf.mimodal',
        ['nombre' => strtoupper($origin),
         'miurl' => $url,
         'modal' => true,
         'tipos' => $this->tipos()]);
    }

/*----------------------------------------------------------------------------------------------------*/

    
public function f_actividades($pagina) { 

    $now = date('Y-m-d');
    $modificar = true;

    $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
        ->where('estado', "Actual")->first();
    if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
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

    return view('DivEProf.actividad.actividades')
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

    $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
        ->where('estado', "Actual")->first();
    if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
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

        return view('DivEProf.actividad.actividades')
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
        //return redirect()->to('DivEProf/actividad/'.$search.'/1');
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

        return view('DivEProf.actividad.actdepto')
        ->with('deptos', $depto)
        ->with('pag', $pag)
        ->with('pa', 1)
        ->with('tipos', $this->tipos()); 
    }

    public function f_actdepto($id_dep, $pagina) { 

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
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

        return view('DivEProf.actividad.actividades')
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

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
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

        return view('DivEProf.actividad.actividades')
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

        return view('DivEProf.actividad.nueva')
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

        return redirect()->to('DivEProf/actividades/1');
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

        return view('DivEProf.actividad.editar')
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

        return redirect()->to('DivEProf/actividades/1');
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

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

            $gruposP = DB::select('SELECT g.id_grupo, g.cupo, g.clave,
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

            $grupos = DB::select('SELECT g.id_grupo, g.cupo, g.clave,
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

        return view('DivEProf.grupo.grupos')
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

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $gruposP = DB::select('SELECT g.id_grupo, g.cupo, g.clave,
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

            $grupos = DB::select('SELECT g.id_grupo, g.cupo, g.clave,
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

        return view('DivEProf.grupo.grupos')
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
        return $this->f_gruposB($search, 1);  
        //return redirect()->to('DivEProf/grupos/'.$search.'/1');
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

        return view('DivEProf.grupo.nuevo')
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

        return view('DivEProf.grupo.nuevo')
        ->with('periodos', $periodo)
        ->with('actividades', $actividad)
        ->with('personas', $persona)
        ->with('lugares', $lugar)
        ->with('deptos', $deptos)
        ->with('tipos', $this->tipos());
    }

    public function f_regGrupo(Request $request){

        $clave = mb_strtoupper($request->clave);
        //$periodo = $request->periodo;
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
                                ->where('estado', "Actual")->get();
        foreach($periodo AS $p){
            $grupo = Mgrupo::create(['id_periodo' => $p->id_periodo, 
                'id_actividad' => $actividad, 'id_persona' => $responsable, 
                'id_lugar' => $lugar, 'clave' => $clave, 'cupo' => $cupo, 
                'cupo_libre' => $cupo, 'orden' => $orden, 'estado' => 1]);
        } 

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

        return redirect()->to('DivEProf/grupos/1');
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

        return view('DivEProf.grupo.editar')->with('grupo', $grupo)
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
        //return $horario;

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
        

        return redirect()->to('DivEProf/grupos/1');
    }

    public function f_deletegru($id_delete){

        Mgrupo::where('id_grupo', $id_delete)
            ->update(['estado' => 0]);

        return redirect()->to('DivEProf/grupos/1');
    }
        
        
/*----------------------------------------------------------------------------------------------------*/

    public function f_estudiantes($pagina) { 
        
        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

            $estudiantesP = DB::select('SELECT e.id_estudiante, e.num_control AS ncontrol,
                p.nombre, p.apePat, p.apeMat, c.nombre AS carrera, 
                e.semestre, p.curp, e.id_persona, p.estado
            FROM persona AS p
            LEFT JOIN estudiante AS e ON p.id_persona = e.id_persona
            LEFT JOIN carrera AS c ON e.id_carrera = c.id_carrera
            WHERE p.estado IN(SELECT estado FROM persona WHERE estado = 1)
            AND p.tipo = "Estudiante"');

            $estudiantes = DB::select('SELECT e.id_estudiante, e.num_control AS ncontrol,
                p.nombre, p.apePat, p.apeMat, c.nombre AS carrera, 
                e.semestre, p.curp, e.id_persona, p.estado
            FROM persona AS p
            LEFT JOIN estudiante AS e ON p.id_persona = e.id_persona
            LEFT JOIN carrera AS c ON e.id_carrera = c.id_carrera
            WHERE p.tipo = "Estudiante"
            AND p.estado IN(SELECT estado FROM persona WHERE estado = 1)
            LIMIT '.(($pagina-1)*10).', 10');

            $pag = 0;
            foreach($estudiantesP as $g){
                $pag = $pag + 1;
            }
            $pag = ceil($pag / 10);

        return view('DivEProf.estudiante.estudiantes')
        ->with('estudiantes', $estudiantes)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 00)
        ->with('mod', true)
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
                e.semestre, p.curp, e.id_persona, p.estado
            FROM persona AS p
            LEFT JOIN estudiante AS e ON p.id_persona = e.id_persona
            LEFT JOIN carrera AS c ON e.id_carrera = c.id_carrera
            WHERE p.tipo = "Estudiante"
            AND p.estado IN(SELECT estado FROM persona WHERE estado = 1)
            AND e.num_control LIKE "%'.$search.'%" 
           
            OR c.nombre LIKE "%'.$search.'%"');
            // OR p.nombre LIKE "%'.$search.'%"
            // OR p.apePat LIKE "%'.$search.'%"
            $estudiantes = DB::select('SELECT e.id_estudiante, e.num_control AS ncontrol,
                p.nombre, p.apePat, p.apeMat, c.nombre AS carrera, 
                e.semestre, p.curp, e.id_persona, p.estado
            FROM persona AS p
            LEFT JOIN estudiante AS e ON p.id_persona = e.id_persona
            LEFT JOIN carrera AS c ON e.id_carrera = c.id_carrera
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

        return view('DivEProf.estudiante.estudiantes')
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
        return $this->f_estudiantesB($search, 1);   
        //return redirect()->to('DivEProf/estudiantes/'.$search.'/1');
    }
            
    public function f_n_estudiante() { 

        $carreras = Mcarrera::select('id_carrera', 'nombre')
            ->where('estado', 1)->get();

        $semestres = ['1', '2', '3', '4', '5', '6', '7', '8', 
        '9', '10','11', '12'];

            return view('DivEProf.estudiante.nuevo')
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
                location.href = "/DivEProf/nuevoEst";
            </script><?php
        }else{
            $persona = Mpersona::create(['nombre' => $nombre, 'apePat' => $apePat,
            'apeMat' => $apeMat, 'curp' => $curp, 'tipo' => "Estudiante", 'estado' => 1]);

            Mestudiante::create(['id_persona' => $persona->id, 'id_carrera' => $carrera, 
            'num_control' => $nControl, 'email' => $email, 'semestre' => $semestre]);

            Musers::create(['id_persona' => $persona->id, 'id_puesto' => 6,
            'nombre' => $nomUser, 'usuario' => $email, 'password' => $contraseña,
            'fecha_registro' => $hoy, 'edo_sesion' => 0, 'estado' => 1]);

            return redirect()->to('/DivEProf/estudiantes/1');
        }
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
        
        
        return view('DivEProf.reportes')
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

        return view('DivEProf.carrera.carreras')
        ->with('carreras', $carreras)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchcar(Request $request) { 

        $search = mb_strtoupper($request->buscar);
        return $this->f_carreras($search);
        //return redirect()->to('DivEProf/carreras/'.$search);
    }

    public function f_n_carrera() { 

        $deptos = Mdepartamento::where('estado', 1)->get();

        return view('DivEProf.carrera.nueva')
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

        return redirect()->to('DivEProf/carreras/0');
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
        
        return view('DivEProf.critEval.c_evaluacion')
        ->with('criterios', $critEval)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchcrit(Request $request) { 

        $search = mb_strtoupper($request->buscar);
        return $this->f_critEva($search);
        //return redirect()->to('DivEProf/critEvaluacion/'.$search);
    }

    public function f_n_critEva() { 
        return view('DivEProf.critEval.nuevo')
        ->with('tipos', $this->tipos());   
    }

    public function f_regCritE(Request $request){

        $nombre = mb_strtoupper($request->nomCritE);
        $descrip = mb_strtoupper($request->desCritE);

        Mcriterios_evaluacion::create(['nombre' => $nombre,
        'descripcion' => $descrip, 'estado' => 1]);

        return redirect()->to('DivEProf/critEvaluacion/0');
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

        return view('DivEProf.depto.departamentos')
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

        return view('DivEProf.depto.departamentos')
        ->with('departamentos', $departamentos)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 01)
        ->with('bus', $search)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchdpt(Request $request) { 

        $search = mb_strtoupper($request->search);
        return $this->f_departamento($search, 1);
        //return redirect()->to('DivEProf/departamentos/'.$search.'/1');
    }

    public function f_n_depto() { 

        $jefes = DB::select('SELECT p.id_persona, g.nombre AS grado, 
            p.nombre, p.apePat, p.apeMat
            FROM persona AS p
            LEFT JOIN empleado AS e ON p.id_persona = e.id_persona
            LEFT JOIN grado AS g ON e.id_grado = g.id_grado
            WHERE e.id_puesto = 2
            AND p.estado = 1');
        
        return view('DivEProf.depto.nuevo')
        ->with('jefes', $jefes)
        ->with('tipos', $this->tipos());   
    }

    public function f_regDepto(Request $request){

        $nombre = mb_strtoupper($request->nomDepto);
        $jefe = $request->persona;

        Mdepartamento::create(['id_persona' => $jefe, 'nombre' => $nombre, 'estado' => 1]);

        return redirect()->to('DivEProf/departamentos/1');
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

        return view('DivEProf.grado.grados')
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

    return view('DivEProf.grado.grados')
    ->with('grados', $grados)
    ->with('pag', $pag)
    ->with('pa', $pagina)
    ->with('vista', 01)
    ->with('bus', $search)
    ->with('tipos', $this->tipos());   
}

    public function f_searchgra(Request $request) { 

        $search = mb_strtoupper($request->search);
        return $this->f_grado($search, 1); 
        //return redirect()->to('DivEProf/grados/'.$search.'/1');
    }

    public function f_n_grado() { 

        return view('DivEProf.grado.nuevo')
        ->with('tipos', $this->tipos());   
    }

    public function f_regGrado(Request $request){

        $nombre = mb_strtoupper($request->nomGrado);
        $sig = mb_strtoupper($request->significado);

        Mgrado::create(['nombre' => $nombre, 
        'significado' => $sig, 'estado' => 1]);

        return redirect()->to('DivEProf/grados/1');
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

        return view('DivEProf.periodo.periodos')
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

        return view('DivEProf.periodo.periodos')
            ->with('periodos', $periodos)
            ->with('pag', $pag)
            ->with('pa', $pagina)
            ->with('vista', 01)
            ->with('bus', $search)
            ->with('tipos', $this->tipos());   
    }

    public function f_searchperi(Request $request) { 

        $search = mb_strtoupper($request->search);
        return $this->f_periodo($search, 1); 
        //return redirect()->to('DivEProf/periodos/'.$search.'/1');
    }

    public function f_n_periodo(){

        $mes = array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO",
                    "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");

        $año = [date("Y"), date("Y")+1, date("Y")+2];
        /**<input type="text" class="form-control" name="nombre" 
            pattern="[ENE]{1}[A-Z]+[-][JUN]{1}[A-Z]+[0-9]{4}|[AGO]{1}[A-Z]+[-][DIC]{1}[A-Z]+[0-9]{4}|[SEP]{1}[A-Z]+[-][ENE]{1}[A-Z]+[0-9]{4}" 
            placeholder="MESINICIO-MESFIN/AÑO ó MESINICIO AÑO-MESFIN AÑO" required> */
        return view('DivEProf.periodo.nuevo')
            ->with('mes', $mes)
            ->with('año', $año)
            ->with('tipos', $this->tipos());
    }

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
        $gob = ''; $tecnm = ''; $ito = ''; $encabezado = '';

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
                   location.href = "DivEProf/nuevoPeri";
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

                    return redirect()->to('DivEProf/periodos/1');
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
                            
                            return redirect()->to('DivEProf/periodos/1');

                        }else{

                            ?> <script>
                                alert('Las fechas de los procesos de Inscripción, Evaluación y G. Constancias no pueden traslaparse.');
                                location.href = "DivEProf/nuevoPeri";
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

                            return redirect()->to('DivEProf/periodos/1');

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

                            return redirect()->to('DivEProf/periodos/1');

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

                            return redirect()->to('DivEProf/periodos/1');

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

                            return redirect()->to('DivEProf/periodos/1');

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

                            return redirect()->to('DivEProf/periodos/1');

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

                        return redirect()->to('DivEProf/periodos/1');

                    }else{

                        ?> <script>
                            alert('Los procesos de Inscripción, Evaluación y G. Constancias deben ser mínimo de 3 días y máximo 2 semanas.');
                            location.href = "DivEProf/nuevoPeri";
                        </script> <?php
                    } 
                }
            }
        }else{
            ?> <script>
                   alert('El término del semestre no puede ser anterior al inicio.');
                   location.href = "DivEProf/nuevoPeri";
                </script> <?php
        }

    }

    public function f_det_periodo($id_peri){

        $periodo = Mperiodo::where('id_periodo', $id_peri)->get();

        return view('DivEProf.periodo.detalle')
        ->with('periodo', $periodo)
        ->with('tipos', $this->tipos());
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


        return view('DivEProf.persona.personas')
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

        return view('DivEProf.persona.personas')
        ->with('personas', $personas)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 01)
        ->with('bus', $search)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchpers(Request $request) { 

        $search = mb_strtoupper($request->search);
        return $this->f_personalB($search, 1); 
        //return redirect()->to('DivEProf/personal/'.$search.'/1');
    }

    public function f_n_persona(){

        $deptos = Mdepartamento::get();
        $puesto = Mpuesto::where('estado', 1)->get();
        $grados = Mgrado::get();

        return view('DivEProf.persona.nueva')
        ->with('departamentos', $deptos)
        ->with('puestos', $puesto)
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
                location.href = "/DivEProf/nuevaPer";
            </script><?php
        }else{
            $persona = Mpersona::create(['nombre' => $nombre, 'apePat' => $apePat,
            'apeMat' => $apeMat, 'curp' => $curp, 'tipo' => "Empleado", 'estado' => 1]);

            Mempleado::create(['id_persona' => $persona->id, 'id_depto' => $depto, 
            'id_grado' => $grado, 'id_puesto' => $puesto]);

            Musers::create(['id_persona' => $persona->id, 'id_puesto' => $puesto,
            'nombre' => $nomUser, 'usuario' => $curp, 'password' => $contraseña,
            'fecha_registro' => $hoy, 'edo_sesion' => 0, 'estado' => 1]);

            return redirect()->to('DivEProf/personal/1');
        }
    }

    public function f_e_persona($id_per){

        $deptos = Mdepartamento::get();
        $puesto = Mpuesto::get();
        $grados = Mgrado::get();

        $persona = DB::table('persona AS p')
        ->join('empleado AS e', 'p.id_persona', '=', 'e.id_persona')
        ->join('departamento AS d', 'e.id_depto', '=', 'd.id_depto')
        ->join('grado AS g', 'e.id_grado', '=', 'g.id_grado')
        ->join('puesto AS pu', 'e.id_puesto', '=', 'pu.id_puesto')
        ->select('p.id_persona AS id_persona', 'p.nombre AS nombre', 
                'p.apePat AS paterno', 'p.apeMat AS materno',
                'p.curp AS curp', 'd.nombre AS depto', 'g.nombre AS grado',
                'pu.nombre AS puesto', 'e.id_depto AS id_depto',
                'e.id_grado AS id_grado', 'e.id_puesto AS id_puesto')
        ->where('p.id_persona', $id_per)
                ->get();

        return view('DivEProf.persona.editar')
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


        return redirect()->to('DivEProf/personal/1');
    }

    public function f_deleteper($id_delete){

        Mpersona::where('id_persona', $id_delete)
            ->update(['estado' => 0]);

        return redirect()->to('DivEProf/personal/1');
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

        return view('DivEProf.puesto.puestos')
        ->with('puestos', $puestos)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchpue(Request $request) { 

        $search = mb_strtoupper($request->search);
        //return $this->f_puestos($search); 
        return redirect()->to('DivEProf/puestos/'.$search);
    }

/*----------------------------------------------------------------------------------------------------*/

    
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
        
        return view('DivEProf.suspencion.sus_labores')
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
        
        return view('DivEProf.suspencion.sus_labores')
        ->with('fechas', $fechas)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('bus', $search)
        ->with('vista', 01)
        ->with('tipos', $this->tipos()); 
    }

    public function f_searchslab(Request $request) { 

        $search = mb_strtoupper($request->search);
        return $this->f_s_labor($search, 1); 
        //return redirect()->to('DivEProf/suspLabores/'.$search.'/1');
    }

    public function f_n_fecha(){

        return view('DivEProf.suspencion.nueva')
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
        
            return redirect()->to('DivEProf/suspLabores/1');
        }
        elseif($fecha > $fechfin) {
            ?>
                <script>
                    alert("La fecha de término no puede ser menor que la fecha de inicio.");
                    location.href = "DivEProf/nuevaFecha";
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
                    return redirect()->to('DivEProf/suspLabores/1');
                }
            }
        }

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

        return view('DivEProf.lugares.lugares')
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

        return view('DivEProf.lugares.lugares')
        ->with('lugares', $lugares)
        ->with('pag', $pag)
        ->with('pa', $pagina)
        ->with('vista', 00)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchlug(Request $request) { 

        $search = mb_strtoupper($request->search);
        return $this->f_lugar($search, 1);   
        //return redirect()->to('DivEProf/lugares/'.$search.'/1');
    }

    public function f_n_lugar(){

        return view('DivEProf.lugares.nuevo')
        ->with('tipos', $this->tipos());
    }

    public function f_regLugar(Request $request){

        $nombre = mb_strtoupper($request->nomLugar);

        Mlugar::create(['nombre' => $nombre, 'estado' => 1]);

        return redirect()->to('DivEProf/lugares/1');
    }

/************************************************************************************************** */

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

        return view('DivEProf.perfil')
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

        return view('DivEProf.editperfil')
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

        return redirect()->to('/DivEProf/datosGen');
    }

    public function f_passwd(){

        return view('DivEProf.editpasswd')
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
                    location.href = "/DivEProf/editpasswd";
                 </script>
              <?php
           }
  
        }else{
           ?>
              <script>
                  alert('Contraseña actual incorrecta, intenta de nuevo.');
                  location.href = "/DivEProf/editpasswd";
              </script>
          <?php
        }
    }
 
    public function logoutDEP(Request $request){

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect("IniciarSesion");
    }
}
