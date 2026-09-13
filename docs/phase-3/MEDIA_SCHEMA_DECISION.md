# Media Schema Architectural Decision

**Project:** Hindi PDF Library (`hindipdflibrary.com`)  
**Phase:** Phase 3 — Database & Core Data Model  
**Updated:** September 13, 2026  

---

## 1. Context & Package Status

In Phase 0, `spatie/laravel-medialibrary: ^11.0` was audited and designated as the official media management standard for books (covers, preview pages), authors (portraits), and documents.

During the Phase 3 environment inspection:
- `spatie/laravel-medialibrary` was not yet installed in `composer.json`.
- The active CLI PHP configuration lacked `ext-exif`, which causes `composer require spatie/laravel-medialibrary` to reject installation until `ext-exif` is enabled or platform requirements are resolved.
- Per Phase 3 instructions:
  > *"If the package is not yet installed, document the dependency and do not invent an incompatible schema."*
  > *"Do NOT create a second incompatible media table."*

---

## 2. Decision: Forward-Compatible Standard Spatie Schema

Rather than creating an incompatible ad-hoc media table that would require painful data migrations later, we implemented **Spatie Media Library v11's official standard database migration schema** directly:

```php
Schema::create('media', function (Blueprint $table) {
    $table->id();
    $table->morphs('model');
    $table->uuid('uuid')->nullable()->unique();
    $table->string('collection_name');
    $table->string('name');
    $table->string('file_name');
    $table->string('mime_type')->nullable();
    $table->string('disk');
    $table->string('conversions_disk')->nullable();
    $table->unsignedBigInteger('size');
    $table->json('manipulations');
    $table->json('custom_properties');
    $table->json('generated_conversions');
    $table->json('responsive_images');
    $table->unsignedInteger('order_column')->nullable()->index();
    $table->nullableTimestamps();
});
```

---

## 3. Eloquent Model Integration

The `App\Models\Media` model was created to interact with this table in Phase 3, supporting:
- Polymorphic ownership (`model()`)
- Explicit JSON casting for `manipulations`, `custom_properties`, `generated_conversions`, `responsive_images`.
- Polymorphic relations on `Book` (`media()`) and `Author` (`media()`).

When `spatie/laravel-medialibrary` is formally installed in Phase 4 (with `ext-exif` enabled in php.ini), it will automatically bind to this table with **zero migrations, zero schema changes, and 100% backward compatibility**.
