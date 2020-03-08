<?php
/***********************************************************************
 * # @Author Mark Firman
 * # @Project Dictionary API V2
 * # @Date 23/05/2019
 * # @Email mark.firman@me.com
 * # @Last Modified 07/03/2020
 */

class Dictionary {
	
	/// API Variables
	// The APP ID and KEY provided by (https://developer.oxforddictionaries.com/)
	protected $APP_ID;
	protected $APP_KEY;
	// The API URL
	private $API_URL = "https://od-api.oxforddictionaries.com/api/v2";
	// The API Language
	public $API_LANG;
	
	/// Data variables
	// The RAW JSON response
	public $json_data;
	// The result array
	public $result;
	
	// The selected result set
	public $selected_result;
	// Total number of available results in current result set
	public $num_returned_results;
	
	/// Class variables
	// Holds the last quieried word
	public $word;
	
	/// Dictionary variables
	public $definition;
	public $shortDefinition;
	public $example;
	public $phonetics;
	public $lexical;
	public $audio;
	public $origin;
	public $language;
	
	/// Thesaurus variables
	public $synonyms;
	public $antonyms;
	
	/// Inflection variables
	public $inflections;
	
	/// Translation variables
	public $translations;
	
	/// Types of API entries defined as const
	private $type;
	const DICTIONARY = 'dictionary';
	const THESAURUS = 'thesaurus';
	const TRANSLATION = 'translation';
	const INFLECTION = 'inflection';

	/// Error handling
	// Holds status' and errors
	public $errors;
	// Determines whether errors are shown
	public $show_errors;
	// The local audio file to use if audio pronunciation cannot be found
	public $no_audio_file = "./no_audio.mp3";
	
	/// =====================================================================================
	/// Constructor   : Intialises the class and its settings
	/// $_app_id      : the application id
	/// $_app_key     : the application key
	/// $_app_lang    : the application lang
	/// $_show_errors : (Optional) determines whether API errors are visible - true by default
	/// =====================================================================================
	function __construct($_app_id, $_app_key, $_app_lang, $_show_errors = true){
		$this->APP_ID = $_app_id;
		$this->APP_KEY = $_app_key;
		$this->API_LANG = $_app_lang;
	}
	
	///=====================================================================================
	/// PUBLIC METHODS : Use these methods to send and recieve data between server and API
	/// =====================================================================================
	
	///=====================================================================================
	/// DICTIONARY : Use these methods to send and recieve dictionary requests
	/// =====================================================================================
	/// =====================================================================================
	/// newDictionaryRequest() : initiates a new API request to pull dictionary data
	/// $_word       : the new word
	/// $_lang	     : (Optional) the language to query (defaults to language used when initialising class if not set)
	/// =====================================================================================
	public function newDictionaryRequest($_word, $_lang = null){
		
		// Set the type to dictionary
		$this->type = self::DICTIONARY;
		
		// Perform API request
		return $this->requestAPI($_word, $_lang, $this->API_URL."/entries/".$this->API_LANG."/".$_word);
		
	}
	
	/// =====================================================================================
	/// changeDictionaryResultSet() : change the result set to use
	/// Some words in the dictionary have more than 1 entry available - use this method to cycle results
	/// =====================================================================================
	public function changeDictionaryResultSet($result_set){
		
		// Set the selected result set
		$this->selected_result = (isset($this->json_data->results[$result_set]) ? $result_set : 0);
		
		// As the result set changed we need to re-decode the data
		$this->decodeDictionaryData();
	}

	/// =====================================================================================
	/// getDefinition() : returns the definition of the current word
	/// $result_set 	: to specify definition result to return (some words will have more than 1 definition available)
	/// If a definition cannot be found using the provided count then use the first element in the result array
	/// =====================================================================================
	public function getDefinition($result_set = 0){
		
		// Check if the result is found
		if(isset($this->definition[$result_set])){
			return $this->definition[$result_set];
		}
		
		// If the users requested result set is not found, try the default
		if($result_set != 0 && isset($this->definition[0])){
			return $this->definition[0];
		}
		
		/// This code is only reachable if the definition text is not found
		return "<i>Oops</i> - a definition for ". $this->word . " cannot be found";	
	}
	
	/// =====================================================================================
	/// getShortDefinition() : returns the short definition of the current word
	/// $result_set 		 : to specify which short definition to return 
	/// If a short definition cannot be found using the provided count then the first element in the result array is used
	/// =====================================================================================
	public function getShortDefinition($result_set = 0){
		
		// Check if the result is found
		if(isset($this->shortDefinition[$result_set])){
			return $this->shortDefinition[$result_set];
		}
		
		// If the users requested result set is not found, try the default
		if($result_set != 0 && isset($this->shortDefinition[0])){
			return $this->shortDefinition[0];
		}
		
		/// This code is only reachable if the short definition text is not found
		return "<i>Oops</i> - a short definition for ". $this->word . " cannot be found";	
	}
	
