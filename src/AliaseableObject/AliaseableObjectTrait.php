<?php

namespace AliaseableObject;


/**
 * Trait AliaseableObjectTrait
 * @package AliasGenerator
 */
trait AliaseableObjectTrait
{
    /**
     * Получение алиаса
     *
     * @return mixed|string
     */
    final public function getAlias() {
        $existingEntry = $this->_getAliasEntry(true);

        return null !== $existingEntry ? $existingEntry->getAlias() : '-alias-not-found-';
    }

    /**
     * Удаление алиаса
     *
     * @param AliaseableObjectInterface $object
     * @return mixed|string
     */
    final public function deleteAlias() {
        $existingEntry = $this->_getAliasEntry();

        if (null !== $existingEntry) {
            return $existingEntry->delete();
        }

        return true;
    }

    /**
     * Обновление алиаса
     */
    final public function updateAlias() {
        if ($this->getIsAliasNeedRemove()) {
            $this->deleteAlias();
            return null;
        }

        $aliasEntry = $this->_getAliasEntry(true);

        $alias = $this->createSimpleAliasString();

        if (null === $aliasEntry) {
            return;
        }

        $duplicates = $aliasEntry->getDuplicates($alias);

        $alias = empty($duplicates) ? $alias : $this->createEnhancedAliasString();

        $aliasEntry->setAlias($alias);

        $aliasEntry->save();
    }

    /**
     * Создание обычного алиаса
     *
     * @return mixed
     */
    protected function createSimpleAliasString() {
        return $this->getModelName() . '-' . $this->getId();
    }

    /**
     * Создание составного алиаса
     *
     * @return string
     */
    protected function createEnhancedAliasString(): string {
        $simpleAliasString = $this->createSimpleAliasString();
        return $this->useCollisionPreventingRule($simpleAliasString);
    }

    /**
     * Нужно ли удалять алиас?
     *
     * @param AliaseableObjectInterface $object
     * @return bool
     */
    protected function getIsAliasNeedRemove(): bool {
        return false;
    }

    /**
     * Правило для предотвращения коллизии
     *
     * @param $alias
     * @return string
     */
    protected function useCollisionPreventingRule($alias): string {
        return hash('md5', serialize($this));
    }

    /**
     * Получение записи с алиасом
     *
     * @param bool $createNewIfNotFound
     * @return AliasEntryTrait|null
     */
    private function _getAliasEntry($createNewIfNotFound = false) {
        $existingEntry = $this->getAliasEntry();

        if ($createNewIfNotFound && null === $existingEntry) {
            $existingEntry = $this->createAliasEntry();
        }
        return $existingEntry;
    }

}