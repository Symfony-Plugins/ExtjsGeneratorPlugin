[?php use_helper('I18N', 'Date') ?]
[?php $sfExtjs3Plugin = new sfExtjs3Plugin() ?]
[?php include_partial('objectActions', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]
<?php if(count($this->configuration->getListActions()) || count($this->configuration->getListBatchActions())): ?>
[?php include_partial('topToolbar', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]
<?php endif; ?>
[?php include_partial('bottomToolbar', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]
[?php include_partial('datastore', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]
[?php include_partial('columnRenderers', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]
[?php include_partial('columnModel', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]
[?php include_partial('gridpanel', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]
[?php include_partial('tabpanel', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]
<?php if($this->configuration->hasFilterForm()):?>
[?php include_partial('filterpanel', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'form' => $filters, 'configuration' => $configuration)) ?]
<?php endif; ?>
[?php //include_partial('formpanel', array('sfExtjs3Plugin' => $sfExtjs3Plugin, 'configuration' => $configuration)) ?]

// initialise CodeLoader
Ext.app.CodeLoader = new Ext.ux.ModuleManager({modulePath: '[?php echo sfContext::getInstance()->getRequest()->getScriptName() ?]' });

Ext.onReady(function(){
[?php include_partial('layout', array('sfExtjs3Plugin' => $sfExtjs3Plugin)) ?]
});