	/// =====================================================================================
	/// getExample() : returns an example case of the current in-use word
	/// $result_set  : to specify the example to return
	/// If example text cannot be found using the provided count then the first element in the result array is used
	/// =====================================================================================
	public function getExample($result_set = 0){
		
		// Check if the result is found
		if(isset($this->example[$result_set])){
			return $this->example[$result_set];
		}
		
		// If the users requested result set is not found, try the default
		if($result_set != 0 && isset($this->example[0])){
			return $this->example[0];
		}
		
		/// This code is only reachable if example text is not found
		return "<i>Oops</i> - an example for ". $this->word . " cannot be found";	
	}
	
	/// =====================================================================================
	/// getPhonetic() : returns the phonetic of the current word
	/// $result_set  : to specify the phonetic to return
	/// If example text cannot be found using the provided count then the first element in the result array is used
	/// =====================================================================================
	public function getPhonetic($result_set = 0){
		
		// Check if the result is found
		if(isset($this->phonetics[$result_set])){
			return $this->phonetics[$result_set];
		}
		
		// If the users requested result set is not found, try the default
		if($result_set != 0 && isset($this->phonetics[0])){
			return $this->phonetics[0];
		}
		
		/// This code is only reachable if phonetic text is not found
		return "<i>Oops</i> - the phonetic for ". $this->word . " cannot be found";
		
	}
	
	/// =====================================================================================
	/// getLexical() : returns the lexical category of the current word
	/// $result_set  : to specify the lexical to return
	/// If example text cannot be found using the provided count then the first element in the result array is used
	/// =====================================================================================
	public function getLexical($result_set = 0){
		
		// Check if the result is found
		if(isset($this->lexical[$result_set])){
			return $this->lexical[$result_set];
		}
		
		// If the users requested result set is not found, try the default
		if($result_set != 0 && isset($this->lexical[0])){
			return $this->lexical[0];
		}
		
		/// This code is only reachable if lexical text is not found
		return "<i>Oops</i> - the lexical for ". $this->word . " cannot be found";
		
	}
	
	/// =====================================================================================
	/// getAudio() : returns the URL of the audio pronuciation
	/// $result_set  : to specify the audio to return
	/// If audio url cannot be found using the provided count then the first element in the result array is used
	/// =====================================================================================
	public function getAudio($result_set = 0){
		
		// Check if the result is found
		if(isset($this->audio[$result_set])){
			return $this->audio[$result_set];
		}
		
		// If the users requested result set is not found, try the default
		if($result_set != 0 && isset($this->audio[0])){
			return $this->audio[0];
		}
		
		/// This code is only reachable if audio text is not found
		// Sets the audio URL to the URL on the local server
		return $this->no_audio_file;
		
	}
	
	/// =====================================================================================
	/// getOrigin() : Returns the origin of the current word
	/// $result_set  : To specify the origin to return
	/// If origin cannot be found using the provided count then the first element in the result array is used
	/// =====================================================================================
	public function getOrigin($result_set = 0){
		
		// Check if the result is found
		if(isset($this->origin[$result_set])){
			return $this->origin[$result_set];
		}
		
		// If the users requested result set is not found, try the default
		if($result_set != 0 && isset($this->origin[0])){
			return $this->origin[0];
		}
		
		/// This code is only reachable if origin text is not found
		return "<i>Oops</i> - the origin for ". $this->word . " cannot be found";
	}
	
	///=====================================================================================
	/// INFLECTIONS : Use these methods to send and recieve inflection requests
	/// =====================================================================================
	public function newInflectionRequest($_word, $_lang = null){
		
		// Set the type to inflection
		$this->type = self::INFLECTION;
		
		// Perform the API request
		return $this->requestAPI($_word, $_lang, $this->API_URL."/inflections/".$this->API_LANG."/".$_word);
		
	}
	
	///=====================================================================================
	/// THESAURUS : Use these methods to send and recieve thesaurus requests
	/// $_word	  : Find words that are similar or opposite in meaning to the input word
	/// =====================================================================================
	public function newThesaurusRequest($_word, $_lang = null){
		
		// Set the type to thesaurus
		$this->type = self::THESAURUS;
		
		// Perform the API request
		return $this->requestAPI($_word, $_lang, $this->API_URL."/thesaurus/".$this->API_LANG."/".$_word);
	}
	
	///=====================================================================================
	/// TRANSLATION : Use these methods to send and recieve translation requests
	/// $_word		: The word to translate
	/// $_lang      : The target translation language. The current language is used as the source language
	/// =====================================================================================
	public function newTranslationRequest($_word, $_lang){
		
		// Set the type to translation
		$this->type = self::TRANSLATION;
		
		// Perform the API request
		return $this->requestAPI($_word, $this->API_LANG, $this->API_URL."/translations/".$this->API_LANG."/".$_lang."/".$_word);
	}
	
