<?php
namespace Tpwd\KeSearchHooks;

/**
 * Class AdditionalIndexerFields
 * @package Tpwd\KeSearchHooks
 */
class AdditionalIndexerFields {
    public function registerAdditionalFields(&$additionalFields) {
        $additionalFields[] = 'mysorting';
    }
}