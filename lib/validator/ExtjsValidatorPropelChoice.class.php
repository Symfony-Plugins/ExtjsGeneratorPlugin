<?php
class ExtjsValidatorPropelChoice extends sfValidatorPropelChoice
{

  protected function doClean($value)
  {
    if(strpos($value, ',')) $value = explode(',', $value);

    $criteria = null === $this->getOption('criteria') ? new Criteria() : clone $this->getOption('criteria');

    if($this->getOption('multiple'))
    {
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

      $dbcount = call_user_func(array(
        constant($this->getOption('model') . '::PEER'),
        'doCount'
      ), $criteria, $this->getOption('connection'));

      if($dbcount != $count)
      {
        throw new sfValidatorError($this, 'invalid', array(
          'value' => $value
        ));
      }
    }
    else
    {
      $criteria->addAnd($this->getColumn(), $value);

      $dbcount = call_user_func(array(
        constant($this->getOption('model') . '::PEER'),
        'doCount'
      ), $criteria, $this->getOption('connection'));

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
