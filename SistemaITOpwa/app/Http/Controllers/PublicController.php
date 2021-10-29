<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Hash;

use App\Models\Mcarrera;
use App\Models\Musers;
use App\Models\Mestudiante;
use App\Models\Mpersona;
use Auth;

class PublicController extends Controller {

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

        $carreras = Mcarrera::select('id_carrera', 'nombre')->where('estado', 1)->get();

        $semestres = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];

        return view('presentacionAC.registro', 
            ['carreras' => $carreras, 
            'semestres' => $semestres,
            'back' => null]);
    }

/**Realiza el request del formalario del registro, valida los
 * datos y crea los registros correspondientes
 */
    public function nuevo_registro(Request $request)  {

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

        return redirect()->to('IniciarSesion');

    }

/**Esta función de encarga de los inicios de sesión, trabaja en
 * diferentes filtros, en el siguiente orden:
 * 1.- Valida usuario y contraseña
 * 2.- Verifica el tipo de usuario
 * 3.- Verifica que sea un usuario vigente
 * 4.- Si es el primer inico de sesión lo redirige a cambiar la
 *      contraseña, si no lo envía a su inicio correspondiente.
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
