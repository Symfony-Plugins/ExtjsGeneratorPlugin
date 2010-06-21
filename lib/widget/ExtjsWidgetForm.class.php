<?php

/**
 * sfWidgetForm
 *
 * @package    symfony
 * @subpackage ExtjsGenerator
 * @author     Benjamin Runnels <benjamin.r.runnels@citi.com>
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
    return $this->renderExtjsContentBlock('filter', 'Checkbox', array(
      'name' => $name . '[is_empty]',
      'boxLabel' => $this->translate($this->getOption('empty_label')),
      'checked' => $values['is_empty'] ? true : false,
      'inputValue' => 'true',
      'hideLabel' => true,
      'listeners' => array(
        'render' => array(
          'fn' => "function(){this.getEl().up('div.x-form-item').setStyle({paddingLeft:'5px',marginTop:'-10px'});}",
          'single' => true
        )
      )
    ));
  }
}
