<?php

namespace Tests\Complete;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
//use PHPUnit\Framework\TestCase;
use App\Http\Controllers\CAC\MyTestUnit;
use App\Models\M_ACITO\Mtipo;           use App\Models\M_ACITO\Mgrupo;
use App\Models\M_ACITO\Mgrado;          use App\Models\M_ACITO\Mlugar;
use App\Models\M_ACITO\Musers;          use App\Models\M_ACITO\Mpuesto;
use App\Models\M_ACITO\Mhorario;        use App\Models\M_ACITO\Mcarrera;
use App\Models\M_ACITO\Mperiodo;        use App\Models\M_ACITO\Mpersona;
use App\Models\M_ACITO\Mempleado;       use App\Models\M_ACITO\Mactividad;
use App\Models\M_ACITO\Mestudiante;     use App\Models\M_ACITO\Minscripcion;
use App\Models\M_ACITO\Mdepartamento;   use App\Models\M_ACITO\Mfechas_inhabiles;
use App\Models\M_ACITO\Mcriterios_evaluacion;
use Mail;       use URL;        use DB;

class AdminTest extends TestCase
{
    /** @test */
    public function conexion_servidor() {
        $response = $this->get('/IniciarSesion');
        $response->assertStatus(200);
    }

    /** @test */
    public function CompruebaTiposActividades()    {
        $mytest = new MyTestUnit;
        $tipos = $mytest->tipos_actividades();
        $testip = Mtipo::select('id_tipo', 'nombre')->get();
        
        foreach($testip as $t){
            $t->nombre = ucwords(mb_strtolower($t->nombre));
        }

        for($i = 0; $i < count($testip); $i++){
            $this->assertEquals($testip[0][$i], $tipos[0][$i]);
        }
        
    }

