<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Codedge\Fpdf\Facades\Fpdf;

/**Declaración de modelos a ocupar */
use App\Models\Mlogs;           use App\Models\Mgrupo;
use App\Models\Mtipo;           
use App\Models\Mgrado;          use App\Models\Mlugar;
use App\Models\Musers;          use App\Models\Mpuesto;
use App\Models\Mhorario;        use App\Models\Mcarrera;
use App\Models\Mperiodo;        use App\Models\Mpersona;
use App\Models\Mempleado;       use App\Models\Mactividad;
use App\Models\Mestudiante;     use App\Models\Minscripcion;
use App\Models\Mdepartamento;   use App\Models\Mfechas_inhabiles;
use App\Models\Mcriterios_evaluacion;
use Mail;       use URL;        use DB;

class MyTestUnit extends Controller
{
    public function registro_ext_estudiante($data)  {

        $nControl = $data[0];
        $contraseña = bcrypt($data[0]);
        $nombre = mb_strtoupper($data[1]);
        $apePat = mb_strtoupper($data[2]);
        $apeMat = mb_strtoupper($data[3]);
        $nomUser = mb_strtoupper($data[1].' '.$data[2].' '.$data[3]);
        $carrera = $data[4];
        $semestre = $data[5];
        $email = $data[6];
        $curp = mb_strtoupper($data[7]);
        $hoy = date("Y-m-d");

        $persona = Mpersona::create(['nombre' => $nombre, 'apePat' => $apePat,
        'apeMat' => $apeMat, 'curp' => $curp, 'tipo' => "Estudiante", 'estado' => 1]);

        $estudiante = Mestudiante::create(['id_persona' => $persona->id, 'id_carrera' => $carrera, 
        'num_control' => $nControl, 'email' => $email, 'semestre' => $semestre]);

        $user = Musers::create(['id_persona' => $persona->id, 'id_puesto' => 6,
        'nombre' => $nomUser, 'usuario' => $email, 'password' => $contraseña,
        'fecha_registro' => $hoy, 'edo_sesion' => 0, 'estado' => 1]);

        if($persona != null && $estudiante != null && $user != null)
            return 1;
        else
            return 0;

    }

    public function tipos_actividades(){

        $tipos = Mtipo::select('id_tipo', 'nombre')->get();

        foreach($tipos as $t){
            $t->nombre = ucwords(mb_strtolower($t->nombre));
        }

        return $tipos;
    }

    public function registrar_actividad($data){

        $clave = mb_strtoupper($data[0]);
        $nombre = mb_strtoupper($data[1]);
        $creditos = $data[2];
        $depto = $data[3];
        $tipo = $data[4];
        $descrip = mb_strtoupper($data[5]);
        $restringida = $data[6];

        $peri = Mperiodo::select('id_periodo')
            ->where('estado', "Actual")
                ->first();

        $acti = Mactividad::create(['id_depto' => $depto, 'id_tipo' => $tipo,
        'id_periodo' => $peri->id_periodo, 'clave' => $clave, 'nombre' => $nombre,
        'creditos' => $creditos, 'descripcion' => $descrip, 
        'restringida' => $restringida, 'estado' => 1]);

        if($acti != null) return 1; else return 0;
    }

    public function registrar_grupo($data, $schedule){

        $clave = mb_strtoupper($data[0]);
        $actividad = $data[1];
        $responsable = $data[2];
        $lugar = $data[3];
        $cupo = $data[4];
        $orden = $data[5];

        $lun = $schedule[0];        $lunf = $schedule[1];
        $mar = $schedule[2];        $marf = $schedule[3];
        $mie = $schedule[4];        $mief = $schedule[5];
        $jue = $schedule[6];        $juef = $schedule[7];
        $vie = $schedule[8];        $vief = $schedule[9];
        $sab = $schedule[10];       $sabf = $schedule[11];

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
            $lun = true;
        }

        if($mar != null){
            Mhorario::create(['id_grupo' => $grupo->id, 
                'id_dia' => 2, 'hora_inicio' => $mar,
                'hora_fin' => $marf]);
            $mar = true;
        }

        if($mie != null){
            Mhorario::create(['id_grupo' => $grupo->id, 
                'id_dia' => 3, 'hora_inicio' => $mie,
                'hora_fin' => $mief]);
            $mie = true;
        }

        if($jue != null){
            Mhorario::create(['id_grupo' => $grupo->id, 
                'id_dia' => 4, 'hora_inicio' => $jue,
                'hora_fin' => $juef]);
            $jue = true;
        }

        if($vie != null){
            Mhorario::create(['id_grupo' => $grupo->id, 
                'id_dia' => 5, 'hora_inicio' => $vie,
                'hora_fin' => $vief]);
                $vie = true;
        }

        if($sab != null){
            Mhorario::create(['id_grupo' => $grupo->id, 
                'id_dia' => 6, 'hora_inicio' => $sab,
                'hora_fin' => $sabf]);
            $sab = true;
            
        }

