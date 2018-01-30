# ro-aliaseable-object

## Присвоение алиасов объектам

__php7 required__

Позволяет:
- создавать алиасы для объектов ORM
- предотвращать появление дублей
- удалять алиасы при определённых условиях

Для работы необходимо:
- реализовать методы интерфейсов
- заюзать trait для хранилища объектов и алиасов

## Вариант использования

Расширение базового trait для получения названия модели
```php
use AliaseableObject\AliaseableObjectTrait;

trait AliaseableBaseObjectTrait
{
    use AliaseableObjectTrait;

    public function getModelName(): string
    {
        return static::getMetadata()['table'];
    }
}
```

Расширение trait для определения способа формирования алиаса для объектов типа Geo
```php
trait AliaseableGeoTrait
{
    use AliaseableBaseObjectTrait;

    protected function createSimpleAliasString()
    {
        $name = $this->getName();
        $enhancedTranslit = new EnhancedTranslit();
        return $enhancedTranslit->filter($name);
    }

    protected function createEnhancedAliasString()
    {
        $alias = $this->createSimpleAliasString();
        $geo = $this->getParent();
        $alias = self::getAliasFor($geo) . '-' . $alias;
        return $alias;
    }
}
```

Использование AliaseableObject в модели
```php
class TGeoObject extends GeoObject implements AliaseableObjectInterface
{
    use AliaseableGeoTrait;
    
    //...
}    
```

Расширение модели ORM хранилища алиасов
```php
    /**
     * Сохраняет объект в БД.
     */
    public function save()
    {
        $uid = Vault::getUser()->getId();
        $time = date('Y-m-d\TH:iP');

        if ($this->isPersistent()) {
            $this->modified_at = $time;
            $this->modified_by = $uid;
        } else {
            $this->created_at = $this->modified_at = $time;
            $this->created_by = $this->modified_by = $uid;
        }

        return parent::save();
    }

    /**
     * @param string $modelName
     * @param $modelId
     * @return AliasEntryTrait
     */
    public static function getEntry(string $modelName, $modelId)
    {
        return self::Mapper()->findOne(['model' => $modelName, 'model_id' => $modelId]);
    }

    /**
     * @param string $modelName
     * @param $modelId
     * @param null|string $alias
     * @return AliasEntryTrait
     */
    public static function createEntry(string $modelName, $modelId, $alias = null)
    {
        $instance = new self();
        $instance->model = $modelName;
        $instance->model_id = $modelName;
        $instance->alias = $alias;
        return $instance;
    }

    /**
     * @return AliasEntryTrait[]
     */
    public function getDuplicates()
    {
        self::Mapper()->findAll(['model' => $this->model, 'alias' => $this->alias]);
    }

    /**
     * @return mixed
     */
    public function getModelId()
    {
        return $this->model_id;
    }

    /**
     * @param mixed $modelId
     */
    public function setModelId($modelId)
    {
        $this->model_id = $modelId;
    }

    /**
     * @return string
     */
    public function getModelName(): string
    {
        return $this->model;
    }

    /**
     * @param string $modelName
     */
    public function setModelName(string $modelName)
    {
        $this->model = $modelName;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias(string $alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return null|AliaseableObjectInterface|AliaseableObjectTrait
     */
    public function getObject()
    {
        $className = [
                'tgeo' => 'TGeo',
            ][$this->model] ?? null;

        if (null === $className) {
            return null;
        }

        return $className::Mapper()->find($this->model_id);
    }
```

Обновление алиаса для объекта
```php
$geoObject->updateAlias();
```
