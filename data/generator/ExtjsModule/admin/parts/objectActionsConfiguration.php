  public function getObjectActionsPartials()
  {
    return <?php echo $this->asPhp(isset($this->config['object_actions']['partials']) ? $this->config['object_actions']['partials'] : array()) ?>;
<?php unset($this->config['object_actions']['partials']) ?>
  }