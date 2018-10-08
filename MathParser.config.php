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
      'type' => 'InputfieldAsmSelect',
      'name' => 'includeIDs',
      'label' => __('Names of fields where to use MathParser'),
      'options' => $this->getGetAllowedFields(),
      'description' => __('Select at least one'),
      'showIf' => 'autoload=0',
    ]);

    $inputfields->add([
      'type' => 'InputfieldAsmSelect',
      'name' => 'excludeIDs',
      'label' => __('Names of fields where NOT to use MathParser'),
      'options' => $this->getGetAllowedFields(),
      'description' => __('Select as required'),
      'showIf' => 'autoload=1',
    ]);

    return $inputfields;
  }

  /**
   * get allowed fields based on the fieldtype
   *
   * @return void
   */
  private function getGetAllowedFields() {
    $allowedFields = array();
    foreach ($this->wire('fields') as $f) {
      // @note: using strrchr to account for namespaced classes
      $baseClass = substr(strrchr('\\'.get_class($f->type), '\\'), 1);
      if(!in_array($baseClass, array('FieldtypeFloat','FieldtypeInteger','FieldtypeDecimal'))) continue;
      // add selectable allowed fields
      // @todo: language fields ok?
      $allowedFields[$f->id] = $f->get('label|name');
    }
    return $allowedFields;
  }
}