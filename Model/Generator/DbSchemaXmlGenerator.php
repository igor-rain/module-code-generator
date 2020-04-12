<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator;

use DOMElement;
use IgorRain\CodeGenerator\Model\Context\ModelContext;
use IgorRain\CodeGenerator\Model\Context\ModelFieldContext;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\SourceFactory;
use IgorRain\CodeGenerator\Model\ResourceModel\Source\XmlSource;

class DbSchemaXmlGenerator extends AbstractXmlGenerator
{
    /**
     * @var SourceFactory
     */
    private $sourceFactory;

    public function __construct(SourceFactory $sourceFactory)
    {
        $this->sourceFactory = $sourceFactory;
    }

    public function generateTable($fileName, ModelContext $context): void
    {
        $source = $this->getSource($fileName);

        $doc = $source->getDocument();

        $table = $this->getOrCreateTable($doc->documentElement, $context);

        $this->getOrCreatePrimaryKeyColumn($table, $context);
        foreach ($context->getFields() as $field) {
            if (!$field->getIsPrimary()) {
                $this->getOrCreateColumn($table, $field);
            }
        }
        $this->getOrCreatePrimaryKeyConstraint($table, $context);

        $source->save();
    }

    /**
     * @return DOMElement
     */
    protected function getOrCreateTable(DOMElement $schema, ModelContext $context): DOMElement
    {
        $table = $this->findTable($schema, $context->getTableName());
        if (!$table) {
            $table = $schema->ownerDocument->createElement('table');
            $table->setAttribute('name', $context->getTableName());
            $table->setAttribute('resource', 'default');
            $table->setAttribute('engine', 'innodb');
            $table->setAttribute('comment', $context->getTableDescription());
            $schema->appendChild($table);
        }

        return $table;
    }

    /**
     * @return DOMElement
     */
    protected function getOrCreateColumn(DOMElement $table, ModelFieldContext $context): DOMElement
    {
        $column = $this->findColumn($table, $context->getName());
        if (!$column) {
            $column = $table->ownerDocument->createElement('column');
            $column->setAttribute('xsi:type', 'varchar');
            $column->setAttribute('name', $context->getName());
            $column->setAttribute('nullable', 'true');
            $column->setAttribute('length', '255');
            $column->setAttribute('comment', $context->getDescriptionInTable());

            $lastColumn = $this->getLastChildByTagName($table, 'column');
            if ($lastColumn) {
                $table->insertBefore($column, $lastColumn->nextSibling);
            } else {
                $table->insertBefore($column, $table->firstChild);
            }
        }

        return $column;
    }

    /**
     * @return DOMElement
     */
    protected function getOrCreatePrimaryKeyColumn(DOMElement $table, ModelContext $context): DOMElement
    {
        $column = $this->findColumn($table, $context->getPrimaryKey()->getName());
        if (!$column) {
            $column = $table->ownerDocument->createElement('column');
            $column->setAttribute('xsi:type', 'int');
            $column->setAttribute('name', $context->getPrimaryKey()->getName());
            $column->setAttribute('padding', '10');
            $column->setAttribute('unsigned', 'true');
            $column->setAttribute('nullable', 'false');
            $column->setAttribute('identity', 'true');
            $column->setAttribute('comment', $context->getPrimaryKey()->getDescriptionInTable());
            $table->insertBefore($column, $table->firstChild);
        }

        return $column;
    }

    /**
     * @return DOMElement
     */
    protected function getOrCreatePrimaryKeyConstraint(DOMElement $table, ModelContext $context): DOMElement
    {
        $constraint = $this->findConstraintByReferenceId($table, 'PRIMARY');
        if (!$constraint) {
            $constraint = $table->ownerDocument->createElement('constraint');
            $constraint->setAttribute('xsi:type', 'primary');
            $constraint->setAttribute('referenceId', 'PRIMARY');
            $table->appendChild($constraint);

            $column = $table->ownerDocument->createElement('column');
            $column->setAttribute('name', $context->getPrimaryKey()->getName());
            $constraint->appendChild($column);
        }

        return $constraint;
    }

    /**
     * @param string $name
     *
     * @return null|DOMElement
     */
    protected function findTable(DOMElement $schema, $name): ?DOMElement
    {
        return $this->findChildByTagNameAndAttributeValue($schema, 'table', 'name', $name);
    }

    /**
     * @param $name
     *
     * @return null|DOMElement
     */
    protected function findColumn(DOMElement $table, $name): ?DOMElement
    {
        return $this->findChildByTagNameAndAttributeValue($table, 'column', 'name', $name);
    }

    /**
     * @param string $referenceId
     *
     * @return null|DOMElement
     */
    protected function findConstraintByReferenceId(DOMElement $table, $referenceId): ?DOMElement
    {
        return $this->findChildByTagNameAndAttributeValue($table, 'constraint', 'referenceId', $referenceId);
    }

    /**
     * @param $fileName
     *
     * @return XmlSource
     */
    protected function getSource($fileName): XmlSource
    {
        /** @var XmlSource $source */
        $source = $this->sourceFactory->create($fileName, 'xml');

        if ($source->exists()) {
            $source->load();
        } else {
            $source->getDocument()->loadXML($this->getEmptyTemplate());
        }

        return $source;
    }

    protected function getEmptyTemplate(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<schema xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:Setup/Declaration/Schema/etc/schema.xsd"></schema>';
    }
}
