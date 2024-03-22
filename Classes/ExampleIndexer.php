<?php
// Set you own vendor name.
// Adjust the extension name part of the namespace to your extension key.
namespace Tpwd\KeSearchHooks;

use PDO;
use Tpwd\KeSearch\Domain\Repository\IndexRepository;
use Tpwd\KeSearch\Indexer\IndexerBase;
use Tpwd\KeSearch\Indexer\IndexerRunner;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

// Set you own class name.
class ExampleIndexer extends IndexerBase
{
    // Set a key for your indexer configuration.
    // Add this key to the $GLOBALS[...] array in Configuration/TCA/Overrides/tx_kesearch_indexerconfig.php, too!
    // It is recommended (but no must) to use the name of the table you are going to index as a key because this
    // gives you the "original row" to work with in the result list template.
    const KEY = 'tx_news_domain_model_news';

    // The database table you want to index, it can be the same as the KEY but that's no must (although recommended).
    const TABLE = 'tx_news_domain_model_news';

    /**
     * Adds the custom indexer to the TCA of indexer configurations, so that
     * it's selectable in the backend as an indexer type, when you create a
     * new indexer configuration.
     *
     * @param array $params
     * @param object $pObj
     */
    public function registerIndexerConfiguration(&$params, $pObj)
    {
        // Set a name and an icon for your indexer.
        $customIndexer = [
            '[CUSTOM] News-Indexer (ext:news)',
            ExampleIndexer::KEY,
            'EXT:ke_search_hooks/Resources/Public/Icons/customnews-indexer-icon.gif'
        ];
        $params['items'][] = $customIndexer;
    }

    /**
     * Custom indexer for ke_search.
     *
     * @param   array $indexerConfig Configuration from TYPO3 Backend.
     * @param   IndexerRunner $indexerObject Reference to indexer class.
     * @return  string Message containing indexed elements.
     */
    public function customIndexer(array $indexerConfig, IndexerRunner $indexerObject): string
    {
        if ($indexerConfig['type'] == ExampleIndexer::KEY) {
            /** @var Connection $connection */
            $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(self::TABLE);
            $queryBuilder = $connection->createQueryBuilder();

            if (empty($indexerConfig['sysfolder'])) {
                throw new \Exception('No folder specified. Please set the folder which should be indexed in the indexer configuration!');
            }

            // Handle restrictions.
            // Don't fetch hidden or deleted elements, but the elements
            // with frontend user group access restrictions or time (start / stop)
            // restrictions in order to copy those restrictions to the index.
            $queryBuilder
                ->getRestrictions()
                ->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
                ->add(GeneralUtility::makeInstance(HiddenRestriction::class));
            $where = [];

            // limit indexing of records to those in the configured folders
            $folders = GeneralUtility::trimExplode(',', htmlentities($indexerConfig['sysfolder']));
            $where[] = $queryBuilder->expr()->in( 'pid', $folders);

            // in incremental mode get only news which have been modified since last indexing time
            if ($this->indexingMode == self::INDEXING_MODE_INCREMENTAL) {
                $where[] = $queryBuilder->expr()->gte('tstamp', $this->lastRunStartTime);
            }

            $statement = $queryBuilder
                ->select('*')
                ->from(self::TABLE)
                ->where(...$where)
                ->executeQuery();

            // Loop through the records and write them to the index.
            $counter = 0;

            while ($record = $statement->fetchAssociative()) {
                // Compile the information, which should go into the index.
                // The field names depend on the table you want to index!
                $title    = strip_tags($record['title'] ?? '');
                $abstract = strip_tags($record['teaser'] ?? '');
                $content  = strip_tags($record['bodytext'] ?? '');

                $fullContent = $title . "\n" . $abstract . "\n" . $content;

                // Link to detail view
                $params = '&tx_news_pi1[news]=' . $record['uid']
                    . '&tx_news_pi1[controller]=News&tx_news_pi1[action]=detail';

                // Tags are comma-separated and wrapped with a hash (#)
                // If you use Sphinx, use "_" instead of "#" (configurable in the extension manager)
                $tags = '#example_tag_1#,#example_tag_2#';

                // Additional information
                // If you registered more additional fields, you will have to set them here, too, even if they are
                // empty! Otherwise, inserting the index record into the database will fail.
                $additionalFields = array(
                    'orig_uid' => $record['uid'],
                    'orig_pid' => $record['pid'],
                    'sortdate' => $record['datetime'],
                );

                // set custom sorting
                $additionalFields['mysorting'] = $counter;

                // You may optionally set "hidden content". Hidden content is included in the search but not shown
                // in the frontend result list.
                //$additionalFields['hidden_content'] = 'Lorem ipsum' . $record['uid'];

                // Add something to the title in this example, just to identify the records in the frontend
                $title = '[CUSTOM INDEXER] ' . $title;

                // ... and store the information in the index
                $indexerObject->storeInIndex(
                    $indexerConfig['storagepid'],   // storage PID
                    $title,                         // record title
                    ExampleIndexer::KEY,            // content type
                    $indexerConfig['targetpid'],    // target PID: where is the single view?
                    $fullContent,                   // indexed content, includes the title (linebreak after title)
                    $tags,                          // tags for faceted search
                    $params,                        // typolink params for singleview
                    $abstract,                      // abstract; shown in result list if not empty
                    $record['sys_language_uid'],    // language uid
                    $record['starttime'],           // starttime
                    $record['endtime'],             // endtime
                    $record['fe_group'],            // fe_group
                    false,                          // debug only?
                    $additionalFields               // additionalFields
                );

                $counter++;
            }

            $content = $counter . ' Elements have been indexed.';

            return $content;
        }
    return '';
    }

