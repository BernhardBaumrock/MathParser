<?php namespace ProcessWire;
class MathParserConfig extends ModuleConfig {
  public function getDefaults() {
    return [
      'autoload' => 0,
    ];
  }
  public function getInputfields() {
    /** @var InputfieldWrapper $inputfields */
    $inputfields = parent::getInputfields();
    
    $inputfields->add([
      'type' => 'radios',
      'name' => 'autoload',
      'label' => __('Use MathParser on all compatible Inputfields?'),
      'options' => [
        0 => 'No',
        1 => 'Yes',
      ],
      'optionColumns' => '1',
    ]);

    $inputfields->add([
      'type' => 'textarea',
      'name' => 'includeStr',
      'label' => __('Names of fields where to use MathParser'),
      'description' => __('Enter one fieldname per line'),
      'showIf' => 'autoload=0',
    ]);

    $inputfields->add([
      'type' => 'textarea',
      'name' => 'excludeStr',
      'label' => __('Names of fields where NOT to use MathParser'),
      'description' => __('Enter one fieldname per line'),
    ]);

    return $inputfields;
  }
}