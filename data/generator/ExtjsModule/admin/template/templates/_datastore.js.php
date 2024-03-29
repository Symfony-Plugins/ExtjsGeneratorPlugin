<?php
  $moduleName = ucfirst(sfInflector::camelize($this->getModuleName()));
  $store = $this->configuration->getDatastoreType();
  $className = $moduleName.$store;
  $xtype = $this->getModuleName().strtolower($store);
  $extends = ($this->configuration->getDatastoreExtends()) ? $this->configuration->getDatastoreExtends() : "Ext.data.$store";
?>
[?php
$className = '<?php echo $className ?>';
$datastore = new stdClass();
$datastore->methods = array();
$datastore->variables = array();

/* store configuration */
$datastore->config_array = array(
<?php foreach ($this->configuration->getDatastoreConfig() as $name => $params): ?>
  '<?php echo $name ?>' => <?php echo $this->asPhp($params) ?>,
<?php endforeach; ?>
);

$datastore->config_array['proxy'] = $sfExtjs3Plugin->HttpProxy(array(
  'url' => url_for('@<?php echo $this->params['route_prefix'] ?>').'/index.json',
  'method' => 'GET',
));

$readerFields = array();
<?php
$key = $this->translateColumnName($this->getTableMap()->getColumnByPhpName($this->getPrimaryKeys(true)));
$fields = $this->configuration->getValue('list.display');

if(!array_key_exists($key, $fields)) $fields = array( $key => $this->configuration->getFieldConfiguration('list', $key)) + $fields;

foreach ( $fields as $name => $field)
{
  echo $this->addCredentialCondition((sprintf("%s;\n", $this->renderJsonReaderField($field))), $field->getConfig());
}
?>

$datastore->config_array['reader'] = $sfExtjs3Plugin->JsonReader(array(
  'root' => 'data',
  'totalProperty' => 'totalCount',
  'fields' => $readerFields,
));

<?php echo $this->getStandardPartials('datastore') ?>
<?php echo $this->getCustomPartials('datastore'); ?>

// create the Ext.app.sf.<?php echo $className ?> class
$sfExtjs3Plugin->beginClass(
  'Ext.app.sf',
  '<?php echo $className ?>',
  '<?php echo $extends ?>',
  array_merge(
    $datastore->methods,
    $datastore->variables
  )
);

$sfExtjs3Plugin->endClass();
?]
// register xtype
Ext.reg('<?php echo $xtype ?>', Ext.app.sf.<?php echo $className ?>);
