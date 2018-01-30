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

        if (null === ($aliasEntry = $this->getAliasEntry())) {
            $aliasEntry = $this->createAliasEntry();
            $aliasEntry->setAlias($this->createSimpleAliasString());
        }

        $isDuplicatesExists = !empty($aliasEntry->getDuplicates());

        if ($isDuplicatesExists) {
            $aliasEntry->setAlias($this->createEnhancedAliasString());
        }

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