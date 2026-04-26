<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4
- filament/filament (FILAMENT) - v4
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- livewire/flux (FLUXUI_FREE) - v2
- livewire/livewire (LIVEWIRE) - v3
- livewire/volt (VOLT) - v1
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `fortify-development` — ACTIVATE when the user works on authentication in Laravel. This includes login, registration, password reset, email verification, two-factor authentication (2FA/TOTP/QR codes/recovery codes), profile updates, password confirmation, or any auth-related routes and controllers. Activate when the user mentions Fortify, auth, authentication, login, register, signup, forgot password, verify email, 2FA, or references app/Actions/Fortify/, CreateNewUser, UpdateUserProfileInformation, FortifyServiceProvider, config/fortify.php, or auth guards. Fortify is the frontend-agnostic authentication backend for Laravel that registers all auth routes and controllers. Also activate when building SPA or headless authentication, customizing login redirects, overriding response contracts like LoginResponse, or configuring login throttling. Do NOT activate for Laravel Passport (OAuth2 API tokens), Socialite (OAuth social login), or non-auth Laravel features.
- `laravel-best-practices` — Apply this skill whenever writing, reviewing, or refactoring Laravel PHP code. This includes creating or modifying controllers, models, migrations, form requests, policies, jobs, scheduled commands, service classes, and Eloquent queries. Triggers for N+1 and query performance issues, caching strategies, authorization and security patterns, validation, error handling, queue and job configuration, route definitions, and architectural decisions. Also use for Laravel code reviews and refactoring existing Laravel code to follow best practices. Covers any task involving Laravel backend PHP code patterns.
- `fluxui-development` — Use this skill for Flux UI development in Livewire applications only. Trigger when working with <flux:*> components, building or customizing Livewire component UIs, creating forms, modals, tables, or other interactive elements. Covers: flux: components (buttons, inputs, modals, forms, tables, date-pickers, kanban, badges, tooltips, etc.), component composition, Tailwind CSS styling, Heroicons/Lucide icon integration, validation patterns, responsive design, and theming. Do not use for non-Livewire frameworks or non-component styling.
- `livewire-development` — Use for any task or question involving Livewire. Activate if user mentions Livewire, wire: directives, or Livewire-specific concepts like wire:model, wire:click, invoke this skill. Covers building new components, debugging reactivity issues, real-time form validation, loading states, migrating from Livewire 2 to 3, converting component formats (SFC/MFC/class-based), and performance optimization. Do not use for non-Livewire reactive UI (React, Vue, Alpine-only, Inertia.js) or standard Laravel forms without Livewire.
- `volt-development` — Develops single-file Livewire components with Volt. Activates when creating Volt components, converting Livewire to Volt, working with @volt directive, functional or class-based Volt APIs; or when the user mentions Volt, single-file components, functional Livewire, or inline component logic in Blade files.
- `tailwindcss-development` — Always invoke when the user's message includes 'tailwind' in any form. Also invoke for: building responsive grid layouts (multi-column card grids, product grids), flex/grid page structures (dashboards with sidebars, fixed topbars, mobile-toggle navs), styling UI components (cards, tables, navbars, pricing sections, forms, inputs, badges), adding dark mode variants, fixing spacing or typography, and Tailwind v3/v4 work. The core use case: writing or fixing Tailwind utility classes in HTML templates (Blade, JSX, Vue). Skip for backend PHP logic, database queries, API routes, JavaScript with no HTML/CSS component, CSS file audits, build tool configuration, and vanilla CSS.
- `shopper-customization` — Patterns for customizing Shopper in your Laravel application. Sidebar navigation, component overrides, event listeners, and domain features like stock, pricing, and media. Use when extending Shopper behavior in a project.
- `shopper-development` — Coding standards and patterns for Shopper monorepo development. Use when creating or modifying Models, Actions, Enums, Livewire components, migrations, tests, or building admin UI with Filament Schemas, Tables, and Actions.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.
- To check environment variables, read the `.env` file directly.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always declare `declare(strict_types=1);` at the top of every `.php` file.
- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app/Console/Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== livewire/core rules ===

# Livewire

- Livewire allow to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== volt/core rules ===

# Livewire Volt

