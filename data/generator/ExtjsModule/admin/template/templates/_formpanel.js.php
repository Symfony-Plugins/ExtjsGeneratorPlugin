<?php
  $moduleName = ucfirst(sfInflector::camelize($this->getModuleName()));
  $className = $moduleName.'FormPanel';
  $xtype = $this->getModuleName().'formpanel';
?>
[?php
$className = '<?php echo $className ?>';
$formpanel = new stdClass();
$formpanel->attributes = array();

/* formpanel configuration */
$formpanel->config_array = array();

//this needs to come first so it can be overridden by generator.yml
$formpanel->config_array['url'] = url_for_form($form, '@<?php echo $this->params['route_prefix'] ?>').'.json';

$formpanel->config_array = array_merge($formpanel->config_array, array(
<?php foreach ($this->configuration->getFormpanelConfig() as $name => $params): ?>
  '<?php echo $name ?>' => <?php echo $this->asPhp($params) ?>,
<?php endforeach; ?>
)) ;

// generate toolbar action handler partials
<?php if ($editActions = $this->configuration->getValue('edit.actions')): ?>
$formpanel->config_array['tbar'] = array();
<?php foreach ($editActions as $name => $params): ?>
<?php if(! isset($params['handler_function']) && $name[0] != '_'):
$this->createPartialFile('_editaction_'.$name,'<?php
  // @object $sfExtjs3Plugin and @object $formpanel provided
  $configArr["source"] = "Ext.Msg.alert(\'Error\',\'handler_function is not defined!<br><br>Copy the template \"_editaction_'.$actionName.'.js.php\" from cache to your application/modules/'.strtolower($this->getModuleName()).'/templates folder and alter it or define the \"handler_function\" in your generator.yml file\');";
  $formpanel->attributes["'.$name.'"] = $sfExtjs3Plugin->asMethod($configArr);
?>');
?>
<?php endif; ?>
<?php if(in_array($name, array('_reload', '_save', '_savenew', '_delete', '_cancel'))): ?>
include_partial('<?php echo 'editaction_'.$name ?>', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'formpanel' => $formpanel));

<?php endif; ?>
<?php echo $this->addCredentialCondition($this->getEditActionButton($name, $params), $params)."\n" ?>
<?php endforeach; ?>
<?php endif; ?>

$readerFields = array();
<?php
$form = $this->configuration->getForm();
$key = sfInflector::underscore($this->getPrimaryKeys(true));
$needsId = true;

foreach ($this->configuration->getFormFields($form, $form->isNew() ? 'new' : 'edit')  as $fieldset => $fields)
{
  foreach ($fields as $name => $field)
  {
    if($name == $key) $needsId = false;
    echo $this->addCredentialCondition((sprintf("%s;\n", $this->renderJsonReaderField($field, $form))), $field->getConfig());
  }
}
if($needsId)
{
  $idField = $this->configuration->getFieldConfiguration('edit', $key);
  echo sprintf("%s;\n", $this->renderJsonReaderField($idField, $form));
}
?>

$formpanel->config_array['reader'] = $sfExtjs3Plugin->JsonReader(array(
  'root' => 'data',
  'totalProperty' => 'totalCount',
  'fields' => $readerFields,
));

foreach ($configuration->getFormFields($form, $form->isNew() ? 'new' : 'edit')  as $fieldset => $fields)
{
  $fieldItems = array();
  $needsId = true;
  
  foreach ($fields as $name => $field)
  {
    //dirty hack till I figure out the real reason this is added or make a hidden field widget
    if(strstr('_csrf_token', $name)) continue;
    if($name == '<?php echo $key ?>') $needsId = false;
    $attributes = array(
      'help' => $field->getConfig('help'),
      'fieldLabel' => $field->getConfig('label', $form[$name]->getParent()->getWidget()->getFormFormatter()->generateLabelName($name)),
    );
    
    eval($form[$name]->render(array_merge($attributes, $field->getConfig('attributes', array()))));
    //echo $form[$name]->render(array_merge($attributes, $field->getConfig('attributes', array())))."\n\n";
  } 
  
  if($fieldset == 'NONE')
  {
    foreach($fieldItems as $fieldItem)
    {
      $formpanel->config_array['items'][] = $fieldItem;
    }
  }
  else
  {     
    $formpanel->config_array['items'][] = $sfExtjs3Plugin->FieldSet(array_merge(array(
      'title'       => __($fieldset, array(), '<?php echo $this->getI18nCatalogue() ?>'),
      'collapsible' => false,
      'autoHeight'  => true,
      'style'   => 'padding:10px;',
      'bodyStyle'   => 'padding:0px 0px;',
      'items' => $fieldItems,
    ), $configuration->getFormFieldsetParams('params_'.$fieldset)));
  }
}

if($needsId)
{
  $formpanel->config_array['items'][] = $sfExtjs3Plugin->asCustomClass('Ext.form.Hidden', array(
    'name' => sprintf($form->getWidgetSchema()->getNameFormat(), '<?php echo $key ?>'),
  ));
}

if ($form->isCSRFProtected())
{
  //add csrf field
  $formpanel->config_array['items'][] = $sfExtjs3Plugin->asCustomClass('Ext.form.Hidden', array(
    'name' => sprintf($form->getWidgetSchema()->getNameFormat(), $form->getCSRFFieldName()),
    'value' => $form->getCSRFToken(),
  ));
}

//initComponent
include_partial('formpanel_method_initComponent', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'formpanel' => $formpanel, 'className' => $className));

<?php echo $this->getStandardPartials('formpanel', array('constructor',)) ?>
<?php echo $this->getCustomPartials('formpanel'); ?>

// initEvents
include_partial('formpanel_method_initEvents', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'formpanel' => $formpanel, 'className' => $className));

// doDelete
include_partial('formpanel_method_doDelete', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'formpanel' => $formpanel, 'className' => $className));

// doLoad
include_partial('formpanel_method_doLoad', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'formpanel' => $formpanel, 'className' => $className, 'form' => $form));

// doSubmit
include_partial('formpanel_method_doSubmit', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'formpanel' => $formpanel, 'className' => $className, 'form' => $form));

// isNew
include_partial('formpanel_method_isNew', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'formpanel' => $formpanel, 'className' => $className));

// onLoadFailure
include_partial('formpanel_method_onLoadFailure', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'formpanel' => $formpanel, 'className' => $className));

// onLoadSuccess
include_partial('formpanel_method_onLoadSuccess', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'formpanel' => $formpanel, 'className' => $className));

// onSubmitFailure
include_partial('formpanel_method_onSubmitFailure', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'formpanel' => $formpanel, 'className' => $className));

// onSubmitSuccess
include_partial('formpanel_method_onSubmitSuccess', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'formpanel' => $formpanel, 'className' => $className));

// setKey
include_partial('formpanel_method_setKey', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'formpanel' => $formpanel, 'className' => $className));

// updateButtonsVisibility
include_partial('formpanel_method_updateButtonsVisibility', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'formpanel' => $formpanel, 'className' => $className));

// close
include_partial('formpanel_method_close', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'formpanel' => $formpanel, 'className' => $className));

// create the Ext.app.sf.<?php echo $className ?> class
$sfExtjs3Plugin->beginClass(
  'Ext.app.sf',
  '<?php echo $className ?>',
  'Ext.FormPanel',
  array_merge(
    $formpanel->methods,
    $formpanel->attributes
  )
);

$sfExtjs3Plugin->endClass();
?]
// register xtype
Ext.reg('<?php echo $xtype ?>', Ext.app.sf.<?php echo $className ?>);
