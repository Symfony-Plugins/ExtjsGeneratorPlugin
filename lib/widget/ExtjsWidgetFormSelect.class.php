<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormSelect represents a select HTML tag.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormSelect.class.php 23994 2009-11-15 22:55:24Z bschussek $
 */
class ExtjsWidgetFormSelect extends ExtjsWidgetFormChoiceBase
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * choices:  An array of possible choices (required)
   *  * multiple: true if the select tag must allow multiple selections
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormChoiceBase
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('context', 'form');
    $this->addOption('allowClear', true);
    $this->addOption('defaultValue');
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The value selected in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $type = 'TwinComboBox';
    $choices = $this->getChoices();
    if (isset($attributes['multiple']) && $attributes['multiple'] == 'multiple')
    {
      $type = 'ItemSelector';
      unset($attributes['multiple']);
      $configArr = array(
        'name' => $name,
        'multiselects' => array(
          array(
            'legend'  => 'Associated',
            'store' => array(
              'xtype' => 'arraystore',
              'fields' => array('value', 'display'),
              'data' => $this->getOptionsForSelect($value, $choices),
            ),
            'valueField' => 'value',
            'displayField' => 'display',
            'width' => isset($attributes['width']) ? $attributes['width'] : 200
          ),
          array(
            'legend'  => 'Unassociated',
            'store' => array(
              'xtype' => 'arraystore',
              'fields' => array('value', 'display'),
              'data' => '[]',
            ),
            'valueField' => 'value',
            'displayField' => 'display',
            'width' => isset($attributes['width']) ? $attributes['width'] : 200
          )
        )
      );
      unset($attributes['width']);
    }
    else
    {
      $configArr = array(
        'hiddenName' => $name,
        'name' => $name,
        'store' => array(
          'xtype' => 'arraystore',
          'fields' => array('value', 'display'),
          'data' => $this->getOptionsForSelect($value, $choices),
        ),
        'allowClear' => $this->getOption('allowClear'),
        'defaultValue' => $this->getOption('defaultValue'),
        'valueField' => 'value',
        'displayField' => 'display',
        'forceSelection' => true,
        'typeAhead' => false,
        'value' => (string)$value,
        'triggerAction' => 'all',
        'mode' => 'local',
      );
    }   

    return $this->renderExtjsContentBlock($this->getOption('context'), $type, array_merge($configArr, $attributes));
  }

  /**
   * Returns an array of option tags for the given choices
   *
   * @param  string $value    The selected value
   * @param  array  $choices  An array of choices
   *
   * @return array  An array of option tags
   */
  protected function getOptionsForSelect($value, $choices)
  {
    $mainAttributes = $this->attributes;
    $this->attributes = array();

    if (!is_array($value))
    {
      $value = array($value);
    }

    $value = array_map('strval', array_values($value));
    $value_set = array_flip($value);

    $options = array();
    foreach ($choices as $key => $option)
    {
      $options[] = array(self::escapeOnce($key), $option);
    }

    $this->attributes = $mainAttributes;

    return $options;
  }
}
