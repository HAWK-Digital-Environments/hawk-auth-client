# HAWK Auth Client

A comprehensive authentication and authorization library that wraps around the Keycloak REST API (
using [hawk-keycloak-auth-server](https://github.com/HAWK-Digital-Environments/hawk-keycloak-auth-server) for additional
features). The library provides a sophisticated way to build microservices with secure authentication, authorization,
and user management.

## Architecture

The library is built on a layered architecture that provides clear separation of concerns:

```mermaid
graph TD
  A[AuthClient] --> B[Authentication Layer]
  A --> C[User Management Layer]
  A --> D[Permission Layer]
  A --> E[Profile Layer]
  
  B --> B1[StatefulAuth]
  B --> B2[StatelessAuth]
  B --> B3[KeycloakProvider]
  
  C --> C1[UserStorage]
  C --> C2[GroupStorage]
  C --> C3[RoleStorage]
  
  D --> D1[Guard]
  D --> D2[PermissionStorage]
  
  E --> E1[ProfileStructure]
  E --> E2[ProfileUpdater]
```

### Core Components

- **Authentication Layer**: Handles both stateful (session-based) and stateless (token-based) authentication
- **User Management Layer**: Manages users, groups, and roles with efficient caching
- **Permission Layer**: Provides fine-grained access control through resource scopes
- **Profile Layer**: Manages flexible user profile structures with validation
- **Frontend Integration**: TypeScript companion library for seamless client-side integration

## Features

### Authentication

The library provides two authentication approaches:

1. **Stateful Authentication** (Session-based)
   - Ideal for server-rendered websites
   - Automatic session management
   - Built-in token refresh handling
   - See [stateful-auth example](examples/stateful-auth/index.php)

2. **Stateless Authentication** (Token-based)
   - Perfect for API implementations
   - No session state required
   - Token validation and verification
   - See [stateless-auth example](examples/stateless-auth/index.php)

### Permission System

The Guard system provides sophisticated access control:

- Resource-based permissions with scopes
- Role-based access control
- Group hierarchy support
- Fine-grained permission checks

```php
// Example permission checks
$guard->hasAnyRole('admin', 'editor');
$guard->hasAnyGroupOrHasChildOfAny('organization.managers');
$guard->hasAnyResourceScope('document-123', 'read', 'write');
```

### Profile Management

Flexible user profile system with:

- Structured field definitions
- Field type validation
- Admin/User view modes
- Grouped fields
- Custom field types

See the [user-profile example](examples/user-profile/index.php) for implementation details.

### Frontend Integration

TypeScript companion library (`@hawk-hhg/auth-client`) providing:

- Event-driven authentication state management
- Profile access
- Permission checking
- Automatic token refresh

See the [frontend-api example](examples/frontend-api/index.html) for implementation details.

## Installation

### PHP Library

```bash
composer require hawk-hhg/auth-client
```

### JavaScript Companion (Optional)

```bash
npm install @hawk-hhg/auth-client
```

## Basic Configuration

```php
$client = new AuthClient(
    redirectUrl: 'https://your-app.com/callback',
    publicKeycloakUrl: 'https://keycloak.example.com',
    realm: 'your-realm',
    clientId: 'your-client-id',
    clientSecret: 'your-client-secret',
    // Optional: internal Keycloak URL for Docker environments
    internalKeycloakUrl: 'http://keycloak:8080'
);
```

### Environment Variables

```env
PUBLIC_KEYCLOAK_URL=https://keycloak.example.com
INTERNAL_KEYCLOAK_URL=http://keycloak:8080 # Optional, for Docker
REALM=your-realm
CLIENT_ID=your-client-id
CLIENT_SECRET=your-client-secret
```

## Examples

The library includes several example implementations:

- [stateful-auth](examples/stateful-auth/index.php): Session-based authentication
- [stateless-auth](examples/stateless-auth/index.php): Token-based API authentication
- [user-profile](examples/user-profile/index.php): Profile management
- [frontend-api](examples/frontend-api/index.html): Frontend integration
- [oauth-flow](examples/oauth-flow/index.php): OAuth authentication flow

Each example demonstrates a specific use case with detailed comments and working code.

The examples come with their own `docker-compose.yml` file you can use to start them locally.
Just run `docker-compose up` in the example directory and open the `http://localhost:8099` URL in your browser.
Note, that you must modify the environment variables in the `docker-compose.yml` file to match your Keycloak setup.

Alternatively, if you are using
the [HAWK Keycloak Infrastructure](https://github.com/HAWK-Digital-Environments/hawk-keycloak-infrastructure),
you can copy the `docker-compose.keycloak.yml` file to `docker-compose.override.yml` and start the environment with
`docker compose up`.

## Development Setup

### CLI Interface

The library comes with a powerful CLI interface (`bin/env`) built with bashly, providing commands for development and
testing:

```bash
# Start the environment
bin/env up        # Starts containers in daemon mode
bin/env up -f     # Starts containers with attached output

# Container management
bin/env stop      # Stop containers
bin/env restart   # Restart containers
bin/env down      # Remove containers
bin/env clean     # Remove container images

# Development tools
bin/env logs [service]     # View container logs
bin/env ssh [service]      # SSH into a container
bin/env composer [...args] # Run composer commands

# Testing
bin/env test unit         # Run unit tests
bin/env test unit -c      # Generate coverage report

# Frontend
bin/env frontend build   # Build TypeScript library
bin/env frontend watch   # Watch TypeScript files
```

### Docker Setup

The library includes Docker support for development and testing. To use the demo environment:

1. Clone the [HAWK Keycloak Infrastructure](https://github.com/HAWK-Digital-Environments/hawk-keycloak-infrastructure)
2. Copy `docker-compose.keycloak.yml` to `docker-compose.override.yml`
3. Start the environment:
   ```bash
   bin/env up
   ```

The demo environment automatically starts the examples for you which you can access at `http://localhost:8099`.

## Testing

```bash
# Run unit tests
bin/env test unit

# Generate coverage report
bin/env test unit -c
```

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a
postcard from your hometown, mentioning which of our package(s) you are using.

```
HAWK Fakultät Gestaltung
Interaction Design Lab
Renatastraße 11
31134 Hildesheim
```

Thank you :D
