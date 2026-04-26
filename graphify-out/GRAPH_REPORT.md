# Graph Report - .  (2026-04-26)

## Corpus Check
- Corpus is ~30,379 words - fits in a single context window. You may not need a graph.

## Summary
- 454 nodes · 638 edges · 36 communities detected
- Extraction: 73% EXTRACTED · 27% INFERRED · 0% AMBIGUOUS · INFERRED: 172 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- [[_COMMUNITY_PaymentStripe Integration|Payment/Stripe Integration]]
- [[_COMMUNITY_Product Catalog|Product Catalog]]
- [[_COMMUNITY_Address Controller|Address Controller]]
- [[_COMMUNITY_Address Management UI|Address Management UI]]
- [[_COMMUNITY_Category Management|Category Management]]
- [[_COMMUNITY_OrderReview Processing|Order/Review Processing]]
- [[_COMMUNITY_ZoneShipping|Zone/Shipping]]
- [[_COMMUNITY_CartE-commerce|Cart/E-commerce]]
- [[_COMMUNITY_Cart Service|Cart Service]]
- [[_COMMUNITY_FormsValidation|Forms/Validation]]
- [[_COMMUNITY_AuthFortify|Auth/Fortify]]
- [[_COMMUNITY_User Management|User Management]]
- [[_COMMUNITY_Community 12|Community 12]]
- [[_COMMUNITY_Community 13|Community 13]]
- [[_COMMUNITY_Community 14|Community 14]]
- [[_COMMUNITY_Community 15|Community 15]]
- [[_COMMUNITY_Community 16|Community 16]]
- [[_COMMUNITY_Community 17|Community 17]]
- [[_COMMUNITY_Community 18|Community 18]]
- [[_COMMUNITY_Community 19|Community 19]]
- [[_COMMUNITY_Community 20|Community 20]]
- [[_COMMUNITY_Community 21|Community 21]]
- [[_COMMUNITY_Community 22|Community 22]]
- [[_COMMUNITY_Community 23|Community 23]]
- [[_COMMUNITY_Community 24|Community 24]]
- [[_COMMUNITY_Community 25|Community 25]]
- [[_COMMUNITY_Community 26|Community 26]]
- [[_COMMUNITY_Community 27|Community 27]]
- [[_COMMUNITY_Community 28|Community 28]]
- [[_COMMUNITY_Community 29|Community 29]]
- [[_COMMUNITY_Community 30|Community 30]]
- [[_COMMUNITY_Community 31|Community 31]]
- [[_COMMUNITY_Community 32|Community 32]]
- [[_COMMUNITY_Community 33|Community 33]]
- [[_COMMUNITY_Community 34|Community 34]]
- [[_COMMUNITY_Community 35|Community 35]]

## God Nodes (most connected - your core abstractions)
1. `User` - 36 edges
2. `CartService` - 26 edges
3. `current_currency()` - 15 edges
4. `Checkout` - 15 edges
5. `CheckoutController` - 14 edges
6. `ProductController` - 12 edges
7. `ZoneSessionManager` - 12 edges
8. `CartController` - 10 edges
9. `StripeService` - 9 edges
10. `ProcessPaymentJob` - 9 edges

## Surprising Connections (you probably didn't know these)
- `current_currency()` --calls--> `ZoneSessionManager`  [INFERRED]
  /home/eshare/wordpress-6.8.1/ecommerse/patkhazana/app/helpers.php → /home/eshare/wordpress-6.8.1/ecommerse/patkhazana/app/Actions/ZoneSessionManager.php
- `getFormattedPrice()` --calls--> `current_currency()`  [INFERRED]
  /home/eshare/wordpress-6.8.1/ecommerse/patkhazana/app/Traits/HasProductPricing.php → /home/eshare/wordpress-6.8.1/ecommerse/patkhazana/app/helpers.php
- `current_tax_label()` --calls--> `ZoneSessionManager`  [INFERRED]
  /home/eshare/wordpress-6.8.1/ecommerse/patkhazana/app/helpers.php → /home/eshare/wordpress-6.8.1/ecommerse/patkhazana/app/Actions/ZoneSessionManager.php
- `cartSession()` --calls--> `ZoneSessionManager`  [INFERRED]
  /home/eshare/wordpress-6.8.1/ecommerse/patkhazana/app/helpers.php → /home/eshare/wordpress-6.8.1/ecommerse/patkhazana/app/Actions/ZoneSessionManager.php
- `cartSession()` --calls--> `Channel`  [INFERRED]
  /home/eshare/wordpress-6.8.1/ecommerse/patkhazana/app/helpers.php → /home/eshare/wordpress-6.8.1/ecommerse/patkhazana/app/Models/Channel.php

## Communities

### Community 0 - "Payment/Stripe Integration"
Cohesion: 0.08
Nodes (5): CheckoutController, Logout, PaymentController, StripePayment, StripeService

### Community 1 - "Product Catalog"
Cohesion: 0.07
Nodes (7): CategoryShow, FeaturedProducts, Product, ProductController, ProductIndex, SearchProducts, Wishlist

