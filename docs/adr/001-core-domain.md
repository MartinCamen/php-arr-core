# ADR 001 – Introduce a Canonical Core Domain for the *arr Ecosystem

**Date:** 2025-12-25

---

## Context

The *arr ecosystem consists of several closely related services such as:
- Sonarr
- Radarr
- Jellyseerr
- NZBGet
- (future) Prowlarr, Lidarr, Readarr

Although these services are independent, they model largely overlapping domains:
- media (movies, series, episodes)
- downloads and queues
- file sizes and progress
- statuses and lifecycles
- users and requests

Each service exposes these concepts using its own API structure, naming conventions, units and status values.

Historically, each SDK or integration has:
- defined its own DTOs
- mapped statuses locally
- reimplemented value conversions
- duplicated logic across services

This has resulted in:
- duplicated models
- inconsistent semantics
- increased maintenance cost
- fragile integrations
- poor cross-service composability

---

## Decision

We introduce a **canonical core domain package** (`php-arr-core`) that defines:
- shared domain models
- value objects
- enums
- normalization logic

All service-specific SDKs will map their API representations into this core domain.

`php-arr-core` will be:
- framework-agnostic
- transport-agnostic
- service-agnostic

Service SDKs will:
- own HTTP communication
- define API DTOs
- perform mapping into core models
- never reimplement domain semantics

---

## Rationale

### Why not reuse API DTOs directly?

API DTOs are shaped by:
- transport concerns
- API history
- backward compatibility
- service-specific features

They are not stable domain models.

A canonical domain allows:
- consistent semantics
- cross-service composition
- stable abstractions
- simpler consumer code

---

### Why value objects instead of primitives?

Primitive values (int, float, string) lose meaning when reused across contexts.

Value objects:
- encode intent
- centralize conversion logic
- prevent unit mismatches
- improve readability and correctness

---

### Why centralize status normalization?

Status values are:
- inconsistent across services
- frequently extended
- semantically overlapping

Central normalization ensures:
- one source of truth
- consistent behavior
- easier evolution

---

### Why not put mapping logic in core?

Mapping is inherently **boundary logic**.

Putting it in core would:
- introduce service dependencies
- break isolation
- reduce extensibility

Core defines _what exists_.  
SDKs define _how it is obtained_.

---

## Consequences

### Positive

- Reduced duplication across SDKs
- Consistent semantics across services
- Easier cross-service workflows
- Improved developer experience
- Clear separation of concerns
- Stable foundation for future services

---

### Negative

- Additional mapping layer
- Slight upfront complexity
- Need for discipline when adding new concepts

These costs are accepted as they significantly reduce long-term complexity.

---

## Scope

`php-arr-core` will initially cover only concepts that are:
- shared by multiple services
- stable across implementations
- central to common workflows

Service-specific features remain in their respective SDKs.

---

## Alternatives Considered

### 1. Independent SDKs with local DTOs

Rejected due to:
- duplication
- inconsistency
- maintenance burden

---

### 2. One unified SDK for all services

Rejected due to:
- tight coupling
- lack of service autonomy
- poor extensibility

---

### 3. Direct reuse of OpenAPI-generated models

Rejected due to:
- unstable semantics
- transport leakage
- weak abstraction

---

## Decision Outcome

This decision establishes `php-arr-core` as the **single source of truth**  
for shared domain semantics across the *arr ecosystem.

All future SDKs and integrations are expected to conform to this model.

---

## Follow-up Actions

1. Define minimal viable core domain (v1)
2. Implement value objects and enums
3. Establish mapping contracts
4. Align existing SDKs incrementally


---

## References

- Domain-Driven Design (Evans)
- Value Objects pattern
- Hexagonal / Clean Architecture
