# Beluga.Translation

A PHP translation/localization library

## Installation

Install it via

```
composer require sagittariusx/beluga.translation
```

or inside the `composer.json`:

```json
   "require": {
      "sagittariusx/beluga.translation": "^0.1.1"
   },
```


## Usage

If you want to use this package inside you're application include the depending composer autoload.php

### First step, the Locale

Create a new Locale instance

```php
use \Beluga\Tranlation\Locale;

Locale::Create(
   // The fallback locale if no other was found
   new Locale( 'de', 'AT', 'UTF-8' ),
   // Check also the URL path for an locale or language part?
   true,
   // This are the names of the parameters, accepted from $_POST, $_GET and $_SESSION
   [ 'locale', 'language', 'lang' ]
)
   ->registerAsGlobalInstance();
```

This creates the new Locale by checking the following places to get the required information

* First The current URL part is checked, if it contains an valid locale string, it is used (you can disable it by
  setting the 2nd Create parameter to FALSE.
* Next it checks if one of the defined request parameters (3rd parameter) is defined by $_POST, $_GET or $_SESSION
* After that it is checked if the browser sends some information about the preferred locale/language.
* Finally it is checked if the system gives usable locale information.

If all this methods fail, the declared fall back locale is returned. You can also call it main locale.

Last but not least the created locale is registered as global available Locale instance. It can be accessed from other
places by:

```php
if ( Locale::HasGlobalInstance() )
{
   $locale = Locale::GetGlobalInstance();
}
else
{
   // Create the locale
   //$locale = Locale::Create( … )->registerAsGlobalInstance();
}
```

### Inside you app or lib

For example if you have an class that requires Localization

```php

use \Beluga\Translation\{Locale,Translator};
use \Beluga\Translation\Source\ArraySource;

class Foo
{

   /**
    * @type \Beluga\Translation\Translator
    */
   private $trans;
   
   public function __construct( Locale $locale = null )
   {
   
      $_locale = null;
   
      if ( ! \is_null( $locale ) )
      {
         $_locale = $locale
      }
      
      else if ( Locale::HasGlobalInstance() )
      {
         $_locale = Locale::GetGlobalInstance();
      }
      
      if ( ! \is_null( $_locale ) )
      {
         $source = ArraySource::LoadFromFolder( __DIR__ . '/i18n', $_locale, false );
         $this->trans = new Translator( $source )
      }
      
   }
   
   public function getTranslation( $mainLanguageText )
   {
      
      if ( ! ( $this->trans instanceof ITranlator ) )
      {
         return $mainLanguageText;
      }
      
      return $this->trans->translateByText( $mainLanguageText );
      
   }
   
   
}
```


