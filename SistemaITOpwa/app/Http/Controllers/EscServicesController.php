<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


use DB;
use App\Models\Mgrado;
use App\Models\Mpuesto;
use App\Models\Mperiodo;
use App\Models\Mevaluacion;
use App\Models\Minscripcion;
use App\Models\Mdepartamento;

class EscServicesController extends Controller
{
    public function _construct() { $this->middleware('auth');  }
/**Retorna la vista de inicio de la sesión, con un saludo y el proceso que
 * se está llevando a cabo en el sistema (Inscripción, Evalucación, 
 * Generación de constancias)
 */
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

        return view('servesc.inicio')
        ->with('hora', $now)
        ->with('process', $processes)
        ->with('end', $endprocess);
    }
/**Realiza el request del buscador de estudiante y envia el resultado a la
 * función searchEst().
 */
    public function f_search(){

        return view('servesc.searchest')
            ->with('search', 0);
    }
/**Realiza la busqueda del estudiante y retorna la vista de historial con los
 * datos del estudiante encontrado
 */
    public function f_searchEst(Request $request){

        $control = $request->search;
        
        $inscription = DB::select('SELECT pr.nombre AS periodo, i.fecha,
            a.nombre AS actividad, ev.constancia, ev.id_evaluacion, 
            g.clave, i.aprobada, pr.id_periodo
        FROM inscripcion AS i 
        LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante 
        LEFT JOIN evaluacion AS ev ON i.id_inscripcion = ev.id_inscripcion        
        LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
        LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
        LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
        WHERE e.num_control = '.'"'.$control.'"
        GROUP BY pr.id_periodo, pr.nombre, i.fecha,
        a.nombre, ev.constancia, ev.id_evaluacion, 
        g.clave, i.aprobada
        ORDER BY pr.id_periodo ASC');

        $student = DB::select('SELECT p.nombre, p.apePat, p.apeMat, 
            e.num_control, e.semestre, c.nombre AS carrera
        FROM estudiante AS e
        LEFT JOIN persona AS p ON e.id_persona = p.id_persona 
        LEFT JOIN carrera AS c ON e.id_carrera = c.id_carrera 
        WHERE e.num_control = '.'"'.$control.'"');

        $cons;
        foreach($inscription as $i){
            $cons = $i->constancia;
        }

        return view('servesc.searchest')
            ->with('search', 1)
            ->with('student', $student)
            ->with('inscripcion', $inscription);
    }
/**Esta función se encarga de registrar la constancia de acreditación de 
 * actividad complementaria en el sistema, para que al momento de vizualizar
 * el historial de un estudiante, también se pueda ver dicha constancia
 */
    public function f_saveproof($ideval, Request $request){

        if($request->hasFile('constancia')){
            $proof = $request->file('constancia')->store('public/constancias');
            $proof = substr($proof, 7); 

            Mevaluacion::where('id_evaluacion', $ideval)
                ->update(['constancia' => $proof]);
        }

        $inscription = DB::select('SELECT pr.nombre AS periodo, i.fecha,
            a.nombre AS actividad, ev.constancia, ev.id_evaluacion, 
            g.clave, i.aprobada, pr.id_periodo
        FROM inscripcion AS i 
        LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante 
        LEFT JOIN evaluacion AS ev ON i.id_inscripcion = ev.id_inscripcion        
        LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
        LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
        LEFT JOIN periodo AS pr ON g.id_periodo = pr.id_periodo
        WHERE ev.id_evaluacion = '.$ideval.'
        GROUP BY pr.id_periodo, pr.nombre, i.fecha,
        a.nombre, ev.constancia, ev.id_evaluacion, 
        g.clave, i.aprobada
        ORDER BY pr.id_periodo ASC');

        $student = DB::select('SELECT p.nombre, p.apePat, p.apeMat, 
            e.num_control, e.semestre, c.nombre AS carrera
        FROM estudiante AS e
        LEFT JOIN persona AS p ON e.id_persona = p.id_persona 
        LEFT JOIN carrera AS c ON e.id_carrera = c.id_carrera 
        LEFT JOIN inscripcion AS i ON e.id_estudiante = i.id_estudiante
        LEFT JOIN evaluacion AS ev ON i.id_inscripcion = ev.id_inscripcion
        WHERE ev.id_evaluacion = '.$ideval);

        

        //$student[0]->num_control;
        return redirect('ServEsc/searchest?search='.$student[0]->num_control);
        // return view('servesc.searchest')
        //     ->with('search', 1)
        //     ->with('student', $student)
        //     ->with('inscripcion', $inscription);
    }
/**Retorna a la vista del perfil de usuario, donde se muestran los datos 
 * generales y las opciones a edición de los mismos como de la contraseña
 */
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

        return view('servesc.perfil')
            ->with('persona', $persona)
            ->with('departamentos', $deptos)
            ->with('puestos', $puesto)
            ->with('grados', $grados);
    }

/**Retorna a la vista de edición de los datos generales del usuario */
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

        return view('servesc.editperfil')
            ->with('persona', $persona)
            ->with('departamentos', $deptos)
            ->with('puestos', $puesto)
            ->with('grados', $grados);
    }
/**Realiza el request de los datos cambiados para actualizar el contenido
 * en la base de datos y retorna a la vista del perfil de usuairo
 */
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

        return redirect()->to('/ServEsc/datosGen');
    }

/**Retorna a la vista para cambiar la contraseña */
    public function f_passwd(){

        return view('servesc.editpasswd');
    }
/**Realiza el request de la nueva contraseña. Para realizar un cambio de contraseña
 * coteja que:
 * 1.- La nueva contraseña no sea igual a la contraseña actual
 * 2.- Sea de tamaño >= 8 && <= 16
 */
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
  
              ?><script>
                     alert('Contraseña actualizada satisfactoriamente.');
                 </script><?php
              
              $request->session()->invalidate();
              $request->session()->regenerateToken();
              
              ?><script>
                    location.href = "/IniciarSesion";
                 </script><?php
  
           }else{
              ?><script>
                    alert('Las nuevas contraseñas no coinciden, intenta de nuevo.');
                    location.href = "/ServEsc/editpasswd";
                 </script><?php
           }
  
        }else{
           ?><script>
                  alert('Contraseña actual incorrecta, intenta de nuevo.');
                  location.href = "/ServEsc/editpasswd";
              </script><?php
        }
    }

/**Destruye la sesión, y redirige a la vista de inicio de sesión */
    public function logoutSE(Request $request){

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect()->to('/IniciarSesion');
    }
}