	///=====================================================================================
	/// PRIVATE METHODS : Used soley by the class, the user will never need to call the below
	/// =====================================================================================
	
	/// =====================================================================================
	/// handleRequestStatus() : checks the status of the request and handles errors
	/// $_status			  : the HTTP header line response status
	/// =====================================================================================
	private function handleRequestStatus($_header){
		
		// Extract the status line from the header
		preg_match('{HTTP\/\S*\s(\d{3})}', $_header, $match);	
		
		switch($match[1]){
			// 200 : successful request
			case 200:
				$this->errors['status'] = 200;
				$this->errors['message'] = "Connected!";
			break;
			// 400 : bad request (Word or language either not found, or incorrect)
			case 400:
				$this->errors['status'] = 400;
				$this->errors['message'] = "Bad Request";
			break;
			// 403 : Authentication failed (APP ID or KEY incorrect)
			case 403:
				$this->errors['status'] = 403;
				$this->errors['message'] = "Authentication failed";
			break;
			// 404: Not found (Incorrect API URL)
			case 404:
				$this->errors['status'] = 404;
				$this->errors['message'] = "Not found";
			break;
			// 414 : Request too long (Word or language exceeds 128 characters)
			case 414:
				$this->errors['status'] = 414;
				$this->errors['message'] = "Request too long";
			break;
			// 500 : Internal server error (This is API side error)
			case 500:
				$this->errors['status'] = 500;
				$this->errors['message'] = "Internal server error";
			break;
			// 504 : Bad gateway (API is down)
			case 502:
				$this->errors['status'] = 502;
				$this->errors['message'] = "Bad Gateway";
			break;
			// 505 : Service Unavailble (API is down)
			case 503:
				$this->errors['status'] = 503;
				$this->errors['message'] = "Service Unavailable";
			break;
			// 504 : Gateway timeout (The API did not reply in time)
			case 504:
				$this->errors['status'] = 504;
				$this->errors['message'] = "Gateway timeout";
			break;
		}
		
		// The request returned an error status
		// Check if errors are turned on
		if($this->show_errors && $this->errors['status'] != 200){
			echo "An error occured: ".$this->errors['status']. " - ".$this->errors['message'];
		}
	}
	
	/// =====================================================================================
	/// checkNumberOfResultSets() : handles result sets
	/// Initiates the default result set to use and gets the total number of availble results
	/// =====================================================================================
	private function checkNumberOfResultSets(){
		
		// Set the result set to use : 0 by default
		// 0 will reference the first element in the result array
		$this->selected_result = 0;
		
		// The total number of results found in the result array
		$this->num_returned_results = count($this->json_data->results);
		
	}
	
	/// =====================================================================================
	/// decodeDictionaryData() : Decodes the dictionary JSON data
	/// =====================================================================================
	private function decodeDictionaryData(){
		
		// Check the status of the request
		// Anything other than 200 is a bad request
		// We do not want to waste resources attemping to decode JSON that is invalid
		if($this->errors['status'] == 200){
			
			// Counter to iterate through results
			$counter = 0;
			
			// Loop over JSON result set to get all results
			for($i = 0; $i < 3; $i++){
				
				// Decode the definitions
				if(isset($this->json_data->results[$this->selected_result]->lexicalEntries[0]->entries[0]->senses[0]->definitions[$counter])){
					$this->definition[$counter] = $this->json_data->results[$this->selected_result]->lexicalEntries[0]->entries[0]->senses[0]->definitions[$counter];
				}
				
				// Decode the short definitions
				if(isset($this->json_data->results[$this->selected_result]->lexicalEntries[0]->entries[0]->senses[0]->shortDefinitions[$counter])){
					$this->shortDefinition[$counter] = $this->json_data->results[$this->selected_result]->lexicalEntries[0]->entries[0]->senses[0]->shortDefinitions[$counter];
				}
				
				// Decode the examples
				if(isset($this->json_data->results[$this->selected_result]->lexicalEntries[0]->entries[0]->senses[0]->examples[$counter]->text)){
					$this->example[$counter] = $this->json_data->results[$this->selected_result]->lexicalEntries[0]->entries[0]->senses[0]->examples[$counter]->text;
				}
				
				// Decode the phonetic (US Finetic)
				if(isset($this->json_data->results[$this->selected_result]->lexicalEntries[0]->pronunciations[$counter]->phoneticSpelling)){
					$this->phonetics[$counter] = $this->json_data->results[$this->selected_result]->lexicalEntries[0]->pronunciations[$counter]->phoneticSpelling;
				}	
				
				// Decode the lexical entry
				if(isset($this->json_data->results[$this->selected_result]->lexicalEntries[$counter]->lexicalCategory->text)){
					$this->lexical[$counter] = $this->json_data->results[$this->selected_result]->lexicalEntries[$counter]->lexicalCategory->text;
				}
				
				// Decode the Audio
				if(isset($this->json_data->results[$this->selected_result]->lexicalEntries[0]->pronunciations[$counter]->audioFile)){
					$this->audio[$counter] = $this->json_data->results[$this->selected_result]->lexicalEntries[0]->pronunciations[$counter]->audioFile;
				}
		
				// Decode the Origin
				if(isset($this->json_data->results[$this->selected_result]->lexicalEntries[0]->entries[0]->etymologies[$counter])){
					$this->origin[$counter] = $this->json_data->results[$this->selected_result]->lexicalEntries[0]->entries[0]->etymologies[$counter];
				}
				
				// Increment the counter
				$counter++;
			}
		} 
	}
	
