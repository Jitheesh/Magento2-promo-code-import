<?php
/**
 * @category    Jworks
 * @package     Jworks_PromoCode
 * @author Jitheesh V O <jitheeshvo@gmail.com>
 * @copyright Copyright (c) 2017 Jworks Digital ()
 */

namespace Jworks\PromoCode\Controller\Adminhtml\Import;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class ImportPost
 * @package Jworks\PromoCode\Controller\Adminhtml\Import
 */
class ImportPost extends \Jworks\PromoCode\Controller\Adminhtml\Import
{
    /**
     * import action from import/export tax
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($this->getRequest()->isPost() && !empty($_FILES['import_promocode_file']['tmp_name'])) {
            try {
                /** @var $importHandler \Jworks\UrlRewriteImport\Model\UrlRewrite\CsvImportHandler */
                $importHandler = $this->_objectManager->create('Jworks\PromoCode\Model\Import\CsvImportHandler');
                $importHandler->importFromCsvFile($this->getRequest()->getFiles('import_promocode_file'));

                $this->messageManager->addSuccess(__('The promo codes has been imported.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__($e->getMessage()));
            }
        } else {
            $this->messageManager->addError(__('Invalid file upload attempt'));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());

        return $resultRedirect;
    }
}
