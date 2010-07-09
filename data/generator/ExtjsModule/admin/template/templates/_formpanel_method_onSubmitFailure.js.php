[?php // @object $sfExtjs3Plugin string $className and @object $formpanel provided
// onSubmitFailure
$formpanel->methods['onSubmitFailure'] = $sfExtjs3Plugin->asMethod(array(
  'parameters'  => 'form, action',
  'source'      => "
  this.form.findField('primary_key').setDisabled(false);
  this.fireEvent('save_failed', this);

  var msg = '".__('The web server returned an unexpected response.', array(), '<?php echo $this->getI18nCatalogue() ?>')."';
  if (action.result)
  {
    msg = action.result.message || action.response.responseText;
  }
  Ext.ux.MessageBox.error(
    '".__('Error!', array(), '<?php echo $this->getI18nCatalogue() ?>')."', 
    msg
  );
"
));
?]