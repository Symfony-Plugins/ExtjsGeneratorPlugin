[?php // @object $sfExtjs3Plugin string $className and @object gridpanel provided
// constructor
$configArr["parameters"] = "c";
$configArr["source"] = "
// gridpanel config
this.colModel = Ext.ComponentMgr.create({xtype:'<?php echo $this->getModuleName() ?>columnmodel'});
this.gridpanel_config = " . (isset($gridpanel->config_array) && count($gridpanel->config_array) ? $sfExtjs3Plugin->asAnonymousClass($gridpanel->config_array) : '{}') . ";

// combine gridpanel config with arguments
Ext.app.sf.$className.superclass.constructor.call(this, Ext.apply(this.gridpanel_config, c));";
$gridpanel->methods["constructor"] = $sfExtjs3Plugin->asMethod($configArr);
