---
layout: default
title: Configuration
description: Defining configuration schemas and accessing user-provided configuration options within your custom extensions
---

# Configuration Schemas and Values

Version 2.0 introduced a new robust system for defining configuration schemas and accessing them within custom extensions.

## Configuration Schemas

Unlike in 1.x, all configuration options must have a defined schema.  This defines which options are available, what types of values they accept, whether any are required, and any default values you wish to define if the user doesn't provide any.

These custom options can be defined from within your [custom extension](/2.5/customization/extensions/) by implementing the `ConfigurableExtensionInterface`:

```php
use League\Config\ConfigurationBuilderInterface;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use Nette\Schema\Expect;

final class MyCustomExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('my_extension', Expect::structure([
            'enable_some_feature' => Expect::bool()->default(true),
            'html_class' => Expect::string()->default('my-custom-extension'),
            'align' => Expect::anyOf('left', 'center', 'right')->default('left'),
            'favorite_number' => Expect::int()->min(1)->max(100)->default(42),
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        // TODO: Implement register() method
    }
}
```

See the [league/config documentation](https://config.thephpleague.com/1.0/schemas/) for more examples of how to define custom configuration schemas.

Note that you only need to implement `ConfigurableExtensionInterface` if you plan to define new configuration options - you don't need this if you're only reading existing options.

## Reading Configuration Values

Okay, so your extension has defined the different options that are available, but now you want to start using them within your custom extension.  There are a few ways you can access the values:

### During Extension Registration

Perhaps your extension needs to decide whether/how to register certain parsers/renderers/etc based on the user-provided configuration values - in that case, you can read the value from the `$environment` - for example:

```php
use League\Config\ConfigurationBuilderInterface;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\ConfigurableExtensionInterface;

final class MyCustomExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        // (see code example above)
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        if ($environment->getConfiguration()->get('my_extension/enable_some_feature')) {
            $environment->addBlockStartParser(new MyCustomParser());
            $environment->addRenderer(MyCustomBlockType::class, new MyCustomRenderer());
        }
    }
}
```

### Within Parsers/Renderers/Listeners

Perhaps you want to reference those configuration values from within a custom parser, renderer, event listener, or something else.  This can easily by done by having that class also implement `ConfigurationAwareInterface`.  This interface signals to the `Environment` that your class needs a copy of the final configuration so it can read it later:

```php
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\Config\ConfigurationAwareInterface;
use League\Config\ConfigurationInterface;

final class MyCustomRenderer implements NodeRendererInterface, ConfigurationAwareInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $config;

    public function setConfiguration(ConfigurationInterface $configuration): void
    {
        $this->config = $configuration;
    }

    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        return 'My favorite number is ' . $this->config->get('my_extension/favorite_number');
    }
}
```

You can access any configuration value from here, not just the ones you might have defined yourself.
