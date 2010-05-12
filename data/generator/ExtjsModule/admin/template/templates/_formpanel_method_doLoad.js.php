[?php
// doLoad
$formpanel->methods['doLoad'] = $sfExtjs3Plugin->asMethod("
  if (this.isNew())
  {
    // cancel loading if no key set or new item
    return;
  }

  var load_config = ".$sfExtjs3Plugin->asAnonymousClass(array(
    'url'     => url_for_form($form, '@<?php echo $this->params['route_prefix'] ?>').'/\'+this.key+\'/edit.json',
    'waitMsg' => __('Loading data', array(), '<?php echo $this->getI18nCatalogue() ?>'),
    'success' => $sfExtjs3Plugin->asVar('this.onLoadSuccess'),
    'failure' => $sfExtjs3Plugin->asVar('this.onLoadFailure'),
    'scope'   => $sfExtjs3Plugin->asVar('this'),
    'method'  => 'GET',
  )).";

  this.getForm().load(load_config);
");
?]