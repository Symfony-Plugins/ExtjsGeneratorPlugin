[?php // @object $sfExtjs3Plugin string $className and @object $formpanel provided
$formpanel->methods['close'] = $sfExtjs3Plugin->asMethod("
  this.gridPanel.getStore().reload();
  <?php echo sfConfig::get('app_extjs_gen_plugin_module_tab_panel_name', 'Ext.app.sf.TabPanel') ?>.remove(this);
");
?]