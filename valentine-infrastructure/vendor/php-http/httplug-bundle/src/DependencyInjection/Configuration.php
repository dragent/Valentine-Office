<?php

declare(strict_types=1);

namespace Http\HttplugBundle\DependencyInjection;

use Http\Client\Common\Plugin\Cache\Generator\CacheKeyGenerator;
use Http\Client\Common\Plugin\Cache\Listener\CacheListener;
use Http\Client\Common\Plugin\CachePlugin;
use Http\Client\Common\Plugin\Journal;
use Http\Client\Plugin\Vcr\NamingStrategy\NamingStrategyInterface;
use Http\Client\Plugin\Vcr\Recorder\PlayerInterface;
use Http\Client\Plugin\Vcr\Recorder\RecorderInterface;
use Http\Message\CookieJar;
use Http\Message\Formatter;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author David Buchmann <mail@davidbu.ch>
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class Configuration implements ConfigurationInterface
{
    public function __construct(
        /**
         * Whether to use the debug mode.
         *
         * @see https://github.com/doctrine/DoctrineBundle/blob/v1.5.2/DependencyInjection/Configuration.php#L31-L41
         */
        private bool $debug,
    ) {
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('httplug');
        $rootNode = $treeBuilder->getRootNode();

        $this->configureClients($rootNode);
        $this->configureSharedPlugins($rootNode);

        $rootNode
            ->validate()
                ->ifTrue(fn ($v) => !empty($v['classes']['client'])
                    || !empty($v['classes']['psr17_request_factory'])
                    || !empty($v['classes']['psr17_response_factory'])
                    || !empty($v['classes']['psr17_uri_factory'])
                    || !empty($v['classes']['psr17_stream_factory']))
                ->then(function ($v) {
                    foreach ($v['classes'] as $key => $class) {
                        if (null !== $class && !class_exists($class)) {
                            throw new InvalidConfigurationException(sprintf(
                                'Class %s specified for httplug.classes.%s does not exist.',
                                $class,
                                $key
                            ));
                        }
                    }

                    return $v;
                })
            ->end()
            ->fixXmlConfig('client')
            ->children()
                ->booleanNode('default_client_autowiring')
                    ->defaultTrue()
                    ->info('Set to false to not autowire ClientInterface and HttpAsyncClient.')
                ->end()
                ->arrayNode('main_alias')
                    ->addDefaultsIfNotSet()
                    ->info('Configure which service the main alias point to.')
                    ->children()
                        ->scalarNode('client')->defaultValue('httplug.client.default')->end()
                        ->scalarNode('psr18_client')->defaultValue('httplug.psr18_client.default')->end()
                        ->scalarNode('psr17_request_factory')->defaultValue('httplug.psr17_request_factory.default')->end()
                        ->scalarNode('psr17_response_factory')->defaultValue('httplug.psr17_response_factory.default')->end()
                        ->scalarNode('psr17_stream_factory')->defaultValue('httplug.psr17_stream_factory.default')->end()
                        ->scalarNode('psr17_uri_factory')->defaultValue('httplug.psr17_uri_factory.default')->end()
                        ->scalarNode('psr17_uploaded_file_factory')->defaultValue('httplug.psr17_uploaded_file_factory.default')->end()
                        ->scalarNode('psr17_server_request_factory')->defaultValue('httplug.psr17_server_request_factory.default')->end()
                    ->end()
                ->end()
                ->arrayNode('classes')
                    ->addDefaultsIfNotSet()
                    ->info('Overwrite a service class instead of using the discovery mechanism.')
                    ->children()
                        ->scalarNode('client')->defaultNull()->end()
                        ->scalarNode('psr18_client')->defaultNull()->end()
                        ->scalarNode('psr17_request_factory')->defaultNull()->end()
                        ->scalarNode('psr17_response_factory')->defaultNull()->end()
                        ->scalarNode('psr17_stream_factory')->defaultNull()->end()
                        ->scalarNode('psr17_uri_factory')->defaultNull()->end()
                        ->scalarNode('psr17_uploaded_file_factory')->defaultNull()->end()
                        ->scalarNode('psr17_server_request_factory')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('profiling')
                    ->addDefaultsIfNotSet()
                    ->treatFalseLike(['enabled' => false])
                    ->treatTrueLike(['enabled' => true])
                    ->treatNullLike(['enabled' => $this->debug])
                    ->info('Extend the debug profiler with information about requests.')
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Turn the toolbar on or off. Defaults to kernel debug mode.')
                            ->defaultValue($this->debug)
                        ->end()
                        ->scalarNode('formatter')->defaultNull()->end()
                        ->scalarNode('captured_body_length')
                            ->validate()
                                ->ifTrue(fn ($v) => null !== $v && !is_int($v))
                                ->thenInvalid('The child node "captured_body_length" at path "httplug.profiling" must be an integer or null ("%s" given).')
                            ->end()
                            ->defaultValue(0)
                            ->info('Limit long HTTP message bodies to x characters. If set to 0 we do not read the message body. If null the body will not be truncated. Only available with the default formatter (FullHttpMessageFormatter).')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('discovery')
                    ->addDefaultsIfNotSet()
                    ->info('Control what clients should be found by the discovery.')
                    ->children()
                        ->scalarNode('client')
                            ->defaultValue('auto')
                            ->info('Set to "auto" to see auto discovered client in the web profiler. If provided a service id for a client then this client will be found by auto discovery.')
                        ->end()
                        ->scalarNode('async_client')
                            ->defaultNull()
                            ->info('Set to "auto" to see auto discovered client in the web profiler. If provided a service id for a client then this client will be found by auto discovery.')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    private function configureClients(ArrayNodeDefinition $root): void
    {
        $root->children()
            ->arrayNode('clients')
                ->useAttributeAsKey('name')
                ->prototype('array')
                ->fixXmlConfig('plugin')
                ->validate()
                    ->ifTrue(fn ($config) =>
                        // Make sure we only allow one of these to be true
                        (bool) $config['flexible_client'] + (bool) $config['http_methods_client'] + (bool) $config['batch_client'] >= 2)
                    ->thenInvalid('A http client can\'t be decorated with several of FlexibleHttpClient, HttpMethodsClient and BatchClient. Only one of the following options can be true. ("flexible_client", "http_methods_client", "batch_client")')
                ->end()
                ->validate()
                    ->ifTrue(fn ($config) => 'httplug.factory.auto' === $config['factory'] && !empty($config['config']))
                    ->thenInvalid('If you want to use the "config" key you must also specify a valid "factory".')
                ->end()
                ->validate()
                    ->ifTrue(fn ($config) => !empty($config['service']) && ('httplug.factory.auto' !== $config['factory'] || !empty($config['config'])))
                    ->thenInvalid('If you want to use the "service" key you cannot specify "factory" or "config".')
                ->end()
                ->children()
                    ->scalarNode('factory')
                        ->defaultValue('httplug.factory.auto')
                        ->cannotBeEmpty()
                        ->info('The service id of a factory to use when creating the adapter.')
                    ->end()
                    ->scalarNode('service')
                        ->defaultNull()
                        ->info('The service id of the client to use.')
                    ->end()
                    ->booleanNode('public')
                        ->defaultNull()
                        ->info('Set to true if you really cannot use dependency injection and need to make the client service public.')
                    ->end()
                    ->booleanNode('flexible_client')
                        ->defaultFalse()
                        ->info('Set to true to get the client wrapped in a FlexibleHttpClient which emulates async or sync behavior.')
                    ->end()
                    ->booleanNode('http_methods_client')
                        ->defaultFalse()
                        ->info('Set to true to get the client wrapped in a HttpMethodsClient which emulates provides functions for HTTP verbs.')
                    ->end()
                    ->booleanNode('batch_client')
                        ->defaultFalse()
                        ->info('Set to true to get the client wrapped in a BatchClient which allows you to send multiple request at the same time.')
                    ->end()
                    ->variableNode('config')->defaultValue([])->end()
                    ->append($this->createClientPluginNode())
                ->end()
            ->end()
        ->end();
    }

    private function configureSharedPlugins(ArrayNodeDefinition $root): void
    {
        $pluginsNode = $root
            ->children()
                ->arrayNode('plugins')
                ->info('Global plugin configuration. Plugins need to be explicitly added to clients.')
                ->addDefaultsIfNotSet()
            // don't call end to get the plugins node
        ;
        $this->addSharedPluginNodes($pluginsNode);
    }

    /**
     * Create plugins node of a client.
     */
    private function createClientPluginNode(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('plugins');
        $node = $treeBuilder->getRootNode();

        /** @var ArrayNodeDefinition $pluginList */
        $pluginList = $node
            ->info('A list of plugin service ids and client specific plugin definitions. The order is important.')
            ->prototype('array')
        ;
        $pluginList
            // support having just a service id in the list
            ->beforeNormalization()
                ->always(function ($plugin) {
                    if (is_string($plugin)) {
                        return [
                            'reference' => [
                                'enabled' => true,
                                'id' => $plugin,
                            ],
                        ];
                    }

                    return $plugin;
                })
            ->end()

            ->validate()
                ->always(function ($plugins) {
                    foreach ($plugins as $name => $definition) {
                        if ('authentication' === $name) {
                            if (!count($definition)) {
                                unset($plugins['authentication']);
                            }
                        } elseif (!$definition['enabled']) {
                            unset($plugins[$name]);
                        }
                    }

                    return $plugins;
                })
            ->end()
        ;
        $this->addSharedPluginNodes($pluginList, true);

        $pluginList
            ->children()
                ->arrayNode('reference')
                    ->canBeEnabled()
                    ->info('Reference to a plugin service')
                    ->children()
                        ->scalarNode('id')
                            ->info('Service id of a plugin')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('configurator')
                    ->canBeEnabled()
                    ->info('Configure a plugin with a configurator')
                    ->children()
                        ->scalarNode('id')
                            ->info('Service id of a plugin configurator')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->arrayNode('config')
                            ->normalizeKeys(false)
                            ->prototype('variable')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('add_host')
                    ->canBeEnabled()
                    ->addDefaultsIfNotSet()
                    ->info('Set scheme, host and port in the request URI.')
                    ->children()
                        ->scalarNode('host')
                            ->info('Host name including protocol and optionally the port number, e.g. https://api.local:8000')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('replace')
                            ->info('Whether to replace the host if request already specifies one')
                            ->defaultValue(false)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('add_path')
                    ->canBeEnabled()
                    ->addDefaultsIfNotSet()
                    ->info('Add a base path to the request.')
                    ->children()
                        ->scalarNode('path')
                            ->info('Path to be added, e.g. /api/v1')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('base_uri')
                    ->canBeEnabled()
                    ->addDefaultsIfNotSet()
                    ->info('Set a base URI to the request.')
                    ->children()
                        ->scalarNode('uri')
                            ->info('Base Uri including protocol, optionally the port number and prepend path, e.g. https://api.local:8000/api')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('replace')
                            ->info('Whether to replace the host if request already specifies one')
                            ->defaultValue(false)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('content_type')
                    ->canBeEnabled()
                    ->info('Detect the content type of a request body and set the Content-Type header if it is not already set.')
                    ->children()
                        ->booleanNode('skip_detection')
                            ->info('Whether to skip detection when request body is larger than size_limit')
                            ->defaultFalse()
                        ->end()
                        ->scalarNode('size_limit')
                            ->info('Skip content type detection if request body is larger than size_limit bytes')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('header_append')
                    ->canBeEnabled()
                    ->info('Append headers to the request. If the header already exists the value will be appended to the current value.')
                    ->fixXmlConfig('header')
                    ->children()
                        ->arrayNode('headers')
                            ->info('Keys are the header names, values the header values')
                            ->normalizeKeys(false)
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('header_defaults')
                    ->canBeEnabled()
                    ->info('Set header to default value if it does not exist.')
                    ->fixXmlConfig('header')
                    ->children()
                        ->arrayNode('headers')
                            ->info('Keys are the header names, values the header values')
                            ->normalizeKeys(false)
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('header_set')
                    ->canBeEnabled()
                    ->info('Set headers to requests. If the header does not exist it wil be set, if the header already exists it will be replaced.')
                    ->fixXmlConfig('header')
                    ->children()
                        ->arrayNode('headers')
                            ->info('Keys are the header names, values the header values')
                            ->normalizeKeys(false)
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('header_remove')
                    ->canBeEnabled()
                    ->info('Remove headers from requests.')
                    ->fixXmlConfig('header')
                    ->children()
                        ->arrayNode('headers')
                            ->info('List of header names to remove')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('query_defaults')
                    ->canBeEnabled()
                    ->info('Sets query parameters to default value if they are not present in the request.')
                    ->fixXmlConfig('parameter')
                    ->children()
                        ->arrayNode('parameters')
                            ->info('List of query parameters. Names and values must not be url encoded as the plugin will encode them.')
                            ->normalizeKeys(false)
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('request_seekable_body')
                    ->canBeEnabled()
                    ->info('Ensure that the request body is seekable so that several plugins can look into it.')
                    ->children()
                        ->booleanNode('use_file_buffer')
                            ->info('Whether to use a file buffer if the stream is too big for a memory buffer')
                            ->defaultTrue()
                        ->end()
                        ->scalarNode('memory_buffer_size')
                            ->info('Maximum memory size in bytes before using a file buffer if use_file_buffer is true. Defaults to 2097152 (2 MB)')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('response_seekable_body')
                    ->canBeEnabled()
                    ->info('Ensure that the response body is seekable so that several plugins can look into it.')
                    ->children()
                        ->booleanNode('use_file_buffer')
                            ->info('Whether to use a file buffer if the stream is too big for a memory buffer')
                            ->defaultTrue()
                        ->end()
                        ->scalarNode('memory_buffer_size')
                            ->info('Maximum memory size in bytes before using a file buffer if use_file_buffer is true. Defaults to 2097152 (2 MB)')
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('vcr')
                    ->canBeEnabled()
                    ->addDefaultsIfNotSet()
                    ->info('Record response to be replayed during tests or development cycle.')
                    ->validate()
                        ->ifTrue(fn ($config) => 'filesystem' === $config['recorder'] && empty($config['fixtures_directory']))
                        ->thenInvalid('If you want to use the "filesystem" recorder you must also specify a "fixtures_directory".')
                    ->end()
                    ->children()
                        ->enumNode('mode')
                        ->info('What should be the behavior of the plugin?')
                        ->values(['record', 'replay', 'replay_or_record'])
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('recorder')
                        ->info(sprintf('Which recorder to use. Can be "in_memory", "filesystem" or the ID of your service implementing %s and %s. When using filesystem, specify "fixtures_directory" as well.', RecorderInterface::class, PlayerInterface::class))
                        ->defaultValue('filesystem')
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('naming_strategy')
                        ->info(sprintf('Which naming strategy to use. Add the ID of your service implementing %s to override the default one.', NamingStrategyInterface::class))
                        ->defaultValue('default')
                        ->cannotBeEmpty()
                    ->end()
                    ->arrayNode('naming_strategy_options')
                        ->info('See http://docs.php-http.org/en/latest/plugins/vcr.html#the-naming-strategy for more details')
                        ->children()
                            ->arrayNode('hash_headers')
                                ->info('List of header(s) that make the request unique (Ex: ‘Authorization’)')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('hash_body_methods')
                                ->info('for which request methods the body makes requests distinct.')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end() // End naming_strategy_options
                    ->scalarNode('fixtures_directory')
                        ->info('Where the responses will be stored and replay from when using the filesystem recorder. Should be accessible to your VCS.')
                    ->end()
                ->end()
            ->end()
        ->end();

        return $node;
    }

    /**
     * Add the definitions for shared plugin configurations.
     *
     * @param ArrayNodeDefinition $pluginNode the node to add to
     * @param bool                $disableAll Some shared plugins are enabled by default. On the client, all are disabled by default.
     */
    private function addSharedPluginNodes(ArrayNodeDefinition $pluginNode, bool $disableAll = false): void
    {
        $children = $pluginNode->children();

        $children->append($this->createAuthenticationPluginNode());
        $children->append($this->createCachePluginNode());

        $children
            ->arrayNode('cookie')
                ->canBeEnabled()
                ->children()
                    ->scalarNode('cookie_jar')
                        ->info('This must be a service id to a service implementing '.CookieJar::class)
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end();
        // End cookie plugin

        $children
            ->arrayNode('history')
                ->canBeEnabled()
                ->children()
                    ->scalarNode('journal')
                        ->info('This must be a service id to a service implementing '.Journal::class)
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end();
        // End history plugin

        $decoder = $children->arrayNode('decoder');
        $disableAll ? $decoder->canBeEnabled() : $decoder->canBeDisabled();
        $decoder->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('use_content_encoding')->defaultTrue()->end()
            ->end()
        ->end();
        // End decoder plugin

        $logger = $children->arrayNode('logger');
        $disableAll ? $logger->canBeEnabled() : $logger->canBeDisabled();
        $logger->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('logger')
                    ->info('This must be a service id to a service implementing '.LoggerInterface::class)
                    ->defaultValue('logger')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('formatter')
                    ->info('This must be a service id to a service implementing '.Formatter::class)
                    ->defaultNull()
                ->end()
            ->end()
        ->end();
        // End logger plugin

        $redirect = $children->arrayNode('redirect');
        $disableAll ? $redirect->canBeEnabled() : $redirect->canBeDisabled();
        $redirect->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('preserve_header')->defaultTrue()->end()
                ->scalarNode('use_default_for_multiple')->defaultTrue()->end()
            ->end()
        ->end();
        // End redirect plugin

        $retry = $children->arrayNode('retry');
        $disableAll ? $retry->canBeEnabled() : $retry->canBeDisabled();
        $retry->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('retry')->defaultValue(1)->end() // TODO: should be called retries for consistency with the class
            ->end()
        ->end();
        // End retry plugin

        $stopwatch = $children->arrayNode('stopwatch');
        $disableAll ? $stopwatch->canBeEnabled() : $stopwatch->canBeDisabled();
        $stopwatch->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('stopwatch')
                    ->info('This must be a service id to a service extending Symfony\Component\Stopwatch\Stopwatch')
                    ->defaultValue('debug.stopwatch')
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ->end();
        // End stopwatch plugin

        $children->arrayNode('error')
            ->canBeEnabled()
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('only_server_exception')->defaultFalse()->end()
            ->end()
        ->end();
        // End error plugin

        $children->arrayNode('throttle')
            ->canBeEnabled()
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('name')
                    ->isRequired()
                    ->info('Rate limiter service name from symfony/rate-limiter configuration. E.g. for a rate limiter http_client you specify limiter.http_client here')
                ->end()
                ->scalarNode('key')
                    ->defaultNull()
                    ->info('Key to avoid sharing this rate limiter with other clients or other services. You can use the name of the client for example.')
                ->end()
                ->integerNode('tokens')
                    ->defaultValue(1)
                    ->info('How many tokens spending per request')
                ->end()
                ->floatNode('max_time')
                    ->defaultNull()
                    ->info('Maximum accepted waiting time in seconds')
                ->end()
            ->end()
        ->end();
        // End throttle plugin
    }

    /**
     * Create configuration for authentication plugin.
     *
     * @return NodeDefinition definition for the authentication node in the plugins list
     */
    private function createAuthenticationPluginNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('authentication');
        $node = $treeBuilder->getRootNode();

        $node
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->validate()
                    ->always()
                    ->then(function ($config) {
                        match ($config['type']) {
                            'basic' => $this->validateAuthenticationType(['username', 'password'], $config, 'basic'),
                            'bearer' => $this->validateAuthenticationType(['token'], $config, 'bearer'),
                            'service' => $this->validateAuthenticationType(['service'], $config, 'service'),
                            'wsse' => $this->validateAuthenticationType(['username', 'password'], $config, 'wsse'),
                            'query_param' => $this->validateAuthenticationType(['params'], $config, 'query_param'),
                            'header' => $this->validateAuthenticationType(['header_name', 'header_value'], $config, 'header'),
                            default => $config,
                        };

                        return $config;
                    })
                ->end()
                ->children()
                    ->enumNode('type')
                        ->values(['basic', 'bearer', 'wsse', 'service', 'query_param', 'header'])
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                    ->scalarNode('username')->end()
                    ->scalarNode('password')->end()
                    ->scalarNode('token')->end()
                    ->scalarNode('service')->end()
                    ->scalarNode('header_name')->end()
                    ->scalarNode('header_value')->end()
                    ->arrayNode('params')->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end(); // End authentication plugin

        return $node;
    }

    /**
     * Validate that the configuration fragment has the specified keys and none other.
     *
     * @param array  $expected Fields that must exist
     * @param array  $actual   Actual configuration hashmap
     * @param string $authName Name of authentication method for error messages
     *
     * @throws InvalidConfigurationException If $actual does not have exactly the keys specified in $expected (plus 'type')
     */
    private function validateAuthenticationType(array $expected, array $actual, string $authName): void
    {
        unset($actual['type']);
        // Empty array is always provided, even if the config is not filled.
        if (empty($actual['params'])) {
            unset($actual['params']);
        }
        $actual = array_keys($actual);
        sort($actual);
        sort($expected);

        if ($expected === $actual) {
            return;
        }

        throw new InvalidConfigurationException(sprintf(
            'Authentication "%s" requires %s but got %s',
            $authName,
            implode(', ', $expected),
            implode(', ', $actual)
        ));
    }

    /**
     * Create configuration for cache plugin.
     *
     * @return NodeDefinition definition for the cache node in the plugins list
     */
    private function createCachePluginNode(): NodeDefinition
    {
        $builder = new TreeBuilder('config');
        $config = $builder->getRootNode();

        $config
            ->fixXmlConfig('method')
            ->fixXmlConfig('respect_response_cache_directive')
            ->fixXmlConfig('cache_listener')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('cache_key_generator')
                    ->info('This must be a service id to a service implementing '.CacheKeyGenerator::class)
                ->end()
                ->scalarNode('cache_lifetime')
                    ->info('The minimum time we should store a cache item')
                    ->validate()
                    ->ifTrue(fn ($v) => null !== $v && !is_int($v))
                    ->thenInvalid('cache_lifetime must be an integer or null, got %s')
                    ->end()
                ->end()
                ->scalarNode('default_ttl')
                    ->info('The default max age of a Response')
                    ->validate()
                        ->ifTrue(fn ($v) => null !== $v && !is_int($v))
                        ->thenInvalid('default_ttl must be an integer or null, got %s')
                    ->end()
                ->end()
                ->arrayNode('blacklisted_paths')
                    ->info('An array of regular expression patterns for paths not to be cached. Defaults to an empty array.')
                    ->defaultValue([])
                    ->beforeNormalization()
                        ->castToArray()
                    ->end()
                    ->prototype('scalar')
                        ->validate()
                            ->ifTrue(fn ($v) => false === @preg_match($v, ''))
                            ->thenInvalid('Invalid regular expression for a blacklisted path: %s')
                        ->end()
                    ->end()
                ->end()
                ->enumNode('hash_algo')
                    ->info('Hashing algorithm to use')
                    ->values(hash_algos())
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('methods')
                    ->info('Which request methods to cache')
                    ->defaultValue(['GET', 'HEAD'])
                    ->prototype('scalar')
                        ->validate()
                            ->ifTrue(fn ($v) =>
                                /* RFC7230 sections 3.1.1 and 3.2.6 except limited to uppercase characters. */
                                preg_match('/[^A-Z0-9!#$%&\'*+\-.^_`|~]+/', (string) $v))
                            ->thenInvalid('Invalid method: %s')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('cache_listeners')
                    ->info('A list of service ids to act on the response based on the results of the cache check. Must implement '.CacheListener::class.'. Defaults to an empty array.')
                    ->beforeNormalization()->castToArray()->end()
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->variableNode('respect_response_cache_directives')
                    ->info('A list of cache directives to respect when caching responses. Omit or set to null to respect the default set of directives.')
                    ->validate()
                        ->always(function ($v) {
                            if (is_null($v) || is_array($v)) {
                                return $v;
                            }

                            throw new InvalidTypeException();
                        })
                    ->end()
                ->end()
            ->end()
        ;

        $treeBuilder = new TreeBuilder('cache');
        $cache = $treeBuilder->getRootNode();

        $cache
            ->canBeEnabled()
            ->info('Configure HTTP caching, requires the php-http/cache-plugin package')
            ->addDefaultsIfNotSet()
            ->validate()
                ->ifTrue(fn ($v) => !empty($v['enabled']) && !class_exists(CachePlugin::class))
                ->thenInvalid('To use the cache plugin, you need to require php-http/cache-plugin in your project')
            ->end()
            ->children()
                ->scalarNode('cache_pool')
                    ->info('This must be a service id to a service implementing '.CacheItemPoolInterface::class)
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('stream_factory')
                    ->info('This must be a service id to a service implementing '.StreamFactoryInterface::class)
                    ->defaultValue('httplug.psr17_stream_factory')
                    ->cannotBeEmpty()
                ->end()
            ->end()
            ->append($config)
        ;

        return $cache;
    }
}
