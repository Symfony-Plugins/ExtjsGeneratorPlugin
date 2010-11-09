<?php
/**
 * Extjs generator.
 *
 * @package    symfony
 * @subpackage ExtjsGeneratorPlugin
 * @author     Benjamin Runnels <kraven@kraven.org>
 */
class ExtjsPropel15Route extends sfPropel15Route
{
  public $object = false,  $options = array();

  /**
   * Gets the object related to the current route and parameters.
   *
   * This method is only accessible if the route is bound and of type "object".
   *
   * @param  array  $query_methods  Array of Propel 1.5 Query methods to be added
   *
   * @return Object The related object
   */
  public function getObject($query_methods = array())
  {
    if(! $this->isBound())
    {
      throw new LogicException('The route is not bound.');
    }

    if('object' != $this->options['type'])
    {
      throw new LogicException(sprintf('The route "%s" is not of type "object".', $this->pattern));
    }

    if(false !== $this->object)
    {
      return $this->object;
    }

    $query = $this->getQuery();

    //add passed query_methods
    if(count($query_methods))
    {
      foreach($query_methods as $method)
      {
        $query->$method();
      }
    }

    // check the related object
    $this->object = $query->filterByArray($this->getModelParameters($this->parameters))->findOne();
    if(! $this->object && (! isset($this->options['allow_empty']) || ! $this->options['allow_empty']))
    {
      throw new sfError404Exception(sprintf('Unable to find the %s object with the following parameters "%s").', $this->options['model'], str_replace("\n", '', var_export($this->filterParameters($this->parameters), true))));
    }

    return $this->object;
  }
}
