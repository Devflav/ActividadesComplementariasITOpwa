const elements = [];
const holdingValues = {};

(() => {
    $('#exampleModal').modal('toggle');
})();

$( "#saveAsistencias" ).click(function(){
    $("#grupoForm").submit(function(e){
        e.preventDefault();
    });

    const asistenciasModalN = document.getElementById("asistenciasModal").value;
    if(!asistenciasModalN)
        return


    document.getElementById('asistencias').value = asistenciasModalN;

    const shouldBeSubmitted = asistenciasModalN * 100 / asistenciasGrupo;

    if(shouldBeSubmitted < 61) {
        alert('Este alumno no cumple con el minimo de 60% de asistencias\nSe registrara automaticamente');
        document.getElementById('observaciones').value='ninguna';
        document.getElementById('idDesempenio').value='1';
        document.getElementById('resultadoNumerico').value='0';
        for (let i = 1; i <= formElements; i++) {
            const id = `#select-${i}`;
            const select = document.querySelector(id);
            select.value ='0';
        }

        document.getElementById('evaluacionForm').submit();
    }else {
        $('#exampleModal').modal('hide');
    }
});

const types = [
    { s: 0, e: 1, value: "Insuficiente", id: 1 },
    { s: 1, e: 1.5, value: "Suficiente", id: 2 },
    { s: 1.5, e: 2.5, value: "Bueno", id: 3 },
    { s: 2.5, e: 3.5, value: "Notable", id: 4 },
    { s: 3.5, e: 4.1, value: "Excelente", id: 5 },
];

const getTextBasedOnValue = (val) =>
    types.find((type) => val >= type.s && val < type.e) || {};

const setResValue = (res) => {
    const resultado = document.getElementById("resultado");
    const resultadoN = document.getElementById("resultadoNumerico");
    const idDesempenio = document.getElementById("idDesempenio");
    const getType = getTextBasedOnValue(Number(res));
    resultado.value = getType.value;
    resultadoN.value = Number(res).toFixed(1);
    idDesempenio.value = getType.id;
};

const handleChange = (event, updatedElement) => {
    holdingValues[updatedElement] = Number(event.target.value);
    const toArr = Object.entries(holdingValues);
    const sum = toArr.map((e) => e[1]).reduce((a, b) => a + b);
    setResValue(sum / formElements);
};

for (let i = 1; i <= formElements; i++) {
    const id = `#select-${i}`;
    const select = document.querySelector(id);
    select.addEventListener("change", (e) => handleChange(e, id));
    elements.push(select);

    holdingValues[id] = 0;
}
