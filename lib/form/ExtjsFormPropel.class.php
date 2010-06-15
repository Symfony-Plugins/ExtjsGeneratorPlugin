<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfFormPropel is the base class for forms based on Propel objects.
 *
 * This class extends BaseForm, a class generated automatically with each new project.
 *
 * @package    symfony
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfFormPropel.class.php 24068 2009-11-17 06:39:35Z Kris.Wallsmith $
 */
abstract class ExtjsFormPropel extends sfFormObject
{

  /**
   * List of forms that can be added by the user
   * @var array[sfForm]
   */
  protected $optionalForms = array();

  /**
   * Name of the field used for deletion.
   * @var string
   */
  protected $deleteField;

  /**
   * Constructor.
   *
   * @param mixed  A object used to initialize default values
   * @param array  An array of options
   * @param string A CSRF secret (false to disable CSRF protection, null to use the global CSRF secret)
   *
   * @see sfForm
   */
  public function __construct($object = null, $options = array(), $CSRFSecret = null)
  {
    $class = $this->getModelName();
    if (!$object)
    {
      $this->object = new $class();
    }
    else
    {
      if (!$object instanceof $class)
      {
        throw new sfException(sprintf('The "%s" form only accepts a "%s" object.', get_class($this), $class));
      }

      $this->object = $object;
      $this->isNew = $this->getObject()->isNew();
    }

    parent::__construct(array(), $options, $CSRFSecret);

    $this->updateDefaultsFromObject();
  }

  /**
   * @return PropelPDO
   * @see sfFormObject
   */
  public function getConnection()
  {
    return Propel::getConnection(constant($this->getPeer().'::DATABASE_NAME'));
  }

  /**
   * Embeds i18n objects into the current form.
   *
   * @param array   $cultures   An array of cultures
   * @param string  $decorator  A HTML decorator for the embedded form
   */
  public function embedI18n($cultures, $decorator = null)
  {
    if (!$this->isI18n())
    {
      throw new sfException(sprintf('The model "%s" is not internationalized.', $this->getModelName()));
    }

    $class = $this->getI18nFormClass();
    foreach ($cultures as $culture)
    {
      $method = sprintf('getCurrent%s', $this->getI18nModelName($culture));
      $i18nObject = $this->getObject()->$method($culture);
      $i18n = new $class($i18nObject);

      if ($i18nObject->isNew())
      {
        unset($i18n['id'], $i18n['culture']);
      }

      $this->embedForm($culture, $i18n, $decorator);
    }
  }