### Community 2 - "Address Controller"
Cohesion: 0.1
Nodes (5): AddressController, AuthController, OrderController, ProfileController, User

### Community 3 - "Address Management UI"
Cohesion: 0.08
Nodes (5): Addresses, AddToCart, Checkout, ProductShow, ProductVariant

### Community 4 - "Category Management"
Cohesion: 0.07
Nodes (7): Category, CategoryController, CategoryIndex, CategoryResource, ShopByCategory, StoreFooter, StoreHeader

### Community 5 - "Order/Review Processing"
Cohesion: 0.09
Nodes (5): AddProductReviewAction, Cart, CreateOrder, ProcessPaymentJob, StripeWebhookController

### Community 6 - "Zone/Shipping"
Cohesion: 0.1
Nodes (7): BannerController, BuildShippingPackages, Channel, cartSession(), current_tax_label(), ZoneSelector, ZoneSessionManager

### Community 7 - "Cart/E-commerce"
Cohesion: 0.13
Nodes (6): CartController, CollectionShow, getFormattedPrice(), current_currency(), ProductResource, ProductVariantResource

### Community 8 - "Cart Service"
Cohesion: 0.17
Nodes (1): CartService

### Community 9 - "Forms/Validation"
Cohesion: 0.16
Nodes (5): AddressForm, CreateNewUser, emailRules(), nameRules(), profileRules()

### Community 10 - "Auth/Fortify"
Cohesion: 0.27
Nodes (2): OrderItemResource, OrderResource

### Community 11 - "User Management"
Cohesion: 0.25
Nodes (2): CountryByZoneData, GetCountriesByZone

### Community 12 - "Community 12"
Cohesion: 0.43
Nodes (1): FortifyServiceProvider

### Community 13 - "Community 13"
Cohesion: 0.48
Nodes (1): BuildVariantOptions

### Community 14 - "Community 14"
Cohesion: 0.47
Nodes (1): AppServiceProvider

### Community 15 - "Community 15"
Cohesion: 0.53
Nodes (1): FetchDeliveryRates

### Community 16 - "Community 16"
Cohesion: 0.5
Nodes (1): CartCount

### Community 17 - "Community 17"
Cohesion: 0.4
Nodes (1): OrderConfirmed

### Community 18 - "Community 18"
Cohesion: 0.4
Nodes (1): OrderPaymentFailed

### Community 19 - "Community 19"
Cohesion: 0.5
Nodes (2): CartItemResource, CartResource

### Community 20 - "Community 20"
Cohesion: 0.5
Nodes (1): FeaturedCollections

### Community 21 - "Community 21"
Cohesion: 0.5
Nodes (1): AddressData

### Community 22 - "Community 22"
Cohesion: 0.5
Nodes (1): VoltServiceProvider

### Community 23 - "Community 23"
Cohesion: 0.5
Nodes (1): OrderCollection

### Community 24 - "Community 24"
Cohesion: 0.67
Nodes (1): StoreBottomNav

### Community 25 - "Community 25"
Cohesion: 0.67
Nodes (1): Home

### Community 26 - "Community 26"
Cohesion: 0.67
Nodes (1): PriceData

### Community 27 - "Community 27"
Cohesion: 0.67
Nodes (1): ProductReviewsData

### Community 28 - "Community 28"
Cohesion: 0.67
Nodes (1): OrderAddressResource

### Community 29 - "Community 29"
Cohesion: 0.67
Nodes (1): ProductCollection

### Community 30 - "Community 30"
Cohesion: 0.67
Nodes (1): BrandResource

### Community 31 - "Community 31"
Cohesion: 0.67
Nodes (1): FetchPaymentMethods

### Community 32 - "Community 32"
Cohesion: 0.67
Nodes (1): ResolveZoneForCountry

### Community 33 - "Community 33"
Cohesion: 0.67
Nodes (1): ResolveVariantAvailability

### Community 34 - "Community 34"
Cohesion: 1.0
Nodes (1): CheckoutSession

### Community 35 - "Community 35"
Cohesion: 1.0
Nodes (1): Controller

## Knowledge Gaps
- **2 isolated node(s):** `CheckoutSession`, `Controller`
  These have ≤1 connection - possible missing edges or undocumented components.
