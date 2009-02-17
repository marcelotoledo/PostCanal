$(document).ready(function() { var active_request = false;  function showSpinner() { $.ab_spinner ({ height: 32, width: 32, image: "<?php $this->img_src('spinner/linux_spinner.png') ?>", message: "... carregando" }
); }
 function hideSpinner() { $.ab_spinner_stop(); }
  $("input[@name='pwdchangesubmit']").click(function() { if(active_request == true) { return null; }
 uid = $("input[@name='uid']").val(); password = $("input[@name='password']").val(); confirm_ = $("input[@name='confirm']").val(); if(password == "" || confirm_ == "") { $.ab_alert("Preencha o formulário corretamente"); return null; }
 if(password != confirm_) { $.ab_alert("Senha e confirmação NÃO CORRESPONDEM"); return null; }
 parameters = { uid: uid, password: password, confirm: confirm_ }
 $.ajax ({ type: "POST", url: "<?php $this->url('profile','password') ?>", dataType: "json", data: parameters, beforeSend: function() { active_request = true; showSpinner(); }
, complete: function() { active_request = false; hideSpinner(); }
, success: function (data) { if(data == "password_change_ok") { $("#pwdform").toggle(); $("#changenotice").toggle(); }
 else if(data == "password_change_failed") { $.ab_alert("Não foi possível alterar a senha de acesso"); }
 else if(data == "password_change_not_matched") { $.ab_alert("Senha e Confirmação NÃO CORRESPONDEM"); }
 }
, error: function (data) { $.ab_alert("ERRO NO SERVIDOR"); }
 }
); }
); }
); 