<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
use App\Models\Meval_valor;
use App\Models\Mcriterios_evaluacion;

class JDepartmentController extends Controller
{
    public function _construct() { $this->middleware('jefedepto');  }

    public function logs($action, $object, $user){

        Log::info($action, ['Object' => $object, 'User:' => $user]);
    }
    
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
        ->with('mod', true);  
    }

    public function f_deptoA($search, $pagina, Request $request) { 

        $user = $request->user()->id_persona;

        $data = [mb_strtoupper("%".$search."%"), $user];

        $depto = Mdepartamento::select('nombre')
            ->where('id_persona', $user)
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
            ->where('p.id_persona', $data[1])
            ->where('a.estado', 1)
            ->where('a.nombre', 'LIKE', $data[0])
            ->orWhere('a.clave', 'LIKE', $data[0])
            ->orderBy('a.id_actividad')
            ->paginate(10);

        return view('jDepto.actividad.actividades')
        ->with('actividades', $actividades)
        ->with('mod', true)
        ->with('pnom', $depto);  
    }
/**Realiza el request del buscador de actividades y redirige a la función
 * f_deptoA()
 */
    public function f_searchact(Request $request) { 

        $search = $request->search;
        return redirect()->to('JDepto/actividad/'.$search.'/1');  
        // return $this->f_deptoA($search, 1, $per); 
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

        $data = $request->all();
        
        $messages = [
            'required' => 'El campo :attribute es requierido.',
            'min' => 'El campo :attribute debe contener minimo 3 caracteres.',
            'max' => 'El campo :attribute se excede en longitud.',
            'unique' => 'El campo :attribute ya se ha registrado.',
            'email' => 'El dominio valido para el e-mail es: @itoaxaca.edu.mx'
        ];
      
        $validation = \Validator::make($request->all(), [
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
                'id_depto' => $depto->id_depto, 
                'id_tipo' => $data['id_tipo'],
                'id_periodo' => $periodo->id_periodo, 
                'clave' => mb_strtoupper($data['clave']), 
                'nombre' => mb_strtoupper($data['nombre']),
                'creditos' => $data['creditos'], 
                'descripcion' => mb_strtoupper($data['descripcion']), 
                'restringida' => $data['restringida'], 
                'estado' => 1]);

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
                    'a.nombre', 
                    'a.creditos',
                    'a.id_tipo',
                    't.nombre as tipo',
                    'a.descripcion', 
                    'a.restringida')
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
                'id_tipo' => 'required|exists:tipo,id_tipo',
                'nombre' => 'required|min:3|max:100',
                'descripcion' => 'nullable|max:250',
                'restringida' => 'required|numeric'
            ], $messages);  
        } else {

            $validation = \Validator::make($request->all(), [
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
return $data;
        Mactividad::where('id_actividad', $id_act)
            ->update([
                'id_tipo' => $data['id_tipo'],
                'clave' => mb_strtoupper($data['clave']), 
                'nombre' => mb_strtoupper($data['nombre']),
                'descripcion' => mb_strtoupper($data['descripcion']),
                'restringida' => $data['restringida']
            ]);

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

        $data = [mb_strtoupper("%".utf8_decode($search)."%"), $request->user()->id_persona];

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
        // return $this->f_grupo($search, 1);
        return redirect()->to('JDepto/grupo/'.$search.'/1');   
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
            
            $depto = Mdepartamento::where('id_persona', $id_per)
                ->first();

            $persona = DB::table('persona as p')
                ->join('empleado as e', 'p.id_persona', '=', 'e.id_persona')
                ->join('grado as g', 'e.id_grado', '=', 'g.id_grado')
                ->select('p.id_persona',
                        'g.nombre AS grado',
                        'p.nombre',
                        'p.apePat',
                        'p.apeMat')
                ->where('e.id_depto', $depto->id_depto)
                ->get();

            $lugar = Mlugar::get();

            return view('jDepto.grupo.nuevo')
            ->with('periodo', $periodo)
            ->with('actividades', $actividad)
            ->with('personas', $persona)
            ->with('depto', $depto)
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
        return $data;

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
                ->select('a.id_actividad as id_actividad', 
                        'a.clave',
                        'a.nombre', 
                        'a.creditos',
                        'd.nombre as depto', 
                        't.nombre as tipo',
                        'a.descripcion')
                ->where('per.id_persona', $id_per)
                ->where('p.estado', "Actual")
                ->groupby('clave', 'id_actividad', 'nombre', 'creditos', 'depto', 't.nombre', 'descripcion')
                ->get();    

            $persona = DB::table('persona as p')
                ->join('empleado as e', 'p.id_persona', '=', 'e.id_persona')
                ->join('grado as g', 'e.id_grado', '=', 'g.id_grado')
                ->select('p.id_persona', 
                        'p.nombre', 
                        'p.apePat', 
                        'p.apeMat',
                        'g.nombre as grado')
                ->get();

            $lugar = Mlugar::get();

            $grupo = DB::table('grupo as g')
            ->join('periodo as p', 'g.id_periodo', '=', 'p.id_periodo')
            ->join('actividad as a', 'g.id_actividad', '=', 'a.id_actividad')
            ->join('persona as pe', 'g.id_persona', '=', 'pe.id_persona')
            ->join('lugar as l', 'g.id_lugar', '=', 'l.id_lugar')
            ->select('g.id_grupo', 
                    'g.cupo', 
                    'g.clave',
                    'g.asistencias', 
                    'p.nombre as periodo',
                    'g.id_periodo', 
                    'g.id_actividad',
                    'g.id_persona', 
                    'g.id_lugar',
                    'a.nombre as actividad',
                    'a.creditos', 
                    'a.clave as aClave', 
                    'pe.nombre as nomP', 
                    'pe.apePat as paterno',
                    'pe.apeMat as materno', 
                    'l.nombre as lugar', 
                    'g.orden')
            ->where('id_grupo', $id_gru)
            ->get();

            $depto = Mdepartamento::where('id_persona', $id_per)
                ->first();

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

            return view('jDepto.grupo.editar')->with('grupo', $grupo)
                    ->with('periodo', $periodo)
                    ->with('actividades', $actividad)
                    ->with('personas', $persona)
                    ->with('lugares', $lugar)
                    ->with('depto', $depto)
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

        $old_data = Mgrupo::select('clave', 'cupo', 'cupo_libre')
            ->where('id_grupo', $id_gru)
            ->first();

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

    public function f_personalB($search, $pagina, Request $request) {

        $id_per = $request->user()->id_persona;

        $depto = Mdepartamento::select('id_depto', 'nombre')
            ->where('id_persona', $id_per)
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
                            ->orWhere('p.apeMat', 'LIKE', $data[0]);
            })
            ->orderBy('p.id_persona')
            ->paginate(10);

        return view('jDepto.persona.personas')
            ->with('personas', $empleados)
            ->with('pnom', $depto);   
    }

    public function f_searchper(Request $request) { 

        $search = $request->search;
        // return $this->f_personalB($search, 1, $request->user()->id_persona);
        return redirect()->to('JDepto/personal/'.$search.'/1');   
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

        $depto = Mdepartamento::select('id_depto')
            ->where('id_persona', $request->user()->id_persona)
            ->first();

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
                    'curp' => 'nullable|unique:persona|size:18',
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
                'id_depto' => $depto->id_depto, 
                'id_grado' => $data['id_grado'], 
                'id_puesto' => 3
            ]);
    
            Musers::create([
                'id_persona' => $persona->id, 
                'id_puesto' => 3,
                'nombre' => $nomUser, 
                'usuario' => mb_strtoupper($data['curp']), 
                'password' => $contraseña,
                'fecha_registro' => $hoy, 
                'edo_sesion' => 0, 
                'estado' => 1]);


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

        $data = $request->all();
        $curp = Mpersona::where('id_persona', $id_emp)
            ->first();

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
                'id_grado' => $data['id_grado']
            ]);

        Musers::where('id_persona', $id_emp)
            ->update([
                'id_puesto' => $data['id_puesto'], 
                'nombre' => $nomUser, 
                'usuario' => $data['curp']
            ]);

        return redirect()->to('JDepto/personal/1');
    }

    public function f_h_mem(Request $request){

        $id_per = $request->user()->id_persona;

        $hoja = DB::table('departamento as d')
            ->join('empleado as e', 'd.id_depto', '=', 'e.id_depto')
            ->select('d.hoja_mem')
            ->where('e.id_persona', $id_per)
            ->get();

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
            ->orderBy('g.id_grupo')
            ->paginate(10);

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
            ->orderBy('p.nombre')
            ->paginate(10);

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

        $criterios =  Mcriterios_evaluacion::get();
        
        $calificacionCrit = Meval_valor::where('id_evaluacion', $data->id_evaluacion)
                            ->get();

        
        $now = date_create('America/Mexico_City');
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('ProfRes.criteriosEvaluacion',
                    array('data' => $data, 'criterios'=>$criterios,
                         'calificacion'=>$calificacionCrit));

        if(!count($calificacionCrit)){
            ?><script>
                alert('Estudiante no evaluado.');
                location.href = "JDepto/lista_alumnos291/criterio";
            </script><?php
        } else {

            return $pdf->download('criterio.pdf');  
        }
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
