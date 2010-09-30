<?php
  //$moduleName = ucfirst(sfInflector::camelize($this->getModuleName()));
  //$listActions = $this->getParameterValue('list.actions');
  //$bbar = $this->getParameterValue('list.params.bbar');
  /*
   * list.actions defines the actions in the toptoolbar of the grid
   *
   * list.actions takes an array of partials that define items that can be added to a toolbar
   *
   * if list.actions is not defined in generator.yml two default actions are added
   * if list.actions is set to an empty array ( [] ) in generator.yml then there will be an empty bar
   * if list.actions is set to false then no toptoolbar will be namespace will be generated
   *
   */
?>
[?php // @object $sfExtjs3Plugin string $className and @object $listpanel provided
// constructor
$configArr = array(
  'source' => "
    // initialise items which use this grid's-store
<?php if(count($this->configuration->getListActions()) || count($this->configuration->getListBatchActions())): ?>
    this.tbar = Ext.ComponentMgr.create({xtype:'<?php echo $this->getModuleName().'toptoolbar' ?>',store: this.ds});
<?php endif; ?>
<?php if (!$this->configuration->bottomToolbarIsDisabled()): ?>
    this.bbar = Ext.ComponentMgr.create({xtype:'<?php echo $this->getModuleName().'bottomtoolbar' ?>',store: this.ds});
<?php endif; ?>

    Ext.app.sf.$className.superclass.initComponent.apply(this, arguments);

    //TODO these events should be implemented
    this.addEvents(
      /**
       * @event saved
       * Fires when an item is saved successfully
       * @param {Ext.app.sf.$className} this List-GridPanel
       */
      'saved',
      /**
       * @event save_failed
       * Fires when an item is not saved successfully
       * @param {Ext.app.sf.$className} this List-GridPanel
       */
      'save_failed',
      /**
       * @event deleted
       * Fires when an item is deleted successfully
       * @param {Ext.app.sf.$className} this List-GridPanel
       */
      'deleted'
    );

  "
);

$listpanel->methods['initComponent'] = $sfExtjs3Plugin->asMethod($configArr);
?]