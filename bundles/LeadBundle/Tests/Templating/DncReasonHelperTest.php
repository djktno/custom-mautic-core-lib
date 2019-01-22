<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\Tests\Templating;

use Mautic\LeadBundle\Entity\DoNotContact;
use Mautic\LeadBundle\Templating\Helper\DncReasonHelper;
use Symfony\Component\Translation\TranslatorInterface;

class DncReasonHelperTest extends \PHPUnit_Framework_TestCase
{
    private $reasonTo = [
        DoNotContact::UNSUBSCRIBED => 'mautic.lead.event.donotcontact_unsubscribed',
        DoNotContact::BOUNCED      => 'mautic.lead.event.donotcontact_bounced',
        DoNotContact::MANUAL       => 'mautic.lead.event.donotcontact_manual',
    ];

    private $translations = [
        'mautic.lead.event.donotcontact_unsubscribed' => 'a',
        'mautic.lead.event.donotcontact_bounced'      => 'b',
        'mautic.lead.event.donotcontact_manual'       => 'c',
    ];

    public function testToText()
    {
        foreach ($this->reasonTo as $reasonId => $translationKey) {
            $translationResult = $this->translations[$translationKey];

            $translator = $this->createMock(TranslatorInterface::class);
            $translator->expects($this->once())
                ->method('trans')
                ->with($translationKey)
                ->willReturn($translationResult);

            $dncReasonHelper = new DncReasonHelper($translator);

            $this->assertSame($translationResult, $dncReasonHelper->toText($reasonId));
        }

        $translator      = $this->createMock(TranslatorInterface::class);
        $dncReasonHelper = new DncReasonHelper($translator);
        $this->expectException(\InvalidArgumentException::class);
        $dncReasonHelper->toText('undefined_dnc_reason_id');
    }

    public function testGetName()
    {
        $translator      = $this->createMock(TranslatorInterface::class);
        $dncReasonHelper = new DncReasonHelper($translator);
        $this->assertSame('lead_dnc_reason', $dncReasonHelper->getName());
    }
}
