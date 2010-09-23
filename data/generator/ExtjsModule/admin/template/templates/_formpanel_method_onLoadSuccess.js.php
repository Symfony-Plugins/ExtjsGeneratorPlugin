[?php // @object $sfExtjs3Plugin string $className and @object $formpanel provided
// onLoadSuccess
$formpanel->methods['onLoadSuccess'] = $sfExtjs3Plugin->asMethod(array(
  'parameters'  => 'form, action',
  'source'      => "
  this.setTitle(action.reader.jsonData.title);
  this.fireEvent('load_success', this);
"
));
?]