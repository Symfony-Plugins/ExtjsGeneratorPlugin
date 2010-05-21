[?php

/**
 * <?php echo $this->getModuleName() ?> module configuration.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage <?php echo $this->getModuleName()."\n" ?>
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: configuration.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class Base<?php echo ucfirst($this->getModuleName()) ?>GeneratorConfiguration extends ExtjsModelGeneratorConfiguration
{
<?php include dirname(__FILE__).'/actionsConfiguration.php' ?>

<?php include dirname(__FILE__).'/fieldsConfiguration.php' ?>

<?php include dirname(__FILE__).'/tabpanelConfiguration.php' ?>

<?php include dirname(__FILE__).'/bottomToolbarConfiguration.php' ?>

<?php include dirname(__FILE__).'/topToolbarConfiguration.php' ?>

<?php include dirname(__FILE__).'/filterpanelConfiguration.php' ?>

<?php include dirname(__FILE__).'/datastoreConfiguration.php' ?>

<?php include dirname(__FILE__).'/columnRenderersConfiguration.php' ?>

<?php include dirname(__FILE__).'/columnModelConfiguration.php' ?>

<?php include dirname(__FILE__).'/gridpanelConfiguration.php' ?>

<?php include dirname(__FILE__).'/formpanelConfiguration.php' ?>

<?php include dirname(__FILE__).'/objectActionsConfiguration.php' ?>

  /**
   * Gets the form class name.
   *
   * @return string The form class name
   */
  public function getFormClass()
  {
    return '<?php echo isset($this->config['form']['class']) ? $this->config['form']['class'] : 'Extjs' .ucfirst($this->getModelClass()).'Form' ?>';
<?php unset($this->config['form']['class']) ?>
  }

  public function hasFilterForm()
  {
    return <?php echo !isset($this->config['filter']['class']) || false !== $this->config['filter']['class'] ? 'true' : 'false' ?>;
  }

  /**
   * Gets the filter form class name
   *
   * @return string The filter form class name associated with this generator
   */
  public function getFilterFormClass()
  {
    return '<?php echo isset($this->config['filter']['class']) ? $this->config['filter']['class'] : 'Extjs' .ucfirst($this->getModelClass()).'FormFilter' ?>';
<?php unset($this->config['filter']['class']) ?>
  }

  public function getPrimaryKeys($firstOne = false)
  {
    $keys = <?php echo $this->asPhp($this->getPrimaryKeys()) ?>;
    return $firstOne ? $keys[0] : $keys;
  }

<?php include dirname(__FILE__).'/paginationConfiguration.php' ?>

<?php include dirname(__FILE__).'/sortingConfiguration.php' ?>

  public function getWiths()
  {
    return <?php echo $this->asPhp(isset($this->config['list']['with']) ? $this->config['list']['with'] : array()) ?>;
<?php unset($this->config['list']['with']) ?>
  }

  public function getQueryMethods()
  {
    return <?php echo $this->asPhp(isset($this->config['list']['query_methods']) ? $this->config['list']['query_methods'] : array()) ?>;
<?php unset($this->config['list']['query_methods']) ?>
  }

  public function getObjectName()
  {
    return '<?php echo isset($this->params['object_name']) ? $this->params['object_name'] : sfInflector::humanize($this->getModuleName()) ?>';
  }


}
