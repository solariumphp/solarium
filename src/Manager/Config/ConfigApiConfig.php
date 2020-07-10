<?php

declare(strict_types=1);

namespace Solarium\Manager\Config;

use Solarium\Manager\Contract\ApiV2ConfigurationInterface;

/**
 * Schema Api Config.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
final class ConfigApiConfig implements ApiV2ConfigurationInterface
{
    /**
     * commands for common properties.
     *
     * @see https://lucene.apache.org/solr/guide/config-api.html#commands-for-common-properties
     */
    public const SET_PROPERTY = 'set-property';

    public const UNSET_PROPERTY = 'unset-property';

    /**
     * commands for user defined properties.
     *
     * @see https://lucene.apache.org/solr/guide/config-api.html#commands-for-user-defined-properties
     */
    public const SET_USER_PROPERTY = 'set-user-property';

    public const UNSET_USER_PROPERTY = 'unset-user-property';

    /**
     * basic commands for components.
     *
     * @see https://lucene.apache.org/solr/guide/config-api.html#basic-commands-for-components
     */
    public const ADD_REQUEST_HANDLER = 'add-requesthandler';

    public const UPDATE_REQUEST_HANDLER = 'update-requesthandler';

    public const DELETE_REQUEST_HANDLER = 'delete-requesthandler';

    public const ADD_SEARCH_COMPONENT = 'add-searchcomponent';

    public const UPDATE_SEARCH_COMPONENT = 'update-searchcomponent';

    public const DELETE_SEARCH_COMPONENT = 'delete-searchcomponent';

    public const ADD_INIT_PARAMS = 'add-initparams';

    public const UPDATE_INIT_PARAMS = 'update-initparams';

    public const DELETE_INIT_PARAMS = 'delete-initparams';

    public const ADD_QUERY_RESPONSE_WRITER = 'add-queryresponsewriter';

    public const UPDATE_QUERY_RESPONSE_WRITER = 'update-queryresponsewriter';

    public const DELETE_QUERY_RESPONSE_WRITER = 'delete-queryresponsewriter';

    /**
     * advanced commands for components.
     *
     * @see https://lucene.apache.org/solr/guide/config-api.html#advanced-commands-for-components
     */
    public const ADD_QUERY_PARSER = 'add-queryparser';

    public const UPDATE_QUERY_PARSER = 'update-queryparser';

    public const DELETE_QUERY_PARSER = 'delete-queryparser';

    public const ADD_VALUE_SOURCE_PARSER = 'add-valuesourceparser';

    public const UPDATE_VALUE_SOURCE_PARSER = 'update-valuesourceparser';

    public const DELETE_VALUE_SOURCE_PARSER = 'delete-valuesourceparser';

    public const ADD_TRANSFORMER = 'add-transformer';

    public const UPDATE_TRANSFORMER = 'update-transformer';

    public const DELETE_TRANSFORMER = 'delete-transformer';

    public const ADD_UPDATE_PROCESSOR = 'add-updateprocessor';

    public const UPDATE_UPDATE_PROCESSOR = 'update-updateprocessor';

    public const DELETE_UPDATE_PROCESSOR = 'delete-updateprocessor';

    public const ADD_QUERY_CONVERTER = 'add-queryconverter';

    public const UPDATE_QUERY_CONVERTER = 'update-queryconverter';

    public const DELETE_QUERY_CONVERTER = 'delete-queryconverter';

    public const ADD_LISTENER = 'add-listener';

    public const UPDATE_LISTENER = 'update-listener';

    public const DELETE_LISTENER = 'delete-listener';

    public const ADD_RUNTIME_LIB = 'add-runtimelib';

    public const UPDATE_RUNTIME_LIB = 'update-runtimelib';

    public const DELETE_RUNTIME_LIB = 'delete-runtimelib';

    public const GET_CONFIG = '';

    public const GET_OVERLAY = 'overlay';

    public const GET_SEARCH_COMPONENTS = 'searchComponent';

    public const GET_REQUEST_HANDLERS = 'requestHandler';

    public const GET_QUERY = 'query';

    public const SUB_PATHS = [
        self::GET_CONFIG,
        self::GET_OVERLAY,
        self::GET_SEARCH_COMPONENTS,
        self::GET_REQUEST_HANDLERS,
        self::GET_QUERY,
    ];

    public const COMMANDS = [
        self::SET_PROPERTY => [],
        self::UNSET_PROPERTY => [],
        self::SET_USER_PROPERTY => [],
        self::UNSET_USER_PROPERTY => [],
        self::ADD_REQUEST_HANDLER => [],
        self::UPDATE_REQUEST_HANDLER => [],
        self::DELETE_REQUEST_HANDLER => [],
        self::ADD_SEARCH_COMPONENT => [],
        self::UPDATE_SEARCH_COMPONENT => [],
        self::DELETE_SEARCH_COMPONENT => [],
        self::ADD_INIT_PARAMS => [],
        self::UPDATE_INIT_PARAMS => [],
        self::DELETE_INIT_PARAMS => [],
        self::ADD_QUERY_RESPONSE_WRITER => [],
        self::UPDATE_QUERY_RESPONSE_WRITER => [],
        self::DELETE_QUERY_RESPONSE_WRITER => [],
        self::ADD_QUERY_PARSER => [],
        self::UPDATE_QUERY_PARSER => [],
        self::DELETE_QUERY_PARSER => [],
        self::ADD_VALUE_SOURCE_PARSER => [],
        self::UPDATE_VALUE_SOURCE_PARSER => [],
        self::DELETE_VALUE_SOURCE_PARSER => [],
        self::ADD_TRANSFORMER => [],
        self::UPDATE_TRANSFORMER => [],
        self::DELETE_TRANSFORMER => [],
        self::ADD_UPDATE_PROCESSOR => [],
        self::UPDATE_UPDATE_PROCESSOR => [],
        self::DELETE_UPDATE_PROCESSOR => [],
        self::ADD_QUERY_CONVERTER => [],
        self::UPDATE_QUERY_CONVERTER => [],
        self::DELETE_QUERY_CONVERTER => [],
        self::ADD_LISTENER => [],
        self::UPDATE_LISTENER => [],
        self::DELETE_LISTENER => [],
        self::ADD_RUNTIME_LIB => [],
        self::UPDATE_RUNTIME_LIB => [],
        self::DELETE_RUNTIME_LIB => [],
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
        return 'config';
    }
}
