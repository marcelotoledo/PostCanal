$(document).ready(function() { var active_request = false;  function showSpinner() { $.ab_spinner ({ height: 32, width: 32, image: "<?php $this->img_src('spinner/linux_spinner.png') ?>", message: "... carregando" }
); }
 function hideSpinner() { $.ab_spinner_stop(); }
  $("input[@name='register']").val("no");  function toggleAuthForm() { register = $("input[@name='register']").val(); $("#authtitle").toggle(); $("#regtitle").toggle(); $("#regrow").toggle(); $("#pwdconfrow").toggle(); $("input[@name='regcancel']").toggle(); $("input[@name='register']").val(register == "yes" ? "no" : "yes"); }
 $("#reglnk").click(function() { toggleAuthForm(); }
); $("input[@name='regcancel']").click(function() { toggleAuthForm(); }
);  $("#pwdlnk").click(function() { if(active_request == true) { return null; }
 if($("input[@name='email']").val() == "") { $.ab_alert("digite um EMAIL"); return null; }
 parameters = { email: $("input[@name='email']").val() }
 $.ajax ({ type: "POST", url: "<?php echo $this->url('profile', 'recovery') ?>", dataType: "json", data: parameters, beforeSend: function() { active_request = true; showSpinner(); }
, complete: function() { active_request = false; hideSpinner(); }
, success: function (data) { if(data == "recovery_ok") $.ab_alert("Um EMAIL foi enviado " + "para o endereço informado"); else if(data == "recovery_instruction_failed") $.ab_alert("Não foi possível enviar instruções " + "para o endereço de e-mail especificado!"); }
, error: function () { $.ab_alert("ERRO NO SERVIDOR"); }
 }
); }
);  $("input[@name='authsubmit']").click(function() { if(active_request == true) { return null; }
 register = $("input[@name='register']").val(); email = $("input[@name='email']").val(); password = $("input[@name='password']").val(); confirm_ = $("input[@name='confirm']").val(); if(email == "" || password == "" || (register == "yes" && confirm_ == "")) { $.ab_alert("Preencha o formulário corretamente"); return null; }
 if(register == "yes" && password != confirm_) { $.ab_alert("Senha e confirmação NÃO CORRESPONDEM"); return null; }
 action = (register == "yes") ? "register" : "login"; parameters = { email: email, password: password, confirm: confirm_ }
 $.ajax ({ type: "POST", url: "<?php $this->url('profile') ?>/" + action, dataType: "json", data: parameters, beforeSend: function () { active_request = true; showSpinner(); }
, complete: function() { active_request = false; hideSpinner(); }
, success: function (data) {  if(data == "login_ok") { window.location = "<?php $this->url('dashboard') ?>"; }
 else if(data == "login_invalid") { $.ab_alert("Autenticação INVÁLIDA"); }
 else if(data == "login_register_unconfirmed") { $.ab_alert("Cadastro NÃO CONFIRMADO.<br>" + "Verifique o pedido de confirmação " + "enviada por e-mail."); }
  else if(data == "register_ok") { $.ab_alert("Cadastro realizado com sucesso.\n" + "Um EMAIL foi enviado para o endereço informado"); toggleAuthForm(); }
 else if(data == "register_failed") { $.ab_alert("Não foi possível efetuar um novo cadastro"); }
 else if(data == "register_incomplete") { $.ab_alert("Cadastro INCOMPLETO"); }
 else if(data == "register_password_not_matched") { $.ab_alert("Senha e Confirmação NÃO CORRESPONDEM"); }
 else if(data == "register_instruction_failed") { $.ab_alert("Não foi possível enviar instruções " + "para o endereço de e-mail especificado!"); }
 }
, error: function () { $.ab_alert("ERRO NO SERVIDOR"); }
 }
); }
); }
); 