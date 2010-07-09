<?php
abstract class ExtjsFormFilterPropel extends sfFormFilterPropel
{

  /**
   * Returns the fields and their foreign query method.
   *
   * @return array An array of fields with their foreign query method
   */
  abstract public function getForeignColumnQueries();

  /**
   * Builds a Propel Criteria with processed values.
   *
   * Overload this method instead of {@link buildCriteria()} to avoid running
   * {@link processValues()} multiple times.
   *
   * @param  array $values
   *
   * @return Criteria
   */
  protected function doBuildCriteria(array $values)
  {
    $criteria = PropelQuery::from($this->getModelName());
    $peer = $criteria->getModelPeerName();
    
    $fields = $this->getFields();
    $foreignQueries = $this->getForeignColumnQueries();
    
    // add those fields that are not represented in getFields() with a null type
    $names = array_merge($fields, array_diff(array_keys($this->validatorSchema->getFields()), array_keys($fields)));
    $fields = array_merge($fields, array_combine($names, array_fill(0, count($names), null)));
    
    foreach($fields as $field => $type)
    {
      if(! isset($values[$field]) || null === $values[$field] || '' === $values[$field])
      {
        continue;
      }
      
      try
      {
        $ucField = call_user_func(array(
          $peer, 
          'translateFieldName'
        ), $field, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);
        $isReal = true;
      }
      catch(Exception $e)
      {
        $ucField = self::camelize($field);
        $isReal = false;
      }
      
      if(isset($foreignQueries[$field]) && method_exists($this, $method = sprintf('add%sCriteria', $type)))
      {
        $foreignCriteria = $criteria->$foreignQueries[$field]();
        $this->$method($foreignCriteria, $field, $values[$field]);
        $criteria = $foreignCriteria->endUse();
      }
      elseif(method_exists($this, $method = sprintf('add%sColumnCriteria', $ucField)))
      {
        // FormFilter::add[ColumnName]Criteria
        $this->$method($criteria, $field, $values[$field]);
      }
      elseif($isReal && method_exists($this, $method = sprintf('add%sCriteria', $type)))
      {
        // FormFilter::add[ColumnType]Criteria
        $this->$method($criteria, $field, $values[$field]);
      }
      elseif(method_exists($criteria, $method = sprintf('filterBy%s', $ucField)))
      {
        // ModelCriteria::filterBy[ColumnName]
        $criteria->$method($values[$field]);
      }
      else
      {
        $columnParams = ExtjsGeneratorUtil::getColumnParams($field, $this->getModelName());
        $foreignQueryMethod = sprintf('use%sQuery', $columnParams['relation_name']);
        if(! method_exists($this, $method = sprintf('add%sCriteria', $columnParams['type'])) || ! method_exists($criteria, $foreignQueryMethod) || ! isset($columnParams['relation_name']))
        {
          throw new LogicException(sprintf('You must define a "%s" method in the %s class to be able to filter with the "%s" field.', sprintf('filterBy%s', $ucField), get_class($criteria), $field));
        }
        $foreignCriteria = $criteria->$foreignQueryMethod();
        $this->$method($foreignCriteria, $columnParams['field_name'], $values[$field]);
        $criteria = $foreignCriteria->endUse();
      }
    }
    
    return $criteria;
  }

  protected function addForeignKeyCriteria(Criteria $criteria, $field, $value)
  {
    $colname = $this->getColumnName($field, $criteria->getModelName());
    
    if(is_array($value) && isset($value['is_empty']) && $value['is_empty'])
    {
      $criterion = $criteria->getNewCriterion($colname, '');
      $criterion->addOr($criteria->getNewCriterion($colname, null, Criteria::ISNULL));
      $criteria->add($criterion);
    }
    else if(is_array($value))
    {
      $values = $value;
      $value = array_pop($values);
      $criterion = $criteria->getNewCriterion($colname, $value);
      
      foreach($values as $value)
      {
        $criterion->addOr($criteria->getNewCriterion($colname, $value));
      }
      
      $criteria->add($criterion);
    }
    else
    {
      $criteria->add($colname, $value);
    }
  }

  protected function addTextCriteria(Criteria $criteria, $field, $values)
  {
    $colname = $this->getColumnName($field, $criteria->getModelName());
    
    if(is_array($values) && isset($values['is_empty']) && $values['is_empty'])
    {
      $criterion = $criteria->getNewCriterion($colname, '');
      $criterion->addOr($criteria->getNewCriterion($colname, null, Criteria::ISNULL));
      $criteria->add($criterion);
    }
    else if(is_array($values) && isset($values['text']) && '' != $values['text'])
    {
      $criteria->add($colname, '%' . $values['text'] . '%', Criteria::LIKE);
    }
    else if(is_scalar($values) && '' != $values)
    {
      $criteria->add($colname, '%' . $values . '%', Criteria::LIKE);
    }
  }

  protected function addNumberCriteria(Criteria $criteria, $field, $values)
  {
    $colname = $this->getColumnName($field, $criteria->getModelName());
    
    if(is_array($values) && isset($values['is_empty']) && $values['is_empty'])
    {
      $criterion = $criteria->getNewCriterion($colname, '');
      $criterion->addOr($criteria->getNewCriterion($colname, null, Criteria::ISNULL));
      $criteria->add($criterion);
    }
    else if(is_array($values) && isset($values['text']) && '' != $values['text'])
    {
      $criteria->add($colname, $values['text']);
    }
    else if(is_scalar($values) && '' != $values)
    {
      $criteria->add($colname, $values);
    }
  }

  protected function addBooleanCriteria(Criteria $criteria, $field, $value)
  {
    $criteria->add($this->getColumnName($field, $criteria->getModelName()), $value);
  }

  protected function addDateCriteria(Criteria $criteria, $field, $values)
  {
    $colname = $this->getColumnName($field, $criteria->getModelName());
    
    if(isset($values['is_empty']) && $values['is_empty'])
    {
      $criteria->add($colname, null, Criteria::ISNULL);
    }
    else
    {
      $criterion = null;
      if(null !== $values['from'] && null !== $values['to'])
      {
        $criterion = $criteria->getNewCriterion($colname, $values['from'], Criteria::GREATER_EQUAL);
        $criterion->addAnd($criteria->getNewCriterion($colname, $values['to'], Criteria::LESS_EQUAL));
      }
      else if(null !== $values['from'])
      {
        $criterion = $criteria->getNewCriterion($colname, $values['from'], Criteria::GREATER_EQUAL);
      }
      else if(null !== $values['to'])
      {
        $criterion = $criteria->getNewCriterion($colname, $values['to'], Criteria::LESS_EQUAL);
      }
      
      if(null !== $criterion)
      {
        $criteria->add($criterion);
      }
    }
  }

  protected function getColumnName($field, $model)
  {
    return call_user_func(array(
      constant($model . '::PEER'), 
      'translateFieldName'
    ), $field, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_COLNAME);
  }
}
