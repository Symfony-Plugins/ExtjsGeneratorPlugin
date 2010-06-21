[?php // @object $sfExtjs3Plugin string $className and @object $filterpanel provided
// constructor
$configArr = array(
  'source' => "
Ext.app.sf.$className.superclass.initEvents.apply(this);

this.addEvents(
  /**
   * @event filter_set
   * Fires when the Filter button on the filter-panel has been pressed, sending as arguments the params of the filterform
   * @param {params} the fields from the filterform
   * @param {Ext.app.sf.ListLbadminFilterPanel} this Filter-FormPanel
   */
  'filter_set',
  /**
   * @event filter_reset
   * Fires when the reset button on the filter-panel has been pressed
   * @param {Ext.app.sf.ListLbadminFilterPanel} this Filter-FormPanel
   */
  'filter_reset'
);

this.on({
  'afterlayout' : {
    fn:     function(){
      this.buttons[0].handler.defer(500, this)
    },
    scope:  this,
    single: true
  }
});
  "
);

$filterpanel->methods['initEvents'] = $sfExtjs3Plugin->asMethod($configArr);
?]