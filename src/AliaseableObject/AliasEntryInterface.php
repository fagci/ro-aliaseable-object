<?php

namespace AliaseableObject;

interface AliasEntryInterface
{
    /**
     * @param string $modelName
     * @param $modelId
     * @return AliasEntryTrait
     */
    public static function getEntry(string $modelName, $modelId);

    /**
     * @param string $modelName
     * @param $modelId
     * @param null|string $alias
     * @return AliasEntryTrait
     */
    public static function createEntry(string $modelName, $modelId, $alias = null);

    public function delete();

    public function save();

    /**
     * @return AliasEntryTrait[]
     */
    public function getDuplicates($alias = null);

    /**
     * @return null|AliaseableObjectInterface|AliaseableObjectTrait
     */
    public function getObject();

    /**
     * @return mixed
     */
    public function getModelId();

    /**
     * @param mixed $modelId
     */
    public function setModelId($modelId);

    /**
     * @return string
     */
    public function getModelName(): string;

    /**
     * @param string $modelName
     */
    public function setModelName(string $modelName);

    /**
     * @return string
     */
    public function getAlias(): string;

    /**
     * @param string $alias
     */
    public function setAlias(string $alias);
}