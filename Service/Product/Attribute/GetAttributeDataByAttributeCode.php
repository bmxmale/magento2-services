<?php

namespace Bmxmale\Services\Service\Product\Attribute;

use Magento\Catalog\Model\Product;
use Magento\Eav\Api\Data\AttributeDefaultValueInterface;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;

class GetAttributeDataByAttributeCode
{
    /**
     * @param ResourceConnection $resourceConnection
     * @param array $attributeColumns
     * @param array $additionalAttributeColumns
     * @param array $attributes
     * @param int $entityTypeId
     * @phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
     */
    public function __construct(
        private ResourceConnection $resourceConnection,
        private readonly array $attributeColumns = [
            AttributeInterface::ATTRIBUTE_CODE,
            AttributeInterface::ATTRIBUTE_ID,
            AttributeInterface::BACKEND_TYPE
        ],
        private array $additionalAttributeColumns = [
            AttributeDefaultValueInterface::DEFAULT_VALUE
        ],
        private array $attributes = [],
        private int $entityTypeId = 0
    ) {
    }

    /**
     * @return int
     */
    private function getProductTypeId(): int
    {
        if (0 !== $this->entityTypeId) {
            return $this->entityTypeId;
        }

        $select = $this->resourceConnection->getConnection()
            ->select()
            ->from('eav_entity_type', 'entity_type_id')
            ->where('entity_type_code = ?', Product::ENTITY);

        $this->entityTypeId = (int)$this->resourceConnection->getConnection()->fetchOne($select);

        return $this->entityTypeId;
    }

    /**
     * @param string $attributeCode
     * @return array|null
     */
    public function execute(string $attributeCode): ?array
    {
        if (isset($this->attributes[$attributeCode])) {
            return $this->attributes[$attributeCode];
        }

        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                ['e' => $connection->getTableName('eav_attribute')],
                [...$this->attributeColumns, ...$this->additionalAttributeColumns]
            )
            ->where(AttributeInterface::ENTITY_TYPE_ID . ' = ?', $this->getProductTypeId())
            ->where(AttributeInterface::BACKEND_TYPE . ' != ?', 'static')
            ->order(AttributeInterface::ATTRIBUTE_CODE . ' ASC');

        $attributesData = $connection->fetchAll($select);
        foreach ($attributesData as $attributeData) {
            $this->attributes[$attributeData[AttributeInterface::ATTRIBUTE_CODE]] = $attributeData;
        }

        if (!isset($this->attributes[$attributeCode])) {
            throw new NoSuchEntityException(__('Attribute "%1" not exist', $attributeCode));
        }

        return $this->attributes[$attributeCode];
    }
}
