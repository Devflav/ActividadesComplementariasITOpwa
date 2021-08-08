$(function(){
    $('#dptpersona').on('change', dptpersonal)
});

function dptpersonal(){
    //alert('corriendo función');
    var id_depto = document.getElementById("dptpersona").value;	
	// Solucion 1: Hacer peticiones y construir c/u de los select
	/*$.get('/CoordAC/nuevoGrupo/' + id_depto, function(data){
		let _html = "";
		$.each(data, (i, e)=> {
			_html+="<option value="+ i + ">" + e + "</option>";
		});
		$("#respon").html(_html);
		//console.log(_html);
	});*/
	

	// Solucion 2: Redireccionando segun el id de departamento
	/*var ruta = location.pathname.split("/")
	ruta[ruta.length-1] = id_depto;
	var nuevo = location.origin + ruta.join("/")
	// console.log("Nueva url= ", nuevo);
	location.href = nuevo*/

	// Solucion 3: Remplazo total (todo o nada)
	$.get('/CoordAC/nuevoGrupo/' + id_depto, function(respuesta){
		let respon = $(respuesta);
		$("#respon").html(respon.find("#respon").html())
		$("#actividades").html(respon.find("#actividades").html())
	});
}

$(function(){
    $('#dptedit').on('change', editgrupo)
});

function editgrupo(){
    var depto = document.getElementById("dptedit").value;	
	var _url = location.pathname.split("/")
	_url[_url.length-1] = depto;
	var newurl = location.origin + _url.join("/")
	location.href = newurl;
}

$(function(){
    $('#deptoactividad').on('change', dptact)
});
function dptact(){
    var depto = document.getElementById("deptoactividad").value;	
	var _url = location.pathname.split("/")
	_url[_url.length-1] = depto;
	var newurl = location.origin + _url.join("/")
	location.href = newurl;
}

const horario = (_url) =>{
	//console.log("adentro");
	$.get(_url, function(respuesta){
		let _html = $(respuesta).find("#alerta").html();
		$("#alerta").html(_html);
		$("#btn_dw").click()
	});
}

const selusu = (_url) =>{
	$.ajax(_url) .done(function(respuesta) {
		let _html = $(respuesta).find("#selusuario").html();
		$("#selusuario").html(_html);
		$("#btn_selusuario").click()
	}).fail(function(error) {
		console.log('Error', error);
	});
}

const imp_horario = (_url) =>{
	$.ajax(_url) .done(function(respuesta) {
		let _html = $(respuesta).find("#horario").html();
		$("#horario").html(_html);
		$("#btn_horario").click()
	}).fail(function(error) {
		console.log('Error', error);
	});
}

const eliminar = (_url) =>{
	$.ajax(_url).done(function(respuesta) {
		let _html = $(respuesta).find("#mimodal").html();
		$("#mimodal").html(_html);
		$("#btn_mimodal").click()
	}).fail(function(error) {
		console.log('Error', error);
	});
}

const editar = (_url) =>{
	$.ajax(_url).done(function(respuesta) {
		let _html = $(respuesta).find("#mimodal").html();
		$("#mimodal").html(_html);
		$("#btn_mimodal").click()
	}).fail(function(error) {
		console.log('Error', error);
	});
}


$(function(){
	$(".custom-file-input").on("change", function() {
		var fileName = $(this).val().split("\\").pop();
		$(this).siblings(".custom-file-label").addClass("selected").html(fileName);
	});
});

$(function(){
	$("#logoEnca").on("change", function() {
		var fileName = $(this).val().split("\\").pop();
		$(this).siblings("logE").addClass("selected").html(fileName);
	});
});

$(function(){
	$("#logoSep").on("change", function() {
		var fileName = $(this).val().split("\\").pop();
		$(this).siblings("#logS").addClass("selected").html(fileName);
	});
});

$(function(){
	$("#logoTecNM").on("change", function() {
		var fileName = $(this).val().split("\\").pop();
		$(this).siblings("#logT").addClass("selected").html(fileName);
	});
});

$(function(){
	$("#logoIto").on("change", function() {
		var fileName = $(this).val().split("\\").pop();
		$(this).siblings("#logI").addClass("selected").html(fileName);
	});
});


$(function(){
    $('#num_stds').on('change', inscrip_students)
});
function inscrip_students(){
    var num = document.getElementById("num_stds").value;	
	var _url = location.pathname.split("/")
	_url[_url.length-2] = num;
	var newurl = location.origin + _url.join("/")
	location.href = newurl;
}


$(function(){
    $('#deptos').on('change', deptosIns)
});
function deptosIns(){
    var dpt = document.getElementById("deptos").value;
	var _url = location.pathname.split("/");
	var num = _url[_url.length-2];
	$.get('/CoordAC/inscrip_fuera_tiempo/' + num + '/' + dpt, function(depto){
		let departamentos = $(depto);
		$("#groups").html(departamentos.find("#groups").html())
	});
}
