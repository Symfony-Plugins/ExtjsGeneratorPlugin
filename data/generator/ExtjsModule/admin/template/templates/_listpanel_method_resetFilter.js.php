[?php // @object $sfExtjs3Plugin string $className and @object $listpanel provided
// resetFilter
$listpanel->methods['resetFilter'] = $sfExtjs3Plugin->asMethod("
  this.getStore().proxy.setMethod('POST');
  this.getStore().proxy.setUrl('".url_for('<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'filter')).".json');
  this.getStore().load({params:{start:0, _reset: true}});
  this.getStore().lastOptions = {};
");
?]