<?php
/***********************************************************************
* # @Author Mark Firman
* # @Project Dictionary API V2
* # @Date 23/05/2019
* # @Email info@markfirman.co.uk
* # @Last Modified 24/05/2019
*/
 
/* Include the dictionary class */
include_once 'dictionary.class.php';

/* Create a new instance of the dictionary class */
$dictionary = new Dictionary("APP ID", "APP KEY", "en-gb");

/* Send new word request to the dictionary */
$dictionary->queryWord("Bark");

/* Set the result to use - some words might have multiple meanings - use this method to switch between different meanings */
/* This is only required if you want to specify a result set other than the default */
/* If you specify a result set that does not exist, it will default to 0 */
$dictionary->setResult(0);

/* Get results from dictionary class */
echo "<h1>Dictionary Class Results - ".$dictionary->getWord()."</h1>";
echo "<b>Word:</b> ".$dictionary->getWord();
echo "<br><b>Definition:</b> ".$dictionary->getDefinition();
echo "<br><b>Short Definition:</b> ".$dictionary->getShortDefinition();
echo "<br><b>Example:</b> ".$dictionary->getExample();
echo "<br><b>Lexical:</b> ".$dictionary->getLexical();
echo "<br><b>Phonetic:</b> ".$dictionary->getPhonetic();
echo "<br><b>Origin:</b> ".$dictionary->getOrigin();
echo "<br><b>Language:</b> ".$dictionary->getLanguage();
echo "<br><b>Audio:</b> <audio controls><source src='".$dictionary->getAudio()."' type='audio/mpeg'>Your browser does not support HTML audio</audio><br>";

/* Displays the current result set */
echo "<br></br>Using result set: <b>".$dictionary->selected_result."</b>";

?>