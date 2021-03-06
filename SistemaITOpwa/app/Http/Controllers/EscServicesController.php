<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


use App\Models\Mgrado;
use App\Models\Musers;
use App\Models\Mpuesto;
use App\Models\Mperiodo;
use App\Models\Mevaluacion;
use App\Models\Minscripcion;
use App\Models\Mdepartamento;
use DB;

class EscServicesController extends Controller
{
    public function _construct() { $this->middleware('escolares');  }
/**Retorna la vista de inicio de la sesión, con un saludo y el proceso que
 * se está llevando a cabo en el sistema (Inscripción, Evalucación, 
 * Generación de constancias)
 */

    public function logs($action, $object, $user){

        Log::info($action, ['Object' => $object, 'User:' => $user]);
    }

    public function f_inicio() { 
        
        try {
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

            return view('servesc.inicio')
            ->with('hora', $now)
            ->with('process', $processes)
            ->with('end', $endprocess);

        } catch (\Exception $e) { 
            $error = 'Ocurrió un problema interno en el sistema, estamos trabajando para solucionarlo, sentimos las molestias.';
            return redirect()->back()->with('Catch', $error); 
        }
    }
/**Realiza el request del buscador de estudiante y envia el resultado a la
 * función searchEst().
 */
    public function f_search(){

        try {
            return view('servesc.searchest')
                ->with('search', 0);

        } catch (\Exception $e) { 
            $error = 'Ocurrió un problema interno en el sistema, estamos trabajando para solucionarlo, sentimos las molestias.';
            return redirect()->back()->with('Catch', $error); 
        }
    }
/**Realiza la busqueda del estudiante y retorna la vista de historial con los
 * datos del estudiante encontrado
 */
    public function f_searchEst(Request $request){

        try {
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

            return view('servesc.searchest')
                ->with('search', 1)
                ->with('student', $student)
                ->with('inscripcion', $inscription);

        } catch (\Exception $e) { 
            $error = 'Ocurrió un problema interno en el sistema, estamos trabajando para solucionarlo, sentimos las molestias.';
            return redirect()->back()->with('Catch', $error); 
        }
    }
/**Esta función se encarga de registrar la constancia de acreditación de 
 * actividad complementaria en el sistema, para que al momento de vizualizar
 * el historial de un estudiante, también se pueda ver dicha constancia
 */
    public function f_saveproof($ideval, Request $request){

        try { 
            if($request->hasFile('constancia')){
                $proof = $request->file('constancia')->store('constancias');

                Mevaluacion::where('id_evaluacion', $ideval)
                    ->update(['constancia' => $proof]);
            }

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
                ->when($ideval, function ($query, $ideval) {
                    return $query->where('ev.id_evaluacion', $ideval);
                })
                ->groupBy('pr.id_periodo')
                ->orderBy('pr.id_periodo');

            $student = DB::table('estudiante as e')
                ->leftJoin('persona as p', 'e.id_persona', '=', 'p.id_persona')
                ->leftJoin('carrera as c', 'e.id_carrera', '=', 'c.id_carrera')
                ->leftJoin('inscripcion as i', 'e.id_estudiante', '=', 'i.id_estudiante')
                ->leftJoin('evaluacion as ev', 'i.id_inscripcion', '=', 'ev.id_inscripcion')
                ->select('p.nombre', 
                        'p.apePat', 
                        'p.apeMat', 
                        'e.num_control', 
                        'e.semestre', 
                        'c.nombre AS carrera')
                ->when($ideval, function ($query, $ideval) {
                    return $query->where('ev.id_evaluacion', $ideval);
                });
            

            //$student[0]->num_control;
            return redirect('ServEsc/searchest?search='.$student[0]->num_control);
            // return view('servesc.searchest')
            //     ->with('search', 1)
            //     ->with('student', $student)
            //     ->with('inscripcion', $inscription);

        } catch (\Exception $e) { 
            $error = 'Ocurrió un problema interno en el sistema, estamos trabajando para solucionarlo, sentimos las molestias.';
            return redirect()->back()->with('Catch', $error); 
        }
    }
/**Retorna a la vista del perfil de usuario, donde se muestran los datos 
 * generales y las opciones a edición de los mismos como de la contraseña
 */
    public function f_perfil(Request $request){

        try {
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

        } catch (\Exception $e) { 
            $error = 'Ocurrió un problema interno en el sistema, estamos trabajando para solucionarlo, sentimos las molestias.';
            return redirect()->back()->with('Catch', $error); 
        }
    }

/**Retorna a la vista de edición de los datos generales del usuario */
    public function f_editperfil(Request $request){

        try {
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

        } catch (\Exception $e) { 
            $error = 'Ocurrió un problema interno en el sistema, estamos trabajando para solucionarlo, sentimos las molestias.';
            return redirect()->back()->with('Catch', $error); 
        }
    }
/**Realiza el request de los datos cambiados para actualizar el contenido
 * en la base de datos y retorna a la vista del perfil de usuairo
 */
    public function f_editar(Request $request){

        try {
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

        } catch (\Exception $e) { 
            $error = 'Ocurrió un problema interno en el sistema, estamos trabajando para solucionarlo, sentimos las molestias.';
            return redirect()->back()->with('Catch', $error); 
        }
    }

/**Retorna a la vista para cambiar la contraseña */
    public function f_passwd(){

        try {
            return view('servesc.editpasswd');

        } catch (\Exception $e) { 
            $error = 'Ocurrió un problema interno en el sistema, estamos trabajando para solucionarlo, sentimos las molestias.';
            return redirect()->back()->with('Catch', $error); 
        }
    }
/**Realiza el request de la nueva contraseña. Para realizar un cambio de contraseña
 * coteja que:
 * 1.- La nueva contraseña no sea igual a la contraseña actual
 * 2.- Sea de tamaño >= 8 && <= 16
 */
    public function f_edpasswd(Request $request){

        try {
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

        } catch (\Exception $e) { 
            $error = 'Ocurrió un problema interno en el sistema, estamos trabajando para solucionarlo, sentimos las molestias.';
            return redirect()->back()->with('Catch', $error); 
        }
    }

/**Destruye la sesión, y redirige a la vista de inicio de sesión */
    public function logoutSE(Request $request){

        try {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        
            return redirect()->to('/IniciarSesion');
        } catch (\Exception $e) { 
            $error = 'Ocurrió un problema interno en el sistema, estamos trabajando para solucionarlo, sentimos las molestias.';
            return redirect()->back()->with('Catch', $error); 
        }
    }
}
