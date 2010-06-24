<?php
  $moduleName = ucfirst(sfInflector::camelize($this->getModuleName()));
  $className = $moduleName.'TopToolbar';
  $xtype = $this->getModuleName().'toptoolbar';
?>
[?php
$className = '<?php echo $className ?>';
$topToolbar = new stdClass();
$topToolbar->methods = array();
$topToolbar->variables = array();

/* topToolbar configuration */
$topToolbar->config_array = array(
<?php foreach ($this->configuration->getTopToolbarConfig() as $name => $params): ?>
  '<?php echo $name ?>' => <?php echo $this->asPhp($params) ?>,
<?php endforeach; ?>
);

// generate batch actions combo and button if batch actions are available
<?php if ($batchActions = $this->configuration->getValue('list.batch_actions')): ?>

<?php foreach ((array) $batchActions as $action => $params): ?>
<?php echo $this->addCredentialCondition("\$batchActionArr[] = array('$action', __('{$params['label']}', array(), '{$this->getI18nCatalogue()}'));", $params) ?>
<?php endforeach; ?>

$topToolbar->config_array['items'][] = array(
  'xtype' => 'twincombo',
  'id' => 'batch_action_combo',
  'hiddenName' => 'batch_action',
  'emptyText' => __('Choose an action', array(), '<?php echo $this->getI18nCatalogue() ?>'),
  'mode' => 'local',
  'triggerAction' => 'all',
  'hideLabel' => true,
  'valueField' => 'value',
  'displayField' => 'display',
  'store' => array(
    'xtype' => 'arraystore',
    'fields' => array(
      'value', 'display',
    ),
    'data' => $batchActionArr,
  ),
);

$form = new BaseForm();
$csrf = ''; 
if ($form->isCSRFProtected())
{
  $csrf = "{$form->getCSRFFieldName()}: '{$form->getCSRFToken()}',\n      ";  
}

$topToolbar->config_array['items'][] = array(
  'xtype' => 'tbbutton',
  'iconCls' => "Ext.ux.IconMgr.getIcon('add')",
  'handler' => $sfExtjs3Plugin->asMethod("
  var selections = this.ownerCt.getSelectionModel().getSelections();
  if(selections.length == 0){
    Ext.ux.MessageBox.error(
      '".__('Error!', array(), '<?php echo $this->getI18nCatalogue() ?>')."', 
      '".__('You must select at least one item.', array(), '<?php echo $this->getI18nCatalogue() ?>')."'
    );
  } else {
    this.ownerCt.getGridEl().mask()
    var action = Ext.ComponentMgr.get('batch_action_combo').getValue();
    var params = {  
      ".$csrf."batch_action: action
    };
    
    for (var i = 0; i < selections.length; i++) {
      params['ids['+i+']'] = selections[i].id;
    }
    
    Ext.Ajax.request({
      url:'".url_for('<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'batch')).".json',
      method : 'POST',
      params : params,
      success : function(response) {
        var json_response = Ext.util.JSON.decode(response.responseText);       
          
        if(json_response.success) {   
          Ext.ux.MessageBox.info(
            '".__('Successful!', array(), '<?php echo $this->getI18nCatalogue() ?>')."', 
            json_response.message,
            this.ownerCt.getStore().reload(),
            this
          );
        } else {
          Ext.ux.MessageBox.error(
            '".__('Error!', array(), '<?php echo $this->getI18nCatalogue() ?>')."', 
            json_response.message
          );
        }       
        this.ownerCt.getGridEl().unmask();        
      },
      failure: function(response) {
        Ext.ux.MessageBox.error(
          '".__('Error!', array(), '<?php echo $this->getI18nCatalogue() ?>')."', 
          '".__('The web server returned an unexpected response.', array(), '<?php echo $this->getI18nCatalogue() ?>')."'
        );
        this.ownerCt.getGridEl().unmask();
      },
      scope: this
    });

  }
"),
  'tooltip' => __('Perform action on selected records', array(), '<?php echo $this->getI18nCatalogue() ?>'),
  'scope' => 'this',
);

$topToolbar->config_array['items'][] = array(
  'xtype' => 'tbseparator'
);

<?php endif; ?>
// generate toolbar action handler partials
<?php if ($listActions = $this->configuration->getValue('list.actions')): ?>
<?php foreach ($listActions as $name => $params): ?>
<?php if(! isset($params['handler_function']) && $name[0] != '_'):
$this->createPartialFile('_listaction_'.$name, <<<EOT
<?php
/* @object \$sfExtjs3Plugin string \$className and @object \$topToolbar provided
*** Method example with no parameters
\$topToolbar->methods['$name'] = \$sfExtjs3Plugin->asMethod("
  //method code
");

*** Method example with parameters
\$configArr->['parameters'] = 'grid, record, action, row, col';
\$configArr->['source'] = "
  //method code
");
\$topToolbar->methods['$name'] = \$sfExtjs3Plugin->asMethod(\$configArr);
*/
\$configArr["source"] = "
  Ext.Msg.alert(\'Error\',\'handler_function is not defined!<br><br>Copy the template \"_listaction_$actionName.js.php\" from cache to your application/modules/'.strtolower(\$this->getModuleName()).'/templates folder and alter it or define the \"handler_function\" in your generator.yml file\');
";
\$topToolbar->methods['$name'] = \$sfExtjs3Plugin->asMethod(\$configArr);
?>
EOT

);
?>
<?php endif; ?>
<?php if(in_array($name, array('_new', '_export'))): ?>
include_partial('<?php echo 'listaction_'.$name ?>', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'topToolbar' => $topToolbar));

<?php endif; ?>
<?php echo $this->addCredentialCondition($this->getListActionButton($name, $params), $params)."\n" ?>
<?php endforeach; ?>
<?php endif; ?>
<?php echo $this->getStandardPartials('topToolbar') ?>
<?php echo $this->getCustomPartials('topToolbar'); ?>

// create the Ext.app.sf.<?php echo $className ?> class
$sfExtjs3Plugin->beginClass(
  'Ext.app.sf',
  '<?php echo $className ?>',
  'Ext.Toolbar',
  array_merge(
    $topToolbar->methods,
    $topToolbar->variables
  )
);

$sfExtjs3Plugin->endClass();
?]
// register xtype
Ext.reg('<?php echo $xtype ?>', Ext.app.sf.<?php echo $className ?>);
