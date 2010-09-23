[?php // @object $sfExtjs3Plugin string $className and @object $formpanel provided
  $formpanel->methods["_cancel"] = $sfExtjs3Plugin->asMethod("
  this.fireEvent('close', this);
  ");
?]