[?php // @object $sfExtjs3Plugin string $className and @object $listpanel provided
// constructor
$configArr = array(
  'source' => "
  Ext.app.sf.$className.superclass.initEvents.apply(this);

<?php $listConfig = $this->configuration->getListpanelConfig(); ?>
<?php if(!$this->configuration->hasFilterForm() || (isset($listConfig['autoLoadStore']) && $listConfig['autoLoadStore'])):?>
  this.on({
    afterrender: {
      fn: function(){this.getStore().load()},
      scope: this,
      single: true
    }
  });
<?php endif; ?>

  this.body.on({
    scope:    this,
    click:    this.onEditLinkClick,
    delegate: 'a.listview_edit_link',
    stopEvent: true
  });

");

$listpanel->methods['initEvents'] = $sfExtjs3Plugin->asMethod($configArr);
?]