<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\Mcarrera;
use App\Models\Musers;
use App\Models\Mestudiante;
use App\Models\Mpersona;
use Auth;

class PublicController extends Controller
{
    public function _construct() {  
        //$this->middleware('guest');
     }

    public function inicio() { return view('presentacionAC.noticias');  }
/**Retorna a la vista de inicio de sesión */
    public function iniSesion() { return view('presentacionAC.login');  }

/**Retorna a la vista del formulario donde los estudiantes se 
 * pueden registrar en el sistema
 */
    public function registro() { 

        $carreras = Mcarrera::select('id_carrera', 'nombre')->get();

        $semestres = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];

        return view('presentacionAC.registro', ['carreras' => $carreras, 'semestres' => $semestres]);
    }

/**Realiza el request del formalario del registro, valida los
 * datos y crea los registros correspondientes
 */
    public function nuevo_registro(Request $request)  {

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

        if( substr_compare($email, "@itoaxaca.edu.mx", -16) == 0 ){

            $existe = false;
            $estudiantes = Mestudiante::select('num_control', 'email')->get();
            foreach($estudiantes as $e){
                if($e->num_control == $nControl || $e->email == $email)
                    $existe = true;
            }

            if($existe){
                ?><script>
                    alert("Ya estas registrado en el sistema");
                    location.href = "/IniciarSesion";
                </script><?php
            }else{
                $persona = Mpersona::create(['nombre' => $nombre, 'apePat' => $apePat,
                'apeMat' => $apeMat, 'curp' => $curp, 'tipo' => "Estudiante", 'estado' => 1]);

                Mestudiante::create(['id_persona' => $persona->id, 'id_carrera' => $carrera, 
                'num_control' => $nControl, 'email' => $email, 'semestre' => $semestre]);

                Musers::create(['id_persona' => $persona->id, 'id_puesto' => 6,
                'nombre' => $nomUser, 'usuario' => $email, 'password' => $contraseña,
                'fecha_registro' => $hoy, 'edo_sesion' => 0, 'estado' => 1]);

                return redirect()->to('IniciarSesion');
            }
        }else{
            ?><script>
                alert("Correo incorrecto, debes usar tu correo institucional.");
                location.href = "/Registrarse";
            </script><?php
        }  
    }