- Single-file Livewire components: PHP logic and Blade templates in one file.
- Always check existing Volt components to determine functional vs class-based style.
- IMPORTANT: Always use `search-docs` tool for version-specific Volt documentation and updated code examples.
- IMPORTANT: Activate `volt-development` every time you're working with a Volt or single-file component-related task.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

=== shopper/framework rules ===

## Shopper

Shopper is a headless e-commerce framework providing a complete admin panel built with Filament and Livewire. For detailed documentation, refer to https://docs.laravelshopper.dev

### Installation

- Use `composer require shopper/framework --with-dependencies` to install Shopper.
- Run `php artisan shopper:install` to publish config, migrations, and assets.
- Run `php artisan shopper:user` to create an admin user.
- The admin panel is accessible at `/cpanel` by default (configurable via `SHOPPER_PREFIX` env variable).

### Configuration

Configuration files are published to `config/shopper/`:

- `admin.php` - Admin panel prefix, domain, and custom pages namespace/path
- `core.php` - Table prefix (default: `sh_`), roles
- `models.php` - Model bindings for customization
- `features.php` - Enable/disable features (attributes, collections, reviews, discounts)
- `media.php` - Media storage settings (Spatie Media Library)
- `orders.php` - Order number generation
- `routes.php` - Custom routes and middleware
- `components/` - Component overrides by feature

### Creating Custom Admin Pages

Use `php artisan make:shopper-page {PageName}` to create a new page in the admin panel. This creates:

- A Livewire component in `App\Livewire\Shopper` namespace (configurable in `config/shopper/admin.php`)
- A Blade view in `resources/views/livewire/shopper`

    <code-snippet name="Create a custom Shopper page with table" lang="php">
    // Run: php artisan make:shopper-page Shipping

    // app/Livewire/Shopper/Shipping.php
    namespace App\Livewire\Shopper;

    use App\Models\ShippingMethod;
    use Filament\Forms\Concerns\InteractsWithForms;
    use Filament\Forms\Contracts\HasForms;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Concerns\InteractsWithTable;
    use Filament\Tables\Contracts\HasTable;
    use Filament\Tables\Table;
    use Illuminate\Contracts\View\View;
    use Shopper\Livewire\Pages\AbstractPageComponent;

    class Shipping extends AbstractPageComponent implements HasForms, HasTable
    {
        use InteractsWithForms;
        use InteractsWithTable;

        public function mount(): void
        {
            $this->authorize('browse_shipping'); // Optional authorization
        }

        public function table(Table $table): Table
        {
            return $table
                ->query(ShippingMethod::query())
                ->columns([
                    TextColumn::make('name')
                        ->label(__('Name'))
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('price')
                        ->label(__('Price'))
                        ->money('USD'),
                    TextColumn::make('is_enabled')
                        ->label(__('Status'))
                        ->badge()
                        ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                ]);
        }

        public function render(): View
        {
            return view('livewire.shopper.shipping');
        }
    }
    </code-snippet>

    <code-snippet name="Blade view for custom page" lang="blade">
    {{-- resources/views/livewire/shopper/shipping.blade.php --}}
    <x-shopper::container>
        <x-shopper::breadcrumb :back="route('shopper.settings.index')" :current="__('Shipping Methods')">
            <x-untitledui-chevron-left class="size-4 shrink-0 text-gray-300 dark:text-gray-600" aria-hidden="true" />
            <x-shopper::breadcrumb.link :link="route('shopper.settings.index')" :title="__('Settings')" />
        </x-shopper::breadcrumb>

        <x-shopper::heading class="my-6" :title="__('Shipping Methods')" />

        <x-shopper::card class="mt-5">
            {{ $this->table }}
        </x-shopper::card>
    </x-shopper::container>
    </code-snippet>

### Registering Custom Routes

After creating a page, register its route in `routes/shopper.php`:

    <code-snippet name="Register custom page route" lang="php">
    // config/shopper/routes.php
    return [
        'custom_file' => base_path('routes/shopper.php'),
    ];

    // routes/shopper.php
    use App\Livewire\Shopper\Shipping;
    use Illuminate\Support\Facades\Route;

    Route::get('shipping', Shipping::class)->name('shopper.shipping.index');
    </code-snippet>

### Sidebar Navigation System

Shopper uses a sidebar system with 4 default groups. Each group is a class extending `AbstractAdminSidebar`:

