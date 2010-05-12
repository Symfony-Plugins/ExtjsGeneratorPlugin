<?php

/**
 * sfWidgetFormFilterDate represents a date filter widget.
 *
 * @package    symfony
 * @subpackage ExtjsGenerator
 * @author     Benjamin Runnels <benjamin.r.runnels@citi.com>
 */
class ExtjsWidgetFormFilterDate extends ExtjsWidgetForm
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
    $this->addOption('template', '%from_date% %to_date% %empty_checkbox%');
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
      'is_empty' => ''
    ), is_array($value) ? $value : array());
    
    // need to do this because to field doesn't get a fieldLabel
    $fieldLabel = $attributes['fieldLabel'];
    unset($attributes['fieldLabel']);
    
    return strtr($this->getOption('template'), array(
      '%from_date%' => $this->renderExtjsContentBlock('filter', 'TwinDateField', array_merge(array(
        'name' => $name . '[from]', 
        'value' => $value['from'], 
        'labelSeparator' => ':<div style="color: #808080;padding-top:2px;">&nbsp;&nbsp;From:</div>', 
        'fieldLabel' => $fieldLabel, 
        'itemCls' => 'date-filter', 
        'anchor' => '80%'
      ), $attributes)), 
      '%to_date%' => $this->renderExtjsContentBlock('filter', 'TwinDateField', array_merge(array(
        'name' => $name . '[to]', 
        'value' => $value['to'], 
        'labelSeparator' => '', 
        'fieldLabel' => '<span style="color: #808080;">&nbsp;&nbsp;To:</span>', 
        'itemCls' => 'date-filter', 
        'anchor' => '80%'
      ), $attributes)), 
      '%empty_checkbox%' => $this->getOption('with_empty') ? $this->renderExtjsFilterIsEmptyCheckbox($name, $values) : ''
    ));
  }
}
