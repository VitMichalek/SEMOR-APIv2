<?
include "config.php";
class SEMOR{
	static $jsonOutput = false; //defaultne vraci vysledek jako JSON, false => vrac� Array()
	static $server = "https://www.semor.cz/api/"; 

	public function __construct(){
		SEMOR::testToken();
	}

	static function testToken(){
		if(strlen(SEMOR_TOKEN) != 45) {
			echo "Chybn� zadan� token. Zkontrolujte sv� nastaven� v config.php";
			return;
		}
	}

	static function send($url,$pole,$method="P"){
		//Odesle po�adavek na server a zpracuje odpoved
		
		$GET = "";
		$ch = curl_init(); 
		
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HEADER, false);
		if($method == "P"){
			$postData = array();
			$postData["token"] = SEMOR_TOKEN;//Jedine�n� token, je p�id�lov�n ka�d�mu z�jemci o API
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
		return (!SEMOR::$jsonOutput) ? json_decode($output,true) : $output;//dle nastaven� jsonOutput vrac� hodnoty json/array
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
			echo "Data v po�adavku nejsou vypln�na!";
			return;
		}
	}

	static function PutProject($pole){
		/*
		$pole["url"] - www projektu
		$pole["https"] - b�� na https A/N
		$pole["typ"] - typ projektu 
						S - m��en� ka�d� den
						R - m��en� na vy��d�n�
		$pole["engine"] - GS - Google, Seznam
						 GB - Google, Bing
		$pole["jazyk"] - ur�euje jazyk pro Google/Bing 
						cz - �esky
						sk - slovenky
						de - nemecky
						pl - polsky
		*/
		return SEMOR::send(SEMOR::$server."PutProject",SEMOR::Data($pole));
	}
}
?>