- `DashboardSidebar` - Dashboard menu (weight: 1, no heading)
- `CatalogSidebar` - Products, Categories, Collections, Brands (weight: 2)
- `SalesSidebar` - Orders, Discounts (weight: 3)
- `CustomerSidebar` - Customers, Reviews (weight: 4)

Groups with the same name are automatically merged. Groups without a name (empty string or omitted) are merged with the Dashboard group.

To add items to an existing sidebar group or create a new one, create a sidebar extender class:

    <code-snippet name="Create sidebar extender to add menu item" lang="php">
    // app/Sidebar/ShippingSidebar.php
    namespace App\Sidebar;

    use Shopper\Sidebar\AbstractAdminSidebar;
    use Shopper\Sidebar\Contracts\Builder\Group;
    use Shopper\Sidebar\Contracts\Builder\Item;
    use Shopper\Sidebar\Contracts\Builder\Menu;

    class ShippingSidebar extends AbstractAdminSidebar
    {
        public function extendWith(Menu $menu): Menu
        {
            // Add to existing "Catalog" group
            $menu->group(__('shopper::layout.sidebar.catalog'), function (Group $group): void {
                $group->weight(2); // Same weight as CatalogSidebar to merge

                $group->item(__('Shipping Methods'), function (Item $item): void {
                    $item->weight(5); // Position after other items
                    $item->useSpa(); // Enable SPA navigation
                    $item->route('shopper.shipping.index');
                    $item->setIcon('untitledui-truck-01');
                });
            });

            return $menu;
        }
    }
    </code-snippet>

    <code-snippet name="Register sidebar extender in ServiceProvider" lang="php">
    // app/Providers/AppServiceProvider.php
    namespace App\Providers;

    use App\Sidebar\ShippingSidebar;
    use Illuminate\Support\ServiceProvider;
    use Shopper\Sidebar\SidebarBuilder;

    class AppServiceProvider extends ServiceProvider
    {
        public function boot(): void
        {
            // Register the sidebar extender
            $this->app['events']->listen(SidebarBuilder::class, ShippingSidebar::class);
        }
    }
    </code-snippet>

### Creating a New Sidebar Group

To create a completely new sidebar group instead of adding to an existing one:

    <code-snippet name="Create new sidebar group" lang="php">
    // app/Sidebar/CustomSidebar.php
    namespace App\Sidebar;

    use Shopper\Sidebar\AbstractAdminSidebar;
    use Shopper\Sidebar\Contracts\Builder\Group;
    use Shopper\Sidebar\Contracts\Builder\Item;
    use Shopper\Sidebar\Contracts\Builder\Menu;

    class CustomSidebar extends AbstractAdminSidebar
    {
        public function extendWith(Menu $menu): Menu
        {
            $menu->group(__('Logistics'), function (Group $group): void {
                $group->weight(5); // After CustomerSidebar (weight 4)
                $group->setAuthorized();

                $group->item(__('Shipping'), function (Item $item): void {
                    $item->weight(1);
                    $item->setAuthorized($this->user->hasPermissionTo('browse_shipping'));
                    $item->useSpa();
                    $item->route('shopper.shipping.index');
                    $item->setIcon('untitledui-truck-01');
                });

                $group->item(__('Carriers'), function (Item $item): void {
                    $item->weight(2);
                    $item->useSpa();
                    $item->route('shopper.carriers.index');
                    $item->setIcon('untitledui-plane');
                });
            });

            return $menu;
        }
    }
    </code-snippet>

### Adding Items to the Dashboard Group (No Heading)

To add items alongside the Dashboard (without a group heading), omit the group name:

    <code-snippet name="Add item to dashboard group" lang="php">
    $menu->group(function (Group $group): void {
        $group->weight(1);
        $group->setAuthorized();

        $group->item(__('Analytics'), function (Item $item): void {
            $item->weight(2); // After Dashboard (weight 1)
            $item->useSpa();
            $item->route('shopper.analytics.index');
            $item->setIcon('phosphor-chart-line');
        });
    });
    </code-snippet>

### Sidebar Item Options

When configuring sidebar items, use these methods:

- `$item->weight(int)` - Position in group (lower = higher)
- `$item->setAuthorized(bool)` - Show/hide based on condition (use `$this->user->hasPermissionTo()`)
- `$item->useSpa()` - Enable SPA navigation with `wire:navigate`
- `$item->route('route.name')` - Set the route
- `$item->setIcon(icon, iconClass, attributes)` - Configure icon (use Untitled UI icons: `untitledui-*`)
- `$item->setItemClass()`, `$item->setActiveClass()` - CSS classes
- `$item->item()` - Add nested sub-items

### Model Architecture

All models use contracts and can be resolved from the container. Always use contracts when type-hinting:

    <code-snippet name="Resolve Shopper models via contracts" lang="php">
    use Shopper\Core\Models\Contracts\Product as ProductContract;
    use Shopper\Core\Models\Contracts\Order as OrderContract;
    use Shopper\Core\Models\Contracts\Category as CategoryContract;

    // In a controller or service
    public function __construct(
        private ProductContract $productModel,
    ) {}

    // Query products
    $products = resolve(ProductContract::class)::query()
        ->where('is_visible', true)
        ->get();
    </code-snippet>

### Custom Models

To extend Shopper models, create your own model extending the base and update `config/shopper/models.php`:

    <code-snippet name="Create and register custom model" lang="php">
    // app/Models/Product.php
    namespace App\Models;

    use Shopper\Core\Models\Product as ShopperProduct;

    class Product extends ShopperProduct
    {
        public function customRelation()
        {
            return $this->hasMany(CustomModel::class);
        }
    }

    // config/shopper/models.php
    return [
        'product' => App\Models\Product::class,
    ];
    </code-snippet>

### Product Types

Products have types that determine capabilities. Use the `ProductType` enum:

- `ProductType::Standard` - Physical products with shipping, supports variants
- `ProductType::Variant` - Product with variants (sizes, colors)
- `ProductType::Virtual` - Digital products, no shipping, no variants
- `ProductType::External` - Affiliate products, no shipping, no variants

Check capabilities with: `$product->canUseVariants()`, `$product->canUseShipping()`, `$product->isVirtual()`

### Stock Management

Products and variants use the `HasStock` trait. Shopper supports multi-location inventory:

    <code-snippet name="Manage inventory stock" lang="php">
    use Shopper\Core\Models\Product;
    use Shopper\Core\Models\Inventory;

    $product = Product::query()->find($id);
    $inventory = Inventory::query()->where('is_default', true)->first();

    $product->setStock(newQuantity: 100, inventoryId: $inventory->id);
    $product->decreaseStock(inventoryId: $inventory->id, quantity: 5);
    $currentStock = $product->getStock();
    </code-snippet>

### Pricing

Products support multi-currency pricing. Amounts are stored in cents:

    <code-snippet name="Create product price" lang="php">
    use Shopper\Core\Models\Currency;

    $product->prices()->create([
        'currency_id' => Currency::where('code', 'USD')->first()->id,
        'amount' => 2999,         // .99
        'compare_amount' => 3999, // .99 (crossed-out price)
        'cost_amount' => 1500,    // .00 (cost for profit calc)
    ]);
    </code-snippet>

### Categories

Categories support hierarchical structures using LaravelAdjacencyList:

    <code-snippet name="Work with category hierarchy" lang="php">
    $category->children;              // Direct children
    $category->descendantCategories(); // All descendants
    $category->parent;                // Parent category
    $category->ancestors;             // All ancestors
    </code-snippet>

### Orders

Orders use a 3-axis status system:

- **Lifecycle** (`status`): `OrderStatus::New`, `OrderStatus::Processing`, `OrderStatus::Completed`, `OrderStatus::Cancelled`, `OrderStatus::Archived`
- **Payment** (`payment_status`): `PaymentStatus::Pending`, `PaymentStatus::Authorized`, `PaymentStatus::Paid`, `PaymentStatus::PartiallyRefunded`, `PaymentStatus::Refunded`, `PaymentStatus::Voided`
- **Shipping** (`shipping_status`): `ShippingStatus::Unfulfilled`, `ShippingStatus::PartiallyShipped`, `ShippingStatus::Shipped`, `ShippingStatus::PartiallyDelivered`, `ShippingStatus::Delivered`, `ShippingStatus::PartiallyReturned`, `ShippingStatus::Returned`

    <code-snippet name="Query orders with relationships" lang="php">
    use Shopper\Core\Models\Order;
    use Shopper\Core\Enum\OrderStatus;
    use Shopper\Core\Enum\PaymentStatus;

    $orders = Order::with(['items', 'customer', 'shippingAddress', 'zone'])
        ->where('status', OrderStatus::Processing)
        ->where('payment_status', PaymentStatus::Paid)
        ->get();
    </code-snippet>