  /**
   * Binds the form with input values.
   *
   * It triggers the validator schema validation.
   *
   * @param array $taintedValues  An array of input values
   * @param array $taintedFiles   An array of uploaded files (in the $_FILES or $_GET format)
   */
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    $this->addOptionalForms($taintedValues);
    return parent::bind($taintedValues, $taintedFiles);
  }

  public function addOptionalForms($taintedValues = null)
  {
    foreach ($this->optionalForms as $name => $form) {
      $i = 1;
      if (strpos($name, '/') === false)
      {
        // The form must be added to the main form
        while (array_key_exists($name . $i, $taintedValues))
        {
          $this->embedForm($name . $i, clone $form);
          $this->getWidgetSchema()->moveField($name . $i, sfWidgetFormSchema::BEFORE, $name);
          $i++;
        }
      }
      else
      {
        // The form must be added to an embedded form
        list($parent, $name) = explode('/', $name);
        if (!isset($taintedValues[$parent]))
        {
          continue;
        }
        $taintedValuesCopy = $taintedValues[$parent];
        $target = $this->embeddedForms[$parent];
        while (array_key_exists($name . $i, $taintedValuesCopy))
        {
          $target->embedForm($name . $i, clone $form);
          $target->getWidgetSchema()->moveField($name . $i, sfWidgetFormSchema::BEFORE, $name);
          $i++;
          // the parent form schema is not updated when updating an embedded form
          // so we must embed it again
          $this->embedForm($parent, $target);
        }
      }
    }
  }


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

  /**
   * Adds a widget to the form, and declare this widget as the delete control.
   * If the bound widget value is true, then the related object will be deleted
   *
   * @param string       $name   The field name
   * @param sfWidgetForm $widget The widget
   *
   * @return sfPropelForm The current form instance
   */
  public function setDeleteWidget($name, $widget)
  {
    $this->setWidget($name, $widget);
    $this->setValidator($name, new sfValidatorPass(array('required' => false)));
    $this->setDeleteField($name);

    return $this;
  }

  /**
   * Updates and saves the current object.
   * @see sfFormObject
   *
   * If you want to add some logic before saving or save other associated
   * objects, this is the method to override.
   *
   * @param mixed $con An optional connection object
   */
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
    $this->saveManyToOneValues($con);
  }

  protected function saveManyToOneValues($con)
  {
    foreach($this->getValues() as $fieldName => $value)
    {
      if(strpos($fieldName, '-') && isset($this->widgetSchema[$fieldName]))
      {
        $params = ExtjsGeneratorUtil::getColumnParams($fieldName, get_class($this->getObject()));
        $relation = call_user_func(array($this->getObject(), sprintf("get%s", $params['relation_name'])));

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

  /**
   * Updates the values of the object with the cleaned up values.
   *
   * If you want to add some logic before updating or update other associated
   * objects, this is the method to override.
   * @see sfFormObject
   *
   * @param array $values An array of values
   */
  protected function doUpdateObject($values)
  {
    if ($this->hasDeleteField())
    {
      if (isset($values[$this->getDeleteField()]) && $values[$this->getDeleteField()])
      {
        $this->getObject()->markForDeletion = true;
        return;
      }
    }
    $this->getObject()->fromArray($values, BasePeer::TYPE_FIELDNAME);
  }

  /**
   * Saves embedded form objects.
   * @see sfFormObject
   *
   * @param mixed $con   An optional connection object
   * @param array $forms An array of forms
   */
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $con)
    {
      $con = $this->getConnection();
    }

    if (null === $forms)
    {
      $forms = $this->embeddedForms;
    }

    foreach ($forms as $form)
    {
      if ($form instanceof sfFormObject)
      {
        $form->saveEmbeddedForms($con);
        // this is Propel specific
        if(isset($form->getObject()->markForDeletion))
        {
          $form->getObject()->delete($con);
        }
        else
        {
          $form->getObject()->save($con);
        }
      }
      else
      {
        $this->saveEmbeddedForms($con, $form->getEmbeddedForms());
      }
    }
  }

  /**
   * Processes cleaned up values with user defined methods.
   *
   * To process a value before it is used by the updateObject() method,
   * you need to define an updateXXXColumn() method where XXX is the PHP name
   * of the column.
   *
   * The method must return the processed value or false to remove the value
   * from the array of cleaned up values.
   *
   * @see sfFormObject
   */
  public function processValues($values)
  {
    // see if the user has overridden some column setter
    $valuesToProcess = $values;
    foreach ($valuesToProcess as $field => $value)
    {
      try
      {
        $method = sprintf('update%sColumn', call_user_func(array($this->getPeer(), 'translateFieldName'), $field, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME));
      }
      catch (Exception $e)
      {
        // not a "real" column of this object
        if (!method_exists($this, $method = sprintf('update%sColumn', self::camelize($field))))
        {
          continue;
        }
      }

      if (method_exists($this, $method))
      {
        if (false === $ret = $this->$method($value))
        {
          unset($values[$field]);
        }
        else
        {
          $values[$field] = $ret;
        }
      }
      else
      {
        // save files
        if ($this->validatorSchema[$field] instanceof sfValidatorFile)
        {
          $values[$field] = $this->processUploadedFile($field, null, $valuesToProcess);
        }
      }
    }

    return $values;
  }

  /**
   * Returns true if the current form has some associated i18n objects.
   *
   * @return Boolean true if the current form has some associated i18n objects, false otherwise
   */
  public function isI18n()
  {
    return null !== $this->getI18nFormClass();
  }

  /**
   * Returns the name of the i18n model.
   *
   * @return string The name of the i18n model
   */
  public function getI18nModelName()
  {
    return null;
  }

  /**
   * Returns the name of the i18n form class.
   *
   * @return string The name of the i18n form class
   */
  public function getI18nFormClass()
  {
    return null;
  }

  /**
   * Updates the default values of the form with the current values of the current object.
   */
  protected function updateDefaultsFromObject()
  {
    // update defaults for the main object
    if ($this->isNew())
    {
      $this->setDefaults(array_merge($this->getObject()->toArray(BasePeer::TYPE_FIELDNAME), $this->getDefaults()));
    }
    else
    {
      $this->setDefaults(array_merge($this->getDefaults(), $this->getObject()->toArray(BasePeer::TYPE_FIELDNAME)));
    }
  }

  /**
   * Saves the uploaded file for the given field.
   *
   * @param  string $field The field name
   * @param  string $filename The file name of the file to save
   * @param  array  $values An array of values
   *
   * @return string The filename used to save the file
   */
  protected function processUploadedFile($field, $filename = null, $values = null)
  {
    if (!$this->validatorSchema[$field] instanceof sfValidatorFile)
    {
      throw new LogicException(sprintf('You cannot save the current file for field "%s" as the field is not a file.', $field));
    }

    if (null === $values)
    {
      $values = $this->values;
    }

    if (isset($values[$field.'_delete']) && $values[$field.'_delete'])
    {
      $this->removeFile($field);

      return '';
    }

    if (!$values[$field])
    {
      $column = call_user_func(array($this->getPeer(), 'translateFieldName'), $field, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);
      $getter = 'get'.$column;

      return $this->getObject()->$getter();
    }

    // we need the base directory
    if (!$this->validatorSchema[$field]->getOption('path'))
    {
      return $values[$field];
    }

    $this->removeFile($field);

    return $this->saveFile($field, $filename, $values[$field]);
  }

  /**
   * Removes the current file for the field.
   *
   * @param string $field The field name
   */
  protected function removeFile($field)
  {
    if (!$this->validatorSchema[$field] instanceof sfValidatorFile)
    {
      throw new LogicException(sprintf('You cannot remove the current file for field "%s" as the field is not a file.', $field));
    }

    $column = call_user_func(array($this->getPeer(), 'translateFieldName'), $field, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);
    $getter = 'get'.$column;

    if (($directory = $this->validatorSchema[$field]->getOption('path')) && is_file($directory.DIRECTORY_SEPARATOR.$this->getObject()->$getter()))
    {
      unlink($directory.DIRECTORY_SEPARATOR.$this->getObject()->$getter());
    }
  }

  /**
   * Get the name of the Peer class of the form's model, e.g. 'AuthorPeer'
   *
   * @return string A Peer class name
   */
  public function getPeer()
  {
    return constant(get_class($this->getObject()).'::PEER');
  }

  /**
   * Saves the current file for the field.
   *
   * @param  string          $field    The field name
   * @param  string          $filename The file name of the file to save
   * @param  sfValidatedFile $file     The validated file to save
   *
   * @return string The filename used to save the file
   */
  protected function saveFile($field, $filename = null, sfValidatedFile $file = null)
  {
    if (!$this->validatorSchema[$field] instanceof sfValidatorFile)
    {
      throw new LogicException(sprintf('You cannot save the current file for field "%s" as the field is not a file.', $field));
    }

    if (null === $file)
    {
      $file = $this->getValue($field);
    }

    $column = call_user_func(array($this->getPeer(), 'translateFieldName'), $field, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);
    $method = sprintf('generate%sFilename', $column);

    if (null !== $filename)
    {
      return $file->save($filename);
    }
    else if (method_exists($this, $method))
    {
      return $file->save($this->$method($file));
    }
    else if (method_exists($this->getObject(), $method))
    {
      return $file->save($this->getObject()->$method($file));
    }
    else
    {
      return $file->save();
    }
  }

  /**
   * Overrides sfForm::mergeForm() to also merge embedded forms
   * Allows autosave of merged collections
   *
   * @param  sfForm   $form      The sfForm instance to merge with current form
   *
   * @throws LogicException      If one of the form has already been bound
   */
  public function mergeForm(sfForm $form)
  {
    foreach ($form->getEmbeddedForms() as $name => $embeddedForm)
    {
      $this->embedForm($name, clone $embeddedForm);
    }
    parent::mergeForm($form);
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
      // no profile
      return null;
    }
  }

  /**
   * Merge a Collection form based on a Relation into this form.
   * Available options:
   *  - add_empty: Whether to allow the user to add new objects to the collection. Defaults to true
   * Additional options are passed to sfFromPropel::getRelationForm()
   *
   * @param string $relationName The name of a relation of the current Model, e.g. 'Book'
   * @param array  $options      An array of options
   *
   * @return sfPropelForm        The current form instance
   */
  public function mergeRelation($relationName, $options = array())
  {
    $options = array_merge(array(
      'add_empty'           => true,
    ), $options);

    $relationForm = $this->getRelationForm($relationName, $options);

    if ($options['add_empty'])
    {
      unset($options['add_empty']);
      $emptyForm = $this->getEmptyRelatedForm($relationName, $options);
      $emptyName = 'new' . $relationName;
      $relationForm->embedOptionalForm($emptyName, $emptyForm);
      $this->optionalForms[$emptyName] = $emptyForm;
    }

    $this->mergeForm($relationForm);

    return $this;
  }

  /**
   * Embed a Collection form based on a Relation into this form.
   * Available options:
   *  - title: The title of the collection form once embedded. Defaults to the relation name.
   *  - decorator: The decorator for the sfWidgetFormSchemaDecorator
   *  - add_empty: Whether to allow the user to add new objects to the collection. Defaults to true
   * Additional options are passed to sfFromPropel::getRelationForm()
   *  - empty_label: The label of the empty form
   *
   * @param string $relationName The name of a relation of the current Model, e.g. 'Book'
   * @param array  $options      An array of options
   *
   * @return sfPropelForm        The current form instance
   */
  public function embedRelation($relationName, $options = array())
  {
    $options = array_merge(array(
      'title'               => $relationName,
      'decorator'           => null,
      'add_empty'           => true,
      'empty_label'         => null,
    ), $options);

    $relationForm = $this->getRelationForm($relationName, $options);

    if ($options['add_empty'])
    {
      $emptyName = $options['empty_label'] ? $options['empty_label'] : 'new' . $relationName;
      unset($options['add_empty'], $options['empty_label']);
      $emptyForm = $this->getEmptyRelatedForm($relationName, $options);
      $relationForm->embedOptionalForm($emptyName, $emptyForm);
      $this->optionalForms[$options['title']. '/' . $emptyName] = $emptyForm;
    }

    $this->embedForm($options['title'], $relationForm, $options['decorator']);

    return $this;
  }

  /**
   * Get a Collection form based on a Relation of the current form's model.
   * Available options:
   *  - hide_on_new: If true, returns null for new objects. Defaults to false.
   *  - collection_form_class: class of the collection form to return. Defaults to sfFormPropelCollection.
   * Additional options are passed to sfFormPropelCollection::__construct()
   *
   * @param string $relationName The name of a relation of the current Model, e.g. 'Book'
   * @param array  $options      An array of options
   *
   * @return sfFormPropelCollection A form collection instance
   */
  public function getRelationForm($relationName, $options = array())
  {
    $options = array_merge(array(
      'hide_on_new'           => false,
      'collection_form_class' => 'sfFormPropelCollection',
      'add_delete'            => true,
    ), $options);

    if ($this->getObject()->isNew() && $options['hide_on_new'])
    {
      return;
    }
    unset($options['hide_on_new']);

    // compute relation elements
    $relationMap = $this->getRelationMap($relationName);
    if ($relationMap->getType() != RelationMap::ONE_TO_MANY)
    {
      throw new sfException('embedRelation() only works for one-to-many relationships');
    }

    $collection = call_user_func(array($this->getObject(), sprintf('get%ss', $relationName)));

    // compute relation fields, to be removed from embedded forms
    // because this data is not editable
    $options['remove_fields'] = $this->getRelationFields($relationMap);

    // create the relation form
    $collectionFormClass = $options['collection_form_class'];
    unset($options['collection_form_class']);

    $collectionForm = new $collectionFormClass($collection, $options);

    return $collectionForm;
  }

  /**
   * Get an empty Propel form based on a Relation of the current form's model.
   * Available options:
   *  - embedded_form_class: The class of the form to return
   *  - empty_label: The label of the empty form
   *
   * @param string $relationName The name of a relation of the current Model, e.g. 'Book'
   * @param array  $options      An array of options
   *
   * @return sfFormPropel A Propel form instance
   */
  public function getEmptyRelatedForm($relationName, $options = array())
  {
    $options = array_merge(array(
      'embedded_form_class' => null,
      'empty_label'         => null,
    ), $options);

    // compute relation elements
    $relationMap = $this->getRelationMap($relationName);
    if ($relationMap->getType() != RelationMap::ONE_TO_MANY)
    {
      throw new sfException('getEmptyRelatedForm() only works for one-to-many relationships');
    }

    $relatedClass = $relationMap->getRightTable()->getClassname();
    $relatedObject = new $relatedClass();

    // the relatedObject must be related to this form's object
    // but without actually adding the relatedObject to the collection
    // that's what the next lines do
    $realRelatedObjects = call_user_func(array($this->getObject(), sprintf('get%ss', $relationName)))->getArrayCopy();
    call_user_func(array($this->getObject(), sprintf('add%s', $relationName)), $relatedObject);
    call_user_func(array($this->getObject(), sprintf('init%ss', $relationName)));
    foreach ($realRelatedObjects as $realRelatedObject)
    {
      call_user_func(array($this->getObject(), sprintf('add%s', $relationName)), $realRelatedObject);
    }

    if (!$formClass = $options['embedded_form_class'])
    {
      $formClass = $relatedClass . 'Form';
    }
    $emptyForm = new $formClass($relatedObject);
    if ($label = $options['empty_label']) {
      $emptyForm->getWidgetSchema()->setLabel($label);
    }
    foreach ($this->getRelationFields($relationMap) as $leftCol => $field)
    {
      unset($emptyForm[$field]);
    }

    return $emptyForm;
  }

  protected function getRelationMap($relationName)
  {
    $tableMap = call_user_func(array($this->getPeer(), 'getTableMap'));
    return $tableMap->getRelation($relationName);
  }

  protected function getRelationFields($relationMap)
  {
    $relatedPeer = $relationMap->getRightTable()->getPeerClassname();
    $relationFields = array();
    foreach ($relationMap->getColumnMappings(RelationMap::LEFT_TO_RIGHT) as $leftCol => $rightCol)
    {
      $relationFields[$leftCol]= call_user_func(array($relatedPeer, 'translateFieldName'), $rightCol, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME);
    }
    return $relationFields;
  }

  public function setDeleteField($fieldName)
  {
    $this->deleteField = $fieldName;
  }

  public function hasDeleteField()
  {
    return null !== $this->deleteField;
  }

  public function getDeleteField()
  {
    return $this->deleteField;
  }

  public function __clone()
  {
    $this->object = clone $this->object;
  }
}
