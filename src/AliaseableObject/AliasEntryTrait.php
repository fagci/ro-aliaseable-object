<?php

namespace AliaseableObject;

trait AliasEntryTrait
{
    public static function getEntryOrCreateNew($modelName, $modelId) {
        $aliasEntry = self::getEntry($modelName, $modelId);
        if (null === $aliasEntry) {
            $aliasEntry = self::createEntry($modelName, $modelId);
        }
        return $aliasEntry;
    }
}