	/// =====================================================================================
	/// decodeInflectionData() : Decodes the inflection JSON data
	/// =====================================================================================
	private function decodeInflectionData(){
		
		// Check the status of the request
		// Anything other than 200 is a bad request
		// We do not want to waste resources attemping to decode JSON that is invalid
		if($this->errors['status'] == 200){
			
			// Iterate through results
			for($i = 0; $i < count($this->json_data->results[0]->lexicalEntries); $i++){
				
				// Store the inflection in the class variable
				$this->inflections[$i] = ($this->json_data->results[0]->lexicalEntries[$i]->inflections[0]->inflectedForm == $this->word ? $this->json_data->results[0]->lexicalEntries[$i]->inflections[1]->inflectedForm : $this->json_data->results[0]->lexicalEntries[$i]->inflections[0]->inflectedForm);
			}
		}
		
		
	}
	
	/// =====================================================================================
	/// decodeTranslationData() : Decodes the inflection JSON data
	/// =====================================================================================
	private function decodeTranslationData(){
		
		// Check the status of the request
		// Anything other than 200 is a bad request
		// We do not want to waste resources attemping to decode JSON that is invalid
		// Store the translation in the class variable
		$this->translations = $this->json_data->results[0]->lexicalEntries[0]->entries[0]->senses[0]->translations[0]->text;
			
	}
	
	/// =====================================================================================
	/// decodeThesaurusData() : Decodes the inflection JSON data
	/// =====================================================================================
	private function decodeThesaurusData(){
		
	}
	
	/// =====================================================================================
	/// requestAPI() : Performs the actual API request
	/// =====================================================================================
	private function requestAPI($_word, $_lang = null, $_url){
		
		// Store the new word
		$this->word = strtolower($_word);
		
		// Check if there is a value in $_lang
		// If so, we need to ensure we use the language requested
		$this->API_LANG = ($_lang == null ? $this->API_LANG : $_lang);
		
		// Create HTTP header array
		// This is the HTTP header thats sent to the API URL when the request takes place
		// The APP ID and KEY are used here to authenticate the requested - an Oxford Dictionaries user account is required
		// (https://developer.oxforddictionaries.com/)
		$options = array(
				'http' => array(
						'method' => "GET",
						'header' => "app_id:".$this->APP_ID."\r\n" .
									"app_key:".$this->APP_KEY."\r\n" .
									"Content-Type: application/json"
								
			)
		);
		
		// Create the request stream
		$context = stream_context_create($options);
		
		// Perform the request
		$result = @file_get_contents($_url, false, $context);
		
		// Check the returned status of the request and handle any errors
		$this->handleRequestStatus($http_response_header[0]);
	
		// Store the RAW json data
		// This allows the use of raw queries
		$this->json_data = json_decode($result);

		// Determine call type
		switch($this->type){
			
			case self::DICTIONARY:
			
				// Check how many results are returned
				// Some words may have multiple definitions and therefore mulitple result sets
				// An example of a word with multiple definitions : Bark - the stuff on trees or the noise a dog makes
				$this->checkNumberOfResultSets();
				
				// Force the dictionary JSON array to be decoded
				// This extracts the data from the returned JSON array into the class variables
				$this->decodeDictionaryData();
			
			break;
			case self::INFLECTION:
			
				// Force the inflection JSON array to be decoded
				$this->decodeInflectionData();
			
			break;
			case self::THESAURUS:
			
				// Force the thesaurus JSON array to be decoded
				$this->decodeThesaurusData();
			
			break;
			case self::TRANSLATION:
			
				// Force the translation JSON array to be decoded
				$this->decodeTranslationData();
			
			break;
		}
		
		// Return the raw JSON data so that direct calls can be made
		return $result;
	}
	
}
?>
