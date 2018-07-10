<?php

namespace AliaseableObject;

interface AliaseableObjectInterface
{
    /**
     * @return string
     */
    public function getModelName(): string;

    /**
     * @return string|int
     */
    public function getId();

    public function getAliasEntry();

    public function createAliasEntry(string $alias = null);
}