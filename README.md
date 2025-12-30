# php-arr-core

A canonical domain model for the *arr ecosystem.

`php-arr-core` provides **shared domain models, value objects, enums and normalization logic**
used across *arr service SDKs such as Sonarr, Radarr, Jellyseerr and NZBGet.

The goal is to eliminate duplicated DTOs, inconsistent status handling and ad-hoc mappings
between services that conceptually model the same things.

---

## Why does this exist?

The *arr ecosystem is highly cohesive:

- Sonarr, Radarr, Jellyseerr and NZBGet all model:
    - media
    - downloads
    - queues
    - file sizes
    - progress
    - statuses
- Yet each service exposes these concepts using:
    - different naming
    - different units
    - different status values
    - different lifecycle semantics

This results in:
- duplicated DTOs
- repeated status mapping logic
- fragile integrations
- inconsistent developer experience

`php-arr-core` solves this by defining **one canonical domain model**
that all *arr SDKs map to.

---

## What this package is

✅ Pure PHP (no framework dependencies)
✅ Canonical domain models
✅ Value objects (FileSize, Duration, Progress, etc.)
✅ Normalized enums and statuses
✅ Mapping helpers and contracts

---

## What this package is NOT

❌ HTTP clients
❌ API DTOs
❌ Guzzle, Curl, or PSR-18 implementations
❌ Service-specific assumptions

---

## Core design principles

### 1. Canonical domain > API representation

APIs change. Domains evolve slowly.

This package models **what things are**, not how services expose them.

---

### 2. Value objects over primitives

Anything that:
- has units
- appears in multiple services
- requires conversion or logic

…is modeled as a value object.

Examples:
- `FileSize`
- `Duration`
- `Progress`

---

### 3. Status normalization is centralized

Each service uses its own status vocabulary.

All normalization happens **once**, in core.

SDKs should never contain `switch` or `if` blocks for statuses.

---

### 4. Mapping happens at the boundary

Service SDKs are responsible for mapping their API DTOs
into `php-arr-core` domain objects.

Core never depends on service-specific code.

---

## Package structure

```text
src/
├── Domain/
│   ├── Media/
│   ├── Download/
│   ├── Request/
│   └── User/
├── ValueObject/
│   ├── FileSize.php
│   ├── Duration.php
│   ├── Progress.php
│   └── ArrId.php
├── Enum/
│   ├── MediaStatus.php
│   ├── DownloadStatus.php
│   └── Service.php
├── Mapping/
│   ├── StatusNormalizer.php
│   └── ServiceCapabilities.php
```

---

## Example usage

```php
use MartinCamen\ArrCore\Domain\Download\DownloadItem;
use MartinCamen\ArrCore\ValueObject\FileSize;
use MartinCamen\ArrCore\ValueObject\Progress;
use MartinCamen\ArrCore\Enum\DownloadStatus;

$item = new DownloadItem(
    id: ArrId::fromInt(123),
    name: 'Example.Movie.2024',
    size: FileSize::fromGB(8.5),
    progress: Progress::fromPercentage(42),
    status: DownloadStatus::Downloading
);
```

---
## How service SDKs integrate

Each service SDK:

1. Defines API-specific DTOs
2. Fetches raw data via HTTP
3. Maps DTOs → `php-arr-core` domain models

Example:

```php
$queueItems = $sonarr->queue();

$coreItems = array_map(
    fn($item) => SonarrToCoreMapper::mapQueueItem($item),
    $queueItems
);
```

---
## Supported services

This package is designed to be used by:
- Sonarr SDK
- Radarr SDK
- Jellyseerr SDK
- NZBGet SDK
- Prowlarr SDK (planned)

---
## Versioning & stability

`php-arr-core` follows semantic versioning.

Breaking changes only occur when:
- a domain concept changes
- a value object’s behavior changes

New services and fields should be additive whenever possible.

---

## Contributing

Contributions are welcome, especially:
- new value objects
- improved normalization
- additional service mappings


Before adding new models, consider:

> Is this a domain concept or an API detail?

If unsure, open an issue.
