[?php // @object $sfExtjs3Plugin string $className and @object $formpanel provided
// onSubmitSuccess
$formpanel->methods['onSubmitSuccess'] = $sfExtjs3Plugin->asMethod(array(
  'parameters'  => 'form, action',
  'source'      => "
  this.setKey(action.result.key);
  this.setTitle(action.result.title);
  
  // clean dirty form
  if (this.trackResetOnLoad){
    form.items.each(function (f){
      f.originalValue = f.getValue();
    });
  }

  this.updateButtonsVisibility();
  this.form.findField('primary_key').setDisabled(false);

  this.fireEvent('saved', this);
    
  Ext.ux.MessageBox.info('".__('Successful!', array(), '<?php echo $this->getI18nCatalogue() ?>')."', action.result.message);
"
));
?]