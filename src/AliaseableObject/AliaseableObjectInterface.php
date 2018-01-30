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
}