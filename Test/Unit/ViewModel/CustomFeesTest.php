<?php
declare(strict_types=1);

namespace LokiCheckout\JosephLeedyCustomFees\Test\Unit\ViewModel;

use JosephLeedy\CustomFees\Api\ConfigInterface;
use JosephLeedy\CustomFees\Api\Data\CustomFeeTaxDetailsInterface;
use JosephLeedy\CustomFees\Model\DisplayType;
use LokiCheckout\JosephLeedyCustomFees\ViewModel\CustomFees;
use Magento\Quote\Api\Data\TotalSegmentExtensionInterface;
use Magento\Quote\Api\Data\TotalSegmentInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CustomFeesTest extends TestCase
{
    private ConfigInterface&MockObject $config;
    private CustomFees $viewModel;

    protected function setUp(): void
    {
        $this->config = $this->createMock(ConfigInterface::class);
        $this->viewModel = new CustomFees($this->config);
    }

    public function testGetDisplayTypeReturnsCartDisplayType(): void
    {
        $this->config->method('getCartDisplayType')->willReturn(DisplayType::Both);

        $this->assertSame(DisplayType::Both, $this->viewModel->getDisplayType());
    }

    public function testGetValueWithTaxReturnsTaxedValue(): void
    {
        $taxDetails = $this->createMock(CustomFeeTaxDetailsInterface::class);
        $taxDetails->method('getValueWithTax')->willReturn(12.1);

        $extensionAttributes = $this->getMockBuilder(TotalSegmentExtensionInterface::class)
            ->addMethods(['getCustomFeeTaxDetails'])
            ->getMock();
        $extensionAttributes->method('getCustomFeeTaxDetails')->willReturn($taxDetails);

        $totalSegment = $this->createMock(TotalSegmentInterface::class);
        $totalSegment->method('getExtensionAttributes')->willReturn($extensionAttributes);
        $totalSegment->method('getValue')->willReturn(10.0);

        $this->assertSame(12.1, $this->viewModel->getValueWithTax($totalSegment));
    }

    public function testGetValueWithTaxFallsBackToValueWithoutTaxDetails(): void
    {
        $extensionAttributes = $this->getMockBuilder(TotalSegmentExtensionInterface::class)
            ->addMethods(['getCustomFeeTaxDetails'])
            ->getMock();
        $extensionAttributes->method('getCustomFeeTaxDetails')->willReturn(null);

        $totalSegment = $this->createMock(TotalSegmentInterface::class);
        $totalSegment->method('getExtensionAttributes')->willReturn($extensionAttributes);
        $totalSegment->method('getValue')->willReturn(10.0);

        $this->assertSame(10.0, $this->viewModel->getValueWithTax($totalSegment));
    }

    public function testGetValueWithTaxFallsBackToValueWithoutExtensionAttributes(): void
    {
        $totalSegment = $this->createMock(TotalSegmentInterface::class);
        $totalSegment->method('getExtensionAttributes')->willReturn(null);
        $totalSegment->method('getValue')->willReturn(10.0);

        $this->assertSame(10.0, $this->viewModel->getValueWithTax($totalSegment));
    }
}
