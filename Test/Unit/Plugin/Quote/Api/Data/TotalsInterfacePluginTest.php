<?php
declare(strict_types=1);

namespace LokiCheckout\JosephLeedyCustomFees\Test\Unit\Plugin\Quote\Api\Data;

use JosephLeedy\CustomFees\Api\ConfigInterface;
use LokiCheckout\JosephLeedyCustomFees\Plugin\Quote\Api\Data\TotalsInterfacePlugin;
use Magento\Framework\View\Layout\ProcessorInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Result\Layout;
use Magento\Quote\Api\Data\TotalSegmentExtensionFactory;
use Magento\Quote\Api\Data\TotalSegmentExtensionInterface;
use Magento\Quote\Api\Data\TotalSegmentInterface;
use Magento\Quote\Api\Data\TotalSegmentInterfaceFactory;
use Magento\Quote\Api\Data\TotalsInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TotalsInterfacePluginTest extends TestCase
{
    private Layout&MockObject $layout;
    private TotalSegmentInterfaceFactory&MockObject $totalSegmentFactory;
    private TotalSegmentExtensionFactory&MockObject $totalSegmentExtensionFactory;
    private ConfigInterface&MockObject $config;
    private TotalsInterfacePlugin $plugin;

    protected function setUp(): void
    {
        $this->layout = $this->createMock(Layout::class);
        $this->totalSegmentFactory = $this->createMock(TotalSegmentInterfaceFactory::class);
        $this->totalSegmentExtensionFactory = $this->createMock(TotalSegmentExtensionFactory::class);
        $this->config = $this->createMock(ConfigInterface::class);

        $this->plugin = new TotalsInterfacePlugin(
            $this->layout,
            $this->totalSegmentFactory,
            $this->totalSegmentExtensionFactory,
            $this->config
        );
    }

    private function mockLayoutHandles(array $handles): void
    {
        $update = $this->createMock(ProcessorInterface::class);
        $update->method('getHandles')->willReturn($handles);

        $layout = $this->createMock(LayoutInterface::class);
        $layout->method('getUpdate')->willReturn($update);

        $this->layout->method('getLayout')->willReturn($layout);
    }

    public function testReturnsResultUnchangedWithoutLokiCheckoutHandle(): void
    {
        $this->mockLayoutHandles(['default']);

        $result = ['subtotal' => $this->createMock(TotalSegmentInterface::class)];
        $subject = $this->createMock(TotalsInterface::class);

        $this->assertSame($result, $this->plugin->afterGetTotalSegments($subject, $result));
    }

    public function testReturnsNullUnchanged(): void
    {
        $this->mockLayoutHandles(['loki_checkout']);

        $subject = $this->createMock(TotalsInterface::class);

        $this->assertNull($this->plugin->afterGetTotalSegments($subject, null));
    }

    public function testReturnsResultUnchangedWithoutConfiguredFees(): void
    {
        $this->mockLayoutHandles(['loki_checkout']);
        $this->config->method('getCustomFees')->willReturn([]);

        $result = ['subtotal' => $this->createMock(TotalSegmentInterface::class)];
        $subject = $this->createMock(TotalsInterface::class);

        $this->assertSame($result, $this->plugin->afterGetTotalSegments($subject, $result));
    }

    public function testGroupsFeeSegmentsIntoCustomFeesSegmentBeforeGrandTotal(): void
    {
        $this->mockLayoutHandles(['loki_checkout']);
        $this->config->method('getCustomFees')->willReturn([
            ['code' => 'example_fee', 'title' => 'Example Fee'],
        ]);

        $feeSegment = $this->createMock(TotalSegmentInterface::class);
        $grandTotalSegment = $this->createMock(TotalSegmentInterface::class);

        $result = [
            'subtotal' => $this->createMock(TotalSegmentInterface::class),
            'example_fee' => $feeSegment,
            'grand_total' => $grandTotalSegment,
        ];

        $extension = $this->getMockBuilder(TotalSegmentExtensionInterface::class)
            ->addMethods(['setCustomFeeSegments'])
            ->getMock();
        $extension->expects($this->once())
            ->method('setCustomFeeSegments')
            ->with(['example_fee' => $feeSegment]);
        $this->totalSegmentExtensionFactory->method('create')->willReturn($extension);

        $customFeesSegment = $this->createMock(TotalSegmentInterface::class);
        $customFeesSegment->expects($this->once())->method('setCode')->with('custom_fees');
        $customFeesSegment->expects($this->once())->method('setExtensionAttributes')->with($extension);
        $this->totalSegmentFactory->method('create')->willReturn($customFeesSegment);

        $subject = $this->createMock(TotalsInterface::class);
        $newResult = $this->plugin->afterGetTotalSegments($subject, $result);

        $this->assertSame(
            ['subtotal', 'custom_fees', 'grand_total'],
            array_keys($newResult)
        );
        $this->assertSame($customFeesSegment, $newResult['custom_fees']);
    }

    public function testReturnsResultUnchangedWhenCustomFeesSegmentAlreadyExists(): void
    {
        $this->mockLayoutHandles(['loki_checkout']);

        $result = ['custom_fees' => $this->createMock(TotalSegmentInterface::class)];
        $subject = $this->createMock(TotalsInterface::class);

        $this->assertSame($result, $this->plugin->afterGetTotalSegments($subject, $result));
    }
}
