<?php
/**
 * @author Igor Rain <igor.rain@icloud.com>
 * See LICENCE for license details.
 */

namespace IgorRain\CodeGenerator\Model\Generator\Etc;

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

    public function generateTable(string $fileName, ModelContext $context): void
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

    protected function findTable(DOMElement $schema, string $name): ?DOMElement
    {
        return $this->findChildByTagNameAndAttributeValue($schema, 'table', 'name', $name);
    }

    protected function findColumn(DOMElement $table, string $name): ?DOMElement
    {
        return $this->findChildByTagNameAndAttributeValue($table, 'column', 'name', $name);
    }

    protected function findConstraintByReferenceId(DOMElement $table, string $referenceId): ?DOMElement
    {
        return $this->findChildByTagNameAndAttributeValue($table, 'constraint', 'referenceId', $referenceId);
    }

    protected function getSource(string $fileName): XmlSource
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
