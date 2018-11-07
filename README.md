# Oxford-Dictionaries-API-Wrapper
PHP wrapper to communicate with the Oxford Dictionary API

# Features
- Easily obtain definitions, derivatives, origins, example text and audio pronunciations for a specified word

# To Do
- Auto check the validity of returned data to prevent errors appearing. (Short fix: `error_reporting(0);`)

# How to
- Check the `test.php` file to see how to implement the Oxford Dictionary API => `dictionary.class.php` or follow the instructions below:

1. Get your free Oxford Dictionary API and APP keys from https://developer.oxforddictionaries.com/
2. Include the `dictionary.class.php` file using `include_once './dictionary.class.php';`
3. Initialise a new instance of the dictionary: `$dictionary = new dictionary("YOUR APP KEY", "YOUR APP ID", "LANGUAGE");`
4. Pass a word to the dictionary: `$dictionary->getWord("pizza");`
5. Use the class functions to return data: `echo $dictionary->getDefinition();`
