[?php // @object $sfExtjs3Plugin and @object $topToolbar provided
  $configArr["parameters"] = "button, event";
  $configArr["source"] =   $configArr["source"] = "

  var formpanel = Ext.ComponentMgr.create({
    xtype: '<?php echo $this->getModuleName() ?>formpanel',
    gridPanel: this.ownerCt  
  });
  <?php echo sfConfig::get('app_extjs_gen_plugin_module_tab_panel_name') ?>.add(formpanel).show();
";
  $topToolbar->attributes["_new"] = $sfExtjs3Plugin->asMethod($configArr);
?]