- **Thin community `Cart Service`** (24 nodes): `CartService.php`, `.store()`, `CartService`, `.addItem()`, `.__construct()`, `.findOrCreateCart()`, `.getCartId()`, `.getCouponCode()`, `.getDiscount()`, `.getItems()`, `.getProductPrice()`, `.getProductThumbnail()`, `.getShippingEstimate()`, `.getStockQuantity()`, `.getSubtotal()`, `.getTotal()`, `.getVariantName()`, `.loadItems()`, `.mergeGuestCart()`, `.removeItem()`, `.resolveCart()`, `.setGuestId()`, `.toArray()`, `.updateItem()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Auth/Fortify`** (11 nodes): `OrderItemResource.php`, `OrderResource.php`, `OrderItemResource`, `.getItemThumbnail()`, `.toArray()`, `OrderResource`, `.buildTimeline()`, `.getFirstItemThumbnail()`, `.getTrackingNumber()`, `.getTrackingUrl()`, `.toArray()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `User Management`** (8 nodes): `GetCountriesByZone.php`, `CountryByZoneData.php`, `CountryByZoneData`, `.__construct()`, `.fromArray()`, `GetCountriesByZone`, `.flush()`, `.handle()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 12`** (7 nodes): `FortifyServiceProvider.php`, `FortifyServiceProvider`, `.boot()`, `.configureActions()`, `.configureRateLimiting()`, `.configureViews()`, `.register()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 13`** (7 nodes): `BuildVariantOptions.php`, `BuildVariantOptions`, `.buildProductOptions()`, `.buildVariantIndex()`, `.buildVariantMap()`, `.handle()`, `.makeVariantKey()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 14`** (6 nodes): `AppServiceProvider.php`, `AppServiceProvider`, `.boot()`, `.configureBladeNamespaces()`, `.configureDefaults()`, `.register()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 15`** (6 nodes): `FetchDeliveryRates.php`, `FetchDeliveryRates`, `.buildDestinationAddress()`, `.buildOriginAddress()`, `.formatRates()`, `.handle()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 16`** (5 nodes): `CartCount.php`, `CartCount`, `.mount()`, `.refreshCount()`, `.render()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 17`** (5 nodes): `OrderConfirmed.php`, `OrderConfirmed`, `.__construct()`, `.content()`, `.envelope()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 18`** (5 nodes): `OrderPaymentFailed.php`, `OrderPaymentFailed`, `.__construct()`, `.content()`, `.envelope()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 19`** (5 nodes): `CartResource.php`, `CartItemResource`, `.toArray()`, `CartResource`, `.toArray()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 20`** (4 nodes): `FeaturedCollections.php`, `FeaturedCollections`, `.collections()`, `.render()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 21`** (4 nodes): `AddressData`, `.__construct()`, `.fromArray()`, `AddressData.php`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 22`** (4 nodes): `VoltServiceProvider.php`, `VoltServiceProvider`, `.boot()`, `.register()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 23`** (4 nodes): `OrderCollection.php`, `OrderCollection`, `.paginationInformation()`, `.toArray()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 24`** (3 nodes): `StoreBottomNav.php`, `StoreBottomNav`, `.render()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 25`** (3 nodes): `Home.php`, `Home`, `.render()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 26`** (3 nodes): `PriceData.php`, `PriceData`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 27`** (3 nodes): `ProductReviewsData.php`, `ProductReviewsData`, `.__construct()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 28`** (3 nodes): `OrderAddressResource.php`, `OrderAddressResource`, `.toArray()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 29`** (3 nodes): `ProductCollection.php`, `ProductCollection`, `.toArray()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 30`** (3 nodes): `BrandResource.php`, `BrandResource`, `.toArray()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 31`** (3 nodes): `FetchPaymentMethods.php`, `FetchPaymentMethods`, `.handle()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 32`** (3 nodes): `ResolveZoneForCountry.php`, `ResolveZoneForCountry`, `.handle()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 33`** (3 nodes): `ResolveVariantAvailability.php`, `ResolveVariantAvailability`, `.handle()`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 34`** (2 nodes): `CheckoutSession.php`, `CheckoutSession`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.
- **Thin community `Community 35`** (2 nodes): `Controller.php`, `Controller`
  Too small to be a meaningful cluster - may be noise or needs more connections extracted.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `User` connect `Address Controller` to `Payment/Stripe Integration`, `Product Catalog`, `Address Management UI`, `Cart/E-commerce`, `Cart Service`, `Forms/Validation`, `Community 15`?**
  _High betweenness centrality (0.140) - this node is a cross-community bridge._
- **Why does `current_currency()` connect `Cart/E-commerce` to `Product Catalog`, `Address Management UI`, `Zone/Shipping`?**
  _High betweenness centrality (0.094) - this node is a cross-community bridge._
- **Why does `CategoryResource` connect `Category Management` to `Cart/E-commerce`?**
  _High betweenness centrality (0.067) - this node is a cross-community bridge._
- **Are the 33 inferred relationships involving `User` (e.g. with `.getCustomerId()` and `.savedAddresses()`) actually correct?**
  _`User` has 33 INFERRED edges - model-reasoned connections that need verification._
- **Are the 13 inferred relationships involving `current_currency()` (e.g. with `ZoneSessionManager` and `.withCurrentPrices()`) actually correct?**
  _`current_currency()` has 13 INFERRED edges - model-reasoned connections that need verification._
- **What connects `CheckoutSession`, `Controller` to the rest of the system?**
  _2 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Payment/Stripe Integration` be split into smaller, more focused modules?**
  _Cohesion score 0.08 - nodes in this community are weakly interconnected._