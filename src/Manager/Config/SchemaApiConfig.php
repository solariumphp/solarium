<?php

declare(strict_types=1);

namespace Solarium\Manager\Config;

use Solarium\Manager\Contract\ApiV2ConfigurationInterface;

/**
 * Schema Api Config.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class SchemaApiConfig implements ApiV2ConfigurationInterface
{
    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#add-a-new-field
     */
    public const ADD_FIELD = 'add-field';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#delete-a-field
     */
    public const DELETE_FIELD = 'delete-field';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#replace-a-field
     */
    public const REPLACE_FIELD = 'replace-field';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#add-a-dynamic-field-rule
     */
    public const ADD_DYNAMIC_FIELD = 'add-dynamic-field';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#delete-a-dynamic-field-rule
     */
    public const DELETE_DYNAMIC_FIELD = 'delete-dynamic-field';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#replace-a-dynamic-field-rule
     */
    public const REPLACE_DYNAMIC_FIELD = 'replace-dynamic-field';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#add-a-new-field-type
     */
    public const ADD_FIELD_TYPE = 'add-field-type';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#delete-a-field-type
     */
    public const DELETE_FIELD_TYPE = 'delete-field-type';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#replace-a-field-type
     */
    public const REPLACE_FIELD_TYPE = 'replace-field-type';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#add-a-new-copy-field-rule
     */
    public const ADD_COPY_FIELD = 'add-copy-field';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#delete-a-copy-field-rule
     */
    public const DELETE_COPY_FIELD = 'delete-copy-field';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#retrieve-the-entire-schema
     */
    public const GET_SCHEMA = '';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#list-fields
     */
    public const LIST_FIELDS = 'fields';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#list-fields
     */
    public const LIST_FIELD = 'fields/%s';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#list-dynamic-fields
     */
    public const LIST_DYNAMIC_FIELDS = 'dynamicfields';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#list-dynamic-fields
     */
    public const LIST_DYNAMIC_FIELD = 'dynamicfields/%s';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#list-field-types
     */
    public const LIST_FIELD_TYPES = 'fieldtypes';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#list-field-types
     */
    public const LIST_FIELD_TYPE = 'fieldtypes/%s';

    /**
     * @ssee https://lucene.apache.org/solr/guide/schema-api.html#list-copy-fields
     */
    public const LIST_COPY_FIELDS = 'copyfields';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#show-schema-name
     */
    public const SHOW_SCHEMA_NAME = 'name';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#show-the-schema-version
     */
    public const SHOW_SCHEMA_VERSION = 'version';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#list-uniquekey
     */
    public const LIST_UNIQUE_KEY = 'uniquekey';

    /**
     * @see https://lucene.apache.org/solr/guide/schema-api.html#show-global-similarity
     */
    public const SHOW_GLOBAL_SIMILARITY = 'similarity';

    public const SUB_PATHS = [
        self::GET_SCHEMA,
        self::LIST_FIELDS,
        self::LIST_FIELD,
        self::LIST_DYNAMIC_FIELDS,
        self::LIST_DYNAMIC_FIELD,
        self::LIST_FIELD_TYPES,
        self::LIST_FIELD_TYPE,
        self::LIST_COPY_FIELDS,
        self::SHOW_SCHEMA_NAME,
        self::SHOW_SCHEMA_VERSION,
        self::LIST_UNIQUE_KEY,
        self::SHOW_GLOBAL_SIMILARITY,
    ];

    /**
     * Available commands for the schema api.
     */
    public const COMMANDS = [
        self::ADD_FIELD => [],
        self::DELETE_FIELD => [],
        self::REPLACE_FIELD => [],
        self::ADD_DYNAMIC_FIELD => [],
        self::DELETE_DYNAMIC_FIELD => [],
        self::REPLACE_DYNAMIC_FIELD => [],
        self::ADD_FIELD_TYPE => [],
        self::DELETE_FIELD_TYPE => [],
        self::REPLACE_FIELD_TYPE => [],
        self::ADD_COPY_FIELD => [],
        self::DELETE_COPY_FIELD => [],
    ];

    /**
     * {@inheritdoc}
     */
    public function getCommands(): array
    {
        return self::COMMANDS;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubPaths(): array
    {
        return self::SUB_PATHS;
    }

    /**
     * {@inheritdoc}
     */
    public function getHandler(): string
    {
        return 'schema';
    }
}
