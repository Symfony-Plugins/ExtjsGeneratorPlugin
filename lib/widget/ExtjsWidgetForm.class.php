<?php

/**
 * sfWidgetForm
 *
 * @package    symfony
 * @subpackage ExtjsGenerator
 * @author     Benjamin Runnels <kraven@kraven.org>
 */
abstract class ExtjsWidgetForm extends sfWidgetForm
{

  public function renderExtjsContentBlock($context, $type, $content = null)
  {
    $context = ($context == 'form') ? 'fieldItems' : 'filterpanel->config_array["items"]';
    return sprintf('$%s[] = $sfExtjs3Plugin->%s(%s);', $context, $type, var_export($content, true));
  }

  public function renderExtjsFilterIsEmptyCheckbox($name, $values)
  {
    return $this->renderExtjsContentBlock('filter', 'IsEmptyCheckbox', array(
      'name' => $name . '[is_empty]',
      'boxLabel' => $this->translate($this->getOption('empty_label')),
      'checked' => $values['is_empty'] ? true : false
    ));
  }
}
