<?php
/**
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga\Translation\Source
 * @since          2016-08-22
 * @subpackage     …
 * @version        0.1.0
 */

namespace Beluga\Translation\Source;


use \Beluga\Translation\Locale;


/**
 * The Beluga\Translation\Source\AbstractSource class.
 *
 * @since v0.1.0
 */
abstract class AbstractSource implements ISource
{


   // <editor-fold desc="// = = = =   P R O T E C T E D   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * All options of the Source implementation
    *
    * @var array
    */
   protected $_options      = [];

   /**
    * The current locale of the source
    *
    * @var Locale
    */
   protected $_locale;

   /**
    * Declares if the current source requires numeric (integer) identifiers.
    *
    * @var
    */
   protected $_useNumericId;

   // </editor-fold>

   /**
    * Gets the locale of current source.
    *
    * @return \Beluga\Translation\Locale
    */
   public function getLocale() : Locale
   {

      return $this->_locale;

   }

   /**
    * Returns if the current source requires numeric (integer) identifiers.
    *
    * @return bool
    */
   function hasNumericIdentifier() : bool
   {

      return $this->_useNumericId;

   }

   /**
    * Gets all options of the translation source.
    *
    * @return array
    */
   function getOptions() : array
   {

      return $this->_options;

   }

   /**
    * Gets the option value of option with defined name or FALSE if the option is unknown.
    *
    * @param string $name The name of the option.
    * @param mixed  $defaultValue This value is remembered and returned if the option not exists. If the value is NULL
    *                             the value is not set, it is only returned in this case.
    * @return mixed
    */
   function getOption( string $name, $defaultValue = false )
   {

      if ( ! $this->hasOption( $name ) )
      {
         if ( \is_null( $defaultValue ) )
         {
            return $defaultValue;
         }
         $this->_options[ $name ] = $defaultValue;
      }

      return $this->_options[ $name ];

   }

   /**
    * Gets if an option with defined name exists.
    *
    * @param string $name The option name.
    * @return bool
    */
   function hasOption( string $name ) : bool
   {

      return \array_key_exists( $name, $this->_options );

   }

   /**
    * Sets a options value.
    *
    * @param string $name
    * @param $value
    * @return \Beluga\Translation\Source\ISource
    */
   function setOption( string $name, $value ) : ISource
   {

      $this->_options[ $name ] = $value;

      return $this;

   }


   /**
    * Gets all translation categories, known by current source.
    *
    * @return array
    */
   public abstract function getCategories() : array;

   /**
    * Reload the source by current defined options.
    *
    * @return \Beluga\Translation\Source\ISource
    * @throws \Exception
    */
   public abstract function reload() : ISource;

   /**
    * Gets the translation with the defined identifier
    *
    * @param string|int  $identifier
    * @param string|null $defaultTranslation Is returned if the translation was not found
    * @return string
    */
   public abstract function getTranslation( $identifier, $defaultTranslation = null ) : string;

   /**
    * Gets all translations of an specific category. If not category is defined all translations of all categories
    * are returned.
    *
    * @param string $category
    * @return array
    */
   public abstract function getTranslations( $category = null ) : array;


}

