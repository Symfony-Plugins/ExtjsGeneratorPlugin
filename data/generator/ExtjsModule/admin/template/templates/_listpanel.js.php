
Ext.ns('Ext.ux.list');
Ext.ux.list.LinkColumn = Ext.extend(Ext.list.Column, {
  constructor : function(c) {
    c.tpl = c.tpl || new Ext.XTemplate('{[this.format(values, "' + c.dataIndex + '")]}');

    c.tpl.format = function(values, index) {
      var v = (values[index] && 'function' == typeof values[index].dateFormat) ? Ext.util.Format.date(values[index], 'm/d/Y') : (values[index]) ? values[index] : 'not set';
      return String.format('<u><b><a class="listview_edit_link" sf_ns:key="{0}" href="#">{1}</a></b></u>', values['<?php echo $this->translateColumnName($this->getTableMap()->getColumnByPhpName($this->getPrimaryKeys(true))) ?>'], v);
    };

    Ext.ux.list.LinkColumn.superclass.constructor.call(this, c);
  }
});
Ext.reg('lvlinkcolumn', Ext.ux.list.LinkColumn);

<?php
  $moduleName = ucfirst(sfInflector::camelize($this->getModuleName()));
  $className = $moduleName."ListPanel";
  $xtype = $this->getModuleName()."listpanel";
  $extends = ($this->configuration->getListpanelExtends()) ? $this->configuration->getListpanelExtends() : 'Ext.ux.ListViewPanel';
?>
[?php
$className = '<?php echo $className ?>';
$listpanel = new stdClass();
$listpanel->methods = array();
$listpanel->variables = array();
$listpanelPlugins = array();

/* listpanel Configuration */
$listpanel->config_array = array(
<?php foreach ($this->configuration->getListpanelConfig() as $name => $params): ?>
  '<?php echo $name ?>' => <?php echo $this->asPhp($params) ?>,
<?php endforeach; ?>
);

$listpanel->config_array['ds'] = "Ext.ComponentMgr.create({xtype:'<?php echo $this->getModuleName().strtolower($this->configuration->getDatastoreType()) ?>'})";

// initComponent
include_partial('listpanel_method_initComponent', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'listpanel' => $listpanel, 'className' => $className));

// initEvents
include_partial('listpanel_method_initEvents', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'listpanel' => $listpanel, 'className' => $className));

// buildListView
include_partial('listpanel_method_buildListView', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'listpanel' => $listpanel, 'className' => $className));

// setFilter
include_partial('listpanel_method_setFilter', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'listpanel' => $listpanel, 'className' => $className));

// resetFilter
include_partial('listpanel_method_resetFilter', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'listpanel' => $listpanel, 'className' => $className));

// onEditLinkClick
include_partial('listpanel_method_onEditLinkClick', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'listpanel' => $listpanel, 'className' => $className));

<?php echo $this->getStandardPartials('listpanel',array('constructor')) ?>
<?php echo $this->getCustomPartials('listpanel'); ?>

// create the Ext.app.sf.<?php echo $className ?> class
$sfExtjs3Plugin->beginClass(
  'Ext.app.sf',
  '<?php echo $className ?>',
  '<?php echo $extends ?>',
  array_merge(
    $listpanel->methods,
    $listpanel->variables
  )
);
$sfExtjs3Plugin->endClass();
?]
// register xtype
Ext.reg('<?php echo $xtype ?>', Ext.app.sf.<?php echo $className ?>);