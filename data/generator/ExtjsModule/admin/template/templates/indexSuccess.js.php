[?php use_helper('I18N', 'Date') ?]
[?php $sfExtjs3Plugin = new sfExtjs3Plugin() ?]
// initialise CodeLoader
Ext.ComponentMgr.loadType = function(type) {  
  Ext.Ajax.request({
    url : '[?php echo url_for('@homepage') ?]js/getXtype/' + type + '.js',
    disableCaching : true,
    method : 'GET',
    async : false,
    success : function(resp, opt) {
      eval.call(window, String(resp.responseText || "").trim());
    },
    failure : function(resp, opt) {   
    }
  });
};

<?php if(!$this->configuration->objectActionsIsDisabled() && count($this->configuration->getListObjectActions())): ?>
[?php include_partial('objectActions', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]
<?php endif; ?>

<?php if(!$this->configuration->topToolbarIsDisabled() && (count($this->configuration->getListActions()) || count($this->configuration->getListBatchActions()))): ?>
[?php include_partial('topToolbar', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]
<?php endif; ?>

<?php if (!$this->configuration->bottomToolbarIsDisabled()): ?>
[?php include_partial('bottomToolbar', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]
<?php endif; ?>

[?php include_partial('datastore', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]

<?php if($this->configuration->getListLayout() == 'gridpanel'): ?>
[?php include_partial('columnRenderers', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]
[?php include_partial('columnModel', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]
<?php endif; ?>

[?php include_partial('<?php echo $this->configuration->getListLayout() ?>', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]

<?php if (!$this->configuration->tabpanelIsDisabled()): ?>
[?php include_partial('tabpanel', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]
<?php endif; ?>

<?php if($this->configuration->hasFilterForm() && !$this->configuration->filterpanelIsDisabled()): ?>
[?php include_partial('filterpanel', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'filters' => $filters, 'configuration' => $configuration)) ?]
<?php endif; ?>

<?php if($this->configuration->hasForm() && !$this->configuration->formpanelIsDisabled()): ?>
[?php //include_partial('formpanel', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'configuration' => $configuration)) ?]
<?php endif; ?>

Ext.onReady(function(){
[?php include_partial('layout', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]
});