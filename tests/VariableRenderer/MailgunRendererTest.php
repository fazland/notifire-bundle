<?php declare(strict_types=1);

namespace Fazland\NotifireBundle\Tests\VariableRenderer;

use Fazland\NotifireBundle\VariableRenderer\MailgunRenderer;
use PHPUnit\Framework\TestCase;

/**
 * @author Massimiliano Braglia <massimiliano.braglia@fazland.com>
 */
class MailgunRendererTest extends TestCase
{
    public function testRender()
    {
        $renderer = new MailgunRenderer();

        $variableName = 'variable_name';
        $expected = '%recipient.variable_name%';

        self::assertEquals($expected, $renderer->render($variableName));
    }
}
