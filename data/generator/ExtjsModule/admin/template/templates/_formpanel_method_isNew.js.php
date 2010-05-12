[?php
// isNew
$formpanel->methods['isNew'] = $sfExtjs3Plugin->asMethod("
  return ((typeof this.key=='undefined') || (this.key==null));
");
?]