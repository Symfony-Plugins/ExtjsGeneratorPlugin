
  <?php echo sfConfig::get('app_extjs_gen_plugin_module_filterpanel_name', 'Ext.app.sf.FilterPanel') ?> = Ext.ComponentMgr.create({
    xtype : '<?php echo $this->getModuleName() ?>filterpanel',
    region: 'west',
    width : 175
  });

  <?php echo sfConfig::get('app_extjs_gen_plugin_module_filterpanel_name', 'Ext.app.sf.FilterPanel') ?>.on('filter_reset', <?php echo sfConfig::get('app_extjs_gen_plugin_module_listpanel_name', 'Ext.app.sf.ListPanel') ?>.resetFilter, <?php echo sfConfig::get('app_extjs_gen_plugin_module_listpanel_name', 'Ext.app.sf.ListPanel') ?>);
  <?php echo sfConfig::get('app_extjs_gen_plugin_module_filterpanel_name', 'Ext.app.sf.FilterPanel') ?>.on('filter_set', <?php echo sfConfig::get('app_extjs_gen_plugin_module_listpanel_name', 'Ext.app.sf.ListPanel') ?>.setFilter, <?php echo sfConfig::get('app_extjs_gen_plugin_module_listpanel_name', 'Ext.app.sf.ListPanel') ?>);

  <?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?> = Ext.ComponentMgr.create({
    xtype : '<?php echo $this->getModuleName() ?>tabpanel',
    items : <?php echo sfConfig::get('app_extjs_gen_plugin_module_listpanel_name', 'Ext.app.sf.ListPanel') ?>,
    region: 'center'
  });

  Ext.app.sf.ViewPort = Ext.ComponentMgr.create({
    xtype : 'viewport',
    layout: 'border',
    items : [
      <?php echo sfConfig::get('app_extjs_gen_plugin_module_filterpanel_name', 'Ext.app.sf.FilterPanel') ?>,
      <?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?>

    ]
  });

  Ext.app.sf.ViewPort.doLayout();
