<?php
class ExtjsValidatorPropelChoice extends sfValidatorPropelChoice
{

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $criteria = PropelQuery::from($this->getOption('model'));
    if($this->getOption('criteria'))
    {
      $criteria->mergeWith($this->getOption('criteria'));
    }
    foreach($this->getOption('query_methods') as $method)
    {
      $criteria->$method();
    }
    
    if($this->getOption('multiple'))
    {
      if(is_string($value) && strpos($value, ',')) $value = explode(',', $value);
      
      if(! is_array($value))
      {
        $value = array(
          $value
        );
      }
      
      $count = count($value);
      
      if($this->hasOption('min') && $count < $this->getOption('min'))
      {
        throw new sfValidatorError($this, 'min', array(
          'count' => $count, 
          'min' => $this->getOption('min')
        ));
      }
      
      if($this->hasOption('max') && $count > $this->getOption('max'))
      {
        throw new sfValidatorError($this, 'max', array(
          'count' => $count, 
          'max' => $this->getOption('max')
        ));
      }
      
      $criteria->addAnd($this->getColumn(), $value, Criteria::IN);
      
      $dbcount = $criteria->count($this->getOption('connection'));
      
      if($dbcount != $count)
      {
        throw new sfValidatorError($this, 'invalid', array(
          'value' => $value
        ));
      }
    }
    elseif(! is_array($value))
    {
      $criteria->addAnd($this->getColumn(), $value);
      
      $dbcount = $criteria->count($this->getOption('connection'));
      
      if(0 === $dbcount)
      {
        throw new sfValidatorError($this, 'invalid', array(
          'value' => $value
        ));
      }
    }
    
    return $value;
  }
}
