<?php

namespace Tpwd\KeSearchHooks;

use Tpwd\KeSearch\Lib\Pluginbase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * This example changes the standard "select" filter to a "radio button" filter
 */
class ExampleFilterRenderer
{
    public function customFilterRenderer(int $filterUid, array $options, Pluginbase $plugin, array &$filterData)
    {
        if ($filterData['rendertype'] == 'select') {
            /** @var StandaloneView $view */
            $view = GeneralUtility::makeInstance(StandaloneView::class);
            $view->setTemplateRootPaths([10 => 'EXT:ke_search_hooks/Resources/Private/Templates']);
            $view->setTemplate('Filters/CustomFilter');
            $view->assign('filter', $filterData);

            $filterData['rendertype'] = 'custom';
            $filterData['rawHtmlContent'] = $view->render();
        }
    }
}
