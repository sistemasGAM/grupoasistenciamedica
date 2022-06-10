with(document.formReg){
	onsubmit = function(e){
		e.preventDefault();
		ok = true;
		if(ok && user.value==""){
			ok=false;
			alert("Debe escribir un nombre de usuario");
			user.focus();
		}
        if(ok && email.value==""){
			ok=false;
			alert("Debe escribir un correo electronico");
			email.focus();
		}       
		if(ok && passOne.value==""){
			ok=false;
			alert("Debe escribir su password");
			passOne.focus();
		}
        if(ok && passTwo.value==""){
			ok=false;
			alert("Debe confirmar su Password");
			passTwo.focus();
		}
        if(ok && nomina.value==""){
			ok=false;
			alert("Debe escribir su nomina");
			nomina.focus();
		}
		if(ok){ submit(); }
	}
}

