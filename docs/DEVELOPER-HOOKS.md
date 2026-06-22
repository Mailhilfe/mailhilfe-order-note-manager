# Developer hooks and filters

The plugin exposes extension points for integrations. Examples use the current public hook names; callback code remains responsible for validation, escaping and compatibility.

## Placeholder definitions

```php
add_filter(
    'mailhilfe_order_note_placeholders',
    static function ( array $definitions ): array {
        $definitions['{warehouse_name}'] = 'Warehouse name';
        return $definitions;
    }
);
```

Signature:

```php
apply_filters( 'mailhilfe_order_note_placeholders', array $definitions )
```

## Placeholder values

```php
add_filter(
    'mailhilfe_order_note_placeholder_values',
    static function ( array $values, WC_Order $order ): array {
        $values['{warehouse_name}'] = 'North warehouse';
        return $values;
    },
    10,
    2
);
```

Signature:

```php
apply_filters( 'mailhilfe_order_note_placeholder_values', array $values, WC_Order $order )
```

## Allowed custom meta keys

```php
add_filter(
    'mailhilfe_order_note_allowed_meta_keys',
    static function ( array $allowed, string $requested_key ): array {
        $allowed[] = '_delivery_reference';
        return array_unique( $allowed );
    },
    10,
    2
);
```

Signature:

```php
apply_filters( 'mailhilfe_order_note_allowed_meta_keys', array $allowed, string $requested_key )
```

Use an explicit allowlist for sensitive integrations.

## Template results

```php
add_filter(
    'mailhilfe_order_note_template_results',
    static function ( array $templates, WC_Order $order, int $user_id ): array {
        return $templates;
    },
    10,
    3
);
```

Signature:

```php
apply_filters( 'mailhilfe_order_note_template_results', array $templates, WC_Order $order, int $user_id )
```

## Additional condition logic

```php
add_filter(
    'mailhilfe_order_note_conditions_match',
    static function ( bool $matches, WP_Post $template, WC_Order $order, array $conditions ): bool {
        return $matches;
    },
    10,
    4
);
```

Signature:

```php
apply_filters( 'mailhilfe_order_note_conditions_match', bool $matches, WP_Post $template, WC_Order $order, array $conditions )
```

## Preview content

```php
add_filter(
    'mailhilfe_order_note_preview_content',
    static function ( string $content, WC_Order $order, WP_Post $template ): string {
        return $content;
    },
    10,
    3
);
```

## Final note content

```php
add_filter(
    'mailhilfe_order_note_content',
    static function ( string $note, WC_Order $order, WP_Post $template, string $note_type ): string {
        return $note;
    },
    10,
    4
);
```

## Before adding a note

```php
do_action( 'mailhilfe_order_note_before_add', WC_Order $order, WP_Post $template, string $note, string $note_type )
```

## After adding a note

```php
do_action( 'mailhilfe_order_note_after_add', WC_Order $order, WP_Post $template, string $note, string $note_type, int $note_id )
```

## History entry recorded

```php
do_action( 'mailhilfe_order_note_history_recorded', array $data, int $history_id )
```

## Diagnostics rows

```php
add_filter(
    'mailhilfe_order_note_diagnostics',
    static function ( array $rows ): array {
        $rows['custom_integration'] = array(
            'label'  => 'Custom integration',
            'value'  => 'Active',
            'status' => 'good',
        );
        return $rows;
    }
);
```

Signature:

```php
apply_filters( 'mailhilfe_order_note_diagnostics', array $rows )
```

## Compatibility notes

- Do not write directly to WooCommerce order tables.
- Use `wc_get_order()` and `WC_Order` methods.
- Validate callback types because another extension may return unexpected data.
- Avoid storing customer or order data in debug logs.
