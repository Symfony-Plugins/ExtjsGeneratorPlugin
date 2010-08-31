[?php // @object $sfExtjs3Plugin string $className and @object $listpanel provided
// setFilter
$listpanel->methods['setFilter'] = $sfExtjs3Plugin->asMethod(array(
  'parameters' => 'params',
  'source' => "
    params['action'] = 'filter';
    this.getStore().proxy.setMethod('POST', false);
    this.getStore().proxy.setUrl('".url_for('<?php echo $this->getUrlForAction('collection') ?>', array('action' => 'filter')).".json', false);
    this.getStore().load({params:params}); 
    this.getStore().lastOptions = {};
"));
?]
