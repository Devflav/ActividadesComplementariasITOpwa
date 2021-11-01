<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
        $this->middleware('divisionep');
      }

    public function logs($action, $object, $user){

        Log::info($action, ['Object' => $object, 'User:' => $user]);
    }

    public function tipos(){

        $tipos = Mtipo::select('id_tipo', 'nombre')->get();

        foreach($tipos as $t){
            $t->nombre = ucwords(mb_strtolower($t->nombre));
        }
        
        return $tipos;
    }

    public function procesoActual(){

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

        return $procesos;
    }

    public function f_inicio(Request $request) { 

        $now = date_create('America/Mexico_City')->format('H');

        $procesos = $this->procesoActual();

        return view('DivEProf.inicio')
        ->with('hora', $now)
        ->with('process', $procesos[0])
        ->with('end', $procesos[1])
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

        $roll = Mperiodo::where('estado', "Actual")->first();

        if($now < $roll->inicio || $now > $roll->fin_inscripcion)
            $modificar = false;
        
        $actividades = DB::table('actividad as a')
            ->leftJoin('departamento as d', 'a.id_depto', '=', 'd.id_depto')
            ->leftJoin('tipo as t', 'a.id_tipo', '=', 't.id_tipo')
            ->select('a.id_actividad', 
                    'a.clave', 
                    'a.nombre', 
                    'a.creditos', 
                    'd.nombre AS depto', 
                    't.nombre AS tipo', 
                    'a.descripcion')
            ->where('a.estado', 1)
            ->orderBy('a.id_actividad')
            ->paginate(10);


        return view('DivEProf.actividad.actividades')
        ->with('actividades', $actividades)
        ->with('periodo', $roll->nombre)
        ->with('mod', true)
        ->with('tipos', $this->tipos()); 
    }

    public function f_actividad($search, $pagina) { 

        $now = date('Y-m-d');
        $modificar = true;
        $search = "%".mb_strtoupper($search)."%";

        $roll = Mperiodo::where('estado', "Actual")->first();
    
        $periodo = $roll->nombre;

        if($now < $roll->inicio || $now > $roll->fin_inscripcion)
            $modificar = false;
            
        $actividades = DB::table('actividad as a')
            ->leftJoin('departamento as d', 'a.id_depto', '=', 'd.id_depto')
            ->leftJoin('tipo as t', 'a.id_tipo', '=', 't.id_tipo')
            ->select('a.id_actividad', 
                    'a.clave', 
                    'a.nombre', 
                    'a.creditos', 
                    'd.nombre AS depto', 
                    't.nombre AS tipo', 
                    'a.descripcion')
            ->when($search, function ($query, $search) {
                return $query->where('a.clave', 'LIKE', $search)
                            ->orWhere('a.nombre', 'LIKE', $search)
                            ->where('a.estado', 1);
            })
            ->orderBy('a.id_actividad')
            ->paginate(10);

            return view('DivEProf.actividad.actividades')
            ->with('actividades', $actividades)
            ->with('periodo', $periodo)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_searchact(Request $request) { 

        $search = $request->search;
        return redirect()->to('/DivEProf/actividad/'.$search.'/1');
        // return $this->f_actividad($search, 1);   
    }

    public function f_depto() { 

        $depto = DB::table('departamento AS d')
            ->select('d.id_depto', 'd.nombre')
            ->where('d.estado', 1)
            ->orderBy('d.id_depto')
            ->paginate(10);

        return view('DivEProf.actividad.actdepto')
            ->with('deptos', $depto)
            ->with('tipos', $this->tipos()); 
    }

    public function f_actdepto($id_dep, $pagina) { 

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('inicio', 'fin_inscripcion')
            ->where('estado', "Actual")->first();

        if($now < $roll->inicio || $now > $roll->fin_inscripcion)
            $modificar = false;

        $depto = Mdepartamento::select('nombre')
            ->where('id_depto', $id_dep)
            ->first();
        
        $actividades = DB::table('actividad as a')
            ->leftJoin('departamento as d', 'a.id_depto', '=', 'd.id_depto')
            ->leftJoin('tipo as t', 'a.id_tipo', '=', 't.id_tipo')
            ->select('a.id_actividad', 
                    'a.clave', 
                    'a.nombre', 
                    'a.creditos', 
                    'd.nombre AS depto', 
                    't.nombre AS tipo', 
                    'a.descripcion')
            ->when($id_dep, function ($query, $id_dep) {
                return $query
                        ->where('a.estado', 1)
                        ->where('d.id_depto', $id_dep);
            })
            ->orderBy('a.id_actividad')
            ->paginate(10);

        return view('DivEProf.actividad.actividades')
        ->with('actividades', $actividades)
        ->with('periodo', $depto->nombre)
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

        $tipo = Mtipo::select('nombre')
               ->where('id_tipo', $id_tip)
               ->first();
        
        
        $actividades = DB::table('actividad as a')
            ->leftJoin('departamento as d', 'a.id_depto', '=', 'd.id_depto')
            ->leftJoin('tipo as t', 'a.id_tipo', '=', 't.id_tipo')
            ->select('a.id_actividad', 
                    'a.clave', 
                    'a.nombre', 
                    'a.creditos', 
                    'd.nombre AS depto', 
                    't.nombre AS tipo', 
                    'a.descripcion')
            ->when($id_tip, function ($query, $id_tip) {
                return $query
                        ->where('a.estado', 1)
                        ->where('t.id_tipo', $id_tip);
            })
            ->orderBy('a.id_actividad')
            ->paginate(10);

        return view('DivEProf.actividad.actividades')
        ->with('actividades', $actividades)
        ->with('periodo', $tipo->nombre)
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

        $data = $request->all();
        
        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.',
            'unique' => 'El campo :attribute ya se ha registrado.',
            'email' => 'El dominio valido para el e-mail es: @itoaxaca.edu.mx'
        ];
      
        $validation = \Validator::make($request->all(), [
            'id_depto' => 'required|exists:departamento,id_depto',
            'id_tipo' => 'required|exists:tipo,id_tipo',
            'clave' => 'required|size:5',
            'nombre' => 'required|min:3|max:100',
            'creditos' => 'required|numeric',
            'descripcion' => 'nullable|max:250',
            'restringida' => 'required|numeric'
        ], $messages);      
          
        if ($validation->fails())  {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $periodo = Mperiodo::select('id_periodo')
            ->where('estado', "Actual")
                ->first();

        Mactividad::create([
                'id_depto' => $data['id_depto'], 
                'id_tipo' => $data['id_tipo'],
                'id_periodo' => $periodo->id_periodo, 
                'clave' => mb_strtoupper($data['clave']), 
                'nombre' => mb_strtoupper($data['nombre']),
                'creditos' => $data['creditos'], 
                'descripcion' => mb_strtoupper($data['descripcion']), 
                'restringida' => $data['restringida'], 
                'estado' => 1]);

        return redirect()->to('/DivEProf/actividades/1');
    }

    public function f_e_actividad($id_act) { 

        $depto = Mdepartamento::get();
        $tipos = Mtipo::get();

        $actividad = DB::table('actividad AS a')
            ->leftJoin('departamento AS d', 'a.id_depto', '=', 'd.id_depto')
            ->leftJoin('tipo AS t', 'a.id_tipo', '=', 't.id_tipo')
            ->select('a.id_actividad', 
                    'a.clave',
                    'a.nombre',
                    'a.creditos',
                    'a.id_tipo',
                    'd.nombre as depto',
                    't.nombre as tipo',
                    'a.descripcion',
                    'a.id_depto',
                    'a.restringida')
            ->where('a.id_actividad', $id_act)
            ->get();

        return view('DivEProf.actividad.editar')
        ->with('actividad', $actividad)
        ->with('deptos', $depto)
        ->with('tipos', $tipos)
        ->with('tipos', $this->tipos());
    }

    public function f_editAct($id_act, Request $request){

        $data = $request->all();
        
        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.',
            'unique' => 'El campo :attribute ya se ha registrado.'
        ];

        $clave = Mactividad::select('clave')
            ->where('id_actividad', $id_act)
            ->first();

        if( $clave->clave == mb_strtoupper($data['clave']) ) {

            $validation = \Validator::make($request->all(), [
                'id_depto' => 'required|exists:departamento,id_depto',
                'id_tipo' => 'required|exists:tipo,id_tipo',
                'nombre' => 'required|min:3|max:100',
                'descripcion' => 'nullable|max:250',
                'restringida' => 'required|numeric'
            ], $messages);  
        } else {

            $validation = \Validator::make($request->all(), [
                'id_depto' => 'required|exists:departamento,id_depto',
                'id_tipo' => 'required|exists:tipo,id_tipo',
                'clave' => 'required|unique:actividad|size:5',
                'nombre' => 'required|min:3|max:100',
                'descripcion' => 'nullable|max:250',
                'restringida' => 'required|numeric'
            ], $messages);   
        }   
          
        if ($validation->fails())  {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        Mactividad::where('id_actividad', $id_act)
            ->update([
                'id_depto' => $data['id_depto'], 
                'id_tipo' => $data['id_tipo'],
                'clave' => mb_strtoupper($data['clave']), 
                'nombre' => mb_strtoupper($data['nombre']),
                'descripcion' => mb_strtoupper($data['descripcion']),
                'restringida' => $data['restringida']
            ]);

        return redirect()->to('/DivEProf/actividades/1');
    }

    public function f_deleteact($id_delete){

        Mactividad::where('id_actividad', $id_delete)
            ->update(['estado' => 0]);

        return  redirect()->to('/DivEProf/actividades/1');
    }

/*----------------------------------------------------------------------------------------------------*/

    public function f_grupos($pagina) {
        $now = date('Y-m-d');
        $modificar;

        $periodo = Mperiodo::select('id_periodo', 'nombre', 'inicio', 'fin_inscripcion')
            ->where('estado', "Actual")
            ->first();
        
        ($now < $periodo->inicio || $now > $periodo->fin_inscripcion)
            ? $modificar = false : $modificar = true;

        $grupos = DB::table('grupo AS g')
            ->join('periodo AS p', 'g.id_periodo', '=', 'p.id_periodo')
            ->join('actividad AS a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('persona AS pe', 'g.id_persona', '=', 'pe.id_persona')
            ->join('lugar AS l', 'g.id_lugar', '=', 'l.id_lugar')
            ->join('departamento as d', 'a.id_depto', '=', 'd.id_depto')
            ->select('g.id_grupo', 
                    'g.cupo_libre', 
                    'g.clave', 
                    'g.asistencias', 
                    'a.nombre AS actividad', 
                    'l.nombre AS lugar',
                    'd.id_depto',
                    DB::raw('CONCAT(pe.nombre, " ", pe.apePat, " ", pe.apeMat) AS responsable'))
            ->where('p.estado', "Actual")
            ->where('g.estado', 1)
            ->orderBy('g.id_grupo')
            ->paginate(10);

        return view('DivEProf.grupo.grupos')
        ->with('grupos', $grupos)
        ->with('periodo', $periodo->nombre)
        // ->with('dept', 1)
        ->with('mod', true)
        ->with('tipos', $this->tipos());   
    }

    public function f_gruposB($search, $pagina) {

        $now = date('Y-m-d');
        $modificar;
        $search = mb_strtoupper("%".utf8_decode($search)."%");

        $periodo = Mperiodo::select('id_periodo', 'nombre', 'inicio', 'fin_inscripcion')
            ->where('estado', "Actual")
            ->first();
        
        ($now < $periodo->inicio || $now > $periodo->fin_inscripcion)
            ? $modificar = false : $modificar = true;

        $grupos = DB::table('grupo AS g')
            ->leftJoin('periodo AS p', 'g.id_periodo', '=', 'p.id_periodo')
            ->leftJoin('actividad AS a', 'g.id_actividad', '=', 'a.id_actividad')
            ->leftJoin('persona AS pe', 'g.id_persona', '=', 'pe.id_persona')
            ->leftJoin('lugar AS l', 'g.id_lugar', '=', 'l.id_lugar')
            ->join('departamento as d', 'a.id_depto', '=', 'd.id_depto')
            ->select('g.id_grupo', 
                    'g.cupo_libre', 
                    'g.clave', 
                    'g.asistencias', 
                    'a.nombre AS actividad', 
                    'l.nombre AS lugar',
                    'd.id_depto',
                    DB::raw('CONCAT(pe.nombre, " ", pe.apePat, " ", pe.apeMat) AS responsable'))
            ->where('p.estado', "Actual")
            ->where('g.estado', 1)
            ->where('g.clave', 'LIKE', $search)
            ->orWhere('a.nombre', 'LIKE', $search)
            // ->orWhere('pe.nombre', 'LIKE', $search)
            // ->orWhere('pe.apePat', 'LIKE', $search)
            ->orderBy('g.id_grupo')
            ->paginate(10);

        return view('DivEProf.grupo.grupos')
        ->with('grupos', $grupos)
        ->with('periodo', $periodo->nombre)
        ->with('dept', 1)
        ->with('mod', true)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchgru(Request $request) { 

        $search = $request->search;
        // return $this->f_gruposB($search, 1);  
        return redirect()->to('/DivEProf/grupos/'.$search.'/1');
    }

    public function f_n_grupo(Request $request, $id_dep){

        $periodo = Mperiodo::select('id_periodo', 'nombre')
            ->where('estado', "Actual")
            ->get();

        $actividad = DB::table('actividad as a')
            ->join('departamento AS d', 'a.id_depto', '=', 'd.id_depto')
            ->select('a.id_actividad',
                    'a.clave',
                    'a.nombre',
                    'a.creditos')
            ->where('a.estado', 1)
            ->where('a.id_depto', $id_dep)
            ->get();

        $persona = DB::table('persona AS p')
            ->join('empleado AS e', 'p.id_persona', '=', 'e.id_persona')
            ->join('grado AS g', 'e.id_grado', '=', 'g.id_grado')
            ->select('p.id_persona', 
                    'g.nombre AS grado', 
                    'p.nombre', 
                    'p.apePat', 
                    'p.apeMat')
            ->where('e.id_depto', $id_dep)
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

        $data = $request->all();
        
        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.',
            'starts_with' => 'El campo :attribute no cumple con el formato.',
            'size' => 'El campo :attribute no cumple con el formato.',
            'unique' => 'El campo :attribute ya se ha registrado.'
        ];
      
        $validation = \Validator::make($request->all(), [
            'id_actividad' => 'required|exists:actividad,id_actividad',
            'id_persona' => 'required|exists:persona,id_persona',
            'id_lugar' => 'required|exists:lugar,id_lugar',
            'clave' => 'required|unique:grupo|starts_with:G,g|size:7',
            'cupo' => 'required|integer:4',
            'orden' => 'required|numeric'
        ], $messages);      
          
        if ($validation->fails())  {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $periodo = Mperiodo::select('id_periodo')
                                ->where('estado', "Actual")->first();

        $grupo = Mgrupo::create([
                    'id_periodo' => $periodo->id_periodo, 
                    'id_actividad' => $data['id_actividad'], 
                    'id_persona' => $data['id_persona'], 
                    'id_lugar' => $data['id_lugar'], 
                    'clave' => mb_strtoupper($data['clave']), 
                    'cupo' => $data['cupo'], 
                    'cupo_libre' => $data['cupo'], 
                    'orden' => $data['orden'], 
                    'estado' => 1]
                );

        if($data['lunes'] != null && $data['lunesf'] != null){
            Mhorario::create(['id_grupo' => $grupo->id, 
                'id_dia' => 1, 'hora_inicio' => $data['lunes'],
                'hora_fin' => $data['lunesf']]);
        }

        if($data['martes'] != null && $data['martesf'] != null){
            Mhorario::create(['id_grupo' => $grupo->id, 
                'id_dia' => 2, 'hora_inicio' => $data['martes'],
                'hora_fin' => $data['martesf']]);
        }

        if($data['miercoles'] != null && $data['miercolesf'] != null){
            Mhorario::create(['id_grupo' => $grupo->id, 
                'id_dia' => 3, 'hora_inicio' => $data['miercoles'],
                'hora_fin' => $data['miercolesf']]);
        }

        if($data['jueves'] != null && $data['juevesf'] != null){
            Mhorario::create(['id_grupo' => $grupo->id, 
                'id_dia' => 4, 'hora_inicio' => $data['jueves'],
                'hora_fin' => $data['juevesf']]);
        }

        if($data['viernes'] != null && $data['viernesf'] != null){
            Mhorario::create(['id_grupo' => $grupo->id, 
                'id_dia' => 5, 'hora_inicio' => $data['viernes'],
                'hora_fin' => $data['viernesf']]);
        }

        if($data['sabado'] != null && $data['sabadof'] != null){
            Mhorario::create(['id_grupo' => $grupo->id, 
                'id_dia' => 6, 'hora_inicio' => $data['sabado'],
                'hora_fin' => $data['sabadof']]);
        }

        return redirect()->to('/DivEProf/grupos/1');
    }
    

    public function f_e_grupo($id_gru, $dpt){

        $periodo = Mperiodo::select('id_periodo', 'nombre')
            ->where('estado', "Actual")
            ->get();

        $actividad = DB::table('actividad as a')
            ->join('departamento AS d', 'a.id_depto', '=', 'd.id_depto')
            ->select('a.id_actividad',
                    'a.clave',
                    'a.nombre',
                    'a.creditos')
            ->where('a.estado', 1)
            ->where('a.id_depto', $dpt)
            ->get();

        $persona = DB::table('persona AS p')
            ->join('empleado AS e', 'p.id_persona', '=', 'e.id_persona')
            ->join('grado AS g', 'e.id_grado', '=', 'g.id_grado')
            ->select('p.id_persona', 
                    'g.nombre AS grado', 
                    'p.nombre', 
                    'p.apePat', 
                    'p.apeMat')
            ->where('e.id_depto', $dpt)
            ->get();

        $grupo = DB::table('grupo as g')
            ->join('periodo as p', 'g.id_periodo', '=', 'p.id_periodo')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('persona as pe', 'g.id_persona', '=', 'pe.id_persona')
            ->join('lugar as l', 'g.id_lugar', '=', 'l.id_lugar')
            ->join('empleado as e', 'pe.id_persona', '=', 'e.id_persona')
            ->join('grado as gr', 'e.id_grado', '=', 'gr.id_grado')
            ->select('g.id_grupo',
                    'g.cupo',
                    'g.clave',
                    'p.nombre as periodo',
                    'a.nombre as actividad',
                    'a.clave as aClave',
                    'a.creditos',
                    'pe.nombre as nomP',
                    'pe.apePat as paterno',
                    'pe.apeMat as materno',
                    'l.nombre as lugar',
                    'g.orden',
                    'gr.nombre as grado',
                    'g.id_actividad',
                    'g.id_persona',
                    'g.id_lugar')
            ->where('g.id_grupo', $id_gru)
            ->get();

        $deptos = Mdepartamento::get();
        $lugar = Mlugar::get();

        $h1 = DB::table('grupo as g')
            ->leftJoin('horario as h', 'g.id_grupo', '=', 'h.id_grupo')
            ->leftJoin('dias_semana as d', 'h.id_dia', '=', 'd.id_dia')
            ->select('h.hora_inicio',
                    'h.hora_fin')
            ->where('d.id_dia', 1)
            ->where('h.estado', 1)
            ->where('g.id_grupo', $id_gru)
            ->get();
        if(count($h1) == 0) 
            $h1 = false;

        $h2 = DB::table('grupo as g')
            ->leftJoin('horario as h', 'g.id_grupo', '=', 'h.id_grupo')
            ->leftJoin('dias_semana as d', 'h.id_dia', '=', 'd.id_dia')
            ->select('h.hora_inicio',
                    'h.hora_fin')
            ->where('d.id_dia', 2)
            ->where('h.estado', 1)
            ->where('g.id_grupo', $id_gru)
            ->get();
        if(count($h2) == 0) 
            $h2 = false;

        $h3 = DB::table('grupo as g')
            ->leftJoin('horario as h', 'g.id_grupo', '=', 'h.id_grupo')
            ->leftJoin('dias_semana as d', 'h.id_dia', '=', 'd.id_dia')
            ->select('h.hora_inicio',
                    'h.hora_fin')
            ->where('d.id_dia', 3)
            ->where('h.estado', 1)
            ->where('g.id_grupo', $id_gru)
            ->get();
        if(count($h3) == 0) 
            $h3 = false;

        $h4 = DB::table('grupo as g')
            ->leftJoin('horario as h', 'g.id_grupo', '=', 'h.id_grupo')
            ->leftJoin('dias_semana as d', 'h.id_dia', '=', 'd.id_dia')
            ->select('h.hora_inicio',
                    'h.hora_fin')
            ->where('d.id_dia', 4)
            ->where('h.estado', 1)
            ->where('g.id_grupo', $id_gru)
            ->get();
        if(count($h4) == 0) 
            $h4 = false;

        $h5 = DB::table('grupo as g')
            ->leftJoin('horario as h', 'g.id_grupo', '=', 'h.id_grupo')
            ->leftJoin('dias_semana as d', 'h.id_dia', '=', 'd.id_dia')
            ->select('h.hora_inicio',
                    'h.hora_fin')
            ->where('d.id_dia', 5)
            ->where('h.estado', 1)
            ->where('g.id_grupo', $id_gru)
            ->get();
        if(count($h5) == 0) 
            $h5 = false;

        $h6 = DB::table('grupo as g')
            ->leftJoin('horario as h', 'g.id_grupo', '=', 'h.id_grupo')
            ->leftJoin('dias_semana as d', 'h.id_dia', '=', 'd.id_dia')
            ->select('h.hora_inicio',
                    'h.hora_fin')
            ->where('d.id_dia', 6)
            ->where('h.estado', 1)
            ->where('g.id_grupo', $id_gru)
            ->get();
        if(count($h6) == 0) 
            $h6 = false;

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
        
        $data = $request->all();
        
        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.',
            'starts_with' => 'El campo :attribute no cumple con el formato.',
            'size' => 'El campo :attribute no cumple con el formato.',
            'unique' => 'El campo :attribute ya se ha registrado.'
        ];
      
        $validation = \Validator::make($request->all(), [
            'id_actividad' => 'required|exists:actividad,id_actividad',
            'id_persona' => 'required|exists:persona,id_persona',
            'id_lugar' => 'required|exists:lugar,id_lugar',
            'clave' => 'required|starts_with:G,g|size:7',
            'cupo' => 'required|integer:4',
            'orden' => 'required|numeric'
        ], $messages);      
          
        if ($validation->fails())  {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $old_data = Mgrupo::select('clave', 'cupo', 'cupo_libre')->where('id_grupo', $id_gru)->first();

        // foreach($old_data as $c){
            
            if($old_data->cupo == $old_data->cupo_libre){
                Mgrupo::where('id_grupo', $id_gru)
                    ->update([
                        'id_actividad' => $data['id_actividad'], 
                        'id_persona' => $data['id_persona'], 
                        'id_lugar' => $data['id_lugar'], 
                        'cupo' => $data['cupo'], 
                        'cupo_libre' => $data['cupo'], 
                        'orden' => $data['orden']
                    ]);
            }else{
                $new_cupo_libre = $data['cupo'] - ($old_data->cupo - $old_data->cupo_libre);

                Mgrupo::where('id_grupo', $id_gru)
                    ->update([
                        'id_actividad' => $data['id_actividad'], 
                        'id_persona' => $data['id_persona'], 
                        'id_lugar' => $data['id_lugar'], 
                        'cupo' => $data['cupo'], 
                        'cupo_libre' => $new_cupo_libre, 
                        'orden' => $data['orden']
                    ]);
            }

            if($old_data->clave != $data['clave']){

                Mgrupo::where('id_grupo', $id_gru)
                    ->update([
                        'clave' => mb_strtoupper($data['clave'])
                    ]);
            }
        // }
        
        $haylun = 0; $haymar = 0; $haymie = 0; $hayjue = 0; $hayvie = 0; $haysab = 0;

        $horario = Mhorario::where('id_grupo', $id_gru)->where('estado', 1)->get();

            foreach($horario as $h){

                if($h->id_dia == 1){
                    if($data['lunes'] != null && $data['lunesf'] != null){
                        Mhorario::where('id_grupo', $id_gru)
                            ->where('id_dia', 1)
                            ->update(['hora_inicio' => $data['lunes'],
                            'hora_fin' => $data['lunesf']]);
                    }else{
                        Mhorario::where('id_grupo', $id_gru)
                            ->where('id_dia', 1)
                            ->update(['estado' => 0]);
                    }
                    $haylun = 1;

                }elseif($h->id_dia == 2){
                    if($data['martes'] != null && $data['martesf'] != null){
                        Mhorario::where('id_grupo', $id_gru)
                            ->where('id_dia', 2)
                            ->update(['hora_inicio' => $data['martes'],
                                'hora_fin' => $data['martesf']]);
                    }else{
                        Mhorario::where('id_grupo', $id_gru)
                            ->where('id_dia', 2)
                            ->update(['estado' => 0]);
                    }
                    $haymar = 1;

                }elseif($h->id_dia == 3){
                    if($data['miercoles'] != null && $data['miercolesf'] != null){
                        Mhorario::where('id_grupo', $id_gru)
                            ->where('id_dia', 3)
                            ->update(['hora_inicio' => $data['miercoles'],
                                'hora_fin' => $data['miercolesf']]);
                    }else{
                        Mhorario::where('id_grupo', $id_gru)
                            ->where('id_dia', 3)
                            ->update(['estado' => 0]);
                    }
                    $haymie = 1;

                }elseif($h->id_dia == 4){
                    if($data['jueves'] != null && $data['juevesf'] != null){
                        Mhorario::where('id_grupo', $id_gru)
                            ->where('id_dia', 4)
                            ->update(['hora_inicio' => $data['jueves'],
                                'hora_fin' => $data['juevesf']]);
                    }else{
                        Mhorario::where('id_grupo', $id_gru)
                            ->where('id_dia', 4)
                            ->update(['estado' => 0]);
                    }
                    $hayjue = 1;

                }elseif($h->id_dia == 5){
                    if($data['viernes'] != null && $data['viernesf'] != null){
                        Mhorario::where('id_grupo', $id_gru)
                            ->where('id_dia', 5)
                            ->update(['hora_inicio' => $data['viernes'],
                                'hora_fin' => $data['viernesf']]);
                    }else{
                        Mhorario::where('id_grupo', $id_gru)
                            ->where('id_dia', 5)
                            ->update(['estado' => 0]);
                    }
                    $hayvie = 1;

                }elseif($h->id_dia == 6){
                    if($data['sabado'] != null && $data['sabadof'] != null){
                        Mhorario::where('id_grupo', $id_gru)
                            ->where('id_dia', 6)
                            ->update(['hora_inicio' => $data['sabado'],
                                'hora_fin' => $data['sabadof']]);
                    }else{
                        Mhorario::where('id_grupo', $id_gru)
                            ->where('id_dia', 6)
                            ->update(['estado' => 0]);
                    }
                    $haysab = 1;
                }
            }

        
            if($haylun == 0){
                if($data['lunes'] != null && $data['lunesf'] != null){
                    Mhorario::create(['id_grupo' => $id_gru, 
                        'id_dia' => 1, 'hora_inicio' => $data['lunes'],
                        'hora_fin' => $data['lunesf']]);
                }

            }

            if($haymar == 0){
                if($data['martes'] != null && $data['martesf'] != null){
                    Mhorario::create(['id_grupo' => $id_gru, 
                        'id_dia' => 2, 'hora_inicio' => $data['martes'],
                        'hora_fin' => $data['martesf']]);
                }
            }
            
            if($haymie == 0){
                if($data['miercoles'] != null && $data['miercolesf'] != null){
                    Mhorario::create(['id_grupo' => $id_gru, 
                        'id_dia' => 3, 'hora_inicio' => $data['miercoles'],
                        'hora_fin' => $data['miercolesf']]);
                }
            }
            
            if($hayjue == 0){
                if($data['jueves'] != null && $data['juevesf'] != null){
                    Mhorario::create(['id_grupo' => $id_gru, 
                        'id_dia' => 4, 'hora_inicio' => $data['jueves'],
                        'hora_fin' => $data['juevesf']]);
                }
            }
            
            if($hayvie == 0){
                if($data['viernes'] != null && $data['viernesf'] != null){
                    Mhorario::create(['id_grupo' => $id_gru, 
                        'id_dia' => 5, 'hora_inicio' => $data['viernes'],
                        'hora_fin' => $data['viernesf']]);
                }
            }
            
            if($haysab == 0){
                if($data['sabado'] != null && $data['sabadof'] != null){
                    Mhorario::create(['id_grupo' => $id_gru, 
                        'id_dia' => 6, 'hora_inicio' => $data['sabado'],
                        'hora_fin' => $data['sabadof']]);
                }
            }

        return redirect()->to('/DivEProf/grupos/1');
    }

    public function f_deletegru($id_delete){

        Mgrupo::where('id_grupo', $id_delete)
            ->update(['estado' => 0]);

        return redirect()->to('/DivEProf/grupos/1');
    }
        
        
/*----------------------------------------------------------------------------------------------------*/

    public function f_estudiantes($pagina) { 
        
        $now = date('Y-m-d');
        $modificar = true;
        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion', 'ini_evaluacion')
            ->where('estado', "Actual")->first();

        if($now < $roll->ini_inscripcion || $now >= $roll->ini_evaluacion)
            $modificar = false;

        $estudiantes = DB::table('persona AS p')
            ->join('estudiante AS e', 'p.id_persona', '=', 'e.id_persona')
            ->join('carrera AS c', 'e.id_carrera', '=', 'c.id_carrera')
            ->join('departamento as d', 'c.id_depto', '=', 'd.id_depto')
            ->select('e.id_estudiante', 
                    'e.num_control', 
                    'c.nombre AS carrera', 
                    'e.semestre', 
                    'p.estado', 
                    'e.id_persona', 
                    'd.id_depto',
                    DB::raw('CONCAT(p.nombre, " ", p.apePat, " ", p.apeMat) AS estudiante'))
            ->where('p.estado', 1)
            ->where('p.tipo', "Estudiante")
            ->orderBy('e.semestre')
            ->paginate(10);

        return view('DivEProf.estudiante.estudiantes')
        ->with('estudiantes', $estudiantes)
        ->with('mod', true)
        ->with('tipos', $this->tipos());   
    }

    public function f_estudiantesB($search, $pagina) { 
        
        $now = date('Y-m-d');
        $modificar = true;
        $search = mb_strtoupper("%".utf8_decode($search)."%");
        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion', 'ini_evaluacion')
        ->where('estado', "Actual")->first();
        
        if($now < $roll->ini_inscripcion || $now >= $roll->ini_evaluacion)
            $modificar = false;

        $estudiantes = DB::table('persona AS p')
            ->join('estudiante AS e', 'p.id_persona', '=', 'e.id_persona')
            ->join('carrera AS c', 'e.id_carrera', '=', 'c.id_carrera')
            ->join('departamento as d', 'c.id_depto', '=', 'd.id_depto')
            ->select('e.id_estudiante', 
                    'e.num_control', 
                    'c.nombre AS carrera', 
                    'e.semestre', 
                    'p.estado', 
                    'e.id_persona', 
                    'd.id_depto',
                    DB::raw('CONCAT(p.nombre, " ", p.apePat, " ", p.apeMat) AS estudiante'))
            ->when($search, function ($query, $search) {
                return $query->where('p.tipo', "Estudiante")
                            ->where('e.num_control', 'LIKE', $search)
                            ->orWhere('p.nombre', 'LIKE', $search)
                            ->orWhere('p.apePat', 'LIKE', $search)
                            ->orWhere('p.apeMat', 'LIKE', $search)
                            ->orWhere('c.nombre', 'LIKE', $search)
                            ->where('p.estado', 1);
            })
            ->orderBy('e.semestre')
            ->paginate(10);

        return view('DivEProf.estudiante.estudiantes')
        ->with('estudiantes', $estudiantes)
        ->with('mod', true)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchest(Request $request) { 

        $search = $request->search;
        // return $this->f_estudiantesB($search, 1);   
        return redirect()->to('/DivEProf/estudiantes/'.$search.'/1');
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

        $data = $request->all();

        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.',
            'unique' => 'El campo :attribute ya se ha registrado.',
            'email' => 'El dominio valido para el e-mail es: @itoaxaca.edu.mx',
            'ends_with' => 'El dominio valido para el e-mail es: @itoaxaca.edu.mx'
        ];
        
        $validation = \Validator::make($request->all(), [
            'num_control' => 'required|unique:estudiante|min:8|max:9',
            'nombre' => 'required|min:3|max:30',
            'apePat' => 'required|min:3|max:20',
            'apeMat' => 'required|min:3|max:20',
            'id_carrera' => 'required|exists:carrera,id_carrera',
            'email' => 'required|email|unique:estudiante|ends_with:@itoaxaca.edu.mx|min:24|max:25',
            'curp' => 'nullable|unique:persona|size:18'
        ], $messages);      

        if ($validation->fails())  {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $hoy = date("Y-m-d");
        $nomUser = mb_strtoupper($data['nombre'].' '.$data['apePat'].' '.$data['apeMat']);
        $contraseña = bcrypt($data['num_control']);

        $persona = Mpersona::create([
                    'nombre' => mb_strtoupper($data['nombre']), 
                    'apePat' => mb_strtoupper($data['apePat']),
                    'apeMat' => mb_strtoupper($data['apeMat']), 
                    'curp' => mb_strtoupper($data['curp']), 
                    'tipo' => "Estudiante", 
                    'estado' => 1]);

        Mestudiante::create([
            'id_persona' => $persona->id, 
            'id_carrera' => $data['id_carrera'], 
            'num_control' => $data['num_control'], 
            'email' => $data['email'], 
            'semestre' => $data['semestre']]);

        Musers::create([
            'id_persona' => $persona->id, 
            'id_puesto' => 6,
            'nombre' => $nomUser, 
            'usuario' => $data['email'], 
            'password' => $contraseña,
            'fecha_registro' => $hoy, 
            'edo_sesion' => 0, 
            'estado' => 1]);

            return redirect()->to('/DivEProf/estudiantes/1');
        
    }

/*----------------------------------------------------------------------------------------------------*/
    
    public function f_reportes(Request $request) { 

        $periodoI = $request->input('periodo');
        $actividadI = $request->input('actividad');

        $periodo = Mperiodo::select('id_periodo', 'nombre')
            ->where('estado', '<>', 'Espera')
            ->where('estado', '<>', 'Siguiente')
            ->get();

        $actividad = DB::table('actividad AS a')
            ->select('a.nombre', 'a.id_actividad')
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
                ->select('e.id_desempenio', 
                        'p.nombre as periodo', 
                        'a.nombre as actividad')
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

        $search = mb_strtoupper("%".utf8_decode($search)."%");

        if($search == "%0%"){
            $carreras = DB::table('carrera as c')
                ->join('departamento as d', 'c.id_depto', '=', 'd.id_depto')
                ->select('c.id_carrera',
                        'c.nombre',
                        'd.nombre AS depto')
                ->where('c.estado', 1)
                ->orderBy('c.id_carrera')
                ->paginate(10); 
        }else{
            $carreras = DB::table('carrera as c')
            ->join('departamento as d', 'c.id_depto', '=', 'd.id_depto')
            ->select('c.id_carrera',
                    'c.nombre',
                    'd.nombre AS depto')
            ->where('c.nombre', 'LIKE', $search)
            ->where('c.estado', 1)
            ->orderBy('c.id_carrera')
            ->paginate(10); 
        }

        return view('DivEProf.carrera.carreras')
        ->with('carreras', $carreras)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchcar(Request $request) { 

        $search = $request->search;
        // return $this->f_carreras($search);
        return redirect()->to('/DivEProf/carreras/'.$search);
    }

    public function f_n_carrera() { 

        $deptos = Mdepartamento::where('estado', 1)->get();

        return view('DivEProf.carrera.nueva')
        ->with('deptos', $deptos)
        ->with('tipos', $this->tipos());   
    }

    public function f_regCar(Request $request){

        $data = $request->all();

        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.',
            'exists' => 'El departamento no existe.'
        ];

        $validation = \Validator::make($request->all(), [
                'nombre' => 'required|min:3|max:87',
                'id_depto' => 'required|exists:departamento,id_depto',
                'tipo' => 'required|boolean',
            ], $messages);
        
        if ($validation->fails())  {
                return redirect()->back()->withInput()->withErrors($validation->errors());
            }

        if($data['tipo'] == 0)
            $data['tipo'] = 'INGENIERÍA ';
        else 
            $data['tipo'] = 'LICENCIATURA ';
        
        Mcarrera::create(['id_depto' => $data['id_depto'], 
                'nombre' => mb_strtoupper($data['tipo'].$data['nombre']), 
                'estado' => 1
            ]);

        return redirect()->to('/DivEProf/carreras/0');
    }


/*----------------------------------------------------------------------------------------------------*/

    public function f_critEva($search) {
        
        $search = mb_strtoupper("%".utf8_decode($search)."%");

        if($search == "%0%"){
            $critEval = DB::table('criterios_evaluacion')
                ->select('id_crit_eval',
                        'nombre',
                        'descripcion')
                ->where('estado', 1)
                ->orderBy('id_crit_eval')
                ->paginate(10);
        }
        else{
            $critEval = DB::table('criterios_evaluacion')
                ->select('id_crit_eval',
                        'nombre',
                        'descripcion')
                ->when($search, function ($query, $search) {
                    return $query->where('nombre', 'LIKE', $search)
                            ->where('estado', 1);
                })
                ->orderBy('id_crit_eval')
                ->paginate(10);
        }
        
        return view('DivEProf.critEval.c_evaluacion')
        ->with('criterios', $critEval)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchcrit(Request $request) { 

        $search = $request->buscar;
        // return $this->f_critEva($search);
        return redirect()->to('/DivEProf/critEvaluacion/'.$search);
    }

    public function f_n_critEva() { 
        return view('DivEProf.critEval.nuevo')
        ->with('tipos', $this->tipos());   
    }

    public function f_regCritE(Request $request){

        $data = $request->all();

        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.'
        ];

        $validation = \Validator::make($request->all(), [
                'nombre' => 'required|min:3|max:45',
                'descripcion' => 'required|min:3|max:150'
            ], $messages);
        
        if ($validation->fails())  {
                return redirect()->back()->withInput()->withErrors($validation->errors());
            }

        Mcriterios_evaluacion::create([
                'nombre' => mb_strtoupper($data['nombre']),
                'descripcion' => mb_strtoupper($data['descripcion']), 
                'estado' => 1
            ]);

        return redirect()->to('/DivEProf/critEvaluacion/0');
    }

/*----------------------------------------------------------------------------------------------------*/

    public function f_departamentos($pagina){

        $departamentos = DB::table('departamento as d')
            ->leftJoin('persona as p', 'd.id_persona', '=', 'p.id_persona')
            ->leftJoin('empleado as e', 'p.id_persona', '=', 'e.id_persona')
            ->leftJoin('grado as g', 'e.id_grado', '=', 'g.id_grado')
            ->select('d.id_depto', 
                    'd.nombre as depto', 
                    'g.nombre as grado', 
                    DB::raw('CONCAT(p.nombre, " ", p.apePat, " ", p.apeMat) AS jefe'))
            ->where('d.estado', 1)
            ->orderBy('d.id_depto')
            ->paginate(10);

        return view('DivEProf.depto.departamentos')
            ->with('departamentos', $departamentos)
            ->with('tipos', $this->tipos());   
    }

    public function f_departamento($search, $pagina){

        $search = mb_strtoupper("%".utf8_decode($search)."%");
        
        $departamentos = DB::table('departamento as d')
            ->leftJoin('persona as p', 'd.id_persona', '=', 'p.id_persona')
            ->leftJoin('empleado as e', 'p.id_persona', '=', 'e.id_persona')
            ->leftJoin('grado as g', 'e.id_grado', '=', 'g.id_grado')
            ->select('d.id_depto', 
                    'd.nombre as depto', 
                    'g.nombre as grado', 
                    DB::raw('CONCAT(p.nombre, " ", p.apePat, " ", p.apeMat) AS jefe'))
            ->when($search, function ($query, $search) {
                return $query->where('d.nombre', 'LIKE', $search)
                        ->where('d.estado', 1);
            })
            ->orderBy('d.id_depto')
            ->paginate(10);

        return view('DivEProf.depto.departamentos')
            ->with('departamentos', $departamentos)
            ->with('tipos', $this->tipos());   
    }

    public function f_searchdpt(Request $request) { 

        $search = mb_strtoupper($request->search);
        // return $this->f_departamento($search, 1);
        return redirect()->to('/DivEProf/departamentos/'.$search.'/1');
    }

    public function f_n_depto() { 

        $todos = DB::table('persona as p')
            ->join('empleado as e', 'p.id_persona', '=', 'e.id_persona')
            ->join('grado as g', 'e.id_grado', '=', 'g.id_grado')
            ->select('p.id_persona',
                    'p.nombre',
                    'p.apePat',
                    'p.apeMat',
                    'g.nombre as grado')
            ->where('e.id_puesto', '<>', 7)
            ->where('e.id_puesto', '<>', 5)
            ->where('p.estado', 1)
            ->get();

        $asignados = Mdepartamento::select('id_persona')->get();

        $jefes = []; $c = 0;
        foreach($todos as $t){
            $exist = false;
            
            foreach ($asignados as $a){
                if($a->id_persona == $t->id_persona)
                    $exist = true;
            }

            if(!$exist){
                $jefes[$c] = $t;
                $c++;
            }
        }
        
        return view('DivEProf.depto.nuevo')
        ->with('jefes', $jefes)
        ->with('tipos', $this->tipos());   
    }

    public function f_regDepto(Request $request){

        $data = $request->all();

        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.'
        ];

        $validation = \Validator::make($request->all(), [
                'nombre' => 'required|min:3|max:100',
                'id_persona' => 'required|exists:persona,id_persona'
            ], $messages);
        
        if ($validation->fails())  {
                return redirect()->back()->withInput()->withErrors($validation->errors());
            }

        Mdepartamento::create([
                'id_persona' => $data['id_persona'], 
                'nombre' => mb_strtoupper($data['nombre']), 
                'estado' => 1
            ]);

        return redirect()->to('/DivEProf/departamentos/1');
    }
    
/*----------------------------------------------------------------------------------------------------*/

    public function f_grados($pagina) {

        $grados = DB::table('grado')
            ->select('id_grado',
                    'nombre',
                    'significado')
            ->where('estado', 1)
            ->orderBy('id_grado')
            ->paginate(10);

        return view('DivEProf.grado.grados')
            ->with('grados', $grados)
            ->with('tipos', $this->tipos());   
    }

    public function f_grado($search, $pagina) {

        $search = mb_strtoupper("%".utf8_decode($search)."%");

        $grados = DB::table('grado')
        ->select('id_grado',
                'nombre',
                'significado')
        ->when($search, function ($query, $search) {
            return $query->where('nombre', 'LIKE', $search)
                    ->where('estado', 1);
        })
        ->orderBy('id_grado')
        ->paginate(10);

        return view('DivEProf.grado.grados')
            ->with('grados', $grados)
            ->with('tipos', $this->tipos());   
    }

    public function f_searchgra(Request $request) { 

        $search = mb_strtoupper($request->search);
        // return $this->f_grado($search, 1); 
        return redirect()->to('/DivEProf/grados/'.$search.'/1');
    }

    public function f_n_grado() { 

        return view('DivEProf.grado.nuevo')
        ->with('tipos', $this->tipos());   
    }

    public function f_regGrado(Request $request){

        $data = $request->all();
        
        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.'
        ];

        $validation = \Validator::make($data, [
                'nombre' => 'required|string|min:3|max:15',
                'signifiado' => 'required|string|min:3|max:100'
            ], $messages);
        
        if ($validation->fails())  {
                return redirect()->back()->withInput()->withErrors($validation->errors());
            }

        Mgrado::create([
            'nombre' => $data['nombre'], 
            'significado' => $data['significado'], 
            'estado' => 1
        ]);

        return redirect()->to('/DivEProf/grados/1');
    }
        
/*----------------------------------------------------------------------------------------------------*/

    public function f_periodos($pagina) {

        $periodos = Mperiodo::select('id_periodo',
                    'nombre',
                    'inicio',
                    'fin',
                    'estado')
            ->where('condicion', 1)
            ->orderBy('id_periodo')
            ->paginate(10);

        return view('DivEProf.periodo.periodos')
            ->with('periodos', $periodos)
            ->with('tipos', $this->tipos());   
    }

    public function f_periodo($search, $pagina) {

        $search = mb_strtoupper("%".utf8_decode($search)."%");

        $periodos = Mperiodo::select('id_periodo',
                    'nombre',
                    'inicio',
                    'fin',
                    'estado')
            ->where('nombre', 'LIKE', $search)
            ->where('condicion', 1)
            ->orderBy('id_periodo')
            ->paginate(10);

        return view('DivEProf.periodo.periodos')
            ->with('periodos', $periodos)
            ->with('tipos', $this->tipos());   
    }

    public function f_searchperi(Request $request) { 

        $search = $request->search;
        // return $this->f_periodo($search, 1); 
        return redirect()->to('/DivEProf/periodos/'.$search.'/1');
    }

    public function f_n_periodo(){

        $mes = array("ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO",
                    "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");

        $año = [date("Y"), date("Y")+1, date("Y")+2];

        return view('DivEProf.periodo.nuevo')
            ->with('mes', $mes)
            ->with('año', $año)
            ->with('tipos', $this->tipos());
    }

    public function f_regPeriodo(Request $request){
        
        $data = $request->all();

        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.',
            'date_format' => 'El campo :attribute no coincide con el formato obligatorio.',
            'required_if' => 'El campo :attribute no cuenta con el valor previo necesario.',
            'file' => 'El campo :attribute debe ser un archivo (png, jpg, etc).'
        ];

        $validation = \Validator::make($data, [
                'mes_ini' => 'required|string|min:4|max:10',
                'mes_fin' => 'required|string|min:4|max:10',
                'anio_ini' => 'required|date_format:Y',
                'anio_fin' => 'required|date_format:Y',
                'inicio' => 'required|date_format:Y-m-d',
                'fin' => 'required|date_format:Y-m-d',
                'ini_inscripcion' => 'nullable|date_format:Y-m-d',
                'fin_inscripcion' => 'nullable|date_format:Y-m-d',
                'ini_evaluacion' => 'nullable|date_format:Y-m-d',
                'fin_evaluacion' => 'nullable|date_format:Y-m-d',
                'ini_gconstancias' => 'nullable|date_format:Y-m-d',
                'fin_gconstancias' => 'nullable|date_format:Y-m-d',
                'cabecera' => 'nullable|file',
                'pie' => 'nullable|file'
            ], $messages);

        if ($validation->fails())  {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $ruta = "images/ac_ito/"; $cabecera = ""; $pie = ""; 

        if($request->hasFile('cabecera')){
            $g_new = $request->file('cabecera')->getClientOriginalName();
            $cabecera =  $request->file('cabecera');
            $cabecera->move($ruta, $g_new);
            $cabecera = '/'.$ruta.$g_new;
        }

        if($request->hasFile('pie')){
            $t_new = $request->file('pie')->getClientOriginalName();
            $pie = $request->file('pie');
            $pie->move($ruta, $t_new);
            $pie = '/'.$ruta.$t_new;
        }

        $inscripcion = false; $evaluacion = false; $gconstancias = false; $semestre = false;

        ($data['ini_inscripcion'] != '' && $data['fin_inscripcion'] != '') 
            ? (($data['fin_inscripcion'] < date('Y-m-d', strtotime('+2 days', strtotime($data['ini_inscripcion'])))
                || $data['fin_inscripcion'] > date('Y-m-d', strtotime('+14 days', strtotime($data['ini_inscripcion']))))
                ? $inscripcion = true : null) 
            : (($data['ini_inscripcion'] != '' || $data['fin_inscripcion'] != '')
                ? $inscripcion = true
                : null);

        ($data['ini_evaluacion'] != '' && $data['fin_evaluacion'] != '') 
            ? (($data['fin_evaluacion'] < date('Y-m-d', strtotime('+2 days', strtotime($data['ini_evaluacion'])))
                || $data['fin_evaluacion'] > date('Y-m-d', strtotime('+14 days', strtotime($data['ini_evaluacion']))))
                ? $evaluacion = true : null) 
            : (($data['ini_evaluacion'] != '' || $data['fin_evaluacion'] != '') 
                ? $evaluacion = true
                : null);

        ($data['ini_gconstancias'] != '' && $data['fin_gconstancias'] != '') 
            ? (($data['fin_gconstancias'] < date('Y-m-d', strtotime('+2 days', strtotime($data['ini_gconstancias'])))
                || $data['fin_gconstancias'] > date('Y-m-d', strtotime('+14 days', strtotime($data['ini_gconstancias']))))
                ? $gconstancias = true : null) 
            : (($data['ini_gconstancias'] != '' || $data['fin_gconstancias'] != '')
                ? $gconstancias = true
                : null);
        
        ($data['fin'] < date('Y-m-d', strtotime('+4 month', strtotime($data['inicio'])))
        || $data['fin'] > date('Y-m-d', strtotime('+5 month', strtotime($data['inicio']))))
            ? $semestre = true 
            : null;

        if($inscripcion || $evaluacion || $gconstancias || $semestre){
            $error = ["tiempo" => "Revise los tiempos en los periodos que intenta registrar."];
            return redirect()->back()->withInput()->withErrors($error);
        }
        
        $siguiente = Mperiodo::select('id_periodo')
            ->where('estado', "Siguiente")->first();
        
        $siguiente != null 
            ? 
                Mperiodo::where('id_periodo', $siguiente->id_periodo)
                    ->update(['estado' => "Espera"])
            :   null;

        $nombre = $data['mes_ini']." ".$data['anio_fin']." - ".$data['mes_fin']." ".$data['anio_fin'];
        
        Mperiodo::create([
            'nombre' => $nombre, 
            'inicio' => $data['inicio'], 
            'fin' => $data['fin'], 
            'ini_inscripcion' => $data['ini_inscripcion'], 
            'fin_inscripcion' => $data['fin_inscripcion'],
            'ini_evaluacion' => $data['ini_evaluacion'], 
            'fin_evaluacion' => $data['fin_evaluacion'],
            'ini_gconstancias' => $data['ini_gconstancias'], 
            'fin_gconstancias' => $data['fin_gconstancias'],
            'cabecera' => $cabecera, 
            'pie' => $pie, 
            'estado' => "Siguiente"]);

        return redirect()->to('/DivEProf/periodos/1');

    }

    public function f_det_periodo($id_peri){

        $periodo = Mperiodo::where('id_periodo', $id_peri)->get();

        return view('DivEProf.periodo.detalle')
        ->with('periodo', $periodo)
        ->with('tipos', $this->tipos());
    }

/*----------------------------------------------------------------------------------------------------*/

    public function f_personal($pagina) {

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
            ->where('p.tipo', "Empleado")
            ->where('p.estado', 1)
            ->orderBy('p.id_persona')
            ->paginate(10);

        return view('DivEProf.persona.personas')
            ->with('personas', $empleados)
            ->with('tipos', $this->tipos());   
    }

    public function f_personalB($search, $pagina) {

        $search = mb_strtoupper("%".utf8_decode($search)."%");

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
            ->where('p.tipo', "Empleado")
            ->where('p.nombre', 'LIKE', $search)
            ->orWhere('p.apePat', 'LIKE', $search)
            ->orWhere('p.apeMat', 'LIKE', $search)
            // ->orWhere('d.nombre', 'LIKE', $search)
            ->orderBy('p.id_persona')
            ->where('p.estado', 1)
            ->paginate(10);

        return view('DivEProf.persona.personas')
            ->with('personas', $empleados)
            ->with('tipos', $this->tipos());   
    }

    public function f_searchpers(Request $request) { 

        $search = $request->search;
        // return $this->f_personalB($search, 1); 
        return redirect()->to('/DivEProf/personal/'.$search.'/1');
    }

    public function f_n_persona(){

        $deptos = Mdepartamento::where('estado', 1)->get();

        $puesto = Mpuesto::select('id_puesto', 'nombre')
            ->where('id_puesto', '<>', 7)
            ->where('id_puesto', '<>', 6)
            ->where('estado', 1)
            ->get();

        $grados = Mgrado::where('estado', 1)->get();

        return view('DivEProf.persona.nueva')
        ->with('departamentos', $deptos)
        ->with('puestos', $puesto)
        ->with('grados', $grados)
        ->with('tipos', $this->tipos());
    }

    public function f_regEmp(Request $request){

        $data = $request->all();

        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.',
            'unique' => 'El campo :attribute ya se ha registrado.',
            'exists' => 'El campo :attribute no es un elemento valido.',
            'integer' => 'El campo :attribute no corresponde al tipo correcto.'
        ];

        $validation = \Validator::make($data, [
                'id_grado' => 'required|integer|exists:grado,id_grado',
                'nombre' => 'required|min:3|max:30',
                'apePat' => 'required|min:3|max:20',
                'apeMat' => 'required|min:3|max:20',
                'id_depto' => 'required|integer|exists:departamento,id_depto',
                'curp' => 'nullable|unique:persona|size:18',
                'id_puesto' => 'required|integer|exists:puesto,id_puesto',
            ], $messages);

        if ($validation->fails())  {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $hoy = date("Y-m-d");
        $nomUser = mb_strtoupper($data['nombre'].' '.$data['apePat'].' '.$data['apeMat']);
        $contraseña = bcrypt(mb_strtoupper($data['curp']));

        $persona = Mpersona::create([
                    'nombre' => mb_strtoupper($data['nombre']), 
                    'apePat' => mb_strtoupper($data['apePat']),
                    'apeMat' => mb_strtoupper($data['apeMat']), 
                    'curp' => mb_strtoupper($data['curp']), 
                    'tipo' => "Empleado", 
                    'estado' => 1]);

        Mempleado::create([
            'id_persona' => $persona->id, 
            'id_depto' => $data['id_depto'], 
            'id_grado' => $data['id_grado'], 
            'id_puesto' => $data['id_puesto']
        ]);

        Musers::create([
            'id_persona' => $persona->id, 
            'id_puesto' => $data['id_puesto'],
            'nombre' => $nomUser, 
            'usuario' => mb_strtoupper($data['curp']), 
            'password' => $contraseña,
            'fecha_registro' => $hoy, 
            'edo_sesion' => 0, 
            'estado' => 1]);

            return redirect()->to('/DivEProf/personal/1');
        
    }

    public function f_e_persona($id_per){

        $deptos = Mdepartamento::get();

        $puesto = Mpuesto::select('id_puesto', 'nombre')
            ->where('id_puesto', '<>', 7)
            ->where('id_puesto', '<>', 6)
            ->where('estado', 1)
            ->get();

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

        return view('DivEProf.persona.editar')
        ->with('persona', $persona)
        ->with('departamentos', $deptos)
        ->with('puestos', $puesto)
        ->with('grados', $grados)
        ->with('tipos', $this->tipos());
    }

    public function f_editEmp($id_emp, Request $request){

        $data = $request->all();
        $curp = Mpersona::where('id_persona', $id_emp)->first();

        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.',
            'unique' => 'El campo :attribute ya se ha registrado.',
            'exists' => 'El campo :attribute no es un elemento valido.',
            'integer' => 'El campo :attribute no corresponde al tipo correcto.'
        ];

        $validation = \Validator::make($data, [
                'id_grado' => 'required|integer|exists:grado,id_grado',
                'nombre' => 'required|min:3|max:30',
                'apePat' => 'required|min:3|max:20',
                'apeMat' => 'required|min:3|max:20',
                'id_depto' => 'required|integer|exists:departamento,id_depto',
                'id_puesto' => 'required|integer|exists:puesto,id_puesto',
            ], $messages);

        if ($validation->fails())  {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        if($curp->curp != mb_strtoupper($data['curp'])){

            $validation = \Validator::make($data, [
                'curp' => 'nullable|unique:persona|size:18'
            ], $messages);

            if ($validation->fails())  {
                return redirect()->back()->withInput()->withErrors($validation->errors());
            }
        }

        $nomUser = mb_strtoupper($data['nombre'].' '.$data['apePat'].' '.$data['apeMat']);

        Mpersona::where('id_persona', $id_emp)
            ->update([
                'nombre' => mb_strtoupper($data['nombre']), 
                'apePat' => mb_strtoupper($data['apePat']),
                'apeMat' => mb_strtoupper($data['apeMat']), 
                'curp' => mb_strtoupper($data['curp'])
            ]);

        Mempleado::where('id_persona', $id_emp)
            ->update([
                'id_depto' => $data['id_depto'], 
                'id_grado' => $data['id_grado'], 
                'id_puesto' => $data['id_puesto']
            ]);

        Musers::where('id_persona', $id_emp)
            ->update([
                'id_puesto' => $data['id_puesto'], 
                'nombre' => $nomUser, 
                'usuario' => $data['curp']
            ]);


        return redirect()->to('/DivEProf/personal/1');
    }

    public function f_deleteper($id_delete){

        Mpersona::where('id_persona', $id_delete)
            ->update(['estado' => 0]);

        return redirect()->to('/DivEProf/personal/1');
    }

/*----------------------------------------------------------------------------------------------------*/

    public function f_puestos($search) {

        $search = mb_strtoupper("%".utf8_decode($search)."%");
        
        if($search == "%0%"){
            $puestos = DB::table('puesto')
                ->select('id_puesto',
                        'nombre',
                        'descripcion')
                ->where('estado', 1)
                ->orderBy('id_puesto')
                ->paginate(10); 
        }else{
            $puestos = DB::table('puesto')
                ->select('id_puesto',
                        'nombre',
                        'descripcion')
                ->where('nombre', 'LIKE', $search)
                ->where('estado', 1)
                ->orderBy('id_puesto')
                ->paginate(10); 
        }

        return view('DivEProf.puesto.puestos')
            ->with('puestos', $puestos)
            ->with('tipos', $this->tipos());   
    }

    public function f_searchpue(Request $request) { 

        $search = $request->search;
        // return $this->f_puestos($search); 
        return redirect()->to('/DivEProf/puestos/'.$search);
    }

/*----------------------------------------------------------------------------------------------------*/

    
    public function f_s_labores($pagina) { 

        $fechas = DB::table('fechas_inhabiles')
            ->select('id_fecha',
                    'fecha',
                    'motivo')
            ->where('estado', 1)
            ->orderBy('id_fecha', 'desc')
            ->paginate(10);
        
        return view('DivEProf.suspencion.sus_labores')
        ->with('fechas', $fechas)
        ->with('tipos', $this->tipos()); 
    }

    public function f_s_labor($search, $pagina) { 

        $search = mb_strtoupper("%".utf8_decode($search)."%");
        
        $fechas = DB::table('fechas_inhabiles')
            ->select('id_fecha',
                    'fecha',
                    'motivo')
            ->when($search, function ($query, $search) {
                return $query->where('estado', 1)
                        ->where('fecha', 'LIKE', $search)
                        ->orWhere('motivo', 'LIKE', $search);
            })
            ->orderBy('id_fecha', 'desc')
            ->paginate(10);
        
        return view('DivEProf.suspencion.sus_labores')
            ->with('fechas', $fechas)
            ->with('tipos', $this->tipos()); 
    }

    public function f_searchslab(Request $request) { 

        $search = $request->search;
        // return $this->f_s_labor($search, 1); 
        return redirect()->to('DivEProf/suspLabores/'.$search.'/1');
    }

    public function f_n_fecha(){

        return view('DivEProf.suspencion.nueva')
        ->with('tipos', $this->tipos());
    }

    public function f_regFecha(Request $request){

        $data = $request->all();

        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'date_format' => 'El campo :attribute no cuanta con formato de fecha.'
        ];

        $validation = \Validator::make($data, [
            'fecha' => 'required|date_format:Y-m-d',
            'fecha_fin' => 'nullable|date_format:Y-m-d',
            'motivo' => 'required|string|min:3|max:100'
        ], $messages);
        
        if ($validation->fails())  {
                return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        if($data['fecha_fin'] == '' || $data['fecha'] == $data['fecha_fin'])  {

            Mfechas_inhabiles::create([
                'fecha' => $data['fecha'],
                'motivo' => mb_strtoupper($data['motivo']), 
                'estado' => 1]);
        
            return redirect()->to('/DivEProf/suspLabores/1');
        }
        elseif($data['fecha'] > $data['fecha_fin']) {
            ?>
                <script>
                    alert("La fecha de término no puede ser menor que la fecha de inicio.");
                    location.href = "DivEProf/nuevaFecha";
                </script>
            <?php
        }
        else{
            $fnew = $data['fecha']; $end = false;
            while($end != true){

                Mfechas_inhabiles::create([
                    'fecha' => $fnew,
                    'motivo' => mb_strtoupper($data['motivo']), 
                    'estado' => 1
                ]);

                $fnew = date('Y-m-d', strtotime('tomorrow', strtotime($fnew)));

                if($fnew == $data['fecha_fin']){ 

                    Mfechas_inhabiles::create([
                        'fecha' => $fnew,
                        'motivo' => mb_strtoupper($data['motivo']), 
                        'estado' => 1
                    ]);
                    $end = true;
                    return redirect()->to('/DivEProf/suspLabores/1');
                }
            }
        }

    }

/*----------------------------------------------------------------------------------------------------*/

    public function f_lugar($search, $pagina) {

        $search = mb_strtoupper("%".utf8_decode($search)."%");
        
        $lugares = DB::table('lugar')
            ->select('id_lugar',
                    'nombre')
            ->when($search, function ($query, $search) {
                return $query->where('nombre', 'LIKE', $search)
                        ->where('estado', 1);
            })
            ->orderBy('id_lugar')
            ->paginate(10);

        return view('DivEProf.lugares.lugares')
            ->with('lugares', $lugares)
            ->with('tipos', $this->tipos());   
    }

    public function f_lugares($pagina) {

        $lugares = DB::table('lugar')
            ->select('id_lugar',
                    'nombre')
            ->where('estado', 1)
            ->orderBy('id_lugar')
            ->paginate(10);

        return view('DivEProf.lugares.lugares')
        ->with('lugares', $lugares)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchlug(Request $request) { 

        $search = $request->search;
        // return $this->f_lugar($search, 1);   
        return redirect()->to('DivEProf/lugares/'.$search.'/1');
    }

    public function f_n_lugar(){

        return view('DivEProf.lugares.nuevo')
        ->with('tipos', $this->tipos());
    }

    public function f_regLugar(Request $request){

        $data = $request->all();

        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.'
        ];

        $validation = \Validator::make($data, [
                'nombre' => 'required|string|min:3|max:100'
            ], $messages);
        
        if ($validation->fails())  {
                return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        Mlugar::create([
            'nombre' => mb_strtoupper($data['nombre']), 
            'estado' => 1
        ]);

        return redirect()->to('/DivEProf/lugares/1');
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

        $data = $request->all();

        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.',
            'exists' => 'El campo :attribute no es un valor valido.',
        ];
        
        $validation = \Validator::make($data, [
            'id_grado' => 'required|exists:grado,id_grado',
            'nombre' => 'required|min:3|max:30',
            'apePat' => 'required|min:3|max:20',
            'apeMat' => 'required|min:3|max:20'
        ], $messages);      

        if ($validation->fails())  {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        if($curp->curp != mb_strtoupper($data['curp'])){

            $validation = \Validator::make($data, [
                'curp' => 'nullable|unique:persona|size:18'
            ], $messages);

            if ($validation->fails())  {
                return redirect()->back()->withInput()->withErrors($validation->errors());
            }
        }

        $usuario = $request->user()->id_persona;

        Mpersona::where('id_persona', $usuario)
            ->update([
                'nombre' => $data['nombre'],
                'apePat' => $data['apePat'], 
                'apeMat' => $data['apeMat'],
                'curp' => $data['curp']
            ]);

        Mempleado::where('id_persona', $usuario)
            ->update(['id_grado' => $data['id_grado']]);

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
