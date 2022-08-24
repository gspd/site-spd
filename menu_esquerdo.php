<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
<!--

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
//-->
</script>
<style type="text/css">
<!--
.Linksssi {	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	text-decoration: underline;
	font-weight: bold;
}
.LinksssiClean {	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	text-decoration: underline;
}
.desenvTXT {	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
}
-->
</style>
<script language="JavaScript" type="text/JavaScript">
<!--

function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>

<!-- DEPENDENDO DA LOCALIZAÇÃO DO SEU ARQUIVO DENTRO DA HIERARQUIA DAS PASTAS DO SEU SITE, A FOLHA DE ESTILOS PODE NÃO ENCONTRAR O CAMINHO CORRETO, SE DER ERRO AO VISUALIZAR O ARQUIVO, VERIFIQUE O CAMINHO QUE DEVE SER COLOCADO NA SUA PÁGINA (SOMENTE A PÁGINA QUE ESTÁ COM ERRO) PARA SOLUCIONAR O PROBLEMA - ENTRE EM CONTATO COM O ADMINISTRADOR DE REDES LOCAL PARA IDENTIFICAR O CAMINHO  -->
<link href="folha.css" rel="stylesheet" type="text/css">
<!-- MANTER OS CAMINHOS DAS FOLHAS ABAIXO (FOLHADESTAQUE e IMPRIMIR) GRATO  -->
<!-- <link href="folhaDESTAQUE.css" rel="stylesheet" type="text/css"> --> 
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
//-->
</script>

<!-- INÍCIO - PARA RODAR O MENU COM ROLAGEM VERTICAL -->
<script language="JavaScript" type="text/javascript" src="menu_esq/menu_rodar_win.js"></script>
<!-- FIM -->
<link href="http://www.dcce.ibilce.unesp.br/~aleardo/menu_dd01.css" rel="stylesheet" type="text/css">
<script>

// JavaScript Document

startList = function() {

if (document.all&&document.getElementById) {

navRoot = document.getElementById("nav");

for (i=0; i<navRoot.childNodes.length; i++) {

node = navRoot.childNodes[i];

if (node.nodeName=="LI") {

node.onmouseover=function() {

this.className+=" over";

  }

  node.onmouseout=function() {

  this.className=this.className.replace(" over", "");

   }

   }

  }

 }

}

window.onload=startList;

</script>

<!-- PARTE 1 PHP: CRIAR FORMATO DE APRESENTAÇÂO DO SUBMENU - NÃO MEXER NESSE CÓDIGO -->
<form action="<? echo $PATH_INFO; ?>"  name="formmenu_esq" method="post">
<input name="menu_ancora" type="hidden" value="">
<input name="menu_esq1" type="text" style="display:none" value="">
<input name="menu_esq2" type="text" style="display:none" value="">
<!-- FIM PARTE 1 -->


<!?php include "url.inc"; ?>
<!--         <table width="550"  border="0" cellpadding="0" cellspacing="0" id="Nome da Area" style="background-image: url(./simbolo2.jpg); background-attachment: fixed; background-repeat: no-repeat; background-position: 200 center;  background-scale:2" >
-->

