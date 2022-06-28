/*
$(document).ready(function () {
  var outer_eye = document.querySelector(".card-details span");
  var eye = document.querySelector(".passcode");
  var input = document.querySelector("#password-input");
  outer_eye.addEventListener('click', function () {

    if (input.type == 'password') {
      input.type = "text";
      eye.classList.remove('fa-eye-slash');
      eye.classList.add('fa-eye');
      input.classList.add('warning');
    } else {
      input.type = "password";
      eye.classList.remove('fa-eye');
      eye.classList.add('fa-eye-slash');
      input.classList.remove('warning');
    }
  });

});
*/

 window.addEventListener("load", function() {
  

// icono para mostrar contraseña
showPassword = document.querySelector('.show-password');
showPassword.addEventListener('click', () => {

    // elementos input de tipo clave
    password1 = document.querySelector('.password1');
    password2 = document.querySelector('.password2');

    if ( password1.type === "text" ) {
        password1.type = "password"
        password2.type = "password"
        showPassword.classList.remove('fa-eye-slash');
    } else {
        password1.type = "text"
        password2.type = "text"
        showPassword.classList.toggle("fa-eye-slash");
    }

})

});

/*
---------------------------------------------------------------------------
*/

$(document).ready(function () {
  $("#carga").hide();
  $("#form").bind("submit", function () {
    $("#carga").show();
    $("#btnAddProvForm").hide();
    $.ajax({
      type: $(this).attr("method"),
      url: $(this).attr("action"),

      data: $(this).serialize(),
      beforeSend: function () { },
      complete: function (data) { },
      success: function (data) {

        $(".contenido").html(data);
        $("#carga").hide();
        $("#btnAddProvForm").show();



      },
      error: function (data) {
        alert("Problemas al tratar de enviar el formulario");
      },
    });
    return false;
  });
});
