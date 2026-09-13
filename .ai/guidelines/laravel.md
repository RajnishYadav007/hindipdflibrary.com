# AI Guidelines: Laravel Engineering Standards

## 1. Application Structure
- Follow standard Laravel 13 directory conventions.
- Business domain logic belongs in Action classes (`app/Actions/Book/...`) or Service classes (`app/Services/...`), not bloated controllers or Livewire components.
- Thin controllers: Validate request ➔ Call Action/Service ➔ Return Inertia/Blade/API response.

## 2. Eloquent Best Practices
- Never execute queries inside Blade loops.
- Use explicit query scopes (`scopePublished`, `scopeFeatured`).
- Relationships must explicitly specify return types:
  ```php
  public function author(): BelongsTo
  {
      return $this->belongsTo(Author::class);
  }
  ```

## 3. Livewire 4 Guidelines
- Keep component state minimal. Avoid storing entire Eloquent collections in component public properties (which bloats wire payloads); store IDs or use computed properties (`#[Computed]`).
- Use action listeners and events (`$this->dispatch('book-reviewed')`) for decoupled UI updates.

## 4. Filament 5 Guidelines
- Use standard Filament form schemas and table column definitions.
- Protect resource actions using Spatie Permission checks:
  ```php
  public static function canCreate(): bool
  {
      return auth()->user()->can('create_books');
  }
  ```
