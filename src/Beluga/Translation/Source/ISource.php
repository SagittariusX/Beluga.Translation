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
 * The Beluga\Translation\Source\ISource class.
 *
 * @since v0.1.0
 */
interface ISource
{

   /**
    * Gets the locale of current source.
    *
    * @return \Beluga\Translation\Locale
    */
   function getLocale() : Locale;

   /**
    * Gets all translation categories, known by current source.
    *
    * @return array
    */
   function getCategories() : array;

   /**
    * Returns if the current source requires numeric (integer) identifiers.
    *
    * @return bool
    */
   function hasNumericIdentifier() : bool;

   /**
    * Gets all options of the translation source.
    *
    * @return array
    */
   function getOptions() : array;

   /**
    * Gets the option value of option with defined name or FALSE if the option is unknown.
    *
    * @param string $name The name of the option.
    * @param mixed  $defaultValue This value is remembered and returned if the option not exists
    * @return mixed
    */
   function getOption( string $name, $defaultValue = false );

   /**
    * Gets if an option with defined name exists.
    *
    * @param string $name The option name.
    * @return bool
    */
   function hasOption( string $name ) : bool;

   /**
    * Sets a options value.
    *
    * @param string $name
    * @param $value
    * @return \Beluga\Translation\Source\ISource
    */
   function setOption( string $name, $value ) : ISource;

   /**
    * Reload the source by current defined options.
    *
    * @return \Beluga\Translation\Source\ISource
    * @throws \Exception
    */
   function reload() : ISource;

   /**
    * Gets the translation with the defined identifier
    *
    * @param string|int  $identifier
    * @param string|null $defaultTranslation Is returned if the translation was not found
    * @return string
    */
   function getTranslation( $identifier, $defaultTranslation = null ) : string;

   /**
    * Gets all translations of an specific category. If not category is defined all translations of all categories
    * are returned.
    *
    * @param string $category
    * @return array
    */
   function getTranslations( $category = null ) : array;

}

