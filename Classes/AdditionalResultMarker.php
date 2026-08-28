<?php
namespace Tpwd\KeSearchHooks;

use Doctrine\DBAL\ParameterType;
use Tpwd\KeSearch\Plugins\ResultlistPlugin;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AdditionalResulMarker
 * Provides additional information in the ResultRow partial
 *
 * @package Tpwd\KeSearchHooks
 */
class AdditionalResultMarker {

    /**
     * @param array $tempMarkerArray
     * @param array $row
     * @param ResultlistPlugin $resultListPlugin
     */
    public function additionalResultMarker(array &$tempMarkerArray, array $row, ResultlistPlugin $resultListPlugin)
    {
        if ($row['type'] == 'news') {
            /** @var ConnectionPool $connectionPool */
            $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
            $queryBuilder = $connectionPool->getQueryBuilderForTable('tx_news_domain_model_news');
            $newsRecord = $queryBuilder
                ->select('author')
                ->from('tx_news_domain_model_news')
                ->where(
                    $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($row['orig_uid'], ParameterType::INTEGER))
                )
                ->executeQuery()
                ->fetchAssociative();
            if ($newsRecord) {
                $tempMarkerArray['author'] = $newsRecord['author'];
            }
        }
    }
}
