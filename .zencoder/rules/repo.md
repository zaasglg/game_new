---
description: Repository Information Overview
alwaysApply: true
---

# Valor Games Platform Information

## Summary
A PHP-based online gaming platform featuring multiple casino-style games including Aviator, Chicken Road, and Mines. The platform includes user account management, payment processing, and game state management.

## Structure
- **aviator/**: Node.js WebSocket server for the Aviator game
- **chicken-road/**: WebSocket server for Chicken Road game with both PHP and Node.js components
- **mines/**: Frontend assets for the Mines game
- **api/**: API endpoints for platform functionality
- **templates/**: PHP templates for site pages and game interfaces
- **js/**: JavaScript files for frontend functionality
- **css/**: Styling for the platform
- **mysql/**: SQL database schemas and setup files
- **politics/**: Legal and policy pages

## Language & Runtime
**Primary Language**: PHP 7.4
**Secondary Languages**: JavaScript (Node.js)
**Database**: MySQL
**Web Server**: Apache (MAMP)

## Dependencies

### PHP Components
- PDO MySQL extension for database connectivity
- No formal dependency management (direct PHP includes)

### Aviator Game (Node.js)
**Package Manager**: npm
**Main Dependencies**:
- express: ^4.19.2
- socket.io: ^4.7.5
- mysql2: ^3.11.0
- axios: ^1.7.4
- dotenv: ^16.0.0

### Chicken Road Game
**Package Manager**: npm and Composer
**Node.js Dependencies**:
- ws: ^8.14.2 (WebSocket)
**PHP Dependencies**:
- ratchet/pawl: ^0.4
- react/socket: ^1.0
- cboden/ratchet: ^0.4

## Database Configuration
**Host**: 127.0.0.1
**Port**: 8889
**Database**: volurgame
**Connection**: PDO with fallback to socket connection

## Build & Installation
```bash
# For Aviator game server
cd aviator
npm install
node core.js

# For Chicken Road game server
cd chicken-road
npm install
composer install
node server.js
```

## Main Files & Resources
**Entry Points**:
- index.php: Main site entry point
- aviator/core.js: Aviator game WebSocket server
- chicken-road/server.js: Chicken Road game WebSocket server
- chicken-road/res/js/game2.js: Main Chicken Road game logic
- templates/games/: Game frontend templates

**Configuration Files**:
- payment_config.json: Payment processing configuration
- deposit_config.json: Deposit settings
- country_settings.json: Country-specific settings
- translations.json: Multilingual support

## Game Components

### Aviator Game
A real-time multiplayer game with WebSocket communication using Socket.io. The server manages game state and communicates with clients for real-time updates.

### Chicken Road Game
A game with integrated prediction features, using both PHP (Ratchet) and Node.js WebSocket servers for real-time gameplay. The main game logic is implemented in game2.js which handles game mechanics and prediction algorithms.

### Mines Game
A client-side game with server communication for game state management and results processing.

## User Management
User authentication, registration, and account management handled through PHP scripts with MySQL database storage. Features include:
- Login/registration system
- Balance management
- Payment processing
- User verification stages