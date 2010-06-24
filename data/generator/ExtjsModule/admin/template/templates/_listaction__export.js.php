[?php // @object $sfExtjs3Plugin string $className and @object $topToolbar provided
  $topToolbar->methods["_export"] = $sfExtjs3Plugin->asMethod("
    window.location = '<?php echo $this->getUrlForAction('list') ?>.csv';
  ");
?]