[?php // @string $csrf @integer $batchId @object $sfExtjs3Plugin and @object $topToolbar provided
$configArr['source'] = "
<?php if($this->configuration->getListLayout() == 'gridpanel'): ?>  
var selections = Ext.app.sf.ListPanel.getSelectionModel().getSelections();
<?php else: ?>
var selections = Ext.app.sf.ListPanel.getView().getSelectedRecords();
<?php endif; ?>
if(selections.length == 0){
  Ext.ux.MessageBox.error(
    '".__('Error!', array(), '<?php echo $this->getI18nCatalogue() ?>')."', 
    '".__('You must select at least one item.', array(), '<?php echo $this->getI18nCatalogue() ?>')."'
  );
} else {
  Ext.app.sf.ListPanel.body.mask('".__('Executing Batch Action ... ', array(), '<?php echo $this->getI18nCatalogue() ?>')."');
  var action = Ext.ComponentMgr.get('" . $batchId . "').getValue();
  var params = {  
    ".$csrf."batch_action: action
  };
  
  for (var i = 0; i < selections.length; i++) {
    params['ids['+i+']'] = selections[i].get('<?php echo sfInflector::underscore($this->getPrimaryKeys(true)) ?>');
  }
  
  Ext.Ajax.request({
    url:'".url_for('<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'batch')).".json',
    method : 'POST',
    params : params,
    timeout: 60000,
    success : function(response) {
      var json_response = Ext.util.JSON.decode(response.responseText);       
        
      if(json_response.success) {   
        Ext.ux.MessageBox.info(
          '".__('Successful!', array(), '<?php echo $this->getI18nCatalogue() ?>')."', 
          json_response.message,
          Ext.app.sf.ListPanel.getStore().reload(),
          this
        );
      } else {
        Ext.ux.MessageBox.error(
          '".__('Error!', array(), '<?php echo $this->getI18nCatalogue() ?>')."', 
          json_response.message
        );
      }       
      Ext.app.sf.ListPanel.body.unmask();        
    },
    failure: function(response) {
      Ext.ux.MessageBox.error(
        '".__('Error!', array(), '<?php echo $this->getI18nCatalogue() ?>')."', 
        '".__('The web server returned an unexpected response.', array(), '<?php echo $this->getI18nCatalogue() ?>')."'
      );
      Ext.app.sf.ListPanel.body.unmask();
    },
    scope: this
  });
}
";
$topToolbar->methods['batchHandler'] = $sfExtjs3Plugin->asMethod($configArr);
?]