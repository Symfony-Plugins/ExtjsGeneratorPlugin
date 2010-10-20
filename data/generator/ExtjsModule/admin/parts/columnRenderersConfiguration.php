  public function getColumnRenderersPartials()
  {
    return <?php echo $this->asPhp(isset($this->config['column_renderers']['partials']) ? $this->config['column_renderers']['partials'] : array()) ?>;
<?php unset($this->config['column_renderers']['partials']) ?>
  }

  public function getColumnRenderersExtends()
  {
    return <?php echo isset($this->config['column_renderers']['extends']) ? "'{$this->config['column_renderers']['extends']}'" : 'null' ?>;
<?php unset($this->config['column_renderers']['extends']) ?>
  }
