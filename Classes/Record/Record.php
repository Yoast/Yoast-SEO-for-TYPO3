<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Record;

class Record
{
    protected const DEFAULT_TITLE_FIELD = 'title';

    protected string $tableName = '';

    protected bool $defaultSeoFields = true;

    protected bool $yoastSeoFields = true;

    protected string $types = '';

    protected string $titleField = 'title';

    protected string $descriptionField = 'description';

    protected bool $addDescriptionField = false;

    protected string $fieldsPosition = 'after:' . self::DEFAULT_TITLE_FIELD;

    protected array $overrideTca = [];

    protected array $getParameters = [];

    protected ?int $recordUid = null;

    protected array $recordData = [];

    protected bool $generatePageTitle = true;

    protected bool $generateMetaTags = true;

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function setTableName(string $tableName): self
    {
        $this->tableName = $tableName;
        return $this;
    }

    public function hasDefaultSeoFields(): bool
    {
        return $this->defaultSeoFields;
    }

    public function setDefaultSeoFields(bool $defaultSeoFields): Record
    {
        $this->defaultSeoFields = $defaultSeoFields;
        return $this;
    }

    public function hasYoastSeoFields(): bool
    {
        return $this->yoastSeoFields;
    }

    public function setYoastSeoFields(bool $yoastSeoFields): Record
    {
        $this->yoastSeoFields = $yoastSeoFields;
        return $this;
    }

    public function getTypes(): string
    {
        return $this->types;
    }

    public function setTypes(string $types): Record
    {
        $this->types = $types;
        return $this;
    }

    public function getTitleField(): string
    {
        return $this->titleField;
    }

    public function setTitleField(string $titleField): Record
    {
        $this->titleField = $titleField;
        return $this;
    }

    public function getDescriptionField(): string
    {
        return $this->descriptionField;
    }

    public function setDescriptionField(string $descriptionField): Record
    {
        $this->descriptionField = $descriptionField;
        return $this;
    }

    public function shouldAddDescriptionField(): bool
    {
        return $this->addDescriptionField;
    }

    public function setAddDescriptionField(bool $addDescriptionField): Record
    {
        $this->addDescriptionField = $addDescriptionField;
        return $this;
    }

    public function getFieldsPosition(): string
    {
        return $this->fieldsPosition;
    }

    public function setFieldsPosition(string $fieldsPosition): Record
    {
        $this->fieldsPosition = $fieldsPosition;
        return $this;
    }

    public function getOverrideTca(): array
    {
        return $this->overrideTca;
    }

    public function setOverrideTca(array $overrideTca): Record
    {
        $this->overrideTca = $overrideTca;
        return $this;
    }

    public function getGetParameters(): array
    {
        return $this->getParameters;
    }

    public function setGetParameters(array $getParameters): Record
    {
        $this->getParameters = $getParameters;
        return $this;
    }

    public function getRecordUid(): ?int
    {
        return $this->recordUid;
    }

    public function setRecordUid(int $recordUid): Record
    {
        $this->recordUid = $recordUid;
        return $this;
    }

    public function getRecordData(): array
    {
        return $this->recordData;
    }

    public function setRecordData(array $recordData): Record
    {
        $this->recordData = $recordData;
        return $this;
    }

    public function shouldGeneratePageTitle(): bool
    {
        return $this->generatePageTitle;
    }

    public function setGeneratePageTitle(bool $generatePageTitle): Record
    {
        $this->generatePageTitle = $generatePageTitle;
        return $this;
    }

    public function shouldGenerateMetaTags(): bool
    {
        return $this->generateMetaTags;
    }

    public function setGenerateMetaTags(bool $generateMetaTags): Record
    {
        $this->generateMetaTags = $generateMetaTags;
        return $this;
    }
}
