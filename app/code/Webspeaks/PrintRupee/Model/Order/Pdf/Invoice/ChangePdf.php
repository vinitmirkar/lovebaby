<?php

namespace Webspeaks\PrintRupee\Model\Order\Pdf\Invoice;

use Magento\Sales\Model\Order\Pdf\Invoice;

class ChangePdf extends Invoice {

    /**
     * Set font as regular
     *
     * @param  \Zend_Pdf_Page $object
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($object, $size = 7)
    {
        if (!$this->isEnabled()) {
            return parent::_setFontRegular($object, $size);
        }

        $font = \Zend_Pdf_Font::fontWithPath(
            $this->getFontsDir() . ('dejavu-sans/DejaVuSansCondensed.ttf')
        );
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as bold
     *
     * @param  \Zend_Pdf_Page $object
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($object, $size = 7)
    {
        if (!$this->isEnabled()) {
            return parent::_setFontBold($object, $size);
        }

        $font = \Zend_Pdf_Font::fontWithPath(
            $this->getFontsDir() . ('dejavu-sans/DejaVuSansCondensed-Bold.ttf')
        );
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as italic
     *
     * @param  \Zend_Pdf_Page $object
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    protected function _setFontItalic($object, $size = 7)
    {
        if (!$this->isEnabled()) {
            return parent::_setFontItalic($object, $size);
        }

        $font = \Zend_Pdf_Font::fontWithPath(
            $this->getFontsDir() . ('dejavu-sans/DejaVuSans-Oblique.ttf')
        );
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Get fonts directory path
     *
     * @return String
     */
    protected function getFontsDir()
    {
        $path = 'app/code/Webspeaks/PrintRupee/view/adminhtml/web/fonts/';
        return $this->_rootDirectory->getAbsolutePath($path);
    }

    /**
     * Check if module is enabled
     *
     * @return String
     */
    protected function isEnabled()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create('Webspeaks\PrintRupee\Helper\Data');
        return $helper->isEnabled();
    }
}
