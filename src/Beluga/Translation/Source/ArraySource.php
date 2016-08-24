<?php
/**
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga
 * @since          2016-08-22
 * @subpackage     Translation\Source
 * @version        0.1.1
 */


declare( strict_types = 1 );


namespace Beluga\Translation\Source;


use \Beluga\Translation\Locale;


/**
 * The Beluga\Translation\Source\ArraySource class.
 *
 * @since v0.1.0
 */
class ArraySource extends AbstractSource
{


   // <editor-fold desc="// = = = =   P R I V A T E   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   private $_translations = [];
   private $_categories   = null;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Init a new ArraySource instance.
    *
    * Known options are:
    *
    * <b>data</b>
    *
    * An optional associative or numeric indicated array (depending to $useNumericId) with translation data.
    *
    * The keys are the identifiers, the values can be strings or an array with the translation text key='text'
    * and optional the name of an category key='category'
    *
    * <b>file</b>
    *
    * If 'data' is not defined a valid PHP file must be specified that return the translations array.
    *
    * example content with numeric indicators:
    *
    * <code>
    * return [
    *    1 => 'Einfacher Übersetzungstext',
    *    3 => [
    *       'text'     => 'Ein anderer Übersetzungstext mit einer Kategorie'
    *       'category' => 'Foo'
    *    ]
    * ];
    * </code>
    *
    * example content with string indicators:
    *
    * <code>
    * return [
    *    'Simple translation text' => 'Einfacher Übersetzungstext',
    *    'An other translation text with a category' => [
    *       'text'     => 'Ein anderer Übersetzungstext mit einer Kategorie'
    *       'category' => 'Foo'
    *    ]
    * ];
    * </code>
    *
    *
    * @param \Beluga\Translation\Locale $locale The source Locale
    * @param bool                       $useNumericId
    * @param array                      $options
    */
   public function __construct( Locale $locale, bool $useNumericId = false, array $options = [] )
   {

      $this->_locale       = $locale;
      $this->_translations = [];
      $this->_useNumericId = $useNumericId;
      $this->_options      = $options;

      $this->reload();

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Gets all translation categories, known by current source.
    *
    * @return array
    */
   public function getCategories() : array
   {

      if ( \is_null( $this->_categories ) )
      {

         $this->extractCategories();

      }

      return $this->_categories;

   }

   /**
    * Gets the translation with the defined identifier
    *
    * @param  string|int  $identifier
    * @param  string|null $defaultTranslation Is returned if the translation was not found
    * @return string
    * @throws \InvalidArgumentException If the $identifier is invalid
    */
   public function getTranslation( $identifier, $defaultTranslation = null ) : string
   {

      if ( $this->_useNumericId )
      {

         // Not integer identifier. End here with an exception
         if ( ! \is_int( $identifier ) )
         {
            throw new \InvalidArgumentException( 'Current ' . __CLASS__ . ' instance requires numeric identifier!' );
         }

         // The identifier not exists, return the default translation
         if ( ! isset( $this->_translations[ $identifier ] ) )
         {
            return $defaultTranslation;
         }

         // Return string translation as it
         if ( \is_string( $this->_translations[ $identifier ] ) )
         {
            return $this->_translations[ $identifier ];
         }

         // Handle translation array values
         if ( \is_array( $this->_translations[ $identifier ] ) )
         {

            if ( isset( $this->_translations[ $identifier ][ 'text' ] ) )
            {
               return $this->_translations[ $identifier ][ 'text' ];
            }

            if ( isset( $this->_translations[ $identifier ][ 'value' ] ) )
            {
               return $this->_translations[ $identifier ][ 'value' ];
            }

            if ( isset( $this->_translations[ $identifier ][ 'translation' ] ) )
            {
               return $this->_translations[ $identifier ][ 'translation' ];
            }

         }

         // No translation found
         return $defaultTranslation;

      }

      // Not integer identifier. End here with an exception
      if ( ! \is_string( $identifier ) )
      {
         throw new \InvalidArgumentException( 'Current ' . __CLASS__ . ' instance requires string identifier!' );
      }

      // The identifier not exists, return the default translation
      if ( ! isset( $this->_translations[ $identifier ] ) )
      {
         return empty( $defaultTranslation ) ? $identifier : $defaultTranslation;
      }

      // Return string translation as it
      if ( \is_string( $this->_translations[ $identifier ] ) )
      {
         return $this->_translations[ $identifier ];
      }

      // Handle translation array values
      if ( \is_array( $this->_translations[ $identifier ] ) )
      {

         if ( isset( $this->_translations[ $identifier ][ 'text' ] ) )
         {
            return $this->_translations[ $identifier ][ 'text' ];
         }

         if ( isset( $this->_translations[ $identifier ][ 'value' ] ) )
         {
            return $this->_translations[ $identifier ][ 'value' ];
         }

         if ( isset( $this->_translations[ $identifier ][ 'translation' ] ) )
         {
            return $this->_translations[ $identifier ][ 'translation' ];
         }

      }

      return empty( $defaultTranslation ) ? $identifier : $defaultTranslation;

   }

   /**
    * Gets all translations of an specific category. If not category is defined all translations of all categories
    * are returned.
    *
    * @param string $category
    * @return array
    */
   public function getTranslations( $category = null ) : array
   {

      $translations = [];

      // If no category is defined (must be NULL) return all translations without a
      if ( \is_null( $category ) )
      {

         foreach ( $this->_translations as $identifier => $transData )
         {
            if ( is_string( $transData ) )
            {
               $translations[ $identifier ] = $transData;
               continue;
            }
            if ( ! \is_array( $transData ) || ! isset( $transData[ 'text' ] ) )
            {
               continue;
            }
            $translations[ $identifier ] = $transData[ 'text' ];
         }

         return $translations;

      }

      $category     = \trim( $category );
      $withCategory = \strlen( $category ) > 0;

      $this->getCategories();

      if ( ! \in_array( $category, $this->_categories ) && $category !== '' )
      {
         return $translations;
      }

      foreach ( $this->_translations as $identifier => $transData )
      {

         if ( $withCategory )
         {
            if ( ! \is_array( $transData ) ||
                 ! isset( $transData[ 'category' ] ) ||
                 ! isset( $transData[ 'text' ] ) ||
                 $transData[ 'category' ] !== $category )
            {
               continue;
            }
            $translations[ $identifier ] = $transData[ 'text' ];
         }
         else
         {
            if ( \is_array( $transData ) && isset( $transData[ 'text' ] ) )
            {
               if ( ( isset( $transData[ 'category' ] ) && $transData[ 'category' ] === '' ) ||
                    ! isset( $transData[ 'category' ] ) )
               {
                  $translations[ $identifier ] = $transData[ 'text' ];
               }
            }
            else if ( \is_string( $transData ) )
            {
               $translations[ $identifier ] = $transData;
            }
         }

      }

      return $translations;

   }

   /**
    * Reload the source by current defined options.
    *
    * @return \Beluga\Translation\Source\ISource
    * @throws \Exception
    */
   public function reload() : ISource
   {

      $this->_translations = [];
      $this->_categories   = null;

      if ( isset( $this->_options[ 'data' ] ) )
      {
         $this->_translations = $this->_options[ 'data' ];
      }

      if ( isset( $this->_options[ 'file' ] ) && \file_exists( $this->_options[ 'file' ] ) )
      {
         try
         {
            /** @noinspection PhpIncludeInspection */
            $translations = include $this->_options[ 'file' ];
         }
         catch ( \Throwable $ex )
         {
            throw new \Exception( 'Can not load the translations file "' . $this->_options[ 'file' ] .
                                  '! Not an valid ArraySource translation file.' );
         }
         if ( ! \is_array( $translations ) || \count( $translations ) < 1 )
         {
            throw new \Exception( 'Can not load the translations file "' . $this->_options[ 'file' ] .
                                  '! Not an valid ArraySource translation file.' );
         }
         if ( $this->_useNumericId )
         {
            foreach ( $translations as $identifier => $data )
            {
               if ( ! \is_int( $identifier ) )
               {
                  throw new \Exception( 'Can not load the translations file "' . $this->_options[ 'file' ] .
                                        '! The translation data do not use numeric identifiers.' );
               }
            }
         }
         else
         {
            foreach ( $translations as $identifier => $data )
            {
               if ( ! \is_string( $identifier ) )
               {
                  throw new \Exception( 'Can not load the translations file "' . $this->_options[ 'file' ] .
                                        '! The translation data do not use string identifiers.' );
               }
            }
         }

         $this->_translations = \array_merge( $this->_translations, $translations );

      }

      return $this;

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P R I V A T E   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = =">

   private function extractCategories()
   {

      $this->_categories = [];

      foreach ( $this->_translations as $identifier => $translationData )
      {
         if ( ! \is_array( $translationData ) )
         {
            continue;
         }
         if ( ! empty( $translationData[ 'category' ] ) )
         {
            $this->_categories[] = $translationData[ 'category' ];
         }
      }

      $this->_categories = \array_unique( $this->_categories );

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   S T A T I C   M E T H O D S   = = = = = = = = = = = = = = = = = =">

   /**
    * Loads a translation array source from a specific folder that contains one or more locale depending PHP files.
    *
    * E.G: if the defined $folder is '/var/www/example.com/translations' and the declared Locale is de_DE.UTF-8
    *
    * it tries to use:
    *
    * - /var/www/example.com/translations/de_DE.UTF-8.php
    * - /var/www/example.com/translations/de_DE.php
    * - /var/www/example.com/translations/de.php
    *
    * The used file should be declared like for translations with numeric indicators
    *
    * <code>
    * return [
    *
    *    // Simple translation as string (translation is not a part of a category)
    *    1 => 'Übersetzter Text',
    *
    *    // Translation is an array (translation is not a part of a category because the category is empty)
    *    2 => [ 'category' => '', 'text' => 'Anderer übersetzter Text' ],
    *
    *    // Translation is an array (translation is a part of the 'category name' category)
    *    4 => [ 'category' => 'category name', 'text' => 'Anderer übersetzter Text' ],
    *
    *    // Translation is an array (translation is not a part of a category because no category is defined)
    *    5 => [ 'text' => 'Anderer übersetzter Text' ],
    *
    *    // Translation will be ignored because the 'text' element not exists
    *    6 => [ 'category' => 'category name', 'value' => 'Anderer übersetzter Text' ]
    *
    * ];
    * </code>
    *
    * or for translations with string indicators:
    *
    * <code>
    * return [
    *
    *    // Simple translation as string (translation is not a part of a category)
    *    'Translated text' => 'Übersetzter Text',
    *
    *    // Translation is an array (translation is not a part of a category because the category is empty)
    *    'Other translated text 1' => [ 'category' => '', 'text' => 'Anderer übersetzter Text 1' ],
    *
    *    // Translation is an array (translation is a part of the 'category name' category)
    *    'Other translated text 2' => [ 'category' => 'category name', 'text' => 'Anderer übersetzter Text 2' ],
    *
    *    // Translation is an array (translation is not a part of a category because no category is defined)
    *    'Other translated text 3' => [ 'text' => 'Anderer übersetzter Text 3' ],
    *
    *    // Translation will be ignored because the 'text' element not exists
    *    'Other translated text 3' => [ 'category' => 'category name', 'value' => 'Anderer übersetzter Text 4' ]
    *
    * ];
    * </code>
    *
    * @param string $folder
    * @param \Beluga\Translation\Locale $locale
    * @param bool $useNumericId
    * @return \Beluga\Translation\Source\ArraySource
    */
   public static function LoadFromFolder( string $folder, Locale $locale, bool $useNumericId = false )
   {

      $languageFolderBase = rtrim( $folder, '\\/' );
      if ( ! empty( $languageFolderBase ) ) { $languageFolderBase .= '/'; }

      $languageFile = $languageFolderBase . $locale->getLID() . '_' . $locale->getCID();
      if ( \strlen( $locale->getCharset() ) > 0 )
      {
         $languageFile .= '/' . $locale->getCharset() . '.php';
      }
      else
      {
         $languageFile .= '.php';
      }

      if ( ! \file_exists( $languageFile ) )
      {
         $languageFile = $languageFolderBase . $locale->getLID() . '_' . $locale->getCID() . '.php';
      }

      if ( ! \file_exists( $languageFile ) )
      {
         $languageFile = $languageFolderBase . $locale->getLID() . '.php';
      }

      if ( ! \file_exists( $languageFile ) )
      {
         return new ArraySource( $locale, $useNumericId, [ 'data' => [] ] );
      }

      return new ArraySource( $locale, $useNumericId, [ 'file' => $languageFile ] );

   }

   // </editor-fold>


}

