$(document).ready(function() { var active_request = false;  function showSpinner() { $.ab_spinner ({ height: 32, width: 32, image: "<?php $this->img_src('spinner/linux_spinner.png') ?>", message: "... carregando" }
); }
 function hideSpinner() { $.ab_spinner_stop(); }
  function setPasswordChange(pwdchange) { if(pwdchange) { $("input[@name='current_password']").attr("disabled", false); $("input[@name='new_password']").attr("disabled", false); $("input[@name='new_password_confirm']").attr("disabled", false); $("input[@name='current_password']").focus(); $("input[@name='pwdchange']").val("yes"); }
 else { $("input[@name='current_password']").attr("disabled", true); $("input[@name='current_password']").val(""); $("input[@name='new_password']").attr("disabled", true); $("input[@name='new_password']").val(""); $("input[@name='new_password_confirm']").attr("disabled", true); $("input[@name='new_password_confirm']").val(""); $("input[@name='pwdchange']").val("no"); }
 }
 setPasswordChange(false); $("#pwdchangelnk").click(function() { setPasswordChange(true); }
);  $("input[@name='editcancel']").click(function() { setPasswordChange(false); }
);  $("input[@name='editsubmit']").click(function() { if(active_request == true) { return null; }
 name = $("input[@name='name']").val(); pwdchange = $("input[@name='pwdchange']").val(); current_password = $("input[@name='current_password']").val(); new_password = $("input[@name='new_password']").val(); new_password_confirm = $("input[@name='new_password_confirm']").val(); if(pwdchange == "yes" && (current_password == "" || new_password == "" || new_password_confirm == "")) { $.ab_alert("Preencha o formulário corretamente"); return null; }
 if(pwdchange == "yes" && new_password != new_password_confirm) { $.ab_alert("Senha e confirmação NÃO CORRESPONDEM"); return null; }
 parameters = { name: name, pwdchange: pwdchange, current_password: current_password, new_password: new_password, new_password_confirm: new_password_confirm }
 $.ajax ({ type: "POST", url: "<?php $this->url('profile', 'edit') ?>", dataType: "json", data: parameters, beforeSend: function () { active_request = true; showSpinner(); }
, complete: function () { active_request = false; setPasswordChange(false); hideSpinner(); }
, success: function (data) { if(data == "edit_save_ok") { $.ab_alert("Perfil alterado com sucesso"); }
 else if(data == "edit_save_failed") { $.ab_alert("Alteração do perfil FALHOU!"); }
 else if(data == "edit_save_password_not_matched") { $.ab_alert("Senha e confirmação NÃO CORRESPONDEM"); }
 else if(data == "edit_save_wrong_password") { $.ab_alert("Senha incorreta!"); }
 }
, error: function () { window.location = "<?php $this->url('dashboard') ?>"; }
 }
); }
); }
); 