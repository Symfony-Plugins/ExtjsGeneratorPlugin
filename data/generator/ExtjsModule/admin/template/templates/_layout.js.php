  <?php echo sfConfig::get('app_extjs_gen_plugin_module_gridpanel_name', 'Ext.app.sf.GridPanel') ?> = Ext.ComponentMgr.create({
    xtype : '<?php echo $this->getModuleName() ?>gridpanel',
    title: '<?php echo $this->configuration->getListTitle() ?>'
  });
<?php if (sfConfig::get('app_extjs_gen_plugin_module_returns_layout', true)): ?>
[?php include_partial('viewport', array('sfExtjs3Plugin' => $sfExtjs3Plugin))?]
<?php else: ?>

  <?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?> = Ext.ComponentMgr.create({
    xtype: '<?php echo $this->getModuleName() ?>tabpanel',
    items: <?php echo sfConfig::get('app_extjs_gen_plugin_module_gridpanel_name', 'Ext.app.sf.GridPanel') ?>

  });
<?php if($this->configuration->hasFilterForm()):?>
  <?php echo sfConfig::get('app_extjs_gen_plugin_module_filterpanel_name', 'Ext.app.sf.FilterPanel') ?> = Ext.ComponentMgr.create({
    xtype : '<?php echo $this->getModuleName() ?>filterpanel'
  });

  <?php echo sfConfig::get('app_extjs_gen_plugin_module_filterpanel_name', 'Ext.app.sf.FilterPanel') ?>.on('filter_reset', <?php echo sfConfig::get('app_extjs_gen_plugin_module_gridpanel_name', 'Ext.app.sf.GridPanel') ?>.resetFilter, <?php echo sfConfig::get('app_extjs_gen_plugin_module_gridpanel_name', 'Ext.app.sf.GridPanel') ?>);
  <?php echo sfConfig::get('app_extjs_gen_plugin_module_filterpanel_name', 'Ext.app.sf.FilterPanel') ?>.on('filter_set', <?php echo sfConfig::get('app_extjs_gen_plugin_module_gridpanel_name', 'Ext.app.sf.GridPanel') ?>.setFilter, <?php echo sfConfig::get('app_extjs_gen_plugin_module_gridpanel_name', 'Ext.app.sf.GridPanel') ?>);
<?php endif; ?>

<?php if(!sfConfig::get('app_extjs_gen_plugin_module_returns_layout')): ?>
[?php
  $partialArr = array();
  $partialArr['sfExtjs3Plugin'] = $sfExtjs3Plugin;
  $partialArr['gridpanel']      = '<?php echo sfConfig::get('app_extjs_gen_plugin_module_gridpanel_name', 'Ext.app.sf.GridPanel') ?>';
  $partialArr['tabpanel']       = '<?php echo sfConfig::get('app_extjs_gen_plugin_module_tabpanel_name', 'Ext.app.sf.TabPanel') ?>';
<?php if($this->configuration->hasFilterForm()):?>
  $partialArr['filterpanel']    = '<?php echo sfConfig::get('app_extjs_gen_plugin_module_filterpanel_name', 'Ext.app.sf.FilterPanel') ?>';
<?php endif; ?>
  include_partial('global/<?php echo sfConfig::get('app_extjs_gen_plugin_module_init_app_partial') ?>', $partialArr);
?]
<?php endif; ?>
<?php endif; ?>
