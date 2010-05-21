[?php

/**
 * Project form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 */
abstract class BaseExtjsFormPropel extends sfFormPropel
{
  public function setup()
  {
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
}
