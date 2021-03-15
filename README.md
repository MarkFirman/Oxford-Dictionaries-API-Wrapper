# Oxford Dictionaries PHP API Wrapper 
PHP wrapper to communicate with the Oxford Dictionary API. This wrapper will only work with V2 of the oxford API.

# Features
- Easily obtain definitions, derivatives, origins, example text, phonetics, lexical entries and audio pronunciations for a specified word
- Error reporting and handling

# New Features (V2)
- Better error reporting/handling
- Iterate through result sets (where words have more than 1 definition eg 'bark')

# To Do
-  Add thesaurus support
-  Add translation support

# How to
- Check the `word.php` file to see how to implement the Oxford Dictionary V2 API => `oxford-dictionary-api.php` or follow the instructions below:

1. Get your free Oxford Dictionary API and APP keys from https://developer.oxforddictionaries.com/
2. To use V2 of their API you must register either a Developer or Prototype account (Prototype is free)
3. Include the `oxford-dictionary-api.php` file using `include_once './oxford-dictionary-api.php';`
4. Initialise a new instance of the dictionary: `$dictionary = new dictionary("YOUR APP ID", "YOUR APP KEY", "LANGUAGE");`
5. For a list of supported languages please see https://developer.oxforddictionaries.com/documentation/languages
6. Pass a word to the dictionary: `$dictionary->newDictionaryRequest("pizza");`
7. Choose the result set to use (this step is optional. If not used, the default (first) result set will be used) `$dictionary->changeResultSet(1);`
8. Use the class functions to return data: `echo $dictionary->definition;`
