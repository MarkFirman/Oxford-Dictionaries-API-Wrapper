<?php
/***********************************************************************
 * # @Author Mark Firman
 * # @Project Dictionary API V2
 * # @Date 23/05/2019
 * # @Email mark.firman@me.com
 * # @Last Modified 07/03/2020
 */

class Dictionary {
	
	/* Error handling */
	/* Show errors? - can be set when initialising the class */
	private $showErrors;
	
	/* Holds status' and errors */
	/* @errors - contains all error responses from the script and API */
	public $errors;

	/* Oxford Dictionary APP ID and Key - DO NOT DIRECTLY INPUT YOUR APP ID OR KEY HERE */
	/* The APP ID and KEY are provided by https://developer.oxforddictionaries.com/ */
	protected $_appID;
	protected $_appKey;

	/* The API URL */
	/* This is the API URL as specified by Oxfords API documentation */
	private $_apiURL = "https://od-api.oxforddictionaries.com/api/v2";
	
	/* @_apiLanguage 
	 * Placeholder for the current language and is set when a new instance of this class is called
	 * Language can also be changed by calling the ChangeLanguage method
	*/
	public $_apiLanguage;
	
	/// Class variables
	/// ======================================================
	///
	/* Holds the last quieried word as sent by the user */
	/* Even if the word is invalid, it is still stored here */
	public $word;
	
	/* @resultSet - holds the current result set index
	* If a word has more than one meaning (eg bark) then multiple results are returned
	*/
	public $resultSet = 0;
	
	/* The total number of available results within the returned data set */
	public $totalResultCount;
	
	/// Dictionary variables
	/// ======================================================
	
	/* @definition - holds the definition of the current word
	 * defaults to null if api request or result set returns no or invalid response */
	public $definition;
	
	/* @shortDefinition - holds the short definition of the current word
	 * defaults to null if api request or result set returns no or invalid response */
	public $shortDefinition;
	
	/* @phonetic - holds the phonetic of the current word
	 * defaults to null if api request or result set returns no or invalid response */
	public $phonetic;
	
	/* @audioURL - holds the audio URL of the current word. MP3/4 format
	 * defaults to null if api request or result set returns no or invalid response */
	public $audioURL;
	
	/* @etymology - holds the etmology
	 * defaults to null if api request or result set returns no or invalid response */
	public $etymology;
	
	/* @dialect - the dialect of the pronunciation
	 * defaults to null if api request or result set returns no or invalid response */
	public $dialect;
	
	/* @phrase - shows the word used in a phrase
	 * defaults to null if api request or result set returns no or invalid response */
	public $phrase;
	
	/* @example - shows the word used in a sentence 
	 * defaults to null if api request or result set returns no or invalid response */
	public $example;
	
	/// =====================================================================================
	/// Constructor   : Intialises the class and its settings
	/// @param $app_id      : the application id
	/// @param $app_key     : the application key
	/// @param $app_lang    : the application lang
	/// @param $show_errors : (Optional) determines whether API errors are visible - false by default
	/// =====================================================================================
	function __construct($id, $key, $lang, $showErrors = false){
		$this->_appID = $id;
		$this->_appKey = $key;
		$this->_apiLanguage = $lang;
	}
	
	///=====================================================================================
	/// PUBLIC METHODS : Use these methods to send and recieve data between server and API
	///=====================================================================================
	
	///=====================================================================================
	/// DICTIONARY : Use these methods to send and recieve dictionary requests
	///=====================================================================================
	///=====================================================================================
	/// newRequest()       : initiates a new API request to pull dictionary data
	/// @param $word       : the word to request from the dictionary
	/// @param $lang	   : (Optional) the language to query (defaults to language used when initialising class if not set)
	///=====================================================================================
	public function newRequest($word, $lang = null){
		
		// Perform API request
		return $this->requestAPI($word, $lang, $this->_apiURL."/entries/".$this->_apiLanguage."/".$word);
		
	}
	
	///=====================================================================================
	/// changeResultSet() : changes the result set to use @param index instead
	/// @param index      : the result set to use 0 - 100
	/// Note: if the result set chosen does not exist, the class will increment down a set until a valid result is returned 
	///=====================================================================================
	public function changeResultSet($index){
		
		// Set the chosen result set to the given index
		$this->resultSet = $index;
		
		// Redecode the request using the new result set
		return $this->DecodeAPIRequest($this->jsonObject);
	}
	
	///=====================================================================================
	/// PRIVATE METHODS : Used soley by the class, the user will never need to call the below
	///=====================================================================================

	/// @param jsonObject - holds the decoded json object and is used when changing result sets
	private $jsonObject;
	
