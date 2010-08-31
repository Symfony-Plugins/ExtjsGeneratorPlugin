[?php // @object $sfExtjs3Plugin string $className and @object $formpanel provided
$formpanel->methods['close'] = $sfExtjs3Plugin->asMethod("
  <?php echo sfConfig::get('app_extjs_gen_plugin_module_listpanel_name', 'Ext.app.sf.ListPanel') ?>.getStore().reload();
  <?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?>.remove(this);
");
?]