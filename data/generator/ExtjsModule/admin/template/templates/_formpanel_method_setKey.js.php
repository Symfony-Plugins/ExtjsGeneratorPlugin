[?php // @object $sfExtjs3Plugin string $className and @object $formpanel provided
// setKey
$formpanel->methods['setKey'] = $sfExtjs3Plugin->asMethod(array(
  'parameters'  => 'key',
  'source'      => "
  var old_key = this.key;
  if (old_key != key)
  {
    this.key = key;
    this.fireEvent('keychange', this.key, old_key, this);
  }
"
));
?]