	///=====================================================================================
	/// handleStatus() 		: checks the status of the request and handles errors
	/// @param $header		: the HTTP header line response status
	///=====================================================================================
	private function handleStatus($header){
		
		// Extract the status line from the header
		preg_match('{HTTP\/\S*\s(\d{3})}', $header, $match);	
		
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
		if($this->showErrors && $this->errors['status'] != 200){
			echo "An error occured: ".$this->errors['status']. " - ".$this->errors['message'];
		}
	}
	

	/// =====================================================================================
	/// DecodeAPIRequest() : Decodes the JSON array returned from the API call
	/// @param $result     : The undecoded result from the API
	/// =====================================================================================
	private function DecodeAPIRequest($result){
		
		/* Set the number of available result sets */
		$this->totalResultCount = count($this->jsonObject->results);
		
		/* Check the selected result set returns data, otherwise iterate down results sets until 0 is reached */
		while(!isset($this->jsonObject->results[$this->resultSet])){
			if($this->resultSet == 0) { return $this->jsonObject; }
			$this->resultSet--;
		}
		
		/* Decode Etymology */
		if(isset($this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->entries[0]->etymologies[0])){
			$this->etymology = $this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->entries[0]->etymologies[0];
		} else {
			$this->etymology = null;
		}
		
		/* Decode Audio URL */
		if(isset($this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->entries[0]->pronunciations[0]->audioFile)){
			$this->audioURL = $this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->entries[0]->pronunciations[0]->audioFile;
		} else {
			$this->audioURL = null;
		}
		
		/* Decode Pronunciation Dialect */
		if(isset($this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->entries[0]->pronunciations[0]->dialects[0])){
			$this->dialect = $this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->entries[0]->pronunciations[0]->dialects[0];
		} else {
			$this->dialect = null;
		}
		
		/* Decode Phonetic Spelling */
		if(isset($this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->entries[0]->pronunciations[0]->phoneticSpelling)){
			$this->phonetic = $this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->entries[0]->pronunciations[0]->phoneticSpelling;
		} else {
			$this->phonetic = null;
		}
		
		/* Decode Definition */
		if(isset($this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->entries[0]->senses[0]->definitions[0])){
			$this->definition = $this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->entries[0]->senses[0]->definitions[0];
		} else {
			$this->definition = null;
		}
		
		/* Decode Short Definition */
		if(isset($this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->entries[0]->senses[0]->shortDefinitions[0])){
			$this->shortDefinition = $this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->entries[0]->senses[0]->shortDefinitions[0];
		} else {
			$this->shortDefinition = null;
		}
		
		/* Decode Phrase */
		if(isset($this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->phrases[0]->text)){
			$this->phrase = $this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->phrases[0]->text;
		} else {
			$this->phrase = null;
		}
		
		/* Decode Word Type */
		if(isset($this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->lexicalCategory->id)){
			$this->type = $this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->lexicalCategory->id;
		} else {
			$this->type = null;
		}
		
		/* Decode Example */
		if(isset($this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->entries[0]->senses[0]->subsenses[0]->examples[0]->text)){
			$this->example = $this->jsonObject->results[$this->resultSet]->lexicalEntries[0]->entries[0]->senses[0]->subsenses[0]->examples[0]->text;
		} else {
			$this->example = null;
		}
		
		/* Return the result array */
		return $this->jsonObject;
		
	}


	/// =====================================================================================
	/// requestAPI() : Performs the actual API request
	/// =====================================================================================
	private function requestAPI($word, $language = null, $url){
		
		// Store the new word
		$this->word = strtolower($word);
		
		// Check if there is a value in $_lang
		// If so, we need to ensure we use the language requested
		$this->_apiLanguage = ($language == null ? $this->_apiLanguage : $language);
		
		// Create HTTP header array
		// This is the HTTP header thats sent to the API URL when the request takes place
		// The APP ID and KEY are used here to authenticate the requested - an Oxford Dictionaries user account is required
		// (https://developer.oxforddictionaries.com/)
		$options = array(
				'http' => array(
						'method' => "GET",
						'header' => "app_id:".$this->_appID."\r\n" .
									"app_key:".$this->_appKey."\r\n" .
									"Content-Type: application/json"
								
			)
		);
		
		// Create the request stream
		$context = stream_context_create($options);
		
		// Perform the request
		$result = @file_get_contents($url, false, $context);
		
		// Check the returned status of the request and handle any errors
		$this->handleStatus($http_response_header[0]);
		
		/* Decode the result into an array */
		$this->jsonObject = json_decode($result);
		
		// Decode the request
		// This method will also return the JSON data so that direct calls can be made with the object
		return $this->DecodeAPIRequest($result);
	
	}
	

}

?>