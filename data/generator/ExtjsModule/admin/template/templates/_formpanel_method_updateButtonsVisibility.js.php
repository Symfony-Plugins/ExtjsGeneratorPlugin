[?php // @object $sfExtjs3Plugin string $className and @object $formpanel provided
// updateButtonsVisibility
$formpanel->methods['updateButtonsVisibility'] = $sfExtjs3Plugin->asMethod("
  // hide delete button when new item
  if (this.topToolbar)
  {
    var len;
    var topToolbar = (typeof this.topToolbar.items != 'undefined')?this.topToolbar.items.items:this.topToolbar;
    for(var i = 0, len = topToolbar.length; i < len; i++)
    {
      var button = topToolbar[i];
      if((typeof button.hide_when_new!='undefined') && button.hide_when_new)
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