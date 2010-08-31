[?php // @object $sfExtjs3Plugin and @object $objectActions provided
<?php if($this->configuration->getListLayout() == 'listpanel'): ?>  
  $configArr['parameters'] = 'view, record, action, node, index';
  $configArr['source'] =   $configArr["source"] = "
    var title = view.ownerCt.title;
<?php else: ?>
  $configArr['parameters'] = 'grid, record, action, row, col';
  $configArr['source'] =   $configArr["source"] = "
    var title = grid.title;
<?php endif; ?>
    var id = record.get('<?php echo sfInflector::underscore($this->getPrimaryKeys(true)) ?>');
    if(!<?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?>.findById(id+title)){
      var formpanel = Ext.ComponentMgr.create({
        xtype: '<?php echo $this->getModuleName() ?>formpanel',
        id: id+title,
        key: id,
<?php if($this->configuration->getListLayout() == 'listpanel'): ?>          
        gridPanel: view
<?php else: ?>
        gridPanel: grid
<?php endif; ?>        
      });
      <?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?>.add(formpanel).show()
    } else {
      <?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?>.setActiveTab(<?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?>.findById(id+title));
    }
";
  $objectActions->methods['_edit'] = $sfExtjs3Plugin->asMethod($configArr);
?]