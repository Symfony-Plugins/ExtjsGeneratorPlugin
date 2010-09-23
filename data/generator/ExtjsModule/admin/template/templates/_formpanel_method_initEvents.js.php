[?php // @object $sfExtjs3Plugin string $className and @object $formpanel provided
$formpanel->methods['initEvents'] = $sfExtjs3Plugin->asMethod("
  Ext.app.sf.$className.superclass.initEvents.apply(this);
  this.mon(this, 'close', this.close, this);
");
?]