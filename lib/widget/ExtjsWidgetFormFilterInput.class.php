<?php

/**
 * ExtjsWidgetFormFilterTextField represents php template code used for filtering text.
 *
 * @package    symfony
 * @subpackage ExtjsGenerator
 * @author     Benjamin Runnels <benjamin.r.runnels@citi.com>
 */
class ExtjsWidgetFormFilterInput extends ExtjsWidgetForm
{

  /**
   * Constructor.
   *
   * Available options:
   *
   * * with_empty:  Whether to add the empty checkbox (true by default)
   * * empty_label: The label to use when using an empty checkbox
   * * template:    The template to use to render the widget
   * Available placeholders: %input%, %empty_checkbox%
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default field attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('with_empty', true);
    $this->addOption('empty_label', 'is empty');
    $this->addOption('template', '%input%  %empty_checkbox%');
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The value displayed in this widget
   * @param  array  $attributes  An array of field attributes to be merged with the default field attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An php template string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $values = array_merge(array(
      'text' => '', 
      'is_empty' => false
    ), is_array($value) ? $value : array());
    
    return strtr($this->getOption('template'), array(
      '%input%' => $this->renderExtjsContentBlock('filter', 'TextField', array_merge(array(
        'name' => $name.'[text]', 
        'value' => $values['text'],
        'listeners' => array(
          'reset' => array(
            'fn' => 'function(){this.originalValue = null;this.setValue(null)}'
          ),
          'specialkey' => array(
            'fn' => 'function(f,e){if(f.getValue() != \'\' && e.getKey() ==13)this.ownerCt.buttons[0].handler.call(this.ownerCt);}'
          )
        )
      ), $attributes)), 
      '%empty_checkbox%' => $this->getOption('with_empty') ? $this->renderExtjsFilterIsEmptyCheckbox($name, $values) : ''
    ));
  }
}
