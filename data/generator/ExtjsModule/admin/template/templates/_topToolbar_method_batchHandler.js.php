[?php // @string $csrf @object $sfExtjs3Plugin and @object $topToolbar provided
$configArr['source'] = "
<?php if($this->configuration->getListLayout() == 'gridpanel'): ?>  
var selected = this.ownerCt.getSelectionModel().getSelections();
<?php else: ?>
var selections = this.ownerCt.getView().getSelectedRecords();
<?php endif; ?>
var action = this.items.items[0].getValue();
if(selections.length == 0){
  Ext.ux.MessageBox.error(
    '".__('Error!', array(), '<?php echo $this->getI18nCatalogue() ?>')."', 
    '".__('You must select at least one item.', array(), '<?php echo $this->getI18nCatalogue() ?>')."'
  );
} else if(action.length == 0) {
  Ext.ux.MessageBox.error(
    '".__('Error!', array(), 'messages')."', 
    '".__('You must select a batch action to perform.', array(), 'messages')."'
  );
} else {
  this.ownerCt.body.mask('".__('Executing Batch Action ... ', array(), '<?php echo $this->getI18nCatalogue() ?>')."');

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
          this.ownerCt.getStore().reload(),
          this
        );
      } else {
        Ext.ux.MessageBox.error(
          '".__('Error!', array(), '<?php echo $this->getI18nCatalogue() ?>')."', 
          json_response.message
        );
      }       
      this.ownerCt.body.unmask();        
    },
    failure: function(response) {
      Ext.ux.MessageBox.error(
        '".__('Error!', array(), '<?php echo $this->getI18nCatalogue() ?>')."', 
        '".__('The web server returned an unexpected response.', array(), '<?php echo $this->getI18nCatalogue() ?>')."'
      );
      this.ownerCt.body.unmask();
    },
    scope: this
  });
}
";
$topToolbar->methods['batchHandler'] = $sfExtjs3Plugin->asMethod($configArr);
?]