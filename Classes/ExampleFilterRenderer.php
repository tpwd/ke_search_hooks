<?php

namespace Tpwd\KeSearchHooks;

use Tpwd\KeSearch\Lib\Pluginbase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExampleFilterRenderer
{
    public function customFilterRenderer(int $filterUid, array $options, Pluginbase $plugin, array &$filterData)
    {
        if ($filterData['rendertype'] == 'select') {
            /** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
            $view = GeneralUtility::makeInstance(\TYPO3\CMS\Fluid\View\StandaloneView::class);
            $view->setTemplateRootPaths([10 => 'EXT:ke_search_hooks/Resources/Private/Templates']);
            $view->setTemplate('Filters/CustomFilter');
            $view->assign('filter', $filterData);

            $filterData['rendertype'] = 'custom';
            $filterData['rawHtmlContent'] = $view->render();
        }
    }
}