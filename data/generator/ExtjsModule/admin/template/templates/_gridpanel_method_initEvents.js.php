[?php // @object $sfExtjs3Plugin string $className and @object $gridpanel provided
// constructor
$configArr = array(
  'source' => "
  Ext.app.sf.$className.superclass.initEvents.apply(this);

<?php $gridConfig = $this->configuration->getGridpanelConfig(); ?>
<?php if(!$this->configuration->hasFilterForm() || (isset($gridConfig['autoLoadStore']) && $gridConfig['autoLoadStore'])):?>
  this.on({
    render: {
      fn: function(){
        this.view.refresh(); 
        this.loadMask.show();
      }
    },
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

$gridpanel->methods['initEvents'] = $sfExtjs3Plugin->asMethod($configArr);
?]