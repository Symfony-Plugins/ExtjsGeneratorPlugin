[?php // @object $sfExtjs3Plugin and @object $objectActions provided
  $configArr["parameters"] = "grid, record, action, row, col";
  $configArr["source"] =   $configArr["source"] = "
    var id = record.get('<?php echo sfInflector::underscore($this->getPrimaryKeys(true)) ?>');
    if(!<?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?>.findById(id+grid.title)){
      var formpanel = Ext.ComponentMgr.create({
        xtype: '<?php echo $this->getModuleName() ?>formpanel',
        id: id+grid.title,
        key: id,
        gridPanel: grid
      });
      <?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?>.add(formpanel).show()
    } else {
      <?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?>.setActiveTab(<?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?>.findById(id+grid.title));
    }
";
  $objectActions->methods["_edit"] = $sfExtjs3Plugin->asMethod($configArr);
?]