<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="menu_esquerdo">
  <tr><td>&nbsp;</td></tr>
  <tr> 
    <td><! valign="top" class="MenuEsqDetalhe">
      <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr> 
          <td><!-- SE SUA IMAGEM NÃO APARECE NA VISUALIZAÇÃO DA SUA PÁGINA, VERIFIQUE SE O CAMINHO PARA A IMAGEM ESTÁ CORRETO, SE PREFERIR,  PROCURE UTILIZAR O CAMINHO ABSOLUTO PARA LOCALIZAÇÃO DESSA IMAGEM, POIS DESSA FORMA, MESMO ESTANDO EM NÍVEIS DIFERENTES NA HIERARQUIA DAS PASTAS DO SEU SITE A IMAGEM SEMPRE SERÁ EXIBIDA NA VISUALIZAÇÃO NO NAVEGADOR (EXEMPLO: HTTP://WWW.UNESP.BR/PADRAO/IMAGENS/SPACER.GIF) - NA DÚVIDA, ENTRE EM CONTATO COM O ADMINISTRADOR DE REDE --><img src="imagens/spacer.gif" width="50" height="2"></td>
        </tr>
        <tr> 
          <td>
            <select name="select2" class="itens-form" onChange="MM_jumpMenu('parent',this,1)">
              <option selected>Site Map</option>			                
              <option value="<?php print('/index.php'); ?>">- Home</option>
              <option value="<?php print('/research.php'); ?>">- Research</option>
              <option value="<?php print('/projects.php'); ?>">- Current Projects</option>
              <option value="<?php print('/teams.php'); ?>">- Staff</option>
              <option value="<?php print('/publication.php'); ?>">- Publications</option> 
              <option value="<?php print('/address.php'); ?>">- Addresses</option>
              <option value="<?php print('/address.php'); ?>">- Downloads</option>
            </select>         			
          </td>
        </tr>
      </table></td>
  </tr>
  <tr><td>&nbsp;</td></tr>
  <tr>
    <td>
      <ul id="nav">
        <li><a href="index.php" class="COMdestaque"  >Home</a>
        </li>
        <li><a href="research.php" class="COMdestaque"  >Research</a>
        </li>
        <li><a href="projects.php" class="COMdestaque"  >Current Projects</a>
        </li>
        <li><a href="teams.php"  class="COMdestaque"  >Staff</a></li>
        </li>
        <li><a href="publication.php" class="COMdestaque"  >Publications</a>
        </li>
        <li><a href="address.php" class="COMdestaque"  >Contact Addresses</a>
        </li>
        <li><a href="downloads.php" class="COMdestaque"  >Downloads</a>
        </li>
        <li><a href="internals.php" class="COMdestaque"  >Internal Sources</a>
        </li>
        <li>&nbsp;</li>
        <li><a href="pindex.php" class="COMdestaque"  ><img src="wbrazil.gif"> Esta página em português</a>
        </li>
        <li>&nbsp;</li>
        <li><div align="center"><a href="http://cradsp.sbc.org.br/eradsp" target="_blank" class="RodapeTexto"><font size=+0><img src="logo-erad-sp.png"></font></a></div></li>
      </ul>
    </td>
  </tr>
</table>


<!-- PARTE 2 PHP: DEFININDO OS ITENS PRINCIPAIS COM SUBITENS - UTILIZAR CUIDADOSAMENTE, UTILIZE O MANUAL -->

<script language="JavaScript" type="text/JavaScript">
function instituicao(){
    if(document.formmenu_esq.menu_esq1.value != 1 ){
	 	document.formmenu_esq.menu_esq1.value = 1;
     	document.formmenu_esq.submit();
	}else{
		document.formmenu_esq.menu_esq1.value = "";
     	document.formmenu_esq.submit();
	}
}
function docentes(){
    if(document.formmenu_esq.menu_esq1.value != 2 ){
	 	document.formmenu_esq.menu_esq1.value = 2;
     	document.formmenu_esq.submit();
	}else{
		document.formmenu_esq.menu_esq1.value = "";
     	document.formmenu_esq.submit();
	}
}
function normas(){
    if(document.formmenu_esq.menu_esq1.value != 7 ){
	 	document.formmenu_esq.menu_esq1.value = 7;
     	document.formmenu_esq.submit();
	}else{
		document.formmenu_esq.menu_esq1.value = "";
     	document.formmenu_esq.submit();
	}
}
function formularios(){
    if(document.formmenu_esq.menu_esq1.value != 8 ){
	 	document.formmenu_esq.menu_esq1.value = 8;
     	document.formmenu_esq.submit();
	}else{
		document.formmenu_esq.menu_esq1.value = "";
     	document.formmenu_esq.submit();
	}
}
function posgraduacao(){
    if(document.formmenu_esq.menu_esq1.value != 3 ){
	 	document.formmenu_esq.menu_esq1.value = 3;
     	document.formmenu_esq.submit();
	}else{
		document.formmenu_esq.menu_esq1.value = "";
     	document.formmenu_esq.submit();
	}
}
function pesquisa(){
    if(document.formmenu_esq.menu_esq1.value != 4 ){
	 	document.formmenu_esq.menu_esq1.value = 4;
     	document.formmenu_esq.submit();
	}else{
		document.formmenu_esq.menu_esq1.value = "";
     	document.formmenu_esq.submit();
	}
}
function extensao(){
    if(document.formmenu_esq.menu_esq1.value != 5 ){
	 	document.formmenu_esq.menu_esq1.value = 5;
     	document.formmenu_esq.submit();
	}else{
		document.formmenu_esq.menu_esq1.value = "";
     	document.formmenu_esq.submit();
	}
}
function selecao(){
    if(document.formmenu_esq.menu_esq1.value != 9 ){
	 	document.formmenu_esq.menu_esq1.value = 9;
     	document.formmenu_esq.submit();
	}else{
		document.formmenu_esq.menu_esq1.value = "";
     	document.formmenu_esq.submit();
	}
}
function servicos(){
    if(document.formmenu_esq.menu_esq1.value != 6 ){
	 	document.formmenu_esq.menu_esq1.value = 6;
     	document.formmenu_esq.submit();
	}else{
		document.formmenu_esq.menu_esq1.value = "";
     	document.formmenu_esq.submit();
	}
}

</script>
</form>
