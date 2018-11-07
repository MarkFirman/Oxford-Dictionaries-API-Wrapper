<?php
/* Turn off error reporting */
error_reporting(0);

class dictionary{
		
	/* Variables */
	private $api_url = "https://od-api.oxforddictionaries.com/api/v1";
	private $lang;
	private $app_key;
	private $app_id;
	private $data;
		
	/* Constructor */
	function __construct($app_key, $app_id, $lang){
		$this->app_key = $app_key;
		$this->app_id = $app_id;
		$this->lang = $lang;
	}	
	
	/* Returns a JSON encoded object */
	function getWord($word){
		
		$options = array(
				'http' => array(
						'method' => "GET",
						'header' => "app_id:".$this->app_id."\r\n" .
									"app_key:".$this->app_key."\r\n" .
									"Content-Type: application/json"
								
			)
		);
		
		$context = stream_context_create($options);
		$result = file_get_contents($this->api_url."/entries/".$this->lang."/".$word, false, $context);
		$this->data = json_decode($result);
		return json_decode($result);
	}
	
	/* Returns the API provider */
	function getProvider(){
		return $this->data->metadata->provider;
	}
	
	/* Returns the language the API searched in */
	function getLanguage(){
		return $this->data->results[0]->language;	
	}
	
	/* Returns a derivative (Word made or developed from another word) */
	function getDerivative(){
		return $this->data->results[0]->lexicalEntries[0]->derivatives[0]->id;
	}
	
	/* Returns the origin of the word */
	function getOrigin(){
		return $this->data->results[0]->lexicalEntries[0]->entries[0]->etymologies[0];
	}
	
	/* Returns the definition of the word */
	/* Somes words may have multiple definitions which can be accessed using the var $count) */
	/* When $count is NULL or 0, the first obtainable definition is returned. $count = 1, the second availble definition ect (Upto 2)*/
	function getDefinition($count = 0){
		switch($count){
			case 0:
				return $this->data->results[0]->lexicalEntries[0]->entries[0]->senses[0]->definitions[0];
			break;
			case 1:
				return $this->data->results[0]->lexicalEntries[0]->entries[0]->senses[1]->definitions[0];
			break;
			case 2:
				return $this->data->results[0]->lexicalEntries[0]->entries[0]->senses[2]->definitions[0];
			break;
		}
	}
	
	/* Returns the short definition of the word */
	function getShortDefinition($count = 0){
		switch($count){
			case 0:
				return $this->data->results[0]->lexicalEntries[0]->entries[0]->senses[0]->short_definitions[0];
			break;
			case 1:
				return $this->data->results[0]->lexicalEntries[0]->entries[0]->senses[1]->short_definitions[0];
			break;
			case 2:
				return $this->data->results[0]->lexicalEntries[0]->entries[0]->senses[2]->short_definitions[0];
			break;
		}
	}
	
	/* Returns example text */
	function getExampleText($count = 0){
		switch($count){
			case 0:
				return $this->data->results[0]->lexicalEntries[0]->entries[0]->senses[0]->examples[0]->text;
			break;
			case 1:
				return $this->data->results[0]->lexicalEntries[0]->entries[0]->senses[1]->examples[0]->text;
			break;
			case 2:
				return $this->data->results[0]->lexicalEntries[0]->entries[0]->senses[2]->examples[0]->text;
			break;
		}
	}

	/* Returns the audio file for pronouncing the word in URL format */
	function getAudioFile($count = 0){
		switch($count){
			case 0:
				return $this->data->results[0]->lexicalEntries[0]->pronunciations[0]->audioFile;
			break;
			case 1:
				return $this->data->results[0]->lexicalEntries[0]->pronunciations[1]->audioFile;
			break;
			case 2:
				return $this->data->results[0]->lexicalEntries[0]->pronunciations[2]->audioFile;
			break;
		}
	}

	/* Checks the result before it is returned to ensure its authenticity and prevent errors */
	function checkValidity(){
		
	}
}

?>