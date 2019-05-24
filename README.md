# Oxford-Dictionaries-API-Wrapper V2
PHP wrapper to communicate with the Oxford Dictionary API. This wrapper will only work with V2 of the oxford API (V1 will be deprecated from June 2019 - so I have removed support from here also)

# Features
- Easily obtain definitions, derivatives, origins, example text and audio pronunciations for a specified word

# New Features (V2)
- Better error reporting/handling
- Iterate through result sets (where words have more than 1 definition eg 'bark')

# To Do
- Boolean function to determine if word is valid

# How to
- Check the `test.php` file to see how to implement the Oxford Dictionary V2 API => `dictionary.class.php` or follow the instructions below:

1. Get your free Oxford Dictionary API and APP keys from https://developer.oxforddictionaries.com/
2. To use V2 of their API you must register either a Developer or Prototype account (Prototype is free)
3. Include the `dictionary.class.php` file using `include_once './dictionary.class.php';`
4. Initialise a new instance of the dictionary: `$dictionary = new dictionary("YOUR APP ID", "YOUR APP KEY", "LANGUAGE");`
5. For a list of supported languages please see https://developer.oxforddictionaries.com/documentation/languages
6. Pass a word to the dictionary: `$dictionary->getWord("pizza");`
7. Choose the result set to use (this step is optional. If not used, the default (first) result set will be used) `$dictionary->setResult(1);`
8. Use the class functions to return data: `echo $dictionary->getDefinition();`
