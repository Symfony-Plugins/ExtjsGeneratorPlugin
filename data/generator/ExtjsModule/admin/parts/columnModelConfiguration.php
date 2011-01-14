  public function getColumnModelConfig()
  {
    $columns = array();
    if($this->getListBatchActions())
    {
      $columns[] = 'this.sm';
      $sm = 'new Ext.grid.CheckboxSelectionModel()';
    }
    else
    {
      $sm = 'new Ext.grid.RowSelectionModel({singleSelect:true})';
    }
    return array_merge(array(
      'sm' => $sm,
      'defaultSortable' => true,
      'columns' => $columns
    ), <?php echo $this->asPhp(isset($this->config['column_model']['config']) ? $this->config['column_model']['config'] : array()) ?>);
<?php unset($this->config['column_model']['config']) ?>
  }

  public function getColumnModelPartials()
  {
    return <?php echo $this->asPhp(isset($this->config['column_model']['partials']) ? $this->config['column_model']['partials'] : array()) ?>;
<?php unset($this->config['column_model']['partials']) ?>
  }

  public function getColumnModelExtends()
  {
    return <?php echo isset($this->config['column_model']['extends']) ? "'{$this->config['column_model']['extends']}'" : 'null' ?>;
<?php unset($this->config['column_model']['extends']) ?>
  }
