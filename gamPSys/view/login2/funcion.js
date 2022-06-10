var outer_eye=document.querySelector(".card-details span");
var eye=document.querySelector(".passcode");
var input=document.querySelector("#password-input");
outer_eye.addEventListener('click',function(){

   if(input.type=='password'){
       input.type="text"; 
       eye.classList.remove('fa-eye-slash');
       eye.classList.add('fa-eye');
     input.classList.add('warning');
    }else{
      input.type="password"; 
      eye.classList.remove('fa-eye');
      eye.classList.add('fa-eye-slash');
      input.classList.remove('warning');
  }
});