        if($grupo != null){
            if($lun || $mar || $mie || $jue || $vie || $sab)
                return 1;
            else    
                return 0; 
        }
        else 
            return 0;

    }

    public function generar_reporte($data) { 

        $periodoI = $data[0];
        $actividadI = $data[1];

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

        if($res != null) return 1; else return 0;
  
    }

    public function registrar_carrera($data){

        $carrera = mb_strtoupper($data[0]);
        $depto = $data[1];
        $tipo = $data[2];
        if($tipo == 1)
            $tipo = 'INGENIERÍA ';
        else 
            $tipo = 'LICENCIATURA ';
        
        $carrera = $tipo.$carrera;

        $registry = Mcarrera::create(['id_depto' => $depto, 'nombre' => $carrera, 
        'estado' => 1]);

        if($registry != null) return 1; else return 0;

    }

    public function registrar_criterio_eval($data){

        $nombre = mb_strtoupper($data[0]);
        $descrip = mb_strtoupper($data[1]);

        $registry = Mcriterios_evaluacion::create(['nombre' => $nombre,
        'descripcion' => $descrip, 'estado' => 1]);

        if($registry != null) return 1; else return 0;

    }

    public function registrar_departamento($data){

        $nombre = mb_strtoupper($data[0]);
        $jefe = $data[1];

        $dpt = Mdepartamento::create(['id_persona' => $jefe, 'nombre' => $nombre, 'estado' => 1]);

        if($dpt != null) return 1; else return 0;
    }

    public function registrar_grado_pers($data){

        $nombre = mb_strtoupper($data[0]);
        $sig = mb_strtoupper($data[1]);

        $grade = Mgrado::create(['nombre' => $nombre, 
        'significado' => $sig, 'estado' => 1]);

        if($grade != null) return 1; else return 0;

    }

    public function registrar_periodo($data){
        
        $ruta = "/images/ac_ito/php_unitest";
        
        $nombre = $data[0]." PHP";
        $inicio = $data[1];
        $fin = $data[2];
        $iniIns = $data[3];
        $finIns = $data[4];
        $iniEval = $data[5];
        $finEval = $data[6];
        $iniCons = $data[7];
        $finCons = $data[8];
        $gob = "php_unitest"; $tecnm = "php_unitest"; $ito = "php_unitest"; $encabezado = "php_unitest";
        $inscrip = true; $eval = true; $const = true; 
        $mi = true; $me = true; $mc = true;
        $peri;

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
                    return 2;
            }else{
                if(!$inscrip && !$eval && !$const){

                    $sig = Mperiodo::select('id_periodo')
                        ->where('estado', "Siguiente")->first();
                    
                    $sig != null 
                        ? 
                            Mperiodo::where('id_periodo', $sig->id_periodo)
                                ->update(['estado' => "Espera"])
                        :   "";

                    $peri = Mperiodo::create(['nombre' => $nombre, 'inicio' => $inicio, 'fin' => $fin, 
                    'logo_gob' => $ruta.$gob, 'logo_tecnm' => $ruta.$tecnm, 'logo_ito' => $ruta.$ito,
                    'logo_anio' => $ruta.$encabezado, 'estado' => "Siguiente"]);

                    if($peri != null) return 1; else return 0;
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

                            $peri = Mperiodo::create(['nombre' => $nombre, 'inicio' => $inicio, 'fin' => $fin, 
                            'ini_inscripcion' => $iniIns, 'fin_inscripcion' => $finIns,
                            'ini_evaluacion' => $iniEval, 'fin_evaluacion' => $finEval,
                            'ini_gconstancias' => $iniCons, 'fin_gconstancias' => $finCons,
                            'logo_gob' => $ruta.$gob, 'logo_tecnm' => $ruta.$tecnm, 'logo_ito' => $ruta.$ito,
                            'logo_anio' => $ruta.$encabezado, 'estado' => "Siguiente"]);
                            
                            if($peri != null) return 1; else return 0;

                        }else{
                            return 3;
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

                            $peri = Mperiodo::create(['nombre' => $nombre, 'inicio' => $inicio, 'fin' => $fin, 
                            'ini_inscripcion' => $iniIns, 'fin_inscripcion' => $finIns,
                            'ini_evaluacion' => $iniEval, 'fin_evaluacion' => $finEval,
                            'logo_gob' => $ruta.$gob, 'logo_tecnm' => $ruta.$tecnm, 'logo_ito' => $ruta.$ito,
                            'logo_anio' => $ruta.$encabezado, 'estado' => "Siguiente"]);

                            if($peri != null) return 1; else return 0;

                        }elseif(!$mc && $finIns < $iniCons && $finCons < $fin){
                            
                            $sig = Mperiodo::select('id_periodo')
                                ->where('estado', "Siguiente")->first();
                            
                            $sig != null 
                                ? 
                                    Mperiodo::where('id_periodo', $sig->id_periodo)
                                        ->update(['estado' => "Espera"])
                                :   "";

                            $peri = Mperiodo::create(['nombre' => $nombre, 'inicio' => $inicio, 'fin' => $fin, 
                            'ini_inscripcion' => $iniIns, 'fin_inscripcion' => $finIns,
                            'ini_gconstancias' => $iniCons, 'fin_gconstancias' => $finCons,
                            'logo_gob' => $ruta.$gob, 'logo_tecnm' => $ruta.$tecnm, 'logo_ito' => $ruta.$ito,
                            'logo_anio' => $ruta.$encabezado, 'estado' => "Siguiente"]);

                            if($peri != null) return 1; else return 0;

                        }else{

                            $sig = Mperiodo::select('id_periodo')
                                ->where('estado', "Siguiente")->first();
                            
                            $sig != null 
                                ? 
                                    Mperiodo::where('id_periodo', $sig->id_periodo)
                                        ->update(['estado' => "Espera"])
                                :   "";

                            $peri = Mperiodo::create(['nombre' => $nombre, 'inicio' => $inicio, 'fin' => $fin, 
                            'ini_inscripcion' => $iniIns, 'fin_inscripcion' => $finIns,
                            'logo_gob' => $ruta.$gob, 'logo_tecnm' => $ruta.$tecnm, 'logo_ito' => $ruta.$ito,
                            'logo_anio' => $ruta.$encabezado, 'estado' => "Siguiente"]);

                            if($peri != null) return 1; else return 0;

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

                            $peri = Mperiodo::create(['nombre' => $nombre, 'inicio' => $inicio, 'fin' => $fin, 
                            'ini_evaluacion' => $iniEval, 'fin_evaluacion' => $finEval,
                            'ini_gconstancias' => $iniCons, 'fin_gconstancias' => $finCons,
                            'logo_gob' => $ruta.$gob, 'logo_tecnm' => $ruta.$tecnm, 'logo_ito' => $ruta.$ito,
                            'logo_anio' => $ruta.$encabezado, 'estado' => "Siguiente"]);

                            if($peri != null) return 1; else return 0;

                        }else{
                            
                            $sig = Mperiodo::select('id_periodo')
                                ->where('estado', "Siguiente")->first();
                            
                            $sig != null 
                                ? 
                                    Mperiodo::where('id_periodo', $sig->id_periodo)
                                        ->update(['estado' => "Espera"])
                                :   "";

                            $peri = Mperiodo::create(['nombre' => $nombre, 'inicio' => $inicio, 'fin' => $fin, 
                            'ini_evaluacion' => $iniEval, 'fin_evaluacion' => $finEval,
                            'logo_gob' => $ruta.$gob, 'logo_tecnm' => $ruta.$tecnm, 'logo_ito' => $ruta.$ito,
                            'logo_anio' => $ruta.$encabezado, 'estado' => "Siguiente"]);

                            if($peri != null) return 1; else return 0;

                        }
                    }elseif(!$mc && $inicio < $iniCons && $finCons < $fin){
                        
                        $sig = Mperiodo::select('id_periodo')
                                ->where('estado', "Siguiente")->first();
                            
                            $sig != null 
                                ? 
                                    Mperiodo::where('id_periodo', $sig->id_periodo)
                                        ->update(['estado' => "Espera"])
                                :   "";

                        $peri = Mperiodo::create(['nombre' => $nombre, 'inicio' => $inicio, 'fin' => $fin, 
                        'ini_gconstancias' => $iniCons, 'fin_gconstancias' => $finCons,
                        'logo_gob' => $ruta.$gob, 'logo_tecnm' => $ruta.$tecnm, 'logo_ito' => $ruta.$ito,
                        'logo_anio' => $ruta.$encabezado, 'estado' => "Siguiente"]);

                        if($peri != null) return 1; else return 0;

                    }else{
                        return 4;
                    } 
                }
            }
        }else{
            return 5;
        }

    }

    public function registrar_empleado($data){

        $curp = mb_strtoupper($data[0]);
        $contraseña = bcrypt($data[0]);
        $nombre = mb_strtoupper($data[1]);
        $apePat = mb_strtoupper($data[2]);
        $apeMat = mb_strtoupper($data[3]);
        $nomUser = $nombre.' '.$apePat.' '.$apeMat;
        $depto = $data[4];
        $puesto = $data[5];
        $grado = $data[6];
        $hoy = date("Y-m-d");
        $exist = false;
        $empleados = Mpersona::select('curp')->get();

        foreach($empleados as $e){
            if($e->curp == $curp)
                $exist = true;
        }

        if($exist){
            return 2;
        }else{
            $persona = Mpersona::create(['nombre' => $nombre, 'apePat' => $apePat,
            'apeMat' => $apeMat, 'curp' => $curp, 'tipo' => "Empleado", 'estado' => 1]);

            $employee = Mempleado::create(['id_persona' => $persona->id, 'id_depto' => $depto, 
            'id_grado' => $grado, 'id_puesto' => $puesto]);

            $user = Musers::create(['id_persona' => $persona->id, 'id_puesto' => $puesto,
            'nombre' => $nomUser, 'usuario' => $curp, 'password' => $contraseña,
            'fecha_registro' => $hoy, 'edo_sesion' => 0, 'estado' => 1]);

        }

        if($user != null)
            return 1;
        else
            return 0;

    }

    public function nuevo_administrador($data){

        $curp = mb_strtoupper($data[0]);
        $contraseña = bcrypt($data[0]);
        $nombre = mb_strtoupper($data[0]);
        $apePat = mb_strtoupper($data[0]);
        $apeMat = mb_strtoupper($data[0]);
        $nomUser = mb_strtoupper($data[0].' '.$data[0].' '.$data[0]);
        $depto = $data[0];
        $grado = $data[0];
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
                
        }
    }

    public function registrar_puesto($data){

        $nombre = mb_strtoupper($data[0]);
        $descrip = $data[1];

        $puesto = Mpuesto::create(['nombre' => $nombre,
        'descripcion' => $descrip, 'estado' => 1]);

        if($puesto != null) return 1; else return 0;

    }

    public function registrar_fecha($data){

        $fecha = $data[0];
        $fechfin = $data[1];
        $motivo = mb_strtoupper($data[2]);
        $end = false;     
  
        if($fechfin == '' || $fecha == $fechfin)  {

            $fecha = Mfechas_inhabiles::create(['fecha' => $fecha,
            'motivo' => $motivo, 'estado' => 1]);
            if($fecha != null) return 1; else return 0;

        }
        elseif($fecha > $fechfin) {
            return 2;
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

                }
            }

            if($end = true) return 1; else return 0;
        }

    }

    public function registrar_lugar($data){

        $nombre = mb_strtoupper($data);

        $place = Mlugar::create(['nombre' => $nombre, 'estado' => 1]);

        if($place != null) return 1; else return 0;
    }

    public function restablecer_usuario($us, $id) { 

        $type = Mpersona::select('tipo')
            ->where('id_persona', $id)->first();
        
        $user = 1;

        if($type->tipo == "Estudiante"){
            $passwd = Mestudiante::select('num_control')
                ->where('id_persona', $id)->first();
            
            $newpw = Hash::make($passwd->num_control);

            $user = Musers::where('id_persona', $id)
                    ->update(['password' => $newpw]);
        }
        else{
            $passwd = Mpersona::select('curp')
                ->where('id_persona', $id)->first();
            
            $newpw = Hash::make($passwd->curp);

            $user = Musers::where('id_persona', $id)
                    ->update(['password' => $newpw]);
        }

        if($user != null) return 1; else return 0;
    }

    public function rehabilitar_usuario($data){

        $user = $data[2];
        $puesto = $data[1];

        $employee = Mempleado::where('id_persona', $user)
            ->update(['id_puesto' => $puesto]);
        
        if($employee != null) return 1; else return 0;

    }

    public function aprobar_inscripcion_estudiante($id_ins){

        $inscription = Minscripcion::where('id_inscripcion', $id_ins)
        ->update(['aprobada' => 1]);

        $group = Minscripcion::where('id_inscripcion', $id_ins)->first();
        $cupo = Mgrupo::select('cupo_libre')->where('id_grupo', $group->id_grupo)->first();
        $cupo = $cupo->cupo_libre - 1;
        
        Mgrupo::where('id_grupo', $group->id_grupo)->update([
            'cupo_libre' => $cupo
        ]);

        $est = DB::select('SELECT g.clave , a.nombre AS actividad, 
                            p.nombre, p.apePat, p.apeMat
        FROM inscripcion AS i
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
        WHERE i.id_inscripcion = '.$id_ins);


        foreach($est as $e){
            $message = 
        'Hola, '.$e->nombre.' '.$e->apePat.' '.$e->apeMat.' 

        Te escribimos para notificarte que ha sido aprobada tu inscripción a la actividad 

        "'.$e->actividad.'"

        si tienes algúna duda envíanos un correo electrónico a la 
        siguiente dirección: altamirano.flv@gmail.com 
            
                                Attentamente 
            
                Coordinación de Actividades Complementarias 
                    Tecnológco Nacional de MéxicoITO
                    Instituto Tecnológico de Oaxaca';
        }
        $message = wordwrap($message, 80);
        return 1;
        // Mail::raw($message, function ($message) {
            
        //     $email = 'tecnm.ito.complementarias@gmail.com';
            
        //     $message->to($email)
        //         ->subject('Inscripción Aprobada');
        //  });

        if($inscription != null) return 1; else return 0;

    }

    public function no_aprobar_inscripcion($id_ins){

        $inscription = Minscripcion::where('id_inscripcion', $id_ins)
        ->update(['aprobada' => 2]);

        $group = Minscripcion::where('id_inscripcion', $id_ins)->first();
        $cupo = Mgrupo::select('cupo_libre')->where('id_grupo', $group->id_grupo)->first();
        $cupo = $cupo->cupo_libre + 1;
        
        Mgrupo::where('id_grupo', $group->id_grupo)->update([
            'cupo_libre' => $cupo
        ]);
        
        $est = DB::select('SELECT g.clave , a.nombre AS actividad, 
                            p.nombre, p.apePat, p.apeMat
        FROM inscripcion AS i
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
        WHERE i.id_inscripcion = '.$id_ins);

        foreach($est as $e){
            $message = 
        'Hola, '.$e->nombre.' '.$e->apePat.' '.$e->apeMat.' 

        Te escribimos para notificarte que no ha sido aprobada tu inscripción a la actividad 

        "'.$e->actividad.'"

        si tienes algúna duda envíanos un correo electrónico a la 
        siguiente dirección: altamirano.flv@gmail.com 
            
                                Attentamente 
            
                Coordinación de Actividades Complementarias 
                    Tecnológco Nacional de MéxicoITO
                    Instituto Tecnológico de Oaxaca';
        }
        $message = wordwrap($message, 80);

        // Mail::raw($message, function ($message) {
        //     $message->to('tecnm.ito.complementarias@gmail.com')
        //         ->subject('Inscripción No Aprobada');
        //  });

        if($inscription != null) return 1; else return 0;


    }

    public function baja_inscripcion($id_ins){

        $inscription = Minscripcion::where('id_inscripcion', $id_ins)
        ->update(['aprobada' => 3]);

        $group = Minscripcion::where('id_inscripcion', $id_ins)->first();
        $cupo = Mgrupo::select('cupo_libre')->where('id_grupo', $group->id_grupo)->first();
        $cupo = $cupo->cupo_libre + 1;
        
        Mgrupo::where('id_grupo', $group->id_grupo)->update([
            'cupo_libre' => $cupo
        ]);
        
        $est = DB::select('SELECT g.clave , a.nombre AS actividad, 
                            p.nombre, p.apePat, p.apeMat
        FROM inscripcion AS i
            LEFT JOIN grupo AS g ON i.id_grupo = g.id_grupo
            LEFT JOIN actividad AS a ON g.id_actividad = a.id_actividad
            LEFT JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
            LEFT JOIN persona AS p ON e.id_persona = p.id_persona
        WHERE i.id_inscripcion = '.$id_ins);

        foreach($est as $e){
            $message = 
        'Hola, '.$e->nombre.' '.$e->apePat.' '.$e->apeMat.' 
        Te escribimos para notificarte que haz sido dado de baja de la actividad 

                "'.$e->actividad.'"

        si tienes algúna duda envíanos un correo electrónico a la 
        siguiente dirección: altamirano.flv@gmail.com 
                    
                                Attentamente 
                    
                Coordinación de Actividades Complementarias 
                    Tecnológco Nacional de MéxicoITO
                    Instituto Tecnológico de Oaxaca';
        }

        $message = wordwrap($message, 80);

        // Mail::raw($message, function ($message) {
        //     $message->to('tecnm.ito.complementarias@gmail.com')
        //         ->subject('Baja Actividad Complementaria');
        //  });

        if($inscription != null) return 1; else return 0;


    }

    public function inscribir_estudiante_segunda_actividad($data){

        $peri = Mperiodo::select('id_periodo')->where('estado', "Actual")->first();
        $student = $data[0];
        $group = $data[1];
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

                return 1;
            }else{

                return 2;

            }

        }
    }

    public function eliminaciones_global($origin, $objeto){
        /**fecha            * lugar
         * puesto           * personal
         * periodo          * grado
         * departamento     * criterio_eval
         * carrera          * estudiante
         * grupo            * actividad
         */
        $url = "/delete/".$origin."/".$objeto;

        return $url;
    }

    public function ediciones_global($origin, $objeto){
         /**fecha            * lugar
         * puesto           * personal
         * periodo          * grado
         * departamento     * criterio_eval
         * carrera          * estudiante
         * grupo            * actividad
         */
        $url = "/update/".$origin."/".$objeto;

        return $url;    
    }

    public function envio_correo_electronico(){

        $message = 'Coordinación de Actividades Complementarias laravel';

        Mail::raw($message, function ($message) {
            $message->to('14161189@itoaxaca.edu.mx')
                ->subject('Prueba Email');
         });
    }

    public function cambiar_contraseña($data){

        $userpwd = $request->user()->password;
        $user = $request->user()->id_persona;
  
        $pswd = $data[0];
        $newpswd = $data[0];
        $conpswd = $data[0];
  
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

    public function delete_of_criterios_evaluacion(){

        DB::delete('DELETE FROM `criterios_evaluacion` WHERE id_crit_eval > 7');
        
        return 1;
    }

    public function delete_of_grado(){

        DB::delete('DELETE FROM `grado` WHERE significado = "DEVELOPER" AND id_grado <> 22');
        
        return 1;
    }

    public function delete_of_carrera(){

        DB::delete('DELETE FROM `carrera` WHERE nombre LIKE "%GASTRONOMÍA%"');
        
        return 1;
    }

    public function delete_of_lugar(){

        DB::delete('DELETE FROM `lugar` WHERE nombre = "PHP UNIT TEST"');

        return 1;
    }

    public function delete_of_periodo(){
        
        DB::delete('DELETE FROM `periodo` WHERE nombre LIKE "%PHP%"');

        return 1;
    }

    public function delete_of_actividad(){
        
        DB::delete('DELETE FROM `actividad` WHERE nombre = "ACTIVIDAD DE PHP UNIT TEST" 
                AND id_actividad <> 122');

        return 1;
    }

    public function delete_of_departamento(){
        
        DB::delete('DELETE FROM `departamento` WHERE nombre = "DEPARTAMENTO PHP UNIT TEST SERVER"');
 
        return 1;
    }

    public function delete_of_puesto(){

        DB::delete('DELETE FROM `puesto` WHERE nombre = "DEVELOPER" AND id_puesto <> 22');

        return 1;
    }

    public function delete_of_fecha_inhabil(){

        DB::delete('DELETE FROM `fechas_inhabiles` WHERE motivo = "PHP UNIT TEST SERVER ONE"');

        return 1;
    }

    public function delete_of_fechas_inhabiles_bloque(){
        
        DB::delete('DELETE FROM `fechas_inhabiles` WHERE motivo = "PHP UNIT TEST SERVER BLOQ"');

        return 1;
    }

    public function delete_of_empleado(){

        $user = DB::select('SELECT id_persona FROM persona WHERE nombre = "MÁXIMO" 
                AND apePat = "DÉCIMO" AND apeMat = "MERIDIO" AND id_persona <> 5300');
        foreach($user as $u){ $user = $u->id_persona; }

        DB::delete('DELETE FROM `empleado` WHERE id_persona = '.$user );

        return 1;
    }

    public function delete_of_empleado_usuario(){

        $user = DB::select('SELECT id_persona FROM persona WHERE nombre = "MÁXIMO" 
                AND apePat = "DÉCIMO" AND apeMat = "MERIDIO" AND id_persona <> 5300');
        foreach($user as $u){ $user = $u->id_persona; }

        DB::delete('DELETE FROM `users` WHERE id_persona = '.$user);

        return 1;
    }

    public function delete_of_empleado_persona(){

        $user = DB::select('SELECT id_persona FROM persona WHERE nombre = "MÁXIMO" 
                AND apePat = "DÉCIMO" AND apeMat = "MERIDIO" AND id_persona <> 5300');
        foreach($user as $u){ $user = $u->id_persona; }

        DB::delete('DELETE FROM `persona` WHERE id_persona = '.$user);
        return 1;
    }

    public function delete_of_horario_grupo(){

        $group = DB::select('SELECT * FROM `grupo` WHERE id_actividad = 122 
                AND id_persona = 5300 AND id_grupo <> 296');
        $ids;
        for($i=0; $i < count($group); $i++){ $ids[$i] = $group[$i]->id_grupo; }

        for($i=0; $i < count($ids); $i++){ 
            
                DB::delete('DELETE FROM `horario` WHERE id_grupo = '.$ids[$i]);
        }

        return 1;
    }

    public function delete_of_grupo(){

        DB::delete('DELETE FROM `grupo` WHERE id_actividad = 122 
                AND id_persona = 5300 AND id_grupo <> 296');
                
        return 1;
    }

    public function delete_of_estudiante(){
        DB::delete('DELETE FROM `inscripcion` WHERE id_grupo = 296');

        $student = DB::select('SELECT * FROM `estudiante` WHERE num_control = "99168765"');

        foreach($student as $s){
            $student = $s->id_persona;
            DB::delete('DELETE FROM `estudiante` WHERE id_persona = '.$student);
        }
        

        return 1;
    }

    public function delete_of_estudiante_usuario(){

        $student = DB::select('SELECT * FROM `users` WHERE usuario = "99168765@itoaxaca.edu.mx"');

        foreach($student as $s){
            $student = $s->id_persona;
            DB::delete('DELETE FROM `users` WHERE id_persona = '.$student);
        }


        return 1;
    }

    public function delete_of_estudiante_persona(){

        $student = DB::select('SELECT * FROM `persona` WHERE curp = "DDRA960704HOCLLR00"');

        foreach($student as $s){
            $student = $s->id_persona;
            DB::delete('DELETE FROM `persona` WHERE id_persona ='.$student);
        }


        return 1;
    }

    public function evaluar_estudiante($student)  {

        $now = date_create('America/Mexico_City')->format('H');

        $alumno = DB::table('estudiante as a')
                    ->join('inscripcion as i', 'i.id_estudiante', '=', 'a.id_estudiante')
                    ->select('i.id_inscripcion as id', 'i.id_grupo as id_grupo')
                    ->where('a.num_control', '=', $student)
                    ->first();

        $evaluacion = new Mevaluacion();
        $evaluacion->id_desempenio = rand(1, 5);
        $evaluacion->id_inscripcion = rand(100, 500);
        $evaluacion->asistencias = rand(0, 25);
        $evaluacion->calificacion = rand(1, 5);
        $evaluacion->observaciones = "Evaluación realizada desde PHP Unit";
        $evaluacion->constancia = '';
        try {

            for ($i = 1; $i <= 7; $i++){
                $evalValor = new Meval_valor();
                $evalValor->id_evaluacion = $evaluacion->id;
                $evalValor->id_crit_eval = $i;
                $evalValor->id_desempenio = rand(1, 5);

            }

            return 1;
        } catch (Exception $e) {
            return 0;
        }

        return 1;

    }

    public function contruccion_horario($group){
        
        $periodo_ = Mperiodo::select('id_periodo', 'nombre', 'inicio')
        ->where('estado', "Actual")->first();
        
        $fecha_hoy = date('d - m - Y');
        
        $estudiantes = DB::select('SELECT p.nombre, p.apePat, p.apeMat, 
            e.num_control, e.semestre, c.nombre as cnombre, e.id_estudiante
        FROM inscripcion AS i
        JOIN estudiante AS e ON i.id_estudiante = e.id_estudiante
        JOIN persona AS p ON e.id_persona = p.id_persona
        JOIN carrera AS c ON e.id_carrera = c.id_carrera
        JOIN grupo AS g ON i.id_grupo = g.id_grupo 
            WHERE i.id_grupo = 77');
        
        $grupo = DB::select('SELECT g.clave, a.nombre AS actividad, a.creditos, 
                l.nombre AS lugar, p.nombre, p.apePat, p.apeMat
                FROM grupo AS g
            JOIN persona AS p ON g.id_persona = p.id_persona
            JOIN actividad AS a ON g.id_actividad = a.id_actividad
            JOIN lugar AS l ON g.id_lugar = l.id_lugar
                WHERE g.id_grupo = '.$group);

        $horario = DB::select('SELECT ds.nombre, h.hora_inicio, h.hora_fin
            FROM grupo AS g
            LEFT JOIN horario AS h ON g.id_grupo = h.id_grupo
            LEFT JOIN dias_semana AS ds ON h.id_dia = ds.id_dia
                WHERE g.id_grupo = '.$group);

        for($i = 0; $i < count($estudiantes); $i++){

                Fpdf::AddPage();
                Fpdf::SetFont('Arial', '', 8);
                Fpdf::SetMargins(30, 5 , 30);
                Fpdf::SetAutoPageBreak(true);
                Fpdf::Image("img/tec_nm.jpeg", 33, 17, 140, 17);   

                Fpdf::setXY(20,33);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 

                Fpdf::setXY(115,33);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);
                
                Fpdf::setXY(20, 39);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 25, 'NUMERO DE CONTROL: '.utf8_decode($estudiantes[$i]->num_control), 0);

                Fpdf::setXY(115, 39);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 25, 'CREDITOS: '.utf8_decode($grupo[0]->creditos), 0);
                
                Fpdf::setXY(20, 45);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat." ".$estudiantes[$i]->apeMat), 0);

                Fpdf::setXY(115, 45);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($estudiantes[$i]->semestre), 0);
                
                Fpdf::setXY(115, 51);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 25, 'ACTIVIDAD: '.utf8_decode($grupo[0]->actividad), 0);
                
                Fpdf::setXY(20, 51);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($estudiantes[$i]->cnombre), 0);
                
                Fpdf::setXY(25, 71);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(10, 13, 'RESP: ', 0);

                Fpdf::setXY(20, 78);
                Fpdf::SetFont('Arial', '', 8);
                Fpdf::Cell(20, 10, utf8_decode($grupo[0]->nombre), 20);
                
                Fpdf::setXY(20, 82);
                Fpdf::SetFont('Arial', '', 8);
                Fpdf::Cell(20, 10, utf8_decode($grupo[0]->apePat), 20);
                
                Fpdf::setXY(20, 86);
                Fpdf::SetFont('Arial', '', 8);
                Fpdf::Cell(20, 10, utf8_decode($grupo[0]->apeMat), 20);

                Fpdf::setXY(46, 71);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 13, 'GPO: ', 0);

                Fpdf::setXY(46, 82);
                Fpdf::SetFont('Arial', '', 8);
                Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);

                Fpdf::setXY(66, 71);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 13, 'LGR: ', 0);

                Fpdf::setXY(63, 81);
                Fpdf::SetFont('Arial', '', 8);
                Fpdf::Cell(50, 13, utf8_decode($grupo[0]->lugar), 0);

                Fpdf::setXY(85, 69);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(1, 2, 'Dias de Actividad', 0);

                Fpdf::SetFont('Arial', '', 9);
                Fpdf::setXY(20, 115);
                Fpdf::Cell(5, 20, '______________________________', 0);
                
                Fpdf::setXY(20, 120);
                Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                Fpdf::setXY(20, 125);
                Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);
                
                Fpdf::setXY(130, 115);
                Fpdf::Cell(5, 20, '____________________________', 0);

                Fpdf::setXY(130, 120);
                Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat), 0);

                Fpdf::setXY(130, 125);
                Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->apeMat), 0);

                //-----------------------------------segunda parte del horario---------------------------------
                
                Fpdf::Image("img/tec_nm.jpeg", 33, 150, 140, 17);   

                Fpdf::setXY(20,166);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 25, 'FECHA: '.utf8_decode($fecha_hoy), 0); 
                
                Fpdf::setXY(115,166);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 25, 'PERIODO ESCOLAR: '.utf8_decode($periodo_->nombre), 0);
                
                Fpdf::setXY(20, 172);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 25, 'NUMERO DE CONTROL: '.utf8_decode($estudiantes[$i]->num_control), 0);

                Fpdf::setXY(115, 172);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 25, 'CREDITOS: '.utf8_decode($grupo[0]->creditos), 0);

                Fpdf::setXY(20, 178);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 25, 'ALUMNO: '.utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat." ".$estudiantes[$i]->apeMat), 0);

                Fpdf::setXY(115, 178);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 25, 'SEMESTRE: '.utf8_decode($estudiantes[$i]->semestre), 0);
                
                Fpdf::setXY(115, 184);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 25, 'ACTIVIDAD: '.utf8_decode($grupo[0]->actividad), 0);

                Fpdf::setXY(20, 184);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 25, 'CARRERA: '.utf8_decode($estudiantes[$i]->cnombre), 0);

                Fpdf::setXY(25, 204);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(10, 13, 'RESP: ', 0);

                Fpdf::setXY(20, 211);
                Fpdf::SetFont('Arial', '', 8);
                Fpdf::Cell(20, 10, utf8_decode($grupo[0]->nombre), 20);
                
                Fpdf::setXY(20, 215);
                Fpdf::SetFont('Arial', '', 8);
                Fpdf::Cell(20, 10, utf8_decode($grupo[0]->apePat), 20);
                
                Fpdf::setXY(20, 219);
                Fpdf::SetFont('Arial', '', 8);
                Fpdf::Cell(20, 10, utf8_decode($grupo[0]->apeMat), 20);

                Fpdf::setXY(46, 204);

                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 13, 'GPO: ', 0);

                Fpdf::setXY(46, 215);
                Fpdf::SetFont('Arial', '', 8);
                Fpdf::Cell(50, 10, utf8_decode($grupo[0]->clave), 0);

                Fpdf::setXY(66, 204);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(60, 13, 'LGR: ', 0);

                Fpdf::setXY(63, 214);
                Fpdf::SetFont('Arial', '', 8);
                Fpdf::Cell(50, 13, utf8_decode($grupo[0]->lugar), 0);

                Fpdf::setXY(85, 202);
                Fpdf::SetFont('Arial', '', 9);
                Fpdf::Cell(1, 2, 'Dias de Actividad', 0);

                Fpdf::SetFont('Arial', '', 9);

                Fpdf::SetFont('Arial', '', 9);

                Fpdf::setXY(20, 248);
                Fpdf::Cell(5, 20, '______________________________', 0);
                
                Fpdf::setXY(20, 253);
                Fpdf::Cell(5, 20, 'COORDINACION DE ACTIVIDADES', 0);

                Fpdf::setXY(20, 258);
                Fpdf::Cell(5, 20, 'COMPLEMENTARIAS', 0);

                Fpdf::setXY(130, 248);
                Fpdf::Cell(5, 20, '____________________________', 0);

                $contador = 60;
                
                foreach ($horario as $c)      {
                    $contador+=25;

                    Fpdf::setXY($contador , 60);
                    Fpdf::Cell(1, 35, utf8_decode($c->nombre), 0);

                    Fpdf::setXY($contador, 60);
                    Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);
                
                    Fpdf::setXY($contador, 60);
                    Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);
                } 

                $contador = 60;

                foreach ($horario as $c)      {
                    $contador+=25;

                    Fpdf::setXY($contador , 193);
                    Fpdf::Cell(1, 35, utf8_decode($c->nombre), 0);

                    Fpdf::setXY($contador, 193);
                    Fpdf::Cell(9, 55, ''.utf8_decode($c->hora_inicio), 0);
                
                    Fpdf::setXY($contador, 193);
                    Fpdf::Cell(9, 65, ''.utf8_decode($c->hora_fin), 0);

                } 

                Fpdf::setXY(130, 253);
                Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->nombre." ".$estudiantes[$i]->apePat), 0);

                Fpdf::setXY(130, 258);
                Fpdf::Cell(5, 20, utf8_decode($estudiantes[$i]->apeMat), 0);
            
        }

        return 1;
    }

    public function generacion_constancia($student){
        
        $data = DB::table('estudiante as e')
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
        ->where('e.num_control', '=', $student)
        ->first();
    
        $jefeDpto = DB::table('persona as p')
                    ->join('departamento as d', 'd.id_persona', '=', 'p.id_persona')
                    ->join('empleado as e', 'e.id_persona', '=', 'p.id_persona')
                    ->join('grado as g', 'g.id_grado', '=', 'e.id_grado')
                    ->where('d.id_persona', '=', $data->jefeId)
                    ->select('p.nombre as nombre', 'p.apePat as apePat',
                            'p.apeMat as apeMat', 'g.nombre as grado')
                    ->first();

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
                ->where('p.id_persona', '=', 5291)
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
        return 1;
        $pdf = App::make('dompdf.wrapper');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
            ->loadView('ProfRes.constancia', array('data' => $data,
                        'profesor' => $profesor,
                        'jefe' => $jefeDpto,
                        'day' => $now->format('d'),
                        'jefeDpto' => $jefe,
                        'month' => $month,
                        'year' => $now->format('Y')
                    ));

        return 1;
    }

    public function generacion_formato_evaluacion($student){
        
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
        ->where('e.num_control', '=', $student)
        ->first();

        $criterios =  DB::table('criterios_evaluacion as ce')
                        ->get();
        
        $calificacionCrit = DB::table('eval_valor  as ev')
                            ->select('ev.id_evaluacion as idEval', 
                                    'ev.id_crit_eval as idCrit',
                                    'ev.id_desempenio as idDes')
                            ->where('ev.id_evaluacion', '=', $data->id_evaluacion)
                            ->get();

        
        $now = date_create('America/Mexico_City');
        return 1;
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('ProfRes.criteriosEvaluacion',
                    array('data' => $data, 'criterios'=>$criterios,
                        'calificacion'=>$calificacionCrit));
        return 1;
    }

    public function auditoria_actividad(){
        $user = 5258;
        $id_event = 122;
        $event = "Log de agregación de actividad desde PHP Unit";
        $url = 'http://127.0.0.1:8000/CoordAC/nuevaAct';
        $day = date('Y-m-d H:i:s');

        Mlogs::create(['id_persona' => $user,'id_de_evento' => $id_event, 
            'evento' => $event, 'direccion' => $url, 'fecha' => $day]);

        return 1;
    }

    public function auditoria_grupo(){
        $user = 5258;
        $id_event = 296;
        $event = "Log de agregación de grupo desde PHP Unit";
        $url = 'http://127.0.0.1:8000/CoordAC/nuevoGrupo/'.rand(1, 16);
        $day = date('Y-m-d H:i:s');

        Mlogs::create(['id_persona' => $user,'id_de_evento' => $id_event, 
            'evento' => $event, 'direccion' => $url, 'fecha' => $day]);

        return 1;
    }

    public function auditoria_inscripcion(){
        $user = 5258;
        $id_event = rand(1, 163);
        $event = "Log de acreditación de inscripción desde PHP Unit";
        $url = 'http://127.0.0.1:8000/CoordAC/inscribir/'.rand(1, 7000).'/'.rand(1, 16);
        $day = date('Y-m-d H:i:s');

        Mlogs::create(['id_persona' => $user,'id_de_evento' => $id_event, 
            'evento' => $event, 'direccion' => $url, 'fecha' => $day]);

        return 1;
    }

    public function auditoria_evaluacion(){
        $user = 5291;
        $id_event = rand(1, 163);
        $event = "Log de evaluación de estudiante desde PHP Unit";
        $url = 'http://127.0.0.1:8000/ProfR/lista'.rand(1, 296).'/evaluar';
        $day = date('Y-m-d H:i:s');

        Mlogs::create(['id_persona' => $user,'id_de_evento' => $id_event, 
            'evento' => $event, 'direccion' => $url, 'fecha' => $day]);

        return 1;
    }
}
