<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

use App\Models\Musers;
use App\Models\Mgrado;
use App\Models\Mpuesto;
use App\Models\Mperiodo;
use App\Models\Mdepartamento;
use DB;

class CAcademicController extends Controller
{
    public function _construct() { $this->middleware('coordinador');  }
/**Retorna la vista de inicio de la sesión, con un saludo y el proceso que
 * se está llevando a cabo en el sistema (Inscripción, Evalucación, 
 * Generación de constancias)
 */
    public function f_inicio() { 
        
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

        return view('coordC.inicio')
        ->with('hora', $now)
        ->with('process', $processes)
        ->with('end', $endprocess);  
    }
/**Realiza el request del buscador de estudiante y envia el resultado a la
 * función searchEst().
 */
    public function search(){

        return view('coordC.searchest')
            ->with('search', 0);
    }
/**Realiza la busqueda del estudiante y retorna la vista de historial con los
 * datos del estudiante encontrado
 */
    public function searchEst(Request $request){

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

        return view('coordC.searchest')
            ->with('search', 1)
            ->with('student', $student)
            ->with('inscripcion', $inscription);
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

        return view('coordC.perfil')
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

        return view('coordC.editperfil')
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

        return redirect()->to('/CoordC/datosGen');
    }
/**Retorna a la vista para cambiar la contraseña */
    public function f_passwd(){

        return view('coordC.editpasswd');
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
                    location.href = "/CoordC/editpasswd";
                 </script>
              <?php
           }
  
        }else{
           ?>
              <script>
                  alert('Contraseña actual incorrecta, intenta de nuevo.');
                  location.href = "/CoordC/editpasswd";
              </script>
          <?php
        }
    }
/**Destruye la sesión, y redirige a la vista de inicio de sesión */
    public function logoutCA(Request $request){

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect("IniciarSesion");
    }
}
