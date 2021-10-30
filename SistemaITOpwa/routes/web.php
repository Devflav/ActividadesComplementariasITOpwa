<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\DEProfessionalsController;
use App\Http\Controllers\JDepartmentController;
use App\Http\Controllers\PResponsableController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CAcademicController;
use App\Http\Controllers\EscServicesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

//Route::group(['middleware' => 'guest'], function() {

    /*-------------------------------------------------------------------------------------------------------------------------------*/
    //Rutas para las vistas de presentación
    Route::group(['namespace' => 'App\Http\Controllers'], function()     {

        Route::get('/', [PublicController::class, 'iniSesion']);
        Route::get('/IniciarSesion', [PublicController::class, 'iniSesion'])->name('inises');
        Route::post('/Acceso', [PublicController::class, 'authenticate']);
        Route::get('/CambiarConstrasenia', [PublicController::class, 'change']);
        Route::post('/change/passwd', [PublicController::class, 'changepasswd']);
        Route::get('/SeleccionarSesion', [PublicController::class, 'selectSesion']);
        Route::post('/redirectUsu', [PublicController::class, 'selectesion']);
        Route::get('/Registrarse', [PublicController::class, 'registro']);
        Route::post('/Enviar/Registro', [PublicController::class, 'nuevo_registro']);
        Route::get('/sesionexpired', [PublicController::class, 'expired']);
        
    });

    // Route::group(['middleware' => 'guest', 'namespace' => 'App\Http\Controllers\Auth'], function()
    // {
    //     Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'inises'])->name('inises');
    //     Route::get('IniciarSesion', [App\Http\Controllers\Auth\LoginController::class, 'inises'])->name('inises');
    //     Route::post('Acceso', [App\Http\Controllers\Auth\LoginController::class, 'authenticate']);
    //     Route::post('Logout', [App\Http\Controllers\Auth\LoginController::class, 'cer_ses'])->name('cer_ses');
    // });
//});


