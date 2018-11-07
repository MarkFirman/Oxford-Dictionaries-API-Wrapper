<?php
/* Turn off error reporting */
error_reporting(0);

/* Include the dictionary class */
include_once './dictionary.class.php';

/* Initialise the dictionary class */
/* You must supply your app_key, app_id and language - in that order */
$dictionary = new dictionary("14bb70b0cc15bb7d5a8e5f7d73c65c40", "cc7f39ed", "en");

/* After the dictionary class has been initialised, you must pass a word to the dictionary, like so: */
$dictionary->getWord("pizza");

/* Now you can get attributes of the word, including origin, definition ect using inbuilt functions */
echo "SAMPLE FUNCTIONS:";
echo "<br>PROVIDER: ".$dictionary->getProvider();
echo "<br>LANGUAGE: ".$dictionary->getLanguage();
echo "<br>DERIVATIVE: ".$dictionary->getDerivative();
echo "<br>ORIGIN: ".$dictionary->getOrigin();
echo "<br>DEFINITION: ".$dictionary->getDefinition();
echo "<br>SHORT DEFINITION: ".$dictionary->getShortDefinition();
echo "<br>EXAMPLE TEXT: ".$dictionary->getExampleText();
echo "<br>AUDIO FILE (URL): ".$dictionary->getAudioFile()."<audio controls><source src='".$dictionary->getAudioFile()."' type='audio/mpeg'>Your browser does not support HTML audio</audio></br>";

/* You can also call results explicity. To do so, you must store the result of getWord() and use it as an array */
$result = $dictionary->getWord("car");
echo "Definition of car, called explicitly: ".$result->results[0]->lexicalEntries[0]->entries[0]->senses[0]->definitions[0]; // Returns the first definition of the word 'car' 

?>