/**Esta función de encarga de los inicios de sesión, trabaja en
 * diferentes filtros, en el siguiente orden:
 * 1.- Valida usuario y contraseña
 * 2.- Verifica el tipo de usuario
 * 3.- Verifica que sea un usuario vigente
 * 4.- Si es el primer inico de sesión lo redirige a cambiar la
 * contraseña, si no lo enia a su inicio correspondiente.
 */
    public function authenticate(Request $request)    {
        $usuario = $request->usuario;
        $password = $request->password;

        if (Auth::attempt(['id_puesto' => 6, 'usuario' => $usuario, 'password' => $password, 'edo_sesion' => 1, 'estado' => 1])) {
            $request->session()->regenerate();
            return redirect()->intended('Est');
        }elseif (Auth::attempt(['id_puesto' => 6, 'usuario' => $usuario, 'password' => $password, 'edo_sesion' => 0, 'estado' => 1])) {
            $request->session()->regenerate();
            return redirect()->intended('CambiarConstrasenia');
        }
        else if (Auth::attempt(['id_puesto' => 7, 'usuario' => $usuario, 'password' => $password, 'edo_sesion' => 1, 'estado' => 1])) {
            $request->session()->regenerate();
            return redirect()->intended('CoordAC');
        }elseif (Auth::attempt(['id_puesto' => 7, 'usuario' => $usuario, 'password' => $password, 'edo_sesion' => 0, 'estado' => 1])) {
            $request->session()->regenerate();
            return redirect()->intended('CambiarConstrasenia');
        }
        else if (Auth::attempt(['id_puesto' => 2, 'usuario' => $usuario, 'password' => $password, 'edo_sesion' => 1, 'estado' => 1])) {
            $request->session()->regenerate();
            return redirect()->intended('JDepto');
        }elseif (Auth::attempt(['id_puesto' => 2, 'usuario' => $usuario, 'password' => $password, 'edo_sesion' => 0, 'estado' => 1])) {
            $request->session()->regenerate();
            return redirect()->intended('CambiarConstrasenia');
        }
        else if (Auth::attempt(['id_puesto' => 3, 'usuario' => $usuario, 'password' => $password, 'edo_sesion' => 1, 'estado' => 1])) {
            $request->session()->regenerate();
            return redirect()->intended('ProfR');
        }elseif (Auth::attempt(['id_puesto' => 3, 'usuario' => $usuario, 'password' => $password, 'edo_sesion' => 0, 'estado' => 1])) {
            $request->session()->regenerate();
            return redirect()->intended('CambiarConstrasenia');
        }
        else if (Auth::attempt(['id_puesto' => 4, 'usuario' => $usuario, 'password' => $password, 'edo_sesion' => 1, 'estado' => 1])) {
            $request->session()->regenerate();
            return redirect()->intended('CoordC');
        }elseif (Auth::attempt(['id_puesto' => 4, 'usuario' => $usuario, 'password' => $password, 'edo_sesion' => 0, 'estado' => 1])) {
            $request->session()->regenerate();
            return redirect()->intended('CambiarConstrasenia');
        }
        else if (Auth::attempt(['id_puesto' => 5, 'usuario' => $usuario, 'password' => $password, 'edo_sesion' => 1, 'estado' => 1])) {
            $request->session()->regenerate();
            return redirect()->intended('ServEsc');
        }elseif (Auth::attempt(['id_puesto' => 5, 'usuario' => $usuario, 'password' => $password, 'edo_sesion' => 0, 'estado' => 1])) {
            $request->session()->regenerate();
            return redirect()->intended('CambiarConstrasenia');
        }
        else if (Auth::attempt(['id_puesto' => 1, 'usuario' => $usuario, 'password' => $password, 'edo_sesion' => 1, 'estado' => 1])) {
            $request->session()->regenerate();
            return redirect()->intended('DivEProf');
        }elseif (Auth::attempt(['id_puesto' => 1, 'usuario' => $usuario, 'password' => $password, 'edo_sesion' => 0, 'estado' => 1])) {
            $request->session()->regenerate();
            return redirect()->intended('CambiarConstrasenia');
        }else if (Auth::attempt(['id_puesto' => 8, 'usuario' => $usuario, 'password' => $password, 'edo_sesion' => 1, 'estado' => 1])) {
            $request->session()->regenerate();
            return redirect()->intended('SeleccionarSesion');
        }elseif (Auth::attempt(['id_puesto' => 8, 'usuario' => $usuario, 'password' => $password, 'edo_sesion' => 0, 'estado' => 1])) {
            $request->session()->regenerate();
            return redirect()->intended('CambiarConstrasenia');
        }else{
            ?>
                <script>
                    alert("Eror: Usuario  y/o contraseña incorrectos, intente de nuevo.");
                    location.href = "IniciarSesion";
                </script>
            <?php
        }
        
    }
    
/**Esta función es para los usuarios que cuentan con diferentes roles, 
 * los envia a una vista donde les permite escoger el usuario con el
 * que quieren ingresar al sistema
 */
    public function selectSesion(){
        return view('presentacionAC.selectuser');
    }

    public function change(){
        return view('presentacionAC.editpasswd');
    }

    public function changepasswd(Request $request){

        $user = $request->user()->id_persona;
        //return $user;

        $newpswd = $request->passwd;
        $conpswd = $request->pswdconfirm;

        if (Hash::check($newpswd, $request->user()->password))
        {
            ?><script>                
                alert('Tu contraseña debe ser diferente a la actual.');
                location.href = "/CambiarConstrasenia";
            </script><?php

        }else{
        
            if($newpswd == $conpswd){
                $updt = Hash::make($newpswd);
                Musers::where('id_persona', $user)
                ->update(['password' => $updt, 'edo_sesion' => 1]);

                ?>
                <script>
                    alert('Contraseña actualizada satisfactoriamente.');
                </script>
                <?php
                
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                //return redirect("IniciarSesion");
                
                ?>
                <script>
                    location.href = "/IniciarSesion";
                </script>
                <?php
                
                //return redirect()->to("IniciarSesion");

            }else{
                ?>
                <script>
                    alert('Las contraseñas no coinciden');
                    location.href = "/CambiarConstrasenia";
                </script>
                <?php
            }

        }
    }

    public function expired(){  return view('expired');  }
}
