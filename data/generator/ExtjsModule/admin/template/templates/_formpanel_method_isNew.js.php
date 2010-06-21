[?php // @object $sfExtjs3Plugin string $className and @object $formpanel provided
// isNew
$formpanel->methods['isNew'] = $sfExtjs3Plugin->asMethod("
  return ((typeof this.key=='undefined') || (this.key==null));
");
?]