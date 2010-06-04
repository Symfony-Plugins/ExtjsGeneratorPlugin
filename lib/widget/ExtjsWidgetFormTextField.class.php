<?php

/**
 * ExtjsWidgetFormTextField represents an Extjs javascript TextField.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Benjamin Runnels <kraven@kraven.org>
 */
class ExtjsWidgetFormTextField extends sfWidgetForm
{

  /**
   * Constructor.
   *
   * Available options:
   *
   * * type: The widget type
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default config options
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
   * @param  array  $attributes  An array of config options to be merged with the default config
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An Extjs3 code string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    return '$sfExtjs3Plugin->TextField(' . var_export(array_merge(array(
      'value' => $value
    ), $attributes)) . ');';
  }
}
