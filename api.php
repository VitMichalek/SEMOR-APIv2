<?
include "config.php";
class SEMOR{
	static $jsonOutput = false; //defaultne vraci vysledek jako JSON, false => vrací Array()
	static $server = "https://www.semor.cz/api/"; 

	public function __construct(){
		SEMOR::testToken();
	}

	static function testToken(){
		if(strlen(SEMOR_TOKEN) != 45) {
			echo "Chybnì zadaný token. Zkontrolujte své nastavení v config.php";
			return;
		}
	}

	
}
?>