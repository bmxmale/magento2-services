<?php

namespace Bmxmale\Services\Service\Product\Attribute;

use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;

class SetAttributeValuesForEntityId
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
     * @param string $attributeCode
     * @param int $entityId
     * @param array $storeValues
     * @return bool
     * @throws NoSuchEntityException
     */
    public function execute(string $attributeCode, int $entityId, array $storeValues): bool
    {
        if (empty($storeValues)) {
            return false;
        }

        $attribute = $this->getAttributeDataByAttributeCode->execute($attributeCode);

        $insertData = [];
        foreach ($storeValues as $storeId => $storeValue) {
            $insertData[] = [
                'value_id' => new \Zend_Db_Expr('null'),
                'attribute_id' => $attribute[AttributeInterface::ATTRIBUTE_ID],
                'store_id' => $storeId,
                'entity_id' => $entityId,
                'value' => $storeValue
            ];
        }

        $connection = $this->resourceConnection->getConnection();
        $connection->insertOnDuplicate(
            $connection->getTableName($this->getTableNameForAttribute($attribute)),
            $insertData
        );

        return true;
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
