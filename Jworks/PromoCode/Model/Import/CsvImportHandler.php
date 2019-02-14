<?php
/**
 * @category    Jworks
 * @package     Jworks_PromoCode
 * @author Jitheesh V O <jitheeshvo@gmail.com>
 * @copyright Copyright (c) 2017 Jworks Digital ()
 */

namespace Jworks\PromoCode\Model\Import;

use Magento\Framework\App\ResourceConnection;

/**
 * URL rewrite CSV Import Handler
 */
class CsvImportHandler
{
    /**
     * DB connection
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * CSV Processor
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * Customer entity DB table name.
     * @var string
     */
    protected $_entityTable;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var \Magento\SalesRule\Model\Rule
     */
    protected $_rule;

    /**
     * @param \Magento\Framework\File\Csv $csvProcessor
     * @param ResourceConnection $resource
     * @param \Magento\SalesRule\Helper\Coupon $salesRuleCoupon
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\RequestInterface $request,
        ResourceConnection $resource,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime
    ) {
        $this->_objectManager = $objectManager;
        $this->csvProcessor = $csvProcessor;
        $this->_coreRegistry = $coreRegistry;
        $this->_request = $request;
        $this->_connection =
            isset($data['connection']) ?
                $data['connection'] :
                $resource->getConnection();
        $this->date = $date;
        $this->dateTime = $dateTime;
        $this->_entityTable = 'salesrule_coupon';

    }

    /**
     * Initiate rule
     * @return void
     */
    protected function _initRule()
    {
        $this->_coreRegistry->register(
            \Magento\SalesRule\Model\RegistryConstants::CURRENT_SALES_RULE,
            $this->_objectManager->create('Magento\SalesRule\Model\Rule')
        );
        $id = (int)$this->_request->getParam('id');

        if (!$id && $this->_request->getParam('rule_id')) {
            $id = (int)$this->_request->getParam('rule_id');
        }

        if ($id) {
            $this->_rule = $this->_coreRegistry->registry(\Magento\SalesRule\Model\RegistryConstants::CURRENT_SALES_RULE)->load($id);

        }
    }

    /**
     * Import Tax Rates from CSV file
     * @param array $file file info retrieved from $_FILES array
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importFromCsvFile($file)
    {
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }
        $this->_initRule();
        $rawData = $this->csvProcessor->getData($file['tmp_name']);

        foreach ($rawData as $rowIndex => $dataRow) {
            $this->_generateCoupon(current($dataRow));
        }
    }

    protected function _generateCoupon($coupon)
    {
        $this->_connection->insertOnDuplicate(
            $this->_entityTable,
            array(
                'rule_id' => $this->_rule->getId(),
                'code' => $coupon,
                'usage_limit' => 0,
                'created_at' => $this->dateTime->formatDate($this->date->gmtTimestamp()),
                'usage_per_customer' => $this->_rule->getUsesPerCustomer(),
                'times_used' => 0,
                'type' => 1,
            ),
            array()
        );
    }

}