### Events

Shopper dispatches events for major actions. Listen to these for custom logic:

- Products: `ProductCreated`, `ProductUpdated`, `ProductDeleted`
- Orders: `OrderCreated`, `OrderCompleted`, `OrderPaid`, `OrderShipped`, `OrderCancelled`, `OrderArchived`, `OrderNoteAdded`, `OrderShipmentCreated`, `OrderShipmentDelivered`, `OrderItemCreated`

    <code-snippet name="Listen to Shopper events" lang="php">
    use Shopper\Core\Events\Products\ProductCreated;
    use Shopper\Core\Events\Orders\OrderCreated;

    // In EventServiceProvider
    protected $listen = [
        ProductCreated::class => [YourListener::class],
        OrderCreated::class => [SendOrderConfirmation::class],
    ];
    </code-snippet>

### Extending Navigation (Simple Method)

For quick sidebar customization, use a closure in your ServiceProvider:

    <code-snippet name="Add sidebar item with closure" lang="php">
    // app/Providers/AppServiceProvider.php
    use Illuminate\Support\Facades\Event;
    use Shopper\Sidebar\Contracts\Builder\Group;
    use Shopper\Sidebar\Contracts\Builder\Item;
    use Shopper\Sidebar\SidebarBuilder;

    public function boot(): void
    {
        Event::listen(SidebarBuilder::class, function (SidebarBuilder $sidebar) {
            $sidebar->add(
                $sidebar->getMenu()->group('Custom Section', function (Group $group) {
                    $group->weight(50);
                    $group->setAuthorized();

                    $group->item('My Custom Page', function (Item $item) {
                        $item->weight(1);
                        $item->useSpa();
                        $item->route('shopper.custom.index');
                        $item->setIcon('heroicon-o-star');
                    });
                })
            );
        });
    }
    </code-snippet>

### Override Existing Livewire Components

Shopper components can be overridden via config files in `config/shopper/components/`. Available config files:

- `account.php` - Account/profile components
- `brand.php` - Brand management
- `category.php` - Category management
- `collection.php` - Collection management
- `customer.php` - Customer management
- `dashboard.php` - Dashboard components
- `discount.php` - Discount management
- `order.php` - Order management
- `product.php` - Product management (pages, forms, modals, slide-overs)
- `review.php` - Review management
- `setting.php` - Settings pages and components

Each config file has two sections: `pages` (full page components) and `components` (partial components like forms, modals, slide-overs).

    <code-snippet name="Override existing component" lang="php">
    // Publish the config file first
    // php artisan vendor:publish --tag=shopper-config

    // config/shopper/components/product.php
    return [
        'pages' => [
            'product-index' => App\Livewire\Shopper\Products\Index::class, // Override index page
            'product-edit' => \Shopper\Livewire\Pages\Product\Edit::class, // Keep default
            'variant-edit' => \Shopper\Livewire\Pages\Product\Variant::class,
            'attribute-index' => \Shopper\Livewire\Pages\Attribute\Browse::class,
        ],

        'components' => [
            'products.form.edit' => App\Livewire\Shopper\Products\EditForm::class, // Override form
            'products.form.media' => \Shopper\Livewire\Components\Products\Form\Media::class,
            // ... keep other defaults
        ],
    ];
    </code-snippet>

    <code-snippet name="Create custom component extending base" lang="php">
    // app/Livewire/Shopper/Products/Index.php
    namespace App\Livewire\Shopper\Products;

    use Filament\Tables\Table;
    use Shopper\Livewire\Pages\Product\Index as BaseIndex;

    class Index extends BaseIndex
    {
        public function table(Table $table): Table
        {
            return parent::table($table)
                ->columns([
                    // Add or modify columns
                    ...parent::table($table)->getColumns(),
                    TextColumn::make('custom_field'),
                ])
                ->filters([
                    // Add custom filters
                ]);
        }
    }
    </code-snippet>

### Permissions