    /**
     * If you want to support incremental indexing, this function must exist. If incremental indexing is started,
     * it will only index new and modified records and remove hidden and deleted records afterward.
     *
     * @param   array $indexerConfig Configuration from TYPO3 Backend.
     * @param   IndexerRunner $indexerObject Reference to indexer class.
     * @return  string Message containing indexed elements.
     */
    public function startIncrementalIndexing(array $indexerConfig, IndexerRunner $indexerObject): string
    {
        $this->indexingMode = self::INDEXING_MODE_INCREMENTAL;
        $content = $this->customIndexer($indexerConfig, $indexerObject);
        $content .= $this->removeDeleted($indexerConfig, self::TABLE);
        return $content;
    }

    /**
     * Removes index records for the records which have been deleted since the last indexing.
     * Only needed in incremental indexing mode since there is a dedicated "cleanup" step in full indexing mode.
     *
     * @return string
     */
    public function removeDeleted(array $indexerConfig, string $tableName): string
    {
        /** @var IndexRepository $indexRepository */
        $indexRepository = GeneralUtility::makeInstance(IndexRepository::class);

        // get the pages from where to index the records
        $folders = $this->getPagelist(
            $indexerConfig['startingpoints_recursive'],
            $indexerConfig['sysfolder']
        );

        // Fetch all records which have been deleted or hidden since the last indexing
        $records = $this->findAllDeletedAndHiddenByPidListAndTimestampInAllLanguages(
            $tableName,
            $folders,
            $this->lastRunStartTime
        );

        // and remove the corresponding index entries
        $count = $indexRepository->deleteCorrespondingIndexRecords(self::KEY, $records, $indexerConfig);
        $message = chr(10) . 'Found ' . $count . ' deleted or hidden record(s).';
        return $message;
    }

    /**
     * @param array $pidList
     * @param int $tstamp
     * @return mixed[]
     */
    public function findAllDeletedAndHiddenByPidListAndTimestampInAllLanguages(
        string $tableName,
        array $pidList,
        int $tstamp
    )
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable($tableName);
        $queryBuilder->getRestrictions()->removeAll();
        return $queryBuilder
            ->select('*')
            ->from($tableName)
            ->orWhere(
                $queryBuilder->expr()->eq('deleted', 1),
                $queryBuilder->expr()->eq('hidden', 1)
            )
            ->andWhere(
                $queryBuilder->expr()->in('pid', $queryBuilder->createNamedParameter($pidList, Connection::PARAM_INT_ARRAY)),
                $queryBuilder->expr()->gte('tstamp', $queryBuilder->createNamedParameter($tstamp, PDO::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
