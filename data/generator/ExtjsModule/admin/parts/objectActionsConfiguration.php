  public function getObjectActionsConfig()
  {
    return array_merge(array(
    ), <?php echo $this->asPhp(isset($this->config['object_actions']['config']) ? $this->config['object_actions']['config'] : array()) ?>);
<?php unset($this->config['object_actions']['config']) ?>
  }

  public function getObjectActionsPartials()
  {
    return <?php echo $this->asPhp(isset($this->config['object_actions']['partials']) ? $this->config['object_actions']['partials'] : array()) ?>;
<?php unset($this->config['object_actions']['partials']) ?>
  }

  public function objectActionsIsDisabled()
  {
    return <?php echo isset($this->config['object_actions']['disabled']) && $this->config['object_actions']['disabled'] === true ? 'true' : 'false' ?>;
<?php unset($this->config['object_actions']['disabled']) ?>
  }

  public function getObjectActionsExtends()
  {
    return <?php echo isset($this->config['object_actions']['extends']) ? "'{$this->config['object_actions']['extends']}'" : 'null' ?>;
<?php unset($this->config['object_actions']['extends']) ?>
  }
