<?php
  $moduleName = ucfirst(sfInflector::camelize($this->getModuleName()));
  $className = $moduleName."ObjectActions";
  $xtype = $this->getModuleName()."objectactions";
?>
[?php
$className = '<?php echo $className ?>';
$objectActions = new stdClass();
$objectActions->attributes = array();

/* objectActions configuration */
//$objectActions->config_array = array(
<?php //foreach ($this->configuration->getObjectActionsConfig() as $name => $params): ?>
  //'<?php //echo $name ?>' => <?php //echo $this->asPhp($params) ?>,
<?php //endforeach; ?>
//);

// generate toolbar action handler partials
<?php if ($actions = $this->configuration->getValue('list.object_actions')): ?>
<?php foreach ($actions as $name => $params): ?>
<?php if(! isset($params['handler_function']) && $name[0] != '_'):
$this->createPartialFile('_list_rowaction_'.$name,'<?php // @object $sfExtjs3Plugin and @object $objectActions provided
  $configArr["parameters"] = "grid, record, action, row, col";
  $configArr["source"] = "Ext.Msg.alert(\'Error\',\'callback is not defined!<br><br>Copy the template file from cache \"_list_action_'.$actionName.'.php\" to your application/modules/'.strtolower($this->getModuleName()).'/templates folder and alter it or define the \"callback\" in your generator.yml file\');";
  $objectActions->attributes["'.$name.'"] = $sfExtjs3Plugin->asMethod($configArr);
?>');
?>
<?php endif; ?>
<?php if(in_array($name, array('_delete', '_edit'))): ?>
include_partial('<?php echo 'objectAction_'.$name ?>', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'objectActions' => $objectActions));

<?php endif; ?>
<?php echo $this->addCredentialCondition($this->getObjectActionButton($name, $params), $params)."\n" ?>
<?php endforeach; ?>
<?php endif; ?>
<?php echo $this->getStandardPartials('objectActions') ?>
<?php echo $this->getCustomPartials('objectActions'); ?>

// create the Ext.app.sf.<?php echo $className ?> class
$sfExtjs3Plugin->beginClass(
  'Ext.app.sf',
  '<?php echo $className ?>',
  'Ext.ux.grid.RowActions',
  $objectActions->attributes
);

$sfExtjs3Plugin->endClass();
?]
// register xtype
Ext.reg('<?php echo $xtype ?>', Ext.app.sf.<?php echo $className ?>);
