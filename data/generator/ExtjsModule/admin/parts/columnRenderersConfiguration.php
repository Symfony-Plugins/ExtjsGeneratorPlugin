  public function getColumnRenderersPartials()
  {
    return <?php echo $this->asPhp(isset($this->config['column_renderers']['partials']) ? $this->config['column_renderers']['partials'] : array()) ?>;
<?php unset($this->config['column_renderers']['partials']) ?>    
  }