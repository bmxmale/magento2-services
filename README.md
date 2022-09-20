```
__/\\\\\\\\\\\\\____/\\\\____________/\\\\__/\\\_______/\\\__/\\\\____________/\\\\_____/\\\\\\\\\_____/\\\______________/\\\\\\\\\\\\\\\_        
 _\/\\\/////////\\\_\/\\\\\\________/\\\\\\_\///\\\___/\\\/__\/\\\\\\________/\\\\\\___/\\\\\\\\\\\\\__\/\\\_____________\/\\\///////////__       
  _\/\\\_______\/\\\_\/\\\//\\\____/\\\//\\\___\///\\\\\\/____\/\\\//\\\____/\\\//\\\__/\\\/////////\\\_\/\\\_____________\/\\\_____________      
   _\/\\\\\\\\\\\\\\__\/\\\\///\\\/\\\/_\/\\\_____\//\\\\______\/\\\\///\\\/\\\/_\/\\\_\/\\\_______\/\\\_\/\\\_____________\/\\\\\\\\\\\_____     
    _\/\\\/////////\\\_\/\\\__\///\\\/___\/\\\______\/\\\\______\/\\\__\///\\\/___\/\\\_\/\\\\\\\\\\\\\\\_\/\\\_____________\/\\\///////______    
     _\/\\\_______\/\\\_\/\\\____\///_____\/\\\______/\\\\\\_____\/\\\____\///_____\/\\\_\/\\\/////////\\\_\/\\\_____________\/\\\_____________   
      _\/\\\_______\/\\\_\/\\\_____________\/\\\____/\\\////\\\___\/\\\_____________\/\\\_\/\\\_______\/\\\_\/\\\_____________\/\\\_____________  
       _\/\\\\\\\\\\\\\/__\/\\\_____________\/\\\__/\\\/___\///\\\_\/\\\_____________\/\\\_\/\\\_______\/\\\_\/\\\\\\\\\\\\\\\_\/\\\\\\\\\\\\\\\_ 
        _\/////////////____\///______________\///__\///_______\///__\///______________\///__\///________\///__\///////////////__\///////////////__
```

### Bmxmale_Services

Magento 2 module with some useful services

#### Services

##### Product / Attribute

> ##### GetAttributeDataByAttributeCode

---

Service select product attributes except `static` backend type. Throws `NoSuchEntityException` if specified attribute not exist.

```php
use Bmxmale\Services\Service\Product\Attribute\GetAttributeDataByAttributeCode;
...
public function __construct(
    private GetAttributeDataByAttributeCode $getAttributeDataByAttributeCode,
) {
}

public function someMethod()
{
    $attributeData = $this->getAttributeDataByAttributeCode->execute('ld_id_ean');

    // ^ array:4 [
    //   "attribute_code" => "ld_id_ean"
    //   "attribute_id" => "583"
    //   "backend_type" => "varchar"
    //   "default_value" => null
    // ]
}
```

 With `di.xml` you are allowed to extend additional attribute columns from `eav_attribute` table. Just override `$additionalAttributeColumns` argument on service construct

```xml
    <virtualType name="ExtendedGetAttributeDataByAttributeCode" type="Bmxmale\Services\Service\Product\Attribute\GetAttributeDataByAttributeCode">
        <arguments>
            <argument name="additionalAttributeColumns" xsi:type="array">
                <item name="default_value" xsi:type="string">default_value</item>
                <item name="frontend_label" xsi:type="string">frontend_label</item>
            </argument>
        </arguments>
    </virtualType>

    <type name="Qwerty\Developer\Console\Command\Sample">
        <arguments>
            <argument name="getAttributeDataByAttributeCode" xsi:type="object">ExtendedGetAttributeDataByAttributeCode</argument>
        </arguments>
    </type>
```
```php
public function someMethod()
{
    $attributeData = $this->getAttributeDataByAttributeCode->execute('ld_id_ean');

    // ^ array:4 [
    //   "attribute_code" => "ld_id_ean"
    //   "attribute_id" => "583"
    //   "backend_type" => "varchar"
    //   "default_value" => null
    //   "frontend_label" => "EAN"
    // ]
}
```

<br />


> ##### GetAttributeValuesForEntityId

---

Service select product attribute values. Return `[store_id => value]` entries. Return empty array if no values.
Throws `NoSuchEntityException` if specified attribute not exist.

```php
use Bmxmale\Services\Service\Product\Attribute\GetAttributeValuesForEntityId;
...
public function __construct(
    private GetAttributeValuesForEntityId $getAttributeValuesForEntityId,
) {
}

public function someMethod()
{
    $attributeValues = $this->getAttributeValuesForEntityId->execute(
        attributeCode: 'ld_id_ean',
        entityId: 19011
    );

    //    # store_id => value     
    //    ^ array:4 [
    //      0 => "5900988500835"
    //      1 => "5900988500835"
    //      4 => "5900988500835"
    //      12 => "5900988500835"
    //    ]
}
```
