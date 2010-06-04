<?php

/**
 * ExtjsWidgetFormCheckbox represents an Extjs javascript Checkbox.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Benjamin Runnels <kraven@kraven.org>
 */
class ExtjsWidgetFormCheckbox extends sfWidgetForm
{

  /**
   * Constructor.
   *
   * Available options:
   *
   * * type: The widget type
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('type');

    // to maintain BC with symfony 1.2
    $this->setOption('type', 'text');
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The value displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    return $this->renderTag('input', array_merge(array(
      'type' => $this->getOption('type'),
      'name' => $name,
      'value' => $value
    ), $attributes));
  }
}
