<?php
/**
 * promo Code Import admin controller
 * @category    Jworks
 * @package     Jworks_PromoCode
 * @author Jitheesh V O <jitheeshvo@gmail.com>
 * @copyright Copyright (c) 2017 Jworks Digital ()
 */

namespace Jworks\PromoCode\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Class Import
 * @package Jworks\PromoCode\Controller\Adminhtml
 */
abstract class Import extends Action
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

}
