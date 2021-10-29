<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Codedge\Fpdf\Facades\Fpdf;
use Illuminate\Support\Arr;

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
        $this->middleware('admin');
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

        // $finish = DB::select('SELECT id_periodo, fin FROM periodo WHERE estado = ? LIMIT 1', 
        //     ["Actual"]);

        $finish = Mperiodo::where('estado', "Actual")->first();
        
        if($today > $finish->fin){

            // Mgrupo::where('id_periodo', $finish->id_periodo)
            //     ->update(['estado' => 0]);

            // Mperiodo::where('estado', "Anterior")
            // ->update(['estado' => "Finalizado"]);
    
            // Mperiodo::where('estado', "Actual")
            //     ->update(['estado' => "Anterior"]);
    
            // Mperiodo::where('estado', "Siguiente")
            //     ->update(['cabecera' => $finish->cabecera,
            //             'pie' =>$finish->pie,
            //             'estado' => "Actual"]);
    
            // DB::delete('DELETE FROM horarios_impresos WHERE id_grupo <> 0');
        }

        return true;
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

        $roll = Mperiodo::where('estado', "Actual")->first();

        if($now < $roll->inicio || $now > $roll->fin_inscripcion)
            $modificar = false;
    
        $periodo = DB::select('SELECT nombre FROM periodo WHERE estado = ? LIMIT 1',
                    ["Actual"]);
        $periodo = $periodo[0]->nombre;

        
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
            // ->chunk(10, function ($query) {
            //     $data = [];
            //     foreach ($query as $q){

            //         $data = Arr::prepend($data,[
            //             'id_actividad' => $q->id_actividad,
            //             'clave' => $q->clave,
            //             'nombre' => $q->nombre,
            //             'creditos' => $q->creditos,
            //             'depto' => $q->depto,
            //             'tipo' => $q->tipo,
            //             'descripcion' => $q->descripcion
            //         ]);
            //     }

            //     return $data;
            // });

        

        return view('CoordAC.actividad.actividades')
        ->with('actividades', $actividades)
        ->with('periodo', $periodo)
        ->with('vista', 00)
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
            // ->chunk(10, function ($query) {
            //     $data = null;
            //     foreach ($query as $q){
            //         $d = [
            //             'id_actividad' => $q->id_actividad,
            //             'clave' => $q->clave,
            //             'nombre' => $q->nombre,
            //             'creditos' => $q->creditos,
            //             'depto' => $q->depto,
            //             'tipo' => $q->tipo,
            //             'descripcion' => $q->descripcion
            //         ];

            //         $data = Arr::add($d);
            //     }

            //     return $data;
            // });

        return view('CoordAC.actividad.actividades')
        ->with('actividades', $actividades)
        ->with('periodo', $periodo)
        ->with('vista', 01)
        ->with('mod', true)
        ->with('tipos', $this->tipos()); 
    }

    public function f_searchact(Request $request) { 

        $search = $request->search;
        return redirect()->to('/CoordAC/actividad/'.$search.'/1');
        // return $this->f_actividad($search, 1);   
    }

    public function f_depto() { 
 
        $depto = DB::table('departamento AS d')
            ->select('d.id_depto', 'd.nombre')
            ->where('d.estado', 1)
            ->orderBy('d.id_depto')
            ->paginate(10);


        return view('CoordAC.actividad.actdepto')
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

        return view('CoordAC.actividad.actividades')
        ->with('actividades', $actividades)
        ->with('periodo', $depto->nombre)
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

        return view('CoordAC.actividad.actividades')
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

        return view('CoordAC.actividad.nueva')
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
            'id_depto' => 'required',
            'id_tipo' => 'required',
            'clave' => 'required|size:5',
            'nombre' => 'required|min:3|max:100',
            'creditos' => 'required',
            'descripcion' => 'nullable|max:250',
            'restringida' => 'required'
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

        return redirect()->to('/CoordAC/actividades/1');
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

        return view('CoordAC.actividad.editar')
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
                'id_depto' => 'required',
                'id_tipo' => 'required',
                'nombre' => 'required|min:3|max:100',
                'descripcion' => 'nullable|max:250',
                'restringida' => 'required'
            ], $messages);  
        } else {

            $validation = \Validator::make($request->all(), [
                'id_depto' => 'required',
                'id_tipo' => 'required',
                'clave' => 'required|unique:actividad|size:5',
                'nombre' => 'required|min:3|max:100',
                'descripcion' => 'nullable|max:250',
                'restringida' => 'required'
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

        return redirect()->to('/CoordAC/actividades/1');
    }

    public function f_deleteact($id_delete){

        Mactividad::where('id_actividad', $id_delete)
            ->update(['estado' => 0]);

        return redirect()->to('/CoordAC/actividades/1');
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

        return view('CoordAC.grupo.grupos')
        ->with('grupos', $grupos)
        ->with('periodo', $periodo->nombre)
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

        return view('CoordAC.grupo.grupos')
        ->with('grupos', $grupos)
        ->with('periodo', $periodo->nombre)
        ->with('vista', 01)
        ->with('mod', true)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchgru(Request $request) { 

        $search = $request->search;
        return redirect()->to('/CoordAC/grupos/'.$search.'/1');
        // return $this->f_gruposB($search, 1);  
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

        return view('CoordAC.grupo.nuevo')
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
            'orden' => 'required|boolean'
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

        return redirect()->to('/CoordAC/grupos/1');
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
            'orden' => 'required|boolean'
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
        

        return redirect()->to('/CoordAC/grupos/1');
    }

    public function f_deletegru($id_delete){

        Mgrupo::where('id_grupo', $id_delete)
            ->update(['estado' => 0]);

        return redirect()->to('/CoordAC/grupos/1');
    
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

        return view('CoordAC.estudiante.estudiantes')
        ->with('estudiantes', $estudiantes)
        ->with('mod', true)
        ->with('outime', $outime)
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
        
        $outime = 2;
        $roll->fin_inscripcion < $now ? $outime = true : $outime = false;

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

        return view('CoordAC.estudiante.estudiantes')
        ->with('estudiantes', $estudiantes)
        ->with('mod', true)
        ->with('outime', $outime)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchest(Request $request) { 

        $search = $request->search;
        return redirect()->to('/CoordAC/estudiantes/'.$search.'/1');
        // return $this->f_estudiantesB($search, 1);
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
            'id_carrera' => 'required',
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


        return redirect()->to('/CoordAC/estudiantes/1');
    }

    public function f_e_estudiante($id_est)  {
           
        $estudiante = DB::table('persona AS p')
            ->join('estudiante AS e', 'p.id_persona', '=', 'e.id_persona')
            ->join('carrera AS c', 'e.id_carrera', '=', 'c.id_carrera')
            ->select('e.id_estudiante', 
                    'e.num_control',
                    'p.nombre', 
                    'p.apePat', 
                    'p.apeMat', 
                    'e.email',
                    'c.nombre AS carrera', 
                    'e.semestre', 
                    'p.curp',
                    'e.id_persona', 
                    'e.id_carrera')
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

        $data = $request->all();
        $estudiante = DB::table('persona as p')
            ->join('estudiante as e', 'p.id_persona', '=', 'e.id_persona')
            ->select('p.*', 'e.*')
            ->where('p.id_persona', $id_est)
            ->get();

        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.',
            'unique' => 'El campo :attribute ya se ha registrado.',
            'email' => 'El dominio valido para el e-mail es: @itoaxaca.edu.mx',
            'ends_with' => 'El dominio valido para el e-mail es: @itoaxaca.edu.mx'
        ];

        $validation = \Validator::make($request->all(), [
            'nombre' => 'required|min:3|max:30',
            'apePat' => 'required|min:3|max:20',
            'apeMat' => 'required|min:3|max:20',
            'id_carrera' => 'required'
        ], $messages);

        if ($validation->fails())  {
            return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $user = false;

        if( $data['email'] != $estudiante[0]->email ) {
            $validationE = \Validator::make($request->all(), [
                'email' => 'required|email|unique:estudiante|ends_with:@itoaxaca.edu.mx|min:24|max:25'
            ], $messages);
        
            if ($validationE->fails())  {
                return redirect()->back()->withInput()->withErrors($validationE->errors());
            }

            $user = true;
        } 
        
        if( $data['num_control'] != $estudiante[0]->num_control ) {
            $validationNC = \Validator::make($request->all(), [
                'num_control' => 'required|unique:estudiante|min:8|max:9'
            ], $messages);

            if ($validationNC->fails())  {
                return redirect()->back()->withInput()->withErrors($validationNC->errors());
            }

            $user = true;
        } 
        
        if(mb_strtoupper($data['curp']) != $estudiante[0]->curp ){
            
            $validationC = \Validator::make($request->all(), [
                'curp' => 'nullable|unique:persona|size:18'
            ], $messages);

            if ($validationC->fails())  {
                return redirect()->back()->withInput()->withErrors($validationC->errors());
            }
        }

        

        $nomUser = mb_strtoupper($data['nombre'].' '.$data['apePat'].' '.$data['apeMat']);

        Mpersona::where('id_persona', $id_est)
            ->update(['nombre' => mb_strtoupper($data['nombre']), 
                    'apePat' => mb_strtoupper($data['apePat']),
                    'apeMat' => mb_strtoupper($data['apeMat']), 
                    'curp' => mb_strtoupper($data['curp'])
                ]);

        Mestudiante::where('id_persona', $id_est)
            ->update(['id_carrera' => $data['id_carrera'], 
                    'num_control' => $data['num_control'], 
                    'email' => $data['email'], 
                    'semestre' => $data['semestre']
                ]);

        if($user){

            Musers::where('id_persona', $id_est)
                ->update(['nombre' => $nomUser, 
                    'usuario' => $data['email'],
                    'password' => Hash::make($data['num_control']),
                    'edo_sesion' => 0
                ]);
        } else {

            Musers::where('id_persona', $id_est)
                ->update(['nombre' => $nomUser
                ]);
        }

        return redirect()->to('/CoordAC/estudiantes/1');
    }

    public function f_deleteest($id_delete){

        Mpersona::where('id_persona', $id_delete)
            ->update(['estado' => 0]);

        return redirect()->to('/CoordAC/estudiantes/1');
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

        return view('CoordAC.carrera.carreras')
            ->with('carreras', $carreras)
            ->with('tipos', $this->tipos());   
    }

    public function f_searchcar(Request $request) { 

        $search = $request->search;
        // return $search;
        return redirect()->to('/CoordAC/carreras/'.$search);
        // return $this->f_carreras($search);
    }

    public function f_n_carrera() { 

        $deptos = Mdepartamento::where('estado', 1)->get();

        return view('CoordAC.carrera.nueva')
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

        return redirect()->to('/CoordAC/carreras/0');
    }

    public function f_e_carrera($id_car) { 

        $carrera = DB::table('carrera AS c')
            ->join('departamento AS d', 'd.id_depto', '=', 'c.id_depto')
            ->select('c.id_carrera', 
                    'c.nombre', 
                    'd.nombre AS depto')
            ->where('c.id_carrera', $id_car)
            ->get();

        return view('CoordAC.carrera.editar')
        ->with('carrera', $carrera)
        ->with('tipos', $this->tipos());   
    }

    public function f_editCar($id_car, Request $request){

        $data = $request->all();

        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.'
        ];

        $validation = \Validator::make($request->all(), [
                'nombre' => 'required|min:3|max:100'
            ], $messages);
        
        if ($validation->fails())  {
                return redirect()->back()->withInput()->withErrors($validation->errors());
            }

        Mcarrera::where('id_carrera', $id_car)
            ->update(['nombre' => mb_strtoupper($data['nombre'])
        ]);

        return redirect()->to('/CoordAC/carreras/0');
    }

    public function f_deletecar($carrera){

        Mcarrera::where('id_carrera', $carrera)
            ->update(['estado' => 0]);

        return redirect()->to('/CoordAC/carreras/0');
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
        
        return view('CoordAC.critEval.c_evaluacion')
        ->with('criterios', $critEval)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchcrit(Request $request) { 

        $search = $request->buscar;
        return redirect()->to('/CoordAC/critEvaluacion/'.$search);
        // return $this->f_critEva($search);
    }

    public function f_n_critEva() { 
        return view('CoordAC.critEval.nuevo')
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

        return redirect()->to('/CoordAC/critEvaluacion/0');
    }

    public function f_e_critEva($id_crit) { 
        
        $critEval = Mcriterios_evaluacion::where('id_crit_eval', $id_crit)
            ->get();
        
        return view('CoordAC.critEval.editar')
            ->with('criterio', $critEval)
            ->with('tipos', $this->tipos());  
    }

    public function f_editCritE($id_critE, Request $request){

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

        Mcriterios_evaluacion::where('id_crit_eval', $id_critE)
            ->update([
                'nombre' => mb_strtoupper($data['nombre']),
                'descripcion' => mb_strtoupper($data['descripcion'])
            ]);

        return redirect()->to('/CoordAC/critEvaluacion/0');
    }

    public function f_deletecrit($criterio){

        Mcriterios_evaluacion::where('id_crit_eval', $criterio)
            ->update(['estado' => 0]);

        return redirect()->to('/CoordAC/critEvaluacion/0');
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

        return view('CoordAC.depto.departamentos')
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

        return view('CoordAC.depto.departamentos')
        ->with('departamentos', $departamentos)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchdpt(Request $request) { 

        $search = $request->search;
        return redirect()->to('/CoordAC/departamentos/'.$search.'/1');
        // return $this->f_departamento($search, 1);
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

        return view('CoordAC.depto.nuevo')
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

        return redirect()->to('/CoordAC/departamentos/1');
    }
    
    public function f_e_depto($id_dep) { 

        $depto = DB::table('departamento as d')
            ->join('persona as p', 'd.id_persona', '=', 'p.id_persona')
            ->join('empleado as e', 'p.id_persona', '=', 'e.id_persona')
            ->join('grado as g', 'e.id_grado', '=', 'g.id_grado')
            ->select('d.id_depto',
                    'd.nombre as depto',
                    'g.nombre as grado',
                    'p.nombre',
                    'p.apePat',
                    'p.apeMat')
            ->where('d.estado', 1)
            ->where('d.id_depto', $id_dep)
            ->get();

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

        return view('CoordAC.depto.editar')
        ->with('depto', $depto)
        ->with('jefes', $jefes)
        ->with('tipos', $this->tipos());   
    }

    public function f_editDepto($id_dep, Request $request){
        
        $data = $request->all();

        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.'
        ];

        $validation = \Validator::make($request->all(), [
                'nombre' => 'required|min:3|max:100',
                'id_persona' => 'nullable|exists:persona,id_persona'
            ], $messages);
        
        if ($validation->fails())  {
                return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        $nombre = mb_strtoupper($data['nombre']);
        $newjefe = $data['id_persona'];

        $asignado = Mdepartamento::where('id_persona', $newjefe)->first();

        if($asignado == null){

            if($newjefe == null){
                Mdepartamento::where('id_depto', $id_dep)
                    ->update(['nombre' => $nombre]);

                return redirect()->to('/CoordAC/departamentos/1');

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

        return redirect()->to('/CoordAC/departamentos/1');
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

        return view('CoordAC.grado.grados')
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

        return view('CoordAC.grado.grados')
            ->with('grados', $grados)
            ->with('tipos', $this->tipos());   
    }

    public function f_searchgra(Request $request) { 

        $search = $request->search;
        return redirect()->to('/CoordAC/grados/'.$search.'/1');
        // return $this->f_grado($search, 1); 
    }

    public function f_n_grado() { 

        return view('CoordAC.grado.nuevo')
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

        return redirect()->to('/CoordAC/grados/1');
    }
        
    public function f_e_grado($id_gra) { 

        $grado = Mgrado::where('id_grado', $id_gra)->get();

        return view('CoordAC.grado.editar')
        ->with('grado', $grado)
        ->with('tipos', $this->tipos());   
    }

    public function f_editGrado($id, Request $request){

        $data = $request->all();
        
        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.'
        ];

        $validation = \Validator::make($data, [
                'nombre' => 'required|string|min:3|max:15',
                'significado' => 'required|string|min:3|max:100'
            ], $messages);
        
        if ($validation->fails())  {
                return redirect()->back()->withInput()->withErrors($validation->errors());
            }

        Mgrado::where('id_grado', $id)
            ->update([
                'nombre' => $data['nombre'], 
            'significado' => $data['significado'], 
            ]);

        return redirect()->to('/CoordAC/grados/1');
    }

    public function f_deletegra($id_delete){

        Mgrado::where('id_grado', $id_delete)
            ->update(['estado' => 0]);

        return redirect()->to('/CoordAC/grados/1');
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

        return view('CoordAC.periodo.periodos')
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

        return view('CoordAC.periodo.periodos')
            ->with('periodos', $periodos)
            ->with('tipos', $this->tipos());   
    }
/**Realiza la busqueda de periodos según el parametro de entrada.
 * Busca coincidencias por año y nombre.
 */
    public function f_searchperi(Request $request) { 

        $search = $request->search;
        // return $this->f_periodo($search, 1); 
        return redirect()->to('/CoordAC/periodos/'.$search.'/1');
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
        
        return redirect()->to('/CoordAC/periodos/1');
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

        $ruta = "images/ac_ito/"; $cabecera = null; $pie = null; 

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

        
        Mperiodo::where('id_periodo', $id_peri)
            ->update([ 
            'ini_inscripcion' => $data['ini_inscripcion'], 
            'fin_inscripcion' => $data['fin_inscripcion'],
            'ini_evaluacion' => $data['ini_evaluacion'], 
            'fin_evaluacion' => $data['fin_evaluacion'],
            'ini_gconstancias' => $data['ini_gconstancias'], 
            'fin_gconstancias' => $data['fin_gconstancias']
        ]);

        if($cabecera != null){
            Mperiodo::where('id_periodo', $id_peri)
                ->update([ 'cabecera' => $cabecera ]);
        }

        if($pie != null){
            Mperiodo::where('id_periodo', $id_peri)
                ->update([ 'pie' => $pie ]);
        }


        return redirect()->to('/CoordAC/periodos/1');
    }

    public function f_deleteperi($id_delete){

        Mperiodo::where('id_periodo', $id_delete)
            ->update(['estado' => "Eliminado", 'condicion' => 0]);

        return redirect()->to('/CoordAC/periodos/1');
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

        return view('CoordAC.persona.personas')
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

        return view('CoordAC.persona.personas')
        ->with('personas', $empleados)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchpers(Request $request) { 

        $search = $request->search;
        return redirect()->to('/CoordAC/personal/'.$search.'/1');
        // return $this->f_personalB($search, 1); 
    }

    public function f_n_persona(){

        $deptos = Mdepartamento::where('estado', 1)->get();

        $puesto = Mpuesto::select('id_puesto', 'nombre')
            ->where('id_puesto', '<>', 7)
            ->where('id_puesto', '<>', 6)
            ->where('estado', 1)
            ->get();

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

            return redirect()->to('/CoordAC/personal/1');
        
    }

    public function f_regAdmin(Request $request){

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
                    'estado' => 1
                ]);

        Mempleado::create([
            'id_persona' => $persona->id, 
            'id_depto' => $data['id_depto'], 
            'id_grado' => $data['id_grado'], 
            'id_puesto' => 7
        ]);

        Musers::create([
            'id_persona' => $persona->id, 
            'id_puesto' => $data['id_puesto'],
            'nombre' => $nomUser, 
            'usuario' => mb_strtoupper($data['curp']), 
            'password' => $contraseña,
            'fecha_registro' => $hoy, 
            'edo_sesion' => 0, 
            'estado' => 1
        ]);

        Mempleado::where('id_persona', $request->user()->id_persona)
            ->update(['id_puesto' => 9]);
            
        return redirect()->to('/CoordAC/personal/1');
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

        return view('CoordAC.persona.editar')
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


        return redirect()->to('/CoordAC/personal/1');
    }

    public function f_inhabilitados() {

        $personas = DB::table('persona AS p')
            ->join('empleado AS e', 'p.id_persona', '=', 'e.id_persona')
            ->join('departamento AS d', 'e.id_depto', '=', 'd.id_depto')
            ->join('grado AS g', 'e.id_grado', '=', 'g.id_grado')
            ->join('puesto AS pu', 'e.id_puesto', '=', 'pu.id_puesto')
            ->select('p.id_persona', 
                    'p.nombre', 
                    'p.apePat AS paterno', 
                    'p.apeMat AS materno',
                    'p.curp', 
                    'd.nombre AS depto', 
                    'g.nombre AS grado',
                    'pu.nombre AS puesto', 
                    'p.estado')
            ->where('p.estado', 1)
            ->where('p.tipo', "Empleado")
            ->where('e.id_puesto', 9)
            ->get();

        return view('CoordAC.persona.inhabilitados')
        ->with('personas', $personas)
        ->with('tipos', $this->tipos());   
    }

    public function f_habilitar($id_emp, Request $request){

        $puesto = $request->id_puesto;

        Mempleado::where('id_persona', $id_emp)
            ->update(['id_puesto' => $puesto]);

        return redirect()->to('/CoordAC/personal/1');
    }

    public function f_deleteper($id_delete){

        // Mpersona::where('id_persona', $id_delete)
        //     ->update(['estado' => 0]);

        Mempleado::where('id_persona', $id_delete)
            ->update(['id_puesto' => 9]);

        return redirect()->to('/CoordAC/personal/1');
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

        return view('CoordAC.puesto.puestos')
        ->with('puestos', $puestos)
        ->with('tipos', $this->tipos());   
    }

    public function f_searchpue(Request $request) { 

        $search = $request->search;
        return redirect()->to('/CoordAC/puestos/'.$search);
        // return $this->f_puestos($search); 
    }

    public function f_n_puesto(){

        return view('CoordAC.puesto.nuevo')
        ->with('tipos', $this->tipos());
    }

    public function f_regPuesto(Request $request){

        $data = $request->all();

        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.',
            'string' => 'El campo :attribute no corresponde al tipo correcto.'
        ];

        $validation = \Validator::make($data, [
                'nombre' => 'required|string|min:3|max:100',
                'descripcion' => 'required|string|min:3|max:150'
            ], $messages);
        
        if ($validation->fails())  {
                return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        Mpuesto::create([
            'nombre' => mb_strtoupper($data['nombre']),
            'descripcion' => $data['descripcion'], 
            'estado' => 1
        ]);

        return redirect()->to('/CoordAC/puestos/0');
    }

    public function f_e_puesto($id_pue){

        $puesto = Mpuesto::where('id_puesto', $id_pue)->get();

        return view('CoordAC.puesto.editar')
        ->with('puesto', $puesto)
        ->with('tipos', $this->tipos()); 
    }

    public function f_editpuesto($id_pue, Request $request){

        $data = $request->all();

        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.',
            'string' => 'El campo :attribute no corresponde al tipo correcto.'
        ];

        $validation = \Validator::make($data, [
                'nombre' => 'required|string|min:3|max:100',
                'descripcion' => 'required|string|min:3|max:150'
            ], $messages);
        
        if ($validation->fails())  {
                return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        Mpuesto::where('id_puesto', $id_pue)
            ->update([
                'nombre' => mb_strtoupper($data['nombre']),
                'descripcion' => $data['descripcion']
            ]);

        return redirect()->to('/CoordAC/puestos/0');
    }

    public function f_deletepue($id_delete){

        Mpuesto::where('id_puesto', $id_delete)
            ->update(['estado' => 0]);

        return redirect()->to('/CoordAC/puestos/0');
    }

/*----------------------------------------------------------------------------------------------------*/

    public function f_r_usuarios($pagina) { 


        $personas = DB::table('persona as p')
            ->join('users as u', 'p.id_persona', '=', 'u.id_persona')
            ->select('p.id_persona',
                    'p.nombre',
                    'p.apePat',
                    'p.apeMat',
                    'u.usuario',
                    'p.tipo')
            ->where('p.estado', 1)
            ->orderBy('p.id_persona')
            ->paginate(10);

        return view('CoordAC.r_usuarios')
        ->with('persona', $personas)
        ->with('tipos', $this->tipos());   
    }

    public function f_r_usuariosB($search, $pagina) { 

        $search = mb_strtoupper("%".utf8_decode($search)."%");

        $personas = DB::table('persona as p')
            ->join('users as u', 'p.id_persona', '=', 'u.id_persona')
            ->join('estudiante as e', 'p.id_persona', '=', 'e.id_persona')
            ->select('p.id_persona',
                    'p.nombre',
                    'p.apePat',
                    'p.apeMat',
                    'u.usuario',
                    'p.tipo')
            ->when($search, function ($query, $search) {
                return $query->where('p.estado', 1)
                        ->where('p.nombre', 'LIKE', $search)
                        ->orWhere('p.apePat', 'LIKE', $search)
                        ->orWhere('p.apePat', 'LIKE', $search)
                        ->orWhere('p.curp', 'LIKE', $search)
                        ->orWhere('e.num_control', 'LIKE', $search)                        ;
            })
            ->orderBy('p.id_persona')
            ->paginate(10);

        return view('CoordAC.r_usuarios')
            ->with('persona', $personas)
            ->with('tipos', $this->tipos());   
    }

    public function f_searchusu(Request $request) { 

        $search = $request->search;
        return redirect()->to('/CoordAC/restUsuario/'.$search.'/1'); 
        // return $this->f_r_usuariosB($search, 1);
    }

    public function f_viewrestart($usuario){
       $cab = "";
        return view('CoordAC.restart')
            ->with('usuario', $usuario)
            ->with('tipos', $this->tipos());
    }

    public function f_restartuser($iduser) { 

        $type = Mpersona::where('id_persona', $iduser)
            ->first();

        if(strcmp($type->tipo, "Estudiante") === 0){
            $passwd = Mestudiante::select('num_control', 'email')
                ->where('id_persona', $iduser)
                ->first();
            
            $newpw = Hash::make($passwd->num_control);

            Musers::where('id_persona', $iduser)
                ->update(['usuario' => $passwd->email,
                        'password' => $newpw,
                        'edo_sesion' => 0]);

        }
        else{
            $passwd = Mpersona::select('curp')
                ->where('id_persona', $iduser)->first();
            
            $newpw = Hash::make($passwd->curp);

            Musers::where('id_persona', $iduser)
                ->update(['password' => $newpw,
                    'edo_sesion' => 0]);

        }

        return redirect()->to('/CoordAC/restUsuario/1'); 

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
        
        return view('CoordAC.suspencion.sus_labores')
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
        
        return view('CoordAC.suspencion.sus_labores')
            ->with('fechas', $fechas)
            ->with('tipos', $this->tipos()); 
    }

    public function f_searchslab(Request $request) { 

        $search = $request->search;
        return redirect()->to('/CoordAC/suspLabores/'.$search.'/1');
        // return $this->f_s_labor($search, 1); 
    }

    public function f_n_fecha(){

        return view('CoordAC.suspencion.nueva')
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
        
            return redirect()->to('/CoordAC/suspLabores/1');
        }
        elseif($data['fecha'] > $data['fecha_fin']) {
            ?>
                <script>
                    alert("La fecha de término no puede ser menor que la fecha de inicio.");
                    location.href = "CoordAC/nuevaFecha";
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
                    return redirect()->to('/CoordAC/suspLabores/1');
                }
            }
        }

    }

    public function f_deletefech($id_delete){

        Mfechas_inhabiles::where('id_fecha', $id_delete)
            ->update(['estado' => 0]);

        return redirect()->to('/CoordAC/suspLabores/1');
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

        return view('CoordAC.lugares.lugares')
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

        return view('CoordAC.lugares.lugares')
            ->with('lugares', $lugares)
            ->with('tipos', $this->tipos());   
    }

    public function f_searchlug(Request $request) { 

        $search = $request->search;
        return redirect()->to('/CoordAC/lugares/'.$search.'/1');
        // return $this->f_lugar($search, 1);   
    }

    public function f_n_lugar(){

        return view('CoordAC.lugares.nuevo')
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

        return redirect()->to('/CoordAC/lugares/1');
    }

    public function f_e_lugar($id_lug){

        $lugar = Mlugar::where('id_lugar', $id_lug)->get();

        return view('CoordAC.lugares.editar')
        ->with('lugar', $lugar)
        ->with('tipos', $this->tipos()); 
    }

    public function f_editlugar(Request $request){

        $data = $request->all();

        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.'
        ];

        $validation = \Validator::make($data, [
                'nombre' => 'required|string|min:3|max:100',
                'id_lugar' => 'required|exists:lugar,id_lugar'
            ], $messages);
        
        if ($validation->fails())  {
                return redirect()->back()->withInput()->withErrors($validation->errors());
        }

        Mlugar::where('id_lugar', $data['id_lugar'])
            ->update(['nombre' => $data['nombre']]);

        return redirect('CoordAC/lugares/1'); 
    }
    

    public function f_deletelug($id_delete){

        Mlugar::where('id_lugar', $id_delete)
            ->update(['estado' => 0]);

        return redirect()->to('/CoordAC/lugares/1');
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
            ->with('type', 13)
            ->with('dpts', $dpts)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_inscrip(Request $request){

        $dpt = $request->dpt;

        return redirect()->to('/CoordAC/inscripPA/'.$dpt.'/1'); 
    }

    public function f_inscripPA($dpt, $pagina){

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $inscripciones = DB::table('inscripcion as i')
            ->join('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
            ->join('persona as p', 'e.id_persona', '=', 'p.id_persona')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('periodo as pr', 'g.id_periodo', '=', 'pr.id_periodo')
            ->select('i.id_inscripcion', 
                    'e.num_control', 
                    'e.semestre', 
                    'g.clave as grupo', 
                    'a.nombre as actividad', 
                    'i.aprobada',
                    DB::raw('CONCAT(p.nombre, " ", p.apePat, " ", p.apeMat) as estudiante'))
            ->when($dpt, function ($query, $dpt) {
                return $query->where('i.aprobada', 0)
                            ->where('pr.estado', "Actual")
                            ->where('a.id_depto', $dpt);
            })
            ->orderBy('i.id_inscripcion')
            ->paginate(10);

        $dptn = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_depto', $dpt)->first();
        
        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();

        return view('CoordAC.inscripciones.inscrip')
            ->with('inscrip', $inscripciones)
            ->with('type', 0)
            ->with('dpts', $dpts)
            ->with('dptn', $dptn)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_inscripPAB($dpt, $pagina, $search){

        $now = date('Y-m-d');
        $modificar = true;
        $search = mb_strtoupper("%".utf8_decode($search)."%");
        $data = [$dpt, $search];

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $inscripciones = DB::table('inscripcion as i')
            ->leftJoin('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
            ->leftJoin('persona as p', 'e.id_persona', '=', 'e.id_persona')
            ->leftJoin('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->leftJoin('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->leftJoin('periodo as pr', 'g.id_periodo', '=', 'pr.id_periodo')
            ->select('i.id_inscripcion', 
                    'e.num_control', 
                    'e.semestre', 
                    'g.clave as grupo', 
                    'a.nombre as actividad', 
                    'i.aprobada', 
                    DB::raw('CONCAT(p.nombre, " ", p.apePat, " ", p.apeMat) AS estudiante'))
            ->when($data, function ($query, $data) {
                return $query->where('pr.estado', "Actual")
                            ->where('a.id_depto', $data[0])
                            ->where('e.num_control', 'LIKE', $data[1])
                            ->where('i.aprobada', 0);
            })
            ->orderBy('i.id_inscripcion')
            ->paginate(10);

        $dptn = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_depto', $dpt)->first();
        
        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();

        return view('CoordAC.inscripciones.inscrip')
            ->with('inscrip', $inscripciones)
            ->with('type', 0)
            ->with('dpts', $dpts)
            ->with('dptn', $dptn)
            ->with('mod', true)
            ->with('tipos', $this->tipos());
    }

    public function f_searchPA($dpt, Request $request) { 

        $search = $request->search;
        // return $this->f_inscripPA($dpt, 1, $search);   
        return redirect()->to('/CoordAC/inscripPA/'.$dpt.'/1/'.$search);
    }

    public function f_inscripA($dpt, $pagina){

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $inscripciones = DB::table('inscripcion as i')
            ->join('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
            ->join('persona as p', 'e.id_persona', '=', 'p.id_persona')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('periodo as pr', 'g.id_periodo', '=', 'pr.id_periodo')
            ->select('i.id_inscripcion', 
                    'e.num_control', 
                    'e.semestre', 
                    'g.clave as grupo', 
                    'a.nombre as actividad', 
                    'i.aprobada',
                    DB::raw('CONCAT(p.nombre, " ", p.apePat, " ", p.apeMat) as estudiante'))
            ->when($dpt, function ($query, $dpt) {
                return $query->where('i.aprobada', 1)
                            ->where('pr.estado', "Actual")
                            ->where('a.id_depto', $dpt);
            })
            ->orderBy('i.id_inscripcion')
            ->paginate(10);

        $dptn = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_depto', $dpt)->first();
        
        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();


        return view('CoordAC.inscripciones.inscrip')
            ->with('inscrip', $inscripciones)
            ->with('type', 1)
            ->with('dpts', $dpts)
            ->with('dptn', $dptn)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_inscripAB($dpt, $pagina, $search){

        $now = date('Y-m-d');
        $modificar = true;
        $search = mb_strtoupper("%".utf8_decode($search)."%");
        $data = [$dpt, $search];

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $inscripciones = DB::table('inscripcion as i')
            ->join('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
            ->join('persona as p', 'e.id_persona', '=', 'p.id_persona')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('periodo as pr', 'g.id_periodo', '=', 'pr.id_periodo')
            ->select('i.id_inscripcion', 
                    'e.num_control', 
                    'e.semestre', 
                    'g.clave as grupo', 
                    'a.nombre as actividad', 
                    'i.aprobada',
                    DB::raw('CONCAT(p.nombre, " ", p.apePat, " ", p.apeMat) as estudiante'))
            ->when($data, function ($query, $data) {
                return $query->where('i.aprobada', 1)
                            ->where('pr.estado', "Actual")
                            ->where('e.num_control', 'LIKE', $data[1])
                            ->where('a.id_depto', $data[0]);
            })
            ->orderBy('i.id_inscripcion')
            ->paginate(10);

        $dptn = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_depto', $dpt)->first();
        
        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();


        return view('CoordAC.inscripciones.inscrip')
            ->with('inscrip', $inscripciones)
            ->with('type', 1)
            ->with('dpts', $dpts)
            ->with('dptn', $dptn)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_searchA($dpt, Request $request) { 

        $search = $request->search;
        // return $this->f_inscripAB($dpt, 1, $search);   
        return redirect()->to('/CoordAC/inscripA/'.$dpt.'/1/'.$search);
        
    }

    public function f_inscripNA($dpt, $pagina){

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $inscripciones = DB::table('inscripcion as i')
            ->join('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
            ->join('persona as p', 'e.id_persona', '=', 'p.id_persona')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('periodo as pr', 'g.id_periodo', '=', 'pr.id_periodo')
            ->select('i.id_inscripcion', 
                    'e.num_control', 
                    'e.semestre', 
                    'g.clave as grupo', 
                    'a.nombre as actividad', 
                    'i.aprobada',
                    DB::raw('CONCAT(p.nombre, " ", p.apePat, " ", p.apeMat) as estudiante'))
            ->when($dpt, function ($query, $dpt) {
                return $query->where('i.aprobada', 2)
                            ->where('pr.estado', "Actual")
                            ->where('a.id_depto', $dpt);
            })
            ->orderBy('i.id_inscripcion')
            ->paginate(10);

        $dptn = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_depto', $dpt)->first();
        
        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();


        return view('CoordAC.inscripciones.inscrip')
            ->with('inscrip', $inscripciones)
            ->with('type', 2)
            ->with('dpts', $dpts)
            ->with('dptn', $dptn)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_inscripNAB($dpt, $pagina, $search){

        $now = date('Y-m-d');
        $modificar = true;
        $search = mb_strtoupper("%".utf8_decode($search)."%");
        $data = [$dpt, $search];

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $inscripciones = DB::table('inscripcion as i')
            ->join('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
            ->join('persona as p', 'e.id_persona', '=', 'p.id_persona')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('periodo as pr', 'g.id_periodo', '=', 'pr.id_periodo')
            ->select('i.id_inscripcion', 
                    'e.num_control', 
                    'e.semestre', 
                    'g.clave as grupo', 
                    'a.nombre as actividad', 
                    'i.aprobada',
                    DB::raw('CONCAT(p.nombre, " ", p.apePat, " ", p.apeMat) as estudiante'))
            ->when($data, function ($query, $data) {
                return $query->where('i.aprobada', 2)
                            ->where('pr.estado', "Actual")
                            ->where('e.num_control', 'LIKE', $data[1])
                            ->where('a.id_depto', $data[0]);
            })
            ->orderBy('i.id_inscripcion')
            ->paginate(10);

        $dptn = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_depto', $dpt)->first();
        
        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();


        return view('CoordAC.inscripciones.inscrip')
            ->with('inscrip', $inscrip)
            ->with('type', 2)
            ->with('dpts', $dpts)
            ->with('dptn', $dptn)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_searchNA($dpt, Request $request) { 

        $search = $request->search;
        // return $this->f_inscripNAB($dpt, 1, $search);   
        return redirect()->to('/CoordAC/inscripNA/'.$dpt.'/1/'.$search);
    }

    public function f_inscripBJ($dpt, $pagina){

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $inscripciones = DB::table('inscripcion as i')
            ->join('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
            ->join('persona as p', 'e.id_persona', '=', 'p.id_persona')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('periodo as pr', 'g.id_periodo', '=', 'pr.id_periodo')
            ->select('i.id_inscripcion', 
                    'e.num_control', 
                    'e.semestre', 
                    'g.clave as grupo', 
                    'a.nombre as actividad', 
                    'i.aprobada',
                    DB::raw('CONCAT(p.nombre, " ", p.apePat, " ", p.apeMat) as estudiante'))
            ->when($dpt, function ($query, $dpt) {
                return $query->where('i.aprobada', 3)
                            ->where('pr.estado', "Actual")
                            ->where('a.id_depto', $dpt);
            })
            ->orderBy('i.id_inscripcion')
            ->paginate(10);

        $dptn = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_depto', $dpt)->first();
        
        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();

        return view('CoordAC.inscripciones.inscrip')
            ->with('inscrip', $inscripciones)
            ->with('type', 3)
            ->with('dpts', $dpts)
            ->with('dptn', $dptn)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_inscripBJB($dpt, $pagina, $search){

        $now = date('Y-m-d');
        $modificar = true;
        $search = mb_strtoupper("%".utf8_decode($search)."%");
        $data = [$dpt, $search];

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")->first();
        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $inscripciones = DB::table('inscripcion as i')
            ->join('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
            ->join('persona as p', 'e.id_persona', '=', 'p.id_persona')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('periodo as pr', 'g.id_periodo', '=', 'pr.id_periodo')
            ->select('i.id_inscripcion', 
                    'e.num_control', 
                    'e.semestre', 
                    'g.clave as grupo', 
                    'a.nombre as actividad', 
                    'i.aprobada',
                    DB::raw('CONCAT(p.nombre, " ", p.apePat, " ", p.apeMat) as estudiante'))
            ->when($data, function ($query, $data) {
                return $query->where('i.aprobada', 3)
                            ->where('pr.estado', "Actual")
                            ->where('e.num_control', 'LIKE', $data[1])
                            ->where('a.id_depto', $data[0]);
            })
            ->orderBy('i.id_inscripcion')
            ->paginate(10);

        $dptn = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_depto', $dpt)->first();
        
        $dpts = Mdepartamento::select('id_depto', 'nombre')
            ->where('estado', 1)->get();


        return view('CoordAC.inscripciones.inscrip')
            ->with('inscrip', $inscripciones)
            ->with('type', 3)
            ->with('dpts', $dpts)
            ->with('dptn', $dptn)
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_searchBJ($dpt, Request $request) { 

        $search = $request->search;
        // return $this->f_inscripBJB($dpt, 1, $search);   
        return redirect()->to('/CoordAC/inscripBJ/'.$dpt.'/1/'.$search);
    }

    public function f_detInscrip($dpto, $id_ins){

        $estudiante = DB::table('inscripcion as i')
            ->join('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
            ->join('persona AS p', 'e.id_persona', '=', 'p.id_persona')
            ->join('carrera AS c', 'e.id_carrera', '=', 'c.id_carrera')
            ->select('e.id_estudiante', 
                    'e.num_control',
                    'p.nombre', 
                    'p.apePat', 
                    'p.apeMat', 
                    'c.nombre AS carrera', 
                    'e.semestre')
            ->where('i.id_inscripcion', $id_ins)
            ->get();

        $actividad = DB::table('inscripcion AS i')
            ->leftJoin('grupo AS g', 'i.id_grupo', '=', 'g.id_grupo')
            ->leftJoin('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->leftJoin('departamento AS d', 'a.id_depto', '=', 'd.id_depto')
            ->select( 
                    'g.clave as grupo',
                    'a.nombre as actividad',
                    'd.nombre as depto',
                    'i.aprobada',
                    'i.id_inscripcion',
                    'a.restringida')
            ->where('i.id_inscripcion', $id_ins)
            ->get();

        $horario = DB::table('inscripcion as i')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('horario as h', 'g.id_grupo', '=', 'h.id_grupo')
            ->join('dias_semana as ds', 'h.id_dia', '=', 'ds.id_dia')
            ->select('ds.id_dia',
                    'ds.nombre',
                    'h.hora_fin',
                    'h.hora_inicio')
            ->where('i.id_inscripcion', $id_ins)
            ->get();

        return view('CoordAC.inscripciones.detalle')
        ->with('estudiante', $estudiante)
        ->with('actividad', $actividad)
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

        $est = DB::table('inscripcion as i')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
            ->join('persona AS p', 'e.id_persona', '=', 'p.id_persona')
            ->select('g.clave', 
                    'a.nombre AS actividad',
                    'p.nombre', 
                    'p.apePat', 
                    'p.apeMat', 
                    'e.email')
            ->where('i.id_inscripcion', $id_ins)
            ->get();

        // $message = 
        // 'Hola, '.$est[0]->nombre.' '.$est[0]->apePat.' '.$est[0]->apeMat.' 

        // Te escribimos para notificarte que ha sido aprobada tu inscripción a la actividad 

        //         "'.$est[0]->clave.' - '.$est[0]->actividad.'"

        // si tienes algúna duda envíanos un correo electrónico a la 
        // siguiente dirección: altamirano.flv@gmail.com 
            
        //                         Attentamente 
            
        //         Coordinación de Actividades Complementarias 
        //             Tecnológco Nacional de MéxicoITO
        //             Instituto Tecnológico de Oaxaca';


        // $est = "".$est[0]->email;
        // $message = wordwrap($message, 80);

        // Mail::raw($message, function ($message) use ($est) {
            
        //     $message->to($est)
        //         ->subject('Inscripción Aprobada');
        //  });

        return redirect()->to('/CoordAC/inscripPA/'.$dpt.'/1');
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
        
        $est = DB::table('inscripcion as i')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
            ->join('persona AS p', 'e.id_persona', '=', 'p.id_persona')
            ->select('g.clave', 
                    'a.nombre AS actividad',
                    'p.nombre', 
                    'p.apePat', 
                    'p.apeMat', 
                    'e.email')
            ->where('i.id_inscripcion', $id_ins)
            ->get();

        //     $message = 
        // 'Hola, '.$est[0]->nombre.' '.$est[0]->apePat.' '.$est[0]->apeMat.' 

        // Te escribimos para notificarte que no ha sido aprobada tu inscripción a la actividad 

        //         "'.$est[0]->clave.' - '.$est[0]->actividad.'"

        // si tienes algúna duda envíanos un correo electrónico a la 
        // siguiente dirección: altamirano.flv@gmail.com 
            
        //                         Attentamente 
            
        //         Coordinación de Actividades Complementarias 
        //             Tecnológco Nacional de MéxicoITO
        //             Instituto Tecnológico de Oaxaca';

        
        // $est = "".$est[0]->email;
        // $message = wordwrap($message, 80);

        // Mail::raw($message, function ($message) use ($est) {
            
        //     $message->to($est)
        //         ->subject('Inscripción No Aprobada');
        // });

        return redirect()->to('/CoordAC/inscripPA/'.$dpt.'/1');
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
        
        $est = DB::table('inscripcion as i')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('estudiante as e', 'i.id_estudiante', '=', 'e.id_estudiante')
            ->join('persona AS p', 'e.id_persona', '=', 'p.id_persona')
            ->select('g.clave', 
                    'a.nombre AS actividad',
                    'p.nombre', 
                    'p.apePat', 
                    'p.apeMat', 
                    'e.email')
            ->where('i.id_inscripcion', $id_ins)
            ->get();

        //     $message = 
        // 'Hola, '.$est[0]->nombre.' '.$est[0]->apePat.' '.$est[0]->apeMat.' 
        // Te escribimos para notificarte que haz sido dado de baja de la actividad 

        //         "'.$est[0]->clave.' - '.$est[0]->actividad.'"

        // si tienes algúna duda envíanos un correo electrónico a la 
        // siguiente dirección: altamirano.flv@gmail.com 
                    
        //                         Attentamente 
                    
        //         Coordinación de Actividades Complementarias 
        //             Tecnológco Nacional de MéxicoITO
        //             Instituto Tecnológico de Oaxaca';
        

        // $est = "".$est[0]->email;
        // $message = wordwrap($message, 80);

        // Mail::raw($message, function ($message) use ($est) {
            
        //     $message->to($est)
        //         ->subject('Baja Actividad Complementaria');
        // });

        return redirect()->to('/CoordAC/inscripBJ/'.$dpt.'/1');
    }

/**Esta función se encarga de las inscripciones realizadas por la 
 * coordinación de actividades complementarias, es decir, cuando 
 * un estudiante ya se inscribió a una actividad y solicita una
 * segunda actividad.
 */
    public function f_inscribir($stdnt, $dpt){

        $student = DB::table('estudiante as e')
            ->join('persona AS p', 'e.id_persona', '=', 'p.id_persona')
            ->join('carrera AS c', 'e.id_carrera', '=', 'c.id_carrera')
            ->select('e.id_estudiante', 
                    'e.num_control',
                    'p.nombre', 
                    'p.apePat', 
                    'p.apeMat', 
                    'c.nombre AS carrera',
                    'c.id_depto', 
                    'e.semestre')
            ->where('e.id_persona', $stdnt)
            ->get();

        $deptos = Mdepartamento::where('estado', 1)->get();

        $grupos = DB::table('grupo as g')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('departamento as d', 'a.id_depto', '=', 'd.id_depto')
            ->join('periodo as pr', 'g.id_periodo', '=', 'pr.id_periodo')
            ->select('g.clave',
                    'a.nombre',
                    'a.creditos',
                    'd.nombre as depto',
                    'a.restringida',
                    'g.id_grupo',
                    'd.id_depto',
                    'g.cupo_libre')
            ->where('a.id_depto', $dpt)
            ->where('pr.estado', "Actual")
            ->where('g.estado', 1)
            ->get();

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

        $grupos = DB::table('grupo as g')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('departamento as d', 'a.id_depto', '=', 'd.id_depto')
            ->join('periodo as pr', 'g.id_periodo', '=', 'pr.id_periodo')
            ->select('g.clave',
                    'a.nombre',
                    'a.creditos',
                    'g.id_grupo')
            ->where('a.id_depto', $dpt)
            ->where('a.estado', 1)
            ->where('pr.estado', "Actual")
            ->get();

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
                $oficio = $request->file('oficio')->store('inscripciones');
                // $oficio = substr($oficio, 7); 
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

        $inscrito = DB::table('inscripcion as i')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->select('i.id_estudiante')
            // ->withCount('i.id_esdtudiante as n_inscrip')
            ->where('i.id_estudiante', $student)
            ->where('g.id_periodo', $peri->id_periodo)
            ->where('i.aprobada', '<>', 4)
            ->where('i.aprobada', '<>', 3)
            ->where('i.aprobada', '<>', 2)
            ->get();

        $inscrito = count($inscrito);

        if($inscrito >= 0 && $inscrito < 2){

            Minscripcion::create(['id_estudiante' => $student,
                'id_grupo' => $group,
                'fecha' => date('Y-m-d'),
                'aprobada' => 1]);

            Mgrupo::where('id_grupo')->update([
                    'cupo_libre' => $cupo
                ]);

            return redirect()->to('/CoordAC/estudiantes/1');
        }else{

            ?><script>
                alert('No se puede inscribir en más de dos actividades por semestre.');
                location.href = "/CoordAC/estudiantes/1";
            </script><?php
        }
    }

    public function logoutCAC(Request $request){

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect("IniciarSesion");
    }

    public function fpdf_imprimir($id_g){

        /**Si ya fueron impresos los horarios del grupo 
         * retorna un modal para confirmar la reimpresión 
         * */
        $impresos = Mhorarios_impresos::where('id_grupo', $id_g)->first();
            
        // if($impresos != null){
        //     return view('CoordAC.imp_horario',
        //         ['grupo' => $id_g,
        //             'tipos' => $this->tipos()]);
        // }

        $grupo = Minscripcion::select('id_estudiante')
            ->where('id_grupo', $id_g)
            ->groupBy('id_estudiante')
            ->get();

        $periodo_ = Mperiodo::select('nombre', 'inicio', 'cabecera')
            ->where('estado', "Actual")->first();

        $periodo_->cabecera = substr($periodo_->cabecera, 1);

        $fecha_hoy = date('d-m-Y'); 
        $nctrl = mb_strtoupper("número control: ");

        foreach ($grupo as $g){

            $schedule = DB::table('inscripcion as i')
                ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
                ->join('horario as h', 'g.id_grupo', '=', 'h.id_grupo')
                ->join('dias_semana as ds', 'h.id_dia', '=', 'ds.id_dia')
                ->select('ds.nombre',
                        'h.hora_fin',
                        'h.hora_inicio',
                        'h.id_grupo')
                ->where('i.id_estudiante', $g->id_estudiante)
                ->where('i.aprobada', 1)
                ->get();

            $student = DB::table('estudiante as e')
                ->join('persona AS p', 'e.id_persona', '=', 'p.id_persona')
                ->join('carrera AS c', 'e.id_carrera', '=', 'c.id_carrera')
                ->select('e.num_control',
                        'p.nombre', 
                        'p.apePat', 
                        'p.apeMat', 
                        'c.nombre AS carrera',
                        'e.semestre')
                ->where('e.id_estudiante', $g->id_estudiante)
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
                ->where('i.id_estudiante', $g->id_estudiante)
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
            // return$gru = $schedule[0]->id_grupo;
            foreach ($schedule as $c) {
                $contador += 11;
                $c->nombre = substr($c->nombre, 0, 4);
                $c->hora_inicio = substr($c->hora_inicio, 0, 5);
                $c->hora_fin = substr($c->hora_fin, 0, 5);

                if($c->id_grupo == $id_g){

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

        }



        /**Agregar a la tabla horarios_impresos el listado de f_estudiantes
         * pertenecientes al grupo del que se generó el horario
         */
        // foreach($estudiantes as $e){
        //     Mhorarios_impresos::create(['id_grupo' => $id_g, 
        //         'id_estudiante' => $e->id_estudiante]);
        // }

        $g_nombre = Mgrupo::select('clave')
            ->where('id_grupo', $id_g)->first();
        
        $tipo = 'I';
        $headers = ['Content-Type' => 'application/pdf'];
        $nombre_archivo = 'Horarios-'.$g_nombre->clave."-".$fecha_hoy.'.pdf';
    
        return Response::make(Fpdf::Output($tipo,$nombre_archivo), 200, $headers);
        // return response()->file(Fpdf::Output($tipo, $nombre_archivo));
    }

    public function re_imprimir_grupo($id_g)    {

        $grupo = Minscripcion::select('id_estudiante')
            ->where('id_grupo', $id_g)
            ->groupBy('id_estudiante')
            ->get();

        $periodo_ = Mperiodo::select('nombre', 'inicio', 'cabecera')
            ->where('estado', "Actual")->first();

        $periodo_->cabecera = substr($periodo_->cabecera, 1);

        $fecha_hoy = date('d-m-Y'); 
        $nctrl = mb_strtoupper("número control: ");

        foreach ($grupo as $g){

            $schedule = DB::table('inscripcion as i')
                ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
                ->join('horario as h', 'g.id_grupo', '=', 'h.id_grupo')
                ->join('dias_semana as ds', 'h.id_dia', '=', 'ds.id_dia')
                ->select('ds.nombre',
                        'h.hora_fin',
                        'h.hora_inicio',
                        'h.id_grupo')
                ->where('i.id_estudiante', $g->id_estudiante)
                ->where('i.aprobada', 1)
                ->get();

            $student = DB::table('estudiante as e')
                ->join('persona AS p', 'e.id_persona', '=', 'p.id_persona')
                ->join('carrera AS c', 'e.id_carrera', '=', 'c.id_carrera')
                ->select('e.num_control',
                        'p.nombre', 
                        'p.apePat', 
                        'p.apeMat', 
                        'c.nombre AS carrera',
                        'e.semestre')
                ->where('e.id_estudiante', $g->id_estudiante)
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
                ->where('i.id_estudiante', $g->id_estudiante)
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
            // return$gru = $schedule[0]->id_grupo;
            foreach ($schedule as $c) {
                $contador += 11;
                $c->nombre = substr($c->nombre, 0, 4);
                $c->hora_inicio = substr($c->hora_inicio, 0, 5);
                $c->hora_fin = substr($c->hora_fin, 0, 5);

                if($c->id_grupo == $id_g){

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

        }

         $g_nombre = Mgrupo::select('clave')
            ->where('id_grupo', $id_g)->first();
        
        $tipo = 'I';
        $headers = ['Content-Type' => 'application/pdf'];
        $nombre_archivo = 'Horarios-'.$g_nombre->clave."-".$fecha_hoy.'.pdf';
    
        return Response::make(Fpdf::Output($tipo, $nombre_archivo), 200, $headers);
    }

    public function f_horarioGrupos($dpt, $pagina){

        $now = date('Y-m-d');
        $modificar = true;

        $roll = Mperiodo::select('ini_inscripcion', 'fin_inscripcion')
            ->where('estado', "Actual")
            ->first();

        if($now < $roll->ini_inscripcion || $now > $roll->fin_inscripcion)
            $modificar = false;

        $data = [$dpt, "".$roll->ini_inscripcion.""];

        $grupos = DB::table('inscripcion as i')
            ->leftJoin('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->leftJoin('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->leftJoin('departamento as d', 'a.id_depto', '=', 'd.id_depto')
            ->select('g.id_grupo', 
                    'g.clave', 
                    'g.cupo', 
                    'a.nombre', 
                    DB::raw('COUNT(i.aprobada) as apro'), 
                    DB::raw('COUNT(i.aprobada) as noapro'))
            ->when($data, function ($query, $data) {
                return $query->where('i.aprobada',  1)
                            ->where('d.id_depto', $data[0])
                            ->where('i.fecha', '>=', $data[1]);
            })
            ->groupBy('g.id_grupo')
            ->orderBy('g.id_grupo')
            ->paginate(10);

        $inscripA = DB::table('inscripcion as i')
            ->join('grupo as g', 'i.id_grupo', '=', 'g.id_grupo')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('departamento as d', 'a.id_depto', '=', 'd.id_depto')
            ->select('g.id_grupo',
                DB::raw('COUNT(i.aprobada) as noapro'))
            ->where('d.id_depto', $dpt)
            ->where('i.fecha', '>=', $roll->ini_inscripcion)
            ->where('i.aprobada', 0)
            ->groupBy('g.id_grupo')
            ->get();
        
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
            ->with('mod', true)
            ->with('tipos', $this->tipos()); 
    }

    public function f_horario($id_ins){

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

        $data = $request->all();

        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.',
            'exists' => 'El campo :attribute no es un valor valido.',
        ];
        
        $validation = \Validator::make($request->all(), [
            'id_grado' => 'required|exists:grado,id_grado',
            'nombre' => 'required|min:3|max:30',
            'apePat' => 'required|min:3|max:20',
            'apeMat' => 'required|min:3|max:20',
            'curp' => 'required|unique:persona|size:18'
        ], $messages);      

        if ($validation->fails())  {
            return redirect()->back()->withInput()->withErrors($validation->errors());
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
            ->update(['id_grado' => $data['id_gradogrado']]);

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
