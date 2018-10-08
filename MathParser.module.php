<?php namespace ProcessWire;
/**
 * Adds an option to parse math expressions in Inputfields
 * Uses this library: http://mathjs.org/
 *
 * @author Bernhard Baumrock, 05.10.2018
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
class MathParser extends WireData implements Module {

  /**
   * array of all allowed fieldtypes
   *
   * @var array
   */
  private $allowed = [
    'FieldtypeFloat',
    'FieldtypeInteger',
    'FieldtypeDecimal'
  ];

  /**
   * array holding all enabled fieldnames
   *
   * @var array
   */
  private $enabledFields = [];

  public static function getModuleInfo() {
    return [
      'title' => 'Math Parser',
      'version' => '0.0.2',
      'summary' => 'Adds an option to parse math expressions in Inputfields',
      'singular' => true,
      'autoload' => true,
      'icon' => 'superscript',
      'requires' => [],
      'installs' => [],
    ];
  }

  public function init() {
    // populate the enabled fields array
    $this->setupEnabledFields();

    // hooks
    $this->wire->addHookAfter('Inputfield::renderReadyHook', $this, 'loadAssets');
    $this->wire->addHookBefore('Inputfield::render', $this, 'addInputfieldClass');
    $this->wire->addHookAfter('Inputfield::render', $this, 'modifyInputfieldMarkup');

    // send translatable strings to javascript
    $this->wire->config->js('invalidMathParserExpr', __('Invalid Expression'));
  }

  // ########## hooks ##########

  /**
   * loadAssets
   *
   * @param HookEvent $event
   * @return void
   */
  public function loadAssets(HookEvent $event) {
    $inputfield = $event->object;


    // add the library
    $this->wire->config->scripts->add(
      $this->wire->config->urls($this).'lib/math'.
      ($this->wire->config->debug ? '' : '.min').
      '.js'
    );
    // add the script to handle all inputs
    $this->wire->config->scripts->add(
      $this->wire->config->urls($this).$this->className.'.js'
    );
    // add css
    $this->wire->config->styles->add(
      $this->wire->config->urls($this).$this->className.'.css'
    );
  }

  /**
   * addInputfieldClass
   *
   * @param HookEvent $event
   * @return String markup
   */
  public function addInputfieldClass(HookEvent $event) {
    $inputfield = $event->object;
    if(!$this->isEnabled($inputfield)) return;

    // add the MathParser class to the inputfield wrapper
    $inputfield->addClass('MathParser', 'wrapClass');
  }

  /**
   * modifyInputfieldMarkup
   *
   * @param HookEvent $event
   * @return String markup
   */
  public function modifyInputfieldMarkup(HookEvent $event) {
    $inputfield = $event->object;
    if(!$this->isEnabled($inputfield)) return;

    $html = $event->return;
    $event->return = "<div class='container'>$html<div class='info'></div></div>";
  }

  // ########## helper methods ##########

  /**
   * is this field enabled?
   *
   * @param Inputfield $inputfield
   * @return boolean
   */
  private function isEnabled($inputfield) {
    // check if the field is in the allowed fieldtypes
    if(!in_array((string)$inputfield->hasFieldtype, $this->allowed)) return;

    // check if the field is enabled in the MathParser module settings
    if(!in_array($inputfield->hasField->id, $this->enabledFields)) return;

    return true;
  }

  /**
   * populate the array of enabled fieldnames
   *
   * @return void
   */
  private function setupEnabledFields() {

    if($this->autoload) {
      $disabled = $this->getFieldIDs($this->excludeIDs);
      foreach($this->wire->fields as $field) {
        if(in_array($field->id, $disabled)) continue;
        if(!in_array((string)$field->type, $this->allowed)) continue;
        $this->enabledFields[] = $field->id;
      }
    }
    else {
      // only add manually enabled fields
      $this->enabledFields = $this->getFieldIDs($this->includeIDs);
    }

    // check if all fields are set to "text", not "number"
    // this is necessary because otherwise the user cannot enter some digits, eg (*/)
    foreach($this->enabledFields as $fieldID) $this->checkTypeText($fieldID);
  }

  /**
   * check if the type of the field is text and not number
   *
   * @param Integer $fieldID
   * @return void
   */
  private function checkTypeText($fieldID) {
    $field = $this->wire->fields->get($fieldID);
    if($field->getInputfield(new NullPage())->inputType != 'text') {
      $this->warning("Field {$field->name} inputtype must be set to 'text' for MathParser to work");
    }
  }

  /**
   * return an array of sanitized fieldnames from a textarea input
   *
   * @param String $str
   * @return Array
   */
  private function getFieldIDs($arr) {
    foreach($arr as $fieldID) {
      if(!$this->wire->fields((int)$fieldID)) {
        $this->warning("Field $fieldname does not exist but is listed in the module settings");
      }
    }
    return $arr;
  }
}