<?php
  $moduleName = ucfirst(sfInflector::camelize($this->getModuleName()));
  $className = $moduleName.'BottomToolbar';
  $xtype = $this->getModuleName().'bottomtoolbar';
  $extends = ($this->configuration->getBottomToolbarExtends()) ? $this->configuration->getBottomToolbarExtends() : 'Ext.PagingToolbar';
?>
[?php
$className = '<?php echo $className ?>';
$bottomToolbar = new stdClass();
$bottomToolbar->methods = array();
$bottomToolbar->variables = array();

/* bottomToolbar configuration */
$bottomToolbar->config_array = array(
<?php foreach ($this->configuration->getBottomToolbarConfig() as $name => $params): ?>
  '<?php echo $name ?>' => <?php echo $this->asPhp($params) ?>,
<?php endforeach; ?>
);

$bottomToolbar->config_array['displayMsg'] = __($bottomToolbar->config_array['displayMsg'], array(), '<?php echo $this->getI18nCatalogue() ?>');
$bottomToolbar->config_array['emptyMsg'] = __($bottomToolbar->config_array['emptyMsg'], array(), '<?php echo $this->getI18nCatalogue() ?>');

<?php echo $this->getStandardPartials('bottomToolbar') ?>
<?php echo $this->getCustomPartials('bottomToolbar'); ?>

// create the Ext.app.sf.<?php echo $className ?> class
$sfExtjs3Plugin->beginClass(
  'Ext.app.sf',
  '<?php echo $className ?>',
  '<?php echo $extends ?>',
  array_merge(
    $bottomToolbar->methods,
    $bottomToolbar->variables
  )
);

$sfExtjs3Plugin->endClass();
?]
// register xtype
Ext.reg('<?php echo $xtype ?>', Ext.app.sf.<?php echo $className ?>);
