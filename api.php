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

	static function send($url,$pole,$method="P"){
		//Odesle požadavek na server a zpracuje odpoved
		
		$GET = "";
		$ch = curl_init(); 
		
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HEADER, false);
		if($method == "P"){
			$postData = array();
			$postData["token"] = SEMOR_TOKEN;//Jedineèný token, je pøidìlován každému zájemci o API
			$postData["data"] = $pole;
			curl_setopt($ch,CURLOPT_POST, count($postData));
			curl_setopt($ch,CURLOPT_POSTFIELDS, $postData);   
		}else{
			$GET = "?";
			$GET.= "token="+SEMOR_TOKEN;
			$GET.= "&".$pole;
		}
		curl_setopt($ch,CURLOPT_URL,$url."/".$GET);
		$output=curl_exec($ch);
		curl_close($ch);
		return (!SEMOR::$jsonOutput) ? json_decode($output,true) : $output;//dle nastavení jsonOutput vrací hodnoty json/array
	}

	static function Data($data){
		if(is_array($data) && count($data)!=0){
			if($this->method == "P"){
				return json_encode($data);
			}else{
				$prop = array();
				foreach($data as $i => $o){
					$prop[] = $i."=".$o;
				}
				return join("&",$prop);
			}
		}else{
			echo "Data v požadavku nejsou vyplnìna!";
			return;
		}
	}

	static function PutProject($pole){
		/*
		$pole["url"] - www projektu
		$pole["https"] - bìží na https A/N
		$pole["typ"] - typ projektu 
						S - mìøení každý den
						R - mìøení na vyžádání
		$pole["engine"] - GS - Google, Seznam
						 GB - Google, Bing
		$pole["jazyk"] - urèeuje jazyk pro Google/Bing 
						cz - èesky
						sk - slovenky
						de - nemecky
						pl - polsky
		*/
		return SEMOR::send(SEMOR::$server."PutProject",SEMOR::Data($pole));
	}
}
?>