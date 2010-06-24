<?php

/**
 * Extjs generator.
 *
 * @package    symfony
 * @subpackage ExtjsGenerator
 * @author     Benjamin Runnels <benjamin.r.runnels@citi.com>
 */
class ExtjsGeneratorUtil
{
  /*
   * converts a boolean value to a string
   * 
   *  renderer:            ExtjsGeneratorUtil::renderBooleanToString
   *  renderer_arguments:
   *    args:
   *      trueValue:       'True'
   *      falseValue:      'False'
   *    
   *  @param $value boolean value
   *  @param $args array of arguments
   *  
   *  @returns string boolean as configured string
   */
  public static function renderBooleanToString($value, $args = array( 'trueValue' => 'true', 'falseValue' => 'false'))
  {
    return (($value == true) ? $args['trueValue'] : $args['falseValue'])."\r";
  }

  /**
   * Returns an array of params for a column name.
   *
   * @return array array of params.
   */
  public static function getColumnParams($columnName, $model)
  {
    $columnArr = explode('-', $columnName);
    $relatedGetter = '';
    $map = call_user_func(array(
      $model . 'Peer',
      'getTableMap'
    ));

    for($i = 0; $i <= count($columnArr) - 1; $i ++)
    {
      $column = $columnArr[$i];

      try
      {
        $fieldName = call_user_func(array(
          $model . 'Peer',
          'translateFieldName'
        ), sfInflector::camelize(strtolower($columnArr[$i])), BasePeer::TYPE_PHPNAME, BasePeer::TYPE_FIELDNAME);
      }
      catch(PropelException $e)
      {
        $fieldName = strtolower($columnArr[$i]);
      }

      try
      {
        $column = $map->getColumn($fieldName);
      }
      catch(PropelException $e)
      {
        $relationName = sfInflector::camelize($fieldName);
        try
        {
          $relation = $map->getRelation($relationName);
        }
        catch(PropelException $e)
        {
          try
          {
            // also try lcfirst as relations could start with either
            $relationName = (string)(strtolower(substr($relationName, 0, 1)) . substr($relationName, 1));
            $relation = $map->getRelation($relationName);
          }
          catch(PropelException $e)
          {
            //not a real column but we try using it anyhow
            if($i != count($columnArr) - 1)
            {
              $relatedGetter .= sprintf('get%s()->', sfInflector::camelize($columnArr[$i]));
            }
            unset($relationName);
            continue;
          }
        }

        $direction = ($relation->getType() == RelationMap::ONE_TO_ONE) ? 'Local' : 'Foreign';
        $map = call_user_func(array($relation, sprintf('get%sTable', $direction))); //$relation->getRightTable();
        $relationColumns = call_user_func(array($relation, sprintf('get%sColumns', $direction))); //$relation->getRightColumns();
        $column = $relationColumns[0];
        $model = $map->getPhpName(); // $relation->getRightTable()->getPhpName();
        if($i != count($columnArr) - 1)
        {
          $relatedGetter .= sprintf('get%s()->', $relationName);
        }
      }
    }

    $phpName = ($column instanceof ColumnMap) ? $column->getPhpName() : sfInflector::camelize($column);

    return array(
      'is_link' => ($column instanceof ColumnMap) ? (boolean)$column->isPrimaryKey() : false,
      'is_real' => ($column instanceof ColumnMap) ? true : false,
      'getter' => sprintf('%sget%s', $relatedGetter, $phpName),
      'model' => $model,
      'php_name' => $phpName,
      'field_name' => $fieldName,
      'relation_name' => isset($relationName) ? $relationName : null,
      'sort_method' => isset($relationName) ? sprintf('orderBy%s.%s', $relationName, $phpName) : null,
      'type' => ($column instanceof ColumnMap) ? self::getType($column) : 'Text'
    );
  }

  /**
   * Returns the type of a column.
   *
   * @param  object $column A column object
   *
   * @return string The column type
   */
  public static function getType($column)
  {
    if($column->isForeignKey())
    {
      return 'ForeignKey';
    }

    switch($column->getType())
    {
      case PropelColumnTypes::BOOLEAN:
        return 'Boolean';
      case PropelColumnTypes::DATE:
      case PropelColumnTypes::TIMESTAMP:
        return 'Date';
      case PropelColumnTypes::TIME:
        return 'Time';
      default:
        return 'Text';
    }
  }
}