    /** @test */
    public function registro_externo_estudiantes() {

        $mytest = new MyTestUnit;
        $data = ["99168765", "Dalia", "Rios", 
            "Altamirano", 2, 5, "99168765@itoaxaca.edu.mx", 
            "DDRA960704HOCLLR00"];
        $registry = $mytest->registro_ext_estudiante($data);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function registro_de_grado() {

        $mytest = new MyTestUnit;
        $data = ["dev", "developer"];
        $registry = $mytest->registrar_grado_pers($data);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function registro_de_carreras() {

        $mytest = new MyTestUnit;
        $data = ["en gastronomía", rand(1, 18), rand(0, 1)];
        $registry = $mytest->registrar_carrera($data);

        $this->assertEquals(1, $registry);
        
    }

    /** @test */
    public function registro_de_criterios_evaluacion() {

        $mytest = new MyTestUnit;
        $data = ["compromiso", 
            "El estudiante muestra compromiso con el desempeño de la actividad complementaria."];
        $registry = $mytest->registrar_criterio_eval($data);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function registro_de_personal() {

        $mytest = new MyTestUnit;
        $grade = Mgrado::where('nombre', "DEV")->first();
        $data = ["aavf".rand(100000, 999999)."hocllr".rand(10, 99), "máximo", "décimo", "meridio", 18, 8, $grade->id_grado];
        $registry = $mytest->registrar_empleado($data);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function registro_de_lugar() {

        $mytest = new MyTestUnit;
        $registry = $mytest->registrar_lugar("php unit test");

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function registro_de_periodos() {
        /**
         * 2 = Periodo fuera de rango, deben ser 4 meses como mínimo y 5 meses como máximo.
         * 3 = Las fechas de los procesos de Inscripción, Evaluación y G. Constancias no pueden traslaparse.
         * 4 = Los procesos de Inscripción, Evaluación y G. Constancias deben ser mínimo de 3 días y máximo 2 semanas.
         * 5 = El término del semestre no puede ser anterior al inicio.
         */
        $nombre = date('F Y')." - ".date('F Y', strtotime('+4 month', strtotime(date('F Y'))));
        $inicio = date('Y-m-d');
        $fin = date('Y-m-d', strtotime('+4 month', strtotime($inicio)));
        $iniIns = date('Y-m-d', strtotime('+5 days', strtotime($inicio)));
        $finIns = date('Y-m-d', strtotime('+10 days', strtotime($iniIns)));
        $iniEval = date('Y-m-d', strtotime('+3 month', strtotime($inicio)));
        $finEval = date('Y-m-d', strtotime('+10 days', strtotime($iniEval)));
        $iniCons = date('Y-m-d', strtotime('+3 days', strtotime($finEval)));
        $finCons = date('Y-m-d', strtotime('+10 days', strtotime($iniCons)));

        $mytest = new MyTestUnit;
        $data = [$nombre, $inicio, $fin, $iniIns, $finIns, $iniEval, $finEval, $iniCons, $finCons];
        $registry = $mytest->registrar_periodo($data);

        $this->assertEquals(1, $registry);
        
        if($registry == 2)
            return "Periodo fuera de rango, deben ser 4 meses como mínimo y 5 meses como máximo.";
        elseif($registry == 3)
            return "Las fechas de los procesos de Inscripción, Evaluación y G. Constancias no pueden traslaparse.";
        elseif($registry == 4)
            return "Los procesos de Inscripción, Evaluación y G. Constancias deben ser mínimo de 3 días y máximo 2 semanas.";
        elseif($registry == 5)
            return "El término del semestre no puede ser anterior al inicio.";
    }

    /** @test */
    public function creacion_de_reportes() {

        $mytest = new MyTestUnit;
        $data = [rand(1, 18), rand(1, 110)];
        $registry = $mytest->generar_reporte($data);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function registro_de_actividad() {

        $mytest = new MyTestUnit;

        $data = ["php".rand(10, 99), "actividad de php unit test",
                 2, rand(1, 18), rand(1, 4), 
                 "ejecución de las pruebas unitarias de php unit test", 0]; 
        $registry = $mytest->registrar_actividad($data);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function registro_de_departamentos() {

        $mytest = new MyTestUnit;
        $boss = Mpersona::select('id_persona')
            ->where('nombre', "MÁXIMO")
            ->where('apePat', "DÉCIMO")
            ->where('apeMat', "MERIDIO")
            ->first();
        $data = ["departamento php unit test server", $boss->id_persona];
        $registry = $mytest->registrar_departamento($data);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function registro_de_puesto() {

        $mytest = new MyTestUnit;
        $data = ["developer", "Developer of PHP-Unit test server"]; 
        $registry = $mytest->registrar_puesto($data);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function registro_de_grupo() {

        $mytest = new MyTestUnit;
        
        $a = Mactividad::select('id_actividad')
            ->where('nombre', "ACTIVIDAD DE PHP UNIT TEST")
            ->first();
        $r = Mpersona::select('id_persona')->where('nombre', "MÁXIMO")
            ->where('apePat', "DÉCIMO")
            ->where('apeMat', "MERIDIO")
            ->first();
        $data = ["gphp".rand(100, 999), $a->id_actividad, $r->id_persona, rand(1, 33), 30, 1];
        $lun = date_create('America/Mexico_City')->format('H');     $lunf = $lun +1;
        $mar = null;        $marf = null;
        $mie = null;        $mief = null;
        $jue = date_create('America/Mexico_City')->format('H') - 1; $juef = $jue - 1;
        $vie = null;        $vief = null;
        $sab = date_create('America/Mexico_City')->format('H') + 1; $sabf = $sab + 1;
        $schedule = [$lun, $lunf, $mar, $marf, $mie, $mief, $jue, $juef, $vie, $vief, $sab, $sabf];

        $registry = $mytest->registrar_grupo($data, $schedule);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function restablecimiento_de_usuario() {

        $mytest = new MyTestUnit;
        $user = Mpersona::select('id_persona')->where('nombre', "MÁXIMO")
                ->where('apePat', "DÉCIMO")
                ->where('apeMat', "MERIDIO")
                ->first();

        $registry = $mytest->restablecer_usuario($user->id_persona, 5300);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function habilitar_usuario_acceso_sistema() {

        $mytest = new MyTestUnit;
        $user = Mpersona::select('id_persona')->where('nombre', "MÁXIMO")->where('apePat', "DÉCIMO")
                    ->where('apeMat', "MERIDIO")->first();
        $puesto = Mpuesto::select('id_puesto')->where('nombre', "DEVELOPER")->first();
        $data = [$user->id_persona, $puesto->id_puesto, 5300];

        $registry = $mytest->rehabilitar_usuario($data);

        $this->assertEquals(0, $registry);
    }

    /** @test */
    public function inscribir_estudiante_segunda_actividad()  {
        /**2 = No se puede inscribir en más de dos actividades por semestre. */

        $mytest = new MyTestUnit;
        $peri = Mperiodo::select('id_periodo')->where('estado', "Actual")->first();
        $student = Mestudiante::select('id_estudiante', 'id_carrera')
                    ->where('num_control', "99168765")
                        ->first();
        $dpt_std = Mcarrera::select('id_depto')
                    ->where('id_carrera', $student->id_carrera)->first();
        $activity = Mactividad::select('id_actividad')
                    ->where('id_depto', $dpt_std->id_depto)
                    ->where('estado', 1)->first();
        $group = Mgrupo::where('id_grupo', 296)->first();
        $data = [$student->id_estudiante, $group->id_grupo];
        $registry = $mytest->inscribir_estudiante_segunda_actividad($data);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function registro_suspension_labores() {
        /**
         * 2 = La fecha de término no puede ser menor que la fecha de inicio.
         */
        $mytest = new MyTestUnit;
        $data = [date('Y-m-d'), '', "php unit test server one"]; 
        $registry = $mytest->registrar_fecha($data);

        $this->assertEquals(1, $registry);

        if($registry == 2) return "La fecha de término no puede ser menor que la fecha de inicio.";
    }

    /** @test */
    public function registro_suspension_labores_bloque() {

        /**
         * 2 = La fecha de término no puede ser menor que la fecha de inicio.
         */
        $mytest = new MyTestUnit;
        $data = [date('Y-m-d'), date('Y-m-d', 
                strtotime('+3 days', strtotime(date('Y-m-d')))),
                "php unit test server bloq"]; 
        $registry = $mytest->registrar_fecha($data);

        $this->assertEquals(1, $registry);

        if($registry == 2) return "La fecha de término no puede ser menor que la fecha de inicio.";
    }

    /** @test */
    public function registro_de_estudiante_admin() {

        $this->registro_externo_estudiantes();
    }

    /** @test */
    public function aprobar_inscripcion()  {

        $mytest = new MyTestUnit;
        $student = Mestudiante::select('id_estudiante')
                    ->where('num_control', "99168765")
                        ->first();
        $inscription = Minscripcion::where('id_estudiante', $student->id_estudiante)
                    ->first();
        
        
        $registry = $mytest->aprobar_inscripcion_estudiante($inscription->id_inscripcion);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function evaluacion_de_estudiante()  {

        $mytest = new MyTestUnit;
        $student = Mestudiante::where('num_control', "99168765")
                        ->first();
        
        $registry = $mytest->evaluar_estudiante($student->num_control);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function no_aprobar_inscripcion()  {
        
        $mytest = new MyTestUnit;
        $student = Mestudiante::select('id_estudiante')
                    ->where('num_control', "99168765")
                        ->first();
        $inscription = Minscripcion::where('id_estudiante', $student->id_estudiante)
                    ->first();
        
        
        $registry = $mytest->no_aprobar_inscripcion($inscription->id_inscripcion);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function dar_de_baja_inscripcion()  {
        
        $mytest = new MyTestUnit;
        $student = Mestudiante::select('id_estudiante')
                    ->where('num_control', "99168765")
                        ->first();
        $inscription = Minscripcion::where('id_estudiante', $student->id_estudiante)
                    ->first();
        
        
        $registry = $mytest->baja_inscripcion($inscription->id_inscripcion);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function generacion_de_horario_estudiante() {

        $mytest = new MyTestUnit;
        $grupo = Mgrupo::where('id_grupo', 296)
                        ->first();
        $registry = $mytest->contruccion_horario($grupo);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function generacion_de_constancia_actividad() {

        $mytest = new MyTestUnit;
        $student = Mestudiante::where('num_control', "15620297")
                        ->first();
        $registry = $mytest->generacion_constancia($student->num_control);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function generacion_de_formato_evaluacion() {

        $mytest = new MyTestUnit;
        $student = Mestudiante::where('num_control', "15620297")
                        ->first();
        $registry = $mytest->generacion_formato_evaluacion($student->num_control);

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function gestion_de_ediciones_de_todos_los_elementos() {

        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /** @test */
    public function eliminacion_criterio_de_evaluacion() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_criterios_evaluacion();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function eliminacion_de_grado() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_grado();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function eliminacion_de_carrera() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_carrera();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function eliminacion_de_lugar() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_lugar();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function eliminacion_de_periodo() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_periodo();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function eliminacion_de_actividad() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_actividad();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function eliminacion_de_departamento() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_departamento();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function eliminacion_de_puesto() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_puesto();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function eliminacion_de_fecha_inhabil() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_fecha_inhabil();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function eliminacion_de_fechas_inhabiles_bloques() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_fechas_inhabiles_bloque();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function eliminacion_de_empleado() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_empleado();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function eliminacion_de_empleado_usuario() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_empleado_usuario();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function eliminacion_de_empleado_persona() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_empleado_persona();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function eliminacion_de_horario_de_grupo() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_horario_grupo();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function eliminacion_de_grupo() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_grupo();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function eliminacion_de_estudiante() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_estudiante();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function eliminacion_de_estudiante_usuario() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_estudiante_usuario();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function eliminacion_de_estudiante_persona() {

        $mytest = new MyTestUnit;
        $registry = $mytest->delete_of_estudiante_persona();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function destruccion_de_sesion()  {
        
        $response = $this->post('/logoutCAC');
        $response->assertStatus(302);
    }

    /** @test */
    public function registro_de_auditoria_actividad() {

        $mytest = new MyTestUnit;
        $registry = $mytest->auditoria_actividad();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function registro_de_auditoria_grupo() {

        $mytest = new MyTestUnit;
        $registry = $mytest->auditoria_grupo();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function registro_de_auditoria_inscripcion() {

        $mytest = new MyTestUnit;
        $registry = $mytest->auditoria_inscripcion();

        $this->assertEquals(1, $registry);
    }

    /** @test */
    public function registro_de_auditoria_evaluacion() {

        $mytest = new MyTestUnit;
        $registry = $mytest->auditoria_evaluacion();

        $this->assertEquals(1, $registry);
    }

}
