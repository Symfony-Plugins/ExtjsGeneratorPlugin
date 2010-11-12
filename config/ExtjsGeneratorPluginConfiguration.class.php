<?php

/**
 * ExtjsGeneratorPlugin configuration.
 *
 * @package     ExtjsGeneratorPlugin
 * @subpackage  config
 * @author      Benjamin Runnels <kraven@kraven.org>
 */
class ExtjsGeneratorPluginConfiguration extends sfPluginConfiguration
{

  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    //automatically enable modules
    if(is_array(sfConfig::get('sf_enabled_modules')))
    {
      $moduleArr = array(
        'ExtjsGeneratorPluginXtypeManager',
        'IconMgrPreview'
      );
      foreach($moduleArr as $module)
      {
        if(! in_array($module, sfConfig::get('sf_enabled_modules')))
        {
          $enabled = sfConfig::get('sf_enabled_modules');
          array_unshift($enabled, $module);
          sfConfig::set('sf_enabled_modules', $enabled);
        }
      }
    }

    $default_stylesheets = array(
      '/ExtjsGeneratorPlugin/css/ExtjsGeneratorPlugin.css',
    );

    $listpanel_stylesheets = array(
      '/ExtjsGeneratorPlugin/css/Ext.ux.list.ProgressColumn.css',
    );

    $gridpanel_stylesheets = array(
      '/ExtjsGeneratorPlugin/css/Ext.ux.grid.plugin.ProgressColumn.css',
    );

    $default_javascripts = array(
      '/ExtjsGeneratorPlugin/js/default/ext-basex.js',  // BaseX 4.1 library, used for monitoring XHR requests (monitoring credentials) and lazy loading
      '/ExtjsGeneratorPlugin/js/default/Ext.ComponentMgr.interceptors.js',  // Interceptors to lazy-load xtypes and pytpes
      '/ExtjsGeneratorPlugin/Ext.ux.IconMgr/Ext.ux.IconMgr.js',  // icon manager extension.  goes first so we can use it anywhere
      '/ExtjsGeneratorPlugin/js/default/ExtjsGeneratorConstants.js',  // Generator javascript constants
      '/ExtjsGeneratorPlugin/js/default/Ext.data.HttpProxy.override.js', // adds setMethod method
      '/ExtjsGeneratorPlugin/js/default/Ext.ux.MessageBox.js',  // adds autohiding info and error message types
      '/ExtjsGeneratorPlugin/js/default/Ext.ux.tabpanel.plugin.TabCloseMenu.js',  // simple context menu for closing tabs or multiple tabs
    );

    $formpanel_javascripts = array(
      '/ExtjsGeneratorPlugin/js/formpanel/Ext.form.TextField.override.js',  //adds reset event
      '/ExtjsGeneratorPlugin/js/formpanel/Ext.form.Hidden.override.js',  //disables reset method
      '/ExtjsGeneratorPlugin/js/formpanel/Ext.form.Field.override.js',  //adds support for required config
      '/ExtjsGeneratorPlugin/js/formpanel/Ext.form.TriggerField.override.js', //makes readonly triggerfields look like disabled
      '/ExtjsGeneratorPlugin/js/formpanel/Ext.form.Checkbox.override.js',  //prevents toggle on readyOnly
      '/ExtjsGeneratorPlugin/js/formpanel/Ext.ux.form.ComboBox.plugin.ComboListAutoSizer.js',
      '/ExtjsGeneratorPlugin/js/formpanel/Ext.ux.form.field.plugin.FieldHelp.js',
      '/ExtjsGeneratorPlugin/js/formpanel/Ext.ux.form.MultiSelect.js',
      '/ExtjsGeneratorPlugin/js/formpanel/Ext.ux.form.ItemSelector.js',
      '/ExtjsGeneratorPlugin/js/formpanel/Ext.ux.form.TwinDateField.js',
      '/ExtjsGeneratorPlugin/js/formpanel/Ext.ux.form.TwinDateTimeField.js',
      '/ExtjsGeneratorPlugin/js/formpanel/Ext.ux.form.TwinComboBox.js',
      '/ExtjsGeneratorPlugin/js/formpanel/Ext.ux.form.IsEmptyCheckbox.js',
      '/ExtjsGeneratorPlugin/js/formpanel/Ext.ux.form.TwinFileUploadField.js',
      '/ExtjsGeneratorPlugin/js/formpanel/Ext.ux.form.PlainTextField.js',
    );

    $listpanel_javascripts = array(
      '/ExtjsGeneratorPlugin/js/listpanel/Ext.ux.ListViewPanel.js',
      '/ExtjsGeneratorPlugin/js/listpanel/Ext.ux.list.GroupingListView.js',
      '/ExtjsGeneratorPlugin/js/listpanel/Ext.ux.list.CheckColumn.js',
      '/ExtjsGeneratorPlugin/js/listpanel/Ext.ux.list.ProgressColumn.js',
      '/ExtjsGeneratorPlugin/js/listpanel/Ext.ux.ListView.plugin.RowActions.js',
      '/ExtjsGeneratorPlugin/js/listpanel/Ext.ux.ListView.plugin.CheckColumn.js',
      '/ExtjsGeneratorPlugin/js/listpanel/Ext.ux.ListView.plugin.CheckboxSelection.js'
    );

