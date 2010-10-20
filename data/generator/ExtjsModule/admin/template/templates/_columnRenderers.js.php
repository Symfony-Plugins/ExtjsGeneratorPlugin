<?php
  $moduleName = ucfirst(sfInflector::camelize($this->getModuleName()));
  $className = $moduleName.'ColumnRenderers';
  $xtype = $this->getModuleName().'columnrenderers';
  $extends = ($this->configuration->getColumnRenderersExtends()) ? $this->configuration->getColumnRenderersExtends() : 'Ext.grid.ColumnModel';
?>
[?php
$className = '<?php echo $className ?>';
$columnRenderers = new stdClass();
$columnRenderers->methods = array();
$columnRenderers->variables = array();

// renderLink
$columnRenderers->methods['renderLink'] = $sfExtjs3Plugin->asMethod(array(
  'parameters' => 'value, params, record, rowIndex, colIndex, store',
  'source' => "
    if('function' == typeof value.dateFormat) value = this.formatDate(value);
    if (record) return String.format('<u><b><a class=\"grid_edit_link\" sf_ns:key=\"{0}\" href=\"#\">{1}</a></b></u>',
      record.data['<?php echo sfInflector::underscore($this->getPrimaryKeys(true)) ?>'],
      value      
    );
    return value;
  "
));

$columnRenderers->methods['formatLongstring'] = $sfExtjs3Plugin->asMethod(array(
  'parameters' => 'value, params, record, rowIndex, colIndex, store',
  'source' => "
    params.css += 'x-grid3-cell-wrap';
    value = Ext.util.Format.stripTags(value);
    return Ext.util.Format.ellipsis(value, 255);
  "
));

$columnRenderers->methods['formatBoolean'] = $sfExtjs3Plugin->asMethod(array(
  'parameters' => 'value, params, record, rowIndex, colIndex, store',
  'source' => "return value ? 'Yes' : 'No';"
));

// formatDate
$columnRenderers->methods['formatDate'] = $sfExtjs3Plugin->asMethod(array(
  'parameters' => 'v',
  'source' => "return Ext.util.Format.date(v, 'm/d/Y')"
));

<?php echo $this->getStandardPartials('columnRenderers', array('initComponent')) ?>
<?php echo $this->getCustomPartials('columnRenderers'); ?>

// create the Ext.app.sf.<?php echo $className ?> class
$sfExtjs3Plugin->beginClass(
  'Ext.app.sf',
  '<?php echo $className ?>',
  '<?php echo $extends ?>',
  array_merge(
    $columnRenderers->methods,
    $columnRenderers->variables
  )
);

$sfExtjs3Plugin->endClass();
?]
// register xtype
Ext.reg('<?php echo $xtype ?>', Ext.app.sf.<?php echo $className ?>);
