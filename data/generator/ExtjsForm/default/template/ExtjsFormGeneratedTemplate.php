[?php

/**
 * <?php echo $this->table->getClassname() ?> form base class.
 *
 * @method <?php echo $this->table->getClassname() ?> getObject() Returns the current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseExtjs<?php echo $this->table->getClassname() ?>Form extends BaseExtjsFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
<?php foreach ($this->table->getColumns() as $column): ?>
      '<?php echo $this->translateColumnName($column) ?>'<?php echo str_repeat(' ', $this->getColumnNameMaxLength() - strlen($column->getName())) ?> => new <?php echo $this->getWidgetClassForColumn($column) ?>(<?php echo $this->getWidgetOptionsForColumn($column) ?>),
<?php endforeach; ?>
<?php foreach ($this->getManyToManyTables() as $tables): ?>
      '<?php echo $this->underscore($tables['middleTable']->getClassname()) ?>_list'<?php echo str_repeat(' ', $this->getColumnNameMaxLength() - strlen($this->underscore($tables['middleTable']->getClassname()).'_list')) ?> => new ExtjsWidgetFormPropelChoice(array('multiple' => true, 'model' => '<?php echo $tables['relatedTable']->getClassname() ?>')),
<?php endforeach; ?>
    ));

    $this->setValidators(array(
<?php foreach ($this->table->getColumns() as $column): ?>
      '<?php echo $this->translateColumnName($column) ?>'<?php echo str_repeat(' ', $this->getColumnNameMaxLength() - strlen($column->getName())) ?> => new <?php echo $this->getValidatorClassForColumn($column) ?>(<?php echo $this->getValidatorOptionsForColumn($column) ?>),
<?php endforeach; ?>
<?php foreach ($this->getManyToManyTables() as $tables): ?>
      '<?php echo $this->underscore($tables['middleTable']->getClassname()) ?>_list'<?php echo str_repeat(' ', $this->getColumnNameMaxLength() - strlen($this->underscore($tables['middleTable']->getClassname()).'_list')) ?> => new ExtjsValidatorPropelChoice(array('multiple' => true, 'model' => '<?php echo $tables['relatedTable']->getClassname() ?>', 'required' => false)),
<?php endforeach; ?>
    ));

<?php if ($uniqueColumns = $this->getUniqueColumnNames()): ?>
    $this->validatorSchema->setPostValidator(
<?php if (count($uniqueColumns) > 1): ?>
      new sfValidatorAnd(array(
<?php foreach ($uniqueColumns as $uniqueColumn): ?>
        new sfValidatorPropelUnique(array('model' => '<?php echo $this->table->getClassname() ?>', 'column' => array('<?php echo implode("', '", $uniqueColumn) ?>'))),
<?php endforeach; ?>
      ))
<?php else: ?>
      new sfValidatorPropelUnique(array('model' => '<?php echo $this->table->getClassname() ?>', 'column' => array('<?php echo implode("', '", $uniqueColumns[0]) ?>')))
<?php endif; ?>
    );

<?php endif; ?>
    $this->widgetSchema->setNameFormat('<?php echo $this->underscore($this->table->getClassname()) ?>[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
<?php foreach ($this->getOneToOneTables() as $oneToOne):?>

    $this->mergeOneToOneRelation('<?php echo $oneToOne->getName() ?>');
<?php endforeach; ?>

    parent::setup();
  }

  public function getModelName()
  {
    return '<?php echo $this->table->getClassname() ?>';
  }

<?php if ($this->isI18n()): ?>
  public function getI18nModelName()
  {
    return '<?php echo $this->getI18nModel() ?>';
  }

  public function getI18nFormClass()
  {
    return '<?php echo $this->getI18nModel() ?>Form';
  }
<?php endif; ?>

<?php if ($this->getManyToManyTables() || $this->getOneToOneTables()): ?>
  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();
<?php foreach ($this->getManyToManyTables() as $tables): ?>
    if (isset($this->widgetSchema['<?php echo $this->underscore($tables['middleTable']->getClassname()) ?>_list']))
    {
      $values = array();
      foreach ($this->object->get<?php echo $tables['middleTable']->getPhpName() ?>s() as $obj)
      {
        $values[] = $obj->get<?php echo $tables['relatedColumn']->getPhpName() ?>();
      }

      $this->setDefault('<?php echo $this->underscore($tables['middleTable']->getClassname()) ?>_list', $values);
    }

<?php endforeach; ?>
<?php foreach ($this->getOneToOneTables() as $oneToOne):?>
    if (!is_null($<?php echo $oneToOne->getName() ?> = $this->getRelatedObject('get<?php echo $oneToOne->getName() ?>')))
    {
      $values = $<?php echo $oneToOne->getName() ?>->toArray(BasePeer::TYPE_FIELDNAME);
<?php foreach($oneToOne->getLocalTable()->getPrimaryKeys() as $primaryKey): ?>
<?php $pk = call_user_func(array($oneToOne->getLocalTable()->getClassname().'Peer', 'translateFieldname'), $primaryKey->getPhpName(), BasePeer::TYPE_PHPNAME, BasePeer::TYPE_FIELDNAME) ?>
      unset($values['<?php echo $pk ?>']);
<?php endforeach; ?>

      // update defaults for the main object
      if ($this->isNew)
      {
        $this->setDefaults(array_merge($values, $this->getDefaults()));
      }
      else
      {
        $this->setDefaults(array_merge($this->getDefaults(), $values));
      }
    }

<?php endforeach; ?>
  }

  protected function doSave($con = null)
  {
    parent::doSave($con);
<?php foreach ($this->getManyToManyTables() as $tables): ?>
    $this->save<?php echo $tables['middleTable']->getPhpName() ?>List($con);
<?php endforeach; ?>
<?php foreach ($this->getOneToOneTables() as $oneToOne):?>
    $this->save<?php echo $oneToOne->getName() ?>();
<?php endforeach; ?>
  }

<?php foreach ($this->getOneToOneTables() as $oneToOne):?>
  public function save<?php echo $oneToOne->getName() ?>()
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (is_null($<?php echo $oneToOne->getName() ?> = $this->getRelatedObject('get<?php echo $oneToOne->getName() ?>')))
    {
      $<?php echo $oneToOne->getName() ?> = new <?php echo $oneToOne->getRightTable()->getPhpName() ?>();
    }
    $values = $this->getValues();
<?php foreach($oneToOne->getLocalTable()->getPrimaryKeys() as $primaryKey): ?>      
<?php $pk = call_user_func(array($oneToOne->getLocalTable()->getClassname().'Peer', 'translateFieldname'), $primaryKey->getPhpName(), BasePeer::TYPE_PHPNAME, BasePeer::TYPE_FIELDNAME) ?>
    unset($values['<?php echo $pk ?>']);
<?php endforeach; ?>
    $<?php echo $oneToOne->getName() ?>->fromArray($values, BasePeer::TYPE_FIELDNAME);
    if($<?php echo $oneToOne->getName() ?>->isModified() || (isset($this->active_one_to_one_relations) && in_array('<?php echo $oneToOne->getName() ?>', $this->active_one_to_one_relations)))
    {
      $<?php echo $oneToOne->getName() ?>->set<?php echo $primaryKey->getPhpName() ?>($this->object->getPrimaryKey());
      $<?php echo $oneToOne->getName() ?>->save();
    }
  }

<?php endforeach; ?>
<?php foreach ($this->getManyToManyTables() as $tables): ?>
  public function save<?php echo $tables['middleTable']->getPhpName() ?>List($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['<?php echo $this->underscore($tables['middleTable']->getClassname()) ?>_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $c = new Criteria();
    $c->add(<?php echo constant($tables['middleTable']->getClassname().'::PEER') ?>::<?php echo strtoupper($tables['column']->getName()) ?>, $this->object->getPrimaryKey());
    <?php echo constant($tables['middleTable']->getClassname().'::PEER') ?>::doDelete($c, $con);

    $values = $this->getValue('<?php echo $this->underscore($tables['middleTable']->getClassname()) ?>_list');
    if (is_array($values))
    {
      foreach ($values as $value)
      {
        $obj = new <?php echo $tables['middleTable']->getClassname() ?>();
        $obj->set<?php echo $tables['column']->getPhpName() ?>($this->object->getPrimaryKey());
        $obj->set<?php echo $tables['relatedColumn']->getPhpName() ?>($value);
        $obj->save();
      }
    }
  }

<?php endforeach; ?>
<?php endif; ?>
}