    $gridpanel_javascripts = array(
      '/ExtjsGeneratorPlugin/js/gridpanel/Ext.grid.GridPanel.override.js',
      '/ExtjsGeneratorPlugin/js/gridpanel/Ext.ux.grid.CheckColumn.js',
      '/ExtjsGeneratorPlugin/js/gridpanel/Ext.ux.grid.ForeignFieldColumn.js',
      '/ExtjsGeneratorPlugin/js/gridpanel/Ext.ux.grid.plugin.RowActions.js',
      '/ExtjsGeneratorPlugin/js/gridpanel/Ext.ux.grid.plugin.ProgressColumn.js',      
    );

    $prod_default_javascripts = array(
      '/ExtjsGeneratorPlugin/Ext.ux.IconMgr/Ext.ux.IconMgr-min.js',
      '/ExtjsGeneratorPlugin/js/ExtjsGeneratorPlugin-Default-min.js' // YUI Compressor file of all default javascript files
    );
    
    $prod_formpanel_javascripts = array(
      '/ExtjsGeneratorPlugin/js/ExtjsGeneratorPlugin-FormPanel-min.js' // YUI Compressor file of all formpanel javascript files
    );

    $prod_listpanel_javascripts = array(
      '/ExtjsGeneratorPlugin/js/ExtjsGeneratorPlugin-ListPanel-min.js' // YUI Compressor file of all listpanel javascript files
    );

    $prod_gridpanel_javascripts = array(
      '/ExtjsGeneratorPlugin/js/ExtjsGeneratorPlugin-GridPanel-min.js' // YUI Compressor file of all gridpanel javascript files
    );

    sfConfig::set('extjs_gen_default_javascripts', (sfConfig::get('sf_environment') == 'dev') ? $default_javascripts : $prod_default_javascripts);
    sfConfig::set('extjs_gen_formpanel_javascripts', (sfConfig::get('sf_environment') == 'dev') ? $formpanel_javascripts : $prod_formpanel_javascripts);
    sfConfig::set('extjs_gen_listpanel_javascripts', (sfConfig::get('sf_environment') == 'dev') ? $listpanel_javascripts : $prod_listpanel_javascripts);
    sfConfig::set('extjs_gen_gridpanel_javascripts', (sfConfig::get('sf_environment') == 'dev') ? $gridpanel_javascripts : $prod_gridpanel_javascripts);
    sfConfig::set('extjs_gen_default_stylesheets', $default_stylesheets);
    sfConfig::set('extjs_gen_listpanel_stylesheets', $listpanel_stylesheets);
    sfConfig::set('extjs_gen_gridpanel_stylesheets', $gridpanel_stylesheets);

    // add support for our javascript ux files to sfExtjs3Plugin
    sfConfig::set('sf_extjs3_classes', array_merge(array(
      'TwinDateField' => 'Ext.ux.form.TwinDateField',
      'TwinDateTimeField' => 'Ext.ux.form.TwinDateTimeField',
      'TwinComboBox' => 'Ext.ux.form.TwinComboBox',
      'MultiSelect' => 'Ext.ux.form.MultiSelect',
      'ItemSelector' => 'Ext.ux.form.ItemSelector',
      'IsEmptyCheckbox' => 'Ext.ux.form.IsEmptyCheckbox',
      'TwinFileUploadField' => 'Ext.ux.form.TwinFileUploadField',
      'PlainTextField' => 'Ext.ux.form.PlainTextField',
    ), sfConfig::get('sf_extjs3_classes', array())));

    sfConfig::set('Ext.ux.form.TwinDateField', array(
      'class' => 'Ext.ux.form.TwinDateField',
      'attributes' => array()
    ));
    
    sfConfig::set('Ext.ux.form.TwinDateTimeField', array(
      'class' => 'Ext.ux.form.TwinDateTimeField',
      'attributes' => array()
    ));

    sfConfig::set('Ext.ux.form.TwinComboBox', array(
      'class' => 'Ext.ux.form.TwinComboBox',
      'attributes' => array()
    ));

    sfConfig::set('Ext.ux.form.MultiSelect', array(
      'class' => 'Ext.ux.form.MultiSelect',
      'attributes' => array()
    ));

    sfConfig::set('Ext.ux.form.ItemSelector', array(
      'class' => 'Ext.ux.form.ItemSelector',
      'attributes' => array()
    ));

    sfConfig::set('Ext.ux.form.IsEmptyCheckbox', array(
      'class' => 'Ext.ux.form.IsEmptyCheckbox',
      'attributes' => array()
    ));

    sfConfig::set('Ext.ux.form.TwinFileUploadField', array(
      'class' => 'Ext.ux.form.TwinFileUploadField',
      'attributes' => array()
    ));
    
    sfConfig::set('Ext.ux.form.PlainTextField', array(
      'class' => 'Ext.ux.form.PlainTextField',
      'attributes' => array()
    ));

  }
}
