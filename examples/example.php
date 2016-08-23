<?php

require '../vendor/autoload.php';

use \Beluga\Translation\Locale;

function initLocale()
{
   if ( Locale::TryParseUrlPath( $refLocale ) )
   {
      return $refLocale;
   }
   if ( Locale::TryParseBrowserInfo( $refLocale ) )
   {
      return $refLocale;
   }
   if ( Locale::TryParseArray( $refLocale, $_POST, [ 'locale', 'language' ] ) )
   {
      return $refLocale;
   }
   if ( isset( $_SESSION ) )
   {
      if ( Locale::TryParseArray( $refLocale, $_SESSION, [ 'locale' ] ) )
      {
         return $refLocale;
      }
   }
   if ( Locale::TryParseSystem( $refLocale ) )
   {
      return $refLocale;
   }
   return new Locale( 'de', 'AT', 'UTF-8' );
}

$locale = initLocale();

$transSource = \Beluga\Translation\Source\ArraySource::LoadFromFolder( __DIR__ . '/l18n', $locale, false );

echo $transSource->getTranslation( 'Mister' ), "\n\n";

print_r( $transSource->getTranslations( 'titles' ) );

echo "\n";

print_r( $transSource->getTranslations( '' ) );

