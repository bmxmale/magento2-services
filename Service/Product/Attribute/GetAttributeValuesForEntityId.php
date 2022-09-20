<?php

namespace Bmxmale\Services\Service\Product\Attribute;

use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\App\ResourceConnection;

class GetAttributeValuesForEntityId
{
    /**
     * @param ResourceConnection $resourceConnection
     * @param GetAttributeDataByAttributeCode $getAttributeDataByAttributeCode
     * @param array $attributeValues
     * @phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
     */
    public function __construct(
        private ResourceConnection $resourceConnection,
        private GetAttributeDataByAttributeCode $getAttributeDataByAttributeCode,
        private array $attributeValues = []
    ) {
    }

    /**
     * @param int $entityId
     * @param string $attributeCode
     * @return array
     */
    public function execute(string $attributeCode, int $entityId): array
    {
        if (isset($this->attributeValues[$entityId]) && isset($this->attributeValues[$entityId][$attributeCode])) {
            return $this->attributeValues[$entityId][$attributeCode];
        }

        $attribute = $this->getAttributeDataByAttributeCode->execute($attributeCode);

        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                ['v' => $connection->getTableName($this->getTableNameForAttribute($attribute))],
                ['store_id', 'value']
            )
            ->where('entity_id = ?', $entityId)
            ->where(AttributeInterface::ATTRIBUTE_ID . ' = ?', $attribute[AttributeInterface::ATTRIBUTE_ID]);

        $valuesData = $connection->fetchAll($select);

        if (empty($valuesData)) {
            $this->attributeValues[$entityId][$attributeCode] = [];

            return [];
        }

        foreach ($valuesData as $valueData) {
            $this->attributeValues[$entityId][$attributeCode][$valueData['store_id']] = $valueData['value'];
        }

        return $this->attributeValues[$entityId][$attributeCode];
    }

    /**
     * @param array $attribute
     * @return string
     */
    private function getTableNameForAttribute(array $attribute): string
    {
        return sprintf('catalog_product_entity_%s', $attribute[AttributeInterface::BACKEND_TYPE]);
    }
}
