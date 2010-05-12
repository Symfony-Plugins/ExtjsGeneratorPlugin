<?php
  $moduleName = ucfirst(sfInflector::camelize($this->getModuleName()));
  $className = $moduleName.'ColumnRenderers';
  $xtype = $this->getModuleName().'columnrenderers';
?>
[?php
$className = '<?php echo $className ?>';
$columnRenderers = new stdClass();
$columnRenderers->attributes = array();

// renderLink
$columnRenderers->attributes['renderLink'] = $sfExtjs3Plugin->asMethod(array(
  'parameters' => 'value, params, record, rowIndex, colIndex, store',
  'source' => "
    if('function' == typeof value.dateFormat) value = this.formatDate(value);
    if (record) return String.format('<u><b><a class=\"grid_edit_link\" sf_ns:key=\"{0}\" href=\"#\">{1}</a></b></u>',
      record.data['<?php echo strtolower($this->getPrimaryKeys(true)) ?>'],
      value      
    );
    return value;
  "
));

$columnRenderers->attributes['formatLongstring'] = $sfExtjs3Plugin->asMethod(array(
  'parameters' => 'value, params, record, rowIndex, colIndex, store',
  'source' => "
    params.css += 'x-grid3-cell-wrap';
    value = Ext.util.Format.stripTags(value);
    return Ext.util.Format.ellipsis(value, 255);
  "
));

$columnRenderers->attributes['formatBoolean'] = $sfExtjs3Plugin->asMethod(array(
  'parameters' => 'value, params, record, rowIndex, colIndex, store',
  'source' => "return value ? 'Yes' : 'No';"
));

// formatDate
$renderers->attributes['formatDate'] = $sfExtjs3Plugin->asMethod(array(
  'parameters' => 'v',
  'source' => "return Ext.util.Format.date(v, 'm/d/Y')"
));

<?php echo $this->getStandardPartials('columnRenderers', array('initComponent')) ?>
<?php echo $this->getCustomPartials('columnRenderers'); ?>

// create the Ext.app.sf.<?php echo $className ?> class
$sfExtjs3Plugin->beginClass(
  'Ext.app.sf',
  '<?php echo $className ?>',
  'Ext.grid.ColumnModel',
  $columnRenderers->attributes
);

$sfExtjs3Plugin->endClass();
?]
// register xtype
Ext.reg('<?php echo $xtype ?>', Ext.app.sf.<?php echo $className ?>);
