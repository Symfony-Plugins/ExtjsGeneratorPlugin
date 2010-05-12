[?php
// onLoadFailure
$formpanel->methods['onLoadFailure'] = $sfExtjs3Plugin->asMethod(array(
  'parameters'  => 'form, action',
  'source'      => "  
  Ext.ux.MessageBox.error(
    '".__('Error!', array(), '<?php echo $this->getI18nCatalogue() ?>')."', 
    '".__('Unable to load the record.', array(), '<?php echo $this->getI18nCatalogue() ?>')."'
  );
  this.fireEvent('load_failure', this);
"
));
?]