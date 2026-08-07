<?php
declare(strict_types=1);

namespace LokiCheckout\JosephLeedyCustomFees\Plugin\Quote\Api\Data;

use JosephLeedy\CustomFees\Api\ConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\Layout;
use Magento\Quote\Api\Data\TotalSegmentExtensionFactory;
use Magento\Quote\Api\Data\TotalSegmentExtensionInterface;
use Magento\Quote\Api\Data\TotalSegmentInterface;
use Magento\Quote\Api\Data\TotalSegmentInterfaceFactory;
use Magento\Quote\Api\Data\TotalsInterface;

use function array_column;
use function array_diff_key;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_search;
use function array_slice;
use function count;
use function in_array;

use const ARRAY_FILTER_USE_KEY;

class TotalsInterfacePlugin
{
    public function __construct(
        private readonly Layout $layout,
        private readonly TotalSegmentInterfaceFactory $totalSegmentFactory,
        private readonly TotalSegmentExtensionFactory $totalSegmentExtensionFactory,
        private readonly ConfigInterface $config
    ) {
    }

    /**
     * @return TotalSegmentInterface[]|null
     */
    public function afterGetTotalSegments(TotalsInterface $subject, ?array $result): ?array
    {
        $layoutHandles = $this->layout->getLayout()->getUpdate()->getHandles();

        if (
            $result === null
            || !in_array('loki_checkout', $layoutHandles, true)
            || array_key_exists('custom_fees', $result)
        ) {
            return $result;
        }

        try {
            $customFees = $this->config->getCustomFees();
        } catch (LocalizedException) {
            $customFees = [];
        }

        if (count($customFees) === 0) {
            return $result;
        }

        $customFeeCodes = array_column($customFees, 'code');
        $customFeesTotalSegments = array_filter(
            $result,
            static fn(string $key): bool => in_array($key, $customFeeCodes, true),
            ARRAY_FILTER_USE_KEY,
        );

        if (count($customFeesTotalSegments) === 0) {
            return $result;
        }

        $result = array_diff_key($result, $customFeesTotalSegments);

        /** @var TotalSegmentInterface $customFeesTotalSegment */
        $customFeesTotalSegment = $this->totalSegmentFactory->create();
        /** @var TotalSegmentExtensionInterface $customFeesTotalSegmentExtension */
        $customFeesTotalSegmentExtension = $this->totalSegmentExtensionFactory->create();

        $customFeesTotalSegmentExtension->setCustomFeeSegments($customFeesTotalSegments);

        $customFeesTotalSegment->setCode('custom_fees');
        $customFeesTotalSegment->setTitle((string)__('Custom Fees'));
        $customFeesTotalSegment->setValue(0);
        $customFeesTotalSegment->setArea();
        $customFeesTotalSegment->setExtensionAttributes($customFeesTotalSegmentExtension);

        $grandTotalPosition = array_search('grand_total', array_keys($result), true);
        if ($grandTotalPosition === false) {
            $result['custom_fees'] = $customFeesTotalSegment;

            return $result;
        }

        return array_slice($result, 0, $grandTotalPosition, true)
            + ['custom_fees' => $customFeesTotalSegment]
            + array_slice($result, $grandTotalPosition, null, true);
    }
}
