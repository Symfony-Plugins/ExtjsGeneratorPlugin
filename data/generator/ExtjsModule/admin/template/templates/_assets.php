<?php $css = sfConfig::get('extjs_gen_default_stylesheets', array()); ?>
<?php $js = sfConfig::get('extjs_gen_default_javascripts', array()); ?>
<?php if (isset($this->params['css']) && ($this->params['css'] !== false)): ?>
[?php use_stylesheet('<?php echo $this->params['css'] ?>', 'first') ?]
<?php endif; ?>

[?php use_javascript('<?php echo $this->getModuleName() ?>/index.js', 'last', array('raw_name' => true)); ?]
[?php
// TODO: Need to put in a mechanism to only include extensions we are currently using in the generator.yml
$sfExtjs3Plugin = new sfExtjs3Plugin(
  array(
    'theme'   => sfConfig::get('extjs_gen_plugin_theme'),
    'adapter' => sfConfig::get('extjs_gen_plugin_adapter')
  ),
  array(
    'css' => <?php var_export($css) ?>,
    'js'  => <?php var_export($js) ?>
  )
);
$sfExtjs3Plugin->load();
?]
