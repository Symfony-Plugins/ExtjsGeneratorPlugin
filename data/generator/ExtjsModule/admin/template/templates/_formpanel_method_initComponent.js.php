[?php
// constructor
$configArr = array(
  'source' => "
Ext.app.sf.$className.superclass.initComponent.apply(this, arguments);

this.addEvents(
  /**
   * @event load_failure
   * Fires when the item is not loaded successfully
   * @param {Ext.app.sf.$className} this Edit-FormPanel
   */
  'load_failure',
  /**
   * @event load_success
   * Fires when the item is loaded successfully
   * @param {Ext.app.sf.$className} this Edit-FormPanel
   */
  'load_success',
  /**
   * @event saved
   * Fires when the item is saved successfully
   * @param {Ext.app.sf.$className} this Edit-FormPanel
   */
  'saved',
  /**
   * @event save_failed
   * Fires when the item is not saved successfully
   * @param {Ext.app.sf.$className} this Edit-FormPanel
   */
  'save_failed',
  /**
   * @event deleted
   * Fires when the item is deleted successfully
   * @param {Ext.app.sf.$className} this Edit-FormPanel
   */
  'deleted',
  /**
   * @event close
   * Fires when the panel closes
   * @param {Ext.app.sf.$className} this Edit-FormPanel
   */
  'close_request',
  /**
   * @event keychange
   * Fires when the items (primary) key has been set (after saving a new item)
   * @param number key
   * @param number oldkey
   * @param {Ext.app.sf.$className} this Edit-FormPanel
   */
   'keychange'
);
// show/hide appropriate buttons
this.doLoad();
this.updateButtonsVisibility();
"
);

$formpanel->attributes['initComponent'] = $sfExtjs3Plugin->asMethod($configArr);
?]