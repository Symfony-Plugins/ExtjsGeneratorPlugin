[?php // @object $sfExtjs3Plugin string $className and @object $topToolbar provided
  $configArr["parameters"] = "button, event";
  $configArr["source"] =   $configArr["source"] = "

  var formpanel = Ext.ComponentMgr.create({
    xtype: '<?php echo $this->getModuleName() ?>formpanel',
    title: '<?php echo $this->configuration->getNewTitle() ?>',
    gridPanel: this.ownerCt
  });
  <?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?>.add(formpanel).show();
";
  $topToolbar->methods["_new"] = $sfExtjs3Plugin->asMethod($configArr);
?]