<?php

namespace AliaseableObject;

abstract class AliasEntry implements AliasEntryInterface
{
    public static function getEntryOrCreateNew($modelName, $modelId) {
        $aliasEntry = self::getEntry($modelName, $modelId);
        if (null === $aliasEntry) {
            $aliasEntry = self::createEntry($modelName, $modelId);
        }
        return $aliasEntry;
    }
}
