[?php // @object $sfExtjs3Plugin string $className and @object $formpanel provided
// updateButtonsVisibility
$formpanel->methods['updateButtonsVisibility'] = $sfExtjs3Plugin->asMethod("
  // hide delete button when new item
  if (this.topToolbar && typeof this.topToolbar.items != 'undefined')
  {
    var len;
    var buttons = this.topToolbar.items.items;
    for(var i = 0, len = buttons.length; i < len; i++)
    {
      var button = buttons[i];
      if(button.hideWhenNew)
      {
        if(typeof button.rendered == 'undefined')
        {
          button.hidden = this.isNew();
        }
        else
        {
          button.setVisible(this.isNew()?false:true);
        }
      }
    }
  }
");
?]