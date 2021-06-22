<?php
/**
 * Copyright © Scandiweb, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tasks\Task2\Plugin\Framework\Css\PreProcessor\Adapter\Less;

use Magento\Framework\App\State;
use Magento\Framework\Css\PreProcessor\Adapter\Less\Processor;
use Magento\Framework\Css\PreProcessor\File\Temporary;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Asset\File;
use Magento\Framework\View\Asset\ContentProcessorException;
use Magento\Framework\Phrase;
use Magento\Framework\View\Asset\Source;

/**
 * Class ProcessorPlugin
 */
class ProcessorPlugin
{
    const HEX_COLOR_CODE_PATH = "general/locale/buttons_color";
    const STORE_CODE = "admin";

    /**
     * @var ScopeConfigInterface
     */

    protected ScopeConfigInterface $_scopeConfig;

    /**
     * @var State
     */
    private State $_appState;

    /**
     * @var Source
     */
    private Source $_assetSource;

    /**
     * @var Temporary
     */
    private Temporary $_temporaryFile;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param State $appState
     * @param Source $assetSource
     * @param Temporary $temporaryFile
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        State $appState,
        Source $assetSource,
        Temporary $temporaryFile
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_appState = $appState;
        $this->_assetSource = $assetSource;
        $this->_temporaryFile = $temporaryFile;
    }

    /**
     * @throws ContentProcessorException
     */
    public function aroundProcessContent(Processor $subject, callable $proceed, File $asset)
    {
        $hexColorCode = $this->_scopeConfig->getValue(self::HEX_COLOR_CODE_PATH, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::STORE_CODE);
        if ($hexColorCode) {
            $path = $asset->getPath();
            $buttonArray = [
                'themeColor' => '#' . $hexColorCode
            ];

            try {
                $parser = new \Less_Parser(
                    [
                        'relativeUrls' => false,
                        'compress' => $this->_appState->getMode() !== State::MODE_DEVELOPER
                    ]
                );

                $content = $this->_assetSource->getContent($asset);

                if (trim($content) === '') {
                    throw new ContentProcessorException(
                        new Phrase('Compilation from source: LESS file is empty: ' . $path)
                    );
                }

                $tmpFilePath = $this->_temporaryFile->createFile($path, $content);

                gc_disable();
                $parser->parseFile($tmpFilePath, '');
                $parser->ModifyVars($buttonArray);//Modifies primary button related LESS variables
                $content = $parser->getCss();
                gc_enable();

                if (trim($content) === '') {
                    throw new ContentProcessorException(
                        new Phrase('Compilation from source: LESS file is empty: ' . $path)
                    );
                } else {
                    return $content;
                }
            } catch (\Exception $e) {
                throw new ContentProcessorException(new Phrase($e->getMessage()));
            }
        }

        $proceed($asset);
    }
}
