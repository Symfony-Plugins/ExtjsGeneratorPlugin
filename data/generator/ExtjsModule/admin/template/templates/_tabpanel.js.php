<?php
  $moduleName = ucfirst(sfInflector::camelize($this->getModuleName()));
  $className = $moduleName.'TabPanel';
  $xtype = $this->getModuleName().'tabpanel';
?>
[?php
$className = '<?php echo $className ?>';
$tabpanel = new stdClass();
$tabpanel->attributes = array();

/* tabpanel configuration */
$tabpanel->config_array = array(
<?php foreach ($this->configuration->getTabpanelConfig() as $name => $params): ?>
  '<?php echo $name ?>' => <?php echo $this->asPhp($params) ?>,
<?php endforeach; ?>
);

<?php echo $this->getStandardPartials('tabpanel') ?>
<?php echo $this->getCustomPartials('tabpanel'); ?>

// create the Ext.app.sf.<?php echo $className ?> class
$sfExtjs3Plugin->beginClass(
  'Ext.app.sf',
  '<?php echo $className ?>',
  'Ext.TabPanel',
  $tabpanel->attributes
);

$sfExtjs3Plugin->endClass();
?]
// register xtype
Ext.reg('<?php echo $xtype ?>', Ext.app.sf.<?php echo $className ?>);
