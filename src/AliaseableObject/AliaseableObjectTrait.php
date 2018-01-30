<?php

namespace AliaseableObject;


/**
 * Trait AliaseableObjectTrait
 * @package AliasGenerator
 *
 */
trait AliaseableObjectTrait
{
    /**
     * Получение алиаса
     *
     * @return mixed|string
     */
    final public function getAlias() {
        $existingEntry = AliasEntryTrait::getEntryOrCreateNew($this->getModelName(), $this->getId());

        return null !== $existingEntry ? $existingEntry->getAlias() : 'e2';
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

        $this->_updateAliasPreventDuplicates();
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
     * Обновить алиас с предотвращением дубликатов если они имеются
     */
    private function _updateAliasPreventDuplicates() {
        $alias      = $this->createSimpleAliasString();
        $duplicates = $this->_getAliasEntry(true)->getDuplicates();

        $isWithoutDuplicates = \count($duplicates) === 0;

        if (!$isWithoutDuplicates) {
            $alias = $this->createEnhancedAliasString();
        }

        $this->setAlias($alias);

        $this->save();
    }

    /**
     * Получение записи с алиасом
     *
     * @param bool $createNewIfNotFound
     * @return AliasEntryTrait|null
     */
    private function _getAliasEntry($createNewIfNotFound = false) {
        $modelName = $this->getModelName();
        $modelId   = $this->getId();

        $existingEntry = self::getEntry($modelName, $modelId);

        if ($createNewIfNotFound && null === $existingEntry) {
            $existingEntry = $this->updateAlias();
        }
        return $existingEntry;
    }

}