Shopper uses Spatie Laravel Permission. Check permissions in Livewire components:

    <code-snippet name="Authorization in components" lang="php">
    // In Livewire component
    public function mount(): void
    {
        $this->authorize('browse_products');
    }

    // In Blade
    @can('add_products')
        <x-filament::button>Add Product</x-filament::button>
    @endcan
    </code-snippet>

### Helper Functions

- `shopper_table('products')` - Returns prefixed table name (e.g., `sh_products`)
- `generate_number()` - Generates order number with configured prefix
- `shopper_fallback_url()` - Returns fallback image URL
- `shopper_setting('shop_name')` - Gets shop setting value
- `shopper()->auth()->user()` - Gets authenticated admin user

### Feature Flags

Enable/disable features in `config/shopper/features.php`:

    <code-snippet name="Check feature flag" lang="php">
    if (\Shopper\Feature::enabled('review')) {
        // Show reviews functionality
    }
    </code-snippet>

### Artisan Commands

- `php artisan shopper:install` - Install Shopper
- `php artisan shopper:user` - Create admin user
- `php artisan shopper:publish` - Publish assets and config
- `php artisan shopper:link` - Create storage symlink
- `php artisan make:shopper-page {PageName}` - Create custom admin page
- `php artisan shopper:component:publish` - Publish specific components
- `php artisan shopper:starter-kit:install` - Install frontend starter kit

### Media Management

Products use Spatie Media Library. Collections are configured in `config/shopper/media.php`:

    <code-snippet name="Add media to product" lang="php">
    // Add thumbnail
    $product->addMedia($file)
        ->toMediaCollection(config('shopper.media.storage.thumbnail_collection'));

    // Add gallery images
    $product->addMedia($file)
        ->toMediaCollection(config('shopper.media.storage.collection_name'));

    // Get URLs
    $thumbnail = $product->getFirstMediaUrl(config('shopper.media.storage.thumbnail_collection'));
    </code-snippet>

### Shopper Blade Components

Use these Shopper Blade components in your custom pages:

    - `<x-shopper::container>` - Main content container
    - `<x-shopper::card>` - Card wrapper
    - `<x-shopper::heading :title="$title">` - Page heading with optional action slot
    - `<x-shopper::breadcrumb>` - Breadcrumb navigation
    - `<x-filament::button>` - Primary button
    - `<x-filament::button color="gray">` - Gray/default button
    - `<x-shopper::empty-card>` - Empty state card
    - `<x-shopper::separator>` - Section separator

### Database Tables

All Shopper tables use a configurable prefix (default: `sh_`). Main tables:

- `sh_products`, `sh_product_variants` - Products and variants
- `sh_orders`, `sh_order_items` - Orders and line items
- `sh_categories`, `sh_brands`, `sh_collections` - Catalog organization
- `sh_customers`, `sh_addresses` - Customer data
- `sh_inventories`, `sh_inventory_histories` - Stock management
- `sh_discounts` - Discount codes and rules

### Testing

When testing Shopper functionality, use factories and respect the model contracts:

    <code-snippet name="Testing with Shopper models" lang="php">
    use Shopper\Core\Models\Product;

    it('can create a product', function () {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'is_visible' => true,
        ]);

        expect($product)->toBeInstanceOf(Product::class)
            ->and($product->is_visible)->toBeTrue();
    });
    </code-snippet>

### Advanced Topics

For detailed guides on building a complete e-commerce site, refer to the documentation at https://docs.laravelshopper.dev:

- **Payment system** - Payment drivers (Manual, Stripe), creating custom gateways, PaymentProcessingService, transaction lifecycle
- **Shipping & carriers** - Shipping drivers (Manual, UPS, FedEx, USPS), custom carrier integration, real-time rate calculation
- **Cart & checkout** - Cart pipeline system, custom pipeline steps, discount application, order conversion
- **Tax system** - Tax zones, tax rates, custom TaxCalculationProvider, VAT vs sales tax
- **Render hooks** - 30+ UI injection points across the admin panel for extending pages without modifying core views
- **Addon development** - BaseAddon contract, AddonManager for registering routes, components, sidebar items, permissions, and assets
- **Events** - Domain events for orders, products, shipments, and cart with `ShouldDispatchAfterCommit` for transaction safety

</laravel-boost-guidelines>
