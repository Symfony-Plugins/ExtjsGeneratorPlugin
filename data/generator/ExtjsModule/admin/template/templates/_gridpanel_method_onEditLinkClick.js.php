[?php // @object $sfExtjs3Plugin string $className and @object $gridpanel provided
// onLinkClick
$configArr = Array(
  'parameters' => 'e, t',
  'source' => "
    var el = Ext.get(e.getTarget());
    var id = el.getAttribute('key','sf_ns');
    if(!<?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?>.findById(id+this.title)){
      var formpanel = Ext.ComponentMgr.create({
        xtype: '<?php echo $this->getModuleName() ?>formpanel',
        id: id+this.title,
        key: id
      });
      <?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?>.add(formpanel).show()
    } else {
      <?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?>.setActiveTab(<?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?>.findById(id+this.title));
    }
  "
);

$gridpanel->methods['onEditLinkClick'] = $sfExtjs3Plugin->asMethod($configArr);
?]