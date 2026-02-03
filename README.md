# Hexagonal Demo - Gestion des Cadeaux

Application de démonstration de l'architecture hexagonale avec Symfony

## Prérequis

**Principe D (Dependency Inversion)** : Les dépendances pointent vers l'intérieur. Le Domain définit les interfaces (Ports), l'Infrastructure les implémente (Adapters).

**Règle** : Domain ne dépend de rien. Application dépend du Domain. Infrastructure implémente les Ports du Domain.

**Concrètement** : Seule l'Infrastructure dépend de Symfony et Doctrine. Domain et Application sont en PHP pur.

---

## Table des Matières

1. [Introduction](#1-introduction)
2. [Architecture](#2-architecture)
3. [Installation](#3-installation)
4. [Utilisation](#4-utilisation)
5. [Structure du Projet](#5-structure-du-projet)
6. [Tests](#6-tests)
7. [Documentation](#7-documentation)

---

## 1. Introduction

### 1.1 Contexte

Cette application illustre l'implémentation d'une architecture hexagonale (Ports & Adapters) avec Symfony. Elle utilise le bundle [hexagonal-maker-bundle](https://github.com/ahmed-bhs/hexagonal-maker-bundle) pour générer automatiquement la structure du code.

### 1.2 Domaine Métier

Le système illustre deux bounded contexts :

**Gift (Gestion des cadeaux)**
- Gestion d'habitants avec leurs caractéristiques (âge, email)
- Catalogue de cadeaux avec gestion de stock
- Attribution de cadeaux aux habitants
- Demandes de cadeaux avec workflow d'approbation

**Order (E-commerce)**
- Catalogue produits avec prix et stock
- Panier d'achat avec ajout/suppression d'articles
- Commandes avec state machine (pending → confirmed → shipped → delivered)
- Événements domaine pour chaque transition d'état

### 1.3 Patterns Appliqués

- **Architecture Hexagonale** : Séparation Domain / Application / Infrastructure
- **Domain-Driven Design** : Entities, Value Objects, Repositories, Domain Events
- **CQRS** : Séparation Commands / Queries
- **Event Sourcing** : Événements domaine avec EventStore
- **Dependency Inversion** : Interfaces (Ports) définies dans le Domain
- **JWT Authentication** : Authentification stateless avec JSON Web Tokens

---

## 2. Architecture

### 2.1 Structure Hexagonale

Le projet suit une structure en couches concentriques :

```
Domain (centre)
  → Application (use cases)
    → Infrastructure (adapters)
      → UI (primary adapters)
```

### 2.2 Organisation du Code

```
src/
├── Gift/                                                    # Bounded Context 1 : Gestion des cadeaux
│   ├── Attribution/                                         # Sous-domaine : Attribution de cadeaux
│   │   ├── Domain/                                          # Logique métier pure (aucune dépendance)
│   │   │   ├── Event/
│   │   │   │   └── GiftAttributed.php                       # Domain Event : cadeau attribué
│   │   │   ├── Model/                                       # Entities DDD (pur PHP sans Doctrine)
│   │   │   │   ├── Gift.php                                 # Entity : Cadeau avec gestion stock
│   │   │   │   ├── GiftAttribution.php                      # Aggregate Root : Attribution d'un cadeau à un résident
│   │   │   │   └── Resident.php                             # Entity : Résident destinataire
│   │   │   ├── Port/                                        # Interfaces (contrats) définies par le Domain
│   │   │   │   ├── In/                                      # Ports primaires (use cases)
│   │   │   │   │   ├── AttributeGiftUseCaseInterface.php
│   │   │   │   │   ├── GetGiftsUseCaseInterface.php
│   │   │   │   │   ├── GetResidentsUseCaseInterface.php
│   │   │   │   │   └── GetStatisticsUseCaseInterface.php
│   │   │   │   └── Out/                                     # Ports secondaires (persistance)
│   │   │   │       ├── GiftAttributionRepositoryInterface.php
│   │   │   │       ├── GiftRepositoryInterface.php
│   │   │   │       └── ResidentRepositoryInterface.php
│   │   │   └── ValueObject/                                 # Objets immuables définis par leurs valeurs
│   │   │       ├── Age.php                                  # VO : Age avec règles métier
│   │   │       ├── GiftId.php                               # VO : Identifiant typé pour Gift
│   │   │       └── ResidentId.php                           # VO : Identifiant typé pour Resident
│   │   ├── Application/                                     # Use Cases (orchestration Domain)
│   │   │   ├── Command/
│   │   │   │   └── AttributeGift/                           # Use Case : Attribuer un cadeau
│   │   │   │       ├── AttributeGiftCommand.php             # DTO d'entrée (write operation)
│   │   │   │       └── AttributeGiftCommandHandler.php      # Orchestration logique métier
│   │   │   ├── DTO/                                         # Data Transfer Objects
│   │   │   │   ├── AttributionResultDTO.php
│   │   │   │   ├── GiftDTO.php
│   │   │   │   └── ResidentDTO.php
│   │   │   ├── Exception/
│   │   │   │   ├── GiftAttributionFailedException.php
│   │   │   │   └── NoEligibleGiftException.php
│   │   │   ├── Query/
│   │   │   │   ├── GetGifts/                                # Use Case : Lister les cadeaux
│   │   │   │   │   ├── GetGiftsQuery.php
│   │   │   │   │   ├── GetGiftsQueryHandler.php
│   │   │   │   │   └── GetGiftsResponse.php
│   │   │   │   ├── GetResidents/                            # Use Case : Lister les résidents (pagination, recherche)
│   │   │   │   │   ├── GetResidentsQuery.php
│   │   │   │   │   ├── GetResidentsQueryHandler.php
│   │   │   │   │   └── GetResidentsResponse.php
│   │   │   │   └── GetStatistics/                           # Use Case : Statistiques globales
│   │   │   │       ├── GetStatisticsQuery.php
│   │   │   │       ├── GetStatisticsQueryHandler.php
│   │   │   │       └── GetStatisticsResponse.php
│   │   │   └── Service/
│   │   │       └── AutomaticGiftAttributionService.php      # Service d'attribution automatique
│   │   └── Infrastructure/
│   │       └── Adapter/
│   │           ├── In/                                      # Primary Adapters (points d'entrée)
│   │           │   ├── Http/Controller/
│   │           │   │   ├── AttributionController.php        # API REST attribution
│   │           │   │   └── AutomaticAttributionController.php
│   │           │   └── Web/Controller/
│   │           │       ├── ListGiftsController.php          # Contrôleur liste cadeaux
│   │           │       └── ListResidentsController.php      # Contrôleur liste résidents
│   │           └── Out/                                     # Secondary Adapters (implémentations)
│   │               ├── EventSubscriber/
│   │               │   └── GiftAttributedSubscriber.php     # Réagit à GiftAttributed event
│   │               ├── Messaging/
│   │               │   └── GenerateGiftCertificate/
│   │               │       ├── GenerateGiftCertificateCommand.php
│   │               │       └── GenerateGiftCertificateCommandHandler.php
│   │               └── Persistence/Doctrine/
│   │                   ├── DoctrineGiftAttributionRepository.php
│   │                   ├── DoctrineGiftRepository.php
│   │                   ├── DoctrineResidentRepository.php
│   │                   ├── Orm/Mapping/                     # Mapping Doctrine XML
│   │                   │   ├── GiftAttribution.orm.xml
│   │                   │   ├── Gift.orm.xml
│   │                   │   └── Resident.orm.xml
│   │                   └── Type/                            # Types Doctrine custom pour Value Objects
│   │                       ├── AgeType.php
│   │                       └── ResidentIdType.php
│   │
│   └── Request/                                             # Sous-domaine : Demandes de cadeaux
│       ├── Domain/
│       │   ├── Event/
│       │   │   └── GiftRequestSubmitted.php                 # Domain Event : demande soumise
│       │   ├── Model/
│       │   │   └── GiftRequest.php                          # Aggregate Root : Demande de cadeau
│       │   └── Port/
│       │       ├── In/
│       │       │   └── SubmitGiftRequestUseCaseInterface.php
│       │       └── Out/
│       │           └── GiftRequestRepositoryInterface.php
│       ├── Application/
│       │   ├── Command/
│       │   │   └── SubmitGiftRequest/                       # Use Case : Soumettre une demande
│       │   │       ├── SubmitGiftRequestCommand.php
│       │   │       └── SubmitGiftRequestCommandHandler.php
│       │   ├── DTO/
│       │   │   └── GiftRequestSummaryDTO.php
│       │   └── Exception/
│       │       └── InvalidGiftRequestException.php
│       └── Infrastructure/
│           └── Adapter/
│               ├── In/Web/
│               │   ├── Controller/
│               │   │   └── GiftRequestFormController.php    # Contrôleur formulaire demande
│               │   └── Form/
│               │       └── GiftRequestType.php              # Type de formulaire Symfony
│               └── Out/
│                   ├── EventSubscriber/
│                   │   └── GiftRequestSubmittedSubscriber.php
│                   └── Persistence/Doctrine/
│                       └── DoctrineGiftRequestRepository.php
│
├── Security/                                                # Bounded Context 2 : Sécurité
│   ├── Authentication/                                      # Sous-domaine : Authentification JWT
│   │   ├── Domain/Port/
│   │   │   ├── In/
│   │   │   │   ├── GetCurrentUserUseCaseInterface.php
│   │   │   │   └── LoginUseCaseInterface.php
│   │   │   └── Out/
│   │   │       └── TokenGeneratorInterface.php
│   │   ├── Application/
│   │   │   ├── Command/Login/
│   │   │   │   ├── LoginCommand.php
│   │   │   │   └── LoginCommandHandler.php
│   │   │   ├── DTO/
│   │   │   │   └── TokenDTO.php
│   │   │   ├── Exception/
│   │   │   │   └── InvalidCredentialsException.php
│   │   │   └── Query/GetCurrentUser/
│   │   │       ├── GetCurrentUserQuery.php
│   │   │       └── GetCurrentUserQueryHandler.php
│   │   └── Infrastructure/Adapter/
│   │       ├── In/Http/Controller/
│   │       │   └── AuthController.php                       # Contrôleur API auth (login, register, me)
│   │       └── Out/
│   │           ├── Jwt/
│   │           │   └── FirebaseJwtTokenGenerator.php         # Implémente TokenGeneratorInterface
│   │           └── Security/
│   │               ├── JwtAuthenticator.php                  # Symfony Security authenticator
│   │               └── SymfonyUserAdapter.php
│   │
│   └── User/                                                # Sous-domaine : Gestion utilisateurs
│       ├── Domain/
│       │   ├── Event/
│       │   │   └── UserRegistered.php                       # Domain Event : utilisateur inscrit
│       │   ├── Model/
│       │   │   └── User.php                                 # Entity : Utilisateur
│       │   ├── Port/
│       │   │   ├── In/
│       │   │   │   └── RegisterUserUseCaseInterface.php
│       │   │   └── Out/
│       │   │       ├── PasswordHasherInterface.php
│       │   │       └── UserRepositoryInterface.php
│       │   └── ValueObject/
│       │       ├── Email.php                                # VO : Email utilisateur
│       │       ├── HashedPassword.php                       # VO : Mot de passe hashé
│       │       └── UserId.php                               # VO : Identifiant utilisateur
│       ├── Application/
│       │   ├── Command/RegisterUser/
│       │   │   ├── RegisterUserCommand.php
│       │   │   └── RegisterUserCommandHandler.php
│       │   ├── DTO/
│       │   │   └── UserDTO.php
│       │   └── Exception/
│       │       └── EmailAlreadyExistsException.php
│       └── Infrastructure/Adapter/Out/
│           ├── EventSubscriber/
│           │   └── UserRegisteredSubscriber.php
│           ├── Persistence/Doctrine/
│           │   └── DoctrineUserRepository.php
│           └── Security/
│               └── SymfonyPasswordHasher.php                # Implémente PasswordHasherInterface
│
└── Shared/                                                  # Shared Kernel : Éléments partagés entre contextes
    ├── Domain/
    │   ├── Aggregate/
    │   │   └── AggregateRoot.php                            # Classe abstraite pour les agrégats
    │   ├── Event/
    │   │   └── DomainEvent.php                              # Interface pour les événements domaine
    │   ├── Port/Out/
    │   │   ├── DomainEventPublisherInterface.php            # Port pour publication d'événements
    │   │   ├── EventStoreInterface.php                      # Port pour stockage d'événements
    │   │   └── IdGeneratorInterface.php                     # Port pour génération IDs (UUID v7)
    │   ├── Validation/                                      # Validation hexagonale
    │   │   ├── ValidationError.php
    │   │   ├── ValidationException.php
    │   │   └── ValidatorInterface.php
    │   └── ValueObject/
    │       └── Email.php                                    # VO : Email partagé entre contextes
    ├── Infrastructure/Adapter/Out/
    │   ├── Event/
    │   │   └── SymfonyDomainEventPublisher.php              # Publie les événements via EventDispatcher + EventStore
    │   ├── Generator/
    │   │   └── UuidV7Generator.php                          # Implémente IdGeneratorInterface
    │   ├── Http/EventListener/
    │   │   └── RequestIdListener.php                        # Correlation ID pour traçabilité
    │   ├── Messenger/Middleware/
    │   │   └── ValidationMiddleware.php                     # Validation automatique des commandes
    │   ├── Persistence/Doctrine/
    │   │   ├── DoctrineEventStore.php                       # Implémente EventStoreInterface
    │   │   ├── DomainEventPublisherListener.php             # Auto-publication après flush
    │   │   ├── Entity/
    │   │   │   └── StoredEvent.php                          # Entity pour persister les événements
    │   │   └── Type/
    │   │       └── EmailType.php                            # Type Doctrine pour Email VO
    │   └── Validator/Constraint/
    │       ├── GiftAvailable.php                            # Contrainte custom : cadeau disponible
    │       └── GiftAvailableValidator.php
    ├── Pagination/Domain/ValueObject/                       # Pagination réutilisable
    │   ├── Page.php
    │   ├── PaginatedResult.php
    │   ├── PerPage.php
    │   └── Total.php
    └── Search/Domain/ValueObject/                           # Recherche réutilisable
        └── SearchTerm.php
│
├── Order/                                                   # Bounded Context 2 : Gestion des commandes
│   ├── Catalog/                                             # Sous-domaine : Catalogue produits
│   │   ├── Domain/
│   │   │   ├── Model/Product.php                            # Entity : Produit avec stock
│   │   │   ├── Exception/
│   │   │   │   ├── ProductNotFoundException.php
│   │   │   │   └── InsufficientStockException.php
│   │   │   ├── Port/Out/ProductRepositoryInterface.php
│   │   │   └── ValueObject/
│   │   │       ├── ProductId.php
│   │   │       ├── ProductName.php
│   │   │       └── Price.php                                # VO Money pattern (add, subtract, multiply)
│   │   ├── Application/
│   │   │   ├── CreateProduct/
│   │   │   ├── GetProduct/
│   │   │   └── GetProducts/
│   │   └── Infrastructure/Persistence/Doctrine/
│   │       ├── DoctrineProductRepository.php
│   │       ├── Orm/Mapping/Model/Product.orm.xml
│   │       └── Type/
│   │           ├── PriceType.php                            # Doctrine type pour Price VO
│   │           └── ProductNameType.php
│   │
│   ├── Cart/                                                # Sous-domaine : Panier d'achat
│   │   ├── Domain/
│   │   │   ├── Model/
│   │   │   │   ├── Cart.php                                 # Aggregate Root avec AggregateRoot trait
│   │   │   │   └── CartItem.php
│   │   │   ├── Event/
│   │   │   │   ├── ItemAddedToCart.php
│   │   │   │   ├── ItemRemovedFromCart.php
│   │   │   │   └── CartCleared.php
│   │   │   ├── Exception/
│   │   │   │   ├── CartNotFoundException.php
│   │   │   │   └── CartItemNotFoundException.php
│   │   │   ├── Port/
│   │   │   │   ├── In/                                      # Use case interfaces
│   │   │   │   └── Out/CartRepositoryInterface.php
│   │   │   └── ValueObject/
│   │   │       ├── CartId.php
│   │   │       └── Quantity.php
│   │   ├── Application/
│   │   │   ├── AddItemToCart/
│   │   │   ├── RemoveItemFromCart/
│   │   │   ├── ClearCart/
│   │   │   └── GetCart/
│   │   └── Infrastructure/Persistence/Doctrine/
│   │
│   └── Ordering/                                            # Sous-domaine : Commandes et livraison
│       ├── Domain/
│       │   ├── Model/
│       │   │   ├── Order.php                                # Aggregate Root avec state machine
│       │   │   └── OrderItem.php
│       │   ├── Event/
│       │   │   ├── OrderPlaced.php
│       │   │   ├── OrderConfirmed.php
│       │   │   ├── OrderShipped.php
│       │   │   ├── OrderDelivered.php
│       │   │   └── OrderCancelled.php
│       │   ├── Exception/
│       │   │   ├── OrderNotFoundException.php               # Pattern not-found avec withId()
│       │   │   ├── InvalidOrderStateException.php           # Pattern invalid-state avec cannotTransition()
│       │   │   └── OrderAlreadyCancelledException.php
│       │   ├── Port/
│       │   │   ├── In/
│       │   │   │   ├── PlaceOrderUseCaseInterface.php
│       │   │   │   ├── CancelOrderUseCaseInterface.php
│       │   │   │   └── GetOrderUseCaseInterface.php
│       │   │   └── Out/OrderRepositoryInterface.php
│       │   └── ValueObject/
│       │       ├── OrderId.php
│       │       ├── OrderStatus.php                          # Status pattern avec state machine
│       │       │   # States: pending → confirmed → shipped → delivered
│       │       │   #         pending → cancelled
│       │       │   #         confirmed → cancelled
│       │       ├── OrderTotal.php                           # Money pattern
│       │       └── ShippingAddress.php
│       ├── Application/
│       │   ├── PlaceOrder/
│       │   ├── ConfirmOrder/
│       │   ├── ShipOrder/
│       │   ├── DeliverOrder/
│       │   ├── CancelOrder/
│       │   ├── GetOrder/
│       │   ├── GetOrders/
│       │   └── Message/SendOrderConfirmationMessage.php     # Async message
│       └── Infrastructure/
│           ├── Messaging/Handler/
│           │   └── SendOrderConfirmationHandler.php
│           └── Persistence/Doctrine/
│               ├── DoctrineOrderRepository.php
│               ├── Orm/Mapping/Model/
│               │   ├── Order.orm.xml
│               │   └── OrderItem.orm.xml
│               └── Type/
│                   ├── OrderStatusType.php
│                   ├── OrderTotalType.php
│                   └── ShippingAddressType.php
```

### 2.3 Flux de Données

Le flux d'exécution suit le pattern suivant :

```
Requête HTTP
  ↓
Controller (UI Layer)
  ↓
Command/Query (Application Layer)
  ↓
Handler (Application Layer)
  ↓
Domain Model (Domain Layer)
  ↓
Repository Interface (Domain Port)
  ↓
Repository Implementation (Infrastructure Adapter)
  ↓
Base de données
```

### 2.4 Dépendances

Les dépendances suivent la règle de dépendance vers l'intérieur :

- **Domain** : Aucune dépendance externe (PHP pur)
- **Application** : Dépend uniquement du Domain
- **Infrastructure** : Implémente les ports du Domain
- **UI** : Utilise Application et Infrastructure

---

## 3. Installation

### 3.1 Prérequis

- PHP 8.1 ou supérieur
- Composer 2.x
- Symfony CLI
- Base de données compatible Doctrine (MySQL, PostgreSQL, SQLite)

### 3.2 Procédure d'Installation

#### Étape 1 : Clonage du Dépôt

```bash
git clone https://github.com/ahmed-bhs/symfony-hexagonal-architecture-demo.git
cd symfony-hexagonal-architecture-demo
```

#### Étape 2 : Installation des Dépendances

```bash
composer install
```

**Note:** Le package `firebase/php-jwt` est déjà inclus dans les dépendances pour l'authentification JWT.

#### Étape 3 : Configuration de l'Application

Éditer le fichier `.env` et configurer les variables nécessaires :

```bash
# Base de données
DATABASE_URL="mysql://user:password@127.0.0.1:3306/hexagonal_demo"
# ou PostgreSQL (recommandé)
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"

# JWT Authentication (IMPORTANT: Changer en production)
JWT_SECRET=your-jwt-secret-key-change-in-production-min-256-bits-please
JWT_ISSUER=hexagonal-demo-app
```

**Sécurité:** Générer un secret JWT sécurisé pour la production :
```bash
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

#### Étape 4 : Création de la Base de Données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

#### Étape 5 : Chargement des Données de Test

```bash
php bin/console doctrine:fixtures:load
```

#### Étape 6 : Démarrage du Serveur

```bash
symfony server:start
```

L'application est accessible à l'adresse : `http://localhost:8000`

---

## 4. Utilisation

### 4.1 API d'Authentification JWT

L'application inclut un système d'authentification JWT complet. Voir la documentation complète dans `docs/JWT_AUTHENTICATION_HEXAGONAL.md`.

#### Inscription
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "secret123"}'
```

#### Connexion
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "user@example.com", "password": "secret123"}'
```

Réponse:
```json
{
  "success": true,
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "user": {
      "id": "...",
      "email": "user@example.com",
      "roles": ["ROLE_USER"]
    }
  }
}
```

#### Accès aux ressources protégées
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Documentation complète:** Voir `docs/JWT_AUTHENTICATION_HEXAGONAL.md` et `docs/JWT_SETUP.md`

### 4.2 Interface Web

#### Page d'Accueil

Route : `/`

Affiche un dashboard avec :
- Statistiques générales (nombre d'habitants, cadeaux, attributions)
- Répartition des habitants par catégorie d'âge
- Liste récente des attributions

#### Liste des Habitants

Route : `/habitants`

Fonctionnalités :
- Pagination (10 habitants par page)
- Recherche par nom, prénom ou email
- Affichage des informations : nom, prénom, âge, email
- Catégorisation : Enfant (< 18 ans), Adulte (18-64 ans), Senior (≥ 65 ans)

#### Catalogue des Cadeaux

Route : `/cadeaux`

Affiche :
- Liste des cadeaux disponibles
- État du stock (disponible / rupture de stock)
- Description de chaque cadeau

### 4.2 Ligne de Commande

```bash
# Lister les habitants
php bin/console app:list-habitants

# Charger les fixtures
php bin/console doctrine:fixtures:load --no-interaction
```

### 4.3 Utilisation Programmatique

#### Dispatcher une Commande

```php
use App\Gift\Attribution\Application\Command\AttributeGift\AttributeGiftCommand;

$command = new AttributeGiftCommand(
    residentId: 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
    giftId: 'a3bb189e-8bf9-3888-9912-ace4e6543002'
);

$this->commandBus->dispatch($command);
```

#### Dispatcher une Query

```php
use App\Gift\Attribution\Application\Query\GetResidents\GetResidentsQuery;
use Symfony\Component\Messenger\Stamp\HandledStamp;

$query = new GetResidentsQuery(
    page: 1,
    perPage: 10,
    searchTerm: ''
);

$envelope = $this->queryBus->dispatch($query);
$response = $envelope->last(HandledStamp::class)->getResult();

foreach ($response->residents as $resident) {
    // Traitement
}
```

---

## 5. Structure du Projet

### 5.1 Répertoires Principaux

| Répertoire | Description |
|------------|-------------|
| `src/Gift/Attribution/` | Bounded context pour l'attribution de cadeaux |
| `src/Gift/Request/` | Bounded context pour les demandes de cadeaux |
| `src/Security/Authentication/` | Bounded context pour l'authentification JWT |
| `src/Security/User/` | Bounded context pour la gestion des utilisateurs |
| `src/Shared/` | Shared Kernel (éléments partagés entre contextes) |
| `tests/` | Tests unitaires, intégration et fonctionnels |
| `config/` | Configuration de l'application |
| `templates/` | Templates Twig |

### 5.2 Configuration

#### Doctrine

Fichier : `config/packages/doctrine.yaml`

Configuration des mappings XML et types custom :

```yaml
doctrine:
    dbal:
        types:
            resident_id: App\Gift\Attribution\Infrastructure\Adapter\Out\Persistence\Doctrine\Type\ResidentIdType
            age: App\Gift\Attribution\Infrastructure\Adapter\Out\Persistence\Doctrine\Type\AgeType
            email_vo: App\Shared\Infrastructure\Adapter\Out\Persistence\Doctrine\Type\EmailType
    orm:
        mappings:
            GiftAttribution:
                type: xml
                dir: '%kernel.project_dir%/src/Gift/Attribution/Infrastructure/Adapter/Out/Persistence/Doctrine/Orm/Mapping'
                prefix: App\Gift\Attribution\Domain\Model
            GiftRequest:
                type: xml
                dir: '%kernel.project_dir%/src/Gift/Request/Infrastructure/Adapter/Out/Persistence/Doctrine/Orm/Mapping'
                prefix: App\Gift\Request\Domain\Model
```

#### Validation

Fichier : `config/packages/validator.yaml`

Configuration pour charger les contraintes YAML (approche hexagonale) :

```yaml
framework:
    validation:
        mapping:
            paths:
                - '%kernel.project_dir%/config/validator'
```

Fichier : `config/validator/submit_gift_request_command.yaml`

Contraintes de validation externalisées (NotBlank, Email, Length, Regex) :

```yaml
App\Gift\Request\Application\Command\SubmitGiftRequest\SubmitGiftRequestCommand:
    properties:
        email:
            - NotBlank: ~
            - Email: ~
```

#### Services

Fichier : `config/services.yaml`

Binding des ports aux adapters :

```yaml
services:
    # ID Generation Port (Shared)
    App\Shared\Domain\Port\Out\IdGeneratorInterface:
        class: App\Shared\Infrastructure\Adapter\Out\Generator\UuidV7Generator

    # Domain Event Publisher Port (Shared)
    App\Shared\Domain\Port\Out\DomainEventPublisherInterface:
        class: App\Shared\Infrastructure\Adapter\Out\Event\SymfonyDomainEventPublisher

    # Repository Ports
    App\Gift\Attribution\Domain\Port\Out\ResidentRepositoryInterface:
        class: App\Gift\Attribution\Infrastructure\Adapter\Out\Persistence\Doctrine\DoctrineResidentRepository
    App\Gift\Attribution\Domain\Port\Out\GiftRepositoryInterface:
        class: App\Gift\Attribution\Infrastructure\Adapter\Out\Persistence\Doctrine\DoctrineGiftRepository
    App\Gift\Request\Domain\Port\Out\GiftRequestRepositoryInterface:
        class: App\Gift\Request\Infrastructure\Adapter\Out\Persistence\Doctrine\DoctrineGiftRequestRepository

    # Security Ports
    App\Security\User\Domain\Port\Out\UserRepositoryInterface:
        class: App\Security\User\Infrastructure\Adapter\Out\Persistence\Doctrine\DoctrineUserRepository
    App\Security\Authentication\Domain\Port\Out\TokenGeneratorInterface:
        class: App\Security\Authentication\Infrastructure\Adapter\Out\Jwt\FirebaseJwtTokenGenerator
```

### 5.3 Conventions de Nommage

- **Entities** : Nom au singulier (ex: `Resident.php`, `Gift.php`)
- **Value Objects** : Nom descriptif (ex: `Age.php`, `Email.php`, `ResidentId.php`)
- **Commands** : Verbe + nom (ex: `AttributeGiftCommand.php`)
- **Queries** : Verbe + nom (ex: `GetResidentsQuery.php`)
- **Handlers** : Nom de la commande/query + `Handler` (ex: `AttributeGiftCommandHandler.php`)
- **Repositories** : `Doctrine` + nom de l'entité + `Repository` (ex: `DoctrineResidentRepository.php`)
- **Ports In** : Use case + `Interface` (ex: `AttributeGiftUseCaseInterface.php`)
- **Ports Out** : Nom + `Interface` (ex: `ResidentRepositoryInterface.php`)

---

## 6. Tests

### 6.1 Pyramide de Tests

Le projet suit la pyramide de tests classique :

```
      E2E (5%)
     /        \
    /  Func.   \
   /   (10%)    \
  /______________\
 /                \
/  Integration     \
/     (20%)         \
/____________________\
/                    \
/    Unit Tests       \
/       (65%)          \
/______________________\
```

### 6.2 Types de Tests

#### Tests Unitaires (Unit)

Emplacement : `tests/Unit/`

Couvrent :
- Value Objects (Age, Email, HabitantId)
- Entities (Cadeau, DemandeCadeau)
- Logique métier pure

Exécution :
```bash
vendor/bin/phpunit tests/Unit/
```

#### Tests d'Intégration (Integration)

Emplacement : `tests/Integration/`

Couvrent :
- Handlers avec repositories InMemory
- Orchestration Application → Domain

Exécution :
```bash
vendor/bin/phpunit tests/Integration/
```

#### Tests Fonctionnels (Functional)

Emplacement : `tests/Functional/`

Couvrent :
- Configuration du kernel Symfony
- Injection de dépendances
- Configuration des buses de messages

Exécution :
```bash
vendor/bin/phpunit tests/Functional/
```

### 6.3 Exécution des Tests

```bash
# Tous les tests
vendor/bin/phpunit

# Avec rapport détaillé
vendor/bin/phpunit --testdox

# Avec couverture (nécessite Xdebug)
vendor/bin/phpunit --coverage-html coverage/
```

### 6.4 Résultats

Au moment de la rédaction :
- **31 tests** exécutés
- **51 assertions**
- **100% de réussite**
- Temps d'exécution : ~149ms

---

## 7. Documentation

### 7.1 Documentation Technique

#### Architecture
- **`docs/README.md`** ⭐ - Index de toute la documentation
- **`docs/APPLICATION_LAYER_STRUCTURE.md`** - Structure complète de la couche Application (CQRS)
- **`docs/SHARED_KERNEL_ARCHITECTURE.md`** - SharedDomain vs SharedInfrastructure
- **`docs/REFACTORING_SUMMARY.md`** - Résumé du refactoring CQRS
- **`docs/UI_LAYER_STRUCTURE.md`** - Structure de la couche UI

#### JWT Authentication
- **`docs/JWT_AUTHENTICATION_HEXAGONAL.md`** ⭐ - Guide complet (26+ pages)
- **`docs/JWT_SETUP.md`** - Installation et tests API
- **`JWT_IMPLEMENTATION_SUMMARY.md`** - Résumé visuel de l'implémentation

#### Autres
- **`COMMANDS.md`** - Toutes les commandes utiles du projet
- `ARCHITECTURE_PURE_100.md` - Analyse de la pureté architecturale
- `docs/TESTS_COMPLETS.md` - Vue d'ensemble de la suite de tests
- `tests/PYRAMIDE_TESTS_HEXAGONAL.md` - Guide de la pyramide de tests

### 7.2 Concepts Clés

#### Architecture Hexagonale

L'architecture hexagonale isole le domaine métier des détails techniques. Les dépendances pointent toujours vers l'intérieur :

- **Domain** : Contient la logique métier pure
- **Application** : Orchestre les use cases
- **Infrastructure** : Implémente les détails techniques
- **UI** : Points d'entrée de l'application

#### Domain-Driven Design

Patterns DDD utilisés :

- **Entities** : Objets avec identité (Resident, Gift, GiftAttribution, User)
- **Value Objects** : Objets définis par leurs attributs (Age, Email, ResidentId, UserId)
- **Aggregate Roots** : Entités racines avec événements domaine (GiftAttribution, GiftRequest, User)
- **Domain Events** : Événements métier (GiftAttributed, GiftRequestSubmitted, UserRegistered)
- **Repositories** : Abstraction de la persistance
- **Bounded Contexts** : Gift (Attribution, Request) et Security (Authentication, User)
- **Shared Kernel** : Éléments partagés (Email, IdGenerator, Pagination, EventStore)

#### CQRS

Séparation stricte entre :

- **Commands** : Opérations d'écriture (création, modification, suppression)
- **Queries** : Opérations de lecture (consultation, recherche)

Chaque opération a son propre handler dédié.

### 7.3 Principes Appliqués

- **SOLID** : Respect des 5 principes de conception objet
- **DRY** : Pas de duplication de code
- **YAGNI** : Seulement le code nécessaire
- **SoC** : Séparation claire des responsabilités

---

## Annexes

### A. Références

- [Architecture Hexagonale - Alistair Cockburn](https://alistair.cockburn.us/hexagonal-architecture/)
- [Domain-Driven Design - Eric Evans](https://domainlanguage.com/ddd/)
- [CQRS Pattern - Martin Fowler](https://martinfowler.com/bliki/CQRS.html)
- [Symfony Documentation](https://symfony.com/doc/current/index.html)

### B. Glossaire

- **Port** : Interface définissant un contrat
- **Adapter** : Implémentation concrète d'un port
- **Bounded Context** : Frontière dans laquelle un modèle est défini
- **Value Object** : Objet immuable défini par ses attributs
- **Entity** : Objet avec identité et cycle de vie
- **Aggregate** : Cluster d'objets traités comme une unité

### C. Licence

MIT License - Voir fichier LICENSE

### D. Auteur

Ahmed EBEN HASSINE
Email : ahmedbhs123@gmail.com
GitHub : https://github.com/ahmed-bhs

Date : Janvier 2026
Version : 1.0.0
