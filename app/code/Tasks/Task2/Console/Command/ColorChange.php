<?php
/**
 * Copyright © Scandiweb, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Tasks\Task2\Console\Command;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ShellInterface;
use Magento\Store\Model\ScopeInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Cache\TypeListInterface;

class ColorChange extends Command
{
    const HEX_COLOR_CODE_PATH = "general/locale/buttons_color";
    const HEX_COLOR_CODE = "hex_color_code";
    const STORE_ID = "store_id";
    const SCOPE_ID = 0;
    const STATIC_FRONTEND_STYLES_PATH = "pub/static/frontend/Scandiweb/buttons/";

    /**
     * @var StoreRepositoryInterface
     */
    protected StoreRepositoryInterface $_storeRepository;

    /**
     * @var WriterInterface
     */
    protected WriterInterface $_configWriter;

    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $_scopeConfig;

    /**
     * @var TypeListInterface
     */
    protected TypeListInterface $_cacheTypeList;

    /**
     * @var ShellInterface
     */
    private ShellInterface $_shell;

    /**
     * @var string
     */
    private string $_functionCallPath;

    /**
     *
     * @param StoreRepositoryInterface $storeRepository
     * @param WriterInterface $configWriter
     * @param ScopeConfigInterface $scopeConfig
     * @param TypeListInterface $cacheTypeList
     * @param ShellInterface $shell
     */
    public function __construct(
        StoreRepositoryInterface $storeRepository,
        WriterInterface $configWriter,
        ScopeConfigInterface $scopeConfig,
        TypeListInterface $cacheTypeList,
        ShellInterface $shell
    ) {
        $this->_storeRepository = $storeRepository;
        $this->_configWriter = $configWriter;
        $this->_scopeConfig = $scopeConfig;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_shell = $shell;
        $this->_functionCallPath =
            PHP_BINARY . ' -f ' . BP . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'magento ';

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $hexColorCode = $input->getArgument(self::HEX_COLOR_CODE);
        $storeId = $input->getArgument(self::STORE_ID);
        if (! ctype_xdigit($hexColorCode) || strlen($hexColorCode) > 6) {
            $output->writeln('Wrong color format for: ' . $hexColorCode . ' ! It should be HEX format with a maximum of six characters.');
            exit("Wrong color format!");
        }
        if (! in_array($storeId, $this->getAllStoreIds()) || $storeId == 0) {
            $output->writeln("Non-existing store ID! or 0, Please verify it. If 0, the Admin store one is not allowed.");
            exit("Wrong Store ID!");
        }

        $this->_configWriter->save(self::HEX_COLOR_CODE_PATH, $hexColorCode, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $storeLocale = $this->getStoreLocale($storeId);
        $this->removeFrontendStylesStaticFiles($storeLocale);
        $this->clearCache();

        $output->writeln("Changing buttons color to: " . $hexColorCode . " For Store ID: " . $storeId);
        $output->writeln("Store Locale is: " . $storeLocale . " For Store ID: " . $storeId . "<br/>");

        //Trigger static assets compilation and deployment for a particular Store.
        $this->deployStaticContent($output, $storeLocale);
        //Removes color value after the buttons color has been changed for a particular store, to be ready for next color-change command.
        $this->_configWriter->save(self::HEX_COLOR_CODE_PATH, null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        $this->clearCache();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("scandiweb:color-change");
        $this->setDescription("Changes the color for all of the store buttons.");
        $this->setDefinition([
            new InputArgument(self::HEX_COLOR_CODE, InputArgument::REQUIRED, "HEX Color Code for buttons."),
            new InputArgument(self::STORE_ID, InputArgument::REQUIRED, "Store ID")
        ]);

        parent::configure();
    }

    /**
     * Returns all existing Store Ids.
     *
     * @return array
     *
     */
    protected function getAllStoreIds() : array
    {
        $stores = $this->_storeRepository->getList();
        $storeIds = [];
        foreach ($stores as $store) {
            $storeIds[] = $store->getId();
        }

        return $storeIds;
    }

    /**
     * Returns Store locale code for a given Store Id.
     *
     * @param int $storeId
     * @return string
     *
     */
    public function getStoreLocale(int $storeId): string
    {
        return $this->_scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Clear cache for new saved color value to be reflected when retrieving it.
     *
     * @return void
     */
    protected function clearCache()
    {
        $this->_cacheTypeList
            ->cleanType(\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER);
        $this->_cacheTypeList
            ->cleanType(\Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER);
    }

    /**
     * Removes css styles frontend static files for a specific Store.
     *
     * @return void
     */
    protected function removeFrontendStylesStaticFiles($storeLocale)
    {
        $path = self::STATIC_FRONTEND_STYLES_PATH . $storeLocale . '/css/styles-*';

        $files = glob($path);
        foreach($files as $file) {
            if (is_file($file) ) {
                unlink($file);
            }
        }
    }

    /**
     * Deploy static content
     *
     * @param OutputInterface $output
     * @param string $storeLocale
     * @return void
     * @throws LocalizedException
     */
    protected function deployStaticContent(
        OutputInterface $output,
        string $storeLocale
    ) {
        $output->writeln('Starting deployment of static content');
        $options = '--language ' . $storeLocale . ' --area frontend --theme Scandiweb/buttons --no-images --no-fonts --no-html --no-misc';
        $cmd = $this->_functionCallPath . 'setup:static-content:deploy -f ' . $options;

        try {
            $execOutput = $this->_shell->execute($cmd);
        } catch (LocalizedException $e) {
            $output->writeln('Something went wrong while deploying static content. See the error log for details.');
            throw $e;
        }
        $output->writeln($execOutput);
        $output->writeln('Deployment of static content complete');
    }
}
