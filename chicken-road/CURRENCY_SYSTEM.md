# Currency System Documentation

## Overview
The Chicken Road game uses a dual-currency system:
- **Game Display**: Shows amounts in USD for consistent user experience
- **Database Storage**: Stores amounts in user's national currency in `volurgame.users` table

## How It Works

### 1. Game Loading
When game loads with `?user_id=X&balance=Y`:
- `Y` is the balance in USD (for game display)
- System gets user's country from `volurgame.users.country`
- Converts USD to national currency using exchange rates
- Saves national currency amount to `volurgame.users.deposit`

### 2. During Gameplay
- Game shows all amounts in USD
- When player places bet or wins/loses:
  - Game calculates new balance in USD
  - API converts USD to national currency
  - Saves national currency to database
  - Returns USD amount to game for display

### 3. Currency Conversion
- Exchange rates defined in `currency.php`
- Functions:
  - `convertToUSD($amount, $country)` - National → USD
  - `convertFromUSD($amount, $country)` - USD → National
  - `getCurrencyRate($country)` - Get exchange rate

### 4. Database Structure
- **volurgame.users.deposit**: Balance in national currency
- **volurgame.users.country**: User's country for currency conversion
- **volurgame.users.user_id**: User identifier

## API Methods

### Users::get_user_balance($data)
- Gets balance from database (national currency)
- Converts to USD for game display
- Returns both USD and national amounts

### Users::save_game_result($data)
- Receives balance in USD from game
- Converts to national currency
- Saves to database
- Returns USD amount for game

## Example Flow
1. User from Argentina has 140,000 ARS in database
2. Game loads and shows $100 USD (140,000 ÷ 1400 = 100)
3. User bets $5 USD, new balance is $95 USD
4. System saves 133,000 ARS to database (95 × 1400 = 133,000)
5. Game continues to show $95 USD

## Files Modified
- `templates/main.tpl.php` - Game initialization and currency conversion
- `classes/Users.class.php` - API methods with currency conversion
- `currency.php` - Exchange rates and conversion functions
- `db_config.php` - Database configuration for volurgame connection

## Exchange Rates (1 USD = X National)
- Argentina: 1400 ARS
- Colombia: 4500 COP  
- Mexico: 18 MXN
- Brazil: 5 BRL
- Chile: 900 CLP
- Peru: 4 PEN
- (See currency.php for complete list)