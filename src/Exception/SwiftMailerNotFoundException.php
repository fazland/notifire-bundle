<?php

namespace Fazland\NotifireBundle\Exception;

/**
 * This exception is raised when the {@see SwiftMailerConfigurationPass} find a
 * SwiftMailer mailer name in NotifireBundle configuration that is not configured
 * in the SwiftMailer configuration part.
 *
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class SwiftMailerNotFoundException extends \Exception
{

}
