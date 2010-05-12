[?php // @object $sfExtjs3Plugin and @object $objectActions provided
  $configArr["parameters"] = "grid, record, action, row, col";
  $configArr["source"] =   $configArr["source"] = "
    var id = record.get('id');
    if(!<?php echo sfConfig::get('app_extjs_gen_plugin_module_tab_panel_name') ?>.findById(id+grid.title)){
      var formpanel = Ext.ComponentMgr.create({
        xtype: '<?php echo $this->getModuleName() ?>formpanel',
        id: id+grid.title,
        key: id,
        gridPanel: grid  
      });
      <?php echo sfConfig::get('app_extjs_gen_plugin_module_tab_panel_name') ?>.add(formpanel).show()
    } else {
      <?php echo sfConfig::get('app_extjs_gen_plugin_module_tab_panel_name') ?>.setActiveTab(<?php echo sfConfig::get('app_extjs_gen_plugin_module_tab_panel_name') ?>.findById(id+grid.title));
    }
";
  $objectActions->attributes["_edit"] = $sfExtjs3Plugin->asMethod($configArr);
?]