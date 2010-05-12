<?php
  $moduleName = ucfirst(sfInflector::camelize($this->getModuleName()));
  $className = $moduleName."ColumnModel";
  $xtype = $this->getModuleName()."columnmodel";
?>
[?php
$className = '<?php echo $className ?>';
$columnModel = new stdClass();
$columnModel->attributes = array();

$columnModel->config_array = array(
<?php foreach ($this->configuration->getColumnModelConfig() as $name => $params): ?>
  '<?php echo $name ?>' => <?php echo $this->asPhp($params) ?>,
<?php endforeach; ?>
);

<?php 
foreach ($this->configuration->getValue('list.display') as $name => $field)
{
  echo $this->addCredentialCondition(sprintf("%s;\n", $this->renderColumnModelField($field)), $field->getConfig());
}

foreach ($this->configuration->getValue('list.display') as $name => $field)
{
  if($field->isPlugin())
  {
    echo $this->addCredentialCondition(sprintf("%s;\n", $this->renderColumnModelPlugin($field)), $field->getConfig());
  }
}
?>

<?php echo $this->getStandardPartials('columnModel',array('initComponent', 'constructor')) ?>
<?php echo $this->getCustomPartials('columnModel'); ?>

// create the Ext.app.sf.<?php echo $className ?> class
$sfExtjs3Plugin->beginClass(
  'Ext.app.sf',
  '<?php echo $className ?>',
  'Ext.app.sf.<?php echo $moduleName."ColumnRenderers" ?>',
  $columnModel->attributes
);

$sfExtjs3Plugin->endClass();
?]
// register xtype
Ext.reg('<?php echo $xtype ?>', Ext.app.sf.<?php echo $className ?>);
