[?php
// constructor
$configArr = array(
  'source' => "
  Ext.app.sf.$className.superclass.initEvents.apply(this);

<?php if(!$this->configuration->hasFilterForm()):?>
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
    delegate: 'a.grid_edit_link',
    stopEvent: true
  });

");

$gridpanel->attributes['initEvents'] = $sfExtjs3Plugin->asMethod($configArr);
?]