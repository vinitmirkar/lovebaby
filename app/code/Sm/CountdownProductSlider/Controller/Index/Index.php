<?php
/*------------------------------------------------------------------------
# SM Count Down Product Slider  - Version 1.1.0
# Copyright (c) 2016 YouTech Company. All Rights Reserved.
# @license - Copyrighted Commercial Software
# Author: YouTech Company
# Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
namespace Sm\CountdownProductSlider\Controller\Index;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;


class Index extends \Magento\Framework\App\Action\Action {
	/** @var  \Magento\Framework\View\Result\Page */
	protected $resultPageFactory;
	protected $jsonEncoder;
	protected $_layout;
	/**      * @param \Magento\Framework\App\Action\Context $context      */
	public function __construct(
		Context $context, 
		PageFactory $resultPageFactory,
		\Magento\Framework\Json\EncoderInterface $jsonEncoder,
		\Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\View\LayoutFactory $layoutFactory){
		$this->resultPageFactory = $resultPageFactory;
		$this->jsonEncoder = $jsonEncoder;
		$this->_layout = $layout;
		parent::__construct($context);
	}

	/**
	 * Blog Index, shows a list of recent blog posts.
	 *
	 * @return \Magento\Framework\View\Result\PageFactory
	 */
	public function execute()
	{
		$isAjax = $this->getRequest()->isAjax();
		if ($isAjax){
			$layout =  $this->_layout;
			$layout->getUpdate()->load(['countdownproductslider_index_ajax']);
			$layout->generateXml();
            $output = $layout->getOutput();
            $this->getResponse()->setHeader('Content-type', 'application/json');
			die($this->jsonEncoder->encode(['items_markup' => $output]));
        }
		$resultPage = $this->resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->prepend(__('SM Countdown Product Slider'));
		return $resultPage;
	}
}