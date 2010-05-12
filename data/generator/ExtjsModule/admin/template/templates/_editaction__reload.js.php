[?php // @object $sfExtjs3Plugin and @object $formpanel provided
  $formpanel->attributes["_reload"] = $sfExtjs3Plugin->asMethod("
  if (!this.getForm().isDirty()) {
    this.doLoad();
  } else {
    Ext.Msg.show({
      title: '".__('Discard changes?', array(), '<?php echo $this->getI18nCatalogue() ?>')."', 
      msg: '".__('If you reload, your changes will be lost!<br>Are you sure you want to reload?', array(), '<?php echo $this->getI18nCatalogue() ?>')."',
      buttons: Ext.Msg.YESNO,
      fn: function(btn){
        if (btn == 'yes') this.doLoad();
      },
      scope: this,
      icon: Ext.MessageBox.WARNING
    });
  }
  ");
?]