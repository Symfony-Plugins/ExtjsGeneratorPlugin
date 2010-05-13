<?php

/**
 * ExtjsGeneratorPlugin configuration.
 *
 * @package     ExtjsGeneratorPlugin
 * @subpackage  config
 * @author      Benjamin Runnels <benjamin.r.runnels@citi.com>
 */
class ExtjsGeneratorPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    // Route to xtype-script-getter
    $this->dispatcher->connect('routing.load_configuration', array('ExtjsGeneratorPluginRouting', 'listenToRoutingLoadConfigurationEvent'));

    //automatically enable module
    if(is_array(sfConfig::get('sf_enabled_modules')))
    {
      $moduleArr = array('ExtjsGeneratorPluginXtypeManager');
      foreach ($moduleArr as $module)
      {
        if (!in_array($module, sfConfig::get('sf_enabled_modules')))
        {
          $enabled = sfConfig::get('sf_enabled_modules');
          array_unshift($enabled,$module);
          sfConfig::set('sf_enabled_modules',$enabled);
        }
      }
    }

    $quoteExcept = array(
      'key' => array('renderer', 'store'),
      'value' => array('true', 'false', 'new Ext.', 'function', 'Ext.')
    );
    
    sfConfig::set('extjs_quote_except', $quoteExcept);
    
//    sfConfig::set('sf_extjs3_classes', array_merge(sfConfig::get('sf_extjs3_classes'), array(
//      'DateField' => 'Ext.form.DateField',
//    )));    

    $default_stylesheets = array(
    );

    $default_javascripts = array(
      '/ExtjsGeneratorPlugin/js/ext-basex.js',  // BaseX/JIT 4.1 library, used for monitoring XHR requests (monitoring credentials) and lazy loading
      '/ExtjsGeneratorPlugin/js/jit.js',  // BaseX/JIT 4.1 library, used for monitoring XHR requests (monitoring credentials) and lazy loading
      '/ExtjsGeneratorPlugin/js/Ext.ComponentMgr.create.createInterceptor.js',  // Interceptor for create method to lazy-load xtypes, REQUIRES INITIALISATION!    
      '/ExtjsGeneratorPlugin/Ext.ux.IconMgr/Ext.ux.IconMgr.js',  // icon manager extension.  goes first so we can use it anywhere
      '/ExtjsGeneratorPlugin/js/ExtjsGeneratorConstants.js',  // Generator javascript constants
      '/ExtjsGeneratorPlugin/js/Ext.ux.TabCloseMenu.js',  // simple context menu for closing tabs or multiple tabs
      '/ExtjsGeneratorPlugin/js/Ext.ux.grid.RowActions.js',
      '/ExtjsGeneratorPlugin/js/Ext.form.TextField.override.js',  //adds reset event
      '/ExtjsGeneratorPlugin/js/Ext.grid.GridPanel.override.js',
      '/ExtjsGeneratorPlugin/js/Ext.ux.MessageBox.js',  // adds autohiding info and error message types
      '/ExtjsGeneratorPlugin/js/Ext.ux.form.MultiSelect.js',
      '/ExtjsGeneratorPlugin/js/Ext.ux.form.ItemSelector.js',
      '/ExtjsGeneratorPlugin/js/Ext.ux.form.TwinDateField.js',
      '/ExtjsGeneratorPlugin/js/Ext.ux.form.TwinComboBox.js',
      '/ExtjsGeneratorPlugin/js/Ext.ux.grid.CheckColumn.js',
      '/ExtjsGeneratorPlugin/js/Ext.data.HttpProxy.override.js',  // adds setMethod method     
    );

    sfConfig::set('extjs_gen_default_javascripts', $default_javascripts);
    sfConfig::set('extjs_gen_default_stylesheets', $default_stylesheets);
  }
}
