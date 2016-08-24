<?php
/**
 * In this file the exception class '\Beluga\Translation\Locale' is defined.
 *
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga
 * @since          2016-08-21
 * @subpackage     Translation
 * @version        0.1.1
 */


declare( strict_types = 1 );


namespace Beluga\Translation;


use \Beluga\DynamicProperties\ExplicitGetter;


/**
 * Defines the Locale class.
 *
 * There are different source where a locale can come from.
 *
 * For it i have implemented 5 different ways + the constructor to initialize a locale.
 *
 * You can use it in combination like
 *
 * <code>
 * function initLocale()
 * {
 *    if ( Locale::TryParseUrlPath( $refLocale ) )
 *    {
 *       return $refLocale;
 *    }
 *    if ( Locale::TryParseBrowserInfo( $refLocale ) )
 *    {
 *       return $refLocale;
 *    }
 *    if ( Locale::TryParseArray( $refLocale, $_POST, [ 'locale', 'language' ] ) )
 *    {
 *       return $refLocale;
 *    }
 *    if ( Locale::TryParseArray( $refLocale, $_SESSION, [ 'locale' ] ) )
 *    {
 *       return $refLocale;
 *    }
 *    if ( Locale::TryParseSystem( $refLocale ) )
 *    {
 *       return $refLocale;
 *    }
 *    return new Locale( 'de', 'AT', 'UTF-8' );
 * }
 * </code>
 *
 * @property-read string $lID      The current 2 character long language id (LID)
 * @property-read string $language This is a alias of {@see \Beluga\Locale::lID}
 * @property-read string $cID      The current 2 character long country id (CID)
 * @property-read string $country  This is a alias of {@see \Beluga\Locale::cID}*
 * @property-read string $charset  The current defined, optional character set defined by this locale.
 * @since  v0.1.0
 */
final class Locale extends ExplicitGetter
{


   // <editor-fold desc="// = = = =   P R I V A T E   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * The current used language (2 characters in lower case)
    *
    * @var    string
    */
   private $_language;

   /**
    * The current used Country (2 characters in upper case)
    *
    * @var    string
    */
   private $_country;

   /**
    * A optional charset defined by the locale.
    *
    * @var    string
    */
   private $_charset;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R I V A T E   S T A T I C   F I E L D S   = = = = = = = = = = = = = = = = = =">

