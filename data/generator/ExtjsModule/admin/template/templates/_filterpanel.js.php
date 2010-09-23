<?php
  $moduleName = ucfirst(sfInflector::camelize($this->getModuleName()));
  $className = $moduleName."FilterPanel";
  $xtype = $this->getModuleName()."filterpanel";
?>
[?php
$className = '<?php echo $className ?>';
$filterpanel = new stdClass();
$filterpanel->methods = array();
$filterpanel->variables = array();

/* Filterpanel Configuration */
$filterpanel->config_array = array(
<?php foreach ($this->configuration->getFilterpanelConfig() as $name => $params): ?>
  '<?php echo $name ?>' => <?php echo $this->asPhp($params) ?>,
<?php endforeach; ?>
);

$filterpanel->config_array['buttons'] = array(
  $sfExtjs3Plugin->Button(array
  (
    'text'    => 'Filter',
    'handler' => $sfExtjs3Plugin->asMethod("
      var params=this.form.getValues();
      this.fireEvent('filter_set', params, this);
      this.fireEvent('click');
    "),
    'scope' => 'this'
  )),
  $sfExtjs3Plugin->Button(array
  (
    'text'    => 'Reset',
    'handler' => $sfExtjs3Plugin->asMethod("
      this.form.reset();
      this.fireEvent('filter_reset', this);
    "),
    'scope' => 'this'
  ))
);

foreach ($configuration->getFormFilterFields($filters) as $name => $field)
{
  //if ((isset($filters[$name]) && $filters[$name]->isHidden()) || (!isset($filters[$name]) && $field->isReal())) continue;
  if (!isset($filters[$name])) continue;

  $attributes = array_merge(array(
    'help' => $field->getConfig('help'),
    'fieldLabel' => addslashes($field->getConfig('label', $filters[$name]->getParent()->getWidget()->getFormFormatter()->generateLabelName($name))),
    'url' => $this->getModuleName().'/combo.json'
  ), $field->getConfig('attributes', array()));

  $params = $field->getConfig();
  if(isset($params['credentials']))
  {
    if ($sf_user->hasCredential($params['credentials'])) eval($filters[$name]->render($attributes));
  }
  else
  {
    eval($filters[$name]->render($attributes));
  }
}

if ($filters->isCSRFProtected())
{
  //add csrf field
  $filterpanel->config_array['items'][] = $sfExtjs3Plugin->asCustomClass('Ext.form.Hidden', array(
    'name' => sprintf($filters->getWidgetSchema()->getNameFormat(), $filters->getCSRFFieldName()),
    'value' => $filters->getCSRFToken(),
  ));
}

// initEvents
include_partial('filterpanel_method_initEvents', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'filterpanel' => $filterpanel, 'className' => $className));
<?php echo $this->getStandardPartials('filterpanel', array('initComponent','constructor')) ?>
<?php echo $this->getCustomPartials('filterpanel'); ?>

// create the Ext.app.sf.<?php echo $className ?> class
$sfExtjs3Plugin->beginClass(
  'Ext.app.sf',
  '<?php echo $className ?>',
  'Ext.FormPanel',
  array_merge(
    $filterpanel->methods,
    $filterpanel->variables
  )
);

$sfExtjs3Plugin->endClass();
?]
// register xtype
Ext.reg('<?php echo $xtype ?>', Ext.app.sf.<?php echo $className ?>);
