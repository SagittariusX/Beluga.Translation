<?php
/**
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga
 * @since          2016-08-21
 * @subpackage     Translation
 * @version        0.1.0
 */


declare( strict_types = 1 );


namespace Beluga\Translation;


use \Beluga\Translation\Source\ISource;


/**
 * Each translator should implement this translator interface.
 *
 * @since v0.1.0
 */
interface ITranslator
{

   /**
    * Gets the Source declaration of the translator.
    *
    * @return \Beluga\Translation\Source\ISource
    */
   function getSource() : ISource;

   /**
    * Gets the Source declaration of the translator.
    *
    * @return \Beluga\Translation\Source\ISource
    */
   function setSource( ISource $source ) : ITranslator;

   /**
    * Gets the translation by an numeric identifier.
    *
    * @param int    $number             The numeric identifier
    * @param string $defaultTranslation This text is returned as translation if the source not holds the
    *                                   translation with the requested identifier.
    * @param array  ...$args            If the translation contains sprintf compatible replacements
    *                                   you can declare the replacing values here.
    * @return string
    */
   function translateByNumber( int $number, string $defaultTranslation, ...$args ) : string;

   /**
    * Gets the translation by an text identifier. If no translation was found inside the current used
    * source $text is returned as translation.
    *
    * @param string $text    The text identifier. Often the english text if english is the main language
    * @param array  ...$args If the translation contains sprintf compatible replacements you can declare the
    *                        replacing values here.
    * @return string
    */
   function translateByText( string $text, ...$args ) : string;

}

