<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormDateTime represents a datetime widget.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Benjamin Runnels <kraven@kraven.org>
 * @version    SVN: $Id: sfWidgetFormDateTime.class.php 20301 2009-07-19 10:57:32Z fabien $
 */
class ExtjsWidgetFormDateTime extends ExtjsWidgetForm
{

  /**
   * Configures the current widget.
   *
   * The attributes are passed to both the date and the time widget.
   *
   * If you want to pass HTML attributes to one of the two widget, pass an
   * attributes option to the date or time option (see below).
   *
   * Available options:
   *
   * * date:      Options for the date widget (see sfWidgetFormDate)
   * * time:      Options for the time widget (see sfWidgetFormTime)
   * * with_time: Whether to include time (true by default)
   * * format:    The format string for the date and the time widget (default to %date% %time%)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
//    $this->addOption('date', array());
//    $this->addOption('time', array());
    $this->addOption('with_time', true);
    $this->addOption('context', 'form');
//    $this->addOption('format', '%date% %time%');
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The date and time displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  function render($name, $value = null, $attributes = array(), $errors = array())
  {
    unset($attributes['url']);

    $configArr = array(
      'name' => $name
    );

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
    
    $xtype = $this->getOption('with_time') ? 'TwinDateTimeField' : 'TwinDateField';

    return $this->renderExtjsContentBlock($this->getOption('context'), $xtype,  array_merge($configArr, $attributes));
  }
}
