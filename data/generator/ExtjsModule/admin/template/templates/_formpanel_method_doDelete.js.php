[?php // @object $sfExtjs3Plugin string $className and @object $formpanel provided
$form = new BaseForm();
$csrf = ''; 
if ($form->isCSRFProtected())
{
  $csrf = "{$form->getCSRFFieldName()}: '{$form->getCSRFToken()}',\n          ";  
}
// doDelete
$formpanel->methods['doDelete'] = $sfExtjs3Plugin->asMethod("
  Ext.Msg.confirm('Confirm','Are you surse you want to delete this?',function(btn,text)
  {
    if(btn == 'yes')
    {
      Ext.Ajax.request({
        url: '".url_for('<?php echo $this->getUrlForAction('list') ?>')."/'+this.key,
        method: 'POST',
        params: {
          ".$csrf."sf_format: 'json', 
          sf_method: 'delete' 
        },
        success : function(response) {
          var json_response = Ext.util.JSON.decode(response.responseText);       
            
          if(json_response.success) { 
            this.fireEvent('deleted', this);  
            Ext.ux.MessageBox.info(
              '".__('Successful!', array(), '<?php echo $this->getI18nCatalogue() ?>')."', 
              json_response.message,
              this.fireEvent('close', this),
              this
            );            
          } else {
            Ext.ux.MessageBox.error(
              '".__('Error!', array(), '<?php echo $this->getI18nCatalogue() ?>')."', 
              json_response.message
            );
          }         
        },
        failure: function(response) {
          Ext.ux.MessageBox.error(
            '".__('Error!', array(), '<?php echo $this->getI18nCatalogue() ?>')."', 
            '".__('The web server returned an unexpected response.', array(), '<?php echo $this->getI18nCatalogue() ?>')."'
          );
        },
        scope: this
      });
    }
  }, this);
");
?]