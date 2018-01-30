# ro-aliaseable-object

## Присвоение алиасов объектам

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

Обновление алиаса для объекта
```php
$geoObject->updateAlias();
```
