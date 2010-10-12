[?php // @object $sfExtjs3Plugin string $className and @object $topToolbar provided
  $topToolbar->methods["_export"] = $sfExtjs3Plugin->asMethod("
    window.location = '" . url_for('@<?php echo $this->params['route_prefix'] ?>') . "/index.csv';
  ");
?]