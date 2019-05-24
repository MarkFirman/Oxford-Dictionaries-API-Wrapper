<?php
/***********************************************************************
 * # @Author Mark Firman
 * # @Project Dictionary API V2
 * # @Date 23/05/2019
 * # @Email info@markfirman.co.uk
 * # @Last Modified 24/05/2019
 */

class Dictionary {
	
	/// API Variables
	protected $APP_ID;
	protected $APP_KEY;
	private $API_URL = "https://od-api.oxforddictionaries.com/api/v2";
	public $API_LANG;
	
	/// Data variables
	public $json_data;
	public $result;
	public $selected_result;
	
	/// Class variables
	public $definition;
	public $shortDefinition;
	public $example;
	public $phonetic;
	public $lexical;
	public $audio;
	public $origin;
	public $language;
	
	/// Error handling
	public $errors;
	
	/// Constructor - Intialises the class and its settings
	function __construct($_app_id, $_app_key, $_app_lang){
		$this->APP_ID = $_app_id;
		$this->APP_KEY = $_app_key;
		$this->API_LANG = $_app_lang;
	}
	
	/// Sends a new word to the dictionary API
	public function queryWord($_word){
		
		/* Create HTTP header array */
		$options = array(
				'http' => array(
						'method' => "GET",
						'header' => "app_id:".$this->APP_ID."\r\n" .
									"app_key:".$this->APP_KEY."\r\n" .
									"Content-Type: application/json"
								
			)
		);
		
		/* Send query to API */
		$context = stream_context_create($options);
		$result = @file_get_contents($this->API_URL."/entries/".$this->API_LANG."/".$_word, false, $context);
		
		/* Check the status - set page to error, if response is not 200 */
		$status_line = $http_response_header[0];
		preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
		$status = $match[1];
		
		/* Set the word */
		$this->word = $_word;
		
		/* Set repsonse for different error messages */
		switch($status){
			case 200:
				$this->errors['status'] = 200;
				$this->errors['message'] = "Connected!";
			break;
			case 400:
				/* Bad Request */
				$this->errors['status'] = 400;
				$this->errors['message'] = "Bad Request";
			break;
			case 403:
				/* Authentication failed */
				$this->errors['status'] = 403;
				$this->errors['message'] = "Authentication failed";
			break;
			case 404:
				/* Not found */
				$this->errors['status'] = 404;
				$this->errors['message'] = "Not found";
			break;
			case 414:
				/* Request too long (exceeds 128 characters */
				$this->errors['status'] = 414;
				$this->errors['message'] = "Request too long";
			break;
			case 500:
				/* Internal server error */
				$this->errors['status'] = 500;
				$this->errors['message'] = "Internal server error";
			break;
			case 502:
				/* Bad Gateway - API is down */
				$this->errors['status'] = 502;
				$this->errors['message'] = "Bad Gateway";
			break;
			case 503:
				/* Service unavailable - API is down */
				$this->errors['status'] = 503;
				$this->errors['message'] = "Service Unavailable";
			break;
			case 504:
				/* Gateway timeout */
				$this->errors['status'] = 504;
				$this->errors['message'] = "Gateway timeout";
			break;
		}
		
		/* Store the JSON data */
		$this->json_data = json_decode($result);
		
		/* Set the default result set */
		$this->selected_result = 0;
		
		/* Force the JSON string to be decoded and stored in class variables */
		$this->decode();
		
		/* Return the encoded JSON string so that direct calls can be made */
		return json_decode($result);
	}
	
	/// Decodes the data
	private function decode(){
		/* Check the status of the request
		 * Anything other than 200 is a bad request */
		if($this->errors['status'] == 200){
			/* Decode the JSON data */
			$this->decodeDefinition();
			$this->decodeShortDefinition();
			$this->decodeExample();
			$this->decodePhonetic();
			$this->decodeLexical();
			$this->decodeAudio();
			$this->decodeOrigin();
		} else {
			/* Show error status and message */
			echo "An error occured: ".$this->errors['status']. " - ".$this->errors['message'];
		}
		
	}
	
	/// Decodes and stores the definitions
	private function decodeDefinition(){
		$count = 0;
		for($i = 0; $i < 3; $i++){
			if(isset($this->json_data->results[$this->selected_result]->lexicalEntries[0]->entries[0]->senses[0]->definitions[$count])){
				$this->definition[$count] = $this->json_data->results[$this->selected_result]->lexicalEntries[0]->entries[0]->senses[0]->definitions[$count];
			}
			$count++;
		}
	}
	
	/// Decodes and stores the short definitions
	private function decodeShortDefinition(){
		$count = 0;
		for($i = 0; $i < 3; $i++){
			if(isset($this->json_data->results[$this->selected_result]->lexicalEntries[0]->entries[0]->senses[0]->shortDefinitions[$count])){
				$this->shortDefinition[$count] = $this->json_data->results[$this->selected_result]->lexicalEntries[0]->entries[0]->senses[0]->shortDefinitions[$count];
			}		
			$count++;
		}
	}
	
