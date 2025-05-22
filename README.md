PHPCS config file:

```xml
<?xml version="1.0"?>
<ruleset name="WooCommerce Coding Standards">
	<description>My projects ruleset.</description>
	
	<!-- Configs -->
	<config name="minimum_supported_wp_version" value="4.7" />
	<config name="testVersion" value="7.2-" />

	<!-- Rules -->
	<rule ref="WooCommerce-Core" />

	<rule ref="WordPress.WP.I18n">
		<properties>
			<property name="text_domain" type="array" value="new-text-domain" />
		</properties>
	</rule>

	<rule ref="PHPCompatibility">
		<exclude-pattern>tests/</exclude-pattern>
	</rule>
</ruleset>


{
  "require": {
  },
  "require-dev": {
    "wp-coding-standards/wpcs": "3.1.0",
    "phpcompatibility/php-compatibility": "^9.1",
    "rarst/phpcs-cognitive-complexity": "^0.2",
    "woocommerce/woocommerce-sniffs": "dev-trunk"
  },
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/OlegApanovich/woocommerce-sniffs"
    }
  ],
  "autoload": {
    "psr-0": {
      "ComposerHooks": "_.tools/"
    }
  },
  "config": {
    "cache-files-ttl": 0,
    "allow-plugins": {
      "dealerdirect/phpcodesniffer-composer-installer": true
    }
  },
  "scripts": {
    "post-update-cmd": "ComposerHooks::postUpdateCmd",
    "post-install-cmd": "ComposerHooks::postInstallCmd",
    "update-classmap": "ComposerHooks::updateClassmap"
  }
}
