<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormInputPlain represents a plain static text field.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Benjamin Runnels <kraven@kraven.org>
 * @version    SVN: $Id: sfWidgetFormInputHidden.class.php 9046 2008-05-19 08:13:51Z FabianLange $
 */
class ExtjsWidgetFormInputPlain extends ExtjsWidgetFormInput
{

  /**
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormInput
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    $this->setOption('type', 'PlainTextField');
  }
}
