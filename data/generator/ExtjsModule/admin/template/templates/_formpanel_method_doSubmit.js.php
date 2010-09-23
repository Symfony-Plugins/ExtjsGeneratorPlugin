[?php // @object $sfExtjs3Plugin @object $form string $className and @object $formpanel provided
// doSubmit
$formpanel->methods['doSubmit'] = $sfExtjs3Plugin->asMethod(array(
  'parameters'  => 'submitnew',
  'source'      => "
  if (!this.getForm().isValid())
  {
    Ext.Msg.show(".$sfExtjs3Plugin->asAnonymousClass(array(
      'title'   =>  __('Error!', array(), '<?php echo $this->getI18nCatalogue() ?>'),
      'msg'     =>  __('Not all fields contain valid data,<br>Please check all fields', array(), '<?php echo $this->getI18nCatalogue() ?>'),
      'modal'   =>  true,
      'icon'    =>  $sfExtjs3Plugin->asVar('Ext.Msg.ERROR'),
      'buttons' =>  $sfExtjs3Plugin->asVar('Ext.Msg.OK')
    )).");
  }
  else
  {      
    var params = {
      sf_format: 'json'    
    };

    if(this.findByType('twinfileuploadfield').length >= 1) this.getForm().fileUpload = true;
        
    var url_key = '';
    
    // add key to url if key is set to an existing primary-key and submitnew
    // isn't set
    if(!this.isNew()&& typeof submitnew == 'undefined'){
      url_key ='/'+this.key;      
      params['action'] = 'update';
      params['sf_method'] = 'put';
    } else {
      this.form.findField('primary_key').setDisabled(true);
      params['action'] = 'create';
    }

    this.getForm().submit(".$sfExtjs3Plugin->asAnonymousClass(array(
      'url'     => $sfExtjs3Plugin->asVar("'".url_for_form($form, '@<?php echo $this->params['route_prefix'] ?>').'\'+url_key'),
      'scope'   => $sfExtjs3Plugin->asVar('this'),
      'success' => $sfExtjs3Plugin->asVar('this.onSubmitSuccess'),
      'failure' => $sfExtjs3Plugin->asVar('this.onSubmitFailure'),
      'params'  => $sfExtjs3Plugin->asVar('params'),
      'waitMsg' => __('Saving...', array(), '<?php echo $this->getI18nCatalogue() ?>'),
    )).");
  }
"));
?]