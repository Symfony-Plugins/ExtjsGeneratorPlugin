[?php // @object $sfExtjs3Plugin and @object $formpanel provided
  $formpanel->attributes["_cancel"] = $sfExtjs3Plugin->asMethod("
  this.fireEvent('close', this);
  ");
?]