# Package Improvements & Refactoring Plan

To ensure the package is a robust, enterprise-grade framework-agnostic library, the following improvements are required:

## 1. Adopt Standard PSR Interfaces
Currently, the package relies on custom interfaces and classes for core architectural components (e.g., HTTP, Container, Events). We must implement PHP Standard Recommendations (PSR) to ensure interoperability with modern frameworks (Symfony, Slim, Laravel, etc.):
- **HTTP Layer (PSR-7 / PSR-15):** Replace custom `Request` and `Response` with PSR-7 HTTP message interfaces (`psr/http-message`).
- **Dependency Injection (PSR-11):** Ensure the package's `Container` implements `Psr\Container\ContainerInterface` (`psr/container`).
- **Event Dispatching (PSR-14):** Ensure the `EventDispatcher` implements `Psr\EventDispatcher\EventDispatcherInterface` (`psr/event-dispatcher`).

## 2. Database and Storage Abstraction (Repository Pattern)
The current models (e.g., `User`, `LoginHistory`) mimic Laravel's Eloquent ORM using static methods like `where()` and `get()`. Without Laravel, these methods have no underlying database engine. 
- **Action:** Introduce **Contracts (Interfaces)** like `UserRepositoryInterface` and `TokenRepositoryInterface`. The business logic must depend entirely on these contracts rather than concrete models. The consuming application will bind its own database implementation (Doctrine, Eloquent, plain PDO) to these interfaces.

## 3. Restore and Rewrite Tests
The previous Laravel-bound tests (`orchestra/testbench`) were deleted to make the test suite pass in a standalone environment.
- **Action:** Restore the core unit tests (`TOTPTest`, `TokenManagerTest`, `UserTrackerTest`) and rewrite them using **Mock objects** and standard PHPUnit techniques to verify business logic independently of any framework.

## 4. Provide Framework Bridges
To maintain the "works as a Laravel drop-in" promise from the original design:
- **Action:** Create a `Bridge/Laravel` directory and move the `AuthMicroserviceServiceProvider` there. This allows the core `src/` to remain purely agnostic while offering a seamless integration path for Laravel users.
