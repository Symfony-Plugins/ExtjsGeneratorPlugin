<?php
  $moduleName = ucfirst(sfInflector::camelize($this->getModuleName()));
  $className = $moduleName."GridPanel";
  $xtype = $this->getModuleName()."gridpanel";
?>
[?php
$className = '<?php echo $className ?>';
$gridpanel = new stdClass();
$gridpanel->methods = array();
$gridpanel->variables = array();
$gridpanelPlugins = array();

$gridpanel->variables['cm'] = $sfExtjs3Plugin->asVar("Ext.ComponentMgr.create({xtype:'<?php echo $this->getModuleName() ?>columnmodel'})");

/* gridpanel Configuration */
$gridpanel->config_array = array(
<?php foreach ($this->configuration->getGridpanelConfig() as $name => $params): ?>
  '<?php echo $name ?>' => <?php echo $this->asPhp($params) ?>,
<?php endforeach; ?>
);

$gridpanel->config_array['ds'] = "Ext.ComponentMgr.create({xtype:'<?php echo $this->getModuleName().strtolower($this->configuration->getDatastoreType()) ?>'})";

$gridpanel->config_array['view'] = $sfExtjs3Plugin->asCustomClass('Ext.grid.<?php echo $this->configuration->getGridpanelType() ?>View',array(
  'listeners' => array(
    'beforerefresh' => $sfExtjs3Plugin->asMethod(array(
      'parameters' => 'v',
      'source' => 'v.scrollTop = v.scroller.dom.scrollTop; v.scrollHeight = v.scroller.dom.scrollHeight;',
    )),
    'refresh' => $sfExtjs3Plugin->asMethod(array(
      'parameters' => 'v',
      'source' => 'v.scroller.dom.scrollTop = v.scrollTop + (v.scrollTop == 0 ? 0 : v.scroller.dom.scrollHeight - v.scrollHeight);',
    )),
  ),
<?php if($this->configuration->getGridpanelGroupTextTpl() != '') echo "  'groupTextTpl' => '{$this->configuration->getGridpanelGroupTextTpl()}'," ?>  
));

<?php //if (!empty($gridConfig['expander_partial'])): ?>
// initialise the row expander plugin
//$gridpanel->rowExpander = 'this.getRowExpander();';
<?php //endif; ?>

// get plugins from generator
<?php 
foreach ($this->configuration->getValue('list.display') as $name => $field)
{
  if($plugin = $this->renderGridPanelPlugin($field)) echo sprintf("%s;\n", $plugin);
}
?>
//merge plugins set from gridpanel.params with generated plugins
if(isset($gridpanel->config_array['plugins']))
{
  $gridpanel->config_array['plugins'] = array_merge($gridpanelPlugins, $gridpanel->config_array['plugins']);
}
elseif(count($gridpanelPlugins))
{
  $gridpanel->config_array['plugins'] = $gridpanelPlugins;
}

/* gridPanel methods and variables */

// initComponent
include_partial('gridpanel_method_initComponent', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'gridpanel' => $gridpanel, 'className' => $className));

// initEvents
include_partial('gridpanel_method_initEvents', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'gridpanel' => $gridpanel, 'className' => $className));

// onEditLinkClick
include_partial('gridpanel_method_onEditLinkClick', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'gridpanel' => $gridpanel, 'className' => $className));

// updateDatabase
include_partial('gridpanel_method_updateDatabase', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'gridpanel' => $gridpanel, 'className' => $className));

// setFilter
include_partial('gridpanel_method_setFilter', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'gridpanel' => $gridpanel, 'className' => $className));

// resetFilter
include_partial('gridpanel_method_resetFilter', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'gridpanel' => $gridpanel, 'className' => $className));

<?php echo $this->getStandardPartials('gridpanel',array('constructor')) ?>
<?php echo $this->getCustomPartials('gridpanel'); ?>
<?php //echo $gridConfig['expander_partial'] ?>

<?php //echo $this->getClassGetters('gridpanel',array('modulename','panelType')); ?>

// create the Ext.app.sf.<?php echo $className ?> class
$sfExtjs3Plugin->beginClass(
  'Ext.app.sf',
  '<?php echo $className ?>',
  'Ext.grid.EditorGridPanel',
  array_merge(
    $gridpanel->methods,
    $gridpanel->variables
  )
);
$sfExtjs3Plugin->endClass();
?]
// register xtype
Ext.reg('<?php echo $xtype ?>', Ext.app.sf.<?php echo $className ?>);