<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormChoice represents a choice widget.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Benjamin Runnels <kraven@kraven.org>
 * @version    SVN: $Id: sfWidgetFormChoice.class.php 23994 2009-11-15 22:55:24Z bschussek $
 */
class ExtjsWidgetFormChoice extends ExtjsWidgetFormChoiceBase
{

  /**
   * Constructor.
   *
   * Available options:
   *
   * * choices:          An array of possible choices (required)
   * * multiple:         true if the select tag must allow multiple selections
   * * expanded:         true to display an expanded widget
   * if expanded is false, then the widget will be a select
   * if expanded is true and multiple is false, then the widget will be a list of radio
   * if expanded is true and multiple is true, then the widget will be a list of checkbox
   * * renderer_class:   The class to use instead of the default ones
   * * renderer_options: The options to pass to the renderer constructor
   * * renderer:         A renderer widget (overrides the expanded and renderer_options options)
   * The choices option must be: new sfCallable($thisWidgetInstance, 'getChoices')
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormChoiceBase
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('context', 'form');
    $this->addOption('multiple', false);
    $this->addOption('expanded', false);
    $this->addOption('renderer_class', false);
    $this->addOption('renderer_options', array());
    $this->addOption('renderer', false);
    $this->addOption('allowClear', true);
    $this->addOption('defaultValue');
    $this->addOption('with_empty', true);
  }

  /**
   * Sets the format for HTML id attributes. This is made avaiable to the renderer,
   * as this widget does not render itself, but delegates to the renderer instead.
   *
   * @param string $format  The format string (must contain a %s for the id placeholder)
   *
   * @see sfWidgetForm
   */
  public function setIdFormat($format)
  {
    $this->options['renderer_options']['id_format'] = $format;
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
    if($this->getOption('multiple'))
    {
      $attributes['multiple'] = 'multiple';
    }
    
    // always go with local store because of foreign column value/display issues
    // remote store still used for local columns or savestate can be configured
    // manually for foreign columns
    if(!isset($attributes['mode'])) $attributes['mode'] = $this->getOption('mode');

    return $this->getRenderer()->render($name, $value, $attributes, $errors);
  }

  public function getRenderer()
  {
    if($this->getOption('renderer'))
    {
      return $this->getOption('renderer');
    }

    if(! $class = $this->getOption('renderer_class'))
    {
      $type = ! $this->getOption('expanded') ? '' : ($this->getOption('multiple') ? 'checkbox' : 'radio');
      $class = sprintf('ExtjsWidgetFormSelect%s', ucfirst($type));
    }

    return new $class(array_merge(array(
      'defaultValue' => $this->getOption('defaultValue'),
      'allowClear' => $this->getOption('allowClear'),
      'with_empty' => $this->getOption('with_empty'),
      'context' => $this->getOption('context'),
      'choices' => new sfCallable(array(
        $this,
        'getChoices'
      )),
      'baseParams' => new sfCallable(array(
        $this,
        'getBaseParams'
      ))
    ), $this->options['renderer_options']), $this->getAttributes());
  }
}
