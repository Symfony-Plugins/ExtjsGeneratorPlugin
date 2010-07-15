<?php
abstract class ExtjsFormPropel extends sfFormPropel
{
  /**
   * Adds CSRF protection to the current form.
   *
   * @param string $secret The secret to use to compute the CSRF token
   *
   * @return sfForm The current form instance
   */
  public function addCSRFProtection($secret = null)
  {
    if (null === $secret)
    {
      $secret = $this->localCSRFSecret;
    }

    if (false === $secret || (null === $secret && false === self::$CSRFSecret))
    {
      return $this;
    }

    if (null === $secret)
    {
      if (null === self::$CSRFSecret)
      {
        self::$CSRFSecret = md5(__FILE__.php_uname());
      }

      $secret = self::$CSRFSecret;
    }

    $token = $this->getCSRFToken($secret);

    $this->validatorSchema[self::$CSRFFieldName] = new sfValidatorCSRFToken(array('token' => $token));
    $this->widgetSchema[self::$CSRFFieldName] = new ExtjsWidgetFormInputHidden();
    $this->setDefault(self::$CSRFFieldName, $token);

    return $this;
  }

  protected function doSave($con = null)
  {
    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $this->updateObject();

    // this is Propel specific
    if(isset($this->getObject()->markForDeletion))
    {
      $this->getObject()->delete($con);
    }
    else
    {
      $this->getObject()->save($con);
    }

    // embedded forms
    $this->saveEmbeddedForms($con);

    // many-to-one fields
//    $this->saveManyToOneValues($con);
  }

  protected function saveManyToOneValues($con)
  {
    foreach($this->getValues() as $fieldName => $value)
    {
      if(strpos($fieldName, '-') && isset($this->widgetSchema[$fieldName]))
      {
        $params = ExtjsGeneratorUtil::getColumnParams($fieldName, get_class($this->getObject()));

        //TODO: throw a real form error here if possible instead of failing quietly
        // not a "real" column of this object
        if (!method_exists($this->getObject(), sprintf("get%s", $params['relation_name'])))
        {
          continue;
        }

        $relation = call_user_func(array($this->getObject(), sprintf("get%s", $params['relation_name'])));

        //TODO: throw a real form error here if possible instead of failing quietly
        // not a "real" column of this object
        if (!method_exists($relation, $method = sprintf('set%s', ucfirst($params['php_name']))))
        {
          continue;
        }

        $relation = call_user_func(array($relation, $method), $value);
        $relation->save($con);
      }
    }
  }

  public function mergeOneToOneRelation($relationName)
  {
    $relationMap = $this->getRelationMap($relationName);
    if ($relationMap->getType() != RelationMap::ONE_TO_ONE)
    {
      throw new sfException('mergeOneToOneRelation() only works for one-to-one relationships');
    }

    $relatedModel = $relationMap->getLocalTable()->getClassname();
    $relatedFormClass = sprintf('Extjs%sForm',ucfirst($relatedModel));
    $relatedForm = new $relatedFormClass();

    foreach($relationMap->getLocalTable()->getPrimaryKeys() as $primaryKey)
    {
      $pk = call_user_func(array($relatedModel.'Peer', 'translateFieldname'), $primaryKey->getPhpName(), BasePeer::TYPE_PHPNAME, BasePeer::TYPE_FIELDNAME);
      unset($relatedForm[$pk]);
    }

    $this->mergeForm($relatedForm);

    return $this;
  }

  public function getRelatedObject($relatedObjectGetter)
  {
    try
    {
      return $this->object->$relatedObjectGetter();
    }
    catch (sfException $e)
    {
      // relation not found
      return null;
    }
  }
}
