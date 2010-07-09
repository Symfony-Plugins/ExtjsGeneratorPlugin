<?php
  $moduleName = ucfirst(sfInflector::camelize($this->getModuleName()));
  $className = $moduleName."ObjectActions";
  $xtype = $this->getModuleName()."objectactions";
?>
[?php
$className = '<?php echo $className ?>';
$objectActions = new stdClass();
$objectActions->methods = array();
$objectActions->variables = array();

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
$this->createPartialFile('_objectAction_'.$name, <<<EOT
<?php
/* @object \$sfExtjs3Plugin string \$className and @object \$objectActions provided
*** Method example with no parameters
\$objectActions->methods['$name'] = \$sfExtjs3Plugin->asMethod("
  //method code
");

*** Method example with parameters
\$configArr->['parameters'] = 'grid, record, action, row, col';
\$configArr->['source'] = "
  //method code
");
\$objectActions->methods['$name'] = \$sfExtjs3Plugin->asMethod(\$configArr);
*/
\$configArr["source"] = "
  Ext.Msg.alert('Error','handler_function is not defined!<br><br>Copy the template \"_objectAction_$name.js.php\" from cache to your application/modules/{$this->getModuleName()}/templates folder and alter it or define the \"handler_function\" in your generator.yml file');
";
\$objectActions->methods['$name'] = \$sfExtjs3Plugin->asMethod(\$configArr);
?>
EOT

);
?>
include_partial('<?php echo 'objectAction_'.$name ?>', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'objectActions' => $objectActions));

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
  'Ext.ux.grid.plugin.RowActions',
  array_merge(
    $objectActions->methods,
    $objectActions->variables
  )
);

$sfExtjs3Plugin->endClass();
?]
// register xtype
Ext.reg('<?php echo $xtype ?>', Ext.app.sf.<?php echo $className ?>);
