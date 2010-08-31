[?php // @object $sfExtjs3Plugin and @object $objectActions provided
  $form = new BaseForm();
  $csrf = ''; 
  if ($form->isCSRFProtected())
  {
    $csrf = "{$form->getCSRFFieldName()}: '{$form->getCSRFToken()}',\n          ";  
  }
<?php if($this->configuration->getListLayout() == 'listpanel'): ?>  
  $configArr['parameters'] = 'view, record, action, node, index';
<?php else: ?>
  $configArr['parameters'] = 'grid, record, action, row, col';
<?php endif; ?>
  $configArr['source'] = "
  Ext.Msg.confirm('Confirm','Are you sure you want to delete this <?php echo $this->configuration->getObjectName() ?>?',function(btn,text){
    if(btn == 'yes')
    {
      Ext.Ajax.request({
        url: '".url_for('<?php echo $this->getUrlForAction('list') ?>')."/'+record.get('id'),
        method: 'POST',
        params: {
          ".$csrf."sf_format: 'json', 
          sf_method: 'delete' 
        },
        success:  function(response){
          var json_response = Ext.util.JSON.decode(response.responseText);
          if(json_response.success)
          {
            record.store.remove(record);
          }
          else
          {
            Ext.Msg.alert('Error while deleting', 'Error while deleting');
          }
        },
        failure: function(response){
          Ext.Msg.alert('Error while deleting', 'Error while deleting');
        }
      });
    }
  });
  ";
  $objectActions->methods['_delete'] = $sfExtjs3Plugin->asMethod($configArr);
?]