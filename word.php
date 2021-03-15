<?php
/***********************************************************************
* # @Author Mark Firman
* # @Project Dictionary API V2
* # @Date 23/05/2019
* # @Email info@markfirman.co.uk
* # @Last Modified 15/03/2021
*/

/* Include the oxford dictionary API class */
/* The dictionary class must be included, in order to invoke it */
include_once 'oxford-dictionary-api.php';

/* DICTIONARY REQUEST */
/* Create a new instance of the dictionary class - this only needs to be done once! */
/* Ensure you replace APP ID and APP KEY with your actual application ID and KEY obtained from https://developer.oxforddictionaries.com/ */
/* You should also ensure you are using a valid language. A list of valid languages and their respective keys: https://developer.oxforddictionaries.com/documentation/languages */
$dictionary = new Dictionary("APP ID", "APP KEY", "en-gb", true);

/* Create a new dictionary request */
$result = $dictionary->newRequest("bark");

print "<h1>Word - ". $dictionary->word."</h1>";
print "<i>Total number of results found: </i>". $dictionary->totalResultCount;
print "<h2>Using result set: ". $dictionary->resultSet ."</h2>";

/* Called from the dictionary class */
print "<h2>ETYMOLOGY:</h2> ". $dictionary->etymology;
print "<h2>PRONUNCIATION URL:</h2> ". $dictionary->audioURL;
print "<h2>PRONUNCIATION DIALECT:</h2> ". $dictionary->dialect;
print "<h2>PHONETIC SPELLING:</h2> ". $dictionary->phonetic;
print "<h2>DEFINITION:</h2> ". $dictionary->definition;
print "<h2>SHORT DEFINITION:</h2> ". $dictionary->shortDefinition;
print "<h2>PHRASE:</h2> ". $dictionary->phrase;
print "<h2>TYPE:</h2> ". $dictionary->type;
print "<h2>EXAMPLE:</h2> ". $dictionary->example;

/* change the result set to see other definitions of 'bark' */
$dictionary->changeResultSet(1);

print "<h2>Using result set: ". $dictionary->resultSet ."</h2>";

/* Called from the dictionary class */
print "<h2>ETYMOLOGY:</h2> ". $dictionary->etymology;
print "<h2>PRONUNCIATION URL:</h2> ". $dictionary->audioURL;
print "<h2>PRONUNCIATION DIALECT:</h2> ". $dictionary->dialect;
print "<h2>PHONETIC SPELLING:</h2> ". $dictionary->phonetic;
print "<h2>DEFINITION:</h2> ". $dictionary->definition;
print "<h2>SHORT DEFINITION:</h2> ". $dictionary->shortDefinition;
print "<h2>PHRASE:</h2> ". $dictionary->phrase;
print "<h2>TYPE:</h2> ". $dictionary->type;
print "<h2>EXAMPLE:</h2> ". $dictionary->example;

/* change the result set to see other definitions of 'bark' */
$dictionary->changeResultSet(2);

print "<h2>Using result set: ". $dictionary->resultSet ."</h2>";

/* Called from the dictionary class */
print "<h2>ETYMOLOGY:</h2> ". $dictionary->etymology;
print "<h2>PRONUNCIATION URL:</h2> ". $dictionary->audioURL;
print "<h2>PRONUNCIATION DIALECT:</h2> ". $dictionary->dialect;
print "<h2>PHONETIC SPELLING:</h2> ". $dictionary->phonetic;
print "<h2>DEFINITION:</h2> ". $dictionary->definition;
print "<h2>SHORT DEFINITION:</h2> ". $dictionary->shortDefinition;
print "<h2>PHRASE:</h2> ". $dictionary->phrase;
print "<h2>TYPE:</h2> ". $dictionary->type;
print "<h2>EXAMPLE:</h2> ". $dictionary->example;







?>







