  <?php echo sfConfig::get('app_extjs_gen_plugin_module_listpanel_name', 'Ext.app.sf.ListPanel') ?> = Ext.ComponentMgr.create({
    xtype : '<?php echo $this->getModuleName() . $this->configuration->getListLayout() ?>',
    title: '<?php echo $this->configuration->getListTitle() ?>'
  });
<?php if (sfConfig::get('app_extjs_gen_plugin_module_returns_layout', true)): ?>
[?php include_partial('viewport', array('sfExtjs3Plugin' => $sfExtjs3Plugin))?]
<?php else: ?>

  <?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?> = Ext.ComponentMgr.create({
    xtype: '<?php echo $this->getModuleName() ?>tabpanel',
    items: <?php echo sfConfig::get('app_extjs_gen_plugin_module_listpanel_name', 'Ext.app.sf.ListPanel') ?>

  });
<?php if($this->configuration->hasFilterForm() && !$this->configuration->filterpanelIsDisabled()):?>
  <?php echo sfConfig::get('app_extjs_gen_plugin_module_filterpanel_name', 'Ext.app.sf.FilterPanel') ?> = Ext.ComponentMgr.create({
    xtype : '<?php echo $this->getModuleName() ?>filterpanel'
  });

  <?php echo sfConfig::get('app_extjs_gen_plugin_module_filterpanel_name', 'Ext.app.sf.FilterPanel') ?>.on('filter_reset', <?php echo sfConfig::get('app_extjs_gen_plugin_module_listpanel_name', 'Ext.app.sf.ListPanel') ?>.resetFilter, <?php echo sfConfig::get('app_extjs_gen_plugin_module_listpanel_name', 'Ext.app.sf.ListPanel') ?>);
  <?php echo sfConfig::get('app_extjs_gen_plugin_module_filterpanel_name', 'Ext.app.sf.FilterPanel') ?>.on('filter_set', <?php echo sfConfig::get('app_extjs_gen_plugin_module_listpanel_name', 'Ext.app.sf.ListPanel') ?>.setFilter, <?php echo sfConfig::get('app_extjs_gen_plugin_module_listpanel_name', 'Ext.app.sf.ListPanel') ?>);
<?php endif; ?>

<?php if(!sfConfig::get('app_extjs_gen_plugin_module_returns_layout')): ?>
[?php
  $partialArr = array();
  $partialArr['sfExtjs3Plugin'] = $sfExtjs3Plugin;
  $partialArr['gridpanel']      = '<?php echo sfConfig::get('app_extjs_gen_plugin_module_listpanel_name', 'Ext.app.sf.ListPanel') ?>';
  $partialArr['tabpanel']       = '<?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?>';
<?php if($this->configuration->hasFilterForm() && !$this->configuration->filterpanelIsDisabled()):?>
  $partialArr['filterpanel']    = '<?php echo sfConfig::get('app_extjs_gen_plugin_module_filterpanel_name', 'Ext.app.sf.FilterPanel') ?>';
<?php endif; ?>
  include_partial('global/<?php echo sfConfig::get('app_extjs_gen_plugin_module_init_app_partial', 'init_app') ?>', $partialArr);
?]
<?php endif; ?>
<?php endif; ?>
