<?
include_once("SemorAPI.php");

//v SemorConfig.php nastavit TOKEN

$pole = array();
$pole["url"] = "www.semor.cz";
$pole["https"] = "A";
$pole["typ"] = "S";//měřím pole zadané frekvence u slov
$pole["engine"] = "GS";
$pole["jazyk"] = "cz";

$odpoved = SEMOR::PutProject($povel);
/*
Odpovědí je pole
*/
if(isset($odpoved["error"])){
	/*Došlo k nějaké chybě*/
}else{
	/*Vše je v pořádku*/
	$idProjektu = $odpoved["idp"]; // toto ID slouží k další komunikaci
}
?>