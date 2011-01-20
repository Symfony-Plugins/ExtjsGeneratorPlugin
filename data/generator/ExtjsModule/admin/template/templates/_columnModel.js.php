<?php
  $moduleName = ucfirst(sfInflector::camelize($this->getModuleName()));
  $className = $moduleName."ColumnModel";
  $xtype = $this->getModuleName()."columnmodel";
  $extends = ($this->configuration->getColumnModelExtends()) ? $this->configuration->getColumnModelExtends() : "Ext.app.sf.{$moduleName}ColumnRenderers";
  $config = $this->configuration->getColumnModelConfig();
  $sm = $config['sm'];
  unset($config['sm']);
?>
[?php
$className = '<?php echo $className ?>';
$columnModel = new stdClass();
$columnModel->methods = array();
$columnModel->variables = array();

$columnModel->variables['sm'] = $sfExtjs3Plugin->asVar("<?php echo $sm ?>");

$columnModel->config_array = array(
<?php foreach ($config as $name => $params): ?>
  '<?php echo $name ?>' => <?php echo $this->asPhp($params) ?>,
<?php endforeach; ?>
);

<?php
foreach ($this->configuration->getValue('list.display') as $name => $field)
{
  echo $this->addCredentialCondition(sprintf("%s;\n", $this->renderColumnField($field)), $field->getConfig());
  echo $this->addCredentialCondition(sprintf("%s;\n", $this->renderColumnPlugin($field)), $field->getConfig());
}
?>

<?php echo $this->getStandardPartials('columnModel',array('initComponent')) ?>
<?php echo $this->getCustomPartials('columnModel'); ?>

// create the Ext.app.sf.<?php echo $className ?> class
$sfExtjs3Plugin->beginClass(
  'Ext.app.sf',
  '<?php echo $className ?>',
  '<?php echo $extends ?>',
  array_merge(
    $columnModel->methods,
    $columnModel->variables
  )
);

$sfExtjs3Plugin->endClass();
?]
// register xtype
Ext.reg('<?php echo $xtype ?>', Ext.app.sf.<?php echo $className ?>);
