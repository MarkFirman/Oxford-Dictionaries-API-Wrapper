<?php
/***********************************************************************
* # @Author Mark Firman
* # @Project Dictionary API V2
* # @Date 23/05/2019
* # @Email info@markfirman.co.uk
* # @Last Modified 07/03/2020
*/
 
/* The dictionary class must be included, in order to invoke it */
/* Include the dictionary class */
include_once 'dictionary.class.php';

/* DICTIONARY REQUEST */
/* Create a new instance of the dictionary class - this only needs to be done once! */
/* Ensure you replace APP ID and APP KEY with your actual application ID and KEY obtained from https://developer.oxforddictionaries.com/ */
/* You should also ensure you are using a valid language. A list of valid languages and their respective keys: https://developer.oxforddictionaries.com/documentation/languages */
$dictionary = new Dictionary("APP ID", "APP KEY", "en-gb");

/* To create a new dictionary request, use: */
/* This will invoke a new API request using the word 'Pizza' */
$dictionary->newDictionaryRequest("Pizza");

/* Set the result to use - some words might have multiple meanings ('bark') - use this method to switch between different meanings */
/* This is only required if you want to specify a result set other than the default */
/* If you specify a result set that does not exist, it will default back to 0 */
$dictionary->setResult(0);

/* Get and display results from dictionary class */
/* The below shows you how you might return the dictionary call results */
echo "<h1>Dictionary Class Results - ".$dictionary->word."</h1> - status: ".$dictionary->errors['status'];
echo "<b>Word:</b> ".$dictionary->word;
echo "<br><b>Definition:</b> ".$dictionary->getDefinition();
echo "<br><b>Short Definition:</b> ".$dictionary->getShortDefinition();
echo "<br><b>Example:</b> ".$dictionary->getExample(0);
echo "<br><b>Example 2:</b> ".$dictionary->getExample(1);
echo "<br><b>Lexical:</b> ".$dictionary->getLexical();
echo "<br><b>Phonetic:</b> ".$dictionary->getPhonetic();
echo "<br><b>Origin:</b> ".$dictionary->getOrigin();
echo "<br><b>Language:</b> ".$dictionary->API_LANG;
echo "<br><b>Audio:</b> <audio controls><source src='".$dictionary->getAudio()."' type='audio/mpeg'>Your browser does not support HTML audio</audio><br>";

/* Displays the current result set and maximum number of result sets */
echo "<br></br>Using result set: <b>".$dictionary->selected_result."</b>";
echo "<br></br>Total result sets available from request: <b>".$dictionary->num_returned_results."</b>";


/* INFLECTION REQUEST */
/* Note: Inflections only work with a developer or enterprise Oxford account. The free prototype account does not support inflections */
/* If you have an incorrect account type, you will recieve a 403 authentication failed message */
/* Send a new inflection request to the API for the word 'run' */
$dictionary->newInflectionRequest("run");

/* Get and display the results from the inflections request */
echo "<h1>Inflection Results - ".$dictionary->word."</h1> - status: ".$dictionary->errors['status'];
// Display each inflection
for($i = 0; $i < count($dictionary->inflections); $i++){
	echo "<b>Inflection ".$i.": </b>".$dictionary->inflections[$i]."</i><br/>";
}

/* TRANSLATION REQUEST */
/* Note: Translations only work with a developer or enterprise Oxford account */
/* If you have an incorrect account type, you will recieve a 403 authentication failed message */
/* Send a new translation request to the API where the first parameter is the word to translate and the second param is the translation language */
/* The source language is set when invoking the dictionary class, but can be changed using: $dictionary->API_LANG = 'it'; */
$dictionary->newTranslationRequest("hello", "es");

/* Get and display the results from the translation request */
echo "<h1>Translation Results - English word: '".$dictionary->word."' to Spanish</h1> - status: ".$dictionary->errors['status'];
echo "<b>Translation:</b> ".$dictionary->translations."</i><br/>";

?>