	//// Decodes and stores the example 
	private function decodeExample(){
		$count = 0;
		for($i = 0; $i < 3; $i++){
			if(isset($this->json_data->results[$this->selected_result]->lexicalEntries[0]->entries[0]->senses[0]->examples[0]->text)){
				$this->example[$count] = $this->json_data->results[$this->selected_result]->lexicalEntries[0]->entries[0]->senses[0]->examples[0]->text;
			}		
			$count++;
		}
	}
	
	/// Decodes and stores the phonetic
	private function decodePhonetic(){
		if(isset($this->json_data->results[$this->selected_result]->lexicalEntries[0]->pronunciations[0]->phoneticSpelling)){
			$this->phonetic = $this->json_data->results[$this->selected_result]->lexicalEntries[0]->pronunciations[0]->phoneticSpelling;
		}	
	}
	
	/// Decodes and stores the lexical entry
	private function decodeLexical(){
		if(isset($this->json_data->results[$this->selected_result]->lexicalEntries[0]->lexicalCategory->text)){
			$this->lexical = $this->json_data->results[$this->selected_result]->lexicalEntries[0]->lexicalCategory->text;
		}	
	}
	
	/// Decodes and stores the audio URL
	private function decodeAudio(){
		if(isset($this->json_data->results[$this->selected_result]->lexicalEntries[0]->pronunciations[0]->audioFile)){
			$this->audio = $this->json_data->results[$this->selected_result]->lexicalEntries[0]->pronunciations[0]->audioFile;
		}
	}
	
	/// Decodes and store the origin
	private function decodeOrigin(){
		if(isset($this->json_data->results[$this->selected_result]->lexicalEntries[0]->entries[0]->etymologies[0])){
			$this->origin = $this->json_data->results[$this->selected_result]->lexicalEntries[0]->entries[0]->etymologies[0];
		}
	}
	
	/// Sets the result set to use
	public function setResult($result_set = 0){
		if(isset($this->json_data->results[$result_set])){
			$this->selected_result = $result_set;
		} else {
			$this->selected_result = 0;
		}
		$this->decode();
	}
	
	/// Returns the current in-use word
	public function getWord(){
		return $this->word;
	}
	
	/// Returns the definition of the current in-use word
	/// Use $count to specify definition to return (some words will have more than 1 definition available)
	/// If a definition cannot be found using the provided count, it will default to show the first
	public function getDefinition($count = 0){
		if(isset($this->definition[$count])){
			/* Return the chosen definition */
			return $this->definition[$count];
		} else {
			/// This code is only reachable if a definition is not found
			return "<i>Oops</i> - a definition for ". $this->word . " cannot be found";
		}
	}
	
	/// Returns the short definition of the current in-use word
	/// Use $count to specify the short definition to return (some words will have more than 1 short definition available)
	/// If a short definition cannot be found using the provided count, it will default to show the first
	public function getShortDefinition($count = 0){
		if(isset($this->shortDefinition[$count])){
			/* Return the chosen short definition */
			return $this->shortDefinition[$count];
		} else {
			/// This code is only reachable if a definition is not found
			return "<i>Oops</i> - a short definition for ". $this->word . " cannot be found";
		}		
	}
	
	/// Returns an example case of the current in-use word
	/// Use $count to specify the example to return (some words will have more than 1 example text available)
	/// If example text cannot be found using the provided count, it will default to show the first
	public function getExample($count = 0){
		if(isset($this->example[$count])){
			/* Return the chosen short definition */
			return $this->example[$count];
		} else {
			/// This code is only reachable if a definition is not found
			return "<i>Oops</i> - an example for ". $this->word . " cannot be found";
		}		
	}
	
	/// Returns the phonetic (Finetic for you 'Mericans) of the current in-use word
	public function getPhonetic(){
		if(isset($this->phonetic)){
			return $this->phonetic;
		} else {
			/// This code is only reachable if a definition is not found
			return "<i>Oops</i> - the lexical for ". $this->word . " cannot be found";
		}
	}
	
	/// Returns the lexical category of the current in-use word
	public function getLexical(){
		if(isset($this->lexical)){
			return $this->lexical;
		} else {
			/// This code is only reachable if a definition is not found
			return "<i>Oops</i> - the lexical for ". $this->word . " cannot be found";
		}
	}
	
	/// Returns the URL of the audio pronuciation
	public function getAudio(){
		if(isset($this->audio)){
			return $this->audio;
		} else {
			/// This code is only reachable if a definition is not found
			return "<i>Oops</i> - the audio for ". $this->word . " cannot be found";
		}
	}
	
	/// Returns the origin of the current in-use word
	public function getOrigin(){
		if(isset($this->origin)){
			return $this->origin;
		} else {
			/// This code is only reachable if a origin is not found
			return "<i>Oops</i> - the origin of ". $this->word . " cannot be found";
		}
	}
	
	/// Returns the current in-use language
	public function getLanguage(){
		return $this->API_LANG;
	}
	
}
?>