Route::group(['middleware' => 'auth'], function() {
/*-------------------------------------------------------------------------------------------------------------------------------*/
//Rutas de la Coordinación de Actividades Complementarias
    Route::group(['middleware' => 'administrador'], function()    {
        //'prefix' => 'CoordAC',
        Route::get('/CoordAC', [AdministratorController::class, 'f_inicio']);

        Route::get('/CoordAC/actividades/{p}', [AdministratorController::class, 'f_actividades']);
        Route::get('/CoordAC/actividad/{s}/{p}', [AdministratorController::class, 'f_actividad']);
        Route::get('/searchact', [AdministratorController::class, 'f_searchact']);
        Route::get('/CoordAC/actip/{t}/{p}', [AdministratorController::class, 'f_actipo']);
        Route::get('/CoordAC/actdeptos', [AdministratorController::class, 'f_depto']);
        Route::get('/CoordAC/actdep/{d}/{p}', [AdministratorController::class, 'f_actdepto']);
        Route::get('/CoordAC/nuevaAct', [AdministratorController::class, 'f_n_actividad']);
        Route::post('/regAct', [AdministratorController::class, 'f_regAct']);
        Route::get('/CoordAC/editarAct/{a}', [AdministratorController::class, 'f_e_actividad']);
        Route::get('/update/actividad/{a}', [AdministratorController::class, 'f_editAct']);
        Route::get('/delete/actividad/{a}', [AdministratorController::class, 'f_deleteact']);
        
        Route::get('/CoordAC/grupos/{p}', [AdministratorController::class, 'f_grupos']);
        Route::get('/CoordAC/grupos/{s}/{p}', [AdministratorController::class, 'f_gruposB']);
        Route::get('/searchgru', [AdministratorController::class, 'f_searchgru']);
        Route::get('/CoordAC/nuevoGrupo/{d}', [AdministratorController::class, 'f_n_grupo']);
        Route::post('/regGrupo', [AdministratorController::class, 'f_regGrupo']);
        Route::get('/CoordAC/editarGru/{g}/{d}', [AdministratorController::class, 'f_e_grupo']);
        Route::post('/update/grupo/{g}', [AdministratorController::class, 'f_editGrupo']);
        Route::get('/delete/grupo/{g}', [AdministratorController::class, 'f_deletegru']);

        Route::get('/CoordAC/estudiantes/{p}', [AdministratorController::class, 'f_estudiantes']);
        Route::get('/CoordAC/estudiantes/{s}/{p}', [AdministratorController::class, 'f_estudiantesB']);
        Route::get('/searchest', [AdministratorController::class, 'f_searchest']);
        Route::get('/CoordAC/nuevoEst', [AdministratorController::class, 'f_n_estudiante']);
        Route::get('/CoordAC/editEst{id_est}', [AdministratorController::class, 'f_e_estudiante']);
        Route::post('/regEst', [AdministratorController::class, 'f_regEst']);
        Route::post('/update/estudiante/{e}', [AdministratorController::class, 'f_editEst']);
        Route::get('/delete/estudiante/{id}', [AdministratorController::class, 'f_deleteest']);
        
        Route::get('/CoordAC/reportes', [AdministratorController::class, 'f_reportes']);
        
        Route::get('/CoordAC/carreras/{search}', [AdministratorController::class, 'f_carreras']);
        Route::get('/searchcar', [AdministratorController::class, 'f_searchcar']);
        Route::get('/CoordAC/nuevaCarr', [AdministratorController::class, 'f_n_carrera']);
        Route::post('/regCar', [AdministratorController::class, 'f_regCar']);
        Route::get('/CoordAC/editarCarr{id_car}', [AdministratorController::class, 'f_e_carrera']);
        Route::post('/update/carrera/{c}', [AdministratorController::class, 'f_editCar']);
        Route::get('/delete/carrera/{id}', [AdministratorController::class, 'f_deletecar']);
        
        Route::get('/CoordAC/critEvaluacion/{search}', [AdministratorController::class, 'f_critEva']);
        Route::get('/searchcrit', [AdministratorController::class, 'f_searchcrit']);
        Route::get('/CoordAC/nuevoCritEval', [AdministratorController::class, 'f_n_critEva']);
        Route::post('/regCritE', [AdministratorController::class, 'f_regCritE']);
        Route::get('/CoordAC/editCritEval{id_crit}', [AdministratorController::class, 'f_e_critEva']);
        Route::post('/update/criterio_evaluacion/{ce}', [AdministratorController::class, 'f_editCritE']);
        Route::get('/delete/criterio_evaluacion/{ce}', [AdministratorController::class, 'f_deletecrit']);
        
        Route::get('/CoordAC/departamentos/{s}', [AdministratorController::class, 'f_departamentos']);
        Route::get('/CoordAC/departamentos/{s}/{p}', [AdministratorController::class, 'f_departamento']);
        Route::get('/searchdpt', [AdministratorController::class, 'f_searchdpt']);
        Route::get('/CoordAC/nuevoDepto', [AdministratorController::class, 'f_n_depto']);
        Route::post('/regDepto', [AdministratorController::class, 'f_regDepto']);
        Route::get('/CoordAC/editDepto{d}', [AdministratorController::class, 'f_e_depto']);
        Route::post('/update/departamento/{d}', [AdministratorController::class, 'f_editDepto']);
        Route::get('/delete/departamento/{d}', [AdministratorController::class, 'f_deletedpt']);
        
        Route::get('/CoordAC/grados/{s}', [AdministratorController::class, 'f_grados']);
        Route::get('/CoordAC/grados/{s}/{p}', [AdministratorController::class, 'f_grado']);
        Route::get('/searchgra', [AdministratorController::class, 'f_searchgra']);
        Route::get('/CoordAC/nuevoGrado', [AdministratorController::class, 'f_n_grado']);
        Route::post('/regGrado', [AdministratorController::class, 'f_regGrado']);
        Route::get('/CoordAC/editGrado{id_gra}', [AdministratorController::class, 'f_e_grado']);
        Route::get('/delete/grado/{id}', [AdministratorController::class, 'f_deletegra']);
        Route::post('/update/grado/{g}', [AdministratorController::class, 'f_editGrado']);
        
        Route::get('/CoordAC/periodos/{p}', [AdministratorController::class, 'f_periodos']);
        Route::get('/CoordAC/periodos/{s}/{p}', [AdministratorController::class, 'f_periodo']);
        Route::get('/searchperi', [AdministratorController::class, 'f_searchperi']);
        Route::get('/CoordAC/nuevoPeri', [AdministratorController::class, 'f_n_periodo']);
        Route::get('/CoordAC/detallePeri{id_peri}', [AdministratorController::class, 'f_det_periodo']);
        Route::get('/CoordAC/editPeri/{id_peri}', [AdministratorController::class, 'f_e_peri']);
        Route::post('/regPeriE', [AdministratorController::class, 'f_regPeriodo']);
        Route::post('/update/periodo/{p}', [AdministratorController::class, 'f_editPeriodo']);
        Route::get('/delete/periodo/{id}', [AdministratorController::class, 'f_deleteperi']);
        
        Route::get('/CoordAC/personal/{p}', [AdministratorController::class, 'f_personal']);
        Route::get('/CoordAC/personal/{s}/{p}', [AdministratorController::class, 'f_personalB']);
        Route::get('/searchpers', [AdministratorController::class, 'f_searchpers']);
        Route::get('/CoordAC/nuevaPer', [AdministratorController::class, 'f_n_persona']);
        Route::post('/regEmp', [AdministratorController::class, 'f_regEmp']);
        Route::get('/CoordAC/nuevoAdmin', [AdministratorController::class, 'f_n_admin']);
        Route::post('CoordAC/regAdmin', [AdministratorController::class, 'f_regAdmin']);
        Route::get('/CoordAC/editPer{id_per}', [AdministratorController::class, 'f_e_persona']);
        Route::post('/update/personal/{e}', [AdministratorController::class, 'f_editEmp']);
        Route::get('/delete/personal/{e}', [AdministratorController::class, 'f_deleteper']);
        Route::get('/CoordAC/inhabilitados', [AdministratorController::class, 'f_inhabilitados']);
        Route::post('/habilitar/personal/{e}', [AdministratorController::class, 'f_habilitar']);
        
        Route::get('/CoordAC/puestos/{s}', [AdministratorController::class, 'f_puestos']);
        Route::get('/searchpue', [AdministratorController::class, 'f_searchpue']);
        Route::get('/CoordAC/nuevoPues', [AdministratorController::class, 'f_n_puesto']);
        Route::post('/regPues', [AdministratorController::class, 'f_regPuesto']);
        Route::get('/CoordAC/editarPues{pu}', [AdministratorController::class, 'f_e_puesto']);
        Route::post('/update/puesto/{pu}', [AdministratorController::class, 'f_editpuesto']);
        Route::get('/delete/puesto/{pu}', [AdministratorController::class, 'f_deletepue']);
        
        Route::get('/CoordAC/lugares/{p}', [AdministratorController::class, 'f_lugares']);
        Route::get('/CoordAC/lugares/{s}/{p}', [AdministratorController::class, 'f_lugar']);
        Route::get('/CoordAC/nuevoLugar', [AdministratorController::class, 'f_n_lugar']);
        Route::get('/CoordAC/editLugar{l}', [AdministratorController::class, 'f_e_lugar']);
        Route::post('/regLugar', [AdministratorController::class, 'f_regLugar']);
        Route::post('/update/lugar/{l}', [AdministratorController::class, 'f_editlugar']);
        Route::get('/searchlug', [AdministratorController::class, 'f_searchlug']);
        Route::get('/delete/lugar/{id}', [AdministratorController::class, 'f_deletelug']);

        Route::get('/CoordAC/restUsuario/{p}', [AdministratorController::class, 'f_r_usuarios']);
        Route::get('/CoordAC/restUsuario/{search}/{p}', [AdministratorController::class, 'f_r_usuariosB']);
        Route::get('/CoordAC/usuariorestart{u}', [AdministratorController::class, 'f_viewrestart']);
        Route::get('/searchusu', [AdministratorController::class, 'f_searchusu']);
        Route::get('/restartuser{user}', [AdministratorController::class, 'f_restartuser']);
        
        Route::get('/CoordAC/suspLabores/{p}', [AdministratorController::class, 'f_s_labores']);
        Route::get('/CoordAC/suspLabores/{s}/{p}', [AdministratorController::class, 'f_s_labor']);
        Route::get('/searchslab', [AdministratorController::class, 'f_searchslab']);
        Route::get('/CoordAC/nuevaFecha', [AdministratorController::class, 'f_n_fecha']);
        Route::post('/regFecha', [AdministratorController::class, 'f_regFecha']);
        Route::get('/delete/fecha_inhabil/{id}', [AdministratorController::class, 'f_deletefech']);
        
        Route::get('/CoordAC/inscripciones', [AdministratorController::class, 'f_inscripciones']);
        Route::get('/cac/inscripciones', [AdministratorController::class, 'f_inscrip']);
        Route::get('/CoordAC/inscripA/{d}/{p}', [AdministratorController::class, 'f_inscripA']);
        Route::get('/CoordAC/inscripA/{d}/{p}/{s}', [AdministratorController::class, 'f_inscripAB']);
        Route::get('/searchA/{d}', [AdministratorController::class, 'f_searchA']);
        Route::get('/CoordAC/inscripNA/{d}/{p}', [AdministratorController::class, 'f_inscripNA']);
        Route::get('/CoordAC/inscripNA/{d}/{p}/{s}', [AdministratorController::class, 'f_inscripNAB']);
        Route::get('/searchNA/{d}', [AdministratorController::class, 'f_searchNA']);
        Route::get('/CoordAC/inscripPA/{d}/{p}', [AdministratorController::class, 'f_inscripPA']);
        Route::get('/CoordAC/inscripPA/{d}/{p}/{s}', [AdministratorController::class, 'f_inscripPAB']);
        Route::get('/searchPA/{d}', [AdministratorController::class, 'f_searchPA']);
        Route::get('/CoordAC/inscripBJ/{d}/{p}', [AdministratorController::class, 'f_inscripBJ']);
        Route::get('/CoordAC/inscripBJ/{d}/{p}/{s}', [AdministratorController::class, 'f_inscripBJB']);
        Route::get('/searchBJ/{d}', [AdministratorController::class, 'f_searchBJ']);
        Route::get('/CoordAC/detInscrip/{d}/{i}', [AdministratorController::class, 'f_detInscrip']);
        Route::get('/aprobar/{i}/{d}', [AdministratorController::class, 'f_aprobar']);
        Route::get('/noaprob/{i}/{d}', [AdministratorController::class, 'f_noaprobar']);
        Route::get('/bajainscrip/{i}/{d}', [AdministratorController::class, 'f_bajaInscrip']);
        Route::get('/CoordAC/impHorario/{d}/{p}', [AdministratorController::class, 'f_horarioGrupos']);
        Route::get('/CoordAC/inscribir/{e}/{d}', [AdministratorController::class, 'f_inscribir']);
        Route::get('/CoordAC/inscrip_fuera_tiempo/{e}/{d}', [AdministratorController::class, 'f_outime']);
        Route::post('CoordAC/inscribir_outime/{ns}', [AdministratorController::class, 'f_inscrip_outime']);
        Route::get('/CoordAC/register/{e}/{g}', [AdministratorController::class, 'f_register']);
        
        Route::get('/CoordAC/datosGen', [AdministratorController::class, 'f_perfil']);
        Route::get('/CoordAC/editperfil', [AdministratorController::class, 'f_editperfil']);
        Route::post('/cac/editperf', [AdministratorController::class, 'f_editar']);
        Route::get('/CoordAC/editpasswd', [AdministratorController::class, 'f_passwd']);
        Route::post('/cac/editpasswd', [AdministratorController::class, 'f_edpasswd']);

        Route::get('/CoordAC/horario/{g}', [AdministratorController::class, 'fpdf_imprimir']);
        Route::get('/CoordAC/imprimir{i}', [AdministratorController::class, 'f_horario']);
        Route::get('/CoordAC/reimprimir_grupo/{g}', [AdministratorController::class, 're_imprimir_grupo']);
        
        Route::get('/CoordAC/actualizar/{ori}/{obj}', [AdministratorController::class, 'f_ediciones']);
        Route::get('/CoordAC/eliminar/{ori}/{obj}', [AdministratorController::class, 'f_eliminaciones']);

        Route::post('logoutCAC', [AdministratorController::class, 'logoutCAC']);

    });

    Route::group(['middleware' => 'divisionep'], function()     {
        //'prefix' => 'CoordAC',
        Route::get('DivEProf', [DEProfessionalsController::class, 'f_inicio']);

        Route::get('DivEProf/actividades/{p}', [DEProfessionalsController::class, 'f_actividades']);
        Route::get('DivEProf/actividad/{s}/{p}', [DEProfessionalsController::class, 'f_actividad']);
        Route::get('DivEProf/searchact', [DEProfessionalsController::class, 'f_searchact']);
        Route::get('DivEProf/actip/{t}/{p}', [DEProfessionalsController::class, 'f_actipo']);
        Route::get('DivEProf/actdeptos', [DEProfessionalsController::class, 'f_depto']);
        Route::get('DivEProf/actdep/{d}/{p}', [DEProfessionalsController::class, 'f_actdepto']);
        Route::get('DivEProf/nuevaAct', [DEProfessionalsController::class, 'f_n_actividad']);
        Route::post('DivEProf/regAct', [DEProfessionalsController::class, 'f_regAct']);
        Route::get('DivEProf/editarAct/{a}', [DEProfessionalsController::class, 'f_e_actividad']);
        Route::get('DivEProf/update/actividad/{a}', [DEProfessionalsController::class, 'f_editAct']);
        //Route::get('DivEProf/eliminar/actividad/{a}', [DEProfessionalsController::class, 'f_eliminaciones']);
        Route::get('DivEProf/delete/actividad/{a}', [DEProfessionalsController::class, 'f_deleteact']);
        
        Route::get('DivEProf/grupos/{p}', [DEProfessionalsController::class, 'f_grupos']);
        Route::get('DivEProf/grupos/{s}/{p}', [DEProfessionalsController::class, 'f_gruposB']);
        Route::get('DivEProf/searchgru', [DEProfessionalsController::class, 'f_searchgru']);
        Route::get('DivEProf/nuevoGrupo/{d}', [DEProfessionalsController::class, 'f_n_grupo']);
        Route::post('DivEProf/regGrupo', [DEProfessionalsController::class, 'f_regGrupo']);
        Route::get('DivEProf/editarGru/{g}/{d}', [DEProfessionalsController::class, 'f_e_grupo']);
        Route::post('DivEProf/update/grupo/{g}', [DEProfessionalsController::class, 'f_editGrupo']);
        Route::get('DivEProf/delete/grupo/{g}', [DEProfessionalsController::class, 'f_deletegru']);

        Route::get('DivEProf/estudiantes/{p}', [DEProfessionalsController::class, 'f_estudiantes']);
        Route::get('DivEProf/estudiantes/{s}/{p}', [DEProfessionalsController::class, 'f_estudiantesB']);
        Route::get('DivEProf/searchest', [DEProfessionalsController::class, 'f_searchest']);
        Route::get('DivEProf/nuevoEst', [DEProfessionalsController::class, 'f_n_estudiante']);
        Route::post('DivEProf/regEst', [DEProfessionalsController::class, 'f_regEst']);
        
        Route::get('DivEProf/reportes', [DEProfessionalsController::class, 'f_reportes']);
        
        Route::get('DivEProf/carreras/{search}', [DEProfessionalsController::class, 'f_carreras']);
        Route::get('DivEProf/searchcar', [DEProfessionalsController::class, 'f_searchcar']);
        Route::get('DivEProf/nuevaCarr', [DEProfessionalsController::class, 'f_n_carrera']);
        Route::post('DivEProf/regCar', [DEProfessionalsController::class, 'f_regCar']);
        
        Route::get('DivEProf/critEvaluacion/{search}', [DEProfessionalsController::class, 'f_critEva']);
        Route::get('DivEProf/searchcrit', [DEProfessionalsController::class, 'f_searchcrit']);
        Route::get('DivEProf/nuevoCritEval', [DEProfessionalsController::class, 'f_n_critEva']);
        Route::post('DivEProf/regCritE', [DEProfessionalsController::class, 'f_regCritE']);
        
        Route::get('DivEProf/departamentos/{s}', [DEProfessionalsController::class, 'f_departamentos']);
        Route::get('DivEProf/departamentos/{s}/{p}', [DEProfessionalsController::class, 'f_departamento']);
        Route::get('DivEProf/searchdpt', [DEProfessionalsController::class, 'f_searchdpt']);
        Route::get('DivEProf/nuevoDepto', [DEProfessionalsController::class, 'f_n_depto']);
        Route::post('DivEProf/regDepto', [DEProfessionalsController::class, 'f_regDepto']);
        
        Route::get('DivEProf/grados/{s}', [DEProfessionalsController::class, 'f_grados']);
        Route::get('DivEProf/grados/{s}/{p}', [DEProfessionalsController::class, 'f_grado']);
        Route::get('DivEProf/searchgra', [DEProfessionalsController::class, 'f_searchgra']);
        Route::get('DivEProf/nuevoGrado', [DEProfessionalsController::class, 'f_n_grado']);
        Route::post('DivEProf/regGrado', [DEProfessionalsController::class, 'f_regGrado']);
        
        Route::get('DivEProf/periodos/{p}', [DEProfessionalsController::class, 'f_periodos']);
        Route::get('DivEProf/periodos/{s}/{p}', [DEProfessionalsController::class, 'f_periodo']);
        Route::get('DivEProf/searchperi', [DEProfessionalsController::class, 'f_searchperi']);
        Route::get('DivEProf/nuevoPeri', [DEProfessionalsController::class, 'f_n_periodo']);
        Route::post('DivEProf/regPeriE', [DEProfessionalsController::class, 'f_regPeriodo']);
        
        Route::get('DivEProf/personal/{p}', [DEProfessionalsController::class, 'f_personal']);
        Route::get('DivEProf/personal/{s}/{p}', [DEProfessionalsController::class, 'f_personalB']);
        Route::get('DivEProf/searchpers', [DEProfessionalsController::class, 'f_searchpers']);
        Route::get('DivEProf/nuevaPer', [DEProfessionalsController::class, 'f_n_persona']);
        Route::post('DivEProf/regEmp', [DEProfessionalsController::class, 'f_regEmp']);
        Route::get('DivEProf/editPer{id_per}', [DEProfessionalsController::class, 'f_e_persona']);
        Route::post('DivEProf/update/personal/{e}', [DEProfessionalsController::class, 'f_editEmp']);
        Route::get('DivEProf/delete/personal/{e}', [DEProfessionalsController::class, 'f_deleteper']);
        Route::get('DivEProf/detallePeri/{id}', [DEProfessionalsController::class, 'f_det_periodo']);
        
        Route::get('DivEProf/puestos/{s}', [DEProfessionalsController::class, 'f_puestos']);
        Route::get('DivEProf/searchpue', [DEProfessionalsController::class, 'f_searchpue']);
        
        Route::get('DivEProf/lugares/{p}', [DEProfessionalsController::class, 'f_lugares']);
        Route::get('DivEProf/lugares/{s}/{p}', [DEProfessionalsController::class, 'f_lugar']);
        Route::get('DivEProf/nuevoLugar', [DEProfessionalsController::class, 'f_n_lugar']);
        Route::post('DivEProf/regLugar', [DEProfessionalsController::class, 'f_regLugar']);
        Route::get('DivEProf/searchlug', [DEProfessionalsController::class, 'f_searchlug']);

        Route::get('DivEProf/suspLabores/{p}', [DEProfessionalsController::class, 'f_s_labores']);
        Route::get('DivEProf/suspLabores/{s}/{p}', [DEProfessionalsController::class, 'f_s_labor']);
        Route::get('DivEProf/searchslab', [DEProfessionalsController::class, 'f_searchslab']);
        Route::get('DivEProf/nuevaFecha', [DEProfessionalsController::class, 'f_n_fecha']);
        Route::post('DivEProf/regFecha', [DEProfessionalsController::class, 'f_regFecha']);
        Route::get('DivEProf/delete/fecha_inhabil/{id}', [DEProfessionalsController::class, 'f_deletefech']);

        Route::get('DivEProf/datosGen', [DEProfessionalsController::class, 'f_perfil']);
        Route::get('DivEProf/editperfil', [DEProfessionalsController::class, 'f_editperfil']);
        Route::post('DivEProf/cac/editperf', [DEProfessionalsController::class, 'f_editar']);
        Route::get('DivEProf/editpasswd', [DEProfessionalsController::class, 'f_passwd']);
        Route::post('DivEProf/cac/editpasswd', [DEProfessionalsController::class, 'f_edpasswd']);
        
        Route::get('DivEProf/actualizar/{ori}/{obj}', [DEProfessionalsController::class, 'f_ediciones']);
        Route::get('DivEProf/eliminar/{ori}/{obj}', [DEProfessionalsController::class, 'f_eliminaciones']);
        
        Route::post('logoutDEP', [DEProfessionalsController::class, 'logoutDEP']);

    });

    /*-------------------------------------------------------------------------------------------------------------------------------*/
    //Rutas de Jefes de Departamento
    Route::group(['middleware' => 'jefedepto'], function()     {

        Route::get('JDepto/genConst{n_control}', [JDepartmentController::class, 'downloadConstancia']);
        Route::get('JDepto/criterioPdf{n_control}', [JDepartmentController::class, 'criterioPdf']);
        Route::get('JDepto', [JDepartmentController::class, 'f_inicio']);
        
        Route::get('JDepto/estudiante/Hist', [JDepartmentController::class, 'f_estudianteH']);
        Route::get('JDepto/estudiante/Historial', [JDepartmentController::class, 'f_estudianteHist']);

        Route::get('JDepto/actividad/{p}', [JDepartmentController::class, 'f_deptoAct']);
        Route::get('JDepto/actividad/{s}/{p}', [JDepartmentController::class, 'f_deptoA']);
        Route::get('JDepto/nuevAct', [JDepartmentController::class, 'f_n_actividad']);
        Route::get('JDepto/searchActividad', [JDepartmentController::class, 'f_searchact']);
        Route::post('/dpt/regAct', [JDepartmentController::class, 'f_regAct']);
        Route::get('JDepto/editAct{id_act}', [JDepartmentController::class, 'f_e_actividad']);
        Route::post('/dpt/editAct/{id_act}', [JDepartmentController::class, 'f_editAct']);
        
        Route::get('JDepto/grupos/{p}', [JDepartmentController::class, 'f_grupos']);
        Route::get('JDepto/grupo/{s}/{p}', [JDepartmentController::class, 'f_grupo']);
        Route::get('JDepto/searchGrupo', [JDepartmentController::class, 'f_searchgrupo']);
        Route::get('JDepto/nuevGru', [JDepartmentController::class, 'f_n_grupo']);
        Route::post('/dpt/regGrupo', [JDepartmentController::class, 'f_regGrupo']);
        Route::get('JDepto/editGru{id_gru}', [JDepartmentController::class, 'f_e_grupo']);
        Route::post('/dpt/editGrupo/{id_gru}', [JDepartmentController::class, 'f_editGrupo']);
        
        Route::get('JDepto/datgen', [JDepartmentController::class, 'f_perfil']);
        Route::get('JDepto/editperf', [JDepartmentController::class, 'f_editperfil']);
        Route::post('/dpt/editPerfil', [JDepartmentController::class, 'f_updatePerfil']);
        Route::get('JDepto/cambcontrasenia', [JDepartmentController::class, 'f_passwd']);
        Route::post('/dpt/editpasswd', [JDepartmentController::class, 'f_editpasswd']);
        
        Route::get('JDepto/personal/{p}', [JDepartmentController::class, 'f_personal']);
        Route::get('JDepto/personal/{s}/{p}', [JDepartmentController::class, 'f_personalB']);
        Route::get('JDepto/searchPersonal', [JDepartmentController::class, 'f_searchper']);
        Route::get('JDepto/nuevper', [JDepartmentController::class, 'f_n_persona']);
        Route::post('/dpt/regEmp', [JDepartmentController::class, 'f_regEmp']);
        Route::get('JDepto/editpers{id_emp}', [JDepartmentController::class, 'f_e_persona']);
        Route::post('/dpt/editpers{id_emp}', [JDepartmentController::class, 'f_editEmp']);
        
        Route::get('JDepto/hmembretada', [JDepartmentController::class, 'f_h_mem']);
        Route::post('/dpt/savehmem', [JDepartmentController::class, 'f_savehmem']);
        
        Route::post('logoutJD', [JDepartmentController::class, 'logoutJD']);

        Route::get('JDepto/lista_alumnos{id_grupo}/{origin}', [JDepartmentController::class, 'f_lista_alumnos']);
        Route::get('JDepto/{origin}', [JDepartmentController::class, 'f_grupos_constancias']);
        Route::get('JDepto/lista{id_gru}/{origin}', [PResponsableController::class, 'f_lista']);
        Route::get('JDepto/download{id_gru}/{print}', [PResponsableController::class, 'downloadPdf']);
        

    });
    
    /*-------------------------------------------------------------------------------------------------------------------------------*/
    //Rutas de Responsables de Actividad
    Route::group(['middleware' => 'profesorr'], function()    {

        Route::get('ProfR/genConst{n_control}', [PResponsableController::class, 'downloadConstancia']);
        Route::get('ProfR/criterioPdf{n_control}', [PResponsableController::class, 'criterioPdf']);
        Route::get('ProfR/formEval/{num_control}/{grupo}', [PResponsableController::class, 'formStudentEvaluation']);
        Route::get('ProfR/download{id_gru}/{print}', [PResponsableController::class, 'downloadPdf']);
        
        Route::get('ProfR', [PResponsableController::class, 'f_inicio']);
        Route::get('ProfR/grupos', [PResponsableController::class, 'f_grupos']);
        Route::get('ProfR/grupos/{s}', [PResponsableController::class, 'f_gruposB']);
        Route::get('/pr/searchgru', [PResponsableController::class, 'f_searchgru']);
        Route::get('ProfR/datosGen', [PResponsableController::class, 'f_perfil']);
        Route::get('ProfR/editper', [PResponsableController::class, 'f_editperfil']);
        Route::post('/prof/editperf', [PResponsableController::class, 'f_editar']);
        Route::get('ProfR/editpasswd', [PResponsableController::class, 'f_passwd']);
        Route::post('/prof/editpasswd', [PResponsableController::class, 'f_edpasswd']);
        Route::post('ProfR/updateGroup', [PResponsableController::class, 'update_grupo']);
        Route::get('ProfR/verh{g}', [PResponsableController::class, 'f_vhorario']);

        Route::post('logoutPR', [PResponsableController::class, 'logoutPR']);

        Route::get('ProfR/{origin}', [PResponsableController::class, 'f_evaluar']);
        Route::get('ProfR/lista{id_gru}/{origin}', [PResponsableController::class, 'f_lista']);
        Route::post('ProfR/saveEval', [PResponsableController::class, 'f_save_eval']);


    });

    /*-------------------------------------------------------------------------------------------------------------------------------*/
    //Rutas de Estudiante
    Route::group(['middleware' => 'estudiante'], function()    {
        //'prefix' => 'Est', 
        Route::get('Est', [StudentController::class, 'f_inicio']);
        Route::get('Est/micarrera', [StudentController::class, 'f_micarrera']);
        Route::get('Est/accarreras', [StudentController::class, 'f_actCarreras']);
        Route::get('Est/actip{t}', [StudentController::class, 'f_actividades']);
        Route::get('Est/actcar{c}', [StudentController::class, 'f_actividadesCar']);
        Route::get('Est/perfil', [StudentController::class, 'f_perfil']);
        Route::get('Est/editar', [StudentController::class, 'f_editar']);
        Route::get('Est/verh{id_gru}', [StudentController::class, 'f_vhorario']);
        Route::get('Est/editpasswd', [StudentController::class, 'f_e_passwd']);
        Route::post('/editpsswd', [StudentController::class, 'f_editpsswd']);
        Route::get('Est/inscribir{id_gru}', [StudentController::class, 'f_inscribir']);
        Route::get('/est/confInscrip', [StudentController::class, 'f_confInscrip']);
        Route::get('Est/cursando', [StudentController::class, 'f_cursando']);
        Route::get('Est/historial', [StudentController::class, 'f_historial']);
        Route::get('Est/lineamiento', [StudentController::class, 'f_lineamiento']);
        Route::get('/solicitudins{id}', [StudentController::class, 'f_solicitudIns']);
        
        Route::get('Est/imprimir/horario/{i}', [StudentController::class, 'f_horario_e']);
        
        Route::post('logoutE', [StudentController::class, 'logoutE']);

    });

    /*-------------------------------------------------------------------------------------------------------------------------------*/
    //Rutas de Coordinadores de Carrera
    Route::group(['middleware' => 'coordinador'], function()    {

        Route::get('CoordC', [CAcademicController::class, 'f_inicio']);
        Route::get('CoordC/estudiante', [CAcademicController::class, 'search']);
        Route::get('CoordC/searchest', [CAcademicController::class, 'searchEst']);
        Route::get('CoordC/datosGen', [CAcademicController::class, 'f_perfil']);
        Route::get('CoordC/editper', [CAcademicController::class, 'f_editperfil']);
        Route::post('/coordcar/editperf', [CAcademicController::class, 'f_editar']);
        Route::get('CoordC/editpasswd', [CAcademicController::class, 'f_passwd']);
        Route::post('/coordcar/editpasswd', [CAcademicController::class, 'f_edpasswd']);

        Route::post('logoutCA', [CAcademicController::class, 'logoutCA']);
        
    });

    /*-------------------------------------------------------------------------------------------------------------------------------*/
    //Rutas de Servicios Escolares
    Route::group(['middleware' => 'escolares'], function()    {

        Route::get('ServEsc', [EscServicesController::class, 'f_inicio']);
        Route::get('ServEsc/estudiante', [EscServicesController::class, 'f_search']);
        Route::get('ServEsc/searchest', [EscServicesController::class, 'f_searchEst']);
        Route::post('ServEsc/saveproof/{id}', [EscServicesController::class, 'f_saveproof']);
        Route::get('ServEsc/datosGen', [EscServicesController::class, 'f_perfil']);
        Route::get('ServEsc/editper', [EscServicesController::class, 'f_editperfil']);
        Route::post('/servesc/editperf', [EscServicesController::class, 'f_editar']);
        Route::get('ServEsc/editpasswd', [EscServicesController::class, 'f_passwd']);
        Route::post('/servesc/editpasswd', [EscServicesController::class, 'f_edpasswd']);

        Route::post('logoutSE', [EscServicesController::class, 'logoutSE']);

    });


});