   /**
    * The global locale instance
    *
    * @var \Beluga\Translation\Locale
    */
   private static $instance = null;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Init a new instance
    *
    * @param  string $language The language id (e.g. 'de')
    * @param  string $country  The optional country id (e.g. 'AT')
    * @param  string $charset  The optional charset (e.g. 'UTF-8')
    * @since  v0.1
    */
   public function __construct( string $language, string $country = null, string $charset = null )
   {

      // Init all class fields
      $this->_language = $language;
      $this->_country  = $country ?? '';
      $this->_charset  = $charset ?? '';

      // Init the array to store some usable locales
      $locales = [];

      // For windows systems we are doing this way
      if ( \DIRECTORY_SEPARATOR === '\\' && ( false !== ( $lcStr = LocaleHelper::ConvertLCIDToWin( (string) $this ) ) ) )
      {

         // explode the windows locale string at first underscore character (max. 2 resulting elements)
         $tmp = \explode( '_', $lcStr, 2 );

         // The LID is always element 0
         $lid = $tmp[ 0 ];

         // The CID is element 1, if defined
         $cid = ! empty( $tmp[ 1 ] ) ? $tmp[ 1 ] : '';

         // Init a empty character set. We must find it now, if defined
         $cset = '';

         // Search only for character set definition if a CID is defined
         if ( ! empty( $cid ) )
         {

            // explode at dot '.'. It separates the CID from a my defined character set
            $tmp = \explode( '.', $cid, 2 );

            // If a character set is defined
            if ( 2 === \count( $tmp ) )
            {
               // Assigne the character set to the variable
               $cset .= $tmp[ 1 ];
               // Remove the charset from the CID
               $cid   = $tmp[ 0 ];
               // Build all usable Locales
               $locales[] = $lid . '_' . $cid . '.' . $cset;
               $locales[] = $lid . '-' . $cid . '.' . $cset;
               $locales[] = $language . '_' . $country . '.' . $charset;
               $locales[] = $language . '-' . $country . '.' . $charset;
               $locales[] = $lid . '_' . $cid;
               $locales[] = $lid . '-' . $cid;
               $locales[] = $language . '_' . $country;
               $locales[] = $language . '-' . $country;
               $locales[] = $lid;
               $locales[] = $language;
            }
            // There is no character set defined
            else
            {
               // Build all usable Locales
               $locales[] = $lid . '_' . $cid;
               $locales[] = $lid . '-' . $cid;
               $locales[] = $language . '_' . $country;
               $locales[] = $language . '-' . $country;
               $locales[] = $lid;
               $locales[] = $language;
            }
         }
         // No usable character set
         else
         {
            $locales[] = $lid;
         }
      }
      // Here we go for non windows systems
      else
      {
         // If a charset is defined, so also a country (CID) and language (LID) must be defined
         if ( ! empty( $charset ) )
         {
            $locales[] = $language . '_' . $country . '.' . $charset;
            $locales[] = $language . '-' . $country . '.' . $charset;
            $locales[] = $language . '_' . $country;
            $locales[] = $language . '-' . $country;
         }
         // If a CID + LID is defined, but not a charset
         else if ( ! empty ( $country ) )
         {
            $locales[] = $language . '_' . $country;
            $locales[] = $language . '-' . $country;
            $locales[] = $language;
         }
         // There is only a LID defined
         else
         {
            $locales[] = $language;
         }
      }

      // Set current before defined locales only for some date+time functionality. Rest in peace… for all other :-)
      \setlocale( \LC_TIME, $locales );

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">


   // <editor-fold desc="// - - -   G E T T E R   - - - - - - - - - - - - - - - - - - - - - -">

   /**
    * Returns the current defined 2 char language ID
    *
    * @return string
    */
   public function getLID()
      : string
   {

      return $this->_language;

   }

   /**
    * This is a alias of {@see \Beluga\Locale::getLID()}.
    *
    * @return string
    */
   public function getLanguage()
      : string
   {

      return $this->getLID();

   }

   /**
    * Returns the current defined 2 char country ID
    *
    * @return string
    */
   public function getCID()
      : string
   {

      return $this->_country;

   }

   /**
    * This is a alias of {@see \Beluga\Locale::getCID()}.
    *
    * @return string
    */
   public function getCountry()
      : string
   {

      return $this->getCID();

   }

   /**
    * Returns the current defined charset
    *
    * @return string
    * @since  v0.1
    */
   public function getCharset()
      : string
   {

      return $this->_charset;

   }

   // </editor-fold>


   /**
    * Overrides the magic __toString method.
    *
    * @return string
    */
   public function __toString()
      : string
   {

      return $this->_language
          .  ( ! empty( $this->_country ) ? '_' . $this->_country : '' )
          .  ( ! empty( $this->_charset ) ? '.' . $this->_charset : '' );

   }

   /**
    * Register the current instance as globally available Locale instance.
    *
    * @return \Beluga\Translation\Locale
    */
   public function registerAsGlobalInstance() : Locale
   {

      static::$instance = $this;

      return $this;

   }


   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = =">

   /**
    * Tries to create a new Locale instance from an specific URL path part. If no URL path part is defined
    * it uses $_SERVER[ 'REQUEST_URI' ] or $_SERVER[ 'SCRIPT_URL' ] otherwise.
    *
    * @param \Beluga\Translation\Locale $refLocale Returns the Locale instance if the method return TRUE.
    * @param string|null $urlPath
    * @return bool
    */
   public static function TryParseUrlPath( &$refLocale, string $urlPath = null )
      : bool
   {

      // Get URL path from $_SERVER[ 'SCRIPT_URL' ]
      if ( empty( $urlPath ) )
      {

         if ( \filter_has_var( \INPUT_SERVER, 'REQUEST_URI' ) )
         {
            $urlPath = \filter_input( \INPUT_SERVER, 'REQUEST_URI' );
         }
         else
         {
            if ( ! \filter_has_var( \INPUT_SERVER, 'SCRIPT_URL' ) )
            {
               return false;
            }
            $urlPath = \filter_input( \INPUT_SERVER, 'SCRIPT_URL' );
         }

      }

      // IF the value does not match the required data format stop this method here
      if ( ! \preg_match( '~^/([a-zA-Z]{2})([_-]([a-zA-Z]{2}))?/~', $urlPath, $matches ) )
      {
         return false;
      }

      // Getting the LID + CID
      $lid = \strtolower( $matches[ 1 ] );
      $cid = empty( $matches[ 2 ] )
         ? null
         : \strtoupper( $matches[ 3 ] );

      // Everything is OK. Init the new instance and return TRUE
      $refLocale = new Locale( $lid, $cid );

      return true;

   }

   /**
    * Tries to create a new Locale instance from defined array. It accepts one of the following array keys
    * to declare an
    *
    * @param  \Beluga\Translation\Locale $refLocale Returns the Locale instance if the method return TRUE.
    * @param  array $requestData The array with the data that should be used for getting local info from.
    * @return bool
    */
   public static function TryParseArray(
      &$refLocale, array $requestData, array $acceptedKeys = [ 'locale', 'language', 'lang', 'loc', 'lc', 'lng' ] )
      : bool
   {

      if ( \count( $requestData ) < 1 )
      {
         return false;
      }

      $requestData  = \array_change_key_case( $requestData , \CASE_LOWER );
      $acceptedKeys = \array_change_key_case( $acceptedKeys, \CASE_LOWER );
      $language     = null;

      foreach ( $acceptedKeys as $key )
      {
         if ( ! isset( $requestData[ $key  ] ) )
         {
            continue;
         }
         $language = \trim( $requestData[ 'language' ] );
         break;
      }

      if ( empty( $language ) ) { return false; }

      if ( ! \preg_match( '~^([a-zA-Z]{2})([_-]([a-zA-Z]{2}))?~', $language, $matches ) )
      {
         return false;
      }

      // Getting the LID + CID
      $lid = \strtolower( $matches[ 1 ] );
      $cid = empty( $matches[ 2 ] )
         ? null
         : \strtoupper( $matches[ 3 ] );

      // Everything is OK. Init the new instance and return TRUE
      $refLocale = new Locale( $lid, $cid );

      return true;

   }

   /**
    * Tries to create a new Locale instance from underlying system/OS locale settings.
    *
    * @param  \Beluga\Translation\Locale $refLocale Returns the Locale instance if the method return TRUE.
    * @return bool
    */
   public static function TryParseSystem( &$refLocale )
      : bool
   {

      // Getting the current system used locale of LC_ALL
      $lcString = \setlocale( \LC_ALL, '0' );

      // Ignore values with lower than 2 characters. It also ignores the 'C' locale
      if ( empty( $lcString ) || 2 > \strlen( $lcString ) )
      {
         return false;
      }

      // Pre initialize the local LID, CID and Charset variables
      $lid = ''; $cid = ''; $charset = '';

      // Handle windows different from other OS
      if ( \DIRECTORY_SEPARATOR === '\\' )
      {

         $tmp = \explode( ';', $lcString );

         // Loop the exploded elements
         foreach ( $tmp as $element )
         {

            // Explode at the first equal sign '='
            $tmp2 = \explode( '=', $element, 2 );

            // If the explode before results in only one element (no '=' inside $element found)
            if ( 2 > \count( $tmp2 ) )
            {

               // Convert to LCID. If it fails ignore this $element
               if ( false === ( $lcid = LocaleHelper::ConvertWinToLCID( $element ) ) )
               {
                  continue;
               }

               // Get the elements of the current LCID
               LocaleHelper::ExpandLCID( $lcid, $lid, $cid, $charset );
               // Create a new instance and return TRUE
               $refLocale = new Locale( $lid, $cid, $charset );

               return true;

            }

            // Ignore 'LC_TYPE' locale types
            if ( 'LC_CTYPE' === \strtoupper( $tmp2[ 0 ] ) )
            {
               continue;
            }

            // Convert the value (after the =) to a LCID. If it fails ignore this $element
            if ( false === ( $lcid = LocaleHelper::ConvertWinToLCID( $tmp2[ 1 ] ) ) )
            {
               continue;
            }

            // Get the elements of the current LCID
            LocaleHelper::ExpandLCID( $lcid, $lid, $cid, $charset );
            $refLocale = new Locale( $lid, $cid, $charset );

            return true;

         }

         // Return false if no usable locale is found by this way
         return false;

      }

      // No windows systems
      if ( ! \preg_match( '~^[a-z]{2}([_-][a-z]{2})?(@[a-z_-]+)?(\.[a-z0-9_-]{1,14})?$~i', $lcString ) )
      {
         // A unknown locale string format
         return false;
      }

      LocaleHelper::ExpandLCID( $lcString, $lid, $cid, $charset );
      $refLocale = new Locale( $lid, $cid, $charset );

      return true;

   }

   /**
    * Tries to create a new Locale instance from browser defined Accept-Language header.
    *
    * @param \Beluga\Translation\Locale $refLocale  Returns the Locale instance if the method return TRUE.
    * @return bool
    */
   public static function TryParseBrowserInfo( &$refLocale )
      : bool
   {

      // If init by browser info is disabled, or the required $_SERVER['HTTP_ACCEPT_LANGUAGE'] is not defined
      if ( ! \filter_has_var( \INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE' ) )
      {
         return false;
      }

      // Format like: de-de,de;q=0.8,en-us;q=0.5,en;q=0.3
      $tmp = \explode( ',', \filter_input( \INPUT_SERVER , 'HTTP_ACCEPT_LANGUAGE' ) );

      // Iterate over each exploded element
      for ( $i = 0; $i < count( $tmp ); $i++ )
      {

         // Explode each element at first semicolon ';' to max. 2 sub elements
         $tmp2 = \explode( ';', $tmp[ $i ], 2 );

         // Explode the first sub element (must be a LCID) at '-' into LID and CID
         $tmp3 = \explode( '-', $tmp2[ 0 ], 2 );

         // If last explode result not with 2 sub elements explode at '_' into LID and CID
         if ( \count( $tmp3 ) < 2 )
         {
            $tmp3 = \explode( '_', $tmp2[ 0 ], 2 );
         }

         // If last explode result not with 2 sub elements use only the LID
         if ( \count( $tmp3 ) < 2 )
         {
            $tmp3 = [ $tmp2[ 0 ] ];
         }
         $la   = \trim( $tmp3[ 0 ] );

         // If the LID do not use 2 characters ignore this element and restart with the next
         if ( \strlen( $la ) != 2 )
         {
            continue;
         }

         // Init a empty CID
         $co = null;

         // Init a empty charset
         $cs = null;

         // If there are more than 1 sub elements extracted from current element
         if ( isset( $tmp3[ 1 ] ) )
         {
            // Explode the second sub element at first point '.'. It separates the CID from the optional character set.
            $tmp2 = \explode( '.', $tmp3[ 1 ], 2 );
            // Assign the CID
            $co   = \trim( $tmp2[ 0 ] );
            // A charset is defined, assign it
            if ( isset( $tmp2[ 1 ] ) )
            {
               $cs = \trim( $tmp2[ 1 ] );
            }
            // clear the CID if its not defined by 2 characters
            if ( \strlen( $co ) != 2 )
            {
               $co = null;
            }
            // Otherwise convert the CID to lower case
            else
            {
               $co = \strtoupper ( $co );
            }
         }

         // Init the new Locale instance and return it.
         $refLocale = new Locale( $la, $co, $cs );

         return true;

      }

      // return FALSE if no usable local here was found.
      return false;

   }

   /**
    * Creates a locale with all available methods. If no method can create a Locale the defined fallback locale is used.
    *
    * First a locale
    *
    * @param  \Beluga\Translation\Locale $fallbackLocale
    * @return \Beluga\Translation\Locale
    */
   public static function Create(
      Locale $fallbackLocale, bool $useUrlPath = true, array $acceptedRequestParams = [ 'locale', 'language', 'lang' ] )
      : Locale
   {

      if ( $useUrlPath
           &&
           static::TryParseUrlPath( $refLocale )
      )
      {
         return $refLocale;
      }

      if ( \count( $acceptedRequestParams ) > 0 )
      {

         if ( Locale::TryParseArray( $refLocale, $_POST, $acceptedRequestParams ) )
         {
            return $refLocale;
         }

         if ( Locale::TryParseArray( $refLocale, $_GET, $acceptedRequestParams ) )
         {
            return $refLocale;
         }

         if ( isset( $_SESSION ) && Locale::TryParseArray( $refLocale, $_SESSION, $acceptedRequestParams ) )
         {
            return $refLocale;
         }

      }

      if ( Locale::TryParseBrowserInfo( $refLocale ) )
      {
         return $refLocale;
      }

      if ( Locale::TryParseSystem( $refLocale ) )
      {
         return $refLocale;
      }

      return $fallbackLocale;

   }

   /**
    * Gets if a global available instance exists.
    *
    * @return bool
    */
   public static function HasGlobalInstance()
      : bool
   {

      return ! \is_null( self::$instance );

   }

   /**
    * Gets the global available instance or NUL if none is registered.
    *
    * @return \Beluga\Translation\Locale|null
    */
   public static function GetGlobalInstance()
   {

      return self::$instance;

   }

   // </editor-fold>


}

