[?php use_helper('I18N', 'Date', 'JavascriptBase') ?]
[?php use_javascript(url_for('@<?php echo $this->params['route_prefix'] ?>').'/index.js', 'last', array('raw_name' => true)); ?]
[?php include_partial('<?php echo $this->getModuleName() ?>/assets') ?]
