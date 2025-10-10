//agregar un nuevo elemnto hijo al ejemplo 1 

require([
    'dojo/dom',           // Importa el módulo 'dojo/dom' para manipular el DOM (por ejemplo, buscar elementos por ID)
    'dojo/dom-construct'  // Importa el módulo 'dojo/dom-construct' para crear o insertar nodos HTML
], function (dom, domConstruct) {

    let greetingNode = dom.byId('greeting');
    domConstruct.place('<em> Dojo!</em>', greetingNode);
});

//Hacer cambios a una etiqueta h3 de html

require([
    'dojo/dom',        // Importa el módulo 'dojo/dom' para manipular el DOM.
    'dojo/fx',         // Para agregar efectos.
    'dojo/domReady!'   // Asegura que el código se ejecute solo cuando el DOM esté completamente cargado.
], function (dom, fx) {

    let greeting = dom.byId('ej1');
    greeting.innerHTML += ' con Dojo!';

    fx.slideTo({//Agregarle movimiento
        node: greeting,
        top: 100,
        left: 200
    }).play();
});

//Captura de eventos

require(["dojo/on", "dojo/dom"], function (on, dom) {
    const boton = dom.byId("btn");
    on(boton, "click", function () {
        alert("¡Evento capturado con dojo/on!");
    });
});

//Ejemplo 4
require(["dijit/form/Button", "dojo/domReady!"], function (Button) {
    new Button({
        label: "Haz clic aquí",
        onClick: function () { alert("Botón Dojo"); }
    }, "boton");
});

//Cargar elemtos

require([
  "dojo/request",     // Módulo para hacer peticiones AJAX
  "dojo/dom",         // Manipular el DOM
  "dojo/on",          // Capturar eventos
  "dojo/dom-construct", // Crear elementos en el DOM
  "dojo/domReady!"    // Asegura que el DOM esté listo
], function(request, dom, on, domConstruct) {

  const boton = dom.byId("btn-cargar");
  const resultado = dom.byId("resultado");

  on(boton, "click", function() {
    // Limpia el contenido anterior
    resultado.innerHTML = "<em>Cargando datos...</em>";

    // Realiza una petición AJAX a un archivo JSON local
    request.get("datos.json", {
      handleAs: "json"
    }).then(function(data) {
      // Éxito: muestra los datos
      resultado.innerHTML = "<h3>Usuarios cargados:</h3>";

      data.usuarios.forEach(function(usuario) {
        domConstruct.create("p", {
          innerHTML: usuario.nombre + " (" + usuario.edad + " años)"
        }, resultado);
      });

    }, function(error) {
      // Error: muestra mensaje
      resultado.innerHTML = "<span style='color:red'>Error al cargar datos: " + error + "</span>";
    });
  });
});
