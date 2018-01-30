# ro-aliaseable-object

Присвоение алиасов объектам

Позволяет:
- создавать алиасы для объектов ORM
- предотвращать появление дублей
- удалять алиасы при определённых условиях

Для работы необходимо:
- реализовать методы интерфейсов
- заюзать trait для хранилища объектов и алиасов

Вариант использования:

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

```php
class TGeoObject extends GeoObject implements AliaseableObjectInterface
{
    use AliaseableGeoTrait;
    
    //...
}    
```

```php
$geoObject->updateAlias();
```
