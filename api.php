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
						seznam.cz - pro všechny subdomeny
						neco.seznam.cz jen pro subdomenu neco
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
						
						
		//vrací ID projektu, pro další komunikace
		*/
		return SEMOR::send(SEMOR::$server."PutProject",SEMOR::Data($pole));
	}

	static function MeasureProject($pole){
		/*
		Využívá se jen pro projekty typu R - mìøení na vyžádání
		$pole["idp"] - ID projektu

		Pokud se v dany den spusteni projekt nemeril zacnou se merit pozice, pokud se uz meril nebo meri vrati se o tom informace
		*/
		return SEMOR::send(SEMOR::$server."MeasureProject",SEMOR::Data($pole));
	}

	static function GetProjectList(){
		//Výpis všech projektù pro daný token
		return SEMOR::send(SEMOR::$server."GetProjectList","{}","G");
	}

	static function PutKeyword($pole){
		//Založení/Update slov
		/*
		$pole["idp"] - ID projektu
		$pole["keyword"][] - pole klíèových slov
		$pole["keyword"][0][0] = "slovo"; // klíèové slovo
		$pole["keyword"][0][1] = "A"; // stav A- aktivni, N-nekativni
		$pole["keyword"][0][2] = 28; // frekvence mìøení
		$pole["keyword"][0][3] = array("stitek","neco2"); // štítky
		$pole["keyword"][0][4] = 5;// vaše interní ID pro klíèové slovo, pres toho ID mùžete pak volat statistiky pro jednotlivá slova
		$pole["keyword"][1][0] = "slovo 2";
		$pole["keyword"][1][1] = "A";
		$pole["keyword"][1][2] = 7;
		$pole["keyword"][1][3] = array();
		//frekvence mìøení (28 - 1x za 28 dní, 14 - 1x za 14 dní, 7 - každý týden, 1 - každý den)
		Pokud bude dodateènì vložené klíèové slov již v systému, bude jeho nastaveni frekvence mìøení/aktivnita/štítky nastaveny dle posledního zaslání
		
		*/
		return SEMOR::send(SEMOR::$server."PutKeyword",SEMOR::Data($pole));
	}
	
	static function GetProjectIndex($pol){
		//Vrací informace o indexovaných stránkách
		/*
		$pole["idp"] - ID projektu
		$pole["datum_od"]  = "YYYY-MM-DD"; 
		$pole["datum_do"]  = "YYYY-MM-DD";
		pokud nejsou datumy nastaveny,vybere se posledních 30 dni
		*/
		
		return SEMOR::send(SEMOR::$server."GetProjectIndex",SEMOR::Data($pole),"G");
	}
	
	static function GetProjectKeys($pole){
		//Základní statistiky o klíèových slovech po posledním mìøení
		// datum mereni,pozice, vstupni stranka,stitky
		/*
		$pole["idp"] - ID projektu
		
		*/
		return SEMOR::send(SEMOR::$server."GetProjectKeys",SEMOR::Data($pole));
	}
	
	static function GetKeywordStat($pole){
		//Vrátí pozice pro každý den mìøení pro 1 vybrané slovo
		/*
		$pole["idp"] - ID projektu
		$pole["idk"] - ID klíèového slova ze SEMOR.cz
			nebo
		$pole["idki"] - Vaše interní ID
		$pole["datum_od"]  = "YYYY-MM-DD";
		$pole["datum_do"]  = "YYYY-MM-DD";
		pokud nejsou datumy nastaveny,vybere se posledních 30 dni
		
		*/
		return SEMOR::send(SEMOR::$server."GetKeywordStat",SEMOR::Data($pole),"G");
	}
	
	
}
?>