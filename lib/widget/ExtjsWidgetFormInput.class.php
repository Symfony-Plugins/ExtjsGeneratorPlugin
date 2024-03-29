<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormInput represents an HTML input tag.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Benjamin Runnels <kraven@kraven.org>
 * @version    SVN: $Id: sfWidgetFormInput.class.php 22081 2009-09-16 13:28:26Z fabien $
 */
class ExtjsWidgetFormInput extends ExtjsWidgetForm
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
    $this->addOption('context', 'form');
    $this->addRequiredOption('type');
    $this->addOption('defaultValue', false);
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
    unset($attributes['url']);

    $configArr = array(
      'name' => $name
    );

    if(is_array($value) && isset($value['text'])) $value = $value['text'];

    if($value) $configArr['value'] = $value;
    if($this->getOption('defaultValue'))
    {
      $configArr['defaultValue'] = $this->getOption('defaultValue');
      if($value) $value = $this->getOption('defaultValue');
    }

    if(isset($attributes['help']))
    {
      $configArr['helpText'] = addslashes($attributes['help']);

      $configArr['plugins'] = array("'fieldHelp'");
      if(isset($attributes['plugins']))
      {
        $configArr['plugins'] = array_merge($configArr['plugins'], $attributes['plugins']);
        unset($attributes['plugins']);
      }
    }

    unset($attributes['help']);

    if($this->getOption('type') == 'Checkbox') $configArr['inputValue'] = 'true';
    if($this->getOption('type') == 'Hidden') unset($attributes['fieldLabel']);

    return $this->renderExtjsContentBlock($this->getOption('context'), $this->getOption('type'), array_merge($configArr, $attributes));
  }
}
