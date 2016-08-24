<?php
/**
 * @author         SagittariusX <unikado+sag@gmail.com>
 * @copyright  (c) 2016, SagittariusX
 * @package        Beluga
 * @since          2016-08-21
 * @subpackage     Translation
 * @version        0.1.1
 */


declare( strict_types = 1 );


namespace Beluga\Translation;


use \Beluga\Translation\Source\ISource;


/**
 * The Beluga\Translation\Translator class.
 *
 * @since v0.1.0
 */
class Translator implements ITranslator
{


   // <editor-fold desc="// = = = =   P R O T E C T E D   F I E L D S   = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * The translation source
    *
    * @var \Beluga\Translation\Source\ISource
    */
   protected $_source;

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   C O N S T R U C T O R   = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Init a new Translator instance.
    *
    * @param \Beluga\Translation\Source\ISource|null $source
    */
   public function __construct( ISource $source )
   {

      $this->_source = $source;

   }

   // </editor-fold>


   // <editor-fold desc="// = = = =   P U B L I C   M E T H O D S   = = = = = = = = = = = = = = = = = = = = = = = = =">

   /**
    * Gets the Source declaration of the translator.
    *
    * @return \Beluga\Translation\Source\ISource
    */
   public function getSource() : ISource
   {

      return $this->_source;

   }

   /**
    * Gets the translation by an numeric identifier.
    *
    * @param int $number The numeric identifier
    * @param string $defaultTranslation This text is returned as translation if the source not holds the
    *                                   translation with the requested identifier.
    * @param array ...$args If the translation contains sprintf compatible replacements
    *                                   you can declare the replacing values here.
    * @return string
    */
   public function translateByNumber( int $number, string $defaultTranslation, ...$args ) : string
   {

      $transStr = $this->_source->getTranslation( $number, $defaultTranslation );

      if ( \count( $args ) < 1 )
      {
         return $transStr;
      }

      return \sprintf( $transStr, $args );

   }

   /**
    * Gets the translation by an text identifier. If no translation was found inside the current used
    * source $text is returned as translation.
    *
    * @param string $text The text identifier. Often the english text if english is the main language
    * @param array ...$args If the translation contains sprintf compatible replacements you can declare the
    *                        replacing values here.
    * @return string
    */
   public function translateByText( string $text, ...$args ) : string
   {

      $transStr = $this->_source->getTranslation( $text );

      if ( \count( $args ) < 1 )
      {
         return $transStr;
      }

      return \sprintf( $transStr, $args );

   }

   /**
    * Gets the Source declaration of the translator.
    *
    * @return \Beluga\Translation\Source\ISource
    */
   public function setSource( ISource $source ) : ITranslator
   {

      $this->_source = $source;

      return $this;

   }

   // </editor-fold>


}

