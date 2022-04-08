<?php

namespace Tpwd\KeSearchHooks;

use Tpwd\KeSearch\Indexer\IndexerBase;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This is an example for how to change the values of the index record before it is stored into the index.
 * In this particular example we add the file metadata keywords to the indexed content (that field is added
 * by EXT:filemetadata).
 */
class modifyFieldValuesBeforeStoring
{
    public function modifyFieldValuesBeforeStoring(array $indexerConfig, array $fieldValues)
    {
        if (
            ExtensionManagementUtility::isLoaded('filemetadata')
            && (substr($fieldValues['type'], 0, 4) == 'file')
            && isset($fieldValues['orig_uid'])
        ) {
            $resourceFactory = GeneralUtility::makeInstance(ResourceFactory::class);
            try {
                $file = $resourceFactory->getFileObject($fieldValues['orig_uid']);
                $metadata = $file->getMetaData();
                if (isset($metadata['keywords']) && !empty($metadata['keywords'])) {
                    $fieldValues['content'] = $metadata['keywords'] . IndexerBase::METADATASEPARATOR . $fieldValues['content'];
                }
            } catch (FileDoesNotExistException $e) {
            }
        }
        return $fieldValues;
    }
}